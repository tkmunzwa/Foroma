<?php
class FO_User{
	/**
	 * This file is an Foroma addition
	 * (c) 2009 Tapiwa Munzwa
	 * 	tapiwa@munzwa.tk
	 * Handles user object
	 */
	var $user_obj = "";
	var $CI;
	
	function FO_User(){
		$this->CI = & get_instance();
		$this->CI->load->library('fo_lang');
		$this->CI->load->helper('language');
		$this->CI->lang->load('fo_user', $this->CI->fo_lang->adminLanguage());	
		
	}

	function getUser($getGroups = TRUE, $permissions = TRUE, $module_depth=2){
		if ($this->user_obj === "") {
			$orderBy = false;
			$this->CI = & get_instance();
			$this->CI->load->library('session');
			$q = Doctrine_Query::create()
			->from('User u');
			if ($getGroups == TRUE){
				$q = $q->leftJoin('u.Groups g');
				if ($permissions == TRUE) {
					$q = $q->leftJoin('g.Permissions p');
					// a bit of explanation on the table below.
					//Doctrine allows us to left join on tables, and we want to left join on
					// the modules table, which is self refering. What we want to do is to recursively left join
					//to a the level specified by $module_depth, starting off by left joining on Permissions (p)
					// so we will leftJoin()'ing m1, m2, ... mn
					if ($module_depth && $module_depth > 0){						
						$parentTable = 'p';
						$orderBy = array(); //we want to order the modules by menuposition @ each level/depth
						$letter='a';
						for($cnt = 1; $cnt <= $module_depth; $cnt++) {
							$letter++;
							if ($cnt == 1){
								 $q = $q->leftJoin("{$parentTable}.Modules m{$letter}");
								 $parentTable = "m{$letter}";
								$orderBy[] = "m{$letter}.menuposition";
							}else {
								$q = $q->leftJoin("{$parentTable}.Children m{$letter}");
								$parentTable = "m{$letter}";
								$orderBy[] = "m{$letter}.menuposition";
							}
						}
					}
				}
			}
			$q = $q->where('u.id = ?', $this->CI->session->userdata("user_id"));
			if ($orderBy) $q = $q->orderBy(join(", ", $orderBy));
			$res = $q->fetchOne();
			if ($res && count($res) > 0){
				$user_obj = $res;
			} else {
				if ($this->CI->config->item("enable_public_user")){
					$user_obj = $this->getPublicUser(false, $getGroups, $permissions, $module_depth);				
				} else {
					$user_obj = FALSE; //TODO: return default user? (non-logged in user)
				}
				//if ($) TODO: check config whether to return default user
				//if not, return false, otherwise:
				//if default user found, return obj, else raise error (couldn't find user who's supposed to be there!s)
			}
		}
		return $user_obj;
	}

	/**
	 * Checks to see if the current user is really logged in or not
	 * @return TRUE when user has been authenticated. FALSE if not
	 */
	function isLoggedIn(){
		if ($this->user_obj === ""){
			$user_obj = $this->getUser(TRUE);//suppress errors
		}
		if (!$user_obj){ //if user not set, not logged in
			return FALSE;
		} else { // user set, check if this is the public user or true user
			if ($user_obj->username != $this->CI->config->item("public_user")) { //it's *not* public
				return TRUE;
			} 
		}
		return FALSE;
	}
	
	/**
	 * 
	 * @return The object for public user, if this is enabled in the configuration file (fo_user.php in CI config dir)
	 * @param object $suppressError[optional] Should errors be suppressed? Errors are displayed in error page, may be due to 
	 * configurations error or public username not being found in the database
	 */
	function getPublicUser($suppressError = FALSE, $getGroups = TRUE, $permissions = TRUE, $module_depth=2){
			$this->CI = & get_instance();
			if ((!$this->CI->config->item("enable_public_user"))){
				if (!$suppressError) {
					//generate error; - public user not allowed
					$error =& load_class('Exceptions');
					$heading = $this->CI->lang->line('fo_user_disabled_public_user_header');
					$message = $this->CI->lang->line('fo_user_disabled_public_user_body');
					echo $error->show_error($heading, $message, 'error_general');
					exit;
				} else {
					return false;
				}
			}
			$publicUser = $this->CI->config->item("public_user");
			if (!$publicUser){
				if (!$suppressError) {
					//generate error; - public user not allowed
					$error =& load_class('Exceptions');
					$heading = $this->CI->lang->line('fo_user_undefined_public_user_header');
					$message = $this->CI->lang->line('fo_user_undefined_public_user_body');
					echo $error->show_error($heading, $message, 'error_general');
					exit;
				} else {
					return false;
				}
			}
			$q = Doctrine_Query::create()
			->from('User u');
			if ($getGroups == TRUE){
				$q = $q->leftJoin('u.Groups g');
				if ($permissions == TRUE) {
					$q = $q->leftJoin('g.Permissions p');
					if ($module_depth && $module_depth > 0){						
						$parentTable = 'p';
						$orderBy = array(); //we want to order the modules by menuposition @ each level/depth
						$letter='a';
						for($cnt = 1; $cnt <= $module_depth; $cnt++) {
							$letter++;
							if ($cnt == 1){
								 $q = $q->leftJoin("{$parentTable}.Modules m{$letter}");
								 $parentTable = "m{$letter}";
								$orderBy[] = "m{$letter}.menuposition";
							}else {
								$q = $q->leftJoin("{$parentTable}.Children m{$letter}");
								$parentTable = "m{$letter}";
								$orderBy[] = "m{$letter}.menuposition";
							}
						}
					}
				}
			}
			$q = $q->where('u.username = ?', $publicUser);
			if ($orderBy) $q = $q->orderBy(join(", ", $orderBy));
			$res = $q->fetchOne();
			if ($res && count($res) > 0){
				$user_obj = $res;
			} else {
				if (!$suppressError){
					$error =& load_class('Exceptions');
					$heading = $this->CI->lang->line('fo_user_no_public_user_header');
					$message = $this->CI->lang->line('fo_user_no_public_user_body');
					echo $error->show_error($heading, $message, 'error_general');
					exit;
				}else{
					return FALSE;
				}
			}
			return $user_obj;
	}
}
?>