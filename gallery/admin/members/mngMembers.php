<?php
	require('../../code/db.php');
 	require('../../code/login.php');
	require '../header.php';
	require 'memberMgmt.php';
?>
<table width="800" align="center"  border="1">
<tr>
<td colspan="5">
<h3>Edit Member</h3>
</td></tr>
<tr>
<td width="100%">
<?php
			if (array_key_exists('memID', $_GET)) {
				if (array_key_exists('aReq', $_GET)) {
					$member = new MemberFunction($_GET['memID'],$_GET['aReq']);		
					$member->processMember();
				}
			}
			elseif (array_key_exists('memID', $_POST)) {
				if (array_key_exists('aReq', $_POST)) {
					$member = new MemberFunction($_POST['memID'],$_POST['aReq']);		
					$member->processMember();
				}
			}
		?><br><br>
</td></tr>
</table>
<br>
</body></html>