<div class="tab_1" style="width:100%;">
	<span class="title_block bo_ra1">
		<span class="fl_dent"><h4><?php echo 'Working hours for this contact - CURRENT PAY PERIOD';?></h4></span>
		<?php if( $login ) { ?>
		<button type="button" class="btn_pur" onclick="setTime('start')" style="width: 50px;" title="Set current day's work start time">Login</button>
		<?php } ?>
		<?php if( $logout ) { ?>
		<button type="button" class="btn_pur" onclick="setTime('end')" style="width: 50px;" title="Set current day's work end time">Logout</button>
		<?php } ?>
		<div style="width:22px; height:16px; float:right; margin-top:-1px;background: url('/theme/default/images/icon.png') no-repeat scroll -235px -61px #fff;cursor: pointer; border-radius: 0px 10px 10px 0px;" title="Next weeks" onclick="scroll_weeks(<?php echo $end_week_date_time;?>,'next');">&nbsp&nbsp
		</div>

        <div class="indent_new width_in " style="width:6%; float:right; margin-top:-1px">
            <?php echo $this->Form->input('date_to', array(
                'class' => ' float_left JtSelectDate validate change_date',
                'readonly' => true,
                'name'=>'date_to',
                'style' =>'padding:0 0%; width:100%;text-align:center;',
                'default' => $to_date
                )); ?>
	        <script>
				$(function() {
					$( "#date_to" ).datepicker({dateFormat: 'dd M, yy' });
				});
			</script>
        </div>
        <div style="width:1%; float:right; margin-top:0px; text-align:right;line-height:16px;">to</div>
        <div class="indent_new width_in" style="width:6%; float:right; margin-top:-1px">
	        <?php echo $this->Form->input('date_from', array(
	            'class' => ' jt_input_search JtSelectDate validate change_date',
	            'readonly' => true,
	            'name'=>'date_from',
	            'style' =>'padding: 0.3% 1%;width: 100%;text-align:center;',
	            'default' => $from_date
	            )); ?>

	        <script>
				$(function() {
					$( "#date_from" ).datepicker({dateFormat: 'dd M, yy' });
				});
			</script>
		</div>
		<div style="width:3%; float:right; margin-top:0px; text-align:right;line-height:16px;">From</div>
		<div style="width:22px; height:16px; float:right; margin-top:-1px;background: url('/theme/default/images/icon.png') no-repeat scroll -213px -61px #fff;cursor: pointer;border-radius: 10px 0px 0px 10px;" title="Pre weeks" onclick="scroll_weeks(<?php echo $begin_date_time;?>,'pre');">&nbsp&nbsp
		</div>

	</span>

	<!-- HEADER BOX -->
	<?php $bgcolor = array('249, 249, 213','249, 217, 213','170, 218, 224','213, 249, 216',
							'249, 249, 213','249, 217, 213','170, 218, 224','213, 249, 216'
						); ?>
	<p class="clear"></p>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd" style="width:5%;"><?php echo translate('DAY'); ?></li>
		<?php for($m=2;$m<4;$m++){?>
		<li class="hg_padd center_txt" style="width:6%;background:rgb(<?php echo $bgcolor[$m];?>)">DATE</li>
		<li class="hg_padd" style="width:11.1%;background:rgb(<?php echo $bgcolor[$m];?>)">
			<?php echo translate('Week '.($m-1)); ?>
		</li>
		<li class="hg_padd" style="width:4%;background:rgb(<?php echo $bgcolor[$m];?>)"><?php echo translate('Lunch '); ?></li>
		<li class="hg_padd right_txt" style="width:4%;background:rgb(<?php echo $bgcolor[$m];?>)">Hours</li>
		<li class="hg_padd center_txt" style="width:10%;background:rgb(<?php echo $bgcolor[$m];?>)">Leave / holidays</li>
		<li class="hg_padd center_txt" style="width:3%;"></li>
		<?php }?>
	</ul>
	<!-- CONTENT BOX -->
	<div id="contacts_work_hour" class="contacts_work_hour">
		<?php
			$arr_hour_lunch['0.25'] = array('value'=>'0.25','class'=>'BgOptionHour','name'=>'0.25');
			$arr_hour_lunch['0.5'] = array('value'=>'0.5','class'=>'BgOptionHour','name'=>'0.5');
			$arr_hour_lunch['0.75'] = array('value'=>'0.75','class'=>'BgOptionHour','name'=>'0.75');
			$arr_hour_lunch['1'] = array('value'=>'1','class'=>'BgOptionHour','name'=>'1');
			$arr_hour_lunch['1.5'] = array('value'=>'1.5','class'=>'BgOptionHour','name'=>'1.5');
			$arr_hour_lunch['2'] = array('value'=>'2','class'=>'BgOptionHour','name'=>'2');

			$arr_purpose['Day off'] = array('value'=>'dayoff','class'=>'BgOptionHour','name'=>'Day off');
			$arr_purpose['Holiday'] = array('value'=>'holiday','class'=>'BgOptionHour','name'=>'Holiday');
			$arr_purpose['Sick'] = array('value'=>'sick','class'=>'BgOptionHour','name'=>'Sick');

			$arr_day = array( '','Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
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



			$i = 1;
			for ($k=1; $k <=7; $k++) {
		?>
			<?php echo $this->Form->hidden('Work._id', array( 'value' => $contact_id )); ?>
			<ul class="ul_mag clear bg<?php echo $i; ?>" id="contacts_work_hour_<?php echo $k; ?>">
				<li class="hg_padd" style="width:5%"><?php echo $arr_day[$k]; ?></li>

				<?php for($m=3;$m<=4;$m++){
					if(isset($data[$k][$m])){
					$key = $data[$k][$m]['yearmonth'].'_'.$data[$k][$m]['date'].'.work_start';

					if(isset($data[$k][$m]['date']) && isset($data[$k][$m]['yearmonth'])){
						$date_int = (int)$data[$k][$m]['yearmonth']*100+(int)$data[$k][$m]['date'];
						$now_int = (int)date('Ymd');
					}
					$is_now_date = false;
					if($date_int==$now_int)
						$is_now_date = true;
				?>
					<li class="hg_padd center_txt" style="width:6%; <?php echo $is_now_date?'color:blue;':'';?>">
						<?php
							if(isset($data[$k][$m]['date']) && isset($data[$k][$m]['yearmonth'])){
								$arr_ym = str_split((string)$data[$k][$m]['yearmonth'],4);
								echo date('d M, Y',strtotime($data[$k][$m]['date'].'-'.$arr_ym[1].'-'.$arr_ym[0]));
							}
						?>
					</li>
					<!-- TIME 1 -->
					<li class="hg_padd center_txt" style="width:4%">
						<div class="select_inner width_select" style="width: 100%; margin: 0;">
							<div class="styled_select" style="margin: 0;">
								<?php
									$start = (isset($data[$k][$m]['work_start'])?$data[$k][$m]['work_start']:'');
									echo $this->Form->input($key, array(
											'style' => 'text-align: center;',
											'class' => 'force_reload input_inner input_inner_w bg'.$i,
											'rel' => 'work_start',
											'rev' => $data[$k][$m]['yearmonth'],
											'time' => $data[$k][$m]['date'],
											'empty' => '   ',
											'value' => $start,

								)); ?>
							</div>
						</div>
					</li>
					<?php $key = $m*2; $zero = ($key<10)?'0':''; ?>
					<li class="hg_padd center_txt" style="width:1%">-</li>
					<li class="hg_padd center_txt" style="width:4%">
						<div class="select_inner width_select" style="width: 100%; margin: 0;">
							<div class="styled_select" style="margin: 0;">
								<?php
									$end = (isset($data[$k][$m]['work_end'])?$data[$k][$m]['work_end']:'');
									echo $this->Form->input($key, array(
											'style' => 'text-align: center;',
											'class' => 'force_reload input_inner input_inner_w bg'.$i,
											'rel' => 'work_end',
											'rev' => $data[$k][$m]['yearmonth'],
											'time' => $data[$k][$m]['date'],
											'empty' => '   ',
											'value' => (isset($data[$k][$m]['work_end'])?$data[$k][$m]['work_end']:''),

								)); ?>
							</div>
						</div>
					</li>
					<li class="hg_padd center_txt" style="width:4%">
						<div class="select_inner width_select" style="width: 100%; margin:0;">
							<div class="styled_select" style="margin: 0;">
								<?php
									echo $this->Form->input($key, array(
											'style' => 'text-align: center;',
											'class' => 'force_reload input_inner input_inner_w bg'.$i,
											'options' => $arr_hour_lunch,
											'rel' => 'lunch',
											'rev' => $data[$k][$m]['yearmonth'],
											'time' => $data[$k][$m]['date'],
											'empty' => '   ',
											'value' => (isset($data[$k][$m]['lunch'])?$data[$k][$m]['lunch']:'')
								)); ?>
							</div>
						</div>
					</li>
					<li class="hg_padd center_txt" style="width:4%; text-align:right;">
						<?php
							if(isset($data[$k][$m]['hours_work']) && $data[$k][$m]['hours_work']>0)
								echo $this->Common->format_currency($data[$k][$m]['hours_work'],2);
						?>
					</li>
					<li class="hg_padd center_txt" style="width:10%; text-align:right;" title="<?php echo isset($data[$k][$m]['leave_by'])?$data[$k][$m]['leave_by']:'';?>">
						<?php
							$string = ''; $color = '';
							if(isset($data[$k][$m]['purpose']) && $data[$k][$m]['purpose']!='')
								$string = $data[$k][$m]['purpose'];
							else if(isset($data[$k][$m]['leave_by']))
								$string = $data[$k][$m]['leave_by'];
							if(isset($data[$k][$m]['leave_by'])) $color = 'color:red;';
						?>
						<?php echo $this->Form->input($key, array(
									'style' => 'margin-top: -3px;'.$color,
									'class' => 'input_inner input_inner_w bg'.$i,
									'rel' => 'purpose',
									'rev' => $data[$k][$m]['yearmonth'],
									'time' => $data[$k][$m]['date'],
									'empty' => '   ',
									'value' => $string
						));?>
					</li>
					<li class="hg_padd center_txt" style="width:3%; text-align:right;"></li>
				<?php } }?>
			</ul>
			<?php $i = 3 - $i; } ?>
	</div>
	<span class="hit"></span>
	<span class="title_block bo_ra2">
		<div style=" width:30%;float:left;margin-left:13%;">
			<span style="float:left;width:57%; text-align:right;">Total W1 : </span>
			<span style="float:left;width:43%;">
				<input style="text-align: right; padding-right:3%; color:red;width: 36%;" class="input_w2" type="text" readonly="true" id="total_week2" value="<?php echo $this->Common->format_currency($sumweek[3]); ?>"> (hours)
			</span>
		</div>
		<div style=" width:30%;float:left;margin-left:14.6%;">
			<span style="float:left;width:57%; text-align:right;">Total W2 : </span>
			<span style="float:left;width:43%;">
				<input style="text-align: right; padding-right:3%; color:red;width: 36%;" class="input_w2" type="text" readonly="true" id="total_week2" value="<?php echo $this->Common->format_currency($sumweek[4]); ?>">(hours)
			</span>
		</div>
		<div style="width:12%;float:left">
			<span style="float:left;width:30%; text-align:right;">Total: </span>
			<span style="float:left;">
				<input style="text-align: center; color:red;font-weight:bold;font-size:15px;" class="input_w2" type="text" readonly="true" id="total_hour_in_week" value="<?php echo $this->Common->format_currency($sum_2_week_2); ?>">(hours)
			</span>
		</div>
	</span>
</div>




<div class="tab_1" style="width:100%; margin-top:1%;">
	<span class="title_block bo_ra1">
		<span class="fl_dent"><h4><?php echo 'Working hours for this contact - LAST PAY PERIOD';?></h4></span>
	</span>

	<!-- HEADER BOX -->
	<?php $bgcolor = array('249, 249, 213','249, 217, 213','170, 218, 224','213, 249, 216',
							'249, 249, 213','249, 217, 213','170, 218, 224','213, 249, 216'
						); ?>
	<p class="clear"></p>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd" style="width:5%;"><?php echo translate('DAY'); ?></li>
		<?php for($m=0;$m<2;$m++){?>
		<li class="hg_padd center_txt" style="width:6%;background:rgb(<?php echo $bgcolor[$m];?>)">DATE</li>
		<li class="hg_padd" style="width:11.1%;background:rgb(<?php echo $bgcolor[$m];?>)">
			<?php echo translate('Last week '.($m+1)); ?>
		</li>
		<li class="hg_padd" style="width:4%;background:rgb(<?php echo $bgcolor[$m];?>)"><?php echo translate('Lunch '); ?></li>
		<li class="hg_padd right_txt" style="width:4%;background:rgb(<?php echo $bgcolor[$m];?>)">Hours</li>
		<li class="hg_padd center_txt" style="width:10%;background:rgb(<?php echo $bgcolor[$m];?>)">Leave / holidays</li>
		<li class="hg_padd center_txt" style="width:3%;"></li>
		<?php }?>
	</ul>
	<!-- CONTENT BOX -->
	<div id="contacts_work_hour" class="contacts_work_hour">
		<?php
			$arr_hour_lunch['0.25'] = array('value'=>'0.25','class'=>'BgOptionHour','name'=>'0.25');
			$arr_hour_lunch['0.5'] = array('value'=>'0.5','class'=>'BgOptionHour','name'=>'0.5');
			$arr_hour_lunch['0.75'] = array('value'=>'0.75','class'=>'BgOptionHour','name'=>'0.75');
			$arr_hour_lunch['1'] = array('value'=>'1','class'=>'BgOptionHour','name'=>'1');
			$arr_hour_lunch['1.5'] = array('value'=>'1.5','class'=>'BgOptionHour','name'=>'1.5');
			$arr_hour_lunch['2'] = array('value'=>'2','class'=>'BgOptionHour','name'=>'2');

			$arr_purpose['Day off'] = array('value'=>'dayoff','class'=>'BgOptionHour','name'=>'Day off');
			$arr_purpose['Holiday'] = array('value'=>'holiday','class'=>'BgOptionHour','name'=>'Holiday');
			$arr_purpose['Sick'] = array('value'=>'sick','class'=>'BgOptionHour','name'=>'Sick');

			$arr_day = array( '','Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
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



			$i = 1;
			for ($k=1; $k <=7; $k++) {
		?>
			<?php echo $this->Form->hidden('Work._id', array( 'value' => $contact_id )); ?>
			<ul class="ul_mag clear bg<?php echo $i; ?>" id="contacts_work_hour_<?php echo $k; ?>">
				<li class="hg_padd" style="width:5%"><?php echo $arr_day[$k]; ?></li>

				<?php for($m=1;$m<=2;$m++){
					$key = $data[$k][$m]['yearmonth'].'_'.$data[$k][$m]['date'].'.work_start';
				?>
					<li class="hg_padd center_txt" style="width:6%">
						<?php
							if(isset($data[$k][$m]['date']) && isset($data[$k][$m]['yearmonth'])){
								$arr_ym = str_split((string)$data[$k][$m]['yearmonth'],4);
								echo date('d M, Y',strtotime($data[$k][$m]['date'].'-'.$arr_ym[1].'-'.$arr_ym[0]));
							}
						?>
					</li>
					<!-- TIME 1 -->
					<li class="hg_padd center_txt" style="width:4%">
						<div class="select_inner width_select" style="width: 100%; margin: 0;">
							<div class="styled_select" style="margin: 0;">
								<?php
									$start = (isset($data[$k][$m]['work_start'])?$data[$k][$m]['work_start']:'');
									echo $this->Form->input($key, array(
											'style' => 'text-align: center;',
											'class' => 'force_reload input_inner input_inner_w bg'.$i,
											'rel' => 'work_start',
											'rev' => $data[$k][$m]['yearmonth'],
											'time' => $data[$k][$m]['date'],
											'empty' => '   ',
											'value' => (isset($data[$k][$m]['work_start'])?$data[$k][$m]['work_start']:''),

								));
								 ?>
							</div>
						</div>
					</li>
					<?php $key = $m*2; $zero = ($key<10)?'0':''; ?>
					<li class="hg_padd center_txt" style="width:1%">-</li>
					<li class="hg_padd center_txt" style="width:4%">
						<div class="select_inner width_select" style="width: 100%; margin: 0;">
							<div class="styled_select" style="margin: 0;">
								<?php
									$end = (isset($data[$k][$m]['work_end'])?$data[$k][$m]['work_end']:'');
									echo $this->Form->input($key, array(
											'style' => 'text-align: center;',
											'class' => 'force_reload input_inner input_inner_w bg'.$i,
											'rel' => 'work_end',
											'rev' => $data[$k][$m]['yearmonth'],
											'time' => $data[$k][$m]['date'],
											'empty' => '   ',
											'value' => (isset($data[$k][$m]['work_end'])?$data[$k][$m]['work_end']:''),

								)); ?>
							</div>
						</div>
					</li>
					<li class="hg_padd center_txt" style="width:4%">
						<div class="select_inner width_select" style="width: 100%; margin:0;">
							<div class="styled_select" style="margin: 0;">
								<?php echo $this->Form->input($key, array(
											'style' => 'text-align: center;',
											'class' => 'force_reload',
											'options' => $arr_hour_lunch,
											'rel' => 'lunch',
											'rev' => $data[$k][$m]['yearmonth'],
											'time' => $data[$k][$m]['date'],
											'empty' => '   ',
											'value' => (isset($data[$k][$m]['lunch'])?$data[$k][$m]['lunch']:'')
								)); ?>
							</div>
						</div>
					</li>
					<li class="hg_padd center_txt" style="width:4%; text-align:right;">
						<?php
							if(isset($data[$k][$m]['hours_work']) && $data[$k][$m]['hours_work']>0)
								echo $this->Common->format_currency($data[$k][$m]['hours_work'],2);
						?>
					</li>
					<li class="hg_padd center_txt" style="width:10%; text-align:right;" title="<?php echo isset($data[$k][$m]['leave_by'])?$data[$k][$m]['leave_by']:'';?>">
						<?php
							$string = ''; $color = '';
							if(isset($data[$k][$m]['purpose']) && $data[$k][$m]['purpose']!='')
								$string = $data[$k][$m]['purpose'];
							else if(isset($data[$k][$m]['leave_by']))
								$string = $data[$k][$m]['leave_by'];
							if(isset($data[$k][$m]['leave_by'])) $color = 'color:red;';
						?>
						<?php echo $this->Form->input($key, array(
									'style' => 'margin-top: -3px;'.$color,
									'class' => 'input_inner input_inner_w bg'.$i,
									'rel' => 'purpose',
									'rev' => $data[$k][$m]['yearmonth'],
									'time' => $data[$k][$m]['date'],
									'empty' => '   ',
									'value' => $string
						));?>
					</li>
					<li class="hg_padd center_txt" style="width:3%; text-align:right;"></li>
				<?php }?>
			</ul>
			<?php $i = 3 - $i; } ?>
	</div>
	<span class="hit"></span>
	<span class="title_block bo_ra2">
		<div style=" width:30%;float:left;margin-left:13%;">
			<span style="float:left;width:57%; text-align:right;">Total LW1 : </span>
			<span style="float:left;width:43%;">
				<input style="text-align: right; padding-right:3%; color:red;width: 36%;" class="input_w2" type="text" readonly="true" id="total_week2" value="<?php echo $this->Common->format_currency($sumweek[1]); ?>"> (hours)
			</span>
		</div>
		<div style=" width:30%;float:left;margin-left:14.6%;">
			<span style="float:left;width:57%; text-align:right;">Total LW2 : </span>
			<span style="float:left;width:43%;">
				<input style="text-align: right; padding-right:3%; color:red;width: 36%;" class="input_w2" type="text" readonly="true" id="total_week2" value="<?php echo $this->Common->format_currency($sumweek[2]); ?>">(hours)
			</span>
		</div>
		<div style="width:12%;float:left">
			<span style="float:left;width:30%; text-align:right;">Total: </span>
			<span style="float:left;">
				<input style="text-align: center; color:red;font-weight:bold;font-size:15px;" class="input_w2" type="text" readonly="true" id="total_hour_in_week" value="<?php echo $this->Common->format_currency($sum_2_week_1); ?>">(hours)
			</span>
		</div>
	</span>
</div>


<script type="text/javascript" src="<?php echo URL.'/theme/default/js/jquery.inputmask.bundle.min.js' ?>"></script>
<script type="text/javascript">
function setTime(type) {
	var type = type || 'start';
	$.ajax({
		url: '<?php echo URL.'/contacts/working_hours_' ?>'+type,
		success: function() {
			$('.ul_tab #workings_holidays').trigger('click');
		}
	})
}
$(function(){
	$('#load_subtab input.force_reload').inputmask('99:99', {'placeholder': '06:01' });
	$(":input", ".contacts_work_hour").change(function() {
		var ids = $("#mongo_id").val();
		var objs = $(this);
		ajax_note_set("Saving... ");
		var value = $(this).val();
		$.ajax({
			url: '<?php echo URL; ?>/contacts/work_hour_auto_save/' + ids,
			timeout: 15000,
			type:"post",
			data: { yearmonth: $(this).attr("rev"), day: $(this).attr("time"),  keyval: $(this).attr("rel"),  value: value },
			success: function(html){
				var cancelCallBack = function() {
					objs.val($(objs).data('value'));
				};
				if( html == "ok" ){
					ajax_note("Saving...Saved !");
					$("#workings_holidays").click();
				} else if( html == 'need_manager' ) {
					callPassword(objs, {'cancelCallBack': cancelCallBack});
				}else if(html == "no_edit_last_date"){
					callPassword(objs, {'cancelCallBack': cancelCallBack});
				}else if(html == "no_edit_future_date"){
					alerts("Message", "You can not edit future date.", function(){ $("#workings_holidays").click(); });
				}else{
					alerts("Error: ", html, function(){ $("#workings_holidays").click(); });
				}
			}
		});
	}).focus(function(){
		$(this).data('value', $(this).val());
	}).click(function(){
		try {
			this.select();
		} catch(err) {

		}
	});

	$(".change_date").change(function(){
		var ids = $(this).attr('id');
		scroll_weeks($(this).val(),ids);
	});
});

function scroll_weeks(begin_time,fc){
	$.ajax({
		url: '<?php echo URL; ?>/contacts/working_hour_set_session/',
		timeout: 15000,
		type:"post",
		data: { begin_time: begin_time, fc: fc},
		success: function(html){
			if( html == "ok" ){
				$("#workings_holidays").click();
			}else{
				alerts("Error: ", html, function(){ $("#workings_holidays").click(); });
			}
		}
	});
}


function callPassword(thisobj, options){
	var callBack = function(){
		$.ajax({
			url: '<?php echo URL; ?>/contacts/work_hour_auto_save/' + $("#mongo_id").val(),
			timeout: 15000,
			type:"post",
			data: { yearmonth: thisobj.attr("rev"), day: thisobj.attr("time"),  keyval: thisobj.attr("rel"),  value: thisobj.val() },
			success: function(html){
				if( html == "ok" ) {
					$("#workings_holidays").click();
				}
			}
		});
	};
	callPasswordPopup(callBack, options);
}

function callPasswordPopup(callBack, options){
	var options = options || {};
	if( $("#confirms_window" ).attr("id") == undefined ){
		var html = '<div id="password_confirm" >';
			html +=	   '<div class="jt_box" style=" width:100%;">';
			html +=		  '<div class="jt_box_line"><div class=" jt_box_label " style=" width:25%;"></div><div id="alert_message"></div></div>';
			html +=	      '<div class="jt_box_line">';
			html +=	         '<div class=" jt_box_label " style=" width:25%;height: 75px">Password</div>';
			html +=	         '<div class="jt_box_field " style=" width:71%"><input name="password" id="password" class="input_1 float_left" type="password" value=""></div><input style="margin-top:2%" type="button" class="jt_confirms_window_cancel" id="confirms_cancel" value=" Cancel " /><input style="margin-top:2%" type="button" class="jt_confirms_window_ok" value=" Ok " id="confirms_ok" />';
			html +=	      '</div>';
			html +=	      '</div>';
			html +=	   '</div>';
			html +=	'</div>';
		$('<div id="confirms_window" style="width: 99%; padding: 0px; overflow: auto;">'+html+'</div>').appendTo("body");
	}
	var confirms_window = $("#confirms_window");
	confirms_window.kendoWindow({
		width: "355px",
		height: "100px",
		title: "Enter password",
		visible: false,
		activate: function(){
		  $('#password').focus();
		}
	});
	$("#password").keypress(function(evt){
		var keyCode = (evt.which) ? evt.which : event.keyCode
	    if(keyCode==13) {
	    	$("#confirms_ok").click();
	    }
	});
	confirms_window.data("kendoWindow").center();
	confirms_window.data("kendoWindow").open();
	$("#confirms_ok").unbind("click");
	$("#confirms_ok").click(function() {
		$("#alert_message").html("");
		if( $("#password").val().trim()==''  ){
			$("#alert_message").html('<span style="margin-left: 5px; color:red; font-weight: 700">Password must not be empty.</span');
			$("#password").focus();
			return false;
		}
		var data = {};
		data['password'] = $("#password").val();
		if(typeof options.extraData == "object"){
			$.extend(data, options.extraData);
		}
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/check_password',
			type:"POST",
			data: data,
			success: function(text_return){
				console.log(text_return)
				if(text_return=='wrong_pass'){
					$("#alert_message").html('<span style="margin-left: 5px; color:red; font-weight: 700">Wrong password.</span');
					if(typeof options.falseCallBack == "function"){
						options.falseCallBack();
					}
				}else if(text_return=='success'){
					callBack();
       				confirms_window.data("kendoWindow").destroy();
				}
			}
		});
	});
	$('#confirms_cancel').click(function() {
		if(typeof options.cancelCallBack == "function"){
			options.cancelCallBack();
		}
		$("#alert_message").html("");
       	confirms_window.data("kendoWindow").destroy();
    });
    return false;
}


</script>