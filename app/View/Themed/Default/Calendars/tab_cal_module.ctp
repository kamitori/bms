<ul class="menu_control2 float_right">
	<li><a href="<?php echo URL; ?>/salesorders/calendar" class="<?php if($controller=='salesorders'){ ?>active<?php } ?>">Sales Orders</a></li>
    <li><a href="<?php echo URL; ?>/resources/calendar" class="<?php if($controller=='resources'){ ?>active<?php } ?>">Resources</a></li>
    <li><a onclick="show_warning_function_in_progress_develop(this); return false;" href="javascript:void(0)">Jobs</a></li>
    <li><a onclick="show_warning_function_in_progress_develop(this); return false;" href="javascript:void(0)">Stages</a></li>
    <li><a onclick="show_warning_function_in_progress_develop(this); return false;" href="javascript:void(0)">Tasks</a></li>
    <li><a onclick="show_warning_function_in_progress_develop(this); return false;" href="javascript:void(0)">TimeLog</a></li>
    <li><a onclick="show_warning_function_in_progress_develop(this); return false;" href="javascript:void(0)">Comms</a></li>
</ul>
<!-- FORCE REFESH  -->
<?php //echo $this->element('force_refesh'); ?>
<!-- END === -->