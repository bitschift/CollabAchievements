<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'header.php';

echo '<body>';

// logout if desired
if (isset($_REQUEST['logout'])) {
	phpCAS::logout();
}
?>

<nav class="navbar navbar-inverse navbar-fixed-top">
		<a class="navbar-brand" href="http://www.oregonstate.edu">Oregon State University</a>
		<div style="padding-right:1%;">
		<?php
		if (isset($onid)){
			echo '<div class="navbar-brand pull-right" style="padding-right:1%;"><span class="glyphicon glyphicon-user"></span>' . $onid . ' - <a href="' . $_SERVER['PHP_SELF'] . '?logout">Logout</a></div>';
		} else {
			echo '<a href="' . $_SERVER['PHP_SELF'] . '?login"><button type="button" class="btn btn-default navbar-btn pull-right">Sign in</button></a>';
		}
		?>
		</div>
	</nav>

<?php

if(isset($_REQUEST['btn-approve']) AND isset($_REQUEST['hash'])) {
	$hash = mysqli_real_escape_string($mysqli, $_REQUEST['hash']);
	$query = "SELECT reviews.id FROM reviews INNER JOIN requests ON requests.id = reviews.requestid WHERE requests.hash = '$hash'";
	//echo '<br><br><br>' . $query . '<br>';
	$result = $mysqli->query($query);
	$row = $result->fetch_assoc();
	$query = "UPDATE reviews SET verdict = 1, completeddate = NOW() WHERE id = " . $row['id'];
	//echo '<br><br><br>' . $query . '<br>';
	$mysqli->query($query);
	
	echo '<h3><BR><BR>All Done!</h3><a href="./home.php">Go Back</a>';
	exit();
}

if(isset($_REQUEST['btn-deny']) AND isset($_REQUEST['hash'])) {
	$hash = mysqli_real_escape_string($mysqli, $_REQUEST['hash']);
	$deny_select = mysqli_real_escape_string($mysqli, $_REQUEST['deny_select']);
	$query = "SELECT reviews.id FROM reviews INNER JOIN requests ON requests.id = reviews.requestid WHERE requests.hash = '$hash'";
	//echo '<br><br><br>' . $query . '<br>';
	$result = $mysqli->query($query);
	$row = $result->fetch_assoc();
	$query = "UPDATE reviews SET verdict = $deny_select, completeddate = NOW() WHERE id = " . $row['id'];
	//echo '<br><br><br>' . $query . '<br>';
	$mysqli->query($query);
	
	echo '<h3><BR><BR>All Done!</h3><a href="./home.php">Go Back</a>';
	exit();
}

if(isset($_REQUEST['btn-abstain']) AND isset($_REQUEST['hash'])) {
	$hash = mysqli_real_escape_string($mysqli, $_REQUEST['hash']);
	$abstain_select = mysqli_real_escape_string($mysqli, $_REQUEST['abstain_select']);
	$query = "SELECT reviews.id FROM reviews INNER JOIN requests ON requests.id = reviews.requestid WHERE requests.hash = '$hash'";
	//echo '<br><br><br>' . $query . '<br>';
	$result = $mysqli->query($query);
	$row = $result->fetch_assoc();
	$query = "UPDATE reviews SET verdict = $abstain_select, completeddate = NOW() WHERE id = " . $row['id'];
	//echo '<br><br><br>' . $query . '<br>';
	$mysqli->query($query);
	
	echo '<h3><BR><BR>All Done!</h3><a href="./home.php">Go Back</a>';
	exit();
}


