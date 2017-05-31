<?php echo $this->element('menu_calendar'); ?>
<?php //echo $this->element('../Jobs/calendar_css'); ?>

<style type="text/css">
    div#jobs_calendar_month ul.list_day22 li {
        width: 13.1% !important;
    }

    .myscheduler a{
        color: #00f;
    }

    .dhx_after .dhx_month_body, .dhx_before .dhx_month_body {
        background-color: #ECECEC;
    }
    .dhx_after .dhx_month_head, .dhx_before .dhx_month_head {
        background-color: #E2E3E6;
        color: #94A6BB;
    }
    .clear_tophead2_nomargin {
        padding-bottom: 26px;
    }
    .fix_heili_old2 {
        background-color:#fff !important;
    }
</style>

<div id="content">
    <div class="top_header_inner2 fx_ul5 ul_res2" style="position: fixed;">
        <ul class="list_day22" style="margin-left: -1px;">
            <li class="child_bor percent_li"><?php echo date( 'D, F d', $date_from_sec ); ?></li>
            <li class="percent_li"><?php echo date( 'D, F d', $date_from_sec + DAY ); ?></li>
            <li class="percent_li"><?php echo date( 'D, F d', $date_from_sec + DAY*2 ); ?></li>
            <li class="percent_li"><?php echo date( 'D, F d', $date_from_sec + DAY*3 ); ?></li>
            <li class="percent_li"><?php echo date( 'D, F d', $date_from_sec + DAY*4 ); ?></li>
            <li class="percent_li"><?php echo date( 'D, F d', $date_from_sec + DAY*5 ); ?></li>
            <li class="bor_mt percent_li"><?php echo date( 'D, F d', $date_from_sec + DAY*6 ); ?></li>
        </ul>
        <p class="clear"></p>
    </div><!--END top_header_inner -->
    <div id="jobs_calendar_day" class="w_ul2 ul_res2">
        <ul class="ul_mag clear bg top_header_inner2 ul_res2">
            <li class="hg_padd" style="width:.5%"></li>
            <li class="hg_padd">Job no</li>
            <li class="hg_padd center_txt" style="width:15%">Job name</li>
            <li class="hg_padd" style="width:10%">Customer</li>
            <li class="hg_padd" style="width:5%">Job type</li>
            <li class="hg_padd" style="width:15%">Job manager</li>
            <li class="hg_padd" style="width:6%">Start</li>
            <li class="hg_padd" style="width:6%">Finish</li>
            <li class="hg_padd" style="width:5%">Status</li>
            <li class="hg_padd" style="width:10%">Stages</li>
            <li class="hg_padd bor_mt" style="width:10%">Tasks</li>
        </ul>

        <?php
        $i = 1;
        foreach ($arr_jobs as $key => $value) {
            $i = 3 - $i;
        ?>
        <ul class="ul_mag clear indent_ul_top bg<?php echo $i; ?>">
            <li class="hg_padd" style="width:.5%"><span class="icon_emp"></span></li>
            <li class="hg_padd"><?php echo $value['no']; ?></li>
            <li class="hg_padd center_txt" style="width:15%"><?php echo $value['name']; ?></li>
            <li class="hg_padd" style="width:10%"></li>
            <li class="hg_padd" style="width:5%"><?php echo $arr_jobs_type[$value['type']]; ?></li>
            <li class="hg_padd" style="width:15%"></li>
            <li class="hg_padd" style="width:6%"><?php echo $this->Common->format_date( $value['work_start']->sec, false); ?></li>
            <li class="hg_padd" style="width:6%"><?php echo $this->Common->format_date( $value['work_end']->sec, false); ?></li>
            <li class="hg_padd" style="width:5%"><?php echo $arr_jobs_status[$value['status']]; ?></li>
            <li class="hg_padd" style="width:10%"></li>
            <li class="hg_padd bor_mt" style="width:10%"></li>
        </ul>
        <?php } ?>

    </div>
</div>