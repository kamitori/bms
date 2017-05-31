
<div class="tab_1 full_width">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4 id="setting_name"><?php
				if (isset($arr_country['name'])) {
					echo $arr_country['name'];
				}
				?>
            </h4>
        </span>

        <a title="Add new province" href="javascript:void(0)" onclick="settings_list_province_add('<?php echo $arr_country['value']; ?>')">
            <span class="icon_down_tl top_f"></span>
        </a>

    </span>
    <ul class="ul_mag clear bg3">
		<li class="hg_padd center_text" style="width:10%">ID</li>
		<li class="hg_padd center_text" style="width:32%">Name <!-- <em style="font-weight: normal; font-size:11px">(allow to edit)</em> --></li>
		<li class="hg_padd center_text" style="width:46%">Value</li>
		<li class="hg_padd center_text" style="width:6%">Inactive</li>
    </ul>

    <div class="container_same_category" style="height: 449px;overflow-y: auto" id="list_province_detail">
		<?php
		$stt = 1;
		$i = 1;
		$count = 0;
		foreach ($arr_provinces as $value) {
			$i = 3 - $i; $count += 1;
			?>
			<?php echo $this->Form->create('Setting'); ?>
			<input type="hidden" name="Setting[_id]" value="<?php echo $value['_id']; ?>" />
			<ul class="ul_mag clear bg<?php echo $i; ?>">
				<li class="hg_padd center_text" style="width:10%"><?php echo $stt++; ?></li>
				<li class="hg_padd center_text" style="width:32%">
					<input type="text" name="Setting[name]" value="<?php echo $value['name']; ?>" class="input_inner bg<?php echo $i; ?>"/>
				</li>
				<li class="hg_padd center_text" style="width:46%">
					<input type="text" name="Setting[key]" value="<?php echo $value['key'] ?>" class="input_inner bg<?php echo $i; ?>" <?php //if($value['value']) echo 'readonly';         ?> />
				</li>
				<li class="hg_padd center_text" style="width:6%">
					<input type="hidden" name="Setting[deleted]" value="0" />
					<input type="checkbox" name="Setting[deleted]" value="1" <?php if ($value['deleted'] == TRUE) echo 'checked'; ?> />
				</li>
			</ul>
			<?php echo $this->Form->end(); ?>
		<?php }
		?>

		<?php
		if ($count < 20) {
			$count = 20 - $count;
			for ($j = 0; $j < $count; $j++) {
				$i = 3 - $i;
				?>
				<ul class="ul_mag clear bg<?php echo $i; ?>">
				</ul>
				<?php
			}
		}
		?>
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
	});
</script>
