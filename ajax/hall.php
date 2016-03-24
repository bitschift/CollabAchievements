<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../phpfunctions.php';
include_once '../dbconnect.php';

if (isset($_REQUEST['room'])) {
	if ($_REQUEST['room'] != 0) {
		// For all users, currently in room, send email.
		$query = "SELECT clock.*, users.onid, users.firstname, rooms.name FROM `clock` INNER JOIN `users` ON clock.userid = users.id INNER JOIN `rooms` ON rooms.id = clock.roomid WHERE `timeout` = '0000-00-00 00:00:00'";
		$result = $mysqli->query($query);
		//echo $query . '<BR>';
		while ($row = $result->fetch_assoc()){
			email_message('Test Message', $row['onid'] . '@oregonstate.edu', create_message('../emails/inroom.eml', $row));	
		}
		echo '<h1> SUCCESS </h1>';	
	}
}
?>
