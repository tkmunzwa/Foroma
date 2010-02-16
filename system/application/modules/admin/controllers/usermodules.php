<?php
//namespace  controllers;

class UserModules extends Controller {
	var $_errors = FALSE;
	var $_infos = FALSE;
	var $_debug = TRUE;

	function UserModules() {
		parent::Controller();
		$this->load->helper( array ('form', 'url'));
		$this->load->library('form_validation');
		$this->template->set_loader($this->load);
		$this->load->library('fo_module');
		if ($this->_debug) $this->load->library('FirePHP');
		$this->form_validation->set_rules('fragment', 'Fragment', 'required|trim');
		$this->load->language('admin', $this->fo_lang->userLanguage());
		$this->load->language('usermodules', $this->fo_lang->userLanguage());
//		$this->form_validation->set_rules('fragment', 'Fragment', 'trim');
		$this->template->set_loader($this->load);
		$this->template->write('title', 'Modules');
		if ($message = $this->session->flashdata('message'))
			$this->_info($message);	
		if ($e = $this->session->flashdata('error'))
			$this->_error($e);	
	}

	function index() {
		$this->listall();
	}
	
	/**
	 * Method to persist data to DB
	 * 
	 * @return TRUE on successful save, FALSE on failure to save. Error message(s) saved to _errors object array
	 * @param string $fragment[optional] Fragment URL to save
	 * @param boolean $skipValidation[optional] If form validation should be carried out
	 */
	function save($fragment = FALSE, $skipValidation = FALSE){
		$filter = array();
		@$filter['icon'] = $_REQUEST['icon'];
		@$filter['description'] = $_REQUEST['description'];
		@$filter['text'] = $_REQUEST['text'];
		@$filter['parent'] = $_REQUEST['parent'];
		@$filter['onmenu'] = $_REQUEST['onmenu'];
		@$filter['name'] = $_REQUEST['name'];
		@$filter['hovertext'] = $_REQUEST['hovertext'];
		@$filter['menuposition'] = $_REQUEST['menuposition'];
				
		if (!$skipValidation && $this->form_validation->run() == FALSE) {
			$this->firephp->warn("validation failed ");
			$this->firephp->warn(form_error('fragment'));
			return FALSE;
		} else {
			if ($fragment) {
				$q = Doctrine_query::create()
				->from('Module m')
				->where('fragment =?', $fragment);
				$module = $q->fetchOne();
				//$module=Doctrine::getTable('Module')->findByFragment('$fragment');
				//print_r($module);
				if (!$module || count($module) == 0){
					$module = new Module();
					$module->fragment = $fragment;
					//try & automagically determine parent
					if($filter['parent'] == ""){
						$components = split("/", $fragment);
						$parent = "";
						for($cnt=0; $cnt < sizeof($components)-1;$cnt++){//get path but skip bit after last '/'
							if ($cnt > 0){
								$parent.="/".$components[$cnt];
							}else{
								$parent = $components[$cnt];
							}
						}
					} else {
						$parent = @$filter['parent'];
					}
					if ($parent == "") $parent = "/";
					$q = Doctrine_query::create()
						->from('Module m')
						->where('fragment =?', $parent);
					$pModule = $q->fetchOne();
					if (($pModule)) $this->firephp->info("parent = $parent, id=".$pModule->id);
					if (($pModule) && count($pModule) > 0) $module->parent_id = $pModule->id;	
				}
				if ($filter['text'] != "") $module->text = $filter['text'];
				if ($filter['icon'] != "") $module->icon = $filter['icon'];
				if ($filter['description'] != "") $module->description = $filter['description'];
				if ($filter['parent'] != "") $module->parent_id = $filter['parent']; 
				if ($filter['parent'] == "<null>") $module->parent_id = NULL;
				if ($filter['menuposition'] != "") $module->menuposition = $filter['menuposition']; 
				if ($filter['name'] != "") $module->name = $filter['name']; 
				if ($filter['hovertext'] != "") $module->hovertext = $filter['hovertext'];
				if ($filter['onmenu']) $module->onmenu = true; else $module->onmenu=false;
				try {
					$module->save();
					return TRUE;
				} catch(Exception $e) {
					$this->firephp->error("doctrine item fail ".$e->getMessage());
					$this->_error(lang("db_error"));
					$this->firephp->warn('returning false');
					return FALSE;
				}
			}else{
				$this->_error(lang("fragment_not_set"));
				return FALSE;		
			}
		}
		return true;
	}
	
