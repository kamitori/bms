<style type="text/css">
	.k-picker-wrap,.k-selected-color{
		height:15px;
		line-height: 15px;
	}
	.k-hsv-rectangle{
		margin-top: 15px;
	}
</style>
<div id="detail" class="tab_1 full_width">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4 id="setting_name"><?php
				if (isset($arr_system['setting_name'])) {
					echo ucwords(str_replace('_', ' ',$arr_system['setting_name']));
				}
				?>
            </h4>
        </span>
        <a title="Link a contact" href="javascript:void(0)" onclick="system_detail_add('<?php echo $arr_system['_id']; ?>')">
            <span class="icon_down_tl top_f"></span>
        </a>

    </span>

    <?php
       	if(@$arr_system['setting_name'] == "tasks_status"):
    ?>
    <ul class="ul_mag clear bg3">
    	<li class="hg_padd center_text" style="width:3%">ID</li>
		<li class="hg_padd center_text" style="width:30%">Value <!-- <em style="font-weight: normal; font-size:11px">(allow to edit)</em> --></li>
		<li class="hg_padd center_text" style="width:25%">Color</li>
		<li class="hg_padd center_text" style="width:10%">Deleted</li>
		<li class="hg_padd center_text" style="width:15%">Moveable Cal.</li>
		<li class="hg_padd center_text" style="width:10%">Show</li>
    </ul>

    <div class="container_same_category" style="height: 449px;overflow-y: auto" id="list_province_detail">
		<?php echo $this->Form->create('Setting'); ?>
			<input type="hidden" name="Setting[_id]" value="<?php echo $arr_system['_id']; ?>" />
		<?php
			$stt = 1;
			$i = 1;
			$count = count($arr_system);
			foreach ($arr_system['option'] as $key=>$values) {
				$i = 3 - $i;
		?>

			<ul class="ul_mag clear bg<?php echo $i; ?>">
				<li class="hg_padd center_text" style="width:3%"><?php echo $stt++; ?></li>
				<li class="hg_padd center_text" style="width:30%">
					<center><input type="text"  style="text-align:center"  name="Setting[option][<?php echo $key; ?>][value]" value="<?php echo $values['value']; ?>" class="input_inner bg<?php echo $i; ?>"/></center>
				</li>
				<li class="hg_padd center_text" style="width:25%;line-height:20px">
					<input type="text" style="text-align:center" name="Setting[option][<?php echo $key; ?>][color]" class="color_picker" value="<?php echo $values['color']; ?>" class="input_inner bg<?php echo $i; ?>"/>
				</li>
				<li class="hg_padd center_text" style="width:10%">
					<input type="hidden" name="Setting[option][<?php echo $key; ?>][deleted]" value="0" />
					<input type="checkbox" name="Setting[option][<?php echo $key; ?>][deleted]" value="1" <?php if ($values['deleted'] == TRUE) echo 'checked'; ?> />
				</li>
				<li class="hg_padd center_text" style="width:15%">
					<input type="hidden" name="Setting[option][<?php echo $key; ?>][cal_enabled_move]" value="0" />
					<input type="checkbox" name="Setting[option][<?php echo $key; ?>][cal_enabled_move]" value="1" <?php if ($values['cal_enabled_move'] == TRUE) echo 'checked'; ?> />
				</li>
				<li class="hg_padd center_text" style="width:10%">
					<input type="hidden" name="Setting[option][<?php echo $key; ?>][show]" value="0" />
					<input type="checkbox" name="Setting[option][<?php echo $key; ?>][show]" value="1" <?php if ($values['show'] == TRUE) echo 'checked'; ?> />
				</li>
			</ul>
			<?php
			}
    	endif;
    ?>
    <?php
		if ($count < 20) {
			$count = 20 - $count;
			for ($j = 0; $j < $count; $j++) {
				$i = 3 - $i;
				?>
				<ul class="ul_mag clear bg<?php echo $i; ?>">
			    	<li class="hg_padd center_text" style="width:3%"></li>
					<li class="hg_padd center_text" style="width:30%"></li>
					<li class="hg_padd center_text" style="width:25%"></li>
					<li class="hg_padd center_text" style="width:10%"></li>
					<li class="hg_padd center_text" style="width:15%"></li>
					<li class="hg_padd center_text" style="width:10%"></li>
				</ul>
				<?php
			}
		}
		?>

			<?php echo $this->Form->end(); ?>
    </div>
    <span class="title_block bo_ra2">
        <span class="float_left bt_block"><?php echo translate('Edit or create values for list'); ?>.</span>
    </span>
</div>
<script type="text/javascript">
function settings_kendo_enable_colorpicker(){
        $(".color_picker", "div#detail").kendoColorPicker({
            value: "#ffffff",
            buttons: false,
            tileSize: { width: 30 }

        });

        $(".color_picker", "div#detail").each(function(){
            var colorPicker = $(this).data("kendoColorPicker");
            colorPicker.bind({
                change: function(e) {
                    var object = $(this.element);
                    object.val(e.value);
                    system_detail_input_change_update(object);
                }

            });
        });
    }


</script>
