<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../dbconnect.php';
include_once '../phpfunctions.php';

echo 'Entering ajax_updateuserlevel.php';
if (isset($_REQUEST['userhash'])){
	$userhash = $mysqli->real_escape_string($_REQUEST['userhash']);
	//echo '<BR>Found Hash';
}else
	exit();
if (isset($_REQUEST['userid'])){
	$userid = $mysqli->real_escape_string($_REQUEST['userid']);
	//echo '<BR>Found ID';
}else
	exit();
if (isset($_REQUEST['userlevel'])){
	$userlevel = $mysqli->real_escape_string($_REQUEST['userlevel']);
	//echo '<BR>Found Level';
}else
	exit();


$query = "SELECT * FROM users WHERE hash = '$userhash' AND userlevel > 2";
//echo $query . '<BR>';
$result = $mysqli->query($query);
if ($result->num_rows > 0){
	$query = "UPDATE `users` SET userlevel = $userlevel WHERE id = $userid";
	//echo $query . '<BR>';
	$mysqli->query($query);
	//Should check this happened and send a congratulations email!
	//email_message($subject, $row['onid']. '@oregonstate.edu', $body);
	}
