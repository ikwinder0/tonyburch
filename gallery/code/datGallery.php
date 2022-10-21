<?php

function MembersBaseGallery() {
		global $db, $totaltime; 
		
   		$model = ""; $modelset = "";
   	    if (array_key_exists('m', $_GET)) {	
   	    	$model = $_GET['m']; $title = $model;
   	    	if (array_key_exists('s', $_GET)) {	$modelset = $_GET['s']; $title = $modelset; }
   	    }
   	    else { $title = "Galleries"; }
   	    if (array_key_exists('ofs', $_GET)) { $lastOffSet = $_GET['ofs']; }
		else { $lastOffSet = 0; }
   	    
    	echo "<table width=\"100%\" align=\"center\" border=\"0\">\n";
    	echo "<tr><td colspan=\"4\" align=\"center\">";
    	
   		$title = str_replace("_", " ", $title);
   		//while (strlen($title) > 3 && (substr($title,0,1) == "0" || intval(substr($title,0,1)) > 0 || substr($title,0,1) == "-" || substr($title,0,1) == " ")) { $title = substr($title,1); }
    	echo '<font size="6"><b>' . ucfirst($title) . "</b></font><br>\n";

		if ($modelset != "") {
			$query = mysql_query("SELECT image_id, caption, thumbwidth, thumbheight FROM pictures WHERE modelname = \"". mysql_real_escape_string($model) ."\" AND setname = \"". mysql_real_escape_string($modelset) ."\" ORDER BY datestamp DESC, filename DESC LIMIT ".$lastOffSet.",24");
			if ($lastOffSet == 0) { $filequery = mysql_query("SELECT image_id, caption, filename FROM videos WHERE modelname = \"". mysql_real_escape_string($model) ."\" AND setname = \"". mysql_real_escape_string($modelset) ."\" ORDER BY filename ASC"); }
			$countquery = mysql_query("SELECT count(image_id) AS count FROM pictures WHERE modelname = \"". mysql_real_escape_string($model) ."\" AND setname = \"". mysql_real_escape_string($modelset) ."\"");
		}
		elseif ($model != "") {
			$query  = mysql_query("SELECT image_id, sets.modelname, DATE_FORMAT(sets.datestamp,'%b %d %y') AS datestamp, sets.setname, pictures, videos, thumbwidth, thumbheight FROM sets, pictures WHERE image_id = thumb_id AND sets.modelname = \"". mysql_real_escape_string($model) ."\" ORDER BY sets.datestamp DESC, sets.setname DESC LIMIT ".$lastOffSet.",24");
			$countquery = mysql_query("SELECT count(modelname) AS count FROM sets WHERE modelname = \"". mysql_real_escape_string($model) ."\"");
		}
		elseif (SINGLE_MODEL == TRUE) {
			$query = mysql_query("SELECT image_id, sets.modelname, DATE_FORMAT(sets.datestamp,'%b %d %y') AS datestamp, sets.setname, pictures, videos, thumbwidth, thumbheight FROM sets, pictures WHERE image_id = thumb_id ORDER BY sets.datestamp DESC, sets.setname DESC LIMIT ".$lastOffSet.",24");
			$countquery = mysql_query("SELECT count(modelname) AS count FROM sets");
		}
		else {
			$query = mysql_query("SELECT status, image_id, models.modelname, DATE_FORMAT(models.datestamp,'%b %d %y') AS datestamp, videos, pictures, thumbwidth, thumbheight FROM models, pictures WHERE image_id = thumb_id AND (videos > 0 OR pictures > 0) ORDER BY models.datestamp DESC, models.modelname ASC LIMIT ".$lastOffSet.",24");
			$countquery = mysql_query("SELECT count(modelname) AS count FROM models");
		}
		if ($modelset) { echo '<font size="-1"><a href="slideshow.php?m='.urlencode($model).'&s='.urlencode($modelset).'">View set as slideshow</a></font>'; }
		echo "</td></tr><tr>";
		if (isset($filequery)) { display_files($filequery); }
		display_thumbnails($query,FALSE);
		    	
		echo '<td colspan="5" align="center"';
		if (USING_CSS == 1) { print ' class="pagination"'; }
		echo '>Page: ';
    	if ($row = mysql_fetch_assoc($countquery)) {
    		$referer = "";
    		if ($model) { $referer .= "&m=" . urlencode($model); }
    		if ($modelset) { $referer .= "&s=" . urlencode($modelset); }
    		do_pagination($lastOffSet,$row['count'],$referer);
    	}
		echo "</td></tr></table>\n";
		
		$mtime = microtime(); 
		$mtime = explode(" ",$mtime); 
		$mtime = $mtime[1] + $mtime[0]; 
		$totaltime = ($mtime - $totaltime); 
		
		echo "</td></tr></table>\n";
		echo "<center><font size=\"-2\">Page created in " . round($totaltime,3) . ' seconds<br><a href="login.php?admin">Admin</a></font></center>';
}

