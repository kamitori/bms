<!-- DIV 3 -->
<style type="text/css">
ul.ul_mag li.hg_padd {
	overflow: visible !important;
}
</style>
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
			<li class="hg_padd" style="width:19%"><?php echo $value['name']; ?></li>
			<li class="hg_padd center_txt" style="width:11%">
				<div class="date">
					<span class="float_left" style="width: 47%;">
						<?php echo $this->Form->input('Resource.'.$k.'.work_start', array(
						       	'id'=> 'ResourceWorkStart_'.$k,
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
						            'id'=> 'ResourceWorkStartHour_'.$k,
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
						        'id'=> 'ResourceWorkEnd_'.$k,
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
						        'id'=> 'ResourceWorkEndHour_'.$k,
								'options' => $arr_hour,
								'style' => 'margin-top: -3px;',
								'value' => date('H:i', $value['work_end']->sec)
						)); ?>
					</div>
				 </div>
			</li>
			<li class="hg_padd" style="width:6%">
				<?php echo $this->Form->input('Resource.'.$k.'.status', array(
				        'id'=> 'ResourceStatus_'.$k,
						'class' => 'input_select bg'.$i,
						'value' => $value['status'],
						'style' => 'border-bottom:none; margin-top:-3px'
				)); ?>
				<input type="hidden" name="data[Resource][<?php echo $k; ?>][status_id]" id="ResourceStatus_<?php echo $k; ?>Id">
				<script type="text/javascript">
					$(function () {
						$("#ResourceStatus_<?php echo $k; ?>").combobox(<?php echo json_encode($arr_equipments_status); ?>);
					});
				</script>
			</li>
			<li class="hg_padd center_txt" style="width:31%">
				<?php echo $this->Form->input('Resource.'.$k.'.note', array(
						'class' => 'input_inner input_inner_w bg'.$i,
						'value' => (isset($value['note']))?$value['note']:''
				)); ?>
			</li>
			<li class="hg_padd center_txt" style="width:1%">
				<div class="middle_check">
					<a href="javascript:void(0)" onclick="tasks_resources_delete('<?php echo $value['_id']; ?>')">
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
			<li class="hg_padd" style="width:19%"></li>
			<li class="hg_padd center_txt" style="width:11%"></li>
			<li class="hg_padd center_txt" style="width:11%"></li>
			<li class="hg_padd center_txt" style="width:6%"></li>
			<li class="hg_padd center_txt" style="width:31%"></li>
			<li class="hg_padd center_txt" style="width:1%"></li>
		</ul>
	<?php  }
	} ?>

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
	$(":input", "#tasks_sub_content").change(function() {

		var form = $(this).closest('form');
		var id = $(this).attr("id");
		id = id.split("_");
		id = id[id.length - 1];
		$(":input", form).removeClass('error_input');
		$.ajax({
			url: '<?php echo URL; ?>/tasks/resources_auto_save',
			timeout: 15000,
			type:"post",
			data: form.serialize(),
			success: function(html){

				if(html == "error_time"){
					$("#ResourceWorkEnd_"+id).addClass('error_input');
					$("#ResourceWorkEndHour_"+id).addClass('error_input');
					alerts("Error: ", '"Work start date" can not greater than "Work end date"');

				}else if(html == "error_work_start"){
					$("#ResourceWorkStart_"+id).addClass('error_input');
					$("#ResourceWorkStartHour_"+id).addClass('error_input');
					alerts("Error: ", '"Work start date" can not lesser than "Task start date"');
				}else if(html != "error_work_end"){
					$("#ResourceWorkEnd_"+id).addClass('error_input');
					$("#ResourceWorkEndHour_"+id).addClass('error_input');
					alerts("Error: ", '"Work end date" can not lesser than "Task end date"');
				}else if(html != "ok"){

					alerts("Error: ", html);
				}

				console.log(html);
			}
		});
	});

});

function after_choose_contacts_resources(contact_id, contact_name){
	$("#window_popup_contacts_resources").data("kendoWindow").close();
	var task_id = "<?php echo $task_id; ?>";
	$.ajax({
		url: "<?php echo URL; ?>/tasks/resources_window_choose/" + task_id + "/Contact/" + contact_id + "/" + contact_name,
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

	$("#window_popup_equipments").data("kendoWindow").close();
	var task_id = "<?php echo $task_id; ?>";
	$.ajax({
		url: "<?php echo URL; ?>/tasks/resources_window_choose/" + task_id + "/Equipment/" + equipment_id + "/" + equipment_name,
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

function tasks_resources_delete(task_id){

	confirms( "Message", "Are you sure you want to delete?",
		function(){
			$.ajax({
				url: '<?php echo URL; ?>/tasks/resources_delete/' + task_id,
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