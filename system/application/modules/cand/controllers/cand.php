<?php


class Cand extends Controller
{
	function Cand(){
		parent::Controller();
		 $this->load->scaffolding('candidate');
		 $this->template->set_loader($this->load);
	}
	
	function newCandidate(){
		$c = new Candidate();
		$cand = new Contact();
		$cand->firstname = "Tapiwa";
		$cand->surname = "Munzwa";
		$add = new Address();
		$add->street = "10725 Candidate";
		$cand->Addresses[] = $add;
		$c->Contact = $cand;
		$c->save();
		echo "Candidate Saved! with id " . $c->id;
	}
	
	function newContact(){
		$cand = new Contact();
		
		$cand->firstname = "Tapiwa";
		$cand->surname = "Munzwa";
		$add = new Address();
		$add->street = "10 Contact way";
		$cand->Addresses[] = $add;
		$cand->save();
		echo "Contact saved! with id ". $cand->id;
	}
	function showCandidate($id = false){
		$candTable = Doctrine::getTable('Candidate');
		//if (!$id)
		$info = $candTable->findOne();
		echo count($info) . " records found...";
		for($i = 0; $i < count($info); $i++) {
				echo $info[$i]->firstname."<br/>";
				echo $info[$i]->Addresses[0]->street."<br/>";
		}
	}
	
	function index(){
		$data['todo_list'] = array('Clean House', 'Call Mom', 'Run Errands');

		$data['title'] = "My Real Title";
		$data['heading'] = "My Real Heading";
		
		$this->load->view('blogview', $data);
	}
	
	function listall(){
		$q = Doctrine_Query::create()
			->from('Candidate c')
			->leftJoin('c.Contact con')
			->leftJoin('con.Addresses a');		
		$info = $q->execute();
		if ($info) {
			$data = array("title"=>"Candidate  Details", 
			"viewname"=>"candidate", "data"=>	$info);
			$this->template->write_view("content","candidate", array("data"=> $info));
		} else {
			$this->_info("No items in database");
			$this->template->write_view("content", "candidate", array("data"=>$info,
			 "errors"=>$_errors, "messages"=>$_messages));
		}
		$this->template->render();
	}
	function show($id=false){
		if ($id){
			$q = Doctrine_Query::create()
				->from('Candidate c')
				->leftJoin('c.Contact con')
				->leftJoin('con.Addresses a')
				->leftJoin('c.Ethnicity e')
				->leftJoin('c.MaritalStatus m')
				->where('c.id = ?', $id);
		} else {
			$q = Doctrine_Query::create()
				->from('Candidate c')
				->leftJoin('c.Contact con')
				->leftJoin('con.Addresses a');		
		}
			$info = $q->execute();
			if ($info) {
				
				$data = array("title"=>"Candidate {$id} Details", 
				"viewname"=>"candidate", "data"=>	$info);
				$this->load->view("container", $data);
			} else {
				$data = array("errortext"=>"candidate with ID {$id} not found".count($info), 
					"errorcode"=>"0x000ff"
				);
				$this->load->view("error", $data);
			}
	}
	function edit($id=false){
		if ($id){
			if ($id == "new") {
				$info = new Candidate();
				$info->id = 0;
				$info->Contact = new Contact();	
			} else {
			$q = Doctrine_Query::create()
				->from('Candidate c')
				->leftJoin('c.Contact con')
				->leftJoin('con.Addresses a')
				->leftJoin('c.Ethnicity e')
				->leftJoin('c.MaritalStatus m')
				->where('c.id = ?', $id);
			$info = $q->execute();
			}
			if ($info) {
				$data = array("title"=>"Candidate {$id} Details", 
				"viewname"=>"candidate_edit", "data"=>	array("data"=>$info));
				$this->load->view("container", $data);
			} else {
				$data = array("errortext"=>"candidate with ID {$id} not found".count($info), 
					"errorcode"=>"0x000ff"
				);
				$this->load->view("error", $data);
			}
		} else {
				$data = array("errortext"=>"Candidate ID not specified".count($info), 
					"errorcode"=>"0x000ff"
				);
				$this->load->view("error", $data);		
		}
		//if (!$id)


	}
	
	function test(){
		//$info = new Contact();
		$q = Doctrine_Query::create()
			->from('Candidate c')
			->leftJoin('c.Contact con');;
		$info = $q->execute();
		//$info->firstname = "test";
		//print_r($info);
		//$info->Contact = new Contact();
		$data = array("viewname"=>"test", "data"=>array("data"=>$info));
		$this->load->view("candidate", array("data"=>$info));
	}
}

?>