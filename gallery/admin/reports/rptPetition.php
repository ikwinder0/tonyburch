<?php

	require('../../code/db.php');
 	require('../../code/login.php');
	require '../header.php';
?>

<table class="contTable" border="1" width="100%">
<tr><td colspan="4">
<h3>Petition Data</h3>
</td></tr><tr>
<?php
$query = "SHOW COLUMNS FROM petition"; $line = mysql_query($query);
while ($column = mysql_fetch_array($line)) { echo "<td>". $column['Field'] . "</td>"; }
echo "\n</tr>";
$query = "SELECT * FROM petition ORDER BY id ASC";
$query = mysql_query($query);
while ($line = mysql_fetch_row($query)) {
	echo "<tr>";
	foreach ($line as $key => $value) {
		if ($key == 4) { echo "<td>".long2ip($value)."</td>"; }
		else { echo "<td>$value</td>"; }
	}
	echo "</tr>\n";
}
echo "<br><br>";
?>
<br>
</body></html>