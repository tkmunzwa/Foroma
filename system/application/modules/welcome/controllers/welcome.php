<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();	
		$this->load->scaffolding('candidate');
		$this->template->set_loader($this->load);
	}
	
	function index()
	{
		$this->template->write_view('content', 'welcome_message');
		$this->template->render();
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */