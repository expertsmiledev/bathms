<!DOCTYPE html>
<html>
<head>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0" />
<title></title>
<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700' rel='stylesheet' type='text/css'>
<?php 
if(isset($css_files)){
	foreach($css_files as $css){
		echo "<link href='$css' rel='stylesheet' type='text/css' />\n";
	}
}
?>
<link href="/css/bat.css" rel="stylesheet" type="text/css" />
<!--[if lt IE 9]>
<script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/
html5.js"></script>
<![endif]-->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<?php 
if(isset($js_files)){
	foreach($js_files as $js){
		echo "<script type='text/javascript' src='$js'></script>\n";
	}
}
?>
</head>
<body>
<div class="wrapper">
	<header>
		<a href='/'><h1><img src="/images/hms.png" alt="BAT Engineering - Hose Management Service"/></h1></a>
		<a href='/login/log_out' class="logoutLink">Log Out</a>
	</header>
	<nav> <?php echo $nav; ?> </nav>
	<section class='content clear'> <?php echo $content; ?> </section>
	<footer></footer>
</div>
</body>
</html>
