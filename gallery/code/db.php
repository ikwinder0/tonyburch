<?php
//globals
define("WEBDIR","/var/www/tonyburch.com/gallery/photos/");
define("HEADER_IMAGE","/gallery/header.jpg");
define("LARGE_HEADER","/gallery/header.jpg");
define("TITLE","TonyBurch.com");
define("SITE","tonyburch.com");
define("PHRASE","Gallery System");
define("COOKIE","tonyburch_Login");
define("WATERMARK",30);
define("DARK_BG","#678ddb");
define("DARKER_BG","#678ddb");
define("NORMAL_BG","#7A9BDE");
define("SINGLE_MODEL",FALSE);
$using_ie = 0;

//DB connect info
$user = 'ssisharkin';
$pass = 'Enn3M9Pqs';
$database = 'tonyburch';

//Performance counter
$mtime = microtime();    
$mtime = explode(" ",$mtime);    
$mtime = $mtime[1] + $mtime[0];    
$totaltime = $mtime;

$db = mysql_connect('localhost', $user, $pass) or die('Database Error: ' . mysql_error());
mysql_select_db($database) or die('Could not select database');
unset($pass,$user);

if (isset($_SERVER['HTTP_USER_AGENT'])) {
	$browser = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($browser,'MSIE') > 0) {
		$browser = substr($browser,strpos($browser,'MSIE') + 5);
		$browser = substr($browser,0,strpos($browser,';'));
		$using_ie = intval($browser);
		unset($browser);
	}
}

function make_thumbnail($thumbnail) {
	$path = str_replace("/thumbnails", "", $thumbnail);
	$dir = substr($thumbnail,0,strrpos($thumbnail,"/"));
	
	if (is_dir(substr($path,0,strrpos($path,"/") + 1)) == FALSE) { echo "Cannot Find Directory<br> <!--" . $thumbnail . "-->"; return FALSE; }
	if (is_dir($dir) == FALSE) { $old_umask = umask(0); mkdir($dir, 0777); umask($old_umask); }
	if (!file_exists($path) || strlen($thumbnail) < 8) { echo "Thumbnail Missing<br>"; return FALSE; }
    if ($src = file_get_contents($path)) {
    	if (substr($src,0,2) == 'BM') { echo "<font size=\"-5\">BMP File, Cannot Create Thumbnail</font><br>"; return FALSE; }
		$src = imagecreatefromstring($src);
	    $h_src = imagesx($src); $v_src = imagesy($src);

   		$h = $h_src; $v = $v_src;
   		$hmax = 100; $vmax = 130;
	
   		if ($h > $hmax) { $v = $v / ($h / $hmax); $h = $hmax; }
   		if ($v > $vmax) { $h = $h / ($v / $vmax); $v = $vmax; }
   		$h = floor($h); $v = floor($v);

   		$img = @imagecreatetruecolor($h,$v);
   		if (!$img) { echo "File Creation Error"; return FALSE; }
   		imagecopyresampled($img,$src,0,0,0,0,$h,$v,$h_src,$v_src);
   		imagedestroy($src);
   		imagejpeg($img, $thumbnail, 78);
   		chmod($thumbnail,0666);
   		imagedestroy($img);
   		ob_flush();
		flush();
   		echo "*";
   		return "thumbwidth = ".$h.", thumbheight = ".$v;
    }
    else { echo "Image Load Failure<br>"; return FALSE; }
}

?>
