<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../phpfunctions.php';
include_once '../dbconnect.php';

if (isset($_REQUEST['sessionhash'])) {
	if ($_REQUEST['sessionhash'] != '') {
		// For all users, currently in room, send email.
		$hash = $_REQUEST['sessionhash'];
		$query = "UPDATE `clock` SET timeout = NOW() WHERE timeout = '0000-00-00 00:00:00' AND hash = '$hash'";
		$result = $mysqli->query($query);
		//echo $query . '<BR>';
		echo '<h1> SUCCESS </h1>';	
	}
}
?>
