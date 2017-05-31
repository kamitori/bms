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
#user_roles_height ul:hover, #user_roles_height ul:hover input{
	background-color: #B8B8B8;
}
.no-image{
	background: none !important;
}
</style>
<div class="clear_percent">
	<div class="clear_percent_19 float_left" style="width: 18%;">
		<div class="tab_1 full_width changePadding">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('Employees'); ?></h4></span>
			</span>
			<ul class="ul_mag clear bg3">
				<li class="hg_padd center_text no_border"><?php echo translate('Full Name'); ?></li>
			</ul>
			<div id="user_roles_height" class="container_same_category" style="height: 449px;overflow-y: auto">
				<ul class="find_list setup_menu">
				<?php $i = 2; $count = 0;
				foreach ($contacts as $key => $value) { $count += 1; ?>
					<li>
						<a class="clickfirst no-image" href="javascript:void(0)" onclick="window.location.assign('<?php echo URL.'/settings?page=user_roles&user_id='. $value['_id']; ?>')"><?php echo $value['first_name'].' '.$value['last_name']; ?></a>
					</li>
				<?php $i = 3 - $i;
				}
				echo '</ul>';

				if ($count < 20) {
					$count = 20 - $count;
					for ($j = 0; $j < $count; $j++) {
				?>
						<ul class="find_list setup_menu">
						</ul>
				<?php
						$i = 3 - $i;
					}
				}
			?>

			</div>
			<span class="title_block bo_ra2"></span>
		</div><!--END Tab1 -->
	</div>
	<div class="clear_percent_9_arrow float_left">
		<div class="full_width box_arrow">
			<span class="icon_emp" style="cursor:default"></span>
		</div>
	</div>
	<div class="clear_percent_19 float_left" style="width:20%">
		<div class="tab_1 full_width changePadding">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo $roles['name']; ?></h4></span>
			</span>
			<ul class="ul_mag clear bg3">
				<li class="hg_padd center_text no_border"><?php echo translate('List of modules'); ?></span></li>
			</ul>
			<div id="roles_module_height" class="container_same_category" style="height: 449px;overflow-y: auto">
				<ul class="find_list setup_menu">
				<?php $i = 1;$count = 0;
				foreach ($arr_permissions as $key => $value) { $count += 1; ?>
					<li>
						<a class="clickfirst" href="javascript:void(0)" onclick="settings_roles_module(this, '<?php echo $value['_id']; ?>')"><?php echo $value['name']; ?></a>
					</li>

				<?php $i = 3 - $i;
				}
				echo '</ul>';

				if ($count < 20) {
				$count = 20 - $count;
				for ($j = 0; $j < $count; $j++) {
					$i = 3 - $i;
					?>
					<ul class="find_list setup_menu">
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
		<div class="full_width box_arrow">
			<span class="icon_emp" style="cursor:default"></span>
		</div>
	</div>
	<div class="clear_percent_11 float_left" id="roles_module_detail" style="width: 58%;">
		<!-- Detail -->
	</div>
</div>

<style type="text/css">
#roles_module_height ul:hover, #roles_module_height ul:hover input{
	background-color: #B8B8B8;
}
</style>

<script type="text/javascript">

	$(function(){

		$('.container_same_category').mCustomScrollbar({
			scrollButtons:{
				enable:false
			}
		});

		$(".clickfirst:first", "#roles_module_height").click();
	});

	// click cot 2
	function settings_roles_module(object, id){

		$("#roles_module_height a").removeClass("active").parents("li").removeClass("active");
		$(object).addClass("active").parents("li").addClass("active");

		var ul = $(object).parents("ul");
		$(ul).attr("style", "background-color: #B8B8B8;");
		$("input", ul).attr("style", "background-color: #B8B8B8;");

		$.ajax({
			url: '<?php echo URL; ?>/settings/roles_module_detail/<?php echo $role_id; ?>/' + id,
			timeout: 15000,
			success: function(html){
				$("div#roles_module_detail").html(html);
				$(".container_same_category", "div#roles_module_detail").mCustomScrollbar({
					scrollButtons:{
						enable:false
					}
				});
			}
		});
	}
</script>