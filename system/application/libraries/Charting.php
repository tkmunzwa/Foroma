<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR."pchart".DIRECTORY_SEPARATOR."pChart.php";
require_once realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR."pchart".DIRECTORY_SEPARATOR."pData.php";
require_once realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR."pchart".DIRECTORY_SEPARATOR."pCache.php";

class Charting {
	static $temp_dir = "/tmp";

	function pChart($xSize, $ySize){
		$obj = new pChart($xSize, $ySize);
		return $obj;
	}
	
	function pData(){
		$obj = new pData();
		return $obj;
	}
	
	function getFontDir(){
		return realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR."pchart".DIRECTORY_SEPARATOR."fonts";
	}
	
	function getTempDir(){
		return Charting::$temp_dir;
	}
}

?>