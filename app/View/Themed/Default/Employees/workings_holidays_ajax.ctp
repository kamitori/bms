<?php
	foreach(array('0.25','0.5','0.45','1','1.25','1.30','1.45','2') as $hour){
		$arr_hour_lunch[(string)$hour] = array('value'=>$hour,'class'=>'BgOptionHour','name'=>$hour);
	}
	$arr_day = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
	for ($i=0; $i < 24; $i++) {
		$j = $i;
		if($j < 10)$j = '0'.$j;
		if($i > 7 && $i < 18){
			$arr_hour[$j.':00'] = array('name'=> $j.':00', 'value'=> $j.':00', 'class'=>'BgOptionHour');
			$arr_hour[$j.':15'] = array('name'=> $j.':15', 'value'=> $j.':15', 'class'=>'BgOptionHour');
			$arr_hour[$j.':30'] = array('name'=> $j.':30', 'value'=> $j.':30', 'class'=>'BgOptionHour');
			$arr_hour[$j.':45'] = array('name'=> $j.':45', 'value'=> $j.':45', 'class'=>'BgOptionHour');
		}else{
			$arr_hour[$j.':00'] = $j.':00';
			$arr_hour[$j.':15'] = $j.':15';
			$arr_hour[$j.':30'] = $j.':30';
			$arr_hour[$j.':45'] = $j.':45';
		}
	}
	$color = 1;
	foreach($arr_day as $day) {
?>
	<?php echo $this->Form->hidden('WorkHoliday._id', array( 'value' => $workings_month )); ?>
	<ul class="ul_mag clear bg<?php echo $color; ?>" id="contacts_work_hour_'.$day.'">
		<li class="hg_padd" style="width:1.5%"></li>
		<li class="hg_padd" style="width:7%"><?php echo $day; ?></li>
		<?php $day = strtolower($day); ?>
		<?php for($i = 1; $i <= 4; $i ++){ ?>
		<!-- TIME <?php echo $i ?> -->
		<li class="hg_padd center_txt" style="width:4%">
			<div class="select_inner width_select" style="width: 100%; margin: 0;">
				<div class="styled_select" style="margin: 0;">
					<?php echo $this->Form->input('WorkHoliday.week_'.$i.'.'.$day.'.from_time', array(
								'style' => 'margin-top: -3px;',
								'options' => $arr_hour,
								'empty' => '   ',
								'value' => $workings_holidays['week_'.$i][$day]['from_time']
					)); ?>
				</div>
			</div>
		</li>
		<li class="hg_padd center_txt" style="width:1%">-</li>
		<li class="hg_padd center_txt" style="width:4%">
			<div class="select_inner width_select" style="width: 100%; margin: 0;">
				<div class="styled_select" style="margin: 0;">
					<?php echo $this->Form->input('WorkHoliday.week_'.$i.'.'.$day.'.to_time', array(
								'style' => 'margin-top: -3px;',
								'options' => $arr_hour,
								'empty' => '   ',
								'value' => $workings_holidays['week_'.$i][$day]['to_time']
					)); ?>
				</div>
			</div>
		</li>
		<li class="hg_padd center_txt" style="width:4%">
			<div class="select_inner width_select" style="width: 100%; margin: 0;">
				<div class="styled_select" style="margin: 0;">
					<?php echo $this->Form->input('WorkHoliday.week_'.$i.'.'.$day.'.lunch_time', array(
								'style' => 'margin-top: -3px;',
								'options' => $arr_hour_lunch,
								'empty' => '   ',
								'value' => $workings_holidays['week_'.$i][$day]['lunch_time']
					)); ?>
				</div>
			</div>
		</li>
		<li class="hg_padd center_txt" style="width:2%; background:rgb(170, 218, 224)"></li>
		<?php } ?>
	</ul>
	<?php $color = 3 - $color;
	}
?>
<script type="text/javascript">
$(function(){
	var total = <?php echo json_encode($total); ?>;
	for(var i in total){
		$("#total_"+i).val(total[i]);
	}
})
$("select","#contacts_work_hour").change(function(){
	$.ajax({
		url : "<?php echo URL.'/employees/workings_holidays_ajax'; ?>",
		type: "POST",
		data: {workings_month :  "<?php echo $workings_month; ?>",key : $(this).attr("name"), value : $(this).val()},
		success: function(result){
			var isObject = true;
			try{
				result = $.parseJSON(result);
			} catch(e){
				isObject = false;
			}
			if(!isObject)
				alerts("Message",result);
			else
				for(var i in result){
					$("#total_"+i).val(result[i]);
				}
		}
	});
});
</script>