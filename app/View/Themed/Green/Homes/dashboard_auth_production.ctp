<script type="text/javascript">
$(function(){
	filter_all_by_my_name("dont_reload");
	$("ul.bg li").click(function(){
		var span = $("span", this);
		var controller = span.attr("controller");
		$("#" + controller + "_field").val( span.attr("field") );
		var type = span.attr("type");
		$("#" + controller + "_type").val( type );

		if( type == "asc" ){
			span.attr("type", "desc");
			span.addClass("desc").removeClass("asc");
		}else{
			span.attr("type", "asc");
			span.addClass("asc").removeClass("desc");
		}

		if ( controller=="task")
		{
			 task_update_data();
		}else if ( controller=="salesorder")
		{
			 salesorder_update_data();
		}else if ( controller=="asset")
		{
			 task_asset_update_data(span.attr("field"), type, span.attr("key"));
		}
	});
});
	function all_update_data_null(){
		$("#rm_search_icon_task").click();
		$("#rm_search_icon_asset").click();
		$("#rm_search_icon_salesorder").click();
	}
	function filter_all_by_my_name(reload_div){
		var id = '<?php echo $_SESSION['arr_user']['contact_id'];?>';
		var name = '<?php echo $_SESSION['arr_user']['contact_name'];?>';

		$("#TaskOurRep").val(name);
		$("#TaskOurRepId").val(id);

		$("#SalesorderOurRep").val(name);
		$("#SalesorderOurRepId").val(id);

		if( reload_div != "dont_reload" ){
			task_update_data();
			salesorder_update_data();
		}
	}
