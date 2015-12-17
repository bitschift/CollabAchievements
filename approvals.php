<?php
include_once 'casconnect.php';
include_once 'dbconnect.php';

echo "<h1>Collaboratory Achievement Management</h1>";

$onid = phpCAS:getUser();
$res=mysql_query("SELECT * FROM Employees WHERE onid='$onid'");
$userRow=mysql_fetch_array($res);

if(isset($_REQUEST['logout'])) {
	phpCAS::logout();
}

echo "<h2>Logged in as ", $userRow['name'];
echo "</h2>";
echo '<header>
	<div class="nav">
	<nav>
	<ul>
	<li><a href="home.php">Home</a></li>
	<li><a href="approvals.php">Approvals</a></li>
	<li><a href="browse.php">Browse</a></li>
	<li><a href="logout.php?logout">Logout</a></li>
	</ul>
	</nav>
	</header>';

if(isset($_POST['btn-approve'])) {
	$achievements = $_POST['achievement'];
	$empRes = mysql_query("SELECT * FROM Employees WHERE user_id=".$_POST['emp_id']);
	$empRow = mysql_fetch_array($empRes);
	$empAch = array();
	$empAch = unserialize($empRow['achievements']);

	array_push($empAch, $achievements);

	$serialized = serialize($empAch);
	mysql_query("UPDATE Employees SET achievements='$serialized' WHERE user_id=".$_POST['emp_id']);
	mysql_query("DELETE FROM Requests WHERE req_id=".$_POST['req_id']);	
}

if(isset($_POST['btn-deny'])) {
	mysql_query("DELETE FROM Requests WHERE req_id=".$_POST['req_id']);
}

if(isset($_POST['btn-endorse'])) {
	$userid = $_SESSION['user'];
	mysql_query("UPDATE Requests SET end='$userid' WHERE req_id=".$_POST['req_id']);
}

if($userRow['name'] == 'master') {
	$requests = mysql_query("SELECT * FROM Requests");
	$result_length = mysql_num_rows($requests);
	for($i=0;$i<$result_length;$i++) {
		$requestRow = mysql_fetch_array($requests);
		$achid = $requestRow['ach_id'];
		$empid = $requestRow['emp_id'];
		$achRes = mysql_query("SELECT * FROM Achievements WHERE id=".$achid);
		$achRow = mysql_fetch_array($achRes);
		$empRes = mysql_query("SELECT * FROM Employees WHERE user_id=".$empid);
		$empRow = mysql_fetch_array($empRes);
		$achievement = $achRow['name'];
		$name = $empRow['name'];
		echo '<form id=request method="post"><input type="hidden" name="req_id" value="', $requestRow['req_id'], '" /><input type="hidden"
		   	name="achievement" value="', $achid, '" />
			<input type="hidden" name="emp_id" value="', $empid, '" /><p>', $name, ' requested 
			', $achievement, '.';
		if ($requestRow['end'] != 0) {
			$endRes = mysql_query("SELECT * FROM Employees WHERE user_id=".$requestRow['end']);
			$endRow = mysql_fetch_array($endRes);
			$endorser = $endRow['name'];
			echo ' Endorsed by: ', $endorser, '.<br>
			<button type="submit" name="btn-approve">Approve</button><button type="submit" name="btn-deny">Toss</button></form></p>';
		} else if ($requestRow['end'] == 0){
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
		$requests = mysql_query("SELECT * FROM Requests WHERE ach_id=".$current);
		$cnt = mysql_num_rows($requests);
		for($y=0;$y<$cnt;$y++) {
			$requestRow = mysql_fetch_array($requests);
			if($requestRow['end'] == 0) {	
				$empid = $requestRow['emp_id'];
				$empRes = mysql_query("SELECT * FROM Employees WHERE user_id=".$empid);
				$empRow = mysql_fetch_array($empRes);
				$achid = $requestRow['ach_id'];
				$achRes = mysql_query("SELECT * FROM Achievements WHERE id=".$achid);
				$achRow = mysql_fetch_array($achRes);
				$achievement = $achRow['name'];
				$name = $empRow['name'];
				echo '<form id=request method="post"><input type="hidden" name="req_id" value="', $requestRow['req_id'], '" /><input type="hidden" 
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

