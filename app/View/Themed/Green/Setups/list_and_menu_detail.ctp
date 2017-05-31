
<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent">
			<h4 id="setting_name"><?php
				if (isset($arr_setting['setting_value'])) {
					echo $arr_setting['setting_value'];
				}
				?>
			</h4>
		</span>

		<?php if (!isset($arr_setting['system_admin'] ) || !$arr_setting['system_admin']) { ?>
			 <a title="Add new list" href="javascript:void(0)" onclick="settings_list_menu_detail_add('<?php echo $arr_setting['_id']; ?>')">
				<span class="icon_down_tl top_f"></span>
			</a>
		<?php } ?>

	<div class="new_select_black" style="width:30%; float:right; margin-right:8px;">
	    <div class="new_select_black_left">&nbsp;</div>
	    <div class="new_select_black_center" style="width:94%;">
	        <input type="text" id="language_list" combobox_blank="1" value="<?php if(isset($arr_language_value)) echo $arr_language[$arr_language_value];?>" />
	        <input type="hidden" id="language_listId" value="<?php if(isset($arr_language_value)) echo $arr_language_value;?>" />
	     	<script type="text/javascript">
				$(function () {
					$("#language_list").combobox(<?php if(isset($arr_language)) echo json_encode($arr_language);?>);
				});
			</script>
	    </div>
	    <div class="new_select_black_right">&nbsp;</div>
	</div>

