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
.clear_percent_19 {
width: 20%;
}
.clear_percent_11 {
width: 77%;
}
</style>
<div class="clear_percent">
	<div class="clear_percent_19 float_left">
		<div class="tab_1 full_width changePadding">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('List of modules'); ?></h4></span>
			</span>
			<ul class="ul_mag clear bg3">
				<li class="hg_padd center_text no_border"><?php echo translate('Modules'); ?></li>
			</ul>
			<div id="privilege_height" class="container_same_category" style="height: 449px;overflow-y: auto">
				<ul class="find_list setup_menu">
				<?php $i = 1;$count = 0;
				foreach ($arr_permissions as $key => $value) { $count += 1; ?>
					<li>
						<a class="clickfirst" href="javascript:void(0)" onclick="settings_privilege(this, '<?php echo $value['_id']; ?>')"><?php echo $value['name']; ?></a>
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
	<div class="clear_percent_11 float_left" id="privilege_detail">
		<!-- Detail -->
	</div>
</div>

<style type="text/css">
#privilege_height ul:hover, #privilege_height ul:hover input{
	background-color: #B8B8B8;
}
</style>

<script type="text/javascript">
	$(window).load(function() {
		$('.container_same_category').mCustomScrollbar({
			scrollButtons:{
				enable:false
			}
		});
	});
</script>

<script type="text/javascript">

	$(function(){
		$('.container_same_category').mCustomScrollbar({
			scrollButtons:{
				enable:false
			}
		});
		$(".clickfirst:first", "#privilege_height").click(); // click menu li dau tien khi page load xong
	});

	// click cot 2
	function settings_privilege(object, id){

		$("#privilege_height a").removeClass("active").parents("li").removeClass("active");
		$(object).addClass("active").parents("li").addClass("active");

		// var ul = $(object).parents("ul");
		// $(ul).attr("style", "background-color: #B8B8B8;");
		// $("input", ul).attr("style", "background-color: #B8B8B8;");

		settings_privilege_update(id);
	}

	function settings_privilege_update(id){
		$.ajax({
			url: '<?php echo URL; ?>/settings/privileges_detail/' + id,
			timeout: 15000,
			success: function(html){

				$("div#privilege_detail").html(html);
				$('.container_same_category', "#privilege_detail").mCustomScrollbar({
					scrollButtons:{
						enable:false
					}
				});
				privilege_detail_input_change();
			}
		});
	}

	function settings_privilege_add(id){

		$.ajax({
			url: '<?php echo URL; ?>/settings/privileges_detail_add/' + id,
			timeout: 15000,
			success: function(html){
				if( html == "ok" )
					$("a.active", "#privilege_height").click();
				else
					alerts("Error: ", html);
			}
		});
	}


	function privilege_detail_input_change(){

		$(":input", "#privilege_detail").change(function() {
			privilege_detail_input_change_update(this);

		});
	};

	function privilege_detail_input_change_update(object){

		var ul = $(object).parents("ul");
		$.ajax({
			url: '<?php echo URL; ?>/settings/privileges_detail_auto_save',
			timeout: 15000,
			type:"post",
			data: $(":input", ul).serialize(),
			success: function(html){
				$("#PrivilegePageOld", ul).val( $("#PrivilegePage", ul).val() );

				if( html == "ok_change" ){
					settings_privilege_update($("#PrivilegeId", ul).val());
				}else if( html != "ok" )alerts("Error: ", html);
			}
		});
	}

</script>