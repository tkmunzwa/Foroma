<?php
//namespace  controllers;

class Newsletter extends Controller {
	var $_errors = FALSE;
	var $_infos = FALSE;
	var $permissions = false; 

	function Newsletter() {
		parent::Controller();
//		$this->load->language('admin', $this->fo_lang->userLanguage());
		$this->load->language('newsletter', $this->fo_lang->userLanguage());
		$this->load->helper( array ('form', 'url'));
		$this->load->library('form_validation');
		$this->load->library('FirePHP');
		$this->template->set_loader($this->load);
		$this->template->write('title', 'Newsletter');
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
		$this->form_validation->set_rules('name', 'NewsletterModel name', 'required|trim|min_length[5]|max_length[255]');
		if (@$_REQUEST['action']) {
			if ($this->save($id, $p)){
				$this->session->set_flashdata('message', sprintf(lang('newsletter_saved'), $p->name));
				redirect("newsletter/listall");
			} else {
				$filter = array ();
				$p = new NewsletterModel();
				$p->id = "new";
//				$this->firephp->warn("save failed".join("| ", $this->_errors));
				$this->template->write_view("content", "newsletter_edit", array("data"=> array ("controller"=>"newsletter/create",
				 "newsletter"=>$p, "errors" => $this->_errors)));
				$this->template->render();
			}
		} else {
			$p = new NewsletterModel();
			$p->id = "new";
			$this->template->write_view("content", "newsletter_edit", array("data"=> array ("controller"=>"newsletter/create", "newsletter"=>$p)));
			$this->template->render();
		}
	}

	function save($id = FALSE, & $p = FALSE) {
		$error = false;
		$filter = array ();
		if (!$id) {
			$id = $_REQUEST['newsletter_id'];
		}
		@$filter['name'] = $_REQUEST['name'];//FIXME: xss_clean
		@$filter['description'] = $_REQUEST['description'];
		///			 = FALSE;
		if ($this->form_validation->run() == FALSE) {
//			$this->_error("Validation failed");
			return FALSE;
		} else {
			if ($id) {
				if ($id == "new") {
					$p = new NewsletterModel();
				} else {
					$p = Doctrine::getTable('NewsletterModel')
					->findOneById($id);
				}
				
				if ($p) {
					$p->name = $filter['name'];
					$p->description = $filter['description'];
					//doctrine bug selects all record if array $filter['description'] is empty (above). we don't want all records, we want 0
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
				$this->_error("error", lang('newsletter_cant_edit'). lang('newsletter_id_not_set'));
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

		$this->form_validation->set_rules('name', 'NewsletterModel name', 'required|min_length[5]|max_length[255]');
		$filter = array ();
		@$filter['name'] = $_REQUEST['name'];
		$p = new NewsletterModel();
		if (!$id)
		@$id = $_REQUEST['newsletter_id'];
		$p->id = $id;
		if ($id) {
			if ( isset ($_REQUEST['action'])) {
				if ($this->save($id, &$p)) {
					$this->session->set_flashdata("message", sprintf(lang('newsletter_saved'), $p->name));
					redirect('newsletter/listall'); 
					return;
				} else {//save failed
					@$this->firephp->warn("Save failed".join("|", $this->_errors));
					@$this->session->set_flashdata("error", join("|", $this->_errors));
					//redirect("/newsletter/listall");
				}
			} else {
				//load data from database using id
				$q = Doctrine_Query::create()
				->from('NewsletterModel p')
				->where('p.id = ?', $id)
				->orderBy('p.name');
				$p = $q->fetchOne();
			}
			if (!$p) {
				$this->session->set_flashdata("error", lang('newsletter_cant_edit'). sprintf(lang('newsletter_id_not_found'), $id));
				redirect("newsletter/listall");
				return;
			}
			//}
		} else {
				$this->session->set_flashdata("error", "Cannot edit group:  record not specified");
				$this->session->set_flashdata("error", lang('newsletter_cant_edit').lang('newsletter_id_not_set'));
				redirect("newsletter/listall");
				return;
		}
		$this->template->write_view("content", "newsletter_edit",  array (
			"data"=> array ("controller"=>"newsletter/edit", "newsletter"=>$p,
			"errors"=>$this->_errors, "messages"=>$this->_infos)));
		$this->template->render();
	}

	function listall() {
		$q = Doctrine_Query::create()
		->from('NewsletterModel p')
		->orderBy('p.name');
		$newsletters = $q->execute();
		$this->template->write_view("content", "newsletter_list",array (
			"data"=> array ("newsletters"=>$newsletters, "errors"=>$this->_errors,
			"messages"=>$this->_infos)));
		$this->template->render();
	}

	function delete($id = FALSE) {
		if ($id){
			$p = Doctrine::getTable('NewsletterModel')
			->findOneById($id);
			if ($p) {
				try {
					$name = $p->name;
					$p->save();
					$p->delete();
					$this->session->set_flashdata('message', sprintf(lang("newsletter_deleted"),$name));
					redirect('newsletter/listall');
				} catch (Exception $e){
					$this->session->set_flashdata('error', lang("db_error"));
					redirect("newsletter/listall");
				}
			} else {
				$this->session->set_flashdata("error", lang('newsletter_cant_delete'). sprintf(lang('newsletter_id_not_found'), $id));
				redirect("newsletter/listall");
			}
		} else{
				$this->session->set_flashdata("error", lang('newsletter_cant_delete'). sprintf(lang('newsletter_id_not_set'), $id));
				redirect("newsletter/listall");		
		}
	}
}
?>
