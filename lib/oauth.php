<?php
	require_once 'helper.php';
	
	//$redirectURL = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
	$GLOBALS['redirectURL'] = 'http://ufin.mashumafi.com/site/index';
	// ALL or EMAIL, change it to either value for correct scope
	$GLOBALS['scope'] = 'ALL';
	// professor scope
	$GLOBALS['scopeALL'] = 'https://spreadsheets.google.com/feeds/ https://www.googleapis.com/auth/userinfo.email https://docs.google.com/feeds/ https://docs.googleusercontent.com/';
	// student scope
	$GLOBALS['scopeEMAIL'] = 'https://www.googleapis.com/auth/userinfo.email';
	
	/*
	 * Will return all the data about a user that we are authorized to view
	 */
	function get_user($token) {
		return wget('GET', 'https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$token);
	}
	
	function oauthSession($token) {
		$tokens = json_decode($token);
		$user = get_user($tokens->access_token);
		$user =  json_decode($user);
		return array_merge((array)$tokens, (array)$user);
	}
	
	/*
	 * This generates an url to begin oauth
	 * The user is presented with dialog
	 */
	function auth_url() {
		$args = array(
			'response_type' => 'code',
			'redirect_uri' => $GLOBALS['redirectURL'],
			'client_id' => '378252524988.apps.googleusercontent.com',
			'scope' => $GLOBALS['scope' . $GLOBALS['scope']],
			'access_type' => 'offline',
			'approval_prompt' => 'force');
		$query = concatQuery($args);
		// url for oauth
		return 'https://accounts.google.com/o/oauth2/auth?' . $query;
	}
	
	/*
	 * Tokens expire every 60 minutes, use this function to refresh them
	 */
	function refresh_token($refreshToken) {
		$args = array(
			'refresh_token' => $refreshToken,
			'client_id' => '378252524988.apps.googleusercontent.com',
			'client_secret' => 'adV0KYY0ZsXnaVc0natJEscz',
			'grant_type' => 'refresh_token'
		);
		$query = concatQuery($args);
		return gget('POST', 'https://accounts.google.com/o/oauth2/token', array(), $query, 'application/x-www-form-urlencoded');
	}

	/*
	 * When first authenticated we need to upgrade our one-time token to long lived token
	 * This is a security feature of oauth
	 */
	function upgradeToken($code) {
		$args = array(
			'code' => $code,
			'client_id' => '378252524988.apps.googleusercontent.com',
			'client_secret' => 'adV0KYY0ZsXnaVc0natJEscz',
			'redirect_uri' => $GLOBALS['redirectURL'],
			'grant_type' => 'authorization_code'
		);
		$query = concatQuery($args);
		return gget('POST', 'https://accounts.google.com/o/oauth2/token', array(), $query, 'application/x-www-form-urlencoded');
	}
	
	/*
	 * Programatically deletes all relations with google
	 */
	function delete_account($refreshToken) {
		wget('https://accounts.google.com/o/oauth2/revoke?token='.$refreshToken);
	}
?>