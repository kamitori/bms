<div id="show_users" style="overflow-y: hidden;height: 96%;">
<?php
	foreach($arr_online_contacts as $contact_id=>$contact){
?>
	<li onclick="open_chatbox('<?php echo $contact_id; ?>','<?php echo $contact['user_name'] ?>')" id="user_<?php echo $contact_id; ?>" class="online">
		<span><?php echo $contact['user_name'] ?></span>
	</li>
<?php
	}
?>
<?php
	foreach($arr_contacts as $contact){
?>
	<li onclick="open_chatbox('<?php echo $contact['_id']; ?>','<?php echo $contact['full_name'] ?>')" id="user_<?php echo $contact['_id']; ?>"><span><?php echo $contact['full_name'] ?></span></li>
<?php
	}
?>
</div>
<script>
	$(function(){
		$("#show_users").mCustomScrollbar({
			scrollButtons:{
				enable:false
			},
			autoHideScrollbar : true,
		});
	});
</script>