<!-- DIV 3 -->
<div class="clear_percent">
    <div class="tab_1 full_width">
        <span class="title_block bo_ra1">
            <span class="fl_dent"><h4><?php echo translate('Details'); ?></h4></span>
            <div class="float_left hbox_form dent_left_form">
                <input id="click_open_window_contacts_resources" class="btn_pur size_width2" type="button" value="New contact">
                <input id="click_open_window_equipments" class="btn_pur size_width2" type="button" value="New asset">
            </div>
        </span>
        <p class="clear"></p>

        <ul class="ul_mag clear bg3">
            <li class="hg_padd" style="width:1.5%"></li>
            <li class="hg_padd" style="width:10%"><?php echo translate('Type'); ?></li>
            <li class="hg_padd" style="width:30%"><?php echo translate('Name'); ?></li>
            <li class="hg_padd center_txt" style="width:11%"><?php echo translate('Start (schedule)'); ?></li>
            <li class="hg_padd center_txt" style="width:11%"><?php echo translate('End (schedule)'); ?></li>
            <li class="hg_padd center_txt" style="width:6%"><?php echo translate('Status'); ?></li>
            <li class="hg_padd center_txt" style="width:6%"></li>
        </ul>
        <?php
        for ($i=0; $i < 24; $i++) {
            $j = $i;
            if($j < 10)$j = '0'.$j;
            if($i > 7 && $i < 18){
                $arr_hour[$j.':00'] = array('name'=> $j.':00', 'value'=> $j.':00', 'class'=>'BgOptionHour');
                $arr_hour[$j.':30'] = array('name'=> $j.':30', 'value'=> $j.':30', 'class'=>'BgOptionHour');
            }else{
                $arr_hour[$j.':00'] = $j.':00';
                $arr_hour[$j.':30'] = $j.':30';
            }
        }

        $i = 1; $k = 0; $count = 0;
        foreach( $arr_task as $value ){
            // if( $value['type'] != 'Equipment' )continue;
            $i = 3 - $i; $k++; $count += 1;
        ?>
            <?php echo $this->Form->create('Resource', array('id' => 'ResourceEntryForm_'.$k)); ?>
                <?php echo $this->Form->hidden('Resource.'.$k.'._id', array(
                            'value' => $value['_id'],
                            'class' => 'ParentForm'
                    )); ?>
            <ul class="ul_mag clear bg<?php echo $i; ?>" id="Resource_<?php echo $value['_id']; ?>">
                <li class="hg_padd" style="width:1.5%"></li>
                <li class="hg_padd" style="width:10%"><?php echo $value['type']; ?></li>
                <li class="hg_padd" style="width:30%"><?php echo $value['name']; ?></li>
                <li class="hg_padd center_txt" style="width:11%">
                    <div class="date">
                        <span class="float_left" style="width: 47%;">
                            <?php echo $this->Form->input('Resource.'.$k.'.work_start', array(
                                    'class' => 'JtSelectDate input_inner input_inner_w bg'.$i,
                                    'style' => 'width: 70px',
                                    'value' => date('m/d/Y', $value['work_start']->sec)
                            )); ?>
                        </span>
                    </div>

                    <div class="select_inner width_select" style="width: 41%; margin: 0;">
                        <div class="styled_select" style="margin: 0;">
                            <?php echo $this->Form->input('Resource.'.$k.'.work_start_hour', array(
                                        'class' => '',
                                        'options' => $arr_hour,
                                        'value' => date('H:i', $value['work_start']->sec)
                                )); ?>
                        </div>
                    </div>

                </li>
                <li class="hg_padd center_txt" style="width:11%">
                    <div class="date">
                        <span class="float_left" style="width: 47%;">
                            <?php echo $this->Form->input('Resource.'.$k.'.work_end', array(
                                    'class' => 'JtSelectDate input_inner input_inner_w bg'.$i,
                                    'style' => 'width: 70px',
                                    'value' => date('m/d/Y', $value['work_end']->sec)
                            )); ?>
                        </span>
                    </div>

                    <div class="select_inner width_select" style="width: 41%; margin: 0;">
                        <div class="styled_select" style="margin: 0;">
                            <?php echo $this->Form->input('Resource.'.$k.'.work_end_hour', array(
                                    'class' => '',
                                    'options' => $arr_hour,
                                    'value' => date('H:i', $value['work_end']->sec)
                            )); ?>
                        </div>
                    </div>
                </li>
                <li class="hg_padd center_txt" style="width:6%">
                     <div class="select_inner width_select" style="width: 100%; margin: 0;">
                        <div class="styled_select" style="margin: 0;">
                            <?php echo $this->Form->input('Resource.'.$k.'.status', array(
                                    'class' => ' bg'.$i,
                                    'options' => $arr_equipments_status,
                                    'value' => $value['status']
                            )); ?>
                        </div>
                    </div>
                </li>
                <li class="hg_padd center_txt" style="width:6%">
                    <div class="middle_check">
                        <a href="javascript:void(0)" onclick="salesorders_resources_delete('<?php echo $value['_id']; ?>')">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
            <?php echo $this->Form->end(); ?>
        <?php } ?>

        <?php
        $count = 8 -$count;
        if( $count > 0 ){
            for($j = 1; $j <= $count; $j++){ $i = 3 - $i; ?>
            <ul class="ul_mag clear bg<?php echo $i; ?>">
                <li class="hg_padd" style="width:1.5%"></li>
                <li class="hg_padd" style="width:10%"></li>
                <li class="hg_padd" style="width:30%"></li>
                <li class="hg_padd center_txt" style="width:11%"></li>
                <li class="hg_padd center_txt" style="width:11%"></li>
                <li class="hg_padd center_txt" style="width:6%"></li>
                <li class="hg_padd center_txt" style="width:6%"></li>
            </ul>
        <?php  }
        } ?>

        <span class="title_block bo_ra2"></span>
    </div><!--END Tab1 -->
