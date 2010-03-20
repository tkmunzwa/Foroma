<?php
class Demo extends Controller implements IAdmin {
   
	function Demo(){
		parent::Controller();
//		$this->load->library('Template');

	}
   
   	function misc(){
   		$this->load->library('FO_ClassFinder');
		$this->fo_classfinder->setMatchInterface('IAdmin');
		var_dump($this->fo_classfinder->getMatches());
   	}
	function index(){
		$this->template->write('content', "Miscelleneous demo items");
		$this->template->render();
		//echo "hey";
	}
	
	function email(){
		$this->load->library('FO_SwiftMailer');
         
		$message = Swift_Message::newInstance()
		
		  //Give the message a subject
		  ->setSubject('Your subject')
		
		  //Set the From address with an associative array
		  ->setFrom(array('tkmunzwa@gmail.com' => 'John Doe'))
		
		  //Set the To addresses with an associative array
		  ->setTo(array('tapiwa@munzwa.tk', 'tkmunzwa@gmail.com' => 'A name'))
		
		  //Give it a body
		  ->setBody('Here is the message itself')
		
		  //And optionally an alternative body
		  ->addPart('<q>Here is the message itself</q>', 'text/html')
		
		  //Optionally add any attachments
		 // ->attach(Swift_Attachment::fromPath('my-document.pdf'))
		  ;
		  
		  //Create the Transport
		$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
		  ->setUsername('chat@munzwa.tk')
		  ->setPassword('**********');
		
		
		//Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport);
				  
		//Send the message
		$sent = $result = $mailer->send($message);
		
		
		$this->template->write('content', "$sent emails sent");
		$this->template->render();

	}
	
	/**
	 * IAdmin method
	 * @return 
	 */
	function _getAdminMethods(){
		return null;
	}
   
}

?>