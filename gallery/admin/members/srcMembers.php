<?php
	require('../../code/db.php');
 	require('../../code/login.php');
	require '../header.php';
	require 'memberMgmt.php';
?>

<table width="800" align="center"  border="1">

<tr><td width="100%">
<form action="srcMembers.php" method="post">
			<table class="contTable" width="100%" cellpadding="5">
				<tr>
					<td>Email Address:</td>
					<td><input id='txtEmailAddress' name='txtEmailAddress' maxlength='96' size='50' value='' /></td>
				</tr>
				<tr>
					<td>User Name:</td>
					<td><input id='txtUserName' name='txtUserName' maxlength='96' size='50' value='' /></td>
				</tr>
				<tr>
					<td>User Level:</td>
					<td>
						<input type="checkbox" id="Level1" name="Level1">Only display privileged users.</input>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input id='memID' name='memID' type='hidden' value='0' /><input id='aReq' name='aReq' type='hidden' value='src' />
					<hr>Uploaders are displayed in <font color="#DAD78A"><b>yellow</b></font>. Admins are displayed in <font color="#D7797B"><b>red</b></font>.
					&nbsp;
					<input id='txtSearch' name='txtSubmit' type='submit' value='Search' />
					<hr></td>
				</tr>
			</table>
		</form>
</td>
</tr>
<tr>
<td width="100">
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