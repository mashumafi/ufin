<?php
	// test a value to see if it is a possible token, usually the value is either null or a token
	// define $access_token anywhere and it will be used, without it we look at session
	// token: the variable to test
	// returns your value or the session token
	function get_token() {
		return $GLOBALS['accessToken'];
	}
	
	function XML2JSON($xml) {
        normalizeSimpleXML(simplexml_load_string($xml), $result);
        return $result;
    }
	
	function normalizeSimpleXML($obj, &$result) {
		$data = $obj;
		if (is_object($data)) {
			$data = get_object_vars($data);
		}
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$res = null;
				normalizeSimpleXML($value, $res);
				if (($key == '@attributes') && ($key)) {
					$result = $res;
				} else {
					$result[$key] = $res;
				}
			}
		} else {
			$result = $data;
		}
	}
	
	function get_account_token($id) {
		$account = Account::model()->find('id=?', array($id));
		if($account->expires <= time()) {
			$token = refresh_token($account->refreshToken);
			$tokens = (array)json_decode($token);
			$account->expires = time() + $tokens['expires_in'];
			$account->refreshToken = $tokens['refresh_token'];
			$account->accessToken = $tokens['access_token'];
			$account->save();
		}
		return $account->accessToken;
	}
	
	function concatQuery($args=array()) {
		$query = '';
		foreach ($args as $key => $value) { // concat to a query string
			$query .= urlencode($key) . '=' . urlencode($value) . '&';
		}
		return substr($query, 0, strlen($query) - 1);
	}

	// adds headers to prevent caching and allows json
	function json_header() {
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
	}

	// redirect the browser to another page
	// s: the url to redirect to
	function redirectToUrl($s) {
		header("Location: $s");
		exit();
	}

	// http request for google
	// token: not required in a session, pass in null
	// method: http method, ie. GET PUT POST DELETE
	// url: the address to use for the request
	// header: array in the format array("Field1: value1", "Field2: value2"), some values get added so it cannot be null
	// content: body of the request, usually atom+xml type
	// contenttype: is type of data in content, should always be 'application/atom+xml' for google requests
	// returns the result of the request
	function gget($method, $url, array $header = array(), $content = null, $contenttype = 'application/atom+xml') {
		$header[] = "GData-Version: 3.0";
		$header[] = 'Authorization: AuthSub token="' . get_token() . '"';
		//return cget($method, $url, $header, $contenttype, $content);
		return cget($method, $url, $header, $contenttype, $content);
	}

	// uses cRUL for general http requests, requires cRUL
	// method: http method, ie. GET PUT POST DELETE
	// url: the address to use for the request
	// header: array in the format array("Field1: value1", "Field2: value2")
	// content: body of the request
	// contenttype: is type of data in content
	// returns the result of the request
	function cget($method, $url, $header = array(), $contenttype = null, $content = null) {
		$curl = curl_init($url);
		// these are for safety
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl,CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_AUTOREFERER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		// depending on method we do different things
		switch($method) {
			case 'GET':
				break;
			case 'POST':
				$header[] = "Content-Type: $contenttype";
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
				break;
			case 'PUT':
				$header[] = "Content-Type: $contenttype";
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
				break;
			case 'DELETE':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
				break;
		}
		if ($header && $header[0]) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		}
		$response = curl_exec($curl);
		if (!$response) {
			$response = curl_error($curl);
		}
		curl_close($curl);
		return $response;
	}

	// uses wget for general http requests, uses exec('wget ...') so requires wget
	// method: http method, ie. GET PUT POST DELETE
	// url: the address to use for the request
	// header: array in the format array("Field1: value1", "Field2: value2")
	// content: body of the request
	// contenttype: is type of data in content
	// extra: additional arguments to append to the exec
	// returns the result of the request
	function wget($method, $url, array $headers = array(), $contenttype = null, $content = null, array $extra = array()) {
		switch($method) {
			case 'GET': // no processing needed
				break;
			case 'POST': // add content type and data
				$headers[] = "Content-Type: $contenttype";
				$content = str_replace("'", "\"", $content);
				$extra[] = "--post-data '$content'";
				break;
			case 'PUT': // add content type and data
				$headers[] = "Content-Type: $contenttype";
				$content = str_replace("'", "\"", $content);
				$extra[] = "--post-data '$content'";
				// simulate PUT request
				$headers[] = "X-Http-Method-Override: PUT";
				break;
			case 'DELETE':
				// simulate DELETE request
				$headers[] = "X-Http-Method-Override: DELETE";
				// simulate only works for POST
				$extra[] = "--post-data -";
				break;
			default:
				return "$method is not a supported http method.";
		}
		if ($headers && $headers[0]) { // are there any headers?
			// generate args correctly
			$headers = "--header '" . implode("' --header '", $headers) . "'";
		} else $headers = ''; // no headers, we should make empty string
		// store the output in this array
		$output = array();
		$command = "wget -q -O- '$url' $headers --no-check-certificate " . implode(' ', $extra);
		$return_var = 0;
		exec($command, $output, $return_var);
		switch($return_var) { // figure out the result
			case 0: // No problems occurred.
				// turn output into giant string
				$output = implode($output);
				return $output;
			case 1:
				return "Generic error code.";
			case 2:
				return "Parse error—for instance, when parsing command-line options, the ‘.wgetrc’ or ‘.netrc’...";
			case 3:
				return "File I/O error.";
			case 4:
				return "Network failure.";
			case 5: // should never happen because of --no-check-certificate
				return "SSL verification failure.";
			case 6:
				return "Username/password authentication failure.";
			case 7:
				return "Protocol errors.";
			case 8:
				return "Server issued an error response.";
		}
	}
?>