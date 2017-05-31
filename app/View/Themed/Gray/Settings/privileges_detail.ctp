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
	</span>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd center_text" style="width:14%">Page</li>
		<li class="hg_padd center_text" style="width:11%">Name</li>
		<li class="hg_padd center_text" style="width:61%">Description</li>
		<li class="hg_padd center_text no_border" style="width:9%">Inactive</li>
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

					<li class="hg_padd" style="width:14%"><?php echo $key_page; ?></li>
					<li class="hg_padd center_text" style="width:11%">
						<input type="text" name="data[Privilege][name]" value="<?php echo $value['name']; ?>" class="input_inner bg<?php echo $i; ?>" />
					</li>
					<li class="hg_padd center_text" style="width:61%">
						<input type="text" name="data[Privilege][description]" value="<?php if(isset($value['description']))echo $value['description'] ?>" class="input_inner bg<?php echo $i; ?>" />
					</li>
					<li class="hg_padd center_text no_border" style="width:9%">
						<input type="hidden" name="data[Privilege][deleted]" value="0" />
						<input type="checkbox" name="data[Privilege][deleted]" value="1" <?php if (isset($value['deleted']) && $value['deleted'] == true) echo 'checked'; ?> />
					</li>
				</ul>
		<?php 		}
				}
			}
		}
		?>
	</div>
	<span class="title_block bo_ra2">
		<span class="float_left bt_block"></span>
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
	<ul class="ul_mag clear bg3">
		<li class="hg_padd center_text" style="width:76%">Option Name</li>
		<li class="hg_padd center_text" style="width:10%">Belong to</li>
		<li class="hg_padd center_text no_border" style="width:9%">Inactive</li>
	</ul>
	<div class="container_same_category" style="height: 220px;overflow-y: auto;">
		<?php $i = 3 - $i; ?>
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

				<li class="hg_padd" style="width:76%;cursor:default;">
					<input type="text" name="data[Optionlist][name]" value="<?php echo $val['name']; ?>" class="input_inner bg<?php echo $i; ?>" />
				</li>
				<li class="hg_padd" style="width:10%;cursor:default; text-align: center;">
					<a href="javascript:void(0)" style="text-align: center;" class="belong_to_module" data-id="<?php echo $arr_privilege['_id'] ?>" data-key="<?php echo $key_column.'_@_'.$key_page.'_@_'.$key; ?>" data-permission-string="<?php echo $arr_privilege['name'].' - '.$val['name']; ?>"><span class="iconw_m " style="float:left;margin-left: 50%;"></span></a>
				</li>
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
			if ($count < 20) {
				$count = 21 - $count;
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
		<span class="float_left bt_block"></span>
	</span>
</div>
<script type="text/javascript">
$(".belong_to_module").click(function(){
	var key = $(this).attr("data-key");
	var id = $(this).attr("data-id");
	var permission_string = $(this).attr("data-permission-string");
	if($("#belong_to_module_window" ).length) return false;
		var url = "<?php echo URL.'/settings/privilege_detail_belong_to_module' ?>/"+id+"/"+key;
	if( $("#belong_to_module_window" ).attr("id") == undefined )
		$('<div id="belong_to_module_window" style="display:none; min-width:300px;"></div>').appendTo("body");
	var loading = '<span style="padding: 19% 0 0 50%;float: left;font-size: xx-large;" >Loading...</span>';
	$("#belong_to_module_window").html(loading);
	belong_to_module = $("#belong_to_module_window");
    belong_to_module.kendoWindow({
    	iframe: false,
        actions: ["Close"],
        content: url,
        resizable: false,
		title: permission_string,
		deactivate: function(e) {
            this.destroy()
        }
    }).data("kendoWindow").center().open();
});
</script>