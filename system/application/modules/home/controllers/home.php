<?php
class Home extends Controller{
	var $_infos = FALSE;
	var $_errors = FALSE;
	function Home(){
		parent::Controller();
		$this->template->set_loader($this->load);
		if ($msg = $this->session->flashdata('message')){
			$this->_messages[] = $msg;
		}
		if ($msg = $this->session->flashdata('error')){
			$this->_errors[] = $msg;
		}
	}
	
	function index(){
		$data = array("viewname"=>"home_view", "data"=>"", "title"=>"Home");
		$this->template->write('title', 'Home');
		//load models into data here.
		$this->template->write_view('content', 'home_view', $data);
		$this->template->render();
	}
}
?>