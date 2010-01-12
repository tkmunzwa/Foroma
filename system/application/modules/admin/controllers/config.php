<?php
class Config extends Controller
{
	function Config(){
		parent::Controller();
		 $this->load->scaffolding('candidate');
		$this->template->set_loader($this->load);
		$this->template->write('title', 'Configuration');
	}
	
	function info(){
		phpinfo();
	}
	
	function index(){
		$this->template->write('content', '<iframe frameborder="0" src="'.site_url('/admin/config/info').'" style="width:100%;height:100%"></iframe>');
		$this->template->render();
	}
}
?>