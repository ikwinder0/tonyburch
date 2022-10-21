<?php

	class Members {
		function Members() {
		}
		
		function getMembers() {
			?>
			<h1>Members</h1>
			<a class='addNewMember' href='adMember.php'>Add New Member</a><br><br><br>
			<table border="0" cellpadding="2" cellspacing="0">
				<thead>
					<tr class="memHdr">
						<?php
							$query = "SHOW COLUMNS FROM tblMembers";
							$result = mysql_query($query) or die("Database Error: " . mysql_error());
							while ($column = mysql_fetch_array($result, MYSQL_ASSOC)) {
								echo "<td>" . $column['Field'] . "</td>\n";
							}
						?>
					</tr>
				</thead>
				<tbody>
					<?php
							$query = "SELECT FirstName, 
LastName, 
EmailAddress, 
UserName, 
Password, 
RecurringBilling, 
DATE_FORMAT(JoinDate,'%c/%e/%Y %H:%i'), 
DATE_FORMAT(MemberExpires,'%c/%e/%Y') FROM tblMembers";
							$result = mysql_query($query) or die("Database Error: " . mysql_error());
							while ($line = mysql_fetch_array($result, MYSQL_NUM)) {
								echo "\t<tr>\n";
								foreach ($line as $key => $value) {
									switch ($key) {
										default:
											echo "\t\t<td>$value</td>\n";
											break;
									}
								}
								echo "\t\t<td><a href='mngMembers.php?memID=$line[0]&aReq=ed'>Edit</a></td>\n";
								echo "\t\t<td><a href='mngMembers.php?memID=$line[0]&un=$line[5]&aReq=de'>Delete</a></td>\n";
								echo "<tr>\n";
							}
							echo "\t<tr>\n";
							echo "\t\t<td colspan='10'>&nbsp;</td>\n";
							echo "<tr>\n";
							echo "\t<tr>\n";
							echo "\t\t<td colspan='10'><a class='addNewMember' href='adMember.php'>Add New Member</a></td>\n";
							echo "<tr>\n";
					?>
				</tbody>
			</table>
			<?php
		}
	}
	
	class MemberFunction {
		var $userID;
		var $fName;
		var $sName;
		var $email;
		var $uname;
		var $password;
		var $ccNum;
		var $expires;
		var $aReq;
	
		function MemberFunction($userID,$aReq) {
			$this->userID = $userID;
			$this->aReq = $aReq;
		}
		
		function processMember() {
			global $userlevel;
			switch ($this->aReq) {
				case "ad":
				    $this->fName = $_POST['txtFirstName'];
    				$this->sName = $_POST['txtLastName'];
    				$this->email = $_POST['txtEmailAddress'];
    				$this->uname = $_POST['txtUserName'];
    				$this->password = $_POST['txtPassword'];
    				$this->expires = $_POST['txtExpires'];
    				
    				if ($this->uname == "") { echo "A username is required."; }
    				else {
   						$query = "INSERT INTO tblMembers ("
	       					. "JoinDate, "
   							. "FirstName, "
   							. "LastName, "
   							. "EmailAddress, "
   							. "UserName, "
   							. "Password, "
   							. "RecurringBilling, "
   							. "MemberExpires"
   							. ") "
   							. "VALUES ("
   							. "NOW(), "
   							. "'" . mysql_real_escape_string($this->fName) . "', "
   							. "'" . mysql_real_escape_string($this->sName) . "', "
   							. "'" . mysql_real_escape_string($this->email) . "', "
   							. "'" . mysql_real_escape_string($this->uname) . "', "
   							. "'" . mysql_real_escape_string($this->password) . "', "
   							. "'Yes', "
							. 'STR_TO_DATE("'. mysql_real_escape_string($this->expires) .'","%c/%e/%Y"))';

      					$result = mysql_query($query);
   						if (!$result) { die("<b>Database Error: </b>" . mysql_error()); }
   						else { 
   							echo "<b>The new member was added to the database.</b><br>";
    					}
    				}
					break;
				case "ed":
				?>
<form action="mngMembers.php" method="post">
<table class="contTable" width="100%">
<?php
				$query = "SELECT UserID, FirstName, LastName, EmailAddress, UserName, Password, RecurringBilling, DATE_FORMAT(JoinDate,'%c/%e/%Y %H:%i'), DATE_FORMAT(MemberExpires,'%c/%e/%Y'),UserLevel " .
						 "FROM tblMembers " .
						 "WHERE UserID = " . mysql_real_escape_string($this->userID) . " AND UserLevel <= ". $userlevel;
				$result = mysql_query($query) or die("Database Error: " . mysql_error());
				while ($line = mysql_fetch_array($result, MYSQL_NUM)) {
					?>
<tr><td>Joined:</td><td><?php echo $line[7]; ?></td></tr>
<tr><td>First Name:</td><td><input id='txtFirstName' name='txtFirstName' maxlength='96' size='50' value='<?php echo $line[1]; ?>'></td></tr>
<tr><td>Last Name:</td><td><input id='txtLastName' name='txtLastName' maxlength='96' size='50' value='<?php echo $line[2]; ?>'></td></tr>
<tr><td>Email Address:</td><td><input id='txtEmailAddress' name='txtEmailAddress' maxlength='96' size='50' value='<?php echo $line[3] ?>'></td></tr>
<tr><td colspan='3'>&nbsp;</td></tr>
<tr><td>User Name:</td><td><input id='txtUserName' name='txtUserName' maxlength='96' size='50' value='<?php echo $line[4]; ?>'></td></tr>
<tr><td>Password:</td><td><input id='txtPassword' name='txtPassword' maxlength='96' size='50' value='<?php echo $line[5]; ?>'></td></tr>
<tr><td colspan='3'>&nbsp;</td></tr>
<tr><td>Member Canceled:</td><td> <?php if ($line[6] == "Yes") { echo "No"; } else { echo "Yes"; } ?></td></tr>
<tr><td>Membership Expires:</td><td><input id='txtExpires' name='txtExpires' maxlength='96' size='50' value='<?php echo $line[8]; ?>'></td></tr>
<tr><td>Access Level:</td><td><select id='txtLevel' name='txtLevel'><?php
						for ($i = 0; $i <= $userlevel; $i++) {
							echo "<option";
							if ($line[9] == $i) { echo " selected"; }
							echo " value='$i'>$i</option>";
						} ?></select></td></tr>
<tr><td colspan='3'>&nbsp;<br><hr><input id='memID' name='memID' type='hidden' value='<?php echo $line[0]; ?>'><input id='aReq' name='aReq' type='hidden' value='edd'></td></tr>
<tr><td><input id='txtSubmit' name='txtSubmit' type='submit' value='Save Changes'></td>
<td align="center"><input id='txtResend' name='txtResend' type='submit' value='Resend welcome email'></td>
<td align="right"><input id='txtDelete' name='txtDelete' type='submit' value='Delete Member'></td>
<tr><td colspan='3'><hr></td></tr>
</tr>
			<?php } ?>
</table></form>
<?php
		break;
		
		case "edd":
			$this->fName = $_POST['txtFirstName'];
			$this->sName = $_POST['txtLastName'];
			$this->email = $_POST['txtEmailAddress'];
			$this->uname = $_POST['txtUserName'];
			$this->password = $_POST['txtPassword'];
			$this->ccNum = $_POST['txtCCFirstFour'] . "-XXXX-XXXX-" . $_POST['txtCCLastFour'];
			$this->expires = $_POST['txtExpires'];
			$level = $_POST['txtLevel'];
			if (isset($_POST['txtSubmit'])) {
				if ($level > 2 && $userlevel < 3) { die("Invalid Userlevel"); } 
				$query = "UPDATE tblMembers SET "
					. "UserName = '" . mysql_real_escape_string($this->uname) . "', "
					. "EmailAddress = '" . mysql_real_escape_string($this->email) . "', "
					. "Password = '" . mysql_real_escape_string($this->password) . "', "
					. "FirstName = '" . mysql_real_escape_string($this->fName) . "', "
					. "LastName = '" . mysql_real_escape_string($this->sName) . "', "
					. 'MemberExpires = STR_TO_DATE("'. mysql_real_escape_string($this->expires) .'","%c/%e/%Y"), '
					. "UserLevel = " . mysql_real_escape_string($level) . " "
					. "WHERE (UserID = " . mysql_real_escape_string($this->userID) . ")";
					
				$result = mysql_query($query);
				if (!$result) { die("<b>Database Error:</b> " . mysql_error()); }
				else { echo "<b>Your changes were saved to the database.</b>"; }
			}
			elseif (isset($_POST['txtResend'])) {
				require("../../code/members.php");
				$single = "";
				$m = new Member($this->uname, $this->password, $this->email, $this->fName ." ". $this->sName, $single, "");
				if (!$single) { $m->CardNumber = "(Card not found)"; }
				$result = $m->send_welcome_email();
				if (!$result) { echo "PHP Mail Error."; }
				else { echo "Message Sent."; }
			}
			elseif (isset($_POST['txtDelete'])) {
				if ($this->userID == "" || $this->userID <= 0) { die("Cannot locate user."); }
				echo "<br>";
			
				$query = "DELETE FROM tblMembers "
    				. "WHERE (UserID = " . mysql_real_escape_string($this->userID) . ")";
					
	      		$result = mysql_query($query);
       			if (!$result) {	echo '<b>Database Error: ' . mysql_error() . '</b>'; }
	    		else { echo "<b>The record was deleted from the database.</b>"; }
			}
			else { echo "Unknown Command"; }
			break;
		case "src":
			$this->email = $_POST['txtEmailAddress'];
   			$this->uname = $_POST['txtUserName'];
   			$this->ccNumFF = $_POST['txtCCFirstFour'];
   			$this->ccNumLF = $_POST['txtCCLastFour'];
   			$criteriaCount = 0;
			?>
				<table border="1" cellpadding="2" cellspacing="0" width="100%">
				<thead>
					<tr>
						<td><b>ID</b></td>
						<td><b>Join Date</b></td>
						<td><b>Expiration</b></td>
						<td><b>First Name</b></td>
						<td><b>Last Name</b></td>
						<td><b>Email Address</b></td>
						<td><b>User Name</b></td>
						<td><b>Password</b></td>
						<td>&nbsp;</td>
					</tr>
				</thead>
				<tbody>
			<?php
			$query = "SELECT UserLevel, UserID, DATE_FORMAT(JoinDate,'%c/%e/%Y'), DATE_FORMAT(tblMembers.MemberExpires,'%c/%e/%Y'), FirstName, LastName, tblMembers.EmailAddress, tblMembers.UserName, Password"
				. " FROM tblMembers"
				. " WHERE UserLevel <= ". $userlevel;
			if ($this->email != '') { $query = $query . " AND (tblMembers.EmailAddress LIKE '" . mysql_real_escape_string($this->email) . "%')"; }
			if ($this->uname != '') { $query = $query . " AND (tblMembers.UserName LIKE '" . mysql_real_escape_string($this->uname) . "%')";	}
			if ($this->ccNumFF != '') { $query = $query . " AND (CardNumber LIKE '" . mysql_real_escape_string($this->ccNumFF) . "%-XXXX-XXXX-%')"; }
			if ($this->ccNumLF != '') { $query = $query . " AND (CardNumber LIKE '%-XXXX-XXXX-" . mysql_real_escape_string($this->ccNumLF) . "%')"; }
			if ($_POST['BlankCC'] == 'on') { $query = $query . " AND CardNumber IS NULL"; }
			if ($_POST['Level1'] == 'on') { $query = $query . " AND UserLevel > 0"; }
			$query = $query . " ORDER BY UserID";
			$result = mysql_query($query) or die("Database Error: " . mysql_error());
			while ($line = mysql_fetch_assoc($result)) {
				echo "\t<tr>\n";
				foreach ($line as $key => $value) {
					switch ($key) {
						case "UserLevel":
							if ($value == 1) { $cellcolor = "#DAD78A"; }
							elseif ($value > 1) { $cellcolor = "#D7797B"; }
							else $cellcolor = "";
							break;
						default:
							echo "<td";
							if ($cellcolor != "") { echo " bgcolor='".$cellcolor."' "; }
							echo ">$value</td>";
						break;
					}
				}
				echo "\t\t<td><a href='mngMembers.php?memID=".$line['UserID']."&aReq=ed'><font color='#000000'>Edit</font></a></td>\n";
				echo "</tr>\n";
			}
			?>
			</tbody>
			</table>
			<?php
			break;
		}
	}
}
?>
