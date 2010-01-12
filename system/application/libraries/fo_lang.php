<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This file is an Foroma addition
 * (c) 2009 Tapiwa Munzwa
 * 	tapiwa@munzwa.tk
 * Handles user language information
 */
class FO_Lang{
	
	function FO_Lang(){		
	}
	
	function userLanguage(){
		$CI =& get_instance();
		$ret = $CI->config->item("language"); //default to configured default lang
		 
		//try & get cached language if user logged in
		
		if ($CI->session->userdata("language") != ""){
			$ret = $CI->session->userdata("language");
		} else {
			//TODO: if no language set in session, use locale information from browser if available
		}
		//TODO: if none found, raise error?
//		echo "return language =".$ret;
		return $ret;
	}

    function adminLanguage(){
    	$CI =& get_instance();
		$ret = $CI->config->item("admin_language"); //default to configured default lang
		
		if (!$ret) $ret = $CI->config->item("language"); //if admin_language not set, default to 'language'
		//try & get cached language from DB Settings - FIXME		
//		if (){
//		} else {
			//TODO: if no language set in session, use locale information from browser if available
//		}
		//TODO: if none found, raise error?
		return $ret;
    }
    
//    function suffixedUserLanguage(){}
}

?>