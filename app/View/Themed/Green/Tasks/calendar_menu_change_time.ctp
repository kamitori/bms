<div class="logo float_left">
    <div class="company_title float_left">
        <div class="clear dent_x">
            <ul id="calendar_day_week_month" class="control_topleft float_left">
                <li><a href="javascript:void(0)" class="prev" onclick="scheduler._click.dhx_cal_prev_button();calendar_get_date();"></a></li>
                <li><a href="javascript:void(0)" onclick="tasks_click_calendar_active(this);scheduler.setCurrentView(scheduler._date, 'day');calendar_get_date();">Day</a></li>
                <li><a href="javascript:void(0)" onclick="tasks_click_calendar_active(this);scheduler.setCurrentView(scheduler._date, 'week');calendar_get_date();" class="active">Week</a></li>
                <li><a href="javascript:void(0)" onclick="tasks_click_calendar_active(this);scheduler.setCurrentView(scheduler._date, 'month');calendar_get_date();">Month</a></li>
                <li><a href="javascript:void(0)" class="next" onclick="scheduler._click.dhx_cal_next_button();calendar_get_date();"></a></li>
            </ul>
            <ul id="calendars_ul_select_date" class="control_topleft2 float_left">
                <li><a href="javascript:void(0)" onclick="scheduler._click.dhx_cal_today_button();">Today</a></li>
                <li>
                    <a href="javascript:void(0)" id="dhx_minical_icon" onclick="show_minical()" class="bor">View date</a>
                </li>
            </ul>
        </div>
        <h2>Tasks</h2>
    </div>
</div>

<!-- dung de kiem tra xem 1 request ajax len server lay du lieu co xong chua moi chay den cai khac -->
<input type="hidden" id="tasks_calendar_load_finish" value="1">
<input type="hidden" id="tasks_calendar_date_from" value="">
<input type="hidden" id="tasks_calendar_date_to" value="">

<script type="text/javascript">
function <?php echo $controller; ?>_click_calendar_active(jtobject){
    $("#calendar_day_week_month a").removeClass("active");
    $(jtobject).addClass("active");

    if($(jtobject).text() == "Month"){
        $("#calendars_ul_select_date").hide();
    }else{
        $("#calendars_ul_select_date").show();
    }
}

</script>