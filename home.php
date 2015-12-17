<?php
include_once 'casconnect.php';
include_once 'dbconnect.php';

echo "<h1>Collaboratory Achievement Management</h1>";

$onid = phpCAS::getUser();
$res=mysql_query("SELECT * FROM Employees WHERE onid='$onid'");
$userRow=mysql_fetch_array($res);

if(isset($_REQUEST['logout'])) {
	phpCAS::logout();
}

if(isset($_POST['btn-request'])) {
	$achievements = array();
	$achievements = $_POST['achievement'];
	$userAch = unserialize($userRow['achievements']);
	$userid = $_SESSION['user'];

	for($i=0;$i<count($userAch);$i++) {
		for($y=0;$y<count($achievements);$y++) {
			if($userAch[$i] == $achievements[$y]) {
?>
				<script>alert('Already have achievement');</script>
<?php		} else {
				$reqAch = $achievements[$y];
				mysql_query("INSERT INTO Requests(emp_id, ach_id) VALUES('$userid', '$reqAch')");	
			}
		}
	}

	if(count($userAch) == 0) {
		for($y=0;$y<count($achievements);$y++) {
			$reqAch = $achievements[$y];
			mysql_query("INSERT INTO Requests(emp_id, ach_id) VALUES('$userid', '$reqAch')");	
		}	
	}
}

if(isset($_POST['btn-give'])) {
	$achievements = array();
	$achievements = $_POST['achievement'];		
	$empRes = mysql_query("SELECT * FROM Employees WHERE user_id=".$_POST['employee']);
	$empRow = mysql_fetch_array($empRes);
	$empAch = array();
	$empAch = unserialize($empRow['achievements']);

	for($i=0;$i<count($empAch);$i++) {
		for($y=0;$y<count($achievements);$y++) {
			if($empAch[$i] == $achievements[$y]) {
?>
				<script>alert('Already have achievement');</script>
<?php		}
		}
	}
	
	for($i=0;$i<count($achievements);$i++) {
		array_push($empAch, $achievements[$i]);
	}
	$serialized = serialize($empAch);
	mysql_query("UPDATE Employees SET achievements='$serialized' WHERE user_id=".$_POST['employee']);
}

if(isset($_POST['btn-endorse'])) {
	$achievements = array();
	$achievements = $_POST['achievement'];
	$empRes = mysql_query("SELECT * FROM Employees WHERE user_id=".$_POST['employee']);
	$empRow = mysql_fetch_array($empRes);
	$empAch = array();
	$empAch = unserialize($empRow['achievements']);
	$empid = $empRow['user_id'];
	$userid = $userRow['user_id'];

	for($i=0;$i<count($empAch);$i++) {
		for($y=0;$y<count($achievements);$y++) {
			if($empAch[$i] == $achievements[$y]) {
?>
				<script>alert('Already have achievement');</script>
<?php		} else {
				$reqAch = $achievements[$y];
				mysql_query("INSERT INTO Requests(emp_id, ach_id, end) VALUES('$empid', '$reqAch', '$userid')");	
			}
		}
	}
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

if($userRow['onid'] == 'heer') {
	$achieve = mysql_query("SELECT * FROM Achievements");
	$ach_length = mysql_num_rows($achieve);

	$empl = mysql_query("SELECT * FROM Employees");
	$emplength = mysql_num_rows($empl);

	echo '<form method="post">
		Give <select name="employee">';
	for($x=0;$x<$emplength;$x++) {
		$empRow = mysql_fetch_array($empl);
		echo '<option value="', $empRow['user_id'], '">', $empRow['name'], '</option>';
	}
	echo '</select>';
	
	echo ' the achievement ';
    echo '<select multiple name="achievement[]">';	
	for($i=0;$i<$ach_length;$i++) {
		$achRow = mysql_fetch_array($achieve);
		echo '<option value="', $achRow['id'], '">', $achRow['name'], '</option>';
	}
	echo '<input type="submit" name="btn-give"></button></form>';
} else if($userRow['achievements']!="") {
	$ser = $userRow['achievements'];
	$dest = unserialize($ser);
	$cnt = count($dest);

	$empl = mysql_query("SELECT * FROM Employees");
	$emplength = mysql_num_rows($empl);

	echo '<form method="post">
		Endorse <select name="employee">';
	
	for($x=0;$x<$emplength;$x++) {
		$empRow = mysql_fetch_array($empl);
		echo '<option value="', $empRow['user_id'], '">', $empRow['name'], '</option>';
	}
	echo '</select>';

	echo ' for the achievement ';
	echo '<select multiple name="achievement[]">';
	
	for($i=0;$i<$cnt;$i++) {
		$current = $dest[$i];
		$achRes = mysql_query("SELECT * FROM Achievements WHERE id=".$current);
		$achRow = mysql_fetch_array($achRes);
		echo '<option value="', $achRow['id'], '">', $achRow['name'], '</option>';
	}
	echo '<input type="submit" name="btn-endorse"></button></form>';
}

if($userRow['onid'] != 'heer') {
	$achieve = mysql_query("SELECT * FROM Achievements");
	$ach_length = mysql_num_rows($achieve);

	$empl = mysql_query("SELECT * FROM Employees");
	$emplength = mysql_num_rows($empl);

	echo '<form method="post">Request the achievement ';
    echo '<select multiple name="achievement[]">';	
	for($i=0;$i<$ach_length;$i++) {
		$achRow = mysql_fetch_array($achieve);
		echo '<option value="', $achRow['id'], '">', $achRow['name'], '</option>';
	}
	echo ' <input type="submit" name="btn-request"></button></form>';
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Tekbots/Collaboratory Employee Management</title>
<LINK REL=StyleSheet HREF="style.css" TYPE="text/css">
<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
</script>
</head>
<body>
