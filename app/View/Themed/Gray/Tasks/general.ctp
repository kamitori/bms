	<div class="clear_percent_18 float_left" id="noteactivity">
		<?php if(isset($noteactivity)){
				echo $noteactivity;
			}else{
				echo $this->element('noteactivity');
			} ?>
	</div>
	<div class="clear_percent_16 float_left">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('Contacts related to this tasks'); ?></h4></span>
				<?php if($this->Common->check_permission($controller.'_@_entry_@_edit', $arr_permission)){ ?>
				<a id="click_open_window_contacts_generals" href="javascript:void(0)" title="Add new contact"><span class="icon_down_tl top_f"></span></a>
				<?php } ?>
			</span>
			<ul class="ul_mag clear bg3">
				<li class="ct"><?php echo translate('Contact'); ?></li>
				<li class="mg"><?php echo translate('Main'); ?></li>
				<li class="emp"></li>
			</ul>

			<style type="text/css">
			ul.ul_mag li.mg{
				position: relative;
			}
			</style>

			<div id="tasks_entry_general_contacts" class="container_same_category small_height7">

				<?php
				$i = 1; $count = 0; $contacts_default_key = 0;
				if( isset($arr_task['contacts']) ){
					foreach ($arr_task['contacts'] as $key => $value) {
						if( $value['deleted'] )continue;
						$i = 3 -$i; $count += 1;

						$checked = false;
						if($arr_task['contacts_default_key'] == $key){
							$checked = true;
							$contacts_default_key = $key;
						}
					?>
					<ul class="ul_mag clear bg<?php echo $i; ?>" id="tasks_general_<?php echo $key; ?>">
						<li class="ct bof">
							<span class="input_inner_w float_left"><?php echo $value['contact_name']; ?></span>

							<a href="<?php echo URL; ?>/contacts/entry/<?php echo $value['contact_id']; ?>" title="Link to contact"><span class="icon_viw"></span></a>
							<!-- <a href="javascript:void(0)" title="Create email"><span class="icon_emaili chan indent_viw2"></span></a> -->
						</li>
						<li class="mg bof">
							<label class="m_check2 cene">
								<?php echo $this->Form->input('Task.main', array(
										'type' => 'checkbox',
										'checked' => $checked,
										'onchange' => 'tasks_entry_general_manager(this, '.$key.', "'.$value['contact_id'].'", "'.$value['contact_name'].'")'
								)); ?>
								<span></span>
							</label>
						</li>
						<li class="emp">
							<div class="middle_check2">
								<a title="Delete link" href="javascript:void(0)" onclick="tasks_general_contact_delete(<?php echo $key; ?>)">
									<span class="icon_remove2"></span>
								</a>
							</div>
						</li>
					</ul>
					<?php } ?>
				<?php }
				$count = 4 - $count;
				if( $count > 0 ){
					for ($j=0; $j < $count; $j++) { $i = 3 -$i;
						echo '<ul class="ul_mag clear bg'.$i.'"><li class="ct"></li><li class="mg"></li><li class="emp"></li></ul>';
					}
				}
				?>
			</div>

			<span class="title_block bo_ra2">
				<span class="icon_vwie indent_down_vwie2"><a href=""><?php echo translate('View contact'); ?></a></span>
			</span>
		</div><!--END Tab1 -->
		<div class="tab_1 full_width block_dent9">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('Alerts'); ?></h4></span>
				<!-- <a href="" title="Link a contact"><span class="icon_down_tl top_f"></span></a> -->
			</span>
			<ul class="ul_mag clear bg3">
				<li class="ct" style="width:50%"></li>
				<li class="mg" style="width:15%"></li>
				<li class="mg" style="width:19%"></li>
				<li class="emp"></li>
			</ul>
			<div class="container_same_category small_height7 mCustomScrollbar _mCS_3">
				<ul class="ul_mag clear bg1">
					<li class="ct bof" style="width:50%"></li>
					<li class="mg bof" style="width:15%">
						<input class="input_inner input_inner_w center_txt" type="text" name="">
					</li>
					<li class="mg" style="width:19%"></li>
					<li class="emp">
						<!-- <div class="middle_check2">
							<a title="Delete link" href="">
								<span class="icon_remove2"></span>
							</a>
						</div> -->
					</li>
				</ul>
				<ul class="ul_mag clear bg2">
					<li class="ct bof" style="width:50%"></li>
					<li class="mg bof" style="width:15%">
						<input class="input_inner input_inner_w center_txt bg_transperent" type="text" name="">
					</li>
					<li class="mg" style="width:19%"></li>
					<li class="emp">
						<!-- <div class="middle_check2">
							<a title="Delete link" href="">
								<span class="icon_remove2"></span>
							</a>
						</div> -->
					</li>
				</ul>
				<ul class="ul_mag clear bg1">
					<li class="ct bof" style="width:50%"></li>
					<li class="mg bof" style="width:15%">
						<input class="input_inner input_inner_w center_txt" type="text" name="">
					</li>
					<li class="mg" style="width:19%"></li>
					<li class="emp">
						<!-- <div class="middle_check2">
							<a title="Delete link" href="">
								<span class="icon_remove2"></span>
							</a>
						</div> -->
					</li>
				</ul>
				<ul class="ul_mag clear bg2">
					<li class="ct bof" style="width:50%"></li>
					<li class="mg bof" style="width:15%">
						<input class="input_inner input_inner_w center_txt bg_transperent" type="text" name="">
					</li>
					<li class="mg" style="width:19%"></li>
					<li class="emp">
						<!-- <div class="middle_check2">
							<a title="Delete link" href="">
								<span class="icon_remove2"></span>
							</a>
						</div> -->
					</li>
				</ul>

			</div>
			<span class="title_block bo_ra2">
				<span class="icon_vwie indent_down_vwie2"><a href=""><?php echo translate('View contact'); ?></a></span>
			</span>
		</div><!--END Tab1 -->
	</div>
	<div class="clear_percent_17 float_right">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="float_left">
					<span class="fl_dent"><h4><?php echo translate('Timelog budget related'); ?></h4></span>
				</span>
			</span>
			<div class="tab_2_inner">
				<p class="clear">
					<span class="label_1 float_left minw_lab2"></span>
					</p><div class="width_in3a float_left indent_input_tp">
						<input readonly="true"class="input_1 float_left" type="text" value="">
					</div>

				<p class="clear">
					<span class="label_1 float_left minw_lab2"></span>
					</p><div class="width_in3a float_left indent_input_tp">
						<input readonly="true"class="input_1 float_left" type="text" value="">
					</div>

				<p class="clear">
					<span class="label_1 float_left minw_lab2"></span>
					</p><div class="width_in3a float_left indent_input_tp">
						<input readonly="true"class="input_1 float_left" type="text" value="">
					</div>

				<p class="clear">
					<span class="label_1 float_left minw_lab2"> </span>
					</p><div class="width_in3a float_left indent_input_tp">
						<input readonly="true"class="input_1 float_left" type="text" value="">
					</div>

				<p class="clear">
					<span class="label_1 float_left minw_lab2"> </span>
					</p><div class="width_in3a float_left indent_input_tp">
						<input readonly="true"class="input_1 float_left" type="text" value="">
					</div>
			   <!--  <p class="clear">
					<span class="label_1 float_left minw_lab2">  </span>
					</p><div class="width_in3a float_left indent_input_tp">
						<div class="in_active2">
							<label class="m_check2">
								<input readonly="true"type="checkbox">
								<span class="bx_check dent_chk"></span>
							</label>
							<span class="float_left dent_check color_hidden">  </span>
							<p class="clear"></p>
						</div>
					</div> -->

				<p class="clear">
					<span class="label_1 float_left minw_lab2"></span>
					</p><div class="width_in3a float_left indent_input_tp">
						<input readonly="true"class="input_1 float_left" type="text" value="">
					</div>

				<p class="clear">
					<span class="label_1 float_left minw_lab2"> </span>
					</p><div class="width_in3a float_left indent_input_tp">
						<input readonly="true"class="input_1 float_left" type="text" value="">
					</div>

				<!-- <p class="clear">
					<span class="label_1 float_left minw_lab2">  </span>
					</p><div class="width_in3a float_left indent_input_tp">
						<div class="once_colum2">
							<input readonly="true"type="text" class="input_2 float_left">
						</div>
						<div class="two_colum2">
							<input readonly="true"class="input_1 color_hidden" type="text" value="">
						</div>
					</div> -->

				<p class="clear">
					<span class="label_1 float_left minw_lab2"> </span>
					</p><div class="width_in3a float_left indent_input_tp">
						<input readonly="true"class="input_1 float_left" type="text" value="">
					</div>

				<p class="clear">
					<span class="label_1 float_left minw_lab2"> </span>
					</p><div class="width_in3a float_left indent_input_tp">
						<input readonly="true"class="input_1 float_left" type="text" value="">
					</div>

				<p class="clear">
					<span class="label_1 float_left minw_lab2 fixbor10"> </span>
					</p><div class="width_in3a float_left indent_input_tp">
						<input readonly="true"class="input_1 float_left" type="text" value="">
					</div>

				<p class="clear"></p>
			</div>
			<span class="title_block bo_ra2"></span>
		</div><!--END Tab1 -->
	</div>