</script>
<?php echo $this->element('../Homes/dashboard_menu'); ?>
<div id="content">

	<div class="width_dash float_left">
		<div class="tab_1 full_width block_dent8">
			<span class="title_block bo_ra1">
				<span class="float_left h_form" style="width:20%">
					<span class="fl_dent"><h4><?php echo translate('Tasks'); ?></h4></span>
				</span>
				<span class="float_right">

					<span class="block_sear bl_t">
						<span class="bg_search_1"></span>
						<span class="bg_search_2"></span>
						<div class="box_inner_search float_left" style="width: 95px;">
							<a href="javascript:void(0)" onclick="task_update_data()"><span class="icon_search"></span></a>
							<?php echo $this->Form->input('Task.our_rep', array(
									'class' => 'input_select input-search-listbox',
									'onchange' => 'task_update_data()',
									'readonly' => true
							)); ?>
							<?php echo $this->Form->hidden('Task.our_rep_id'); ?>
							<script type="text/javascript">
								$(function () {
									$("#TaskOurRep").combobox(<?php echo json_encode($arr_employee); ?>);
								});
								function task_update_data(){
								var field = $("#task_field").val();
								var type = $("#task_type").val();
									$.ajax({
										url: '<?php echo URL; ?>/homes/dashboard_tasks/' + $("#TaskOurRepId").val(),
										timeout: 15000,
										 type: 'POST',
										data: { field: field, type: type },
										success: function(html){
											$("#task_update_data").html(html);
											reload_scrollbar();
										    if( type == "asc" ){
													 $("#task_type").val("desc");
											}else{
													$("#task_type").val("asc");
											}
										}
									});
								}

							</script>
							<a id="rm_search_icon_task" href="javascript:void(0)" onclick="$('#TaskOurRepId').val('');$('#TaskOurRep').val('');task_update_data();"><span class="icon_closef"></span></a>
						</div>
					</span>
				</span>
			</span>
			 <ul class="ul_mag clear bg">
				<input type="hidden" id="task_field">
				<input type="hidden" id="task_type">
				<li class="hg_padd center_txt" style="width:25%">
					<label><?php echo translate('Work end'); ?></label>
					<span controller="task" class="click_sort desc" field="work_end" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt" style="width:18%"><?php echo translate('Time'); ?></li>
				<li class="hg_padd center_txt" style="width:38%">
				 	<label><?php echo translate('Task'); ?> </label>
					<span controller="task" class="click_sort desc" field="name" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt no_border" style="width:13.5%"><?php echo translate('Late'); ?></li>
			</ul>
			<div class="div_updated_data container_same_category" id="task_update_data">
				<?php echo $this->element('../Homes/dashboard_tasks'); ?>
			</div>

			<p class="clear"></p>
			<span class="title_block bo_ra2">

			</span>
		</div><!--END Tab1 -->
	</div>

	<div class="width_dash float_left">
		<div class="tab_1 full_width block_dent8">
			<span class="title_block bo_ra1">
				<span class="float_left h_form" style="width:33%">
					<span class="fl_dent"><h4><?php echo translate('Dockets'); ?></h4></span>
				</span>
				<span class="float_right">

					<span class="block_sear bl_t">
						<span class="bg_search_1"></span>
						<span class="bg_search_2"></span>
						<div class="box_inner_search float_left" style="width: 95px;">
							<a href="javascript:void(0)" onclick="salesorder_update_data()"><span class="icon_search"></span></a>
							<input name="data[Salesorder][our_rep]" class="input_select input-search-listbox" onchange="salesorder_update_data()" readonly="readonly" type="text" id="SalesorderOurRep" style="margin: 0px 14px 0px 0px;">
							<input type="hidden" name="data[Salesorder][our_rep_id]" id="SalesorderOurRepId" value="">
							<script type="text/javascript">
								$(function () {
									$("#SalesorderOurRep").combobox(<?php echo json_encode($arr_employee); ?>);
								});
								function salesorder_update_data(){
								var field = $("#salesorder_field").val();
								 var type = $("#salesorder_type").val();
									$.ajax({
										url: '<?php echo URL; ?>/homes/dashboard_salesorders/' + $("#SalesorderOurRepId").val(),
										timeout: 15000,
										type: 'POST',
										data: { field: field, type: type },
										success: function(html){
											$("#salesorder_update_data").html(html);
											reload_scrollbar();
											if( type == "asc" ){
													 $("#salesorder_type").val("desc");
											}else{
													$("#salesorder_type").val("asc");
											}

										}
									});
								}

							</script>
							<a id="rm_search_icon_salesorder" href="javascript:void(0)" onclick="$('#SalesorderOurRepId').val('');$('#SalesorderOurRep').val('');salesorder_update_data();"><span class="icon_closef"></span></a>
						</div>
					</span>
				</span>
			</span>
			<ul class="ul_mag clear bg">
					 <input type="hidden" id="salesorder_field">
					 <input type="hidden" id="salesorder_type">
				<li class="hg_padd center_txt" style="width:25%">

				<label>Due date</label>
				<span controller="salesorder" class="click_sort desc" field="payment_due_date" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt " style="width:34%">

				<label>Customer</label>
				<span controller="salesorder" class="click_sort desc" field="company_name" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt" style="width:26%">Total (bf.Tax)</li>
				<li class="hg_padd center_txt no_border" style="width:9.5%">Late</li>
			</ul>
			<div class="div_updated_data container_same_category" id="salesorder_update_data">
				<?php echo $this->element('../Homes/dashboard_salesorders');?>
			</div>
			<p class="clear"></p>
			<span class="title_block bo_ra2">
				<span class="float_right">Total &nbsp;<input readonly="true" id="sales_orders_total"  class="input_w2 float_right right_txt" type="text" style="width:75px;margin-right:25px">
					<div class="clear"></div>
				</span>
			</span>
		</div><!--END Tab1 -->
	</div>

	<!-- ASSETS -->
	<?php foreach ($arr_equipment as $key => $value) { ?>
	<div class="width_dash float_left">
		<div class="tab_1 full_width block_dent8">
			<span class="title_block bo_ra1">
				<span class="float_left h_form">
					<span class="fl_dent"><h4><?php echo $value; ?></h4></span>
				</span>
			</span>
			 <ul class="ul_mag clear bg">
				<input type="hidden" id="asset_field">
				<input type="hidden" id="asset_type">
				<li class="hg_padd center_txt" style="width:25%">
					<label><?php echo translate('Work end'); ?></label>
					<span controller="asset" class="click_sort desc" field="work_end" key="<?php echo $key; ?>" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt" style="width:19%"><?php echo translate('Time'); ?></li>
				<li class="hg_padd center_txt" style="width:39%">
					<label><?php echo translate('Task'); ?> </label>
					<span controller="asset" class="click_sort desc" field="name" key="<?php echo $key; ?>" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt no_border" style="width:11%"><?php echo translate('Late'); ?></li>
			</ul>
			<div class="div_updated_data container_same_category" id="task_asset_update_data_<?php echo $key; ?>">
				<?php echo $this->element('../Homes/dashboard_assets', array('asset_key' => $key)); ?>
			</div>
			<p class="clear"></p>
			<span class="title_block bo_ra2">

			</span>
		</div><!--END Tab1 -->
	</div>
	<?php } ?>
	<script type="text/javascript">
		function task_asset_update_data(field, type, key){
			$.ajax({
				url: '<?php echo URL; ?>/homes/dashboard_assets/' + key,
				timeout: 15000,
				 type: 'POST',
				data: { field: field, type: type },
				success: function(html){
					$("#task_asset_update_data_" + key).html(html);
				}
			});
		}

	</script>

	<div style="clear:both"></div>
	<br>
</div>

<script type="text/javascript">
function dashboard_run(module, id){
	window.location = "<?php echo URL; ?>/" + module + "/entry/" + id;
}
function reload_scrollbar(){
	$(".container_same_category").mCustomScrollbar({
		scrollButtons:{
			enable:false
		}
	});
}
$(function(){
	reload_scrollbar();
});
</script>