function SearchGallery() {
		global $db, $totaltime; 

		if (isset($_GET['search'])) { $search = $_GET['search']; }
		elseif (isset($_POST['search'])) { $search = $_POST['search']; }
		else { $search = ""; }
		
		if (isset($_GET['fromdate'])) { $fromdate = $_GET['fromdate']; }
		elseif (isset($_POST['fromdate'])) { $fromdate = $_POST['fromdate']; }
		else { $fromdate = ""; }
		if (isset($_GET['todate'])) { $todate = $_GET['todate']; }
		elseif (isset($_POST['todate'])) { $todate = $_POST['todate']; }
		else { $todate = ""; }
		
		$nomatch = TRUE;
   	    if ($search || $fromdate || $todate) {
   	    	$where = ""; $referer = "";
   	    	if ($search != "") { 
   	    		$search = str_replace("*", "%", $search);
   	    		$where .= " AND sets.setname LIKE \"%". mysql_real_escape_string($search) ."%\"";
	    		$referer .= "&search=". urlencode($search);
   	    	}
   	    	if ($fromdate != "") { 
   	    		$where .= " AND sets.datestamp >= STR_TO_DATE('". mysql_real_escape_string($fromdate) ."','%m/%d/%Y')";
   	    		$referer .= "&fromdate=". urlencode($fromdate);	
   	    	}
   	    	if ($todate != "") { 
   	    		$where .= " AND sets.datestamp <= STR_TO_DATE('". mysql_real_escape_string($todate) ."','%m/%d/%Y')";
   	    		$referer .= "&todate=". urlencode($todate);
   	    	}
   	    	$where = substr($where,5);
	   	    
   	    	if (array_key_exists('ofs', $_GET)) { $lastOffSet = $_GET['ofs']; }
			else { $lastOffSet = 0; }
   	    
    		echo "<table width=\"100%\" align=\"center\" border=\"0\">\n";
    		echo "<tr><td colspan=\"4\" align=\"center\">";
    	
    		echo "<h1>Search Results</h1>\n";
			echo "</td></tr><tr>";

 			$query = mysql_query("SELECT image_id, sets.modelname, DATE_FORMAT(sets.datestamp,'%b %d %y') AS datestamp, sets.setname, pictures, videos, thumbwidth, thumbheight FROM sets, pictures WHERE image_id = thumb_id AND $where ORDER BY sets.datestamp DESC, setname DESC LIMIT ".$lastOffSet.",24");
			$countquery = mysql_query("SELECT count(modelname) AS count FROM sets WHERE $where");
			$row = mysql_fetch_assoc($countquery);
			if ($row['count'] == 0) {
				echo "<tr><td colspan=\"4\" align=\"center\">"; 
				echo "<b>No Results</b><p><p>";
			}
			else {
				$nomatch = FALSE;
				display_thumbnails($query,TRUE);
		    	
				echo "<td colspan=\"5\" align=\"center\">Page: ";
    			if ($row['count'] > 0) { do_pagination($lastOffSet,$row['count'],$referer); }
			}
			echo "</td></tr></table>\n";
   	    }
   	    if ($nomatch == TRUE) {
?>
<form action="search.php" method="post">
<table border="0" cellpadding="5" bgcolor="<?php echo DARK_BG; ?>"><tr>
<td colspan="2" align="center"><h1>Search Gallery</h1></td></tr><tr>
<td>Search for Text:<br><font size="-1">This search engine does not support boolean searches.  Use * as a wildcard for partial matches.</font></td>
<td valign="top"><input type="text" style="width: 300px" class="post" name="search" size="30"></td></tr>
<tr><td>Search by Upload Date:<br><font size="-1">Date format is mm/dd/yyyy.</font></td>
<td><input type="text" style="width: 80px" class="post" name="fromdate" name="search" size="30"> &nbsp; to &nbsp; 
<input type="text" style="width: 80px" class="post" name="todate" name="search" size="30">
</td></tr>
<tr><td colspan="2" align="center"><input type="submit" value="Search"></td></tr>
</table>
</form>
<?php
   	    }
		
		$mtime = microtime(); 
		$mtime = explode(" ",$mtime); 
		$mtime = $mtime[1] + $mtime[0]; 
		$totaltime = ($mtime - $totaltime); 
		
		echo "</td></tr></table>\n";
		echo "<center><font size=\"-2\" color=\"#808080\">Page created in " . round($totaltime,3) . " seconds</font></center>";
}

