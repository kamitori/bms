<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent">
			<h4 id="setting_name"><?php
				echo translate('Module: ');
				if (isset($arr_privilege['name']))
					echo $arr_privilege['name'];
				?>
			</h4>
		</span>
		<a title="Add new permission" href="javascript:void(0)" onclick="settings_privilege_add('<?php echo $arr_privilege['_id']; ?>')">
			<span class="icon_down_tl top_f"></span>
		</a>
	</span>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd center_text" style="width:24%">Page</li>
		<li class="hg_padd center_text" style="width:11%">Name</li>
		<li class="hg_padd center_text" style="width:11%">Name</li>
		<li class="hg_padd center_text" style="width:33%">Description</li>
		<li class="hg_padd center_text" style="width:9%">Inactive</li>
	</ul>

	<div class="container_same_category" style="height: 150px;overflow-y: auto">
		<?php
		$stt = 1;
		$i = 1;
		$count = 0;

		if(!function_exists('cmp')){
			function cmp($a, $b)
			{
				return strcmp($a["name"], $b["name"]);
			}
		}

		if( isset($arr_privilege['permission']) ){
			foreach ($arr_privilege['permission'] as $privilege) {
				ksort($privilege);
				foreach ($privilege as $key_page => $page) {
					// usort($page, "cmp");
					foreach ($page as $key => $value) {
						$i = 3 - $i;
						$count += 1;
						// if($key_page == 'tab_addresses')pr($page);
			?>

				<ul class="ul_mag clear bg<?php echo $i; ?>" id="Permission_<?php echo $key_page; ?>_<?php echo $arr_privilege['_id']; ?>_<?php echo $key; ?>">

					<?php echo $this->Form->hidden('Privilege._id', array('value' => $arr_privilege['_id'])); ?>
					<?php echo $this->Form->hidden('Privilege.option_key', array('value' => $key)); ?>
					<?php echo $this->Form->hidden('Privilege.page_old', array('value' => $key_page)); ?>
					<?php echo $this->Form->hidden('Privilege.page', array('value' => $key_page)); ?>
					<?php echo $this->Form->hidden('Privilege.codekey', array('value' => $value['codekey'])); ?>

					<li class="hg_padd" style="width:24%">
						<input type="text" name="data[Privilege][page]" value="<?php echo $key_page; ?>" class="input_inner bg<?php echo $i; ?>" /></li>
					<li class="hg_padd center_text" style="width:11%">
						<input type="text" name="data[Privilege][name]" value="<?php echo $value['name']; ?>" class="input_inner bg<?php echo $i; ?>" />
					</li>
					<li class="hg_padd center_text" style="width:11%">
						<input type="text" name="data[Privilege][codekey]" value="<?php echo $value['codekey']; ?>" class="input_inner bg<?php echo $i; ?>" />
					</li>
					<li class="hg_padd center_text" style="width:33%">
						<input type="text" name="data[Privilege][description]" value="<?php if(isset($value['description']))echo $value['description'] ?>" class="input_inner bg<?php echo $i; ?>" />
					</li>
					<li class="hg_padd center_text" style="width:9%">
						<input type="hidden" name="data[Privilege][deleted]" value="0" />
						<input type="checkbox" name="data[Privilege][deleted]" value="1" <?php if (isset($value['deleted']) && $value['deleted'] == true) echo 'checked'; ?> />
					</li>
				</ul>
		<?php 		}
				}
			}
		}
		?>
		<?php
			$count++;
			if ($count < 8) {
				$count = 7 - $count;
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

<!-- OPTION -->
<div class="tab_1 full_width" style=" margin-top:1%">
	<span class="title_block bo_ra1">
		<span class="fl_dent">
			<h4 id="setting_name"><?php echo translate('Option');?>
			</h4>
		</span>
	</span>
		<?php $count = 0; $i = 3 - $i; ?>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd center_text" style="width:21%">Name</li>
		<li class="hg_padd center_text" style="width:21%">Codekey</li>
		<li class="hg_padd center_text" style="width:21%">Finish</li>
		<li class="hg_padd center_text" style="width:21%">Permission</li>
		<li class="hg_padd center_text no_border" style="width:9%">Inactive</li>
	</ul>
	<div class="container_same_category" style="height: 220px;overflow-y: auto;">
		<?php $count += 1;
			foreach($arr_privilege['option_list'] as $key_column => $roles){
				foreach($roles as $key_page=> $value){
					foreach($value as $key=>$val){
						$count += 1;
						$i = 3 - $i;
		?>
			<ul class="ul_mag clear bg<?php echo $i; ?>">

				<?php echo $this->Form->hidden('Optionlist._id', array('value' => $arr_privilege['_id'])); ?>
				<?php echo $this->Form->hidden('Optionlist.option_key', array('value' => $key)); ?>
				<?php echo $this->Form->hidden('Optionlist.key_column', array('value' => $key_column)); ?>
				<?php echo $this->Form->hidden('Optionlist.key_page', array('value' => $key_page)); ?>
				<?php echo $this->Form->hidden('Optionlist.description', array('value' => '')); ?>

				<li class="hg_padd" style="width:21%;cursor:default;">
					<input type="text" name="data[Optionlist][name]" value="<?php echo $val['name']; ?>" class="input_inner bg<?php echo $i; ?>" /></li>
				<li class="hg_padd" style="width:21%;cursor:default;">
					<input type="text" name="data[Optionlist][codekey]" value="<?php if(isset($val['codekey']))echo $val['codekey']; ?>" class="input_inner bg<?php echo $i; ?>" /></li>
				<li class="hg_padd" style="width:21%;cursor:default;">
					<input type="text" name="data[Optionlist][finish]" value="<?php if(isset($val['finish']))echo $val['finish']; ?>" class="input_inner bg<?php echo $i; ?>" /></li>
				<li class="hg_padd" style="width:21%;cursor:default;">
					<input type="text" name="data[Optionlist][permission]" value="<?php if(isset($val['permission']))echo $val['permission']; ?>" class="input_inner bg<?php echo $i; ?>" /></li>
				<li class="hg_padd center_text no_border" style="width:9%">
					<input type="hidden" name="data[Optionlist][deleted]" value="0" />
					<input type="checkbox" name="data[Optionlist][deleted]" value="1" <?php if (isset($val['deleted']) && $val['deleted']) echo 'checked'; ?> />
				</li>
			</ul>
		<?php
					}
				}
			}
		?>
		<?php
			$count++;
			if ($count < 11) {
				$count = 12 - $count;
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