<?php
class FO_SwiftMailer {
	function FO_SwiftMailer(){
		#ini_set('include_path', ini_get('include_path').':'.realpath(dirname(__FILE__).DIRECTORY_SEPARATOR."swiftmailer").DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR);
		#require_once "Swift.php";
		require_once realpath(dirname(__FILE__).DIRECTORY_SEPARATOR."swiftmailer").DIRECTORY_SEPARATOR."swift_required.php";
	}
	
}

?>