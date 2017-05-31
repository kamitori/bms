<div class="clear_percent">
	<div class="clear_percent_19 float_left">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('Specify values for lists used in system'); ?></h4></span>
			</span>
			<div class="title_small_once">
				<ul class="ul_mag clear bg3">
					<li class="hg_padd">
						<?php echo translate('Messsage type'); ?> <span class="normal">(<?php echo translate('click a name to view or edit on right'); ?>)</span>
					</li>
				</ul>
			</div>
			<div id="list_and_menu_height" class="container_same_category" style="height: 449px;overflow-y: auto">
				<ul class="find_list setup_menu">
					<li onclick="settings_listmenu_detail(this, 'static')">
						<a href="javascript:void(0)" class="active">
							<?php echo translate('static');?>
						</a>
					</li>
					<li onclick="settings_listmenu_detail(this,'message_box')">
						 <a href="javascript:void(0)">
							   <?php echo translate('message_box');?>
						 </a>
					</li>
				</ul>
			</div>
			<span class="title_block bo_ra2"></span>
		</div><!--END Tab1 -->
	</div>
	<div class="clear_percent_9_arrow float_left">
		<div class="full_width box_arrow">
			<span class="icon_emp" style="cursor:default"></span>
		</div>
	</div>
	<div class="clear_percent_11 float_left" id="list_and_menu_detail">
		<!-- Detail -->
	</div>
</div>

<script type="text/javascript">

	$(function(){
		$("li:first", "#list_and_menu_height").click(); // click menu li dau tien khi page load xong
	});

	function delete_message_detail_null(message_type){
			  $.ajax({
						 url: '<?php echo URL; ?>/settings/delete_message_detail_null/'+message_type,
						 timeout: 15000,
						 success: function(html){

							 $("div#list_and_menu_detail").html(html);

							 settings_kendo_enable_colorpicker();

							 list_and_menu_detail_input_change();
						 }
					 });
	}
	function settings_listmenu_detail(object, id){

		$("#list_and_menu_height a").removeClass("active");
		$("a", object).addClass("active");
		$.ajax({
			url: '<?php echo URL; ?>/settings/list_message_detail/' + id,
			timeout: 15000,
			success: function(html){



				$("div#list_and_menu_detail").html(html);

				// ----------- kendo color picker --------
				settings_kendo_enable_colorpicker();
				// ----------- end --- kendo color picker --------

				list_and_menu_detail_input_change();
			}
		});
	}

	function settings_kendo_enable_colorpicker(){
		$(".color-picker", "div#list_and_menu_detail").kendoColorPicker({
			// value: "#ffffff",
			buttons: false
			// select: list_and_menu_detail_input_change_update
		});

		$(".color-picker", "div#list_and_menu_detail").each(function(){
			var colorPicker = $(this).data("kendoColorPicker");
			colorPicker.bind({

				change: function(e) {
					var object = $(this.element);
					object.val(e.value);
					list_and_menu_detail_input_change_update(object);
				}

			});
		});
	}


	function settings_listmenu_detail_add(message_type){

		$.ajax({
			url: '<?php echo URL; ?>/settings/list_message_detail_add/'+message_type,
			timeout: 15000,
			success: function(html){


				$("div#list_and_menu_detail").html(html);


				settings_kendo_enable_colorpicker();

				list_and_menu_detail_input_change();


			}
		});
	}


	function list_and_menu_detail_input_change(){

		$("form :input", "#list_and_menu_detail").change(function() {
			list_and_menu_detail_input_change_update(this);

		});
	};

	function list_and_menu_detail_input_change_update(object){
		$.ajax({
			url: '<?php echo URL; ?>/settings/list_message_detail_auto_save',
			timeout: 15000,
			type:"post",
			data: $(object).closest('form').serialize(),
			success: function(html){
				console.log(html);
				if( html != "ok" )alerts("Error: ", html);
			}
		});
	}

</script>