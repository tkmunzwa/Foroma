<html>
	<head>
	<title>Error</title>
	<script type="text/javascript">
		<?php if (isset($extra_script)) echo $extra_script; ?>
	</script>		
	</head>
	<body>
		<h1>Unexpected error!</h1>
		An unexpected error has occured! The detailed error message is:<br/>
		<?php echo $errortext; ?><br/>
		Error code: <?php echo $errorcode?>
	</body>
</html>