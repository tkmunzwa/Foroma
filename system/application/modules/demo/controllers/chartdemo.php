<?php

class Chartdemo extends Controller {

	function Chartdemo()
	{
		parent::Controller();
		$this->template->set_loader($this->load);
		$this->template->write('title', 'Chart demo');
		$this->load->library('Charting');
	}
	
	function index(){
		$this->template->write('content',  "this is a pretty-but-totally-random graph: <img src='".site_url("/demo/chartdemo/image/1")."' />");
		$this->template->render();
	}
	
	function image($id=false)
	{
		//$this->load->view('welcome_message');
		$ds = $this->charting->pData();
		$s1 =array();
		$s2 = array();
		for ($cnt = 1; $cnt < 20; $cnt++){
			$s1[] = rand(0, 7);
			$s2[] = rand(0, 7);
		}
/*		$ds->AddPoint(array(1,4,3,2,3,3,2,1,0,7,4,3,2,3,3,5,1,0,7),"Serie1");
		$ds->AddPoint(array(1,4,2,6,2,3,0,1,5,1,2,4,5,2,1,0,6,4,2),"Serie2");*/
		$ds->AddPoint($s1,"Serie1");
		$ds->AddPoint($s2,"Serie2");
		$ds->AddAllSeries();
		$ds->SetAbsciseLabelSerie();
		$ds->SetSerieName("January","Serie1");
		$ds->SetSerieName("February","Serie2");
		$ch = $this->charting->pChart(600, 400);
		 // Initialise the graph
		$ch = new pChart(700,230);
		$ch->setFontProperties($this->charting->getFontDir()."/tahoma.ttf",8);
		$ch->setGraphArea(50,30,585,200);
		$ch->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240);
		$ch->drawRoundedRectangle(5,5,695,225,5,230,230,230);
		$ch->drawGraphArea(255,255,255,TRUE);
		$ch->drawScale($ds->GetData(),$ds->GetDataDescription(),5,150,150,150,TRUE,0,2);
		$ch->drawGrid(4,TRUE,230,230,230,50);
		
		 // Draw the 0 line
		$ch->setFontProperties($this->charting->getFontDir()."/tahoma.ttf",6);
		$ch->drawTreshold(0,143,55,72,TRUE,TRUE);
		
		 // Draw the cubic curve graph
		$ch->drawCubicCurve($ds->GetData(),$ds->GetDataDescription());
		
		 // Finish the graph
		$ch->setFontProperties($this->charting->getFontDir()."/tahoma.ttf",8);
		$ch->drawLegend(600,30,$ds->GetDataDescription(),255,255,255);
		$ch->setFontProperties($this->charting->getFontDir()."/tahoma.ttf",10);
		$ch->drawTitle(50,22,"Random Data Plot (".date('d/m/Y H:i:s').")",50,50,50,585);
		header('Content-type: image/png');
		$ch->Render($this->charting->getTempDir()."/example2.png");
		echo file_get_contents($this->charting->getTempDir()."/example2.png");
	}
}

/* End of file chartdemo.php */
/* Location: ./system/application/controllers/welcome.php */