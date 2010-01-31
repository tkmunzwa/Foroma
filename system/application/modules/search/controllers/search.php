<?php
class Search extends Controller {
 
	function Search()
	{
		parent::Controller();
 
		$this->load->library('zend');
		$this->zend->load('Zend/Search/Lucene'); 
	}
 
	function create()
	{
		// This method should be authenticated, or removed once the index is created.
		// TODO: Some sort of site spidering process to add the entire site to the index.
 
		$index = Zend_Search_Lucene::create(APPPATH . 'search/index');
 
    		$doc = Zend_Search_Lucene_Document_Html::loadHTMLFile('http://www.andrewrowland.com');
 
    		$index->addDocument($doc);
 
		echo '<p>Index created</p>';
	}
 
	function index()
	{
		// TODO: Create an index method that contains a search form.
	}
 
	function result()
	{
		// Hardcoded search result example.
		// TODO: Take a user search query, and expand the result summary
 
		$index = Zend_Search_Lucene::open(APPPATH . 'search/index');
 
		$data['results'] = $index->find('andrew');
 
		$this->load->view('search_result_view', $data);		
	}
}
?>