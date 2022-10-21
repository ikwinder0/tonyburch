<?php
require('code/db.php');
require('code/login.php');

$table = "pictures"; $thumb == FALSE;
if (array_key_exists('p', $_GET)) { $picture = $_GET['p']; }
elseif (array_key_exists('t', $_GET)) { $picture = $_GET['t']; $thumb = TRUE; }
elseif (array_key_exists('v', $_GET)) { $picture = $_GET['v']; $table = "videos"; }
else { die; }

$query = mysql_query('SELECT modelname, setname, filename FROM '.$table.' WHERE image_id = '. mysql_real_escape_string($picture));
$row = mysql_fetch_assoc($query) or die("Database Error, please try again later.");
$model = $row['modelname'];
$set = $row['setname'];
$picture = $row['filename'];

if ($thumb == TRUE) { $location = $model ."/". $set ."/thumbnails/". $picture; }
else { 
	$location = $model ."/". $set ."/". $picture;
	if (SINGLE_MODEL == TRUE && $set) { $views = $set; }
	else {
		$views = $model;
		mysql_query('UPDATE views SET totalviews = totalviews + 1 WHERE views.set=1 AND modelname="'.mysql_real_escape_string($views.': '.$set).'" AND datestamp=CURDATE()');
		if (mysql_affected_rows() != 1) { mysql_query('INSERT INTO views (views.set,modelname,datestamp,totalviews) VALUES (1,"'.mysql_real_escape_string($views.': '.$set).'",CURDATE(),1)'); }
		mysql_query('INSERT IGNORE INTO viewsunique SET viewsunique.set = 1, UserIP = "'.mysql_real_escape_string(ip2long($_SERVER['REMOTE_ADDR'])).'", modelname="'.mysql_real_escape_string($views.': '.$set).'", datestamp=DATE_FORMAT(CURDATE(),"%Y-%c-01")');
	}
	mysql_query('UPDATE views SET totalviews = totalviews + 1 WHERE modelname="'.mysql_real_escape_string($views).'" AND datestamp=CURDATE()');
	if (mysql_affected_rows() != 1) { mysql_query('INSERT INTO views (modelname,datestamp,totalviews) VALUES ("'.mysql_real_escape_string($views).'",CURDATE(),1)'); }
	mysql_query('INSERT IGNORE INTO viewsunique SET UserIP = "'.mysql_real_escape_string(ip2long($_SERVER['REMOTE_ADDR'])).'", modelname="'.mysql_real_escape_string($views).'", datestamp=DATE_FORMAT(CURDATE(),"%Y-%c-01")');
}

if (!file_exists(WEBDIR . $location)) {
	if ($thumb == FALSE) { die("File not found."); }
	else { $location = "missing.jpg"; }
}
$filename = substr($location,strrpos($location,"/") + 1);
$filesize = filesize(WEBDIR . $location);

