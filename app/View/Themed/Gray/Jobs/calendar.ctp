<?php echo $this->element('menu_calendar'); ?>
<?php echo $this->element('../Jobs/calendar_css'); ?>

<div id="calendar-left">
    <ul id="have_data" class="list_day_rs">
        <?php
        foreach ($arr_contact as $key => $value) { ?>
            <li id="<?php echo $key; ?>" onclick="jobs_calendar_onchange_type(this, '<?php echo $key; ?>')">
                <?php echo $value; ?>
            </li>
        <?php } ?>
    </ul>

    <ul id="no_data" class="list_day_rs" style="display:none">
        <li><?php echo translate('There are no resources is used'); ?></li>
    </ul>
</div>

<div id="calendar-right">
    <div class="top_header_inner2 fx_ul5 " style="position: absolute;">
        <ul class="dent_top list_day22">
            <li class="fix_width"><?php echo date( 'D, F d', $date_from_sec ); ?></li>
            <li class="fix_width"><?php echo date( 'D, F d', $date_from_sec + DAY ); ?></li>
            <li class="fix_width"><?php echo date( 'D, F d', $date_from_sec + DAY*2 ); ?></li>
            <li class="fix_width"><?php echo date( 'D, F d', $date_from_sec + DAY*3 ); ?></li>
            <li class="fix_width"><?php echo date( 'D, F d', $date_from_sec + DAY*4 ); ?></li>
            <li class="fix_width"><?php echo date( 'D, F d', $date_from_sec + DAY*5 ); ?></li>
            <li class="fix_width bor_mt"><?php echo date( 'D, F d', $date_from_sec + DAY*6 ); ?></li>
        </ul>
        <p class="clear"></p>
    </div><!--END top_header_inner -->
    <div id="content_of_calendar" class="" style="height:80%; overflow-y: auto;overflow-x: hidden;padding-top: 21px;">

        <?php

        $check_in_current_week = false;
        $time_current = strtotime('now');
        if( $date_from_sec < $time_current && $time_current < $date_to_sec )$check_in_current_week = true;

        $row = 100;
        for ($i=0; $i < $row; $i++) {

            $arr_job = array();
            if (isset($arr_jobs[$i])) {
                $arr_job = $arr_jobs[$i];
            }
            // if ($arr_jobs->count() > 0) {
            //     $arr_jobs->next();
            //     $arr_job = $arr_jobs->current();
            // }

            if( empty($arr_job) ){

                if( $i < 24 ){
                    $row = 24;
                }else{
                    break;
                }
            }
         ?>
        <ul class="list_rs">

            <?php for ($j=0; $j < 7; $j++) {  ?>

            <li class="<?php if( $j == 0 ){ ?>child_bor<?php } ?> <?php if( $check_in_current_week && date('N') == ($j+1) ){ ?>active<?php } ?>">

                <?php
                if( !empty($arr_job) ){
                    $time_current_day = $date_from_sec+$j*DAY;
                    if( $time_current_day >= $arr_job['work_start']->sec && $time_current_day <= $arr_job['work_end']->sec ){ ?>
                    <div class="box_3 <?php echo $arr_job['status']; ?> <?php echo $arr_job['contacts'][$arr_job['contacts_default_key']]['contact_id']; ?>" ondblclick="jobs_go_to_entry('<?php echo $arr_job['_id']; ?>')">
                        <span class="title_nv">
                            <h5><?php echo $arr_job['name']; ?></h5>
                        </span>
                        <p><?php
                            if( trim($arr_job['company_name']) != '' ){
                                echo $arr_job['company_name'];

                            }else{
                                if( isset($arr_jobs_type[$arr_job['type']]) && isset($arr_jobs_status[$arr_job['status']]) )
                                    echo $arr_jobs_type[$arr_job['type']] . ' | '. $arr_jobs_status[$arr_job['status']];
                            }
                        ?></p>
                    </div>
                <?php }
                } ?>

            </li>

            <?php }  ?>

        </ul>

        <?php } ?>

    </div>
    <p class="clear_height"></p>
</div>

<div style="clear:both;"></div>
<script type="text/javascript">
    function jobs_go_to_entry(id){
        location.href = "<?php echo URL; ?>/jobs/entry/" + id;
    }

    function jobs_calendar_onchange_status(){
        var contain = $("#calendar-right");

        if( $.trim($("#JobStatusFilter").val()).length < 1 ){
            $("li div", contain).show();
        }else{
            $("li div", contain).hide();
            $("li div." + $("#JobStatusFilter").val(), contain).show();
        }

    }

    function jobs_calendar_onchange_type(object, item_id) {

        if($(object).hasClass("active")){
            $("li", "#calendar-left").removeClass("active");
            $("div", "#content_of_calendar").show();

        }else{
            $("li", "#calendar-left").removeClass("active");
            $(object).addClass("active");

            $("div", "#content_of_calendar").hide();
            $("." + item_id).show();
        }
    }
</script>