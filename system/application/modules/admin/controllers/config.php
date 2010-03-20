<?php
class Config extends Controller implements IAdmin
{
	var $emailEnc = array("None"=>"", "SSL"=>"SSL", "TLS"=>"TLS");
	var $_infos;
	var $_errors;
	
	function Config($suppressLoad = false){
		parent::Controller();
		if (!$suppressLoad){
			$this->load->language('admin', $this->fo_lang->userLanguage());
//			$this->load->language('config', $this->fo_lang->userLanguage());
			$this->load->scaffolding('candidate');
			$this->template->set_template('admin');
			$this->template->set_loader($this->load);
			$this->template->write('title', 'Configuration');
		}
	}
	
	function showinfo(){
		phpinfo();
	}
	
	function info(){
		$this->template->write('content', '<iframe frameborder="0" src="'.site_url('/admin/config/showinfo').'" style="width:100%;height:100%"></iframe>');
		$this->template->render();
	}
	
	function index(){
		$this->settings();
	}
	
	function showLinks($callClass){
		$this->load->library('FO_ClassFinder');
		$this->fo_classfinder->setMatchInterface('IAdmin');
		$matchClasses = $this->fo_classfinder->getMatches();
		$configItems = array();
		foreach ($matchClasses as $className => $path){
			$obj = new $className(true);
			$a = $obj->_getAdminMethods();
			if ($a) foreach($a as $k=>$v){
				if ($v) $v->fragment = preg_replace('/\//', ':', $v->fragment);
				if ($v) $v->text = preg_replace('/\//', ':', $v->text);
				//maybe check user permissions here later?
				$configItems[] = $v;
			}				
		}
		$this->template->write_view("sidebar", "config_sidebar", array("data"=> array ("modules"=>$configItems, 'class'=>$callClass)));
	}
	/**
	 * show config settings
	 * @return 
	 */
	function settings($section = false){
		$callObj = $callMethod = false;
		if ($section){
			list($callObjName, $callMethod) = split (":", $section, 2);
			$callObj = new $callObjName(true);
			$callObj->$callMethod($this->template)
	;	}
		$this->showLinks($callObj);
		$this->template->render();

	}
	
	function _getAdminMethods(){
		$email = new Module();
		$email->fragment = "admin/config/smtpemail";
		$email->description = "Email settings";
		$email->text = "config/_smtpemail";
		$site = new Module();
		$site->fragment = "admin/config/site";
		$site->description = "Site settings";
		$site->text = "config/_site";
		return array($email, $site);
	}
	
	function _smtpemail($t = false){
		$smtp = Doctrine::getTable('SMTPSetting')
			->findOneByName("system");
//		$smtp = $q->execute();
		if (!$smtp || count($smtp) == 0){
			$smtp = new SMTPSetting();
		} else {
			$smtp->password = "";
		}


		$t->write("title", ":: Email settings");
		if ( isset ($_REQUEST['action'])) {
			if ($this->_saveSMTP()){
				$this->session->set_flashdata("message", lang('email_saved'));
				redirect("admin/config/");
			}
		} else{
			
		}
		$t->write_view("content", "config_smtp_edit", array("encryptionschemes"=>$this->emailEnc, "smtp"=>$smtp,
		"data"=>array("controller"=>"admin/config/settings/config:_smtpemail", "errors"=>$this->_errors)));
	}

	function _site($t = false){
		$timezones=array("Harare"=>"+2", "Gaborone"=>"+2");
		$t->write_view("content", "config_site_edit", array("timezones"=>$timezones, 
			"data"=>array("controller"=>"admin/config/settings/config:_site")));
		$t->write("title", ":: Site settings");
		if ( isset ($_REQUEST['action'])) {
			if ($this->_saveSMTP()){
				$this->session->set_flashdata("message", sprintf(lang('fragment_saved'), $fragment));
				redirect("admin/config/");
			}
		} else{
			
		}
	}
	
	function _saveSMTP(){
		$filter = array();
		@$filter['server'] = $_REQUEST['server'];
		@$filter['port'] = $_REQUEST['port'];
		@$filter['username'] = $_REQUEST['username'];
		@$filter['password'] = $_REQUEST['password'];
		@$filter['encryption'] = $_REQUEST['encryption'];
		@$filter['name'] = $_REQUEST['name'];
		
		$smtp = Doctrine::getTable('SMTPSetting')
			->findOneByName("system");
		//var_dump($smtp);
		if (!$smtp || count($smtp) == 0){
			$smtp = new SMTPSetting();
			$smtp->name = "system";
		}
		$smtp->server = $filter['server'];
		if ($filter['username']) $smtp->username = $filter['username'];
		if ($filter['password']) $smtp->password = $filter['password'];
		$smtp->port = $filter['port'];
		$smtp->encryption = $filter['encryption'];
		try{
			$smtp->save();
		}catch(Exception $e){
			$this->_error(lang("db_error")." ".$e);
			return false;
		}
		return true;
	}
}
?>