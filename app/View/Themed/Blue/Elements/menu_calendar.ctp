<div class="menu">
    <div class="bg_menu bg_menu_res">
        <?php if($controller == 'resources'){ ?>
        <ul class="menu_control2 float_left" id="resources_check_tab_active">
            <li>
                <a href="<?php echo URL; ?>/resources/calendar/Contact" <?php if($type=="Contact"){ ?>class="active"<?php } ?>>Contacts</a>
            </li>
            <li>
                <a href="<?php echo URL; ?>/resources/calendar/Equipment" <?php if($type=="Equipment"){ ?>class="active"<?php } ?>>Assets</a>
            </li>
        </ul>
        <?php }elseif($controller == 'salesorders'){ ?>
        <ul class="menu_control2 float_left" id="salesorders_check_tab_active">
            <li>
                <a href="<?php echo URL; ?>/salesorders/calendar/contacts" <?php if($type=="contacts"){ ?>class="active"<?php } ?>>Contacts</a>
            </li>
            <li>
                <a href="<?php echo URL; ?>/salesorders/calendar/assets" <?php if($type=="assets"){ ?>class="active"<?php } ?>>Assets</a>
            </li>
        </ul>
        <?php }elseif($controller == 'salesorderdds'){ ?>
        <ul class="menu_control2 float_left" id="salesorderdds_check_tab_active">
            <li>
                <a href="javascript:void(0)" class="active">Our Rep</a>
            </li>
        </ul>
        <?php }elseif($controller == 'tasks'){ ?>
        <ul class="menu_control2 float_left" id="tasks_check_tab_active">
            <li>
                <a href="<?php echo URL; ?>/tasks/calendar/contacts" <?php if($type=="contacts"){ ?>class="active"<?php } ?>>Contacts</a>
            </li>
            <li>
                <a href="<?php echo URL; ?>/tasks/calendar/assets" <?php if($type=="assets"){ ?>class="active"<?php } ?>>Assets</a>
            </li>
        </ul>
        <?php }elseif($controller == 'jobs' && $action == 'calendar' ){ ?>
        <ul class="menu_control2 float_left" id="jobs_check_tab_active">
            <li>
                <a href="javascript:void(0)" class="active">Manager</a>
            </li>
        </ul>
        <?php }elseif($controller == 'stages' && $action == 'calendar' ){ ?>
        <ul class="menu_control2 float_left" id="jobs_check_tab_active">
            <li>
                <a href="javascript:void(0)" class="active">Responsible</a>
            </li>
        </ul>
        <?php } ?>
        <!-- <ul class="menu_control float_left">
            <li><a href="#">New</a></li>
            <li><a href="#">Find</a></li>
        </ul> -->
        <?php if(substr($action, 0, 8) == 'calendar'){ ?>
        <ul class="menu_control2 float_right">
            <!-- <li><a href="<?php echo URL; ?>/resources/calendar" class="<?php if($controller=='resources'){ ?>active<?php } ?>">Resources</a></li> -->
            <li><a href="<?php echo URL; ?>/tasks/calendar" class="<?php if($controller=='tasks'){ ?>active<?php } ?>">Tasks</a></li>
            <li><a href="<?php echo URL; ?>/jobs/calendar" class="<?php if($controller=='jobs'){ ?>active<?php } ?>">Jobs</a></li>
            <li><a href="<?php echo URL; ?>/salesorders/calendar" class="<?php if($controller=='salesorders'){ ?>active<?php } ?>">Sales Orders</a></li>
            <li><a href="<?php echo URL; ?>/salesorderdds/calendar" class="<?php if($controller=='salesorderdds'){ ?>active<?php } ?>">SO Due Date</a></li>
            <!-- <li><a href="<?php echo URL; ?>/stages/calendar" class="<?php if($controller=='stages'){ ?>active<?php } ?>">Stages</a></li> -->
            <!-- <li><a href="<?php echo URL; ?>/timelogs/calendar" class="<?php if($controller=='timelogs'){ ?>active<?php } ?>">TimeLog</a></li> -->
            <!-- <li><a href="<?php echo URL; ?>/communications/calendar" class="<?php if($controller=='communications'){ ?>active<?php } ?>">Comms</a></li> -->
        </ul>
        <?php } ?>
    </div>
</div>
<!-- FORCE REFESH  -->
<?php //echo $this->element('force_refesh'); ?>