<?php
	/**
	* Upload the specified document
	*
	* @param Zend_Gdata_Docs $docs The service object to use for communicating
	*     with the Google Documents server.
	* @param boolean $html True if output should be formatted for display in a
	*     web browser.
	* @param string $originalFileName The name of the file to be uploaded. The
	*     MIME type of the file is determined from the extension on this file
	*     name. For example, test.csv is uploaded as a comma separated volume
	*     and converted into a spreadsheet.
	* @param string $temporaryFileLocation (optional) The file in which the
	*     data for the document is stored. This is used when the file has been
	*     uploaded from the client's machine to the server and is stored in
	*     a temporary file which does not have an extension. If this parameter
	*     is null, the file is read from the originalFileName.
	*/
	function uploadDocument($docs, $html, $originalFileName, $temporaryFileLocation) {
	  $fileToUpload = $originalFileName;
	  if ($temporaryFileLocation) {
		$fileToUpload = $temporaryFileLocation;
	  }
	 
	  // Upload the file and convert it into a Google Document. The original
	  // file name is used as the title of the document and the MIME type
	  // is determined based on the extension on the original file name.
	  $newDocumentEntry = $docs->uploadFile($fileToUpload, $originalFileName, null, Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI);
	 
	  echo "New Document Title: ";
	 
	  if ($html) {
		  // Find the URL of the HTML view of this document.
		  $alternateLink = '';
		  foreach ($newDocumentEntry->link as $link) {
			  if ($link->getRel() === 'alternate') {
				  $alternateLink = $link->getHref();
			  }
		  }
		  // Make the title link to the document on docs.google.com.
		  echo "<a href=\"$alternateLink\">\n";
	  }
	  echo $newDocumentEntry->title."\n";
	  if ($html) {echo "</a>\n";}
	}
?>