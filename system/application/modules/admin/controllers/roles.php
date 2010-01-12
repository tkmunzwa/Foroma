<?php
//namespace  controllers;

class Roles extends Controller {
	var $_errors = FALSE;
	var $_infos = FALSE;

	function Roles() {
		parent::Controller();
		$this->load->helper( array ('form', 'url'));
		$this->load->library('form_validation');
		$this->load->library('FirePHP');
		$this->template->set_loader($this->load);
		$this->template->write('title', 'Roles');
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
		$this->form_validation->set_rules('name', 'Role name', 'required|trim|min_length[5]|max_length[12]');
		if (@$_REQUEST['action']) {
			if ($this->save($id, $p)){
				$this->session->set_flashdata('message', "Role '{$p->name}' saved");
				redirect("admin/roles/listall");
			} else {
				$this->template->write_view("content", "role_edit", array("data"=> array ("controller"=>"admin/roles/create",
				 "role"=>"new", "errors" => $this->_errors)));
				$this->template->render();
			}
		} else {
			$p = new Permission();
			$p->id = "new";
			$this->template->write_view("content", "role_edit", array("data"=> array ("controller"=>"admin/roles/edit", "role"=>$p)));
			$this->template->render();
		}
	}

	function save($id = FALSE, & $p = FALSE) {
		$error = false;
		$filter = array ();
		if (!$id) {
			$id = $_REQUEST['roletype filter text_id'];
		}
		$filter['name'] = $_REQUEST['name'];//FIXME: xss_clean
		$filter['description'] = $_REQUEST['description'];//FIXME: xss_clean
		@$filter['modules'] = $_REQUEST['modules'];
		//($filter['modules']);
		///$p = FALSE;
		if ($this->form_validation->run() == FALSE) {
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
						$this->_error("Failed to save item");
						return FALSE;
					}
				} else {
					$this->firephp->error("failed to save role");
					$this->_error("Failed to save item");
					return FALSE;
				}
				//try & save the form
			} else {
				$this->firephp->error("failed to save because id was not specified");
				$this->_error("Failed to save because id was not specified");
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 *
	 * @return
	 * @param object $id[optional] id of the role to edit
	 * @param object $method[optional] Transport to be used to respond, may be html(default), json or xml. currently html is supported
	 */
	function edit($id = FALSE, $method = "html") {
		$error = FALSE;
		$this->form_validation->set_rules('name', 'Role name', 'required|min_length[5]|max_length[12]');
		$this->form_validation->set_rules('description', 'Description', 'trim');
		$filter = array ();
		@$filter['name'] = $_REQUEST['name'];
		$p = new Permission();
		if (!$id)
		@$id = $_REQUEST['role_id'];
		$p->id = $id;
		if ($id) {
			if ( isset ($_REQUEST['action'])) {
				if ($this->save($id, & $p)) {
					$this->session->set_flashdata('message', "'{$p->name}' role saved");
					//$this->firephp->warn("save succeeded!");
					redirect('admin/roles/listall'); 
					return;
				} else {//save failed
					$this->firephp->warn("Save failed");
					$this->_error("Save failed");
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
				$this->session->set_flashdata("error", "Cannot edit record: role with id $id not found");
				redirect("admin/roles/listall");
				return;
			}
			//}
		} else {
				$this->session->set_flashdata("error", "Cannot edit role:  record not specified");
				redirect("admin/roles/listall");
				return;
		}
		$this->template->write_view("content", "role_edit",  array (
			"data"=> array ("controller"=>"admin/roles/edit/".$id, "role"=>$p,
			"errors"=>$this->_errors, "messages"=>$this->_infos)));
		$this->template->render();
	}

	function listall() {
		$q = Doctrine_Query::create()
		->from('Permission p')
		->orderBy('p.name');
		$permissions = $q->execute();
		$this->template->write_view("content", "role_list",array (
			"data"=> array ("roles"=>$permissions, "errors"=>$this->_errors,
			"messages"=>$this->_infos)));
		$this->template->render();
	}

	function delete($id = FALSE) {
		$p = Doctrine::getTable('Permission')
		->findOneById($id);
		if ($p) {
			try {
				$name = $p->name;
				$p->unlink('Modules');
				$p->save();
				$p->delete();
				$this->session->set_flashdata('message', "'{$name}' role deleted");
				redirect('admin/roles/listall');
			} catch (Exception $e){
				$this->session->set_flashdata('error', "Error while trying to delete role '{$name}'");
				redirect("admin/roles/listall");
			}
		} else {
			$this->session->set_flashdata('error', "Record with id '{$id}' not found!");
			redirect("admin/roles/listall");
		}
	}
}
?>
