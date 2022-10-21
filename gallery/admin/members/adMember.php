<?php
	require('../../code/db.php');
 	require('../../code/login.php');
	require '../header.php';
	require 'memberMgmt.php';
?>
<table width="800" align="center"  border="1">
<tr>
<td colspan="5">
<h3>Add Member</h3>
</td></tr>

<tr><td width="100%">
<form action="adMember.php" method="post">
			<table class="contTable" width="100%">
				<tr>
					<td>First Name:</td>
					<td><input id='txtFirstName' name='txtFirstName' maxlength='96' size='50' value='' /></td>
				</tr>
				<tr>
					<td>Last Name:</td>
					<td><input id='txtLastName' name='txtLastName' maxlength='96' size='50' value='' /></td>
				</tr>
				<tr>
					<td>Email Address:</td>
					<td><input id='txtEmailAddress' name='txtEmailAddress' maxlength='96' size='50' value='' /></td>
				</tr>
				<tr><td colspan='2'>&nbsp;</td></tr>
				<tr>
					<td>User Name:</td>
					<td><input id='txtUserName' name='txtUserName' maxlength='96' size='50' value='' /></td>
				</tr>
				<tr>
					<td>Password:</td>
					<td><input id='txtPassword' name='txtPassword' maxlength='96' size='50' value='' /></td>
				</tr>
				<tr><td colspan='2'>&nbsp;</td></tr>
				<tr>
					<td>Membership Expires:</td>
					<td><input id='txtExpires' name='txtExpires' maxlength='96' size='50' value='<?php echo  date("m/d/Y",mktime(0, 0, 0, date("m")+1,  date("d"),  date("Y")))	?>' /></td>
				</tr>
				<tr>
					<td colspan='2'><input id='memID' name='memID' type='hidden' value='0' /><input id='aReq' name='aReq' type='hidden' value='ad' />&nbsp;</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input id='txtSubmit' name='txtSubmit' type='submit' value='Save' />
					</td>
				</tr>
			</table>
		</form>
</td>
</tr>
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