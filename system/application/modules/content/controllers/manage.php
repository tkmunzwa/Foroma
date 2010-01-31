<?php
class Manage extends Controller{
	var $_infos = FALSE;
	var $_errors = FALSE;
	function Manage(){
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
		$this->dashboard();
	}

	function dashboard(){
		$data = array("viewname"=>"articles_view", "data"=>"", "title"=>"Articles");
		$this->template->write('title', 'Home');
		//load models into data here.
		$this->template->write_view('content', 'articles_view', $data);
		$this->template->render();
	}
	
	function publish(){
		
	}
	
	function edit(){
		
	}
	
	function delete(){
		//FIXME
	}
	
	function unpublish(){
		//FIXME
	}
	
	function listall(){
		//FIXME:
	}
	
	function listown(){
		//FIXME: 
	}
}
?>