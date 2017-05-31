<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent"><h4><?php echo translate('Details'); ?></h4></span>
		<div class="float_left hbox_form dent_left_form">
			<input id="click_open_window_contacts_resources" class="btn_pur size_width2" type="button" value="Add contact">
			<script type="text/javascript">
			$(function(){
				window_popup("contacts", "Specify contact", "_resources", "", "?is_employee=1");
			});
			</script>
			<input id="click_open_window_equipments" class="btn_pur size_width2" type="button" value="Add asset">
			<script type="text/javascript">
			$(function(){
				window_popup('equipments', 'Specify equipment');
			});
			</script>

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
	<div class="container_same_category" style="height: 200px;overflow-y: auto;">
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
	$delete = $this->Common->check_permission($controller.'_@_resources_tab_@_delete',$arr_permission);
	foreach( $arr_job as $value ){
		// if( $value['type'] != 'Equipment' )continue;
		 $k++; $count += 1;
	?>
		<?php echo $this->Form->create('Resource', array('id' => 'ResourceEntryForm_'.$k)); ?>
			<?php echo $this->Form->hidden('Resource.'.$k.'._id', array(
						'value' => $value['_id'],
						'class' => 'ParentForm'
				)); ?>
		<ul class="ul_mag clear bg<?php echo $i; ?>" id="Resource_<?php echo $value['_id']; ?>">
			<li class="hg_padd" style="width:1.5%"></li>
			<li class="hg_padd" style="width:10%"><?php echo $value['type']; ?></li>
			<li class="hg_padd" style="width:19%"><?php echo $value['name']; ?></li>
			<li class="hg_padd center_txt" style="width:11%">
				<div class="date">
					<span class="float_left" style="width: 47%;">
						<?php echo $this->Form->input('Resource.'.$k.'.work_start', array(
								'class' => 'JtSelectDate input_inner input_inner_w bg'.$i,
								'style' => 'width: 70px',
								'readonly' => true,
								'value' => date('m/d/Y', $value['work_start']->sec)
						)); ?>
					</span>
				</div>

				<div class="select_inner width_select" style="width: 41%; margin: 0;">
					<div class="styled_select" style="margin: 0;">
						<?php echo $this->Form->input('Resource.'.$k.'.work_start_hour', array(
									'style' => 'margin-top: -3px;',
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
								'readonly' => true,
								'value' => date('m/d/Y', $value['work_end']->sec)
						)); ?>
					</span>
				</div>

				<div class="select_inner width_select" style="width: 41%; margin: 0;">
					<div class="styled_select" style="margin: 0;">
						<?php echo $this->Form->input('Resource.'.$k.'.work_end_hour', array(
								'options' => $arr_hour,
								'style' => 'margin-top: -3px;',
								'value' => date('H:i', $value['work_end']->sec)
						)); ?>
					</div>
				</div>
			</li>
			<li class="hg_padd center_txt" style="width:6%">
				 <div class="select_inner width_select" style="width: 100%; margin: 0;">
					<div class="styled_select bg<?php echo $i; ?>" style="margin: 0;">
						<?php echo $this->Form->input('Resource.'.$k.'.status', array(
								'options' => $arr_equipments_status,
								'style' => 'margin-top: -3px;',
								'value' => $value['status']
						)); ?>
					</div>
				</div>
			</li>
			<li class="hg_padd center_txt" style="width:31%">
				<?php echo $this->Form->input('Resource.'.$k.'.note', array(
						'class' => 'input_inner input_inner_w bg'.$i,
						'value' => (isset($value['note']))?$value['note']:''
				)); ?>
			</li>
			<li class="hg_padd center_txt" style="width:1%">
				<?php if($delete): ?>
				<div class="middle_check">
					<a href="javascript:void(0)" onclick="jobs_resources_delete('<?php echo $value['_id']; ?>')">
						<span class="icon_remove2"></span>
					</a>
				</div>
				<?php endif; ?>
			</li>
		</ul>
		<?php echo $this->Form->end(); ?>
	<?php $i = 3 - $i; } ?>

	<?php
	$count = 9 -$count;
	if( $count > 0 ){
		for($j = 1; $j <= $count; $j++){?>
		<ul class="ul_mag clear bg<?php echo $i; ?>">
			<li class="hg_padd" style="width:1.5%"></li>
			<li class="hg_padd" style="width:10%"></li>
			<li class="hg_padd" style="width:19%"></li>
			<li class="hg_padd center_txt" style="width:11%"></li>
			<li class="hg_padd center_txt" style="width:11%"></li>
			<li class="hg_padd center_txt" style="width:6%"></li>
			<li class="hg_padd center_txt" style="width:31%"></li>
			<li class="hg_padd center_txt" style="width:1%"></li>
		</ul>
	<?php   $i = 3 - $i; }
	} ?>
	</div>
	<span class="title_block bo_ra2"></span>
</div><!--END Tab1 -->

<?php if($this->request->is('ajax')){ ?>
<script type="text/javascript">
$(function(){
	input_show_select_calendar(".JtSelectDate");
});
</script>
<?php } ?>

<script type="text/javascript">
$(function(){
	$(":input", "#jobs_sub_content").change(function() {
		var form = $(this).closest('form');
		$(":input", form).removeClass('error_input');

		$.ajax({
			url: '<?php echo URL; ?>/jobs/resources_auto_save',
			timeout: 15000,
			type:"post",
			data: form.serialize(),
			success: function(html){

				if(html == "date_work"){
					$("#Resource1WorkEnd", form).addClass('error_input');
					$("#Resource1WorkEndHour", form).addClass('error_input');

					alerts("Error: ", '"Work start date" can not greater than "Work end date"');

				}else if(html != "ok"){

					alerts("Error: ", html);
				}

				console.log(html);
			}
		});
	});

});

function after_choose_contacts_resources(contact_id, contact_name){
	var job_id = "<?php echo $job_id; ?>";
	$.ajax({
		url: "<?php echo URL; ?>/jobs/resources_window_choose/" + job_id + "/Contact/" + contact_id + "/" + contact_name,
		timeout: 15000,
		success: function(html){
			if(html == "ok"){
				$("#resources").click();
				$("#window_popup_contacts_resources").data("kendoWindow").close();

			}else{
				alerts("Error: ", html);
			}
		}
	});
	return false;
}

function after_choose_equipments(equipment_id, equipment_name){
	var job_id = "<?php echo $job_id; ?>";
	$.ajax({
		url: "<?php echo URL; ?>/jobs/resources_window_choose/" + job_id + "/Equipment/" + equipment_id + "/" + equipment_name,
		timeout: 15000,
		success: function(html){
			if(html == "ok"){
				$("#resources").click();
				$("#window_popup_equipments").data("kendoWindow").close();
			}else{
				alerts("Error: ", html);
			}
		}
	});
	return false;
}

function jobs_resources_delete(job_id){

	confirms( "Message", "Are you sure you want to delete?",
		function(){
			$.ajax({
				url: '<?php echo URL; ?>/jobs/resources_delete/' + job_id,
				timeout: 15000,
				success: function(html){
					if( html != "ok" ){
						alerts("Error: ", html);
					}else{
						$("#Resource_" + job_id).fadeOut();
					}
				}
			});
		},function(){
			//else do somthing
	});
}
</script>