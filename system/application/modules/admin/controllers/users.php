<?php
//namespace  controllers;

class Users	 extends Controller
{

	var $_infos;
	var $_errors;
	
	function Users(){
		parent::Controller();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->library('FirePHP');
		$this->template->set_loader($this->load);
		$this->template->write('title', 'User Management');
		if ($message = $this->session->flashdata('message'))
			$this->_info($message);	
		if ($e = $this->session->flashdata('error'))
			$this->_error($e);	

	}

	function index(){
		$this->listall();
	}

	function create(){
		$id = FALSE;
		$u = FALSE;
		$this->form_validation->set_rules('password', 'Password', 'required|matches[passconf]');
		$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required');
		$this->form_validation->set_rules('username', 'Username', 'required|min_length[5]|max_length[12]');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		if (@$_REQUEST['action']) {
			if ($this->save($id, $u)){
				$this->session->set_flashdata('message', "User '{$u->username}' saved");
				redirect("admin/users/listall");
			} else {
				$this->template->write_view("content", "role_edit", array("data"=> array ("controller"=>"admin/users/create",
				 "role"=>"new", "errors" => $this->_errors)));
				$this->template->render();
			}
		} else {
			$u = new User();
			$u->id = "new";
			$this->template->write_view("content", "user_edit", array("data"=>array('user'=>$u,
				'controller'=>'admin/users/edit')));
			$this->template->render();
		}
	}

	function save($id = FALSE, &$u = FALSE){
		$error = false;
		$filter = array();
		if(!$id) @$id = $_REQUEST['user_id'];
		@$filter['email'] = $_REQUEST['email'];//FIXEM: xss clean
		@$filter['username'] = $_REQUEST['username'];//FIXME: xss_clean
		@$filter['password'] = $_REQUEST['password'];//FIXME - xss_clean
		@$filter['groups'] = $_REQUEST['groups'];//xss_cleam me
		
		if ($this->form_validation->run() == FALSE) {
			return FALSE;
		} else {
			if ($id){
				if ($id == "new"){
					$u = new User();
				} else {
					$u = Doctrine::getTable('User')
					->findOneById($id);
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
					try{
						$u->save();
						return TRUE;
					} catch  (Exception $e) {
						$this->firephp->error("doctrine item fail". $e);
						$this->_error('Failed to save record');
						if ($id == "new") //if save failed for new item - keep form id as 'new'
							$u->id = "new";
						return FALSE;
					}
				}else{
					$this->_error("Record not saved because specified ID not found in database. Might have been deleted recently");
					return FALSE;
				}
				//try & save the form
			} else {
				$this->_error("Record not saved because ID not set");
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
					if ($u->id == "new"){
						$u = Doctrine::getTable('Users')
						->findOneByUsername($filter['username']);
					}
					$this->firephp->warn("save succeeded!");
					$this->session->set_flashdata("message", "User '{$u->username}' saved");
					redirect('admin/users/listall');
				}else{//save failed
					$this->firephp->warn("Save failed");
					$this->_error("Error saving record");
				}
			} else { //display edit form
				//load data from database using id
				$u = Doctrine::getTable('User')
				->findOneById($id);
			}
			if (!$u){
				$this->session->set_flashdata("error", "Cannot edit record: user with id '$id' not found");
				redirect("admin/users/listall");
				return;
			}
//			$this->template->write_view("content", "user_edit", array(
//			 	"data"=>array("controller"=>"admin/users/edit/".$u->id, "user"=>$u, "messages"=>$this->_infos, "errors"=>$this->_errors)));			
		} else {
			$this->session->set_flashdata("error", "Cannot edit record: identifier not set");
			redirect("admin/users/listall");
			return;
		}
		$this->template->write_view("content", "user_edit",array(
		 "data"=>array("controller"=>"admin/users/edit/".$u->id, "user"=>$u, "messages"=>$this->_infos, "errors"=>$this->_errors)));
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
					$this->session->set_flashdata("message", "User '{$name}' deleted");
				} else {
					$this->session->set_flashdata("error", "Could not delete record: user with id $id not found");
				}
			}catch (Exception $e){
				$this->session->set_flashdata("error", "Database error while attempting to delete user '{$name}'");
			}
		} else {
			$this->session->set_flashdata("error", "Could not delete record:  user identifier not specified");
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