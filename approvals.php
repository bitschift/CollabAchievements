<?php
include_once 'header.php';

// if master approves an achievement
if(isset($_POST['btn-approve'])) {
	$achievements = $_POST['achievement'];
	$empRes = $mysqli->query("SELECT * FROM users WHERE id=".$_POST['emp_id']);
	$empRow = $empRes->fetch_array(MYSQLI_ASSOC);
	$empAch = array();
	$empAch = unserialize($empRow['achievements']);

	array_push($empAch, $achievements);

	$serialized = serialize($empAch);
	$mysqli->query("UPDATE users SET achievements='$serialized' WHERE id=".$_POST['emp_id']);
	$mysqli->query("UPDATE requests SET status='1' WHERE id=".$_POST['req_id']);	// status=1 means approved
}

// if master denies an achievement
if(isset($_POST['btn-deny'])) {
	$mysqli->query("UPDATE requests SET status='3' WHERE id=".$_POST['req_id']); // status=3 means denied
}

// if user endorses an achievement
if(isset($_POST['btn-endorse'])) {
	$userid = $userRow['id'];
	$mysqli->query("UPDATE requests SET committeeids='$userid' WHERE id=".$_POST['req_id']);
}

// achievements display for master
// userlevel 3 suggests ability to approve/deny any request
if($userRow['userlevel'] == '3') {
	$requests = $mysqli->query("SELECT * FROM requests");
	$result_length = $requests->num_rows;
	for($i=0;$i<$result_length;$i++) {
		$requestRow = $requests->fetch_array(MYSQLI_ASSOC);
		$achid = $requestRow['achievementid'];
		$empid = $requestRow['requesterid'];
		$achRes = $mysqli->query("SELECT * FROM levels WHERE id=".$achid);
		$achRow = $achRes->fetch_array(MYSQLI_ASSOC);
		$nameRes = $mysqli->query("SELECT * FROM achievementList WHERE id=".$achRow['achievementid']);
		$nameRow = $nameRes->fetch_array(MYSQLI_ASSOC);
		$empRes = $mysqli->query("SELECT * FROM users WHERE id=".$empid);
		$empRow = $empRes->fetch_array(MYSQLI_ASSOC);
		$achievement = $nameRow['name'];
		$name = $empRow['firstname'];
		echo '<form id=request method="post"><input type="hidden" name="req_id" value="', $requestRow['id'], '" /><input type="hidden"
		   	name="achievement" value="', $achid, '" />
			<input type="hidden" name="emp_id" value="', $empid, '" /><p>', $name, ' requested 
			', $achievement, ' Level ', $achRow['level'], '.<br>';
		if ($requestRow['committeeids'] != 0) {
			$endRes = $mysqli->query("SELECT * FROM users WHERE id=".$requestRow['committeeids']);
			$endRow = $endRes->fetch_array(MYSQLI_ASSOC);
			$endorser = $endRow['firstname'];
			echo ' Endorsed by: ', $endorser, '.<br>
			<button type="submit" name="btn-approve">Approve</button><button type="submit" name="btn-deny">Toss</button></form></p>';
		} else if ($requestRow['committeeids'] == 0){
			echo '
			<button type="submit" name="btn-approve">Approve</button><button type="submit" name="btn-deny">Toss</button></form></p>';
		}
	}
} else {
	$ser=$userRow['achievements'];
	$dest = array();
	$dest = unserialize($ser);

	for($x=0;$x<count($dest);$x++) {
		$current = $dest[$x];
		$requests = $mysqli->query("SELECT * FROM requests WHERE achievementid=".$current);
		$cnt = $requests->num_rows;
		for($y=0;$y<$cnt;$y++) {
			$requestRow = $requests->fetch_array(MYSQLI_ASSOC);
			if($requestRow['committeeids'] == 0) {	
				$empid = $requestRow['requesterid'];
				$empRes = $mysqli->query("SELECT * FROM users WHERE id=".$empid);
				$empRow = $empRes->fetch_array(MYSQLI_ASSOC);
				$achid = $requestRow['achievementid'];
				$achRes = $mysqli->query("SELECT * FROM levels WHERE id=".$achid);
				$achRow = $achRes->fetch_array(MYSQLI_ASSOC);
				$nameRes = $mysqli->query("SELECT * FROM achievementList WHERE id=".$achRow['achievementid']);
				$nameRow = $nameRes->fetch_array(MYSQLI_ASSOC);
				$achievement = $nameRow['name'];
				$name = $empRow['firstname'];
				echo '<form id=request method="post"><input type="hidden" name="req_id" value="', $requestRow['id'], '" /><input type="hidden" 
					name="achievement" value="', $achid, '" />
					<input type="hidden" name="name" value="', $empid, '" /><p>', $name, ' requested 
					', $achievement, '.</p>
					<button type="submit" name="btn-endorse">Endorse</button><button type="submit" name="btn-deny">Toss</button></form>';
			}	
		}
	}
}

?>

<!DOCTYPE html>
<html>
<head>
<LINK REL=StyleSheet HREF="style.css" TYPE="text/css">
</head>
<body>

</body>
</html>

