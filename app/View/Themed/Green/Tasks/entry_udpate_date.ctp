<?php for ($i=0; $i < 24; $i++) {
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
?>
<p class="clear">
    <span class="label_1 float_left fixbor minw_lab"><?php echo translate('Start'); ?></span>
</p>
<div class="width_in3 float_left indent_input_tp">
    <div class="two_colum border_right">
        <?php echo $this->Form->input('Task.work_start', array(
            'class' => 'JtSelectDate input_1 float_left force_reload',
            'readonly' => true
        )); ?>
    </div>
    <div class="once_colum top_se">
        <div class="styled_select">
            <?php echo $this->Form->input('Task.work_start_hour', array(
                'options' => $arr_hour,
                'class' => 'force_reload'
            )); ?>
        </div>
    </div>
</div>

<p class="clear">
    <span class="label_1 float_left minw_lab"><?php echo translate('Finish'); ?></span>
</p>
<div class="width_in3 float_left indent_input_tp">
    <div class="two_colum border_right">
        <?php echo $this->Form->input('Task.work_end', array(
            'class' => 'JtSelectDate input_1 float_left force_reload',
            'readonly' => true
        )); ?>
    </div>
    <div class="once_colum top_se">
        <div class="styled_select">
            <?php echo $this->Form->input('Task.work_end_hour', array(
                'options' => $arr_hour,
                'class' => 'force_reload'
            )); ?>
        </div>
    </div>
</div>