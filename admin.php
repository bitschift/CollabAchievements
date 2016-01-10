<?php
include_once 'header.php';

echo '<body>';

if (isset($_POST['btn-request'])) {
	$achievementid = $_POST['achievement'];
	
	$poop = $mysqli->query("SELECT * FROM levels WHERE achievementid='$achievementid'");
	$cnt = $poop->num_rows;
	for ($x=0;$x<$cnt;$x++) {
		$scoop = $poop->fetch_array(MYSQLI_ASSOC);
		echo '<br><p>Level ', $x+1, '</p><br>';
		loadAchievement($scoop['id'], $mysqli);
	}
}

if (isset($_POST['btn-update'])) {
	updateAchievement($_POST['info'], $_POST['levelid'], $mysqli);
}

function loadAchievement($levelid, $mysqli) {
	$res = $mysqli->query("SELECT * FROM levels WHERE id=".$levelid);
	$numRows = $res->num_rows;
	$row = $res->fetch_array(MYSQLI_ASSOC);
	
	echo '<form method="post">';
	echo '<input type="hidden" name="levelid" value="', $levelid, '">';
	echo '<textarea name="info" rows="10" cols="30">', $row['info'], '</textarea>';
	echo '<input type="submit" name="btn-update"></button>';
	echo '</form>'; 
} 

function updateAchievement($info, $levelid, $mysqli) {
	$res = $mysqli->query("SELECT * FROM levels WHERE id=".$levelid);
	$numRows = $res->num_rows;
	$row = $res->fetch_array(MYSQLI_ASSOC);
	
	$mysqli->query("UPDATE levels SET info='$info' WHERE id='$levelid'");
}

$achRes = $mysqli->query("SELECT * FROM achievementList");
$count = $achRes->num_rows;

echo '<form method="post">';
echo '<p>Select an achievement to modify: </p><br>
	<select name="achievement">';
for ($y=0; $y<$count; $y++) {
	$achRow = $achRes->fetch_array(MYSQLI_ASSOC);
	echo '<option value="', $achRow['id'], '">', $achRow['name'], '</option>';
}
echo '</select><br><br>';
echo '<input type="submit" name="btn-request"></button></form></div>';

echo '<form method="post">';
echo '<p>Select a user to modify: </p><br>
	<select name="user">';
for 

echo '</body>';
?>
