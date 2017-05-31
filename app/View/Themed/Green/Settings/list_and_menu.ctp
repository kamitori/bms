<style type="text/css">
#confirms_window .jt_confirms_window_ok{
	width: 15%;
	height: 18%;
	margin-top: 14%;
	margin-left: 80%;
}
#list_and_menu_height ul li{
	padding-left: 4px !important;
}
</style>
<div class="clear_percent">
	<div class="clear_percent_19 float_left">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('Specify values for lists used in system'); ?></h4></span>
			</span>
			<ul class="ul_mag clear bg3">
				<li class="hg_padd center_text" style="width:65%;border: none;"><?php echo translate('List name'); ?> <span class="normal">(<?php echo translate('edit on right'); ?>)</span></li>
				<li class="hg_padd center_text" style="width:22%;border: none;">System</li>
			</ul>
			<div id="list_and_menu_height" class="container_same_category" style="height: 449px;overflow-y: auto">
				<?php $i = 1;
				foreach ($arr_settings as $key => $value) {
				?>
				<ul class="ul_mag clear bg<?php echo $i; ?>" id="system_<?php echo $value['_id']; ?>">
					<li class="hg_padd" style="width: 64.3%;border: none;">
						<input type="text" value="<?php if(isset($value['setting_name'])){ echo $value['setting_name']; }else{ echo $value['setting_value']; } ?>" class="input_inner bg<?php echo $i; ?>" readonly="">
						</li>
					<li class="hg_padd center_text" style="width: 21%;border: none;">
							<input type="checkbox" name="Setting[deleted]" <?php if($value['system_admin']){ ?>checked="checked"<?php } ?> onchange="update_system_admin(this, '<?php echo $value['_id']; ?>')">
						</li>
					<li class="hg_padd center_text clickfirst" style="width: 3.1%;border-right: none;cursor:pointer" onclick="settings_list_menu_detail(this, '<?php echo $value['_id']; ?>')"><span class="icon_emp"></span></li>

				</ul>
				<?php $i = 3 - $i;
				} ?>

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

<style type="text/css">
#list_and_menu_height ul:hover, #list_and_menu_height ul:hover input{
	background-color: #B8B8B8;
}
</style>

<script type="text/javascript">
	function update_system_admin(object, id){
		$.ajax({
			url: '<?php echo URL; ?>/settings/update_system_admin/' + id + "/" + $(object).prop("checked"),
			timeout: 15000,
			success: function(html){
				if( html != "ok" ){
					alerts("Error: ", html);
				}else{
					console.log("#system_" + id);
					$("#system_" + id + " li").click();
				}
			}
		});
	}


</script>

<script type="text/javascript">

	$(function(){

		$('.container_same_category').mCustomScrollbar({
			scrollButtons:{
				enable:false
			}
		});

		$(".clickfirst:first", "#list_and_menu_height").click(); // click menu li dau tien khi page load xong
	});

	function settings_list_menu_detail(object, ids,lang){

		// $("#list_and_menu_height a").removeClass("active");
		// $("a", object).addClass("active");
		var langkey = 'en';
		if(lang!=undefined)
			langkey = lang;

		$("#list_and_menu_height ul").attr("style", "");
		$("input", "#list_and_menu_height").attr("style", "");

		var ul = $(object).parents("ul");
		$(ul).attr("style", "background-color: #B8B8B8;");
		$("input", ul).attr("style", "background-color: #B8B8B8;");

		$.ajax({
			url: '<?php echo URL; ?>/settings/list_and_menu_detail/' + ids + '/'+langkey,
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
				// select: function(e) {
				//     kendoConsole.log("Select in picker #" + this.element.attr("id") + " :: " + e.value);
				// },
				change: function(e) {
					var object = $(this.element);
					object.val(e.value);
					list_and_menu_detail_input_change_update(object);
				}
				// ,
				// open: function() {
				//     kendoConsole.log("Open in picker #" + this.element.attr("id"));
				// },
				// close: function() {
				//     kendoConsole.log("Close in picker #" + this.element.attr("id"));
				// }
			});
		});
	}

	function settings_list_menu_detail_add(id){

		$.ajax({
			url: '<?php echo URL; ?>/settings/list_and_menu_detail_add/' + id + "/" + $("#all_field_of_option").val(),
			timeout: 15000,
			success: function(html){
				$("div#list_and_menu_detail").html(html);

				// settings_kendo_enable_colorpicker();

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
			url: '<?php echo URL; ?>/settings/list_and_menu_detail_auto_save',
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