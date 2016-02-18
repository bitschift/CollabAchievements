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
	phpCAS::logoutWithRedirectService('http://eecs.oregonstate.edu/education/achievements3');
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

if (isset($_REQUEST['btn-signup'])) {
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


if ($userrow['userlevel'] > 2){
	echo '<div class="row" style="padding-top:2em;"><div style="padding-top:5em;" class="col-sm-10 col-sm-offset-1"><div class="table-responsive">';
	
	
//	$tabledata = '<table id="table-custom-sort" data-sort-name="price" data-sort-order="desc" class="table table-condensed sortable" style="border-collapse:collapse;" style="border-right:none;border-bottom:none;"><thead><tr><th data-field="name" data-sortable="true">Task Name &#x25B4&#x25BE</th><th data-field="due" data-sortable="true">Due Date &#x25B4&#x25BE</th><th data-field="completed" data-sortable="true">Completed Date &#x25B4&#x25BE</th><th data-field="assigned" data-sortable="true">Date Assigned &#x25B4&#x25BE</th><th data-field="owner" data-sortable="true">Owner &#x25B4&#x25BE</th><th data-field="status" data-sortable="true">Status &#x25B4&#x25BE</th></tr></thead><tbody>';

	
	
	echo '<table class="table table-hover table-condensed sortable" id="table-custom-sort" data-sort-name="onid" data-sort-order="desc">';
	
	
	$query = "SELECT * FROM users ORDER BY onid ASC";
	$result = $mysqli->query($query);
	echo '<thead><tr>
	<th data-field="onid" data-sortable="true">ONID ID &#x25B4&#x25BE</th>
	<th data-field="username" data-sortable="true">Username &#x25B4&#x25BE</th>
	<th data-field="firstname" data-sortable="true">First Name &#x25B4&#x25BE</th>
	<th data-field="lastname" data-sortable="true">Last Name &#x25B4&#x25BE</th>
	<th data-field="userlevel" data-sortable="true">User Level &#x25B4&#x25BE</th>
	<th data-field="reviewrate" data-sortable="true">Review Rate &#x25B4&#x25BE</th>
	<th data-field="approvalrate" data-sortable="true">Approval Rate &#x25B4&#x25BE</th>
	</tr></thead><tbody>'; 
	while ($row = $result->fetch_assoc()){
		echo '<tr><td>' . $row['onid'] . '</td><td>' . $row['username'] . '</td><td>' . $row['firstname'] . '</td><td>' . $row['lastname'] . '</td>';
		
		if ($row['userlevel'] == 3)
			echo '<td sorttable_customkey="'.$row['userlevel'].'">Admin</td>'; 
		else if ($row['userlevel'] == 2)
			echo '<td sorttable_customkey="'.$row['userlevel'].'">Reviewer</td>'; 
		else if ($row['userlevel'] == 1)
			echo '<td sorttable_customkey="'.$row['userlevel'].'">Standard</td>'; 
		else if ($row['userlevel'] == 0)
			echo '<td sorttable_customkey="'.$row['userlevel'].'">New</td>'; 
		
		if ($row['userlevel'] ==2 OR $row['userlevel'] == 3){ //Do Review Stuff
			$query = "SELECT * FROM reviews WHERE reviewer = " . $row['id'];
			$temp = $mysqli->query($query);
			$total = 0;
			$reviewed = 0;
			$approved = 0;
			while ($temprow = $temp->fetch_assoc()){
				$total ++;
				if ($temprow['verdict'] == 1) //Approved
					$approved ++;
				if ($temprow['verdict'] != 0) //Reviewed
					$reviewed ++;
			}
			if ($reviewed == 0 OR $total == 0)
				echo '<td sorttable_customkey="1">No Data</td><td sorttable_customkey="1">No Data</td></tr>';
			else
				echo '<td  sorttable_customkey="'.number_format(($reviewed/$total)*100,1).'">'.$reviewed.'/'.$total.'=' . number_format(($reviewed/$total)*100,1) . '%</td><td sorttable_customkey="'.number_format(($approved/$reviewed)*100,1).'">'.$approved.'/'.$reviewed.'=' . number_format(($approved/$reviewed)*100,1) . '%</td></tr>';
		} else {
			echo '<td sorttable_customkey="0">-</td><td sorttable_customkey="0">-</td></tr>';
		}
	}
	echo '</tbody></table></div></div></div>';
} else {
	echo 'You do not belong here';
}
?>
</body>
</html>
