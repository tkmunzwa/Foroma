<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
	<title><?php if(isset($title)) echo $title;?></title>
	<link rel="stylesheet" href="<?php echo base_url()?>css/base.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo base_url()?>css/menu.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo base_url()?>css/widgets.css" type="text/css" />
	<script src="<?php echo base_url()?>js/jquery.js" type="text/javascript"></script> 
	<script src="<?php echo base_url()?>js/hoverIntent.jquery.js" type="text/javascript"></script> 
	<script src="<?php echo base_url()?>js/menu.js" type="text/javascript"></script> 
	<script src="<?php echo base_url()?>js/bridge.init.js" type="text/javascript"></script>
	<script src="<?php echo base_url()?>js/widgets.init.js" type="text/javascript"></script>
	<script type="text/javascript">
		<?php if (isset($extra_script)) echo $extra_script; ?>
	</script>		
	</head>
	<body>
		<div class="page">
		<div id="topnav">
		<?php $this->load->view('topmenu_view');?>
		<div id="greeting" style="float: right">
		<?php if ($user = $this->fo_user->getUser()):?>
		<span class="greeting"><span class="greetingname"><?php echo  sprintf(lang('greeting'), ($user->firstname != "" ? $user->firstname:$user->username));?></span></span>
		<?php endif; ?>
		<?php if ($this->fo_user->isLoggedIn()):?>
		<a href="<?php echo site_url("/login/logout"); ?>">logout</a>
		<?php else :?>
		<a href="<?php echo site_url("/login"); ?>">log in</a>
		<?php endif; ?>
		</div>
		</div>
		<?php 
		//print_r($data);
		
		if (isset($viewname) && isset($data) && $viewname!= ""):
			
	 		$this->load->view($viewname, array("data"=>$data));
		elseif(!isset($viewname) || $viewname == ""): ?>
				<p>Slight problem - No view was set</p>
				<? //print_r($data);
		elseif(!isset($data)): ?>
		<p> slight problem - no data set</p>
		<?php
		else:?>
		<p>View and data not set</p>
		<?
		 endif ?>
		 </div>
	</body>
</html>