<!-- 		<span>
			<form id="form_comms" class="float_right hbox_form" style="width:130px; height:10px; margin: -3px 16px 0px 0px;" >
				<select>
					<option value="1">English</option>
					<option value="2">Viet Nam</option>
				</select>
			 </form>
		</span> -->
	</span>

	<ul class="ul_mag clear bg3">
		<?php
		$all_field_of_option = array();
		foreach (end($arr_setting['option']) as $key => $value) {
			$all_field_of_option[] = $key;
		}
		if($arr_setting['setting_name']!='email_setting'):
		?>
		<li class="hg_padd center_text" style="width:32%">Name <!-- <em style="font-weight: normal; font-size:11px">(allow to edit)</em> --></li>
		<li class="hg_padd center_text" style="width:46%">Value</li>
		<?php if( isset($value['color']) || $arr_setting['setting_value'] == 'tasks_status' ){ ?>
		<li class="hg_padd center_text" style="width:8%">Color</li>
		<?php } ?>
		<?php if( isset($value['rate']) || $arr_setting['setting_value'] == 'currency_type' ){ ?>
		<li class="hg_padd center_text" style="width:8%">Rate</li>
		<?php } ?>
		<li class="hg_padd center_text " style="width:6%">Inactive</li>

		<?php else: ?>
		<li class="hg_padd center_text" style="width:20%">Name <!-- <em style="font-weight: normal; font-size:11px">(allow to edit)</em> --></li>
		<li class="hg_padd center_text" style="width:30%">Host</li>
		<li class="hg_padd center_text" style="width:25%">Port</li>
		<li class="hg_padd center_text no_border" style="width:6%">Inactive</li>
		<?php endif; ?>
	</ul>

	<input type="hidden" id="all_field_of_option" value="<?php echo implode('_._', $all_field_of_option); ?>">

	<div class="container_same_category" style="height: 449px;overflow-y: auto">
		<?php
		$stt = 1;
		$i = 1;
		$count = count($arr_setting['option']);
		if($arr_setting['setting_value']!='email_setting'):
		usort($arr_setting['option'], function($a ,$b){
			return $a['name'] > $b['name'];
		});
		foreach ($arr_setting['option'] as $key => $value) {
			$i = 3 - $i;
			?>
			<?php echo $this->Form->create('Setting', array('id' => 'SettingForm_' . $key)); ?>
			<?php echo $this->Form->hidden('Setting.option_key', array('value' => $key)); ?>
			<?php echo $this->Form->hidden('Setting._id', array('value' => $arr_setting['_id'])); ?>

			<ul class="ul_mag clear bg<?php echo $i; ?>">

				<li class="hg_padd center_text" style="width:32%">
					<?php
						foreach($arr_language as $k => $v){
							$names = 'name_'.$k;
							if($k==DEFAULT_LANG)
								$names = 'name';

							$types = 'hidden';
							if($k==$arr_language_value){
								$types = 'text';
							}
					?>
							<input type="<?php echo $types;?>" name="Setting[<?php echo $names;?>]" value="<?php if(isset($value[$names])) echo $value[$names]; else echo ''; ?>" class="input_inner bg<?php echo $i; ?>" <?php if(isset($arr_setting['system_admin']) && $arr_setting['system_admin']) echo 'readonly'; ?> />
					<?php } ?>
				</li>

				<li class="hg_padd" style="width:46%">
					<input type="text" name="Setting[value]" value="<?php echo $value['value'] ?>" class="input_inner bg<?php echo $i; ?>" <?php if(isset($arr_setting['system_admin']) && $arr_setting['system_admin']) echo 'readonly'; ?> />
				</li>

				<?php if( isset($value['color']) ){ ?>
				<li class="hg_padd center_text" style="width:8%">
					<input name="Setting[color]" class="color-picker input_inner input_inner_w" value="<?php echo $value['color']; ?>" type="text">
				</li>
				<?php } ?>

				<?php if( isset($value['rate']) ){ ?>
				<li class="hg_padd center_text" style="width:8%">
					<input type="text" name="Setting[rate]" value="<?php echo $value['rate'] ?>" class="input_inner bg<?php echo $i; ?>" <?php if(isset($arr_setting['system_admin']) && $arr_setting['system_admin']) echo 'readonly'; ?> style="text-align:right;" />
				</li>
				<?php } ?>

				<li class="hg_padd center_text" style="width:6%">
					<input type="hidden" name="Setting[deleted]" value="0" />
					<input <?php if(isset($arr_setting['system_admin']) && $arr_setting['system_admin']) echo 'disabled'; ?> type="checkbox" name="Setting[deleted]" value="1" <?php if (isset($value['deleted']) && $value['deleted'] == TRUE) echo 'checked'; ?> />
				</li>
				<li class="hg_padd center_text delete_list" style="width:2%" id="delete_list_<?php echo $arr_setting['_id']?>_<?php echo $key?>" >
					<div class="middle_check">
	                    <span class="icon_remove2"></span>
		            </div>
				</li>

			</ul>
			<?php echo $this->Form->end(); ?>
		<?php }
		else:
		foreach ($arr_setting['option'] as $key => $value) {
			$i = 3 - $i;
		?>
			<?php echo $this->Form->create('Setting', array('id' => 'SettingForm_' . $key)); ?>
			<?php echo $this->Form->hidden('Setting.option_key', array('value' => $key)); ?>
			<?php echo $this->Form->hidden('Setting._id', array('value' => $arr_setting['_id'])); ?>
			<ul class="ul_mag clear bg<?php echo $i; ?>">
				<li class="hg_padd center_text" style="width:10%"><?php echo $stt++; ?></li>
				<li class="hg_padd center_text" style="width:20%">
					<input type="text" name="Setting[name]" value="<?php echo $value['name']; ?>" onkeyup="$(this).val($(this).val().toUpperCase())" class="input_inner bg<?php echo $i; ?>"/>
				</li>
				<li class="hg_padd center_text" style="width:30%">
					<input type="text" name="Setting[value][host]" value="<?php echo $value['value']['host'] ?>" class="input_inner bg<?php echo $i; ?>" <?php if(isset($arr_setting['system_admin']) && $arr_setting['system_admin']) echo 'readonly'; ?> />
				</li>
				<li class="hg_padd center_text" style="width:25%">
					<input type="text" name="Setting[value][port]" onkeypress="return isNumbers(event);" value="<?php echo $value['value']['port'] ?>" class="input_inner bg<?php echo $i; ?>" />
				</li>
				<li class="hg_padd center_text" style="width:6%">
					<input type="hidden" name="Setting[deleted]" value="0" />
					<input type="checkbox" name="Setting[deleted]" value="1" <?php if (isset($value['deleted']) && $value['deleted'] == TRUE) echo 'checked'; ?> />
				</li>
			</ul>
		<?php }
			echo $this->Form->end();
		endif;
		?>
		<?php
		if ($count < 20) {
			$count = 20 - $count;
			if($arr_setting['setting_name']!='email_setting'):
			for ($j = 0; $j < $count; $j++) {
				$i = 3 - $i;
				?>
				<ul class="ul_mag clear bg<?php echo $i; ?>">
				</ul>
				<?php
			}
			else:
			for ($j = 0; $j < $count; $j++) {
				$i = 3 - $i;
				?>
				<ul class="ul_mag clear bg<?php echo $i; ?>">
				</ul>
				<?php
			}
			endif;
		}
		?>
	</div>
	<span class="title_block bo_ra2">
		<span class="float_left bt_block"><?php echo translate('Edit or create values for list'); ?>.</span>
	</span>
</div>

<script type="text/javascript">
$(function () {


	$("#language_list").change(function(){
		var lang = $("#language_listId").val();
		var actions = $("#SettingForm_0").attr('action');
		var ids = actions.split('/');
		var lengths = ids.length-2;
		ids = ids[lengths];
		settings_list_menu_detail(null, ids,lang);
	});

	$(".delete_list").click(function(){
        var ids = $(this).attr("id");
        ids = ids.split("_");
        var key = ids[ids.length - 1];
        ids = ids[ ids.length - 2];
        confirms( "Message", "Are you sure you want to delete?",
        function(){
            $.ajax({
                url: '<?php echo URL; ?>/settings/list_menu_delete/' + ids +'/'+ key,
                success: function(html){
                    if(html == "ok"){
                        $("li:last","#system_"+ids).click();
                    }else{
                        alerts("Message",html);
                    }
                }
            });
        },function(){
            //else do somthing
        });
    });
});


	function settings_add_admin() {
		// setting_name = $('#setting_admin').attr('title');
		// $.post("<?php echo URL . '/settings/set_admin/' ?>", {setting_name: setting_name}, function(data) {
		// 	alert(data);
		// });
	}
</script>

