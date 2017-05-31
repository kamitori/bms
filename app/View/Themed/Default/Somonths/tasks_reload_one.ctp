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

echo $this->Form->hidden('Task._id', array( 'value' => $value['_id'] )); ?>

<li class="hg_padd" style="width:1%">
    <a href="<?php echo URL; ?>/tasks/entry/<?php echo $value['_id']; ?>">
        <span class="icon_emp"></span>
    </a>
</li>
<li class="hg_padd center_txt" style="width:1%">
    <?php echo $this->Form->input('Task.no', array(
            'class' => 'input_inner input_inner_w',
            'value' => $value['no'],
            'style' => 'text-align:center;'
    )); ?>
</li>
<li class="hg_padd" style="width:1%">
    <img id="toggle_plus" src="/theme/default/img/toggle_plus.png" style="cursor:pointer" onclick="so_task_show_resource('<?php echo $value['_id']; ?>', '', this)">
    <img id="toggle_minus" src="/theme/default/img/toggle_minus.png" style="cursor:pointer;display:none" onclick="so_task_show_resource('<?php echo $value['_id']; ?>', '', this)">
</li>
<li class="hg_padd line_mg" style="width:32%">
    <?php echo $this->Form->input('Task.name', array(
            'class' => 'input_inner input_inner_w',
            'value' => $value['name'],
            'style' => 'text-align:left;'
    )); ?>
</li>
<li class="hg_padd line_mg" style="width:8%">
    <?php echo $this->Form->input('Task.our_rep', array(
            'class' => 'input_inner float_left',
            'value' => $value['our_rep'],
            'readonly' => true,
    )); ?>
    <?php echo $this->Form->hidden('Task.our_rep_id', array(
            'value' => $value['our_rep_id'],
    )); ?>
    <span class="iconw_m indent_dw_m" onclick="$('#so_task_save_flag').val('rep');$('#contacts_responsible_so_task_save_pos').val(<?php echo $stt; ?>);$('#click_open_window_contacts_responsible_so_task').click()"></span>
</li>
<li class="hg_padd" style="width:8%">
    <?php echo $this->Form->input('Task.type', array(
            'class' => 'input_select',
            'value' => (isset($value['type'])?$value['type']:''),
    )); ?>
    <?php echo $this->Form->hidden('Task.type_id', array('value' => $value['type_id'])); ?>
    <script type="text/javascript">
        $(function () {
            $("#TaskType", "#Salesorder_Task_<?php echo $value['_id']; ?>").combobox(<?php echo json_encode($arr_tasks_type); ?>);
        });
    </script>
</li>
<li class="hg_padd line_mg center_txt" style="width:10%">
    <div class="date">
        <span class="float_left" style="width: 47%;">
            <?php echo $this->Form->hidden('Task.work_start_old', array('value' => $value['work_start']->sec )); ?>
            <?php echo $this->Form->input('Task.work_start', array(
                    'class' => 'JtSelectDate force_reload input_inner input_inner_w',
                    'style' => 'width: 70px',
                    'id' => 'Taskwork_start'.rand(0,99999999),
                    'readonly' => true,
                    'value' => date('m/d/Y', $value['work_start']->sec)
            )); ?>
        </span>
    </div>
    <div class="select_inner width_select" style="width: 41%; margin: 0;">
        <div class="styled_select" style="margin: 0;">
            <?php echo $this->Form->input('Task.work_start_hour', array(
                        'style' => 'margin-top: -3px;',
                        'class' => 'force_reload',
                        'options' => $arr_hour,
                        'value' => date('H:i', $value['work_start']->sec)
                )); ?>
        </div>
    </div>
</li>
<li class="hg_padd line_mg center_txt" style="width:10%">
    <div class="date">
        <span class="float_left" style="width: 47%;">
            <?php echo $this->Form->hidden('Task.work_end_old', array('value' => $value['work_end']->sec )); ?>
            <?php echo $this->Form->input('Task.work_end', array(
                    'class' => 'JtSelectDate force_reload input_inner input_inner_w',
                    'style' => 'width: 70px',
                    'readonly' => true,
                    'id' => 'Taskwork_end'.rand(0,99999999),
                    'value' => date('m/d/Y', $value['work_end']->sec)
            )); ?>
        </span>
    </div>

    <div class="select_inner width_select" style="width: 41%; margin: 0;">
        <div class="styled_select" style="margin: 0;">
            <?php echo $this->Form->input('Task.work_end_hour', array(
                    'options' => $arr_hour,
                    'style' => 'margin-top: -3px;',
                    'class' => 'force_reload',
                    'value' => date('H:i', $value['work_end']->sec)
            )); ?>
        </div>
     </div>
</li>
<li class="hg_padd" style="width:6%;">
    <?php echo $this->Form->input('Task.status', array(
            'class' => 'input_select',
            'value' => (isset($value['status'])?$value['status']:''),
    )); ?>
    <?php echo $this->Form->hidden('Task.status_id', array('value' => $value['status_id'])); ?>
    <script type="text/javascript">
        $(function () {
            $("#TaskStatus", "#Salesorder_Task_<?php echo $value['_id']; ?>").combobox(<?php echo json_encode($arr_tasks_status); ?>);
        });
    </script>
</li>
<li class="hg_padd bor_mt center_txt" style="width:1%">
    <div class="middle_check">
        <a title="Delete link" href="javascript:void(0)" onclick="resource_remove_task_salesorder('<?php echo $value['_id']; ?>')">
            <span class="icon_remove2"></span>
        </a>
    </div>
</li>