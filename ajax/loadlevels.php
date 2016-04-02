<?php
require_once ('../phpfunctions.php');
//include_once '../casconnect.php';
include_once '../dbconnect.php';

if (isset($_REQUEST['room']) && $_REQUEST['room'] != 0) {
	$room = mysqli_real_escape_string($mysqli, $_REQUEST['room']);
	$id = $_REQUEST['id'];
	$query = "SELECT * FROM clock WHERE userid = $id AND timeout = '0000-00-00 00:00:00'";
	$result = $mysqli->query($query);
	if ($result->num_rows == 0){
		$query = "INSERT INTO clock(userid, roomid, hash) VALUES('$id', '$room', '" . randomhash() . "')";
		$mysqli->query($query);
	}
}

if (isset($_REQUEST['id']) && !isset($_REQUEST['room']) && $_REQUEST['id'] != 0) {
	$id = $_REQUEST['id'];
	$query = "DELETE FROM clock WHERE userid='$id'";
	$mysqli->query($query);
}	

if (isset($_REQUEST['giveachievement']) && $_REQUEST['giveachievement'] != 0){
	$giveachievement = mysqli_real_escape_string($mysqli, $_REQUEST['giveachievement']);

	$query = "SELECT * FROM levels WHERE achievementid = $giveachievement ORDER BY level ASC";
	//echo $query . '<BR>';
	$result = $mysqli->query($query);
	$i=1;
	while ($row = $result->fetch_assoc()){
		echo '<option value="'.$i.'">Level '.$i.'</option>'; 
		$i++;
		
	}
} 

if (isset($_REQUEST['requestachievement']) && $_REQUEST['requestachievement'] != 0){
	$requestachievement = mysqli_real_escape_string($mysqli, $_REQUEST['requestachievement']);

	$query = "SELECT * FROM levels WHERE achievementid = $requestachievement ORDER BY level ASC";
	//echo $query . '<BR>';
	$result = $mysqli->query($query);
	$i=1;
	while ($row = $result->fetch_assoc()){
		echo '<option value="'.$i.'">Level '.$i.'</option>'; 
		$i++;
	}
}
