<ul class="navleft_footer float_right">
	<li>
		<a href="<?php echo URL;?>/" <?php if($controller == 'homes' && $action != 'dashboard'){ ?>class="active"<?php } ?>><?php echo translate('Home'); ?></a>
	</li>

	<?php if( $this->Common->check_permission('calendar_@_entry_@_view', $arr_permission) ){ ?>
	<li>
		<a href="<?php echo URL;?>/calendars" <?php if($controller == 'calendars'){ ?>class="active"<?php } ?>><?php echo translate('Calendar'); ?></a>
	</li>
	<?php } ?>

	<?php if( $this->Common->check_permission('dashboard_@_entry_@_view', $arr_permission) ){ ?>
	<li>
		<a href="<?php echo URL;?>/homes/dashboard" <?php if($action == 'dashboard'){ ?>class="active"<?php } ?>>
			<?php echo translate('Dashboard'); ?>
		</a>
	</li>
	<?php } ?>

	<li title="Alerts for you">
		<a id="alerts_footer" href="javascript:void(0)" <?php if($controller == 'alerts'){ ?>class="active"<?php } ?>>
			<span id="alerts_title"><?php echo translate('Alerts'); ?><span id="alerts_num"></span></span>
		</a>
	</li>
	<li>
		<a href="<?php echo URL.'/contacts/entry/'.(isset($_SESSION['arr_user']['_id']) ? $_SESSION['arr_user']['_id'] : ''); ?>#workings_holidays"><?php echo (isset($_SESSION['arr_user']['full_name']) ? $_SESSION['arr_user']['full_name'] : ''); ?></a>
	</li>
	<li title="Chat">
		<a id="chat" href="javascript:void(0)" >
			<span id="chat"><?php echo translate('Chat'); ?></span>
		</a>
	</li>
</ul>