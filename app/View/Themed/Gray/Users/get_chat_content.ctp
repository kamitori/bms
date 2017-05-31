<?php  foreach($arr_message as $key=>$message){
	$message = (array)$message;
	?>
<div class="shout_msg">
	<time><?php echo $message['time'] ?></time>
	<span class="username"><?php echo $message['name']; ?></span>
	<span class="message"><?php echo $message['message']; ?></span>
	<span class="hidden key"><?php echo $key; ?></span>
</div>
<?php } ?>