<?php
class Templatedemo extends Controller {
   
	function Templatedemo(){
		parent::Controller();
//		$this->load->library('Template');

	}
   
	function index(){
		$this->template->write('content', "stuff comes here");
		$this->template->render();
		//echo "hey";
	}
   
}

?>