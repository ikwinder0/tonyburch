<?php

require('code/db.php');
require 'code/datGallery.php';
require('code/login.php');

require('header.php');

mysql_query('UPDATE pagecounter SET totalviews = totalviews + 1 WHERE page = "index.php"');
MembersBaseGallery();
?>
</body>
</html>