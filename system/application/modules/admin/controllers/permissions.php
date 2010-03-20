<?php
//namespace  controllers;

class Permissions extends Controller {
	var $_errors = FALSE;
	var $_infos = FALSE;

	function Permissions() {
		parent::Controller();
		$this->load->language('admin', $this->fo_lang->userLanguage());
		$this->load->language('permissions', $this->fo_lang->userLanguage());
		$this->load->helper( array ('form', 'url'));
		$this->load->library('form_validation');
		$this->load->library('FirePHP');
		$this->template->set_loader($this->load);
		$this->template->write('title', 'Permissions');
		if ($message = $this->session->flashdata('message'))
			$this->_info($message);	
		if ($e = $this->session->flashdata('error'))
			$this->_error($e);	
//s		$this->session->set_flashdata('message', 'remember me');
		//$this->_info("hell yeah!");
	}

	function index() {
		$this->listall();
	}

	function create() {
		$id = FALSE;
		$p = FALSE;
		$this->form_validation->set_rules('name', 'Permission name', 'required|trim|min_length[5]|max_length[36]');
		if (@$_REQUEST['action']) {
			if ($this->save($id, $p)){
				$this->session->set_flashdata('message', sprintf(lang('permission_saved'), $p->name));
				redirect("admin/permissions/listall");
			} else {
				$filter = array ();
				@$filter['modules'] = $_REQUEST['modules'];
				$p = new Permission();
				$p->id = "new";
				$q = Doctrine_Query::create()
				->from('Module m')
				->whereIn('m.id', $filter['modules']);
				$m = $q->execute();
				if (sizeof($filter['modules']) > 0) foreach ($m as $n) {
					$p->Modules[] = $n;
				}
//				$this->firephp->warn("save failed".join("| ", $this->_errors));
				$this->template->write_view("content", "permission_edit", array("data"=> array ("controller"=>"admin/permissions/create",
				 "permission"=>$p, "errors" => $this->_errors)));
				$this->template->render();
			}
		} else {
			$p = new Permission();
			$p->id = "new";
			$this->template->write_view("content", "permission_edit", array("data"=> array ("controller"=>"admin/permissions/create", "permission"=>$p)));
			$this->template->render();
		}
	}

	function save($id = FALSE, & $p = FALSE) {
		$error = false;
		$filter = array ();
		if (!$id) {
			$id = $_REQUEST['permission_id'];
		}
		$filter['name'] = $_REQUEST['name'];//FIXME: xss_clean
		$filter['description'] = $_REQUEST['description'];//FIXME: xss_clean
		@$filter['modules'] = $_REQUEST['modules'];
		//($filter['modules']);
		///$p = FALSE;
		if ($this->form_validation->run() == FALSE) {
//			$this->_error("Validation failed");
			return FALSE;
		} else {
			if ($id) {
				if ($id == "new") {
					$p = new Permission();
				} else {
					$p = Doctrine::getTable('Permission')
					->findOneById($id);
				}
				
				if ($p) {
					$p->description = $filter['description'];
					$p->name = $filter['name'];
					$q = Doctrine_Query::create()
					->from('Module m')
					->whereIn('m.id', $filter['modules']);
					$m = $q->execute();
					$m_arr = array ();
					$p->unlink('Modules');
					$this->firephp->warn($q->getSQL());
					
					//doctrine bug selects all record if array $filter['modules'] is empty (above). we don't want all records, we want 0
					if (sizeof($filter['modules']) > 0) foreach ($m as $n) {
						$p->Modules[] = $n;
					}
					try {
						$p->save();
						return TRUE;
					} catch(Exception $e) {
						$this->firephp->error("doctrine item fail ".$e);
						$this->_error(lang("db_error"));
						return FALSE;
					}
				} else {
					$this->firephp->error("failed to save permission");
					$this->_error(lang("save_error"));
					return FALSE;
				}
				//try & save the form
			} else {
				$this->firephp->error("failed to save because id was not specified");
				$this->_error("error", lang('permission_cant_edit'). lang('permission_id_not_set'));
				return FALSE;
			}
		}
		return false;
	}

	/**
	 *
	 * @return
	 * @param object $id[optional] id of the permission to edit
	 * @param object $method[optional] Transport to be used to respond, may be html(default), json or xml. currently html is supported
	 */
	function edit($id = FALSE, $method = "html") {
		$error = FALSE;
		$this->form_validation->set_rules('name', 'Permission name', 'required|min_length[5]|max_length[12]');
		$this->form_validation->set_rules('description', 'Description', 'trim');
		$filter = array ();
		@$filter['name'] = $_REQUEST['name'];
		$p = new Permission();
		if (!$id)
		@$id = $_REQUEST['permission_id'];
		$p->id = $id;
		if ($id) {
			if ( isset ($_REQUEST['action'])) {
				if ($this->save($id, & $p)) {
					$this->session->set_flashdata("message", sprintf(lang('permission_saved'), $p->name));
					redirect('admin/permissions/listall'); 
					return;
				} else {//save failed
					@$this->firephp->warn("Save failed".join("|", $this->_errors));
					@$this->session->set_flashdata("error", join("|", $this->_errors));
					redirect("/admin/permissions/listall");
				}
			} else {
				//load data from database using id
				$q = Doctrine_Query::create()
				->from('Permission p')
				->leftJoin('p.Modules m')
				->where('p.id = ?', $id)
				->orderBy('m.fragment');
				$p = $q->fetchOne();
			}
			if (!$p) {
				$this->session->set_flashdata("error", lang('permission_cant_edit'). sprintf(lang('permission_id_not_found'), $id));
				redirect("admin/permissions/listall");
				return;
			}
			//}
		} else {
				$this->session->set_flashdata("error", "Cannot edit permission:  record not specified");
				$this->session->set_flashdata("error", lang('permission_cant_edit').lang('permission_id_not_set'));
				redirect("admin/permissions/listall");
				return;
		}
		$this->template->write_view("content", "permission_edit",  array (
			"data"=> array ("controller"=>"admin/permissions/edit/".$id, "permission"=>$p,
			"errors"=>$this->_errors, "messages"=>$this->_infos)));
		$this->template->render();
	}

	function listall() {
		$q = Doctrine_Query::create()
		->from('Permission p')
		->orderBy('p.name');
		$permissions = $q->execute();
		$this->template->write_view("content", "permission_list",array (
			"data"=> array ("permissions"=>$permissions, "errors"=>$this->_errors,
			"messages"=>$this->_infos)));
		$this->template->render();
	}

	function delete($id = FALSE) {
		if ($id){
			$p = Doctrine::getTable('Permission')
			->findOneById($id);
			if ($p) {
				try {
					$name = $p->name;
					$p->unlink('Modules');
					$p->save();
					$p->delete();
					$this->session->set_flashdata('message', sprintf(lang("permission_deleted"),$name));
					redirect('admin/permissions/listall');
				} catch (Exception $e){
					$this->session->set_flashdata('error', lang("db_error"));
					redirect("admin/permissions/listall");
				}
			} else {
				$this->session->set_flashdata("error", lang('permission_cant_delete'). sprintf(lang('permission_id_not_found'), $id));
				redirect("admin/permissions/listall");
			}
		} else{
				$this->session->set_flashdata("error", lang('permission_cant_delete'). sprintf(lang('permission_id_not_set'), $id));
				redirect("admin/permissions/listall");		
		}
	}
}
?>
