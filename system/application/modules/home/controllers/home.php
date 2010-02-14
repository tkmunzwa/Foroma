<?php
class Home extends Controller{
	var $_infos = FALSE;
	var $_errors = FALSE;
	
	function Home(){
		parent::Controller();
		$this->template->set_loader($this->load);
		if ($message = $this->session->flashdata('message'))
			$this->_info($message);	
		if ($e = $this->session->flashdata('error'))
			$this->_error($e);	
	}
	
	function index(){
		$data = array("viewname"=>"home_view", "data"=>"", "title"=>"Home");
		$this->template->write('title', 'Home');
		//load models into data here.
		$this->template->write_view('content', 'home_view', $data);
		$this->template->render();
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