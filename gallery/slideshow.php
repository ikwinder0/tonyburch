<?php
require('code/db.php');
require('code/login.php');
require('header.php');

$model = ""; $modelset = "";
if (array_key_exists('m', $_GET)) {	
	$model = $_GET['m']; $title = $model;
	if (array_key_exists('s', $_GET)) {	$modelset = $_GET['s']; $title = $modelset; }
}
else { echo "No set listed."; exit; }
?>
<!-- sections from http://gallery.sourceforge.net/ -->
<?php

$total_photos = 1;
$query = mysql_query("SELECT image_id FROM pictures WHERE modelname = \"". mysql_real_escape_string($model) ."\" AND setname = \"". mysql_real_escape_string($modelset) ."\" ORDER BY filename ASC");
while ($row = mysql_fetch_assoc($query)) {
   	echo '<a id="photo'. $total_photos .'" name="'. $row['image_id'] .'"></a>';
	$total_photos++;
}

if ($total_photos > 1) { 
	$total_photos--;
	$title = $modelset;
	$title = str_replace("_", " ", $title);
  	$title = substr($title,strpos($title," "));
    echo "<h1>" . ucfirst($title) . "</h1>\n";
	
}
else { echo "No images found"; exit; }
?>

<script language="JavaScript">
var timer;
var current_location = 1; var next_location = 1; 
var preload_pic = 0; var playing = 0; var direction = 1;
var timeout_value;
var images = new Array;
var loop = 1;

function StopStart() { if (playing) { stop(); } else { start(); } }

function ToggleLoop() {
	if (loop) {	loop = 0; document.getElementById("LoopText").innerHTML = "<strike>Looping Set</strike>"; } 
	else { loop = 1; document.getElementById("LoopText").innerHTML = "Looping Set"; } 
}

function stop() {
    document.getElementById("StopStartText").innerHTML = "Resume"
    playing = 0;
    clearTimeout(timer);
}

function start() {
    document.getElementById("StopStartText").innerHTML = "Pause"
    playing = 1;
    go_to_next_photo();
}

function changeDirection() {
    if (direction == 1) {
    	document.getElementById("Directions").innerHTML = "<=" 
    	direction = -1;
    } 
    else {
    	document.getElementById("Directions").innerHTML = "=>"
    	direction = 1;
    }
    preload_next_photo();
}

function reset_timer() {
    clearTimeout(timer);
    if (playing) {
		timeout_value = document.Slideshow.time.options[document.Slideshow.time.selectedIndex].value * 1000;
		timer = setTimeout('go_to_next_photo()', timeout_value);
    }
}

function wait_for_current_photo() {
    if (!show_current_photo()) {
		clearTimeout(timer);
		timer = setTimeout('wait_for_current_photo()', 250);
		return 0;
    } 
    else {
		preload_next_photo();
		reset_timer();
    }
}

function go_to_last_photo() {
	next_location = current_location;
    current_location = (parseInt(current_location) - parseInt(direction));
    
    if (current_location > <?php echo $total_photos; ?>) { current_location = 1; }
    if (current_location <= 0) { current_location = <?php echo $total_photos; ?>; }

    if (!show_current_photo()) {
		wait_for_current_photo();
		return 0;
    }
    reset_timer();
}


function go_to_next_photo() {
    current_location = next_location;

    if (!show_current_photo()) {
		wait_for_current_photo();
		return 0;
    }
    
    preload_next_photo();
    reset_timer();
}

function preload_next_photo() {
    next_location = (parseInt(current_location) + parseInt(direction));
    if (next_location > <?php echo $total_photos; ?>) {
		next_location = 1;
		if (!loop) { stop(); }
    }
    if (next_location <= 0) {
        next_location = <?php echo $total_photos; ?>;
		if (!loop) { stop(); }
    }
    
    preload_photo(next_location);
}

function show_current_photo() {
    if (!images[current_location] || !images[current_location].complete) {
		preload_photo(current_location);
		return 0;
    }

    document.slide.src = images[current_location].src;
    document.getElementById("CurrentText").innerHTML = current_location;
    return 1;
}

function preload_photo(index) {
    if (preload_pic < <?php echo $total_photos; ?>) {
		if (!images[index]) {
		    images[index] = new Image;
	    	images[index].src = 'throwpic.php?m=<?php echo urlencode($model); ?>&s=<?php echo urlencode($modelset); ?>&p=' + document.getElementById("photo" + index).name;
	    	preload_pic++;
		}
    } 
}
</Script>
<form name="Slideshow">

<table width="100%" border="1" cellspacing="0" cellpadding="0">
<tr><td align="middle" valign="middle">
<a href='#' onClick='StopStart(); return false;'><span id='StopStartText'>Pause</span></a>&nbsp;|&nbsp;<a href='#' onClick='changeDirection(); return false;'>Direction <span id='Directions'>=></span></a>&nbsp;|&nbsp;Delay: 
<select name="time" size="1"  onchange="reset_timer()">
<option value="5" selected> 5 seconds
<option value="10"> 10 seconds
<option value="15"> 15 seconds
<option value="30"> 30 seconds
<option value="45"> 45 seconds
<option value="60"> 60 seconds
</select>
&nbsp;|&nbsp;<a href='#' onclick='ToggleLoop();'><span id='LoopText'>Looping Set</span></a>
</td>
</tr>
</table>

<br>

<script language="JavaScript">
document.write("<img border=0 src=\"");
document.write('throwpic.php?m=<?php echo urlencode($model); ?>&s=<?php echo urlencode($modelset); ?>&p=' + document.getElementById("photo1").name);
document.write('" name=slide height="600">');
</script>
<noscript>
You'll need JavaScript enabled for the slideshow to work.
</noscript>
<br>

<p>
<img src="arrowleft.png" onclick='go_to_last_photo()'>&nbsp;
Image <span id='CurrentText'>0</span> of <?php echo $total_photos; ?>
&nbsp;&nbsp;<img src="arrowright.png" onclick='go_to_next_photo()'>
<script language="Javascript">
/* Start the show. */
preload_photo(1);
start();
</script>
</form>

</td></tr></table>
</body>
</html>
