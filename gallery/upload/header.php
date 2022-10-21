<?php
/*
 * Created on Dec 14, 2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
	if ($userlevel < 1) { die("Access Denied");	}
?>
<html>
	<head>
		<title>
		<?php echo TITLE; ?> :: Upload
		</title>
	</head>

	<body bgcolor="#000000" text="#000000" link="#000000" vlink="#000000">

	<table width="1024" align="center" bgcolor="#ffffff">
	<tr>
	<td>
		<table width="1020" bgcolor="<?php echo NORMAL_BG; ?>">
		<tr>
		<td align="center">
	<img src="<?php echo HEADER_IMAGE; ?>" alt="<?php echo TITLE; ?>">

	<br>
<table cellpadding="20" align="center" border="0">
<tr><td valign="top">
<h3>Galleries</h3>
<li><a href="/gallery/upload/uploader/">Manage Galleries</a><br></li>
<li><a href="/gallery/">View Galleries</a></li>
</td><td valign="top">
<h3>Other</h3>
<li><a href="/member/login.php?logout">Sign Out</a></li>
</td></tr>
</table>
