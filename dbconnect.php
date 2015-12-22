<?php

$dbhost = 'engr-db.engr.oregonstate.edu';
$dbname = 'heer_achieve';
$dbuser = 'heer_achieve';
$dbpass = 'gNc3Phk7';

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname,'3307');
if ($mysqli->connect_errno) {
		printf("Connection Failed, <B>Error: ".mysql_error()."</B><p>Contact <a HREF=\"mailto::support@engr.orst.edu\">COE Support</A></p>Connect failed: %s\n</BODY></HTML>", $mysqli->connect_error);
			exit();
}

?>
