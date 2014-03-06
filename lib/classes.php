<?php
	// example to convert letter columns in worksheet to base 26
	// intval("ABCD", 26);

	/**
	 * This is a wrapper around gdata entity
	 */
	abstract class AtomObject {
		protected $feed;
		
		/**
		 * @param stdObj $feed
		 */
		protected function __construct($feed) {
			$this->feed = $feed;
		}

		/**
		 * magic php function
		 * @param string $name
		 * @return mixed
		 */
		protected function __get($name) {
			if ($this->feed[$name]) {
				return $this->feed[$name];
			}
		}
		
		/**
		 * @return string the full id url of the resource
		 */
		public function id() {
			return $this->id;
		}
		
		/**
		 * @return string the title of the resource
		 */
		public function title() {
			return $this->title;
		}
		
		/**
		 * @return string json encodes this entity's feed
		 */
		public function to_json() {
			return json_encode($this->feed);
		}
	}
	
	/**
	 * This is a wrapper around gdata feed, its an array of AtomObjects
	 */
	class Feed extends ArrayObject {
		/**
		 * @return string json encodes entire feed into json array
		 */
		public function to_json() {
			$ret = array();
			foreach($this as $entity) {
				$ret[] = $entity->to_json();
			}
			return '[' . implode(', ', $ret) . ']';
		}
	}
	
	/**
	 * Wrapper around spreadsheet gdata
	 */
	final class Spreadsheet extends AtomObject {
		/**
		 * @param
		 */
		protected function __construct($feed) {
			parent::__construct($feed);
		}
		
		/**
		 * @param int $id
		 * @return Spreadsheet
		 */
		public static function single($id) {
			$url = "https://spreadsheets.google.com/feeds/spreadsheets/private/full/$id";
			$feed = XML2JSON(gget('GET', $url));
			return new Spreadsheet($feed);
		}
		
		/**
		 * @param string $title
		 * @return string result of the request, in json if no errors
		 */
		public static function create($title) {
			$url = 'https://docs.google.com/feeds/default/private/full';
			$entry = '<?xml version="1.0" encoding="UTF-8"?>';
			$entry .= '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:docs="http://schemas.google.com/docs/2007">';
			$entry .= '<category scheme="http://schemas.google.com/g/2005#kind"';
			$entry .= ' term="http://schemas.google.com/docs/2007#spreadsheet"/>';
			$entry .= "<title>$title</title>";
			$entry .= '</entry>';
			$feed = XML2JSON(gget('POST', $url, array(), $entry));
			return new Spreadsheet($feed);
		}
		
		/**
		 * @param string $title
		 * @param int $max_results
		 * @return Feed<Spreadsheet>
		 */
		public static function feed($title = null, $max_results = 10) {
			if(isset($title)) {
				$title = "&title=$title";
			}
			if(isset($max_results)) {
				$max_results = "&max-results=$max_results";
			}
			$url = "https://spreadsheets.google.com/feeds/spreadsheets/private/full?$max_results$title";
			$feed = XML2JSON(gget('GET', $url));
			$ret = new Feed();
			$arr = $feed['entry'];
			if(array_values($arr) === $arr) {
				foreach($feed['entry'] as $spreadsheet) {
					$ret[] = new Spreadsheet($spreadsheet);
				}
			} else {
				$ret[] = new Spreadsheet($feed['entry']);
			}
			return $ret;
		}
		
		/**
		 * @return int 
		 */
		public function id() {
			$id = parent::id();
			$id = explode('/', $id);
			return $id[5];
		}
		
		public function view_uri() {
			$alternate = '';
			foreach($this->link as $link) {
				if($link->rel == 'alternate') {
					$alternate = $link->href;
					$alternate = explode('=', $alternate);
					$alternate = $alternate[1];
				}
			}
			return "https://spreadsheets.google.com/ccc?key=" . $alternate;
		}
		
		/**
		 * @return string result of the request, in json if no errors
		 */
		public function delete($match = true) {
			$headers[] = 'If-Match: *';
			$url = 'https://docs.google.com/feeds/default/private/full/spreadsheet%3A' . $this->id() . '?delete=true';
			return $feed = gget('DELETE', $url, $headers);
		}
		
		/**
		 * @param string $title name of the copy
		 * @return string result of the request, in json if no errors
		 */
		public function copy($title) {
			$url = 'https://docs.google.com/feeds/default/private/full';
			$entry = '<?xml version="1.0" encoding="UTF-8"?><entry xmlns="http://www.w3.org/2005/Atom">
				<id>https://docs.google.com/feeds/default/private/full/spreadsheet%3A' . $this->id() . '</id>
				<title>' . $title . '</title>
			</entry>';
			$feed = XML2JSON(gget('POST', $url, array(), $entry));
			return new Spreadsheet($feed);
		}
		
		public function batchCopy($title, $emails) {
			$url = 'https://docs.google.com/feeds/default/private/full/batch';
			$entries = '';
			foreach($emails as $email) {
				$entries .= '<entry xmlns="http://www.w3.org/2005/Atom">
						<id>https://docs.google.com/feeds/default/private/full/spreadsheet%3A' . $this->id() . '</id>
						<title>' . $title . ' - ' . $email . '</title>
					</entry>';
			}
			$entry = '<?xml version="1.0" encoding="UTF-8"?>' . $this->batch($entries);
			$res = XML2JSON(gget('POST', $url, array(), $entry));
			$feed = $res['entry'];
			
			if(array_values($feed) === $feed)
				return $feed;
			else
				return array($feed);
		}
		
		public function batchCopyAndShare($title, $emails, $role = 'writer', $scope = 'user') {
			$url = 'https://docs.google.com/feeds/default/private/full/batch';
			$entries = '';
			$id = 1;
			foreach($emails as $email) {
				$entries .= '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gAcl="http://schemas.google.com/acl/2007">' .
					"<category scheme='http://schemas.google.com/g/2005#kind' term='http://schemas.google.com/acl/2007#accessRule'/>" .
						'<id>https://docs.google.com/feeds/default/private/full/spreadsheet%3A' . $this->id() . '</id>
						<title>' . $title . ' - ' . $email . '</title>'
					. "<gAcl:role value='$role'/>"
					. "<gAcl:scope type='$scope' value='$email'/>"
				. "</entry>";
			}
			$entry = '<?xml version="1.0" encoding="UTF-8"?>' . $this->batch($entries);
			$res = (gget('POST', $url, array(), $entry));//XML2JSON
			//$feed = $res['entry'];
			return $res;
		}
		
		public function batch($content) {
			return '<feed xmlns="http://www.w3.org/2005/Atom"
				xmlns:docs="http://schemas.google.com/docs/2007"
				xmlns:batch="http://schemas.google.com/gdata/batch"
				xmlns:gd="http://schemas.google.com/g/2005">' . $content . '</feed>';
		}
		
		/**
		 * @param string $email
		 * @param string $role
		 *		reader - a viewer (equivalent to read-only access).
		 *		writer - a collaborator (equivalent to read/write access).
		 *		owner - typically the site admin (equivalent to read/write access).
		 * @param string $scope
		 *		user - an e-mail address value, e.g "user@gmail.com".
		 *		group - a Google Group e-mail address, e.g "group@domain.com".
		 *		domain - a Google Apps domain name, e.g "domain.com".
		 *		invite - a user that has been invited to the site, but hasn't yet been added to the ACL for the site. (Not available if gdata 1.3 or below is specified.)
		 *		default - There is only one possible scope of type "default", which has no value (e.g <gAcl:scope type="default">). This particular scope controls the access that any user has by default on a public site.
		 * @return string result of the request, in xml if no errors
		 */
		public function share($email, $role = 'writer', $scope = 'user') {
			$url = 'https://docs.google.com/feeds/default/private/full/spreadsheet%3A' . $this->id() . '/acl';
			$entry = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gAcl="http://schemas.google.com/acl/2007">'
					. "<category scheme='http://schemas.google.com/g/2005#kind' term='http://schemas.google.com/acl/2007#accessRule'/>"
					. "<gAcl:role value='$role'/>"
					. "<gAcl:scope type='$scope' value='$email'/>"
				. "</entry>";
			return gget('POST', $url, array(), $entry);
		}
		
		public function batchShare($emails, $feed) {
			$url = 'https://docs.google.com/feeds/default/private/full/batch';
			$entries = '<category scheme="http://schemas.google.com/g/2005#kind" term="http://schemas.google.com/acl/2007#accessRule"/>';
			$len = count($emails);
			if($len == count($feed)) {
				for($i = 0; $i < $len; $i++) {
					$email = $emails[$i];
					$id = $feed[$i]['id'];
					$entries .= '<entry>'
						. "<gAcl:role value='writer'/>"
						. "<gAcl:scope type='user' value='$email'/>"
						. "<id>$id</id>"
					. "</entry>";
				}
				$entry = '<?xml version="1.0" encoding="UTF-8"?><feed xmlns="http://www.w3.org/2005/Atom"
					xmlns:gAcl="http://schemas.google.com/acl/2007"
					xmlns:batch="http://schemas.google.com/gdata/batch">'
						. $entries .
					'</feed>';
				$res = (gget('POST', $url, array(), $entry)); // XML2JSON
				//$feed = $res['entry'];
				return $res;
			} else {
				echo count($feed) . " error " . $len;
			}
		}
		
		/**
		 * @param string $email
		 * @return string result of the request, in json if no errors
		 */
		public function deny($email) {
			$url = 'https://docs.google.com/feeds/default/private/full/spreadsheet%3A' . $this->id() . '/acl/user:' . $email;
			return gget('DELETE', $url);
		}
		
		/**
		 * Allows anyone with the url to access the spreadsheet
		 * @return string result of the request, in json if no errors
		 */
		public function make_public() {
			$ssid = $this->id();
			$url = 'https://docs.google.com/feeds/default/private/full/spreadsheet%3A' . $ssid . '/acl';
			$entry = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gAcl="http://schemas.google.com/acl/2007">';
			$entry .= "<gAcl:withKey key='$ssid'>";
			$entry .= "<gAcl:role value='writer'/>";
			$entry .= "</gAcl:withKey>";
			$entry .= "<gAcl:scope type='default'/>";
			$entry .= "</entry>";
			return gget('POST', $url, array(), $entry);
		}
		
		/**
		 * @param string $title
		 * @param int $rows
		 * @param int $cols
		 * @return Worksheet
		 */
		public function create_worksheet($title, $rows = 10, $cols = 10) {
			return Worksheet::create($this->id(), $title, $rows, $cols);
		}
		
		/**
		 * @param int $wsid
		 * @return Worksheet
		 */
		public function worksheet($wsid) {
			return Worksheet::single($this->id(), $wsid);
		}
		
		/**
		 * @param string $title
		 * @return Worksheet
		 */
		public function worksheet_feed($title) {
			return Worksheet::feed($this->id(), $title);
		}
	}
	
	/**
	 * Wrapper around gdata spreadsheet
	 */
	final class Worksheet extends AtomObject {
		/**
		 * @param stdObj $feed decoded json
		 */
		protected function __construct($feed) {
			parent::__construct($feed);
		}
		
		/**
		 * @param int $ssid spreadsheet id
		 * @param int $wsid worksheet id
		 * @return Worksheet
		 */
		public static function single($ssid, $wsid) {
			$url = "https://spreadsheets.google.com/feeds/worksheets/$ssid/private/full/$wsid";
			return new Worksheet(XML2JSON(gget('GET', $url))->entry);
		}
		
		/**
		 * @param int $ssid spreadsheet id
		 * @param string $title new spreadsheet title
		 * @param int $rows number of rows initially in worksheet
		 * @param int $cols number of cols initially in worksheet
		 * @return string result of the request, in json if no errors
		 */
		public static function create($ssid, $title, $rows, $cols) {
			$url = "https://spreadsheets.google.com/feeds/worksheets/$ssid/private/full?alt=json";
			$entry = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">'
				. "<title>$title</title>"
				. "<gs:rowCount>".(isset($rows) ? $rows : 10)."</gs:rowCount>"
				. "<gs:colCount>".(isset($cols) ? $cols : 10)."</gs:colCount>"
			. '</entry>';
			return gget('POST', $url, array(), $entry);
		}
		
		/**
		 * @param int $ssid spreadsheet id
		 * @param string $title new spreadsheet title
		 * @param int $max_results maximum number of spreadsheets in the feed
		 * @return Feed<Worksheet>
		 */
		public static function feed($ssid, $title = null, $max_results = 10) {
			if(isset($title)) {
				$title = "&title=$title";
			}
			if(isset($max_results)) {
				$max_results = "&max-results=$max_results";
			}
			$url = "https://spreadsheets.google.com/feeds/worksheets/$ssid/private/full?alt=json$max_results$title";
			// $header[] = If-None-Match: W/"D0cERnk-eip7ImA9WBBXGEg."; this can be used to prevent deleting newer versions
			$feed = json_decode(gget('GET', $url));
			$ret = new Feed();
			if($feed->feed->entry) { // no entry means no cells
				foreach($feed->feed->entry as $worksheet) {
					$ret[] = new Worksheet($worksheet);
				}
			} else {
				if($feed->entry) {
					$ret[] = new Worksheet($feed->entry);
				}
			}
			return $ret;
		}
		
		/**
		 * @return string spreadsheet id
		 */
		public function ssid() {
			$id = parent::id();
			$id = explode('/', $id);
			return $id[5];
		}
		
		/**
		 * @return string worksheet id
		 */
		public function id() {
			$id = parent::id();
			$id = explode('/', $id);
			return $id[6];
		}
		
		/**
		 * @return string result of the request, in json if no errors
		 */
		public function delete() {
			$headers[] = 'If-Match: *';
			$url = "https://spreadsheets.google.com/feeds/worksheets/" . $this->ssid() . "/private/full/" . $this->id() . "?alt=json";
			return $feed = gget('DELETE', $url, $headers);
		}
		
		/**
		 * updates only the title of the worksheet
		 * @param string $title
		 * @return result of the request, in json if no errors
		 */
		public function rename($title) {
			return $this->update($title);
		}
		
		/**
		 * @param string $title
		 * @param int $rows
		 * @param int $cols
		 * @return string result of the request, in json if no errors
		 */
		public function update($title = null, $rows = null, $cols = null) {
			$headers[] = 'If-Match: *';
			$url = "https://spreadsheets.google.com/feeds/worksheets/" . $this->ssid() . "/private/full/" . $this->id() . "?alt=json";
			$entry = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">'
				. '<title type="text">' . (isset($title) ? $title : $this->title()) . '</title>'
				. "<gs:rowCount>".(isset($rows) ? $rows : $this->getRowCount())."</gs:rowCount>"
				. "<gs:colCount>".(isset($cols) ? $cols : $this->getColCount())."</gs:colCount>";
			$entry .= '</entry>';
			return $feed = json_decode(gget('PUT', $url, $headers, $entry));
		}
		
		/**
		 * @return int number of columns in the worksheet
		 */
		public function getColCount() {
			return $this->{'gs$colCount'};
		}
		
		/**
		 * @param int $count new column count
		 * @return string result of the request, in json if no errors
		 */
		public function setColCount($count) {
			return $this->update(null, null, $count);
		}
		
		/**
		 * @return string result of the request, in json if no errors
		 */
		public function getRowCount() {
			return $this->{'gs$rowCount'};
		}
		
		/**
		 * @param int $count new row count
		 * @return string result of the request, in json if no errors
		 */
		public function setRowCount($count) {
			return $this->update(null, $count);
		}
		
		/**
		 * @param
		 * @return
		 */
		public function create_cell() {
		}
		
		/**
		 * @param int $minrow min row the feed will range, inclusive
		 * @param int $mincol min col the feed will range, inclusive
		 * @param int $maxrow max row the feed will range, inclusive
		 * @param int $maxcol max col the feed will range, inclusive
		 * @return Feed<Cell>
		 */
		public function cell_feed($minrow = null, $mincol = null, $maxrow = null, $maxcol = null) {
			return Cell::feed($this->ssid(), $this->id(), $minrow, $mincol, $maxrow, $maxcol);
		}
		
		/**
		 * @param int $cellid id of the cell
		 * @return Cell with the specified id
		 */
		public function cell($cellid) {
			return Cell::single_rc($this->ssid(), $this->id(), $cellid);
		}
		
		/**
		 * @param int $row
		 * @param int $col
		 * @return Cell cell in the specified $row and $col
		 */
		public function cell_rc($row, $col) {
			return Cell::single_rc($this->ssid(), $this->id(), $row, $col);
		}
		
		/**
		 * @param
		 * @return
		 */
		public function list_feed() {
		}
	}
	
	/**
	 * 
	 */
	final class Cell extends AtomObject {
		/**
		 * @param stdObj $feed json decoded feed
		 */
		protected function __construct($feed) {
			parent::__construct($feed);
		}
		
		/**
		 * @param int $ssid spreadsheet id
		 * @param int $wsid worksheet id
		 * @param Cell $minrow 
		 * @param Cell $mincol 
		 * @param Cell $maxrow 
		 * @param Cell $maxcol 
		 * @return Feed<Cell>
		 */
		public static function feed($ssid, $wsid, $minrow = null, $mincol = null, $maxrow = null, $maxcol = null) {
			$mincol = isset($mincol) ? "&min-col=$mincol" : $mincol;
			$maxcol = isset($maxcol) ? "&max-col=$maxcol" : $maxcol;
			$minrow = isset($minrow) ? "&min-row=$minrow" : $minrow;
			$maxrow = isset($maxrow) ? "&max-row=$maxrow" : $maxrow;
			$url = "https://spreadsheets.google.com/feeds/cells/$ssid/$wsid/private/full?alt=json$mincol$maxcol$minrow$maxrow";
			$feed = gget('GET', $url);
			$feed = json_decode($feed);
			$ret = new Feed();
			if($feed->feed->entry) { // no entry means no cells
				foreach($feed->feed->entry as $cell) {
					$ret[] = new Cell($cell);
				}
			} else {
				if($feed->entry) {
					$ret[] = new Cell($feed->entry);
				}
			}
			return $ret;
		}
		
		/**
		 * @param int $ssid spreadsheet id
		 * @param int $wsid worksheet id
		 * @param int $cellid cell id
		 * @return Cell
		 */
		public static function single($ssid, $wsid, $cellid) {
			$url = "https://spreadsheets.google.com/feeds/cells/$ssid/$wsid/private/full/$cellid?alt=json";
			return new Cell(json_decode(gget('GET', $url))->entry);
		}
		
		/**
		 * @param int $ssid spreadsheet id
		 * @param int $wsid worksheet id
		 * @param int $row
		 * @param int $col
		 * @return Cell
		 */
		public static function single_rc($ssid, $wsid, $row, $col) {
			$url = "https://spreadsheets.google.com/feeds/cells/$ssid/$wsid/private/full/"."R$row"."C$col"."?alt=json";
			$get = gget('GET', $url);
			return new Cell(json_decode($get)->entry);
		}
		
		/**
		 * @param int $ssid spreadsheet id
		 * @param int $wsid worksheet id
		 * @param int $row
		 * @param int $col
		 * @param string $val inputValue, google excel formula
		 * @return Cell
		 */
		public static function create($ssid, $wsid, $row, $col, $val) {
			$headers[] = 'If-Match: *';
			$url = "https://spreadsheets.google.com/feeds/cells/$ssid/$wsid/private/full/"."R$row"."C$col"."?alt=json";
			$entry = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">
					<id>https://spreadsheets.google.com/feeds/cells/' . "$ssid/$wsid" . '/private/full/' . "R$row"."C$col" . '</id>
					<link rel="edit" type="application/atom+xml" href="https://spreadsheets.google.com/feeds/cells/' . "$ssid/$wsid" . '/private/full/' . "R$row"."C$col" . '"/>
					<gs:cell row="'.$row.'" col="'.$col.'" inputValue="'.$val.'"/>
				</entry>';
			return new Cell(gget('PUT', $url, $headers, $entry));
		}
		
		/**
		 * @return int spreadsheet id
		 */
		public function ssid() {
			$id = parent::id();
			$id = explode('/', $id);
			return $id[5];
		}
		
		/**
		 * @return in worksheet id
		 */
		public function wsid() {
			$id = parent::id();
			$id = explode('/', $id);
			return $id[6];
		}
		
		/**
		 * @return int cell id
		 */
		public function id() {
			$id = parent::id();
			$id = explode('/', $id);
			return $id[7];
		}
		
		/**
		 * @param string $val google excel formula
		 * @return string result from the request, usually json if no errors
		 */
		public function update($val) {
			$headers[] = 'If-Match: *';
			$col = $this->getCol();
			$row = $this->getRow();
			$sskey = $this->ssid();
			$wsid = $this->wsid();
			$url = "https://spreadsheets.google.com/feeds/cells/$sskey/$wsid/private/full/"."R$row"."C$col"."?alt=json";
			$entry = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">
					<id>https://spreadsheets.google.com/feeds/cells/' . "$sskey/$wsid" . '/private/full/' . "R$row"."C$col" . '</id>
					<link rel="edit" type="application/atom+xml" href="https://spreadsheets.google.com/feeds/cells/' . "$sskey/$wsid" . '/private/full/' . "R$row"."C$col" . '"/>
					<gs:cell row="'.$row.'" col="'.$col.'" inputValue="'.$val.'"/>
				</entry>';
			return gget('PUT', $url, $headers, $entry);
		}
		
		/**
		 * @return int column of cell
		 */
		public function getCol() {
			return $this->{'gs$cell'}->{'col'};
		}
		
		/**
		 * @return int row of cell
		 */
		public function getRow() {
			return $this->{'gs$cell'}->{'row'};
		}
		
		/**
		 * @return string google excel formula
		 */
		public function getInput() {
			return $this->{'gs$cell'}->{'inputValue'};
		}
		
		/**
		 * @return int numerice result of formula
		 */
		public function getNumeric() {
			return $this->{'gs$cell'}->{'numericValue'};
		}
		
		/**
		 * @return string text result of forumla
		 */
		public function getText() {
			return $this->{'gs$cell'};
		}
	}
	
	/**
	 * 
	 */
	final class CellList extends AtomObject {
		/**
		 * @param
		 * @return
		 */
		protected function __construct($feed) {
			die('CellLists are not implemented yet');
			parent::__construct($feed);
		}
	}
	
	/**
	 * 
	 */
	final class Collection extends AtomObject {
		/**
		 * @param
		 * @return
		 */
		protected function __construct($feed) {
			die('Collections are not implemented yet');
			parent::__construct($feed);
		}
		
		// root is root collection
	
		// gets a list of collections the user owns
		// title: filter by title of each collection
		// max: maximum number of results to return
		// returns JSON encoded string of collection
		function feed($title = null, $max = 10, $token = null) {
			if(isset($title)) {
				$title = "&title=$title";
			}
			if(isset($max)) {
				$max = "&max-results=$max";
			}
			$method = 'GET';
			$url = "https://docs.google.com/feeds/default/private/full/-/folder?alt=json$max$title";
			return gget($token, $method, $url);
		}
		
		// id: id for the folder
		// sskey: id for the spreadsheet
		// type: document, drawing, file, folder, pdf, presentation, spreadsheet
		// returns JSON encoded string
		function add($id, $sskey, $type = 'spreadsheet', $token = null) {
			$url = "https://docs.google.com/feeds/default/private/full/folder%3A$id/contents?alt=json";
			$entry = "<?xml version='1.0' encoding='UTF-8'?>" .
				'<entry xmlns="http://www.w3.org/2005/Atom">'.
				"<id>https://docs.google.com/feeds/default/private/full/$type:$sskey</id>" .
			'</entry>';
			return gget($token, 'POST', $url, array(), $entry);
		}
		
		// $id: id only for the folder
		// $sskey: key for the spreadsheet, or anything else
		// returns JSON encoded string
		function remove($id, $sskey, $token = null) {
			$url = "https://docs.google.com/feeds/default/private/full/folder%3A$id/contents/$sskey?alt=json";
			$headers[] = 'If-Match: *';
			return gget($token, 'DELETE', $url, $headers);
		}
		
		// title: name of new collection
		// returns JSON encoded string
		function create($title, $token = null) {
			$url = "https://docs.google.com/feeds/default/private/full?alt=json";
			$entry = "<?xml version='1.0' encoding='UTF-8'?>" .
				'<entry xmlns="http://www.w3.org/2005/Atom">
				<category scheme="http://schemas.google.com/g/2005#kind" term="http://schemas.google.com/docs/2007#folder"/>' .
				"<title>$title</title>" .
			'</entry>';
			return gget($token, 'POST', $url, array(), $entry);
		}
		
		// id: id for the folder
		// returns JSON encoded string
		function delete($id, $token = null) {
			$url = "https://docs.google.com/feeds/default/private/full/$id?delete=true&alt=json";
			$headers[] = 'If-Match: *';
			return gget($token, 'DELETE', $url, $headers);
		}
		
		// id: id for the folder
		// title: name of new sub collection
		// returns JSON encoded string
		function create_sub($id, $title, $token = null) {
			$url = "https://docs.google.com/feeds/default/private/full/folder%3A$id/contents?alt=json";
			$entry = "<?xml version='1.0' encoding='UTF-8'?>" .
				'<entry xmlns="http://www.w3.org/2005/Atom">
				<category scheme="http://schemas.google.com/g/2005#kind"
				term="http://schemas.google.com/docs/2007#folder"/>'.
				"<title>$title</title>".
			'</entry>';
			return gget($token, 'POST', $url, array(), $entry);
		}
	}
?>