<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
	<title>FreePBX: Call Detail Reports</title>
	<meta http-equiv="Content-Type" content="text/html">
	<link href="common/mainstyle.css" rel="stylesheet" type="text/css">
	<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="favicon.ico">
	<!--[if IE]>
	<link href="common/ie.css" rel="stylesheet" type="text/css">
	<![endif]-->	
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >

	<script type="text/javascript" src="common/script.js.php"></script>
	<script type="text/javascript" src="common/libfreepbx.javascripts.js" language="javascript"></script>
	
<!--[if IE]>
    <style type="text/css">div.inyourface a{position:absolute;}</style>
<![endif]-->
</head>

<body onload="body_loaded();"   >

<!-- module process box, used by module admin (page.modules.php) - has to be here because of IE6's z-order stupidity -->
<div id="moduleBox" style="display:none;"></div>

<div id="page">
	<div id="header">
		<div id="freepbx"><a href="http://www.freepbx.org" target="_blank" title="FreePBX"><img src="images/freepbx_large.png" alt="FreePBX" /></a></div>
		<div id="version"><a href="http://www.freepbx.org" target="_blank">FreePBX</a> 2.5.0.1 on <a href="http://mythtv.van.murrell.ca:8081">mythtv.van.murrell.ca</a></div>
		<ul id="metanav">
		<li class="first"><a href="config.php">Admin</a></li>
		<li class="current"><a href="reports.php">Reports</a></li>
		<li class="noselect"><a href="panel.php">Panel</a></li>
		<li class="noselect"><a target="ari" href="../recordings/index.php">Recordings</a></li>
		<li class="noselect"><a target="help" href="http://www.freepbx.org/freepbx-help-system?freepbx_version=2.5.0.1">Help</a></li>
<li class="last"><a >&nbsp</a></li>		</ul>
		<div id="logo"><a href="http://www.freepbx.org" target="_blank" title="FreePBX"><img src="images/logo.png" alt="FreePBX" /></a></div>
	<div id="login_message">	</div>	</div>

<div id="content">

<div id="reportnav" ><ul><li><nobr><a id="current" href="reports.php?display=1">Call Logs</a><nobr></li><li><nobr><a id="" href="reports.php?display=2">Compare Calls</a><nobr></li><li><nobr><a id="" href="reports.php?display=3">Monthly Traffic</a><nobr></li><li><nobr><a id="" href="reports.php?display=4">Daily load</a><nobr></li></ul></div><div id="reportframe"><iframe width="97%" height="2000" frameborder="0" align="top" scrolling="auto" src="config.php?handler=cdr&s=1&posted=1"></iframe></div></div> <!-- content -->
</div> <!-- page -->
</body>
</html>
</div>