</div>

<?php if($this->request->is('ajax')){ ?>
<script type="text/javascript">
$(function(){
    input_show_select_calendar(".JtSelectDate");
});
</script>
<?php } ?>

<script type="text/javascript">
$(function(){
    $("form :input", "#salesorders_sub_content").change(function() {
        // var filters = {};
        // filters[$(this).attr('name')] = $(this).val();
        $.ajax({
            url: '<?php echo URL; ?>/salesorders/resources_auto_save',
            timeout: 15000,
            type:"post",
            data: $(this).closest('form').serialize(),
            success: function(html){
                if( html != "ok" )alerts("Error: ", html);
            }
        });
    });

    window_popup('contacts', 'Specify contact doing this sales order', '_resources');
    window_popup('equipments', 'Specify equipment');
});

function after_choose_contacts_resources(contact_id, contact_name){
    var salesorder_id = "<?php echo $salesorder_id; ?>";
    $.ajax({
        url: "<?php echo URL; ?>/salesorders/resources_window_choose/" + salesorder_id + "/Contact/" + contact_id + "/" + contact_name,
        timeout: 15000,
        success: function(html){
            if(html == "ok"){
                $("#resources").click();
            }else{
                alerts("Error: ", html);
            }
        }
    });
    return false;
}

function after_choose_equipments(equipment_id, equipment_name){
    var salesorder_id = "<?php echo $salesorder_id; ?>";
    $.ajax({
        url: "<?php echo URL; ?>/salesorders/resources_window_choose/" + salesorder_id + "/Equipment/" + equipment_id + "/" + equipment_name,
        timeout: 15000,
        success: function(html){
            if(html == "ok"){
                $("#resources").click();
                $("#resources").trigger('click');
            }else{
                alerts("Error: ", html);
            }
        }
    });
    return false;
}

// function salesorders_resources_choose(salesorder_id, value, type){
//     $.ajax({
//         url: "<?php echo URL; ?>/salesorders/resources_window_choose/" + salesorder_id + "/" + type + "/" + value,
//         timeout: 15000,
//         success: function(html){
//             if(html == "ok"){
//                 var contain = $("li.active", "#salesorders_ul_sub_content");
//                 $("a", contain).click();
//             }else{
//                 alerts("Error: ", html);
//             }
//         }
//     });
// }

function salesorders_resources_delete(task_id){

    confirms( "Message", "Are you sure you want to delete?",
        function(){
            $.ajax({
                url: '<?php echo URL; ?>/salesorders/resources_delete/' + task_id,
                timeout: 15000,
                success: function(html){
                    if( html != "ok" ){
                        alerts("Error: ", html);
                    }else{
                        $("#Resource_" + task_id).fadeOut();
                    }
                }
            });
        },function(){
            //else do somthing
    });
}
</script>