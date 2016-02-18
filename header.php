<?php
include_once 'casconnect.php';
include_once 'dbconnect.php';
include_once 'phpfunctions.php';


$onid = phpCAS::getUser();
$res = $mysqli->query("SELECT * FROM users WHERE onid = '$onid'");
$userrow = $res->fetch_array(MYSQLI_ASSOC);		//keep an array of elements in the user's table for easy access

if (isset($_REQUEST['logout'])) {
	phpCAS::logoutWithRedirectService('http://eecs.oregonstate.edu/education/achievements');
}

echo '<!DOCTYPE html>
	<html>
	<head>
	<title>Collaboratory Achievement Management</title>
	</head>';
?>

	<!-- Bootstrap -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="./js/bootstrap.min.js"></script>
	
	
	
	
	
