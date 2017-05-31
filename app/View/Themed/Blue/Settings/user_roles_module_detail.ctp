<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent">
			<h4 id="setting_name"><?php
				echo translate('Modules: ');
				if (isset($arr_persmiss['name']))
					echo $arr_persmiss['name'];
				?>
			</h4>
		</span>
	</span>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd center_text" style="width:18%">Page</li>
		<li class="hg_padd center_text" style="width:12%">Name</li>
		<li class="hg_padd center_text" style="width:40%">Description</li>
		<li class="hg_padd center_text no_border" style="width:25%">Select</li>
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

		if( isset($arr_persmiss['permission']) ){
			foreach ($arr_persmiss['permission'] as $roles) {
				ksort($roles);
				foreach ($roles as $key_page => $page) {
					usort($page, "cmp");
					foreach ($page as $key => $value) {
						$count += 1;
						$i = 3 - $i;

						if( isset($value['deleted']) && $value['deleted'] )continue;
			?>

				<ul class="ul_mag clear bg<?php echo $i; ?>">

					<li class="hg_padd" style="width:18%"><?php echo $key_page; ?></li>
					<li class="hg_padd" style="width:12%"><?php echo $value['name']; ?></li>
					<li class="hg_padd" style="width:40%"><?php if(isset($value['description']))echo $value['description']; ?></li>
					<li class="hg_padd center_text no_border" style="width:25%">

						<?php 
							$value_role = $arr_persmiss['controller'].'_@_'.$key_page.'_@_'.$value['codekey']; 

						?>

						<input name="contact_id" type="hidden" value="<?php echo $contact_id; ?>">
						<input name="permission_id" type="hidden" value="<?php echo $permission_id; ?>">
						<input name="permission_path" type="hidden" value="<?php echo $value_role; ?>">
						<input name="controller" type="hidden" value="<?php echo $arr_persmiss['controller']; ?>">
						<select name="ownership" class="ownership">
							<option value="">Disabled</option>
							<option value="all" <?php if( (isset($arr_user_roles[$value_role]) && in_array('all',$arr_user_roles[$value_role]) ) ){ ?>selected="selected"<?php } ?> >All</option>
							<!-- <option value="group" <?php if( (isset($arr_user_roles[$value_role]) && in_array('group',$arr_user_roles[$value_role]) ) ){ ?>selected="selected"<?php } ?>>Group</option> -->
							<option value="owner" <?php if( (isset($arr_user_roles[$value_role]) && in_array('owner',$arr_user_roles[$value_role]) ) ){ ?>selected="selected"<?php } ?>>Owner</option>
						</select>
						<a href="javascript:void(0)" style="cursor: pointer" class="custom_link" data-permission="<?php echo $value_role; ?>" data-permission-string="<?php echo $arr_persmiss['controller'].' / '.$key_page.' / '.$value['codekey'];  ?>">Custom</a>
					</li>
				</ul>
		<?php 		}
				}
			}
		} ?>

		<?php
			if ($count < 8) {
				$count = 6 - $count;
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
		<li class="hg_padd center_text" style="width:82%">Option Name</li>
		<li class="hg_padd center_text no_border" style="width:15%">Select</li>
	</ul>
	<div class="container_same_category" style="height: 223px;overflow-y: auto">
		<?php $count = 0;
			foreach($arr_persmiss['option_list'] as $roles){
				foreach($roles as $key=>$value){
					foreach($value as $val){
						if( isset($val['deleted']) && $val['deleted'] )continue;
						$count += 1;
						$i = 3 - $i;
						// $option_name = ucfirst(str_replace('_', ' ', $key));
		?>
			<ul class="ul_mag clear bg<?php echo $i; ?>">
				<li class="hg_padd" style="width:82%;cursor:default;"><?php echo $val['name']; ?></li>
				<li class="hg_padd center_text no_border" style="width:15%">
					<?php $value_role = $arr_persmiss['controller'].'_@_options_@_'.$val['codekey']; ?>
					<input name="contact_id" type="hidden" value="<?php echo $contact_id; ?>">
					<input name="permission_path" type="hidden" value="<?php echo $value_role; ?>">
					<input name="controller" type="hidden" value="<?php echo $arr_persmiss['controller']; ?>">
					<input type="checkbox" class="option_child role_module_detail" value="all" <?php if( isset($arr_user_roles[$value_role])   ){ ?>checked="checked"<?php } ?> />
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
			$count = 10 - $count;
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
	$(".custom_link").click(function(){
		var permission = $(this).attr("data-permission");
		var permission_string = $(this).attr("data-permission-string");
		if($("#custom_permission_window" ).length) return false;
 		var url = "<?php echo URL.'/settings/user_roles_module_detail_custom_permission' ?>/"+permission;
		if( $("#custom_permission_window" ).attr("id") == undefined )
			$('<div id="custom_permission_window" style="display:none; min-width:300px;"></div>').appendTo("body");
		var loading = '<span style="padding: 19% 0 0 50%;float: left;font-size: xx-large;" >Loading...</span>';
		$("#custom_permission_window").html(loading);
		custom_permission = $("#custom_permission_window");
        custom_permission.kendoWindow({
	    	iframe: false,
	        actions: ["Close"],
	        content: url,
	        resizable: false,
			title: 'Custom permission - '+permission_string,
			deactivate: function(e) {
                this.destroy()
            }
	    }).data("kendoWindow").center().open();
	});

	$(".option_child ").change(function(){
		var check;
		if($(this).is(":checked"))
			check = 'true';
		else
			check = 'false';
		var parent = $(this).parent();
		$.ajax({
			url : "<?php echo URL; ?>/settings/user_roles_module_detail_option_save/"+check,
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
			url : "<?php echo URL; ?>/settings/user_roles_module_detail_auto_save/",
			type: "POST",
			data: $(":input,:checkbox", parent).serialize(),
			success: function(html){
				if( html != "ok" )
					alerts("Message: ", html);
			}
		});
	});
</script>