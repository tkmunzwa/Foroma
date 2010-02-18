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
					$u->unlink("Groups");
					$q = Doctrine_Query::create()
					->from('Group g')
					->whereIn('g.id', $filter['groups']);
					$g = $q->execute();					
					$this->firephp->info($g->toArray(true));
					$g_arr = array();
					foreach($g as $item){
						$u->Groups[] = $item;
					}
					/*$q = Doctrine_Query::create()
						->from('Language l')
						->where('l.name', $filter['lang']);
					$l = $q->execute();*/
					$l = Doctrine::getTable('Language')
					->findOneByName($filter['lang']);
					/*@$this->firephp->info($q->getSqlQuery);
					$l = Doctrine::getTable('Language')
						->findOneByName($filter['lang']);
					$this->firephp->info($l->name);*/
					//$this->firephp->info(@$l->name);
					if ($l) $u->Language = $l;
					try{
						$u->save();
						$user = $this->fo_user->getUser();
				//					echo "{$user->id} == {$u->id}";
						if ($user->id == $u->id){//current user
							$this->session->set_userdata(array('user_id'=> $u->id, 'username'=>$u->username,
								'language'=>$u->language));
						}
						return TRUE;
					} catch  (Exception $e) {
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
				if ($this->save($id, &$u)) {//save suceeded
					$this->firephp->info("save ok");
					if ($u->id == "new"){
						$u = Doctrine::getTable('Users')
						->findOneByUsername($filter['username']);
					}
					$this->session->set_flashdata("message", sprintf(lang("user_saved"), $u->username));
					redirect('admin/users/listall');
				}else{//save failed
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