<script type="text/javascript">

jQuery.fn.outer = function() {
	return $($('<div></div>').html(this.clone())).html();
}

$(function(){

	<?php if($this->Common->check_permission($controller.'_@_entry_@_edit', $arr_permission)){ ?>
	window_popup('contacts', 'Specify contact', '_generals', "", "?is_employee=1");
	<?php } ?>

	<?php if( $contacts_default_key != 0 ){ ?>
		var outer = $("#tasks_general_<?php echo $contacts_default_key; ?>").outer();
		$("#tasks_general_<?php echo $contacts_default_key; ?>").remove();
		$("#tasks_entry_general_contacts").prepend(outer);
		refesh_ul_color("#tasks_entry_general_contacts");
	<?php } ?>

});

function after_choose_contacts_generals(contact_id, contact_name){

	$("#window_popup_contacts_generals").data("kendoWindow").close();
	$.ajax({
		url: "<?php echo URL; ?>/tasks/general_window_contact_choose/<?php echo $task_id; ?>/" + contact_id + "/" + contact_name,
		timeout: 15000,
		success: function(html){
			console.log(html);
			if(html == "ok"){
				$("#general").click();
			}else{
				alerts("Error: ", html);
			}
		}
	});
	return false;
}

function tasks_entry_general_manager(object, option_id, contact_id, contact_name){

	if( !$( object ).prop("checked") ){
		$( object ).prop("checked", true);
		return false;
	}

	var task_id = "<?php echo $task_id; ?>";

	$( "input[type=checkbox]", "#tasks_entry_general_contacts").prop("checked", false);
	$( object ).prop("checked", true);

	$.ajax({
		url: "<?php echo URL; ?>/tasks/general_choose_manager/" + task_id + "/" + option_id,
		timeout: 15000,
		success: function(html){
			console.log(html);
			if(!html == "ok"){
				alerts("Error: ", html);
			}

			// refesh
			var outer = $("#tasks_general_" + option_id).outer();
			$("#tasks_general_"  + option_id).remove();
			$("#tasks_entry_general_contacts").prepend(outer);
			$( "#TaskMain", "#tasks_general_" + option_id ).prop("checked", true);
			refesh_ul_color("#tasks_entry_general_contacts");
		}
	});

	// Đổi lại responsible luôn
	// $("#TaskOurRepId").val(contact_id);
	// $("#TaskOurRep").val(contact_name);
	// tasks_auto_save_entry();

	return false;
}

function tasks_general_contact_delete(key){

	confirms( "Message", "Are you sure you want to delete?",
		function(){
			$.ajax({
				url: '<?php echo URL; ?>/tasks/general_delete_contact/<?php echo $task_id; ?>/'+ key,
				success: function(html){
					if(html == "ok"){
						$("#tasks_general_" + key).fadeOut();
						refesh_ul_color("#tasks_entry_general_contacts");
					}else{
						console.log(html);
					}
				}
			});
		},function(){
			//else do somthing
	});
}


</script>