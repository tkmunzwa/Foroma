<?php
//namespace  controllers;

class Roles extends Controller {
	var $_errors = FALSE;
	var $_infos = FALSE;
	var $permissions = false;

	function Roles() {
		parent::Controller();
		$this->load->language('admin', $this->fo_lang->userLanguage());
		$this->load->language('roles', $this->fo_lang->userLanguage());
		$this->load->helper( array ('form', 'url'));
		$this->load->library('form_validation');
		$this->load->library('FirePHP');
		$this->template->set_loader($this->load);
		$this->permissions = Doctrine::getTable('Permission')->findAll();
		$this->template->write('title', 'Groups');
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
		$this->form_validation->set_rules('name', 'Group name', 'required|trim|min_length[5]|max_length[255]');
		if (@$_REQUEST['action']) {
			if ($this->save($id, $p)){
				$this->session->set_flashdata('message', sprintf(lang('role_saved'), $p->name));
				redirect("admin/roles/listall");
			} else {
				$filter = array ();
				@$filter['permissions'] = $_REQUEST['permissions'];
				$p = new Group();
				$p->id = "new";
				$q = Doctrine_Query::create()
				->from('Permission m')
				->whereIn('m.id', $filter['permissions']);
				$m = $q->execute();
				if (sizeof($filter['permissions']) > 0) foreach ($m as $n) {
					$p->Permissions[] = $n;
				}
//				$this->firephp->warn("save failed".join("| ", $this->_errors));
				$this->template->write_view("content", "role_edit", array("data"=> array ("controller"=>"admin/roles/create",
				 "role"=>$p, "permissions"=>$this->permissions, "errors" => $this->_errors)));
				$this->template->render();
			}
		} else {
			$p = new Group();
			$p->id = "new";
			$this->template->write_view("content", "role_edit", array("data"=> array ("controller"=>"admin/roles/create", "role"=>$p,"permissions"=>$this->permissions )));
			$this->template->render();
		}
	}

	function save($id = FALSE, & $p = FALSE) {
		$error = false;
		$filter = array ();
		if (!$id) {
			$id = $_REQUEST['role_id'];
		}
		$filter['name'] = $_REQUEST['name'];//FIXME: xss_clean
		$filter['permissions'] = $_REQUEST['permissions'];
		///			 = FALSE;
		if ($this->form_validation->run() == FALSE) {
//			$this->_error("Validation failed");
			return FALSE;
		} else {
			if ($id) {
				if ($id == "new") {
					$p = new Group();
				} else {
					$p = Doctrine::getTable('Group')
					->findOneById($id);
				}
				
				if ($p) {
					$p->name = $filter['name'];
					$q = Doctrine_Query::create()
					->from('Permission m')
					->whereIn('m.id', $filter['permissions']);
					$m = $q->execute();
					$m_arr = array ();
					$p->unlink('Permissions');
					$this->firephp->warn($q->getSQL());
					
					//doctrine bug selects all record if array $filter['permissions'] is empty (above). we don't want all records, we want 0
					if (sizeof($filter['permissions']) > 0) foreach ($m as $n) {
						$p->Permissions[] = $n;
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
					$this->firephp->error("failed to save group");
					$this->_error(lang("save_error"));
					return FALSE;
				}
				//try & save the form
			} else {
				$this->firephp->error("failed to save because id was not specified");
				$this->_error("error", lang('role_cant_edit'). lang('role_id_not_set'));
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 *
	 * @return
	 * @param object $id[optional] id of the group to edit
	 * @param object $method[optional] Transport to be used to respond, may be html(default), json or xml. currently html is supported
	 */
	function edit($id = FALSE, $method = "html") {
		$error = FALSE;
			$this->firephp->warn("1");

		$this->form_validation->set_rules('name', 'Group name', 'required|min_length[5]|max_length[255]');
		$filter = array ();
		@$filter['name'] = $_REQUEST['name'];
		$p = new Group();
		if (!$id)
		@$id = $_REQUEST['role_id'];
		$p->id = $id;
		if ($id) {
			if ( isset ($_REQUEST['action'])) {
				if ($this->save($id, &$p)) {
					$this->session->set_flashdata("message", sprintf(lang('role_saved'), $p->name));
					redirect('admin/roles/listall'); 
					return;
				} else {//save failed
					@$this->firephp->warn("Save failed".join("|", $this->_errors));
					@$this->session->set_flashdata("error", join("|", $this->_errors));
					//redirect("/admin/roles/listall");
				}
			} else {
				//load data from database using id
				$q = Doctrine_Query::create()
				->from('Group p')
				->leftJoin('p.Permissions m')
				->where('p.id = ?', $id)
				->orderBy('p.name');
				$p = $q->fetchOne();
			}
			if (!$p) {
				$this->session->set_flashdata("error", lang('role_cant_edit'). sprintf(lang('role_id_not_found'), $id));
				redirect("admin/roles/listall");
				return;
			}
			//}
		} else {
				$this->session->set_flashdata("error", "Cannot edit group:  record not specified");
				$this->session->set_flashdata("error", lang('role_cant_edit').lang('role_id_not_set'));
				redirect("admin/roles/listall");
				return;
		}
		$this->template->write_view("content", "role_edit",  array (
			"data"=> array ("controller"=>"admin/roles/edit", "role"=>$p,"permissions"=>$this->permissions, 
			"errors"=>$this->_errors, "messages"=>$this->_infos)));
		$this->template->render();
	}

	function listall() {
		$q = Doctrine_Query::create()
		->from('Group p')
		->orderBy('p.name');
		$roles = $q->execute();
		$this->template->write_view("content", "role_list",array (
			"data"=> array ("roles"=>$roles, "errors"=>$this->_errors,
			"messages"=>$this->_infos)));
		$this->template->render();
	}

	function delete($id = FALSE) {
		if ($id){
			$p = Doctrine::getTable('Group')
			->findOneById($id);
			if ($p) {
				try {
					$name = $p->name;
					$p->unlink('Permissions');
					$p->save();
					$p->delete();
					$this->session->set_flashdata('message', sprintf(lang("role_deleted"),$name));
					redirect('admin/roles/listall');
				} catch (Exception $e){
					$this->session->set_flashdata('error', lang("db_error"));
					redirect("admin/roles/listall");
				}
			} else {
				$this->session->set_flashdata("error", lang('role_cant_delete'). sprintf(lang('role_id_not_found'), $id));
				redirect("admin/roles/listall");
			}
		} else{
				$this->session->set_flashdata("error", lang('role_cant_delete'). sprintf(lang('role_id_not_set'), $id));
				redirect("admin/roles/listall");		
		}
	}
}
?>
