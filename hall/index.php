<?php
include_once '../dbconnect.php';
include_once '../phpfunctions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo '<!DOCTYPE html>
	<html>
	<head>
	<meta http-equiv="refresh" content="600">
	<title>Collaboratory Achievement Management</title>
	</head>
	<body>';

$roomid = 1;
?>

<input type="hidden" value="<?php echo $roomid;?>" id="roomid" name="roomid"></input>

<!-- Bootstrap -->
<link href="../css/bootstrap.min.css" rel="stylesheet">
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins -->
<script src="../js/bootstrap.min.js"></script>

<script>
$(document).ready(setInterval(function() {
	var dt = new Date();
	var mins = (dt.getMinutes() + (60 * dt.getHours())) % 22;
	if (mins == 0){
	var room = $('#roomid').val();
	$.ajax({
		type: 'GET',
		url: '../ajax/hall.php',
		dataType: 'html',
		data: {room: room},
		success: function(result)
			{
			
			}
	});	
	}
}, 1000 * 60));
</script>

<style>
.fadein { 
position:relative; margin-left:auto; margin-right:auto;

 }
.fadein img { position:absolute; top:10px; }
</style>

<script>
$(function(){
	$('.fadein img:gt(0)').hide();
		// last element of next line controls the delay of the pictures
	setInterval(function(){$('.fadein :first-child').fadeOut().next('img').fadeIn().end().appendTo('.fadein');}, 5000);
});

$(function(){
	
		setInterval(function(){
			if (hostReachable() != false){
				location.reload(true);
			}
			}, 1000 * 60 * 5);
});

function hostReachable() {

  // Handle IE and more capable browsers
  var xhr = new ( window.ActiveXObject || XMLHttpRequest )( "Microsoft.XMLHTTP" );
  var status;

  // Open new request as a HEAD to the root hostname with a random param to bust the cache
  xhr.open( "HEAD", "//" + window.location.hostname + "/?rand=" + Math.floor((1 + Math.random()) * 0x10000), false );

  // Issue request and handle response
  try {
    xhr.send();
    return ( xhr.status >= 200 && xhr.status < 300 || xhr.status === 304 );
  } catch (error) {
    return false;
  }

}

</script>


<?php
$calendar = '

	<div class="panel panel-default" style="width:100%;">
	<div class="panel-heading">Who\'s Available</div>
	<table class="table">';
	
$query = "SELECT users.username, rooms.name, users.id FROM clock INNER JOIN users ON users.id = clock.userid INNER JOIN rooms ON rooms.id = clock.roomid WHERE clock.roomid = $roomid AND timeout = '0000-00-00 00:00:00'";
$clockRes = $mysqli->query($query);
while ($clockRow = $clockRes->fetch_array(MYSQLI_ASSOC)){
	$query = "SELECT levels.image, levels.level, achievementList.name FROM achievements INNER JOIN achievementList ON achievementList.id = achievements.achievementid INNER JOIN levels ON levels.id = achievements.levelid WHERE userid='" . $clockRow['id']  . "' ORDER BY levels.level ASC LIMIT 10";
	$imageresult = $mysqli->query($query);
	//echo $query . '<BR>';
	$calendar .=   '<tr><td>' . $clockRow['username'] . ': ';
	while ($imagerow = $imageresult->fetch_assoc())
		$calendar .=   '<img src="../img/' . $imagerow['image'] . '" style="height:3em;">';
	$calendar .=   '</tr></td>';
} 
$calendar .=  '</table></div>
	<iframe src="https://www.google.com/calendar/embed?showTitle=0&amp;showNav=0&amp;showDate=0&amp;showPrint=0&amp;showTabs=0&amp;showCalendars=0&amp;showTz=0&amp;mode=WEEK&amp;height=600&amp;wkst=1&amp;bgcolor=%23FFFFFF&amp;src=oregonstate.edu_b87rj2po68mq8srlr972fcbbd4%40group.calendar.google.com&amp;color=%2323164E&amp;ctz=America%2FLos_Angeles" style=" border-width:0 " width="100%" height="500px" frameborder="0" scrolling="no"></iframe>
	<h1>KEC1119 Mastery Challenge Lounge</h1>Last Updated: ' . date("l",time()) . ' at ' . date("g:ia",time());

$information = '
<div class="alert">
<h4><p>Welcome to the Mastery Challenge Lounge. This lounge is dedicated to helping anyone interested in doing cool projects to learn new skills and create amazing things. The volunteers in this room are here to help you with things like electrical design, microcontroller programming, PCB design, 3D Printing, CAD Modelling, Laser Cutting, PHP, Python, and much more!</p>
<p>To make the most of this room, join our on-line Mastery Challenge by either scanning this QR code or visiting the website below. Login and sign-up and pick your challenge. Prizes are awarded each term for people completing challenges!</p>
<ul>
<li>Winter 2016: $150 Cash Prize to the most achievements this term</li>
<li>Winter 2016: $150 Cash Prize for the most level 2 and higher achievements this term</li>
<li>Winter 2016: If you earn even one achievement this term, you will be entered into a drawing for a $50 prize.</li>
</ul>
</div>
<div class="row">
<div class="col-xs-6" >
<img src="./masteryqr.jpg" class="img-responsive">
<BR>http://eecs.oregonstate.edu/education/achievements</h4>
</div>
<div class="col-xs-6 fadein">';


    $array = array();

    $i = 0; 
    $dir = 'kec1119img/';
    if ($handle = opendir($dir)) {
	//put the names of the images in the folder into an array
        while (($file = readdir($handle)) !== false){
            if (!in_array($file, array('.', '..')) && !is_dir($dir.$file)) {
                $i++;
		  array_push($array, $file);
	     }
        }
    }

	for($pic = 0; $pic < $i; $pic += 1){
		$information .= '<img class="img-rounded img-responsive" style="width:100%;" src="' . $dir . $array[$pic] . '">';
        }

	$information .= '</div></div>';


?>

<div class="wrapper container-fluid">
<div class="row">
<?php
if (intval(date("h",time()))%2 != 0){
	echo '<div class="col-xs-5 col-xs-offset-1" style="padding-top:3em;">';
	echo $information;
	echo '</div>';
	echo '<div class="col-xs-5" style="padding-top:3em;">';
	echo $calendar;
	echo '</div>';
} else{
	echo '<div class="col-xs-5 col-xs-offset-1" style="padding-top:3em;">';
	echo $calendar;
	echo '</div>';
	echo '<div class="col-xs-5" style="padding-top:3em;">';
	echo $information ;
	echo '</div>';
	
}
?>
</div>
</div>
</body>
</html>
