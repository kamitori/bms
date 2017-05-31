<style type="text/css">
	ul.ul_mag li.hg_padd {
	overflow: visible !important;
	}
	.bg4 {
	background: none repeat scroll 0 0 #949494;
	color: #fff;
	}
	.bg4 span h4 {
	margin-left: 1%;
	width: 100%;
	}

</style>
<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent">
			<h4>Assets</h4>
		</span>
		<a title="Add new equipment" href="javascript:void(0)" onclick="settings_equipment_add()">
			<span class="icon_down_tl top_f"></span>
		</a>
	</span>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd" style="width:30%"><?php echo translate('Asset'); ?></li>
		<li class="hg_padd" style="width:10%"><?php echo translate('UOM'); ?></li>
		<li class="hg_padd" style="width:7%"><?php echo translate('Speed/hour'); ?></li>
		<li class="hg_padd" style="width:27%"><?php echo translate('Description'); ?></li>
		<li class="hg_padd center_txt" style="width:6%"><?php echo translate('Color'); ?></li>
		<li class="hg_padd line_mg center_txt" style="width:10%"><?php echo translate('Inactive'); ?></li>
	</ul>
	<div class="container_same_category" style="height: 449px;overflow-y: auto">

		 <?php $i = 1; $j=0; $count = 0;
		foreach ($arr_equipments as $key => $value) {
			$i = 3 - $i; $count += 1;
		?>

		<?php echo $this->Form->create('Equipment', array('id' => 'EquipmentForm_'.$key)); ?>
		<?php echo $this->Form->hidden('Equipment._id', array( 'value' => $value['_id'] )); ?>
		<ul class="ul_mag clear bg<?php echo $i; ?>">
			<li class="hg_padd line_mg" style="width:30%">
				<?php echo $this->Form->input('Equipment.name', array(
						'class' => 'input_inner input_inner_w bg'.$i,
						'value' => (isset($value['name']))?$value['name']:''
				)); ?>
			</li>
			<li class="hg_padd line_mg" style="width:10%">
				<input type="text" name="data[Equipment][uom]" value="<?php echo (isset($value['uom']) ? $value['uom'] : ''); ?>" class="input_inner input_inner_w bg<?php echo $i; ?>" id="EquipmentUom_<?php echo $j; ?>" />
				<input type="hidden" name="data[Equipment][uom_id]" id="EquipmentUom_<?php echo $j; ?>Id" value="" />
				<script type="text/javascript">
					$(function () {
						$("#EquipmentUom_<?php echo $j; ?>").combobox(<?php echo json_encode($arr_uom); ?>);
					});
				</script>
			</li>
			<li class="hg_padd line_mg" style="width:7%">
				<?php echo $this->Form->input('Equipment.speed_per_hour', array(
						'class' => 'input_inner input_inner_w  right_txt bg'.$i,
						'value' => (isset($value['speed_per_hour']) ? $value['speed_per_hour'] : ''),
						'onKeyPress'=> 'return isPrice(event);',
				)); ?>
			</li>
			<li class="hg_padd line_mg" style="width:27%">
				<?php echo $this->Form->input('Equipment.description', array(
						'class' => 'input_inner input_inner_w   bg'.$i,
						'value' => (isset($value['description']) ? $value['description'] : '')
				)); ?>
			</li>
			<li class="hg_padd line_mg center_txt" style="width:6%">
				<img src="<?php echo URL; ?>/img/color_icon.png" onclick="open_alerts('<?php if(isset($value['name']))echo $value['name']; ?>', '<?php echo $value['_id']; ?>')" style="cursor: pointer;" title="View color of status of this asset">

				<!-- STATUS COLOR -->
				<div id="equipment_color_<?php echo $value['_id']; ?>" style="display:none">
					<div style="width: 300px">
						<ul class="ul_mag clear bg3">
							<li class="hg_padd" style="width:60%"><?php echo translate('Status'); ?></li>
							<li class="hg_padd center_txt" style="width:37%"><?php echo translate('Color'); ?></li>
						</ul>
						<?php foreach ($arr_tasks_status as $key_status => $value_status) { ?>
						<ul class="ul_mag clear" style="height: 30px;">
							<li class="hg_padd" style="width:60%;"><?php echo $value_status; ?></li>
							<li class="hg_padd center_txt" style="width:37%;">

								<?php $color = '';
									if(isset($value['color'][$key_status]))$color = $value['color'][$key_status];
								?>
								<input rel="<?php echo $key_status; ?>" name="status_color" class="color-picker input_inner input_inner_w" value="<?php echo $color; ?>" type="text">
							</li>
						</ul>
						<?php } ?>
					</div>
				</div>
				<!-- END -->

			</li>
			<li class="hg_padd line_mg center_txt position-relative" style="width:10%">
				<label class="m_check2 cene">
					<?php echo $this->Form->input('Equipment.deleted', array(
							'type' => 'checkbox',
							'checked' => (isset($value['deleted']))?$value['deleted']:''
					));?>
					<span></span>
				</label>
			</li>
		</ul>
		<?php $j++; echo $this->Form->end(); ?>

		<?php
		} ?>

		<?php if( $count < 20 ){
				$count = 20 - $count;
				for ($j=0; $j < $count; $j++) {
					$i = 3 - $i;
		?>
		<ul class="ul_mag clear bg<?php echo $i; ?>">
		</ul>
		<?php }
		} ?>

	</div>
	<span class="title_block bo_ra2">
		<span class="float_left bt_block"><?php echo translate('Edit or create values for list'); ?>.</span>
	</span>
