<html>
<head>
	<title>Search example</title>
</head>
<body>
 
<ul>
	<?php foreach($results as $result):?>
	<li><?php echo $result->title;?></li>
	<?php endforeach;?>
</ul>
 
</body>
</html>