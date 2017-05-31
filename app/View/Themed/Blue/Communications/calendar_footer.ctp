<div id="footer">
    <div class="bg_footer footer_res5">
        <ul class="navleft_footer float_right">
            <li><a href="<?php echo URL;?>/">Home</a></li>
            <li><a href="<?php echo URL;?>/calendars" class="active">Calendar</a></li>
            <li><a onclick="show_warning_function_in_progress_develop(this); return false;" href="<?php echo URL;?>/" <?php if($controller == 'dashboards'){ ?>class="active"<?php } ?>>Dashboard</a></li>
            <li><a onclick="show_warning_function_in_progress_develop(this); return false;" href="<?php echo URL;?>/" <?php if($controller == 'alerts'){ ?>class="active"<?php } ?>>Alerts</a></li>
        </ul>

    </div>
</div>