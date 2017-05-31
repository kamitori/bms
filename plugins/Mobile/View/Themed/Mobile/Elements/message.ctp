<?php
	if (CakeSession::check('message_success')){
		echo '<div class="message-success">' . CakeSession::read('message_success') . '</div>';
		CakeSession::delete('message_success');
	}
	if (CakeSession::check('message_error')){
		echo '<div class="message-error">' . CakeSession::read('message_error') . '</div>';
		CakeSession::delete('message_error');
	}
?>
