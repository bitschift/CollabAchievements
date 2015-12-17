<?php

$dbhost = 'oniddb.cws.oregonstate.edu';
$dbname = 'parasura-db';
$dbuser = 'parasura-db';
$dbpass = 'XVIIBwcT84mSvLlJ';

$mysql_handle = mysql_connect($dbhost, $dbuser, $dbpass)
	    or die("Error connecting to database server");

mysql_select_db($dbname, $mysql_handle)
	    or die("Error selecting database: $dbname");

?>
