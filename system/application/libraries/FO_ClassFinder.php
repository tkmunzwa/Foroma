<?php
/**
 * Utility class to find certain controller classes that match either a superclass, an implemented interface
 * or the existance of a named method (whether inherited or defined in current class).  Interfaces that are
 * considered to be matched may have been implemented in parent/ancestor class(es)
 *
 * This file is an Foroma addition
 * (c) 2010 Tapiwa Munzwa
 *
 * 	tapiwa@munzwa.tk
 * 
 */
class FO_ClassFinder{

	private $_hasMethod = false;
	private $_implements = false;
	private $_extends = false;
	private $_matchedClasses;
	
	function FO_ClassFinder(){
		
	}
	
	function setMatchMethod($m){
		$this->_hasMethod = $m;
	}	
	function setMatchInterface($m){
		$this->_implements = $m;
	}
	
	/**
	 * Set match method to find matching superclass
	 * @return 
	 * @param string $c
	 */
	function setMatchSuperclass($c){
		$this->_extends = $c;
	}
	
	/**
	 * Process all matched controller classes & return them. Have to set a matching method first,
	 * otherwise returned match is guaranteed to be null
	 * @return array with matching class=>URL path 
	 */
	function getMatches(){
		$wpath =  dirname(dirname(__FILE__)."..")."/modules";
		if (is_dir($wpath)){
			$handle = opendir($wpath);
			
			$arr_files = array();

			//Get files in module root directory into array, then sort the array
		    while (false !== ($file = readdir($handle))) {
		    	if ($file == "." || $file == "..") continue;
		    	$arr_files[] = $file;
			}
	    	closedir($handle);
			sort($arr_files);
			
			//process each file
			foreach($arr_files as $file){

		    	if (is_dir($wpath."/".$file)) {
					if (is_dir($wpath."/".$file."/controllers")){
			    		$this->processPath($wpath."/".$file."/controllers", $file);
					} 
		    	}
			}
		}
		return $this->_matchedClasses;
	}

	/**
	 * Recursively process files/dirs in the given directory.
	 * @return 
	 * @param object $wpath[optional] path (full path)
	 * @param object $fragment[optional] current file fragment
	 */
	function processPath($wpath = "", $fragment = ""){

		if (is_dir($wpath)){ //is path a directory? if so, find file names inside
			if (!$handle = opendir($wpath)){
				log_message('error', "Failed to process directory $wpath");
				return false; //failed to process file
			}
			$arr_files = array();
		    while (false !== ($file = @readdir($handle))) {
		    	if ($file[0] == "." || $file == "..") continue;
				$arr_files[] = $file; 
			}
			closedir($handle);
			sort($arr_files);
			
			
			foreach($arr_files as $file){
		    	if (is_dir($wpath."/".$file)) { //curent 'file' is a directory
		    		$newpath = $wpath."/".$file;
		    		$ret = $this->processPath($newpath, "$fragment/$file");

		    	} else { //constructed path is infact, a file
		    		if (preg_match('/.php$/i', $file) > 0 ) {
		    			$filep = substr($file, 0, strlen($file)-4 );
			    		$newpath = $wpath.DIRECTORY_SEPARATOR.$filep;
						if ($fragment == $filep){
		    				$ret = $this->processPath($newpath, "$fragment", TRUE);
						}else{
							$ret = $this->processPath($newpath, "$fragment/$filep");
						}
		    		}
		    	}
    		}
			//scan files in dir & other dirs, adding to $files and $dirs
		} else if (is_file($wpath.".php")){

			$this->_parseFile($fragment, $wpath.".php");
		}
		return true;
	}
	

	
	/**
	 * Go through passed Controller class. If it matches the set parameter, add to the array of
	 * matched items
	 * 
	 * @return true on success, false on error
	 * @param string $path the CI URL fragment required to get to this controller
	 * @param string $wpath Working path: the path on the hardisk
	 */
	private function _parseFile($path, $wpath){

		$components = split("/", $path);
		$cName = $components[(count($components)-1)];

 		if (!class_exists($cName, false)){//DO NOT AUTOLOAD. *****! spent 4 hours trying to figure out why class loading was wonky
			include_once($wpath);
		}


		if (class_exists($cName)){ //ensure class definition is available first

			//match using method name, if set
			if ($this->_hasMethod){
				$methods = get_class_methods($cName);	//fetch method names into array (including inherited ones)

				if (in_array($this->_hasMethod, $methods)){	//check if any of the methods match
					$this->_addMatch($cName, $path);
				}			
			}
			
			//match using superclass, if set
			if ($this->_extends){
				if (is_subclass_of($cName, $this->_extends)){
					$this->_addMatch($cName, $path);
				}
			}
			
			//match using interface, if set
			if ($this->_implements){
				$interfaces = class_implements($cName, false); //fetch interfaces into array
				
				if (in_array($this->_implements, $interfaces)){ //check if any of the interfaces match
					$this->_addMatch($cName, $path);
				}
			}
		} else {
			return false;
		}
		return true;
	}
	
	
	/**
	 * Add matched item to maches array in form ['class name'=> 'controller path']
	 * 
	 * @return 
	 * @param string $className name of class to be saved
	 * @param string $fragment path of the controller
	 */
	private function _addMatch($className, $fragment){
		if (!$this->_matchedClasses){
			$this->_matchedClasses = array();
		}
		$this->_matchedClasses[$className] = $fragment;
	}
}

/*end of FO_ClassFinder file*/