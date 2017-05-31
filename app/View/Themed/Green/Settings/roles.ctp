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
	<div class="clear_percent_19 float_left" style="width: 14%;">
		<div class="tab_1 full_width changePadding">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('List of roles'); ?></h4></span>

				<a title="Add new role" href="javascript:void(0)" onclick="settings_roles_add()">
					<span class="icon_down_tl top_f"></span>
				</a>

			</span>
			<ul class="ul_mag clear bg3">
				<li class="hg_padd center_text no_border"><?php echo translate('Name'); ?></li><!--  style="width:70.3%" -->
				<!-- <li class="hg_padd center_text" style="width:10%">All</li> -->
				<!-- <li class="hg_padd center_text" style="width:9.1%"></li> -->
			</ul>
			<div id="roles_height" class="container_same_category" style="height: 449px;overflow-y: auto">
				<?php $i = 2; $count = 0;
				foreach ($arr_roles as $key => $value) {

					if($value['name'] == 'System Admin')continue;
					$count += 1; ?>
				<!-- Chinh sua lai ko dung All  -->
				<ul class="ul_mag clear bg<?php echo $i; ?>" id="system_<?php echo $value['_id']; ?>">
					<li class="hg_padd" style="width: 70.3%;">
						<input type="hidden" name="_id" value="<?php echo $value['_id']; ?>">
						<input type="text" name="name" value="<?php if(isset($value['name'])){ echo $value['name']; } ?>" class="input_inner bg<?php echo $i; ?>">
						</li>

					<!-- <li class="hg_padd center_text" style="width:10%">
						<input id="Role_<?php echo $value['_id']; ?>" type="checkbox" name="data[Privilege][deleted]" value="1" <?php if (isset($value['value']) && isset($value['value']['all'])) echo 'checked'; ?> onchange="settings_roles_all_permission('<?php echo $value['_id']; ?>', this)" />
					</li> -->

					<li class="hg_padd center_text clickfirst no_border" style="width: 22.1%;cursor:pointer" onclick="settings_roles(this, '<?php echo $value['_id']; ?>')">
						<span class="icon_emp"></span></li>
				</ul>
				<?php $i = 3 - $i;
				}

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
			<span class="title_block bo_ra2"><i>Click Name to edit</i></span>
		</div><!--END Tab1 -->
	</div>
	<div class="clear_percent_11 float_left" id="roles_module" style="width: 82%;">
		<!-- Detail -->
	</div>
</div>

<style type="text/css">
#roles_height ul:hover, #roles_height ul:hover input{
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

		// gán Input change vào danh sách Role đang hiển thị
		roles_input_change();

		$("li.clickfirst:first", "#roles_height").click();
	});

	// click li cot 1 de view
	function settings_roles(object, id){
		$("#roles_height ul").attr("style", "");
		$("input", "#roles_height").attr("style", "");

		var ul = $(object).parents("ul");
		$(ul).attr("style", "background-color: #B8B8B8;");
		$("input", ul).attr("style", "background-color: #B8B8B8;font-weight:bold;");

		// lấy ra danh sách module tương ứng với Role được click
		$.ajax({
			url: '<?php echo URL; ?>/settings/roles_module/' + id,
			timeout: 15000,
			success: function(html){
				// Cập nhật danh sách module vào div
				$("div#roles_module").html(html);
			}
		});
	}

	// click add cot 1
	function settings_roles_add(id){
		$.ajax({
			url: '<?php echo URL; ?>/settings/roles_add/' + id,
			timeout: 15000,
			success: function(html){
				$("#roles").click();
			}
		});
	}

	// click checkbox cot 2
	function settings_roles_all_permission(id, object){
		$.ajax({
			url: '<?php echo URL; ?>/settings/roles_set_all_permission/' + id + "/" + $(object).prop("checked"),
			timeout: 15000,
			success: function(html){
				if(html == "ok")
					// lấy ra danh sách module tương ứng
					$.ajax({
						url: '<?php echo URL; ?>/settings/roles_module/' + id,
						timeout: 15000,
						success: function(html){
							$("div#roles_module").html(html);
						}
					});
				else
					alerts("Error: ", html);
			}
		});
	}

	// doi ten cot 1
	function roles_input_change(){
		$(":input", "#roles_height").change(function() {
			var object = $(this);
			var ul = $(object).parents("ul");
			$.ajax({
				url: '<?php echo URL; ?>/settings/roles_auto_save',
				timeout: 15000,
				type:"post",
				data: $(":input", ul).serialize(),
				success: function(html){
					if( html != "ok" )
						alerts("Error: ", html);
				}
			});

		});
	};
</script>