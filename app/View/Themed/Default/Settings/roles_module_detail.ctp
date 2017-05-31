<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent">
			<h4 id="setting_name"><?php
				echo translate('Modules: ');
				if (isset($arr_permission['name']))
					echo $arr_permission['name'];
				?>
			</h4>
		</span>
	</span>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd center_text" style="width:16%">Page</li>
		<li class="hg_padd center_text" style="width:14%">Name</li>
		<li class="hg_padd center_text" style="width:40%">Description</li>
		<li class="hg_padd center_text no_border" style="width:21%">
			<label for"checkall">All/Disable all</label>
			<input type="checkbox" id="checkall">
		</li>
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
		if( isset($arr_permission['permission']) ){
			foreach ($arr_permission['permission'] as $roles) {
				ksort($roles);
				foreach ($roles as $key_page => $page) {
					if(is_array($page)){
						usort($page, "cmp");
						foreach ($page as $key => $value) {
							if( isset($value['deleted']) && $value['deleted'] )continue;
							$i = 3 - $i;
							$count += 1;
			?>

				<ul class="ul_mag clear bg<?php echo $i; ?>">

					<li class="hg_padd" style="width:16%"><?php echo $key_page; ?></li>
					<li class="hg_padd" style="width:14%"><?php echo $value['name']; ?></li>
					<li class="hg_padd" style="width:40%"><?php if(isset($value['description']))echo $value['description']; ?></li>
					<li class="hg_padd center_text no_border" style="width:21%">
						<?php $value_role = $arr_permission['controller'].'_@_'.$key_page.'_@_'.$value['codekey']; ?>
						<?php
							// Kiem tra toan quyen tren Module
							$check_role = false;
							if( isset($arr_roles['all']) ){
								$check_role = true;
							}
						?>

						<input name="role_id" type="hidden" value="<?php echo $role_id; ?>">
						<input name="permission_path" type="hidden" value="<?php echo $value_role; ?>">
						<input name="controller" type="hidden" value="<?php echo $arr_permission['controller']; ?>">
						<select name="ownership" class="ownership">
							<option value="">Disabled</option>
							<option value="all" <?php if( $check_role || (isset($arr_roles[$value_role]) && in_array('all',$arr_roles[$value_role]) ) ){ ?>selected="selected"<?php } ?> >All</option>
							<option value="group" <?php if( $check_role || (isset($arr_roles[$value_role]) && in_array('group',$arr_roles[$value_role]) ) ){ ?>selected="selected"<?php } ?>>Group</option>
							<option value="owner" <?php if( $check_role || (isset($arr_roles[$value_role]) && in_array('owner',$arr_roles[$value_role]) ) ){ ?>selected="selected"<?php } ?>>Owner</option>
						</select>
					</li>
				</ul>
		<?php 			}
					}
				}
			}
		} ?>
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
		<span class="float_left bt_block"></span>
	</span>
</div>

<!-- OPTION -->
<div class="tab_1 full_width" style="margin-top:1%">
	<span class="title_block bo_ra1">
		<span class="fl_dent">
			<h4 id="setting_name"><?php echo translate('Option'); ?>
			</h4>
		</span>
	</span>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd center_text" style="width:88%">Option Name</li>
		<li class="hg_padd center_text no_border" style="width:9%">Select</li>
	</ul>
	<div class="container_same_category" style="height: 220px;overflow-y: auto">
		<?php $count = 0;
			foreach($arr_permission['option_list'] as $roles){
				foreach($roles as $key=>$value){
					foreach($value as $val){
						if( isset($val['deleted']) && $val['deleted'] )continue;
						$count += 1;
						$i = 3 - $i;
						// $option_name = ucfirst(str_replace('_', ' ', $key));
		?>
			<ul class="ul_mag clear bg<?php echo $i; ?>">
				<li class="hg_padd" style="width:88%;cursor:default;"><?php echo $val['name']; ?></li>
				<li class="hg_padd center_text no_border" style="width:9%">
					<?php if( !isset($val['permission']) ){ ?>
					<?php $value_role = $arr_permission['controller'].'_@_options_@_'.$val['codekey']; ?>
					<input name="role_id" type="hidden" value="<?php echo $role_id; ?>">
					<input name="permission_path" type="hidden" value="<?php echo $value_role; ?>">
					<input name="controller" type="hidden" value="<?php echo $arr_permission['controller']; ?>">
					<input type="checkbox" class="option_child role_module_detail" value="all" <?php if( $check_role || isset($arr_roles[$value_role])   ){ ?>checked="checked"<?php } ?>>
					<?php }else{ ?>
						<input type="checkbox" class="option_child role_module_detail" value="all" disabled="disabled">
					<?php } ?>
				</li>
			</ul>
		<?php
					}
				}
			}
		?>
		<?php
			$count++;
		if ($count < 10) {
			$count = 11 - $count;
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
	/*function option_list_permission(object){
		var li = object.parents("li");
		$.ajax({
			url: '<?php echo URL; ?>/settings/roles_module_detail_option_list/',
			timeout: 15000,
			type:"post",
			data: $(":input, :checkbox", li).serialize(),
			success: function(html){
				if( html != "ok" )
					alerts("Message: ", html);
			}
		});
	}*/
	$("#checkall").change(function(){
		if($(this).is(":checked"))
			check ='true';
		else
			check ='false';
		$.ajax({
			url : "<?php echo URL; ?>/settings/roles_modules_detail_choose_all/"+check,
			type: "POST",
			data: {controller: "<?php echo $arr_permission['controller'] ?>", role_id : "<?php echo $role_id; ?>"},
			success: function(html){
				if( html != "ok" )
					alerts("Message: ", html);
				else {
					if(check == "true"){
						$(".ownership ").val("all");
						$(".option_child").prop("checked",true);
					} else {
						$(".ownership ").val("");
						$(".option_child").prop("checked",false);
					}

				}
			}
		})
	});
	$(".option_child ").change(function(){
		var check;
		if($(this).is(":checked"))
			check = 'true';
		else
			check = 'false';
		var parent = $(this).parent();
		$.ajax({
			url : "<?php echo URL; ?>/settings/roles_modules_detail_option_save/"+check,
			type: "POST",
			data: $(":input,:checkbox", parent).serialize(),
			success: function(html){
				if( html != "ok" )
					alerts("Message: ", html);
			}
		});
	})
	$(".ownership").change(function(){
		var parent = $(this).parent();
		$.ajax({
			url : "<?php echo URL; ?>/settings/roles_modules_detail_auto_save/",
			type: "POST",
			data: $(":input,:checkbox", parent).serialize(),
			success: function(html){
				if( html != "ok" )
					alerts("Message: ", html);
			}
		});
	});
</script>