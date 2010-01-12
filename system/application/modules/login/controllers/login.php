<?php


class Login extends Controller
{
	var $_errors = FALSE;
	
	function Login(){
		parent::Controller();
		$this->load->scaffolding("login");
		$this->load->language('login', $this->fo_lang->userLanguage());
		$this->load->language('login', $this->fo_lang->adminLanguage());
		$this->load->language('app', $this->fo_lang->adminLanguage());
		$this->load->library('FirePHP');
		$this->template->set_loader($this->load);
		$this->template->set_template('bare');
	}

	function index($redirect = ""){
		$this->template->write_view("content", "login_view", array("redirect"=>$redirect));
		$this->template->render();
	}

	function log_in(){
		if (isset($_POST['username']) && isset($_POST['password']) && 
		  $_POST['username'] != "" && $_POST['username']){
		  	$log = new LoginActivity();
			$log->time = date("Y-m-d H:i:s");
			$log->ipaddress = $_SERVER['REMOTE_ADDR'];
			//var_dump($_SERVER);
			$q = Doctrine_Query::create()->
			from('User u')->
			where('u.username = ?', $_POST['username']);
			$user = $q->fetchOne();
			if (count($user) == 0 || !$user) {
				$this->_error( $this->lang->line('login_wrong_user'));
				$log->event = "Invalid user";
			} else {
				$log->user_id = $user->id;
				//set IP address/host name

				if($user->authenticate($_POST['password'])){
					$this->session->set_userdata(array('user_id'=> $user->id, 'username'=>$user->username,
						'language'=>$user->language));
					$log->event = $this->lang->line('admin_login_success');;
					if (isset($_POST['redirect']) && $_POST['redirect']!= "") {
						header("Location: ".$_POST['redirect']);
					} else {
						redirect('/home');
						return;
					}
				} else {
					$this->_error( $this->lang->line('login_wrong_pass'));;
					$log->event = $this->lang->line('login_wrong_pass');;
				}
			}
		} else {
			$this->_error( $this->lang->line('login_missing_fields'));
			$log->event = $this->lang->line('login_missing_fields');
		}
		try{
			@$log->save();
		}catch (Exception $e){
			$this->firephp->warn("failed to save log even $e");
			$this->_errors('db_error');
		}
//		if (count($errors) > 0){
			$this->template->write_view("content", "login_view", array("errors"=>$this->_errors));
			$this->template->render();
//		}

	}

	function redirect($url = ""){
		$this->index($url);
	}

	function logout(){
		if ($this->session->userdata("user_id")!= "") {
			$la = new LoginActivity();
			$la->user_id = $this->session->userdata("user_id");
			$la->time = date("Y-m-d H:i:s");
			$la->event = "Log out";
			$la->save();
			$messages = array("messages"=> $this->lang->line('login_logged_out'));
			$this->session->sess_destroy();
			$data = array("messages"=>$messages);
		} else {
			$errors = array($this->lang->line('login_notloggedin'));
			$data = array("errors"=>$this->_errors);
		}
		redirect('/home');
		//	$this->load->view("container",array("viewname"=>'home_view', "data"=>$data));
	}
	
	function _error($msg){
		if (!$this->_errors)
			$this->errors = array();
		$this->_errors[] =  $msg;
	}
}

?>