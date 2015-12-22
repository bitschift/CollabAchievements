<?php
include_once 'casconnect.php';
include_once 'dbconnect.php';

$onid = phpCAS::getUser();
$res = $mysqli->query("SELECT * FROM users WHERE onid='$onid'");
$userRow = $res->fetch_array(MYSQLI_ASSOC);		//keep an array of elements in the user's table for easy access

if (isset($_REQUEST['logout'])) {
	phpCAS::logoutWithRedirectService('http://eecs.oregonstate.edu/education/achievements2');
}

echo '<!DOCTYPE html>
	<html>
	<center>
	<head>
	<title>Collaboratory Achievement Management</title>
	<LINK REL=StyleSheet HREF="style.css" TYPE="text/css">
	<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>';
echo '<h1>Collaboratory Achievement Management</h1>
	<h2>Logged in as ', $userRow['firstname'], '</h2>';
echo '<header>
	<div class="nav">
	<nav>
	<ul>
	<li><a href="home.php">Home</a></li>
	<li><a href="approvals.php">Approvals</a></li>
	<li><a href="browse.php">Browse</a></li>
	<li><a href="achievements.php">Achievements</a></li>
	<li><a href="', $_SERVER['PHP_SELF'], '?logout">Logout</a></li>
	</ul></nav></div></header>';
?>
