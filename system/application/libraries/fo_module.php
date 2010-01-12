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
			
		    while (false !== ($file = readdir($handle))) {
		    	if ($file == "." || $file == "..") continue;
		    	if (is_dir($wpath."/".$file)) {
					if (is_dir($wpath."/".$file."/controllers")){
			    		$ret = $this->getHMVCModules($wpath."/".$file."/controllers", $file);
	    				if ($ret) { //ret is like a surrogent parent
    						$root->Children[] = $ret;//$child;
	    				}
					} 
		    	}
			}
	    	closedir($handle);
		}
		return $root;
	}

	function getHMVCModules($wpath = "", $fragment = ""){
		$root = new Module();
		$root->fragment = $fragment;
		
		if (is_dir($wpath)){
			$handle = opendir($wpath);
		    while (false !== ($file = readdir($handle))) {
		    	if ($file[0] == "." || $file == "..") continue;
		    	if (is_dir($wpath."/".$file)) {
		    		$newpath = $wpath."/".$file;
		    		$ret = $this->getHMVCModules($newpath, "$fragment/$file");
	    			if ($ret) { //ret is like a surrogent parent
	    				if ($fragment == $file) {
	    					foreach($ret->Children as $child){
	    						$root->Children[] = $child;
	    					}
						} else {
							$root->Children[] = $ret;//$child;
						}
	    			}
		    	} else {
		    		if (preg_match('/.php$/i', $file) > 0 ) {
		    			$filep = substr($file, 0, strlen($file)-4 );
			    		$newpath = $wpath."/".$filep;
						if ($fragment == $filep){	
		    				$ret = $this->getHMVCModules($newpath, "$fragment");
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
    		closedir($handle);
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
	
/*	function getModules($path = "", $fragment = ""){
		$root = false;
		$dirs = false;
		$files = false;
		
//		echo "finding out about '$path'<br/>";
		$wpath =  dirname(dirname(__FILE__)."..")."/controllers";
		$wpath .= "/".$path;
//		echo $wpath;
		if ($path == ""){
			$root = new Module();
			$root->fragment = "";
		} else {
			$root = new Module();
			$root->fragment = $path;
		}
  // 		$root->text = "roott"; 
		if (is_dir($wpath)){
//			echo "$wpath resolves to dir<br/>";
			$handle = opendir($wpath);
		    while (false !== ($file = readdir($handle))) {
		    	if ($file[0] == "." || $file == "..") continue;
		    	if (is_dir($wpath."/".$file)) {
//		    		echo "found directory $file<br/>"; 
		    		if ($path == "") $newpath = $file;
		    		else $newpath = $path."/".$file;
		    		$ret = $this->getModules($newpath);
	    			if ($ret) { //ret is like a surrogent parent
	    				//foreach($ret->Children as $child){
	    					$root->Children[] = $ret;//$child;
	    				//}
	    			}
		    	} else {
		    		if (preg_match('/.php$/i', $file) > 0 ) {
//		    			echo "found php file $file<br/>";
		    			$filep = substr($file, 0, strlen($file)-4 );
			    		if ($path == "") $newpath = $filep;
			    		else $newpath = $path."/".$filep;
		    			$ret = $this->getModules($newpath);
		    			if ($ret) { //ret is like a surrogent parent
		    				foreach($ret->Children as $child){
		    					$root->Children[] = $child;
		    				}
		    			}
		    		} else{
//		    			echo "non-php file $file<br/>";
		    		}
		    	}
    		}
    		closedir($handle);
			//scan files in dir & other dirs, adding to $files and $dirs
		}else if (is_file($wpath.".php")){
//			echo "$wpath resolves to file";
			$ret = $this->_parseFile($path, $wpath.".php");
			if ($ret) $root->Children[] = $ret;
		} else {
//			echo "$wpath resolve to unreadable file/dir";
		}
		return $root;
	}*/
	
	function getModules(){
		return $this->getHMVCRootModule();
	}
	
	function _getClassNames($path = ""){
		
	}
	
	function _getDirNames($path = ""){
		
	}
	
	function _parseFile($path, $wpath){
//		echo "Including file $wpath<br/>";
		$root = false;
		$module = false;
		$components = split("/", $path);
		$cName = $components[(count($components)-1)];
		if (!class_exists($cName)){
			include($wpath);
		}
		$ignoreNames = array("get_instance", "controller", "ci_base", "database", strtolower($cName),
			"helpers", "helper", "language", "library", "model", "models", "module", "modules",
			"plugin", "plugins", "view", "ci_loader", "dbforge", "file", "vars", "scaffold",
			"scaffolding", "dbutil", "scaffold_language", "config");
		if (class_exists($cName)){
			$root = new Module();
			$root->fragment = $path;
			$methods = get_class_methods($cName);
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