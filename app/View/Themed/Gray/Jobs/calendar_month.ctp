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
    #jobs_calendar_month span a:link, #jobs_calendar_month span a {
        color: blue !important;
    }
    #jobs_calendar_month span{
        text-align: right;
        clear: both;
        display: block;
        width: 100%;
    }
    #jobs_calendar_month .ChangeColor span a:link, #jobs_calendar_month .ChangeColor span a{
        color: #9E9E9E !important;
    }
    .ChangeColor{
        background-color: #F7F7F7 !important;
    }
</style>

<div id="content">
    <div class="top_header_inner2 fx_ul5 ul_res2" style="position: fixed;">
        <ul class="list_day22" style="margin-left: -1px;">
            <li class="child_bor percent_li">Monday</li>
            <li class="percent_li">Tuesday</li>
            <li class="percent_li">Wednesday</li>
            <li class="percent_li">Thursday</li>
            <li class="percent_li">Friday</li>
            <li class="percent_li">Saturday</li>
            <li class="bor_mt percent_li">Sunday</li>
        </ul>
        <p class="clear"></p>
    </div><!--END top_header_inner -->
    <div id="jobs_calendar_month" class="clear_tophead2_nomargin ul_res2">
        <ul class="list_day22">
            <?php

            $current_date_view = $date_from_sec + DAY*27;

            $date_sec = $date_from_sec;
            for ($i=0; $i < 42; $i++) {

                $current_date_sec = $date_from_sec + DAY*$i;
            ?>
                <li class="fix_heili_old2 <?php if( date('m', $current_date_sec) != date('m', $current_date_view) ){ ?>ChangeColor<?php } ?>">
                    <span><a href="<?php echo URL; ?>/jobs/calendar_day/<?php echo $date_from_sec; ?>"><?php echo date('d', $date_sec); ?></a></span>
                    <?php
                    foreach ($arr_jobs as $key => $value) {

                        if( $value['work_start']->sec <= $current_date_sec &&  $value['work_end']->sec >= $current_date_sec ){
                    ?>
                            <p><a href="<?php echo URL; ?>/jobs/entry/<?php echo $value['_id']; ?>"><?php echo $value['no'] . ': '.$value['name']; ?></a></p>
                    <?php
                        }
                    } ?>
                </li>
            <?php
                $date_sec += DAY;
            } ?>

        </ul>
    </div><!--END Content -->
</div>