<?php
include_once 'header.php';

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

$users = $mysqli->query("SELECT * FROM users");
$num_users = $users->num_rows;

//if($userRow['name'] == 'master') {
	for($i=0;$i<$num_users;$i++) {
		$row = $users->fetch_array(MYSQLI_ASSOC);
		echo '<div id=request>
			<p>', $row['firstname'], ' ', $row['lastname'], '</p>
			<p>', $row['username'], '</p>';
		$ach = unserialize($row['achievements']);
		for($y=0;$y<count($ach);$y++) {

			$achres = $mysqli->query("SELECT * FROM levels WHERE id=".$ach[$y]);
			$achrow = $achres->fetch_array(MYSQLI_ASSOC);
			$nameres = $mysqli->query("SELECT * FROM achievementList WHERE id=".$achrow['achievementid']);
			$namerow = $nameres->fetch_array(MYSQLI_ASSOC);
			echo '<input type="hidden" name="empid" value="', $row['id'], '" /><input type="hidden" name="achid" value="', $achrow['id'], '" />
				<input type="image" src="img/', $achrow['image'], '" alt="', $namerow['name'], '" name="ach-delete" height="24" width="24" />';
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
