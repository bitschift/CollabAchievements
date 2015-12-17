<?php
session_start();
include_once 'dbconnect.php';

echo "<h1>Collaboratory Achievement Management</h1>";

if(isset($_REQUEST['logout'])) {
	phpCAS::logout();
}

$onid = phpCAS::getUser();
$res=mysql_query("SELECT * FROM Employees WHERE user_id='$onid'");
$userRow=mysql_fetch_array($res);

/*IF($_POST['ach-delete']) {
	$achievement = $_POST['achid'];
	echo '<h1>Achievement:', $achievement, '</h1>';
	$userres = mysql_query("SELECT * FROM Employees WHERE user_id=".$_POST['empid']);
	$terirow = mysql_fetch_array($userres);
	$teriach = $terirow['achievements'];
	$userach = unserialize($teriach);
//	for($x=0;$x<count($userach);$x++) {
//		if($userach[$x]==$achievement) {
			unset($userach[$achievement]);
			$finalach = serialize($userach);
			mysql_query("UPDATE Employees SET achievements='$finalach' WHERE user_id=".$_POST['empid']);
//		}
//	}
}*/

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

$users = mysql_query("SELECT * FROM Employees");
$num_users = mysql_num_rows($users);

//if($userRow['name'] == 'master') {
	for($i=0;$i<$num_users;$i++) {
		$row = mysql_fetch_array($users);
		echo '<div id=request>
			<p>', $row['name'], '</p>
			<p>', $row['project'], '</p>';
		$ach = unserialize($row['achievements']);
		for($y=0;$y<count($ach);$y++) {
			$achres = mysql_query("SELECT * FROM Achievements WHERE id=".$ach[$y]);
			$achrow = mysql_fetch_array($achres);

			echo '<input type="hidden" name="empid" value="', $row['user_id'], '" /><input type="hidden" name="achid" value="', $achrow['id'], '" />
				<input type="image" src="', $achrow['img'], '" alt="', $achrow['name'], '" name="ach-delete" height="24" width="24" />';
		}
		echo '<br></div>';
	}
//}
?>

<!DOCTYPE html>
<html>
<head>
<title>Collaboratory Employee Management</title>
<LINK REL=StyleSheet HREF="style.css" TYPE="text/css">
</head>
</html>
