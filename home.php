<?php
include_once 'header.php';

if(isset($_REQUEST['btn-request'])) {
	$achievement = $_REQUEST['achievement'];
	$level = $_REQUEST['level'];
	$userAch = unserialize($userrow['achievements']);
	$userid = $userrow['id'];

	$levelRes = $mysqli->query("SELECT * FROM levels WHERE achievementid='$achievement' && level='$level'");
	$levelRow = $levelRes->fetch_array(MYSQLI_ASSOC);
	
	for($i=0;$i<count($userAch);$i++) {
		if($userAch[$i] == $achievement) {
?>
			<script>alert('Already have achievement');</script>
<?php	}
	}
	
	$reqAch = $levelRow['id'];
	$query = "SELECT * FROM achievementList WHERE id = $achievement";
	$result = $mysqli->query($query);
	$row = $result->fetch_assoc();
	$userrow['name'] = $row['name'];
	$userrow['level'] = $level;
	
	$query = "SELECT * FROM requests WHERE requesterid = '$userid' AND achievementid = '$reqAch' AND status = 0";
	$result = $mysqli->query($query);
	if ($result->num_rows > 0) {//Already Under Review
		echo "<script>alert('You have already requested to be reviewed for level $level of the " . $userrow['name'] . " achievement. Please wait for the that review to complete.');</script>";
	} else{
	$mysqli->query("INSERT INTO requests(requesterid, achievementid) VALUES('$userid', '$reqAch')");
	
	
	$query = "SELECT * FROM achievementList WHERE id = $achievement";
	$result = $mysqli->query($query);
	$row = $result->fetch_assoc();
	$userrow['name'] = $row['name'];
	$userrow['level'] = $level;
	
	email_message('Achievement Request', $userrow['onid'] . '@oregonstate.edu', create_message('./emails/request.eml', $userrow));
	}
	
}

if(isset($_REQUEST['btn-give'])) {
	$achievement = $_REQUEST['achievement'];
	$level = $_REQUEST['level'];
	$employeeid = $_REQUEST['employee'];	
	$empRes = $mysqli->query("SELECT * FROM users WHERE id='$employeeid'");
	$empRow = $empRes->fetch_array(MYSQLI_ASSOC);
	$empAch = array();
	$empAch = unserialize($empRow['achievements']);
	
	$levelRes = $mysqli->query("SELECT * FROM levels WHERE achievementid='$achievement' && level='$level'");
	$levelRow = $levelRes->fetch_array(MYSQLI_ASSOC);

	//query to erase a lower level if one exists
	$lowerRes = $mysqli->query("SELECT * FROM levels WHERE achievementid='$achievement'");
	$lowerRow = $lowerRes->fetch_array(MYSQLI_ASSOC);

	for($i=0;$i<count($empAch);$i++) {
		if($empAch[$i] == $levelRow['id']) {	
?>
			<script>alert('Already have achievement');</script>
<?php	}
		// remove previous levels
		if($empAch[$i] == $lowerRow['id']) {
			unset($empAch[$i]);
		}
	}

	array_push($empAch, $levelRow['id']);
	$serialized = serialize($empAch);
	$mysqli->query("UPDATE users SET achievements='$serialized' WHERE id=".$_REQUEST['employee']);
}

if(isset($_REQUEST['btn-endorse'])) {
	$achievement = $_REQUEST['achievement'];
	$empRes = $mysqli->query("SELECT * FROM users WHERE id=".$_REQUEST['employee']);
	$empRow = $empRes->fetch_array(MYSQLI_ASSOC);
	$empAch = array();
	$empAch = unserialize($empRow['achievements']);
	$empid = $empRow['id'];
	$userid = $userrow['id'];

	$levelRes = $mysqli->query("SELECT * FROM levels WHERE achievementid='$achievement' && level='$level'");
	$levelRow = $levelRes->fetch_array(MYSQLI_ASSOC);
	
	for($i=0;$i<count($empAch);$i++) {
		for($y=0;$y<count($achievements);$y++) {
			if($empAch[$i] == $achievements[$y]) {
?>
				<script>alert('Already have achievement');</script>
<?php		}
		}
	}

	$reqAch = $levelRow['id'];
	$mysqli->query("INSERT INTO requests(requesterid, achievementid, committeeids) VALUES('$empid', '$reqAch', '$userid')");	
	
}

$ser = $userrow['achievements'];
$dest = unserialize($ser);
$cnt = count($dest);

echo '<body>';

if($userrow['userlevel'] == '3') {
	$achieve = $mysqli->query("SELECT * FROM achievementList");
	$ach_length = $achieve->num_rows;

	$empl = $mysqli->query("SELECT * FROM users");
	$emplength = $empl->num_rows;

	echo '<div id="request"><form method="post">
		Give <select name="employee">';
	for($x=0;$x<$emplength;$x++) {
		$empRow = $empl->fetch_array(MYSQLI_ASSOC);
		echo '<option value="', $empRow['id'], '">', $empRow['firstname'], '</option>';
	}
	echo '</select>';
	
	echo ' the achievement ';
    echo '<select name="achievement">';	
	for($i=0;$i<$ach_length;$i++) {
		$achRow = $achieve->fetch_array(MYSQLI_ASSOC);
		echo '<option value="', $achRow['id'], '">', $achRow['name'], '</option>';
	}
	echo '</select><select name="level">';
	echo '<option value="1">Level 1</option>
		<option value="2">Level 2</option>
		<option value="3">Level 3</option>';
	echo '</select><br><br>';
	echo '<input type="submit" name="btn-give"></button></form></div>';
} else if($cnt > 1) {
	$empl = $mysqli->query("SELECT * FROM users");
	$emplength = $empl->num_rows;

	echo '<div id="request"><form method="post">
		Endorse <select name="employee">';
	
	for($x=0;$x<$emplength;$x++) {
		$empRow = $empl->fetch_array(MYSQLI_ASSOC);
		echo '<option value="', $empRow['id'], '">', $empRow['firstname'], '</option>';
	}
	echo '</select>';

	echo ' for the achievement ';
	echo '<select name="achievement">';
	
	for($i=0;$i<$cnt;$i++) {
		$current = $dest[$i];
		$achRes = $mysqli->query("SELECT * FROM achievementList WHERE id=".$current);
		$achRow = $achRes->fetch_array(MYSQLI_ASSOC);
		echo '<option value="', $achRow['id'], '">', $achRow['name'], '</option>';
	}
	echo '</select><select name="level">';
	echo '<option value="1">Level 1</option>
		<option value="2">Level 2</option>
		<option value="3">Level 3</option>';
	echo '</select><br><br>';
	echo '<input type="submit" name="btn-endorse"></button></form></div>';
}


	$achieve = $mysqli->query("SELECT * FROM achievementList");
	$ach_length = $achieve->num_rows;

	$empl = $mysqli->query("SELECT * FROM users");
	$emplength = $empl->num_rows;

	echo '<div id="request"><form method="post">Request the achievement ';
    echo '<select name="achievement">';	
	for($i=0;$i<$ach_length;$i++) {
		$achRow = $achieve->fetch_array(MYSQLI_ASSOC);
		echo '<option value="', $achRow['id'], '">', $achRow['name'], '</option>';
	}
	echo '</select><select name="level">';
	echo '<option value="1">Level 1</option>
		<option value="2">Level 2</option>
		<option value="3">Level 3</option>';
	echo '</select><br><br>';
	echo '<input type="submit" name="btn-request"></button></form></div>';


echo '</body>';

?>
