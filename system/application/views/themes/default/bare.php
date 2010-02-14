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
      <div class="page">
         <div id="header">
            <?php echo $header ?>
         </div>
		<div id="topnav">
		<?php $this->load->view('topmenu_view');?>

		</div>

            <div id="content">
               <div class="post">
                  <?php echo $content ?>
               </div>

            </div>

         <div id="footer">
            <?php echo $footer ?>
         </div>
      </div>
   </body>
</html>