function display_thumbnails($query,$showmodel){
   	$title = "";
	$itemcount = 0;

   	while ($row = mysql_fetch_assoc($query)) {
		if (isset($row['setname'])) { $title = $row['setname']; } else { $title = $row['modelname']; }
		$title = str_replace("_", " ", $title);
		//while (strlen($title) > 3 && (substr($title,0,1) == "0" || intval(substr($title,0,1)) > 0 || substr($title,0,1) == "-" || substr($title,0,1) == " ")) { $title = substr($title,1); }
  		
		echo "<td width='25%' align='center'>\n";
		if (isset($row['modelname'])) { 
			echo "<b><font size=\"4\">$title</font></b><br>\n";
			echo "<a href=\"?m=". urlencode($row['modelname']);
			if (isset($row['setname'])) { echo "&s=" . urlencode($row['setname']); }
		}
		else {
			echo "<a target=\"_new\" href=\"throwpic.php?p=". urlencode($row['image_id']);
		}
        echo "\">";
		
		$thumbpath = "throwpic.php?t=" . urlencode($row['image_id']);
		echo "<img src=\"". $thumbpath ."\"";
		if (isset($row['thumbwidth'])) { if ($row['thumbwidth'] > 0) { echo ' width="'.$row['thumbwidth'].'"'; } }
		if (isset($row['thumbheight'])) { if ($row['thumbheight'] > 0) { echo ' height="'.$row['thumbheight'].'"'; } }
		echo " border=\"0\"></a><br>\n";
		
		if ($showmodel == TRUE) { echo "<font size=\"-2\">". $row['modelname'] ."</font><br>"; }
   		if (isset($row['pictures'])) { 
			echo "<font size=\"-2\">". $row['pictures'] ." Pics";
			if ($row['videos'] > 0) { echo " - ". $row['videos'] ." Vids"; }
			echo "</font><br>";
   		}
   		if (isset($row['caption'])) { echo "<font size=\"-2\">". $row['caption'] ."</font><br>"; }
   		if (isset($row['datestamp'])) { if ($row['datestamp'] != "") { echo "<font size=\"-2\">Last Modified: " . $row['datestamp'] . "</font><br>"; } }
   		if (isset($row['status'])) { if ($row['status'] != "") { echo "<font size=\"-2\">Current Status: " . $row['status'] . "</font><br>"; } }
   		echo "</td>";
  		$itemcount++;

		if ($itemcount == 4) { echo "</tr><tr><td colspan=\"4\">&nbsp;</td></tr><tr>"; $itemcount = 0; }
	}

	if ($itemcount <> 0) { echo "</tr><tr>"; }
}

function display_files($query){
	$itemcount = 0;
	
   	while ($row = mysql_fetch_assoc($query)) {
?><td width='25%' align='center' valign='center'>
<p>Video:<br><a href="throwpic.php?v=<?php echo urlencode($row['image_id']) .'">'. $row['filename'] . "</a><br>";
   		if (isset($row['caption'])) { echo "<font size=\"-2\">". $row['caption'] ."</font><br>"; }
   		echo "&nbsp;<p></td>";
  		$itemcount++;

		if ($itemcount == 4) { echo "</tr><tr><td>&nbsp;</td></tr><tr>"; $itemcount = 0; }
	}

	if ($itemcount <> 0) { echo "</tr><tr>"; }
}

function do_pagination($offset,$totalpics,$referer, $pagination = 24) {
   $x = 0; $page = 1;
   while ($x < $totalpics) {
      if ($offset == $x)  { print "<b>" . $page . "</b>"; }
      else {
         print "<a href=\"?";
         print "ofs=". $x;
         if ($referer != "") { print $referer; }
         print '"';
         if (USING_CSS == 1) { print ' class="pagination"'; }
         print ">". $page ."</a>";
      }
      if ($x == 0 && fmod($offset, $pagination) > 0) { $x += fmod($offset, $pagination); }
      else { $x += $pagination; }
      if ($x < $totalpics) { print ", "; }
      $page++;
   }
}

?>

