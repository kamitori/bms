<?php echo $this->element('menu_calendar'); ?>
<?php //echo $this->element('../Stages/calendar_css'); ?>

<style type="text/css">
    div#stages_calendar_month ul.list_day22 li {
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
    <div id="stages_calendar_day" class="w_ul2 ul_res2">
        <ul class="ul_mag clear bg top_header_inner2 ul_res2">
            <li class="hg_padd" style="width:.5%"></li>
            <li class="hg_padd" style="width:14%"><?php echo translate('Stage'); ?></li>
            <li class="hg_padd center_txt" style="width:10%"><?php echo translate('Responsible'); ?></li>
            <li class="hg_padd" style="width:10%"><?php echo translate('Start'); ?></li>
            <li class="hg_padd" style="width:5%"><?php echo translate('Finish'); ?></li>
            <li class="hg_padd" style="width:4%"><?php echo translate('Day left'); ?></li>
            <li class="hg_padd" style="width:6%"><?php echo translate('Status'); ?></li>
            <li class="hg_padd" style="width:6%"><?php echo translate('Tasks'); ?></li>
            <li class="hg_padd bor_mt" style="width:33%"><?php echo translate('Job name'); ?></li>
        </ul>

        <?php
        $i = 1;
        foreach ($arr_stages as $key => $value) {
            $i = 3 - $i;
        ?>
        <ul class="ul_mag clear indent_ul_top bg<?php echo $i; ?>">
            <li class="hg_padd" style="width:.5%">
                <a href="<?php echo URL; ?>/stages/entry/<?php echo $value['_id'];?>">
                    <span class="icon_emp"></span>
                </a>
            </li>
            <li class="hg_padd" style="width:14%"><?php echo $value['stage']; ?></li>
            <li class="hg_padd" style="width:10%"><?php echo $value['our_rep']; ?></li>
            <li class="hg_padd" style="width:10%"><?php echo $this->Common->format_date( $value['work_start']->sec, false); ?></li>
            <li class="hg_padd" style="width:5%"><?php echo $this->Common->format_date( $value['work_end']->sec, false); ?></li>
            <li class="hg_padd" style="width:4%">
                <?php
                    $work_end_sec = $value['work_end']->sec;
                    $day_left = (strtotime(date('Y-m-d')) - $work_end_sec)/DAY;
                    echo $day_left;
                ?>
            </li>
            <li class="hg_padd" style="width:6%"><?php echo $value['status']; ?></li>
            <li class="hg_padd" style="width:6%"></li>
            <li class="hg_padd bor_mt" style="width:33%"><?php echo $value['job']; ?></li>
        </ul>
        <?php } ?>

    </div>
</div>