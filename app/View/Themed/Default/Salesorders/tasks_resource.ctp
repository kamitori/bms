<div class="clear_percent">
    <div class="tab_1 full_width">
        <span class="title_block bo_ra1">
            <span class="fl_dent"><h4><?php echo translate('Details'); ?></h4></span>
            <div class="float_left hbox_form dent_left_form">
                <input class="btn_pur size_width2" type="button" value="Add contact" onclick="$('#task_id').val('<?php echo $task_id; ?>');$('#so_task_save_flag').val('resource');$('#click_open_window_contacts_responsible_so_task').click()">
                <input id="click_open_window_equipments" class="btn_pur size_width2" type="button" value="Add asset" onclick="$('#task_id').val('<?php echo $task_id; ?>');$('#click_open_window_equipments').click()">
            </div>
        </span>
        <p class="clear"></p>

        <ul class="ul_mag clear bg3">
            <li class="hg_padd" style="width:1.5%"></li>
            <li class="hg_padd" style="width:10%"><?php echo translate('Type'); ?></li>
            <li class="hg_padd" style="width:19%"><?php echo translate('Name'); ?></li>
            <li class="hg_padd center_txt" style="width:11%"><?php echo translate('Start (schedule)'); ?></li>
            <li class="hg_padd center_txt" style="width:11%"><?php echo translate('End (schedule)'); ?></li>
            <li class="hg_padd center_txt" style="width:6%"><?php echo translate('Status'); ?></li>
            <li class="hg_padd center_txt" style="width:31%"><?php echo translate('Note'); ?></li>
            <li class="hg_padd center_txt" style="width:1%"></li>
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
        foreach( $arr_resource as $resource ){
            // if( $resource['type'] != 'Equipment' )continue;
            $i = 3 - $i; $k++; $count += 1;
        ?>
        <ul class="ul_mag clear bg<?php echo $i; ?>" id="Resource_<?php echo $resource['_id']; ?>">
            <li class="hg_padd" style="width:1.5%"><?php echo $this->Form->hidden('Resource._id', array( 'value' => $resource['_id'])); ?></li>
            <li class="hg_padd" style="width:10%"><?php echo $resource['type']; ?></li>
            <li class="hg_padd" style="width:19%"><?php echo $resource['name']; ?></li>
            <li class="hg_padd center_txt" style="width:11%">
                <div class="date">
                    <span class="float_left" style="width: 47%;">
                        <?php echo $this->Form->hidden('Resource.work_start_old', array('value' => $resource['work_start']->sec )); ?>
                        <?php echo $this->Form->input('Resource.work_start', array(
                                'class' => 'JtSelectDate force_reload input_inner input_inner_w bg'.$i,
                                'style' => 'width: 70px',
                                'readonly' => true,
                                'id' => 'Resourcework_end'.rand(0,99999999),
                                'value' => date('m/d/Y', $resource['work_start']->sec)
                        )); ?>
                    </span>
                </div>

                <div class="select_inner width_select" style="width: 41%; margin: 0;">
                    <div class="styled_select" style="margin: 0;">
                        <?php echo $this->Form->input('Resource.work_start_hour', array(
                                    'style' => 'margin-top: -3px;',
                                    'options' => $arr_hour,
                                    'class' => 'force_reload',
                                    'value' => date('H:i', $resource['work_start']->sec)
                            )); ?>
                    </div>
                </div>

            </li>
            <li class="hg_padd center_txt" style="width:11%">
                <div class="date">
                    <span class="float_left" style="width: 47%;">
                        <?php echo $this->Form->hidden('Resource.work_end_old', array('value' => $resource['work_end']->sec )); ?>
                        <?php echo $this->Form->input('Resource.work_end', array(
                                'class' => 'JtSelectDate force_reload input_inner input_inner_w bg'.$i,
                                'style' => 'width: 70px',
                                'readonly' => true,
                                'id' => 'Resourcework_end'.rand(0,99999999),
                                'value' => date('m/d/Y', $resource['work_end']->sec)
                        )); ?>
                    </span>
                </div>

                <div class="select_inner width_select" style="width: 41%; margin: 0;">
                    <div class="styled_select" style="margin: 0;">
                        <?php echo $this->Form->input('Resource.work_end_hour', array(
                                'options' => $arr_hour,
                                'style' => 'margin-top: -3px;',
                                'class' => 'force_reload',
                                'value' => date('H:i', $resource['work_end']->sec)
                        )); ?>
                    </div>
                 </div>
            </li>
            <li class="hg_padd" style="width:6%">
                <?php echo $this->Form->input('Resource.status', array(
                        'class' => 'input_select bg'.$i,
                        'value' => $resource['status'],
                        'style' => 'border-bottom:none; margin-top:-3px'
                )); ?>
                <?php echo $this->Form->hidden('Resource.status_id'); ?>
                <script type="text/javascript">
                    $(function () {
                        $("#ResourceStatus", "#Resource_<?php echo $resource['_id']; ?>").combobox(<?php echo json_encode($arr_equipments_status); ?>);
                    });
                </script>
            </li>
            <li class="hg_padd center_txt" style="width:31%">
                <?php echo $this->Form->input('Resource.note', array(
                        'class' => 'input_inner input_inner_w bg'.$i,
                        'value' => (isset($resource['note']))?$resource['note']:''
                )); ?>
            </li>
            <li class="hg_padd center_txt" style="width:1%">
                <div class="middle_check">
                    <a href="javascript:void(0)" onclick="tasks_resources_delete('<?php echo $resource['_id']; ?>')">
                        <span class="icon_remove2"></span>
                    </a>
                </div>
            </li>
        </ul>
        <?php } ?>

        <span class="title_block bo_ra2"></span>
    </div><!--END Tab1 -->
</div>