	/**
	 * Function to edit a user fragment. If URL fetched with GET (ie 'action' parameter not found), it displays the 
	 * edit form with data ready to edit. However, if processing form submissions (POST with 'action' HTTP var set), it 
	 * attempts to save the data. Redirects to list if succcessful, displays error message on error message on form otherwise 
	 * @return 
	 * @param string $fragment[optional] Fragment to edit (seperated by '-' and not '/' becuase slass has special meaning).
	 * - passed by system automatically
	 */
	function edit($fragment=FALSE){
		if (!$fragment){
			@$fragment = $_REQUEST['fragment'];
		}
		if (!$fragment){
			$this->session->set_flashdata('error', lang("fragment_cant_edit").lang("fragment_not_set"));
			$module = new Module();
			//return;
			redirect("admin/usermodules");
		} else {
			$fragment = str_replace("-", "/", $fragment);
			if ( isset ($_REQUEST['action'])) {
				if ($this->save($fragment)){
					$this->session->set_flashdata("message", sprintf(lang('fragment_saved'), $fragment));
					redirect("admin/usermodules/");
				}
			}	
			$q = Doctrine_query::create()
				->from('Module m')
				->where('fragment =?', $fragment);
			//$module=Doctrine::getTable('Module')->findByFragment('$fragment');
			$module = $q->fetchOne();
			if (!$module || count($module) == 0){
				$module = new Module();
				$module->fragment = $fragment;
			}
		}

		$db = Doctrine::getTable('Module')->findAll();
		$d = array('module'=>$module,'db'=>$db,"controller"=>"admin/usermodules/edit");
		$this->template->write_view('content', 'module_edit', array("data"=>$d, "errors"=>$this->_errors, "messages"=>$this->_infos));
		$this->template->render();
	}
	
	/**
	 * Controller to list all user modules
	 * @return 
	 */
	function listall() {
		$db = Doctrine::getTable('Module')->findAll();
		$avail_UserModules = $this->fo_module->getModules();
		$data = array('available'=>$avail_UserModules,'db'=>$db,
			"errors"=>$this->_errors, "messages"=>$this->_infos, "controller"=>"/admin/usermodules/process");
//		$this->template->set_loader($this->load);
		$this->template->write_view('content', 'module_list', array("data"=>$data));
/*		$this->template->write('errors', $this->_errors);
		$this->template->write('messages', $this->_infos);*/			
		$this->template->render();
		//$this->load->view('mlist', array("data"=>$data));
	}
	
	/**
	 * Controller to process checked values for add[] or remove[], iterating through each and 
	 * calling the relevant function. Redirects to listall when done with success/failure message in session flash data
	 * 
	 * @return 
	 */
	function process(){
		@$added = $_REQUEST['add'];
		@$removed = $_REQUEST['remove'];
		$realAdd = 0;
		$realRemove = 0;
		$addFail = 0;
		$removeFail = 0;
		if (sizeof($added) > 0){
			foreach($added as $item){
				$msg = "";
				$item = str_replace("-", "/", $item);
				if (!$this->_add($item, $msg, true)){
					if ($this->_debug) $this->firephp->error($msg);
					$addFail++;
				} else {
					$realAdd++;
				}
			}
		}
		if (sizeof($removed) > 0){
			foreach($removed as $item){
				$msg = "";
				$item = str_replace("-", "/", $item);
				if (!$this->_delete($item, $msg)){
					if ($this->_debug) $this->firephp->error($msg);
					$removeFail++;
				}else{
					$realRemove++;
				}
			}
		}
		if ($realAdd > 0 || $realRemove > 0||$removeFail>0 ||$addFail>0){
			$desc = array();
			if ($realAdd)
				$desc[] = sprintf(lang("fragment_added"), $realAdd);
			if ($addFail)
				$desc[] = sprintf(lang("fragment_add_fail"), $addFail);
			
			if ($realRemove> 0)
				$desc[] = sprintf(lang("fragment_removed"), $realRemove);
			if ($removeFail>0)
				$desc[] = sprintf(lang("fragment_remove_fail"), $removeFail);				
			$this->session->set_flashdata('message', join(lang("fragment_seperator"), $desc));
		} else {
			$this->session->set_flashdata('error', lang("fragment_no_action"));			
		}
		redirect("admin/usermodules/");
	}