</div>

<script type="text/javascript">
	$(function(){

		$('.container_same_category').mCustomScrollbar({
			scrollButtons:{
				enable:false
			}
		});

		$("form :input", "#detail_for_main_menu").change(function() {
			$.ajax({
				url: '<?php echo URL; ?>/settings/equipments_auto_save',
				timeout: 15000,
				type:"post",
				data: $(this).closest('form').serialize(),
				success: function(html){
					console.log(html);
					if( html != "ok" )alerts("Error: ", html);
				}
			});
		});
	});

	function settings_equipment_add(){
		$.ajax({
			url: '<?php echo URL; ?>/settings/equipments_add',
			timeout: 15000,
			success: function(html){
				$("#detail_for_main_menu").html(html);
			}
		});
	}

	function settings_kendo_enable_colorpicker(contain, equipment_id){
		$(".color-picker", contain).kendoColorPicker({
			// value: "#ffffff",
			buttons: false
			// select: list_and_menu_detail_input_change_update
		});

		$(".color-picker", contain).each(function(){
			var input = $(this);
			var colorPicker = $(this).data("kendoColorPicker");
			colorPicker.bind({
				// select: function(e) {
				//     kendoConsole.log("Select in picker #" + this.element.attr("id") + " :: " + e.value);
				// },
				change: function(e) {
					var object = $(this.element);
					object.val(e.value);
					$.ajax({
						url: '<?php echo URL; ?>/settings/equipments_auto_save/' +e.value + "/" + equipment_id,
						timeout: 15000,
						type: 'post',
						data: { equipment_id: equipment_id, color:e.value, status: input.attr("rel") },
						success: function(html){
							if( html != "ok" )alerts("Error: ", html);
						}
					});
				}
				// ,
				// open: function() {
				//     kendoConsole.log("Open in picker #" + this.element.attr("id"));
				// },
				// close: function() {
				//     kendoConsole.log("Close in picker #" + this.element.attr("id"));
				// }
			});
		});
	}

	function open_alerts(name, id){
		var html = $("#equipment_color_" + id).html();
		popup_show(name, html);
		settings_kendo_enable_colorpicker("#alerts_right_footer_window", id);
	}
</script>