if ($thumb == FALSE) {
	$query = mysql_query('SELECT throttle,sizelimit,sizedate FROM tblSession WHERE session = "'. mysql_real_escape_string($session_id) .'"');
	$row = mysql_fetch_assoc($query) or die("Session Error, please try again later.");
	
	$updatequery = ""; $sizelimit = $row['sizelimit']; 
	if ($row['sizedate'] <= strtotime("now")) { 
		$sizelimit = 0;
		$updatequery .= " sizedate = ". (strtotime("now") + 120) .", throttle = 0,";
	}
	$sizelimit = $sizelimit + $filesize;
	$updatequery .= " sizelimit = ". $sizelimit .",";

	if ($updatequery && strpos($_SERVER['HTTP_USER_AGENT'],"Windows-Media-Player") === FALSE && strpos($_SERVER['HTTP_USER_AGENT'],"NSPlayer") === FALSE) {
		// 1MB = 1048576
		if ($sizelimit > 68157440) { //really slow leechers down 
			if ($row['throttle'] == 1) { 
				$updatequery .= " sizedate = ". (strtotime("now") + 300) .", throttle = 2,";
				mysql_query('INSERT INTO logging (datestamp,site,username,email,type,status,ip) ' .
				'VALUES (NOW(),"'.mysql_real_escape_string(SITE).'","'.mysql_real_escape_string($username).'","",255,"'.mysql_real_escape_string("Throttle Kill Active").'",'.ip2long($_SERVER['REMOTE_ADDR']).')');
			}
		}
		elseif ($sizelimit > 31457280) { //slow leechers down 
			sleep(rand(5, 10)); 
			if ($row['throttle'] == 0) { 
				$updatequery .= " sizedate = ". (strtotime("now") + 300) .", throttle = 1,";
				mysql_query('INSERT INTO logging (datestamp,site,username,email,type,status,ip) ' .
				'VALUES (NOW(),"'.mysql_real_escape_string(SITE).'","'.mysql_real_escape_string($username).'","'.mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']).'",255,"'.mysql_real_escape_string("Throttle Slowdown Active").'",'.ip2long($_SERVER['REMOTE_ADDR']).')');
			}
		}
		mysql_query("UPDATE tblSession SET". trim($updatequery,",") .' WHERE session = "'. mysql_real_escape_string($session_id) .'"');
		if ($row['throttle'] == 2) { die("You have exceeded the download limit.  Please wait " . ($row['sizedate'] - strtotime("now")) . " seconds and try again."); }
	}
}

/*if (array_key_exists('p', $_GET)) {
	$src = file_get_contents(WEBDIR . $location) or die("Image Load Failure.");
	$datestamp = filemtime(WEBDIR . $location);
	$src = imagecreatefromstring($src) or die("Image Create Failure");

	$stamp = imagecreatefrompng(WEBDIR . "watermark.png") or die ("Couldn't load watermark.");
	$w = imagesx($stamp); $h = imagesy($stamp);
	imagecopymerge($src, $stamp, 0, 0, 0, 0, $w, $h, WATERMARK) or die("Couldn't merge images.");
	imagedestroy($stamp);
	//custom watermarks
	if (strpos(strtolower($model),"maria") !== FALSE) {
		if ($stamp = imagecreatefrompng(WEBDIR . "maria-watermark.png")) {
			$w = imagesx($stamp); $h = imagesy($stamp);
			imagecopymerge($src, $stamp, imagesx($src) - $w, imagesy($src) - $h, 0, 0, $w, $h, WATERMARK) or die("Couldn't merge images.");
			imagedestroy($stamp);
		}
	}
	if (strpos(strtolower($model),"taylor") !== FALSE) {
		if ($stamp = imagecreatefrompng(WEBDIR . "taylor-watermark.png")) {
			$w = imagesx($stamp); $h = imagesy($stamp);
			imagecopymerge($src, $stamp, imagesx($src) - $w, imagesy($src) - $h, 0, 0, $w, $h, 60) or die("Couldn't merge images.");
			imagedestroy($stamp);
		}
	}
	
	
	$mime = "image/jpeg"; $disposition = "inline";
}
else {*/
	$handle = fopen(WEBDIR . $location,'r') or die;
	$datestamp = filemtime(WEBDIR . $location);
	$ext = strtolower(substr($filename,strrpos($filename,".") + 1));
	$disposition = "inline";
	switch ($ext) {
		case "jpe":
		case "jpeg":
		case "jpg":
			$mime = "image/jpeg"; $disposition = "inline"; 
			if (array_key_exists('v', $_GET)) { die("Invalid Filetype."); }
			break;
   		case "mpeg":
   		case "mpg":
   		case "mpe":
   		case "m1v": $mime = "video/mpeg"; break;
   		case "qt":
   		case "mov": $mime = "video/quicktime"; break;
   		case "avi": $mime = "video/x-msvideo"; break;
   		case "asf":
   		case "asx": $mime = "video/x-ms-asf"; break;
   		case "wm": $mime = "video/x-ms-wm"; break;
   		case "wmv": $mime = "video/x-ms-wmv"; break;
   		case "wmx": $mime = "video/x-ms-wmx"; break;
   		case "wvx": $mime = "video/x-ms-wvx"; break;
   		default: $mime = "text/plain";
	}
//}

header("Content-Type: " . $mime);
header('Content-Disposition: '. $disposition .'; filename="' . $filename . '"');
header('Pragma:');
header('Cache-Control:');
header('Expires:');
header("Content-Transfer-Encoding: binary\n");
header("Last-Modified: ".gmdate("D, d M Y H:i:s",$datestamp)." GMT");
//header("Last-Modified: ".gmdate("D, d M Y H:i:s",$row['datestamp'])." GMT");

/*if (array_key_exists('p', $_GET)) {
	ob_flush();	flush();
	if ($using_ie == 0 || $using_ie > 7) { imageinterlace($src,1); }
	imagejpeg($src,"",80);
	imagedestroy($src);
	echo gzencode($username,9);
}
else {*/
	if (isset($_SERVER['HTTP_RANGE'])) {
		$Range = trim($_SERVER['HTTP_RANGE']);
		if (preg_match( "/^bytes=([0-9]+)-$/", $Range, $matches)) {
			$seekto = $matches[1];
			header("Content-Range: bytes $seekto-" . $filesize - 1 . "/$filesize");
			header("HTTP/1.1 206 Partial content");
			fseek($handle,$seekto);
		}
	}
	header("Content-Length: " . ($filesize - $seekto));

	ob_flush();	flush();
  
	while(!feof($handle)) { $buffer = fread($handle, 4096); echo $buffer; }
	fclose ($handle);
//}
  
exit;
?>