	/**
	 * Controller to add a fragment to the database. Always redirects to listall controller with result error/message
	 * set in session flash data
	 * @return 
	 * @param string $fragment[optional] URL fragment to be added to the database
	 */
	function add($fragment = FALSE){
		$msg = "";
		if ($fragment){
			$_REQUEST['fragment'] = $fragment;
			$fragment = str_replace("-", "/", $fragment);
			if ($this->_add($fragment, $msg, true)){
				$this->session->set_flashdata('message', sprintf(lang("fragment_saved"), $fragment));
			} else {
				$this->_error($msg);
				$this->firephp->error($msg);
				//display error?
				$this->session->set_flashdata('error', $msg);
				redirect("admin/usermodules");			
			}
		} else {
			$this->session->set_flashdata('error',lang("fragment_cant_save").lang("fragment_not_set"));			
		}
		redirect("admin/usermodules");
	}
	
	/**
	 * Private method to add URL fragment to database
	 * 
	 * @return TRUE on successful adding to database, FALSE on failure. If returning false, error message(s) set in
	 * _errors private array variable
	 * @param string $fragment URL fragment
	 * @param string $msg[optional] Error message (if any) is set into this variable (passed by reference)
	 * @param boolean $skipValidation[optional] whether or not form validation should be performed
	 */
	private function _add($fragment, &$msg = FALSE, $skipValidation = false){
		if ($fragment){
			try{
				if ($this->save($fragment, $skipValidation)){
					return true;				
				}else{
					$msg=sprintf(lang("fragment_add_failed"), $fragment);
					return false;
				}
			} catch (Exception $e){
				$msg = lang("db_error");
				return false;
			}
		} else {
			$msg = lang('fragment_cant_save').lang("fragment_not_set");
			return false;
		}
		$msg = "adfadsf";
		return false;
	}
	
	/**
	 * Controller to handle removal of URL fragments from database. Always redirects to listall, sets success/error message to 
	 * session flash data
	 * @return 
	 * @param string $fragment[optional] URL fragment to be deleted (using '-' character to seperate parts instead of '/')
	 */
	function delete($fragment = FALSE){
		$msg = "";
		if ($fragment){
			$fragment = str_replace("-", "/", $fragment);
			if (!$this->_delete($fragment, $msg)){
				$this->session->set_flashdata('error', $msg);
				redirect("admin/usermodules/");
			}else{
				$this->session->set_flashdata('message', sprintf(lang("fragment_deleted"), $fragment));				
			}
		} else {
			$msg = lang("fragment_cant_delete").lang("fragment_not_set");
			$this->session->set_flashdata('error', $msg);
		}
		redirect("admin/usermodules/");	

	}
	
	
	/**
	 * {rivate method to actually do the removal from db
	 * @return TRUE when item deleted successfully, FALSE otherwise
	 * @param string $fragment The URL fragment to be deleted from the database
	 * @param string $msg [optional] (passed by reference) where error message is placed if error occurs
	 */
	function _delete($fragment, &$msg = -1){
		try{
			if ($fragment){
				$q = Doctrine_query::create()
					->from('Module m')
					->where('fragment =?', $fragment);
				$module = $q->fetchOne();
				if ($module){
					$module->delete();
					return true;
				} else  {
					//if ($msg == -1)
					 $msg = lang('fragment_cant_delete').sprintf(lang("fragment_not_found"), $fragment);
					return false;//not found
				}
			} else{
				 $msg = lang('fragment_cant_delete').lang("fragment_not_set");
				 return FALSE;
			}
		}catch(Exception $e){
			//if ($msg == -1)
			$msg = lang("db_error");
			 //$msg = $e->getMessage();
			return false;
		}
		return false;	
	}
	
	/**
	 * method to add an error message to array holding errors 
	 * @return 
	 * @param object $message Error text to be added 
	 */
	function _error($message) {
		if (!$this->_errors)
		$this->_errors = array ();
		$this->_errors[] = $message;
	}
	
	/**
	 * method to add an info message to array holding errors 
	 * @return 
	 * @param object $message info text to be added 
	 */
	function _info($message) {
		if (!$this->_infos)
		$this->_infos = array ();
		$this->_infos[] = $message;
	}
}
?>
