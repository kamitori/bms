<div class="logo float_left">
    <div class="company_title float_left">
        <div class="clear dent_x">
            <ul id="calendar_day_week_month" class="control_topleft float_left">
                <li><a href="javascript:void(0)" class="prev" onclick="scheduler._click.dhx_cal_prev_button();"></a></li>
                <li><a href="javascript:void(0)" onclick="scheduler.setCurrentView(scheduler._date, 'day');jobs_click_tab_active(this);">Day</a></li>
                <li><a href="javascript:void(0)" onclick="scheduler.setCurrentView(scheduler._date, 'week');jobs_click_tab_active(this);" class="active">Week</a></li>
                <li><a href="javascript:void(0)" onclick="scheduler.setCurrentView(scheduler._date, 'month');jobs_click_tab_active(this);">Month</a></li>
                <li><a href="javascript:void(0)" class="next" onclick="scheduler._click.dhx_cal_next_button();"></a></li>
            </ul>
            <ul class="control_topleft2 float_left">
                <li><a href="javascript:void(0)" onclick="scheduler._click.dhx_cal_today_button();">Today</a></li>
                <li>
                    <a href="javascript:void(0)" id="dhx_minical_icon" onclick="show_minical()" class="bor">View date</a>
                </li>
            </ul>
        </div>
        <h2>Jobs</h2>
    </div>
</div>

<script type="text/javascript">
function jobs_click_tab_active(jtobject){
    $("#calendar_day_week_month a").removeClass("active");
    $(jtobject).addClass("active");
}
</script>