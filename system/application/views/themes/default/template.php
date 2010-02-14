<!doctype html>
<html>
   <head>
	<title><?php if(isset($title)) echo $title;?></title>
	<link rel="stylesheet" href="<?php echo base_url()?>css/base.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo base_url()?>css/menu.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo base_url()?>css/widgets.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo base_url()?>res/sexybuttons/sexybuttons.css" type="text/css" />
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
   	<?php
	if (isset($messages) &&$messages !="" && count($messages)>0){ ?>
	<ul class="error"><?php 
	if (is_array($errors)){
		foreach($errors as $error){
			echo "<li>$error</li>";
		}
	} else echo "<li>$errors</li>";?></div>
	<?php } ?>
      <div class="page">
         <div id="header">
            <?php echo $header ?>
				<div id="greeting">
					<?php if ($user = $this->fo_user->getUser()):?>
					<span class="greeting"><?php echo sprintf(lang("greeting"),  sprintf('<span class="greetingname">%s</span>', ($user->firstname != "" ? $user->firstname:$user->username)));?></span>
					<?php endif; ?>
					<?php if ($this->fo_user->isLoggedIn()):?>
					<a href="<?php echo site_url("/login/logout"); ?>"><?php echo lang("logout");?></a>
					<?php else :?>
					<a href="<?php echo site_url("/login"); ?>"><?php echo lang("login");?></a>
					<?php endif; ?>
				</div>
         </div>
		<div id="topnav">
		<?php $this->load->view('topmenu_view');?>
		</div>

            <div id="content"<?php if ($sidebar) echo " class=\"hassidebar\"";?>>
            	<?php if ($page_title): ?>
               <h2><?php echo $page_title ?></h2>
			   <?php endif ?>
               <div class="post">
                  <?php echo $content ?>
               </div>
			   <?php if ($sidebar){?>
        	    <div id="sidebar"> sidebar
	               <?php echo $sidebar ?>
    	        </div>
				<?php } ?>
            </div>

         <div id="footer">
            <?php echo $footer ?>
         </div>
      </div>
   </body>
</html>