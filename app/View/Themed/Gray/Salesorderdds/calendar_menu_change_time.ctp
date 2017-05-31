<div class="logo float_left">
    <div class="company_title float_left">
        <div class="clear dent_x">
            <ul id="calendar_day_week_month" class="control_topleft float_left">
                <li><a href="<?php echo URL; ?>/salesorderdds/<?php echo $action; ?>/<?php echo $arr_time_sec['prev'][0].'/'.$arr_time_sec['prev'][1] ;?>" class="prev"></a></li>
                <li><a href="<?php echo URL; ?>/salesorderdds/calendar_day" <?php if($action == 'calendar_day'){ ?>class="active"<?php } ?>><?php echo translate('Day'); ?></a></li>
                <li><a href="<?php echo URL; ?>/salesorderdds/calendar" <?php if($action == 'calendar'){ ?>class="active"<?php } ?>><?php echo translate('Week'); ?></a></li>
                <li><a href="<?php echo URL; ?>/salesorderdds/calendar_month" <?php if($action == 'calendar_month'){ ?>class="active"<?php } ?>><?php echo translate('Month'); ?></a></li>
                <li><a href="<?php echo URL; ?>/salesorderdds/<?php echo $action; ?>/<?php echo $arr_time_sec['next'][0].'/'.$arr_time_sec['next'][1] ;?>" class="next" ></a></li>
            </ul>
            <ul id="calendars_ul_select_date" class="control_topleft2 float_left">
                <li><a href="<?php echo URL; ?>/salesorderdds/<?php echo $action; ?>"><?php echo translate('Today'); ?></a></li>
                <!-- <li>
                    <a href="javascript:void(0)" id="dhx_minical_icon" onclick="show_minical()" class="bor">View date</a>
                </li> -->
            </ul>
        </div>
        <h2>Salesorders
            <?php if(isset($current_view_date)){
                    echo ': ' . date('D', strtotime(date($current_view_date))) .', '. $this->Common->format_date( strtotime(date($current_view_date)), false);
                }elseif(isset($date_from_sec)){
                    echo ': ' . date('D', $date_from_sec) .', '. $this->Common->format_date( $date_from_sec, false);
                } ?>
        </h2>
    </div>
</div>