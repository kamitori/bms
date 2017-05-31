<div class="clear_percent">
	<div class="clear_percent_19 float_left">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4>Sale ruler list</h4></span>
			</span>
			<div class="title_small_once">
				<ul class="ul_mag clear bg3">
					<li class="hg_padd" style="border-right:none;">
						<span class="normal">(Click a menu below to view on right box)</span>
					</li>
				</ul>
			</div>
			<div id="list_and_menu_height" class="container_same_category" style="height: 449px;overflow-y: auto">
				<ul class="find_list setup_menu">
					<li onclick="show_box_detail(this,'sale_price_list_type')" id="sale_price_list_type">
						<a href="javascript:void(0)" class="active">
							Pricing Tablelookup
						</a>
					</li>
					<li onclick="show_box_detail(this,'bleed_calculation')" id="bleed_calculation">
						<a href="javascript:void(0)" class="active">
							Bleed calculation
						</a>
					</li>
					<li onclick="settings_list_menu_detail(this, '<?php echo isset($product_base_id)?$product_base_id:'';?>')" id="sales_ruler_basic_product">
						<a href="javascript:void(0)">
							Product Base
						</a>
					</li>
					<li onclick="settings_list_menu_detail(this, '<?php echo isset($product_category_id)?$product_category_id:'';?>')" id="sales_ruler_basic_product">
						<a href="javascript:void(0)">
							Product Category
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
	<div class="clear_percent_11 float_left" id="view_box_detail" style="display:block;">
		<!-- Detail -->
	</div>
	<div class="clear_percent_11 float_left" id="list_and_menu_detail" style="display:none;">
		<!-- Detail -->
	</div>
</div>
<script type="text/javascript">

	$(function(){
		$("li:first", "#list_and_menu_height").click(); // click menu li dau tien khi page load xong
		$(".setup_menu li").click(function(){
			if($(this).attr('id')=='sales_ruler_basic_product'){
				$("#view_box_detail").css("display","none");
				$("#list_and_menu_detail").css("display","block");
			}else{
				$("#list_and_menu_detail").css("display","none");
				$("#view_box_detail").css("display","block");
			}
		});
	});

	function show_box_detail(object,id,key){
		if(key==undefined)
			key = '';
		$("#list_and_menu_height a").removeClass("active");
		$("a", object).addClass("active");
		$.ajax({
			url: '<?php echo URL; ?>/settings/'+id+'/'+key,
			type:"POST",
			timeout: 15000,
			success: function(html){
				$("#view_box_detail").html(html);
			}
		});
	}



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