if (isset($_REQUEST['reviewhash'])){
	$hash = mysqli_real_escape_string($mysqli, $_REQUEST['reviewhash']);
	$query = "SELECT reviews.*, requests.requesterid, requests.achievementid AS levelid, requests.evidence, requests.created, requests.status FROM reviews INNER JOIN requests ON requests.id = reviews.requestid WHERE requests.hash = '$hash' AND reviews.reviewer = " . $userrow['id'];
	//echo '<br><br><br>' . $query . '<br>';
	$result = $mysqli->query($query);
	if ($result->num_rows == 0){
		echo "<div class='row'><div style='padding-top:5em;' class='col-sm-8 col-sm-offset-2'><h3>You are not assigned to review this request.</h3></div></div>";
	} else {
		echo "<div class='row'><div style='padding-top:5em;' class='col-sm-8 col-sm-offset-2'><h3>About to process hash: $hash</h3></div></div>";
		$row = $result->fetch_assoc();
		if ($row['status'] != 0) { //Already completed. No work reviewing needed
			echo "<div class='row'><div style='padding-top:5em;' class='col-sm-8 col-sm-offset-2'><h3>This request has already been processed and a verdict rendered. Thank you for your assistance!</h3></div></div>";
		} else if ($row['verdict'] != 0){
			echo "<div class='row'><div style='padding-top:5em;' class='col-sm-8 col-sm-offset-2'><h3>You have already voted on this request. If you need to change your vote, please email and administrator.</h3></div></div>"; 
		} else {
			$query = "SELECT * FROM users WHERE id = " . $row['requesterid'];
			$result = $mysqli->query($query);
			$requesterrow = $result->fetch_assoc();
			$query = "SELECT levels.*, achievementList.info AS achievementinfo, achievementList.name FROM levels INNER JOIN achievementList ON achievementList.id = levels.achievementid WHERE levels.id = " . $row['levelid'] . " ORDER BY level ASC";
			//echo '<br><br><br>' . $query . '<br>';
			$result = $mysqli->query($query);
			$achievementrow = $result->fetch_assoc();
			echo "
			<div class='row'><div style='padding-top:5em;' class='col-sm-8 col-sm-offset-2'>
			<p>User " . $requesterrow['username'] . " is requesting <b>Level " . $achievementrow['level'] . "</b> of achievement <b>" . 
			$achievementrow['name'] . "</b>. Below you will find the information about this requirement and a link to the evidence provided by " . 
			$requesterrow['username'] . ". Please review the evidence provided bearing in mind the requirements listed. After that, please 
			select Approve, Abstain, or Deny. If you select Abstain or Deny you will be asked to indicate why you selected this option. 
			Your reason will be supplied to the faculty approver only.</p>
			Evidence: <a href='" . $row['evidence'] . "'>" . $row['evidence'] . "</a></div></div>
			<div class='row'><div class='col-sm-5 col-sm-offset-2'>";
			
			
			$query = "SELECT levels.*, achievementList.info AS achievementinfo, achievementList.name FROM levels INNER JOIN achievementList ON achievementList.id = levels.achievementid WHERE levels.achievementid = " . $achievementrow['achievementid'] . " ORDER BY level ASC";
			$result = $mysqli->query($query);
			$achievementrow = $result->fetch_assoc();
			
			echo '<h3>' . $achievementrow['name'] . '</h3><p>' . $achievementrow['achievementinfo'] . '</p>';
			if ($achievementrow['id'] == $row['levelid'])
				echo '<strong>Level: '.$achievementrow['level'].'<BR>' . nl2br($achievementrow['info']) . '</strong><BR>';
			else
				echo 'Level: '.$achievementrow['level'].'<BR>' . nl2br($achievementrow['info']) . '<BR>';
			while ($achievementrow = $result->fetch_assoc()){
				if ($achievementrow['level'] == $row['levelid'])
					echo '<strong>Level: '.$achievementrow['level'].'<BR>' . nl2br($achievementrow['info']) . '</strong><BR>';
				else
					echo 'Level: '.$achievementrow['level'].'<BR>' . nl2br($achievementrow['info']) . '<BR>';
			}
			
			
			
			echo "</div>
			
			<div class='col-sm-3'>
			<div style='padding:1em;margin:1em;background-color:#f2f2f2;border-radius:10px;'>
			<form method='post' class='form-group'>
			<input type='hidden' name='hash' value='$hash'>
			<input class='form-control' type='submit' value='Approve' name='btn-approve'></button></form>
			</div>
			
			<div style='padding:1em;margin:1em;background-color:#f2f2f2;border-radius:10px;'>
			<form method='post' class='form-group'>
			<input type='hidden' name='hash' value='$hash'>
			<label for='abstain_select'>Why are you abstaining? (Select the best option)</label>";
			
			$query = "SELECT * FROM verdicts WHERE verdict = 'Abstain'";
			$result = $mysqli->query($query);
			while ($row = $result->fetch_assoc())
				echo "<div class='radio'><label><input type='radio' name='abstain_select' value='".$row['id']."' required>".$row['content']."</label></div>";
			
			echo "<br>
			<input class='form-control' type='submit' value='Abstain' name='btn-abstain'></button></form>
			</div>
			
			<div style='padding:1em;margin:1em;background-color:#f2f2f2;border-radius:10px;'>
			<form method='post' class='form-group'>
			<input type='hidden' name='hash' value='$hash'>
			<label for='deny_select'>Why are you denying? (Select the best option)</label>";
					
			$query = "SELECT * FROM verdicts WHERE verdict = 'Deny'";
			$result = $mysqli->query($query);
			while ($row = $result->fetch_assoc())
				echo "<div class='radio'><label><input type='radio' name='deny_select' value='".$row['id']."' required>".$row['content']."</label></div>";

			echo "<br>
			<input class='form-control' type='submit' value='Deny' name='btn-deny'></button></form>
			</div></div></div>
			";
		}
	}

} else if (isset($_REQUEST['requesthash'])){
	echo '';	
} else {
?>	
	<div class='row'><div style="padding-top:5em;" class='col-sm-8 col-sm-offset-2'><h3>No Data Submitted</h3></div></div>
<?php
}

?>
</body>
</html>
