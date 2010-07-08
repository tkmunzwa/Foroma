<?php
//namespace  controllers;

class Users	 extends Controller
{

	var $_infos;
	var $_errors;
	var $groups;//collection of avail groups
	var $langs;//collection of avail langs
	
	function Users(){
		parent::Controller();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->library('FirePHP');
		$this->template->set_loader($this->load);
		$this->load->language('admin', $this->fo_lang->userLanguage());
		$this->load->language('users', $this->fo_lang->userLanguage());
		$this->template->write('title', 'User Management');
		$this->groups = Doctrine_Query::create()
			->from('Group')
			->orderBy('name')
			->execute();
		$this->langs = Doctrine_Query::create()
			->from('Language')
			->orderBy('name')
			->execute();
		
		if ($message = $this->session->flashdata('message'))
			$this->_info($message);	
		if ($e = $this->session->flashdata('error'))
			$this->_error($e);	

	}
	
	/**
	 * Default controller. Default action is to list all users
	 * @return 
	 */
	function index(){
		$this->listall();
	}

	/**
	 * Controller to handle creation of new users. On initial load, it displays blank user form. Also handles submission of
	 * the said form & detects using 'action' variable. If form is being submitted, attempts to save data to db. Redirects 
	 * to default controller on success, otherwise displays error message above form on error
	 * 
	 * @return 
	 */
	function create(){
		$id = FALSE;
		$u = FALSE;
		$this->form_validation->set_rules('password', lang('password'), 'required|matches[passconf]');
		$this->form_validation->set_rules('passconf', lang('password_repeat'), 'required');
		$this->form_validation->set_rules('username', lang('username'), 'required|min_length[5]|max_length[12]');
		$this->form_validation->set_rules('lang', lang('language'), 'required');
		$this->form_validation->set_rules('email', lang('email_add'), 'required|valid_email');
		if (@$_REQUEST['action']) {
			if ($this->save($id, $u)){
				$this->session->set_flashdata('message', sprintf(lang('user_saved'), $u->username));
				redirect("admin/users");
			} else {
				$u = new User();//set dummy user so not to throw errors on renders
				$u->id = $_REQUEST['user_id'];	
				$this->template->write_view("content", "user_edit", array("data"=> array ('user'=>$u, "controller"=>"admin/users/create",
				 "role"=>"new", "errors" => $this->_errors, 'groups'=>$this->groups, 'langs'=>$this->langs)));
				 $this->firephp->error($this->_errors);
				$this->template->render();
			}
		} else {
			$u = new User();
			$u->id = "new";
			$this->template->write_view("content", "user_edit", array("data"=>array('user'=>$u,
				'controller'=>'admin/users/edit', 'groups'=>$this->groups, 'langs'=>$this->langs)));
			$this->template->render();
		}
	}

