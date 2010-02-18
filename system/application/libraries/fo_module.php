<?php
class FO_Module{
	/**
	 * This file is an Foroma addition
	 * (c) 2009 Tapiwa Munzwa
	 * v 0.1
	 * 	tapiwa@munzwa.tk
	 * 
	 * Handles user object
	 */
	var $CI;
	
	function FO_Module(){
		$this->CI = & get_instance();
		
	}
	
	function getHMVCRootModule(){
		$wpath =  dirname(dirname(__FILE__)."..")."/modules";
		if (is_dir($wpath)){
			$handle = opendir($wpath);
			$root = new Module();
			
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
			    		$ret = $this->getHMVCModules($wpath."/".$file."/controllers", $file);
	    				if ($ret) { //ret is like a surrogent parent
    						$root->Children[] = $ret;//$child;
	    				}
					} 
		    	}
			}
		}
		return $root;
	}

	/**
	 * Function that returns all modules in a certain path
	 * @return 
	 * @param object $wpath[optional] path (full path)
	 * @param object $fragment[optional] current file fragment
	 * @param object $filepart[optional] filename part
	 */
	function getHMVCModules($wpath = "", $fragment = ""){
		$root = new Module();
		$root->fragment = $fragment;
		
		if (is_dir($wpath)){ //is path a directory? if so, find file names inside
			$handle = opendir($wpath);
			$arr_files = array();
		    while (false !== ($file = readdir($handle))) {
		    	if ($file[0] == "." || $file == "..") continue;
				$arr_files[] = $file; 
			}
			closedir($handle);
			sort($arr_files);
			
			
			foreach($arr_files as $file){
		    	if (is_dir($wpath."/".$file)) { //curent 'file' is a directory
		    		$newpath = $wpath."/".$file;
		    		$ret = $this->getHMVCModules($newpath, "$fragment/$file");
	    			if ($ret) { //ret is like a surrogent parent, take it's childrej & make them current root elements'
	    				if ($fragment == $file) { //if fragment is the same name as file part
	    					foreach($ret->Children as $child){//take it's childrej & make them current root elements'
	    						$root->Children[] = $child;
	    					}
						} else { //otherwise returned element is child of current root element
							$root->Children[] = $ret;//$child;
						}
	    			}
		    	} else { //constructed path is infact a file
		    		if (preg_match('/.php$/i', $file) > 0 ) {
		    			$filep = substr($file, 0, strlen($file)-4 );
			    		$newpath = $wpath."/".$filep;
						if ($fragment == $filep){
		    				$ret = $this->getHMVCModules($newpath, "$fragment", TRUE);
						}else{
							$ret = $this->getHMVCModules($newpath, "$fragment/$filep");
						}
		    			if ($ret) { //ret is like a surrogent parent
		    				if ($fragment == $filep){
		    					foreach($ret->Children as $child){
		    						foreach($child->Children as $grandchild){
		    							$root->Children[] = $grandchild;
									}
		    					}
								
		    				}else {
		    					foreach($ret->Children as $child){
		    						$root->Children[] = $child;
		    					}
							}
		    			}
		    		} else{
//		    			echo "non-php file $file<br/>";
		    		}
		    	}
    		}
			//scan files in dir & other dirs, adding to $files and $dirs
		}else if (is_file($wpath.".php")){
//			echo "$wpath resolves to file";
			$ret = $this->_parseFile($fragment, $wpath.".php");
			if ($ret) $root->Children[] = $ret;
		} else {
//			echo "$wpath resolve to unreadable file/dir";
		}
		return $root;
	}
	
	/**
	 * Function to retrieve all modules
	 * @return 
	 */
	function getModules(){
		return $this->getHMVCRootModule();
	}
	
	function _getClassNames($path = ""){
		
	}
	
	function _getDirNames($path = ""){
		
	}
	
	/**
	 * Function that parses a Controller file to get public controller methods
	 * @return 
	 * @param object $path
	 * @param object $wpath
	 */
	private function _parseFile($path, $wpath){
		$root = false;
		$module = false;
		$components = split("/", $path);
		$cName = $components[(count($components)-1)];

 		if (!class_exists($cName, false)){//DO NOT AUTOLOAD. *****! spent 4 hours trying to figure out why class loading was wonky
			include_once($wpath);
		}
		$ignoreNames = array("get_instance", "controller", "ci_base", "database", strtolower($cName),
			"helpers", "helper", "language", "library", "model", "models", "module", "modules",
			"plugin", "plugins", "view", "ci_loader", "dbforge", "file", "vars", "scaffold",
			"scaffolding", "dbutil", "scaffold_language", "config");
		if (class_exists($cName)){
			$root = new Module();
			$root->fragment = $path;
			
			//fetch all controller methods
			$methods = get_class_methods($cName);
			
			sort($methods);//arrange methods alphabetically
			foreach($methods as $method){
				if ($method[0] === "_") continue; //ignore private methods
					if (array_search(strtolower($method), $ignoreNames)) {
						continue; //ignore constructor, CI_Base & get_instance methods
					}
					$child = new Module();
					$child->fragment = "$path/$method";
					$root->Children[] = $child;
			}
		}
		return $root;
	}

}
?>