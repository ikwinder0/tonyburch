<?php
/*
 * Created on Dec 8, 2005
 *
 */
 
require('../../code/db.php');
require('../../code/login.php');

header('Content-Disposition: attachment; filename="members export '. date("m-d-y") .'.csv"');
	 
$query = mysql_query('SELECT * FROM tblMembers WHERE UserLevel < 3 ORDER BY UserID ASC');
$loop = FALSE;
while ($row = mysql_fetch_assoc($query)) {
	$output = "";
	if ($loop == FALSE) {
		foreach($row as $key => $value){ if (strpos($key,",") !== FALSE) { $key = '"'. $key .'"'; } $output .= $key .','; $loop = TRUE; }
		$output = substr($output,0,strlen($output) - 1) . "\n";
	}
	foreach($row as $key){ if (strpos($key,",") !== FALSE) { $key = '"'. $key .'"'; } $output .= $key .','; $loop = TRUE; }
	$output = substr($output,0,strlen($output) - 1) . "\n";
	echo $output;
}
 
?>