	/**
	 * 
	 * @return 
	 * @param object $id[optional]
	 * @param object $u[optional]
	 */
	private function save($id = FALSE, &$u = FALSE){
		$error = false;
		$filter = array();
		if(!$id) @$id = $_REQUEST['user_id'];
		@$filter['email'] = $_REQUEST['email'];//FIXEM: xss clean
		@$filter['username'] = $_REQUEST['username'];//FIXME: xss_clean
		@$filter['password'] = $_REQUEST['password'];//FIXME - xss_clean
		@$filter['groups'] = $_REQUEST['groups'];//xss_cleam me
		@$filter['lang'] = $_REQUEST['lang'];//xss_cleam me
		
		if ($this->form_validation->run() == FALSE) {
			return FALSE;
		} else {
			if ($id){
				if ($id == "new"){
					$u = new User();
				} else {
					$u = Doctrine::getTable('User')
					->findOneById($id);
					/*$q = Doctrine_Query::create()
					->from("User u")
					->where("u.id", $id);
					$u = $q->execute();*/
				}
				if ($u){
					$u->emailaddress=$filter['email'];
					$u->username = $filter['username'];
					if (isset($filter['password']) && $filter['password'] != "")
						$u->setPassword($filter['password']);

					
					/************************
					 * THe following code is one dirty, dirty, dirty hack. Doctrine_Recored::unlink() does not seem to work very well when the 
					 * unlinked record is added right back before a save - the record is *not* saved. So, to bypass this, determine 
					 * which records to be unlinked($remove_groups) (exclude those selected by user - $filter['groups']). Actual Doctrine_Record
					 * instances need to be added to the $u->Group[] array, so store an associative array (called $arr_db_keys) using ID as the
					 *  key and position in db-retrieved array($g) as value. 
					 *  
					 *  ...snip to later:
					 *   Found a simpler alternative of simply calling save() after unlink()ing but before adding to child group. Adds a DB write
					 *   operation
					 */
					$q = Doctrine_Query::create()
						->from('Group g');
//						->whereIn('g.id', $filter['groups']); //ideally should fetch user selected groups, unlink() all previous and link resultant records...
					$g = $q->execute();

/*
					//the code below sadly does not work, due to Doctrine_Record::unlink() wierdness
 					$u->unlink('Groups');
					foreach($g as $item){
						$u->Groups[] = $item;
					}
*/

					$arr_db_ids = array(); //IDs fetched from database
					$arr_db_keys = array(); //Look-up array for objects retrieved from database
					foreach($g as $k=>$item){
						$arr_db_ids[] = $item['id'];
						$arr_db_keys[$item['id']] = $k;//store key in look-up array. can't be too sure about how doctrine produces keys.
					}
					$remove_groups = array_diff($arr_db_ids, $filter['groups']); //find out difference. ie, groups that should be unlinked
					@$u->unlink("Groups", $remove_groups);
					foreach($filter['groups'] as $item){ //now add user-selected groups to recored
						$u->Groups[] = $g[$arr_db_keys[$item]];
					}
					//the end of the dirty hack. I feel like I need a shower.

					$l = Doctrine::getTable('Language')
					->findOneByName($filter['lang']);

					if ($l) $u->Language = $l;
					try{
						$u->save();
						$user = $this->fo_user->getUser();

						if ($user->id == $u->id){//if user being edited is current user, update session values immediately.
							$this->session->set_userdata(array('user_id'=> $u->id, 'username'=>$u->username,
								'language'=>$u->language));
						}
						return TRUE;
					} catch  (Exception $e) {
						$this->warn($e);
						$this->firephp->error("doctrine item fail". $e);
						$this->_error(lang('db_error'));
						if ($id == "new") //if save failed for new item - keep form id as 'new'
							$u->id = "new";
						return FALSE;
					}
				}else{
					$this->_error(lang('user_cant_save').lang('user_id_not_found'));
					return FALSE;
				}
				//try & save the form
			} else {
				$this->_error(lang('user_cant_save').lang('user_id_not_set'));
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * 
	 * @return 
	 * @param object $id[optional] id of the user to edit
	 * @param object $method[optional] Transport to be used to respond, may be html(default), json or xml. currently html is supported
	 */
	function edit($id = FALSE, $method="html") {
		$error = FALSE;
		$this->form_validation->set_rules('password', 'Password', 'matches[passconf]');
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|max_length[12]');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$filter = array();
		@$filter['username'] = $_REQUEST['username'];
		$u = new User();
		if (!$id) @$id = $_REQUEST['user_id'];
		$u->id = $id;
		if ($id) {
			if (isset($_REQUEST['action'])) { //record being saved from form 
				if ($this->save($id, $u)) {//save suceeded
					$this->firephp->info("save ok");
					if ($u->id == "new"){
						$u = Doctrine::getTable('Users')
						->findOneByUsername($filter['username']);
					}
					$this->session->set_flashdata("message", sprintf(lang("user_saved"), $u->username));
					redirect('admin/users/listall');
				}else{//save failed
					$this->firephp->warn("save not ok");
					$this->_error("Error saving record");
					$this->firephp->error("Save failed");
				}
			} else { //display edit form
				$this->firephp->info("no action");
				//load data from database using id
				$u = Doctrine::getTable('User')
				->findOneById($id);
			}
			if (!$u){
				$this->session->set_flashdata("error", lang('user_cant_edit'). sprintf(lang('user_id_not_found'), $id));
				redirect("admin/users/listall");
				return;
			}
//			$this->template->write_view("content", "user_edit", array(
//			 	"data"=>array("controller"=>"admin/users/edit/".$u->id, "user"=>$u, "messages"=>$this->_infos, "errors"=>$this->_errors)));			
		} else {
			$this->session->set_flashdata("error", lang('user_cant_edit'). lang('user_id_not_set'));
			redirect("admin/users/listall");
			return;
		}
		
		$this->template->write_view("content", "user_edit",array(
		 "data"=>array("controller"=>"admin/users/edit/".$u->id, "user"=>$u, 'groups'=>$this->groups, 'langs'=>$this->langs, "messages"=>$this->_infos, "errors"=>$this->_errors)));
		$this->template->render();
	}
	
	function listall(){
		$q = Doctrine_Query::create()
			->from('User u')
			->orderBy('u.username');
		$users = $q->execute();
		$this->template->write_view("content", "user_list", array("data"=>$users, "messages"=>$this->_infos, "errors"=>$this->_errors));
		$this->template->render();
	}
	function delete($id = FALSE){
		$name = "";
		if($id) {
			try{
				$u = Doctrine::getTable('User')
					->findOneById($id);
				if($u){
					@$name = $u->username;
					$u->delete();
					$this->session->set_flashdata("message", sprintf(lang("user_deleted"), $name));
				} else {
					$this->session->set_flashdata("error", lang('user_cant_delete'). sprintf(lang('user_id_not_found'), $id));
				}
			}catch (Exception $e){
				$this->session->set_flashdata("error", lang("db_error"));
			}
		} else {
			$this->session->set_flashdata("error", lang('user_cant_delete').lang('user_id_not_set'));
		}
//		/$this->firephp->info("$error");
		redirect("admin/users/listall");
	}
	
	function _error($message) {
		if (!$this->_errors)
		$this->_errors = array ();
		$this->_errors[] = $message;
	}
	function _info($message) {
		if (!$this->_infos)
		$this->_infos = array ();
		$this->_infos[] = $message;
	}
}
?>