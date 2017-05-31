<style type="text/css">
#confirms_window .jt_confirms_window_ok{
	width: 15%;
	height: 18%;
	margin-top: 14%;
	margin-left: 80%;
}
.changePadding ul li{
	padding-left: 4px !important;
}
</style>
<div class="clear_percent">
	<div class="clear_percent_19 float_left" id="roles_module_height">
		<div class="tab_1 full_width changePadding">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('List of role & modules'); ?></h4></span>
			</span>

			<!-- ROLES -->
			<ul class="ul_mag clear bg3">
				<li class="hg_padd center_text" style="width:69%;border: none;"><?php echo translate('Roles'); ?></li>
				<li class="hg_padd center_text" style="width:22%;border: none;">Select</li>
			</ul>

			<div class="container_same_category" style="height: 150px;overflow-y: auto">
				<?php $i = 1;$count = 0;
				foreach ($arr_roles as $key => $value) {

					if($value['name'] == 'System Admin' && !isset($system_admin))continue;

					$count += 1; ?>
				<ul class="ul_mag clear bg<?php echo $i; ?>" id="system_<?php echo $value['_id']; ?>">
					<li class="hg_padd" style="width: 69%;border: none;">
							<?php echo $value['name']; ?>
						</li>
					<?php
						// Kiểm tra quyền với từng role
						$has_role_right = false;
						if ( isset($arr_user_roles['roles']) ){
							foreach ($arr_user_roles['roles'] as $roles_id) {
								if( (string)$roles_id == (string)$value['_id'] ){
									$has_role_right = true;
									break;
								}
							}
						}
					?>
					<li class="hg_padd center_text" style="width:22%;border: none;">
						<input type="checkbox" value="1" <?php if ($has_role_right) echo 'checked'; ?> onchange="settings_user_roles_all_permission(this, '<?php echo $value['_id']; ?>')" />
						</li>
				</ul>
				<?php $i = 3 - $i;
				}
			?>
			</div>
			<span class="title_block bo_ra2"></span>
		</div><!--END Tab1 -->

		<!-- OPTION -->
		<div class="tab_1 full_width changePadding" style="margin-top:1%">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('Specify additional permissions'); ?></h4></span>
			</span>
			<!-- MODULES -->
			<ul class="ul_mag clear bg3">
				<li class="hg_padd center_text no_border"><?php echo translate('Modules'); ?></li>
			</ul>

			<div id="roles_module_height" class="container_same_category" style="height: 226px;overflow-y: auto">
				<ul class="find_list setup_menu">
				<?php $i = 1;$count = 0;
				foreach ($arr_permissions as $key => $value) { $count += 1; ?>
				<li>
					<a class="clickfirst" href="javascript:void(0)" onclick="settings_roles_module_detail(this, '<?php echo $value['_id']; ?>')"><?php echo $value['name']; ?></a>
				</li>
				<?php $i = 3 - $i;
				}
				echo '</ul>';

				if ($count < 16) {
				$count = 16 - $count;
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
			<span class="title_block bo_ra2"></span>
		</div><!--END Tab1 -->
	</div>
	<div class="clear_percent_9_arrow float_left">
		<div class="full_width box_arrow" style="padding-top:330px">
			<span class="icon_emp" style="cursor:default"></span>
		</div>
	</div>
	<div class="clear_percent_11 float_left" id="roles_module_detail">
		<!-- Detail -->
	</div>
</div>

<script type="text/javascript">

	$(function(){
		$('.container_same_category', "#user_roles_module").mCustomScrollbar({
			scrollButtons:{
				enable:false
			}
		});
	});

	// click lên li cột thứ 2
	function settings_roles_module_detail(object, id){

		$("#roles_module_height a").removeClass("active").parents("li").removeClass("active");
		$(object).addClass("active").parents("li").addClass("active");

		var ul = $(object).parents("ul");
		$(ul).addClass("active_li2");
		$("input", ul).attr("style", "background-color: #B8B8B8;");

		$.ajax({
			url: '<?php echo URL; ?>/settings/user_roles_module_detail/<?php echo $contact_id; ?>/' + id,
			timeout: 15000,
			success: function(html){
				$("div#roles_module_detail").html(html);
				$('.container_same_category', "#roles_module_detail").mCustomScrollbar({
					scrollButtons:{
						enable:false
					}
				});
			}
		});
	}

	// click radio box cột 3
	/*function roles_module_detail_input_change(){
		$(":input", "#roles_module_detail").change(function() {
			if($(this).attr("id")=="option_parent") return false;
			roles_module_detail_input_change_run_ajax($(this), "true");
		});
	};

	function roles_module_detail_input_change_run_ajax(object, user_change){
		var ul = object.parents("ul");
		$.ajax({
			url: '<?php echo URL; ?>/settings/user_roles_module_detail_set_permission/' + object.prop("checked"),
			timeout: 15000,
			type:"post",
			data: $(":input, :checkbox", ul).serialize(),
			success: function(html){
				if( html != "ok" ){
					alerts("Message: ", html);
				}else{

					if( user_change == "true" ){
						// nếu stick vào delete thì có luôn quyền view và edit
						if( object.attr("id") == ("checkbox_" + object.attr("rel") + "_delete" ) ){
							var edit = $("#checkbox_" + object.attr("rel") + "_edit" );
							if(edit.attr("id") != undefined && !edit.prop("checked") ){
								edit.prop("checked", true);
								roles_module_detail_input_change_run_ajax(edit);
							}
							var view = $("#checkbox_" + object.attr("rel") + "_view" );
							if(view.attr("id") != undefined && !view.prop("checked") ){
								view.prop("checked", true);
								roles_module_detail_input_change_run_ajax(view);
							}
						}else if( object.attr("id") == ("checkbox_" + object.attr("rel") + "_edit" ) ){

							// nếu có quyền edit thì có luôn view
							if( object.prop("checked") ){
								var view = $("#checkbox_" + object.attr("rel") + "_view" );
								if(view.attr("id") != undefined && !view.prop("checked") ){
									view.prop("checked", true);
									roles_module_detail_input_change_run_ajax(view);
								}
							}else{ // nếu bỏ quyền edit thì bỏ delete luôn
								var input_delete = $("#checkbox_" + object.attr("rel") + "_delete" );
								if(input_delete.attr("id") != undefined && input_delete.prop("checked") ){
									input_delete.prop("checked", false);
									roles_module_detail_input_change_run_ajax(input_delete);
								}
							}

						}else if( object.attr("id") == ("checkbox_" + object.attr("rel") + "_view" ) ){

							// nếu có quyền view thì có luôn add
							if( !object.prop("checked") ){
								var add = $("#checkbox_" + object.attr("rel") + "_add" );
								if(add.attr("id") != undefined && add.prop("checked") ){
									add.prop("checked", false);
									roles_module_detail_input_change_run_ajax(add);
								}

								var edit = $("#checkbox_" + object.attr("rel") + "_edit" );
								if(edit.attr("id") != undefined && edit.prop("checked") ){
									edit.prop("checked", false);
									roles_module_detail_input_change_run_ajax(edit);
								}

								var input_delete = $("#checkbox_" + object.attr("rel") + "_delete" );
								if(input_delete.attr("id") != undefined && input_delete.prop("checked") ){
									input_delete.prop("checked", false);
									roles_module_detail_input_change_run_ajax(input_delete);
								}
							}

						}else if( object.attr("id") == ("checkbox_" + object.attr("rel") + "_add" ) ){

							// nếu có quyền add thì có luôn view
							if( object.prop("checked") ){
								var view = $("#checkbox_" + object.attr("rel") + "_view" );
								if(view.attr("id") != undefined && !view.prop("checked") ){
									view.prop("checked", true);
									roles_module_detail_input_change_run_ajax(view);
								}
							}
						}
					}
				}
			}
		});
	}*/

	// click checkbox cột 2
	function settings_user_roles_all_permission(object, role_id){
		$.ajax({
			url: '<?php echo URL; ?>/settings/user_roles_set_all_permission/<?php echo $contact_id; ?>/' + role_id + "/" + $(object).prop("checked"),
			timeout: 15000,
			success: function(html){
				if( html != "ok" ){
					alerts("Message: ", html);
				}else{
					$("a.active", "#roles_module_height").click();
				}
			}
		});
	}
</script>