<?php
	require('../../code/db.php');
 	require('../../code/login.php');
	require '../header.php';
	
	function display_matches($query) {
		$loop = FALSE;
 		$query = mysql_query($query);
		while ($row = mysql_fetch_assoc($query)) {
			$output = "";
			if ($loop == FALSE) {
				$output .= "<tr>";
				foreach($row as $key => $value){ $output .= "<td><b>" . ucfirst($key) . "</b></td>"; $loop = TRUE; }
				$output .= "</tr>\n";
				$loop = TRUE;
			}
			$output .= "<tr>";
			foreach($row as $key){
				if (SINGLE_MODEL == FALSE && !is_numeric($key) && !array_key_exists('model', $_GET)) { 
					$key = '<a href="?model='.urlencode($key).'&from='.urlencode($_POST['txtDateFrom']).'&to='.urlencode($_POST['txtDateTo']).'">'.$key.'</a>';
				}
				$output .= "<td>" . $key . "</td>"; 
			}
			$output .= "</tr>\n";
			echo $output;
		}	
	}
?>

<table width="800" align="center"  border="1">
<tr>
<td colspan="5">
<h3>Model Views</h3>
</td></tr>
<tr>

<td width="100%">
<form action="rptViews.php" method="post">
			<table class="contTable" width="100%">
				<tr>
					<td class='contLabel'>View Date Range:</td>
					<td><br>
						<input id='txtDateFrom' name='txtDateFrom' maxlength='96' size='10' value='<?php
							if (!array_key_exists('txtDateFrom', $_POST)) {	echo date('m/d/y',time() - 2592000);	}
							else { echo $_POST['txtDateFrom']; }
						?>' >
						&nbsp; to &nbsp; 
						<input id='txtDateTo' name='txtDateTo' maxlength='96' size='10' value='<?php
							if (!array_key_exists('txtDateTo', $_POST)) { echo date('m/d/y',time()); }
							else { echo $_POST['txtDateTo']; }
						?>' >
						<input id='txtSearch' name='txtSubmit' type='submit' value='Search'>
					</td>
				</tr>
			</table>
		</form>
</td>
</tr>
<tr>
<td width="100%"><center>
<?php
$localtime = microtime(); $localtime = explode(" ",$localtime); $localtime = $localtime[1] + $localtime[0];
$where = "";

if ((array_key_exists('model', $_GET))&&(array_key_exists('from', $_GET))) {
	if ($_GET['from'] != "") { $where .= ' AND datestamp >= STR_TO_DATE("'. mysql_real_escape_string($_GET['from']) .'","%m/%d/%y")'; }
	if ($_GET['to'] != "") { $where .= ' AND datestamp <= STR_TO_DATE("'. mysql_real_escape_string($_GET['to']) .'","%m/%d/%y")'; }
	if ($where != "") {
 		echo '<table border="1">';
		$query = 'SELECT sum(totalviews) AS totalviews, (SELECT count(UserIP) FROM viewsunique WHERE viewsunique.set = 1 AND views.modelname = viewsunique.modelname AND '.substr($where,4).') AS uniqueviews, modelname FROM views WHERE views.set = 1 AND '.substr($where,4).' AND views.modelname LIKE "'.mysql_real_escape_string($_GET['model']).':%" GROUP BY modelname ORDER BY totalviews DESC';
 		display_matches($query);
 		$query = 'SELECT sum(totalviews) AS totalviews, (SELECT count(UserIP) FROM viewsunique WHERE viewsunique.set = 1 AND viewsunique.modelname LIKE "'.mysql_real_escape_string($_GET['model']).':%" AND '.substr($where,4).') AS uniqueviews FROM views WHERE views.set = 1 AND '.substr($where,4).' AND views.modelname LIKE "'.mysql_real_escape_string($_GET['model']).':%" ORDER BY totalviews DESC';
		display_matches($query);
		echo "</table>\n";
	}
}

elseif ((array_key_exists('txtDateFrom', $_POST))&&(array_key_exists('txtDateTo', $_POST))) {
	if ($_POST['txtDateFrom'] != "") { $where .= ' AND datestamp >= STR_TO_DATE("'. mysql_real_escape_string($_POST['txtDateFrom']) .'","%m/%d/%y")'; }
	if ($_POST['txtDateTo'] != "") { $where .= ' AND datestamp <= STR_TO_DATE("'. mysql_real_escape_string($_POST['txtDateTo']) .'","%m/%d/%y")'; }
	if ($where != "") {
 		echo '<table border="1">';
 		$localtime = microtime(); $localtime = explode(" ",$localtime); $localtime = $localtime[1] + $localtime[0]; 
 		$query = 'SELECT sum(totalviews) AS totalviews, (SELECT count(UserIP) FROM viewsunique WHERE viewsunique.set = 0 AND views.modelname = viewsunique.modelname AND '.substr($where,4).') AS uniqueviews, modelname FROM views WHERE views.set = 0 AND '.substr($where,4).' GROUP BY modelname ORDER BY totalviews DESC';
 		display_matches($query);
		$query = 'SELECT sum(totalviews) AS totalviews, (SELECT count(UserIP) FROM viewsunique WHERE viewsunique.set = 0 AND '.substr($where,4).') AS uniqueviews FROM views WHERE views.set = 0 AND '.substr($where,4).' ORDER BY totalviews DESC';
		display_matches($query);
		echo "</table>\n";
	}
}

$ltime = microtime(); $ltime = explode(" ",$ltime); $ltime = $ltime[1] + $ltime[0]; $localtime = ($ltime - $localtime); 
echo '<font size="-2"><center>Total processing time is '. round($localtime,3) .' seconds</center></font>'; 
?></center><br><br>
</td></tr>
</table>
<br>
</body></html>