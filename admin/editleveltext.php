<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include_once '../casconnect.php';
include_once '../dbconnect.php';
include_once '../phpfunctions.php';


$onid = phpCAS::getUser();
$res = $mysqli->query("SELECT * FROM users WHERE onid = '$onid'");
$userrow = $res->fetch_array(MYSQLI_ASSOC);		//keep an array of elements in the user's table for easy access

if (isset($_REQUEST['logout'])) {
	phpCAS::logoutWithRedirectService('http://eecs.oregonstate.edu/education/achievements');
}

echo '<!DOCTYPE html>
	<html>
	<head>
	<title>Collaboratory Achievement Management</title>
	</head>';
?>

	<!-- Bootstrap -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../js/bootstrap.min.js"></script>
	
	<script src="../js/sorttable.js"></script>
	

<?php
echo '<body>';

if (isset($_POST['btn-update'])) {
	updateAchievement($_POST['info'], $_POST['levelid'], $mysqli);
}

function loadAchievement($levelid, $mysqli) {
	$query = "SELECT levels.*, achievementList.name FROM levels INNER JOIN achievementList ON achievementList.id = levels.achievementid WHERE levels.id = '$levelid'";
	$res = $mysqli->query($query);
	$numRows = $res->num_rows;
	$row = $res->fetch_array(MYSQLI_ASSOC);
	
	echo '<div class="row"><div class="col-xs-8 col-xs-offset-2">';
	echo '<h4>Level ' . $row['level'] . ' - ' . $row['name'] . '</h4><form method="post">';
	echo '<input type="hidden" name="levelid" value="', $levelid, '">';
	echo '<textarea class="form-control" name="info" rows="10">', $row['info'], '</textarea><BR>';
	echo '<input type="submit" name="btn-update"></button>';
	echo '</form></div></div>'; 
} 

function updateAchievement($info, $levelid, $mysqli) {
	$res = $mysqli->query("SELECT * FROM levels WHERE id=".$levelid);
	$numRows = $res->num_rows;
	$row = $res->fetch_array(MYSQLI_ASSOC);
	
	$mysqli->query("UPDATE levels SET info='$info' WHERE id='$levelid'");
}

$achRes = $mysqli->query("SELECT * FROM achievementList");
$count = $achRes->num_rows;

?>

<nav class="navbar navbar-inverse navbar-fixed-top">
		<a class="navbar-brand" href="http://www.oregonstate.edu">Oregon State University</a>
		<div style="padding-right:1%;">
<?php
if (isset($onid)){
	echo '<div class="navbar-brand pull-right" style="padding-right:1%;"><span class="glyphicon glyphicon-user"></span> <a href="../profile.php">Account (' . $onid . ')</a> - <a href="' . $_SERVER['PHP_SELF'] . '?logout">Logout</a> - <a href="../home.php">Home</a></div>';
} else {
	echo '<a href="' . $_SERVER['PHP_SELF'] . '?login"><button type="button" class="btn btn-default navbar-btn pull-right">Sign in</button></a>';
}
?>
		</div>
</nav>

<?php
echo '<div class="row" style="padding-top:2em;"><div class="col-xs-8 col-xs-offset-2">';
echo '<form method="post">';
echo '<p>Select an achievement to modify: </p><br>
	<select name="achievement">';
for ($y=0; $y<$count; $y++) {
	$achRow = $achRes->fetch_array(MYSQLI_ASSOC);
	echo '<option value="', $achRow['id'], '">', $achRow['name'], '</option>';
}
echo '</select><br><br>';
echo '<input type="submit" name="btn-request"></button></form></div></div>';

if (isset($_POST['btn-request'])) {
	$achievementid = $_POST['achievement'];
	
	$query = $mysqli->query("SELECT levels.*, achievementList.name FROM levels INNER JOIN achievementList ON achievementList.id = levels.achievementid WHERE levels.achievementid='$achievementid'");
	$cnt = $query->num_rows;
	$scoop = $query->fetch_array(MYSQLI_ASSOC);
	loadAchievement($scoop['id'], $mysqli);
	for ($x=1;$x<$cnt;$x++) {
		$scoop = $query->fetch_array(MYSQLI_ASSOC);
		loadAchievement($scoop['id'], $mysqli);
	}
}

?>

</body>
</html>
