<?php
/**
 * This file is an Foroma addition
 * (c) 2009 Tapiwa Munzwa
 * 	tapiwa@munzwa.tk
 */
class FO_Auth_SecurityHook{
	var $CI;
	
	function checkPermissions(&$instance){
		$segments = array();
		$allowAccess = FALSE;
		$this->CI = $instance;
		$this->CI = &CI_Base::get_instance();
		$this->CI->load->library('fo_user');
		$RTR = & load_class('Router');
		$method =  $RTR->fetch_method();
		$class = $RTR->fetch_class();
		$dir = $RTR->fetch_directory();	
		
		$fragment = $dir.$class."/".$method;


		//debug
//		echo "Dir:".$RTR->fetch_directory()."<br/>";
;
		
		//if using HMVV compartmentalisation TODO: check config var or autodetect
		if(true){
			$cnt = 1;
			for($cnt =1; $this->CI->uri->segment($cnt)!== false; $cnt++){
				$segments[] = $this->CI->uri->segment($cnt);
				$dirprocessed = false;
				if ($this->CI->uri->segment($cnt) == $method) {
					if ($method == $dir){ //if name of method is same as directory, check which segment is checked first
						if ($dirprocessed) break; //if directory segment has been processed
						else $dirprocessed = true;
					} else {
				 		break;//skip segments after the method
					}
				}
			}
			$fragment = join("/", $segments);
		}else{
			if ($dir)$segments[] = $dir;
			if($class) $segments[] = $class;
			$segments[] = $method;
		}
		$fragment = join("/", $segments);
		$q = Doctrine_Query::create()
			->from('Module m')
			->where('m.fragment = ?', $fragment );
		// if method is 'index' it's possible only actual method was not set up in security
		//make an exception, just in case
		if ($method == "index"){
			$q = $q->orWhere('m.fragment = ?', join("/", array_slice($segments, 0, (count($segments)-1))));
		}
		$module = $q->fetchOne();
		if (!$module || count($module) < 1){ //not matching results, let's try again with more generous parameters
			//check to see if parent is defined with no children (if no children defined, then access defaults to all children)
			$q = Doctrine_Query::create()
				->from('Module m')
				->where('m.fragment = ?', join("/", array_slice($segments, 0, (count($segments)-1) )));
			// if class is default class it's possible only directory was set up in security
			//make an exception, just in case
			if ($class === $RTR->default_controller){
				$q = $q->orWhere('m.fragment = ?', $dir);
			}
			$module = $q->fetchOne();
			
		}
		if ($module && count ($module) > 0)  {//page not public/free for all based on controller
			if (!$module->ispublic) { //If page is not public
				$user = $this->CI->fo_user->getUser();
				if (!$user){
					$allowAccess = FALSE;
				} else {
					foreach($user->Groups as $group) {
						foreach($group->Permissions as $perm){
							foreach($perm->Modules as $mod){
								if ($this->_hasPermission($fragment, $mod)) {
									$allowAccess = TRUE;
								}
							}
						}
					}
				}
			} else {
				$allowAccess = TRUE;
			}
		} else {
				$error =& load_class('Exceptions');
				$heading = "Security not defined"; //FIXME: i18lize
				$message = sprintf("The security for the resource you are trying to access (%s) is not configured yet", "<b>$fragment</b>") ; //FIXME: i18lize
				echo $error->show_error($heading, $message, 'error_general');
				exit;
		}
		if (!$allowAccess) { //user does not have access to the said controller/method/action
			$error =& load_class('Exceptions');
			$heading = "Permission Denied"; //FIXME: i18lize
			$message = sprintf("You do not have permissions to view specified resource (%s)", "<b>$fragment</b>"); //FIXME: i18lize
			echo $error->show_error($heading, $message, 'error_permission');
			exit;
		}
	}
	
	function _hasPermission($fragment, $module){
		if (!$module) return FALSE;
		if ($fragment === $module->fragment){
			return TRUE;
		//FIXME: remove assumption that parent contains firstpart of child below & just search everything
		//now it assumes /admin/ is parent of /admin/users but it should be possible for /admin/home to be parent of /admin/users
		} else if (stripos($fragment, $module->fragment) !== false){ //module is parent of said fragment. 
			if ($fragment == $module->fragment."/index"){
				return TRUE;
			}
			if ($module->Children && count($module->Children) > 0){
				foreach($module->Children as $child){
					if ($this->_hasPermission($fragment, $child)){ //recurse
						return TRUE;
					}
				}
			} else {
				return TRUE; //since module has no children but matches fragment, assume has access to all children 
			} 
		}
		return FALSE;
	}

}
?>