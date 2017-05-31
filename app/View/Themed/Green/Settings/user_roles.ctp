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
	<div class="clear_percent_19 float_left" style="width: 24%;">
		<div class="tab_1 full_width changePadding">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('List of employees'); ?></h4></span>
			</span>
			<ul class="ul_mag clear bg3">
				<li class="hg_padd center_text no_border"><?php echo translate('Full Name'); ?></li>
			</ul>
			<div id="user_roles_height" class="container_same_category" style="height: 449px;overflow-y: auto">
				<ul class="find_list setup_menu">
				<?php
					$i = 2;
					$count = 0;
					foreach ($arr_employees as $value) {
						$count++;
				?>
					<li>
						<a class="clickfirst" data-id="<?php echo $value['_id']; ?>" href="javascript:void(0)" onclick="settings_roles(this, '<?php echo $value['_id']; ?>')"><?php echo $value['full_name']; ?></a>
					</li>
				<?php
						$i++;
					}
				?>
				</ul>
				<?php
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
	<div class="clear_percent_11 float_left" id="user_roles_module">
		<!-- Detail -->
	</div>
</div>

<style type="text/css">
#user_roles_height ul:hover, #user_roles_height ul:hover input{
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
		if(window.location.href.indexOf("&user_id=") != -1){
			var user_id = window.location.href.split("&user_id=");
			user_id = user_id[user_id.length - 1];
			findUser(user_id);
		}
		else
			findCurrentUser();<?php //Tung, them 13/01/2014 ?>
	});

	function settings_roles(object, id){
		$("#user_roles_height a").removeClass("active").parents("li").removeClass("active");
		$(object).addClass("active").parents("li").addClass("active");

		// lấy ra danh sách module tương ứng với Role được click
		$.ajax({
			url: '<?php echo URL; ?>/settings/user_roles_module/' + id,
			timeout: 15000,
			success: function(html){
				// Cập nhật danh sách module vào div
				$("div#user_roles_module").html(html);
			}
		});
	}
	function findUser(id) {
		$("a.clickfirst","#user_roles_height").each(function(){
			if($(this).attr("data-id")==id){
				found = true;
				$("#roles_module_height a").removeClass("active").parent("li").removeClass("active");
				$(this).addClass("active").parent("li").addClass("active");
				$(this).click();
				$(".container_same_category").mCustomScrollbar("scrollTo",$(this).offset().top);
				return false;
			}
		});
	}
	function findCurrentUser(){
		var name = "<?php echo $_SESSION['arr_user']['contact_name'] ?>";
		var found = false;
		$("a.clickfirst","#user_roles_height").each(function(){
			if($(this).html()==name){
				found = true;
				$("#roles_module_height a").removeClass("active").parent("li").removeClass("active");
				$(this).addClass("active").parent("li").addClass("active");
				$(this).click();
				$(".container_same_category").mCustomScrollbar("scrollTo",$(this).offset().top);
				return false;
			}
		});
		if(!found)
			$(".clickfirst:first", "#user_roles_height").click();
	}
</script>