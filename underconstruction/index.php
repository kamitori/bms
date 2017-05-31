<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>JobTraq</title>
<link href="app/webroot/favicon.ico" type="image/x-icon" rel="icon">
<link href="app/webroot/favicon.ico" type="image/x-icon" rel="shortcut icon">

<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="stylesheet" href="/underlayout_files/style.css" media="all" type="text/css">
<link href="/underlayout_files/css" rel="stylesheet" type="text/css">
<script type="text/javascript" language="javascript" src="/underlayout_files/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var theDaysBox  = $("#numdays");
	var theHoursBox = $("#numhours");
	var theMinsBox  = $("#nummins");
	var theSecsBox  = $("#numsecs");
	
	var refreshId = setInterval(function() {
	  var currentSeconds = theSecsBox.text();
	  var currentMins    = theMinsBox.text();
	  var currentHours   = theHoursBox.text();
	  var currentDays    = theDaysBox.text();
	  
	  if(currentSeconds == 0 && currentMins == 0 && currentHours == 0 && currentDays == 0) {
	  	// if everything rusn out our timer is done!!
	  	// do some exciting code in here when your countdown timer finishes
	  	
	  } else if(currentSeconds == 0 && currentMins == 0 && currentHours == 0) {
	  	// if the seconds and minutes and hours run out we subtract 1 day
	  	theDaysBox.html(currentDays-1);
	  	theHoursBox.html("23");
	  	theMinsBox.html("59");
	  	theSecsBox.html("59");
	  } else if(currentSeconds == 0 && currentMins == 0) {
	  	// if the seconds and minutes run out we need to subtract 1 hour
	  	theHoursBox.html(currentHours-1);
	  	theMinsBox.html("59");
	  	theSecsBox.html("59");
	  } else if(currentSeconds == 0) {
	  	// if the seconds run out we need to subtract 1 minute
	  	theMinsBox.html(currentMins-1);
	  	theSecsBox.html("59");
	  } else {
      	theSecsBox.html(currentSeconds-1);
      }
   }, 1000);
});
</script>
</head>


<?php
	$end_time = 'Aug 1th, 2014 08:00:00';
	$end_time_int = strtotime($end_time);
	$now_time_int = time();
	$blan_time = (int)$end_time_int - (int)$now_time_int;
?>
<body>
    <div class="wrap">
		<h1 style="color:#C60">Anvydigital</h1>
        <div class="main">
            <h2>JobTraq is on progressive</h2>
            <div class="banner">
                <img src="/underlayout_files/banner.png" alt="">
            </div>
            <div class="text">
                <h3>We are working on our project and will be on-line again in:</h3>
                <div class="clock-ticker">
                    <div class="block">
                        <span class="flip-top" id="numdays"><?php echo ((int)date('d',$blan_time)-1);?></span>
                        <span class="flip-btm"></span>
                        <footer class="label">Days</footer>
                    </div>
                    <div class="block">
                        <span class="flip-top" id="numhours"><?php echo date('H',$blan_time);?></span>
                        <span class="flip-btm"></span>
                        <footer class="label">Hours</footer>
                    </div>
                    <div class="block">
                        <span class="flip-top" id="nummins"><?php echo date('i',$blan_time);?></span>
                        <span class="flip-btm"></span>
                        <footer class="label">Mins</footer>
                    </div>
                    <div class="block">
                        <span class="flip-top" id="numsecs"><?php echo date('s',$blan_time);?></span>
                        <span class="flip-btm"></span>
                        <footer class="label">Secs</footer>
                    </div>
                </div>
                
            </div>
            <div class="clear"></div>
        </div>
        <div class="footer">
            <p><a href="http://anvyinc.com/">AnvyInc</a></p>
        </div>
    </div>
</body>
</html>
