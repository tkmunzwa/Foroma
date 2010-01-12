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
		$this->load->library('fo_module');
		if ($this->_debug) $this->load->library('FirePHP');
//		$this->form_validation->set_rules('fragment', 'Fragment', 'required|trim');
		$this->form_validation->set_rules('fragment', 'Fragment', 'trim');
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
	
	function save($fragment = FALSE){
		$filter = array();
		@$filter['icon'] = $_REQUEST['icon'];
		@$filter['description'] = $_REQUEST['description'];
		@$filter['text'] = $_REQUEST['text'];
		@$filter['parent'] = $_REQUEST['parent'];
		@$filter['onmenu'] = $_REQUEST['onmenu'];
		@$filter['name'] = $_REQUEST['name'];
		@$filter['hovertext'] = $_REQUEST['hovertext'];
		@$filter['menuposition'] = $_REQUEST['menuposition'];
				
		if ($this->form_validation->run() == FALSE) {
			$this->firephp->warn("validation failed1");
			//return FALSE;
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
					return FALSE;
				}
			}
		}
	}
	
	function edit($fragment=FALSE){
		if (!$fragment){
			@$fragment = $_REQUEST['fragment'];
		}
		if (!$fragment){
			$this->_error("No fragment supplied");
			$module = new Module();
			//return;
		} else {
			$fragment = str_replace("-", "/", $fragment);
			if ( isset ($_REQUEST['action'])) {
				$this->save($fragment);
			}	
			$q = Doctrine_query::create()
				->from('Module m')
				->where('fragment =?', $fragment);
			//$module=Doctrine::getTable('Module')->findByFragment('$fragment');
			$module = $q->fetchOne();
			if (!$module || count($module) == 0){
				$module = new Module();
				echo "not quite found";
				$module->fragment = $fragment;
			}
		}

		$db = Doctrine::getTable('Module')->findAll();
		$d = array('module'=>$module,'db'=>$db,"controller"=>"admin/usermodules/edit",
			"errors"=>$this->_errors, "messages"=>$this->_infos);
		$this->template->write_view('content', 'module_edit', array("data"=>$d));
		$this->template->render();
	}
	

	function listall() {
		$avail_UserModules = $this->fo_module->getModules();
		$db = Doctrine::getTable('Module')->findAll();
		$data = array('available'=>$avail_UserModules,'db'=>$db,
			"errors"=>$this->_errors, "messages"=>$this->_infos, "controller"=>"/admin/usermodules/process");
		$this->template->set_loader($this->load);
		$this->template->write_view('content', 'module_list', array("data"=>$data));
		$this->template->write('errors', $this->_errors);
		$this->template->write('messages', $this->_infos);
		$this->template->render();
		//$this->load->view('mlist', array("data"=>$data));
	}
	
	/**
	 * Process checked values for add[] or remove[], iterating through each and calling the relevant function
	 * 
	 * @return 
	 */
	function process(){
		@$added = $_REQUEST['add'];
		@$removed = $_REQUEST['remove'];
		
		if (sizeof($added) > 0){
			foreach($added as $item){
				$msg = "";
				$item = str_replace("-", "/", $item);
				if (!$this->_add($item, $msg)){
					if ($this->_debug) $this->firephp->error($msg);
				}
			}
		}
		if (sizeof($removed) > 0){
			foreach($removed as $item){
				$msg = "";
				$item = str_replace("-", "/", $item);
				if (!$this->_delete($item, $msg)){
					if ($this->_debug) $this->firephp->error($msg);
				}
			}
		}
		$this->listall();
	}

	function add($fragment = FALSE){
		$msg = "";
		if ($fragment){
			$_REQUEST['fragment'] = $fragment;
			$fragment = str_replace("-", "/", $fragment);
			if ($this->_add($fragment, $msg)){
				header("Location: ".site_url("/admin/usermodules/listall"));
			} else {
				$this->_errors($msg);
				$this->firephp->error($msg);
				//display error?
			}
		}
	}
	
	function _add($fragment, &$msg = FALSE){
		if ($fragment){
			try{
				$this->save($fragment);
				$this->firephp->warn("$fragment");
				return true;
			} catch (Exception $e){
				if ($msg !== FALSE) $msg = $e->getMessage();
				return false;
			}
		}
		return true;
	}
	function delete($fragment = FALSE){
		$msg = "";
		if ($fragment){
			$fragment = str_replace("-", "/", $fragment);
			if (!$this->_delete($fragment, $msg)){
				$this->session->set_flashdata('error', $msg);//TODO: display error screen
			}
		} else {
			$msg = 'Fragment not specified';
			$this->session->set_flashdata('error', $msg);//TODO: display error screen			
		}
		header("Location: ".site_url("/admin/usermodules/listall"));	

	}
	
	
	/**
	 * method to actually do the removal from db
	 * @return true when item deleted successfully, false otherwise
	 * @param object $fragment The fragment to be deleted from the database
	 * @param object $msg [optional] object where error message is placed if error occurs
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
				} else  {
					//if ($msg == -1)
					 $msg = "Record not deleteed becasue it was not found";
					return false;//not found
				}
			}
		}catch(Exception $e){
			//if ($msg == -1)
			 $msg = $e->getMessage();
			return false;
		}
		return true;	
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
