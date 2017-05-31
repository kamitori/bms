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
		if( controller == "enquiry" ){
			 enquiry_update_data();
		}else if ( controller=="job")
		{
			 job_update_data();
		}else if ( controller=="task")
		{
			 task_update_data();
		}else if ( controller=="salesorder")
		{
			 salesorder_update_data();
		}else if ( controller=="quotation")
		{
			 quotation_update_data();
		}else if ( controller=="purchaseorder")
		{
			 purchaseorder_update_data();
		}else if ( controller=="shipping")
		{
			 shipping_update_data();
		}else if ( controller=="salesinvoice")
		{
			 salesinvoice_update_data();
		}



	});
});
	function all_update_data_null(){
		$("#rm_search_icon_enquiry").click();
		$("#rm_search_icon_job").click();
		$("#rm_search_icon_task").click();
		$("#rm_search_icon_salesorder").click();
		$("#rm_search_icon_quotation").click();
		$("#rm_search_icon_purchaseorder").click();
		$("#rm_search_icon_shipping").click();
		$("#rm_search_icon_salesinvoice").click();

	}
	function filter_all_by_my_name(reload_div){
		var id = '<?php echo $_SESSION['arr_user']['contact_id'];?>';
		var name = '<?php echo $_SESSION['arr_user']['contact_name'];?>';
		$("#EnquiryOurRep").val(name);
		$("#EnquiryOurRepId").val(id);

		$("#JobContact").val(name);
		$("#JobContactId").val(id);

		$("#TaskOurRep").val(name);
		$("#TaskOurRepId").val(id);

		$("#SalesorderOurRep").val(name);
		$("#SalesorderOurRepId").val(id);

		$("#QuotationOurRep").val(name);
		$("#QuotationOurRepId").val(id);

		$("#PurchaseorderOurRep").val(name);
		$("#PurchaseorderOurRepId").val(id);

		$("#ShippingOurRep").val(name);
		$("#ShippingOurRepId").val(id);

		$("#SalesinvoiceOurRep").val(name);
		$("#SalesinvoiceOurRepId").val(id);
		if( reload_div != "dont_reload" ){
			enquiry_update_data();
			job_update_data();
			task_update_data();
			salesorder_update_data();
			quotation_update_data();
			purchaseorder_update_data();
			shipping_update_data();
			salesinvoice_update_data();
		}

	}
</script>
<?php echo $this->element('../Homes/dashboard_menu'); ?>
<?php
	if(( $count = count($arr_employee) ) > 1)
		$arr_settings = array(
				'class' => 'input_select input-search-listbox',
				'readonly' => true
		);
	else
		$arr_settings = array(
				'class' => 'input_select input-search-listbox',
				'readonly' => true,
				'combobox_blank' => 1,
		);

?>
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
							<?php echo $this->Form->input('Task.our_rep', array_merge($arr_settings,array('onchange' => 'task_update_data()'))); ?>
							<?php echo $this->Form->hidden('Task.our_rep_id'); ?>
							<script type="text/javascript">
								<?php if($count>1){ ?>
								$(function () {
									$("#TaskOurRep").combobox(<?php echo json_encode($arr_employee); ?>);
								});
								<?php } ?>
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
				<span class="float_left h_form" style="width:20%">
					<span class="fl_dent"><h4><?php echo translate('Enquiries'); ?></h4></span>
				</span>
				<span class="float_right">

					<span class="block_sear bl_t">
						<span class="bg_search_1"></span>
						<span class="bg_search_2"></span>
						<div class="box_inner_search float_left" style="width: 95px;">
							<a href="javascript:void(0)" onclick="enquiry_update_data()"><span class="icon_search"></span></a>
							<?php 
								echo $this->Form->input('Enquiry.our_rep', array_merge($arr_settings,array('onchange' => 'enquiry_update_data()')));

							?>
							<?php echo $this->Form->hidden('Enquiry.our_rep_id'); ?>
							<script type="text/javascript">
								<?php if($count>1){ ?>
								$(function () {
									$("#EnquiryOurRep").combobox(<?php echo json_encode($arr_employee); ?>);
								});
								<?php } ?>
								function enquiry_update_data(){
									var field = $("#enquiry_field").val();
									var type = $("#enquiry_type").val();
									$.ajax({
										url: '<?php echo URL; ?>/homes/dashboard_enquiries/' + $("#EnquiryOurRepId").val(),
										timeout: 15000,
										type: 'POST',
										data: { field: field, type: type },
										success: function(html){
											$("#enquiry_update_data").html(html);
											reload_scrollbar();
											if( type == "asc" ){
												$("#enquiry_type").val("desc");
											}else{
												$("#enquiry_type").val("asc");
											}
										}
									});
								}
							</script>

							<a id="rm_search_icon_enquiry" href="javascript:void(0)" onclick="$('#EnquiryOurRepId').val('');$('#EnquiryOurRep').val('');enquiry_update_data();"><span class="icon_closef"></span></a>
						</div>
					</span>
				</span>
			</span>

			<ul class="ul_mag clear bg">
							<input type="hidden" id="enquiry_field">
							<input type="hidden" id="enquiry_type">
				<li class="hg_padd center_txt" style="width:26%">

				<label><?php echo translate('Date'); ?></label>
				<span controller="enquiry" class="click_sort desc" field="date" type="desc" ></span>
				</li>

				<li class="hg_padd center_txt " style="width:41%">
					<label><?php echo translate('Name'); ?></label>
					<span controller="enquiry" class="click_sort desc" field="company" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt no_border" style="width:29%"><?php echo translate('Enquiry value'); ?></li>
			</ul>
			<div class="div_updated_data container_same_category" id="enquiry_update_data">
				<?php echo $this->element('../Homes/dashboard_enquiries');
				?>
			</div>
			<p class="clear"></p>
			<span class="title_block bo_ra2">
				<span class="float_right">Total &nbsp;<input readonly="true" id="enquiries_total" class="input_w2 float_right right_txt"  type="text" style="width:75px;margin-right:0px">
					<div class="clear"></div>
				</span>
			</span>
		</div><!--END Tab1 -->
	</div>
	<div class="width_dash float_left">
		<div class="tab_1 full_width block_dent8">
			<span class="title_block bo_ra1">
				<span class="float_left h_form" style="width:20%">
					<span class="fl_dent"><h4><?php echo translate('Jobs'); ?></h4></span>
				</span>
				<span class="float_right">

					<span class="block_sear bl_t">
						<span class="bg_search_1"></span>
						<span class="bg_search_2"></span>
						<div class="box_inner_search float_left" style="width: 95px;">
							<a href="javascript:void(0)" onclick="job_update_data()"><span class="icon_search"></span></a>
							<?php echo $this->Form->input('Job.contact', array_merge($arr_settings,array('onchange' => 'job_update_data()'))); ?>
							<?php echo $this->Form->hidden('Job.contact_id'); ?>
							<script type="text/javascript">
								<?php if($count>1){ ?>
								$(function () {
									$("#JobContact").combobox(<?php echo json_encode($arr_employee); ?>);
								});
								<?php } ?>
								function job_update_data(){
								var field = $("#job_field").val();
								 var type = $("#job_type").val();
									$.ajax({
										url: '<?php echo URL; ?>/homes/dashboard_jobs/' + $("#JobContactId").val(),
										timeout: 15000,
										 type: 'POST',
										 data: { field: field, type: type },
										success: function(html){
											$("#job_update_data").html(html);
											reload_scrollbar();
											if( type == "asc" ){
													  $("#job_type").val("desc");
											 }else{
													 $("#job_type").val("asc");
											 }
										}
									});
								}

							</script>
							<a id="rm_search_icon_job" href="javascript:void(0)" onclick="$('#JobContactId').val('');$('#JobContact').val('');job_update_data();"><span class="icon_closef"></span></a>
						</div>
					</span>
				</span>
			</span>
			<ul class="ul_mag clear bg">
										<input type="hidden" id="job_field">
										<input type="hidden" id="job_type">
				<li class="hg_padd center_txt" style="width:27%">

				<label>Finish date</label>
				<span controller="job" class="click_sort desc" field="work_end" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt " style="width:60%">

				<label>Job</label>
				<span controller="job" class="click_sort desc" field="company_name" type="desc" ></span>
				</li>

				<!--<li class="hg_padd center_txt" style="width:12%">Job no</li>-->
				<li class="hg_padd center_txt no_border" style="width:8%">Late</li>
			</ul>
			<div class="div_updated_data container_same_category" id="job_update_data">
				<?php echo $this->element('../Homes/dashboard_jobs'); ?>
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
					<span class="fl_dent"><h4><?php echo translate('Sales Orders'); ?></h4></span>
				</span>
				<span class="float_right">

					<span class="block_sear bl_t">
						<span class="bg_search_1"></span>
						<span class="bg_search_2"></span>
						<div class="box_inner_search float_left" style="width: 95px;">
							<a href="javascript:void(0)" onclick="salesorder_update_data()"><span class="icon_search"></span></a>
							<?php echo $this->Form->input('Salesorder.our_rep', array_merge($arr_settings,array('onchange' => 'salesorder_update_data()'))); ?>
							<input type="hidden" name="data[Salesorder][our_rep_id]" id="SalesorderOurRepId" value="">
							<script type="text/javascript">
								<?php if($count>1){ ?>
								$(function () {
									$("#SalesorderOurRep").combobox(<?php echo json_encode($arr_employee); ?>);
								});
								<?php } ?>
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

				<label>Name</label>
				<span controller="salesorder" class="click_sort desc" field="company_name" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt" style="width:26%">Total (bf.Tax)</li>
				<li class="hg_padd center_txt no_border" style="width:9.5%">Late</li>
			</ul>
			<div class="div_updated_data container_same_category" id="salesorder_update_data">
				<?php echo $this->element('../Homes/dashboard_salesorders');
				?>
			</div>
			<p class="clear"></p>
			<span class="title_block bo_ra2">
				<span class="float_right">Total &nbsp;<input readonly="true" id="sales_orders_total"  class="input_w2 float_right right_txt" type="text" style="width:75px;margin-right:25px">
					<div class="clear"></div>
				</span>
			</span>
		</div><!--END Tab1 -->
	</div>

	 <div class="width_dash float_left">
		<div class="tab_1 full_width block_dent8">
			<span class="title_block bo_ra1">
				<span class="float_left h_form" style="width:20%">
					<span class="fl_dent"><h4><?php echo translate('Quotations'); ?></h4></span>
				</span>
				<span class="float_right">

					<span class="block_sear bl_t">
						<span class="bg_search_1"></span>
						<span class="bg_search_2"></span>
						<div class="box_inner_search float_left" style="width: 95px;">
							<a href="javascript:void(0)" onclick="quotation_update_data()"><span class="icon_search"></span></a>
							<?php echo $this->Form->input('Quotation.our_rep', array_merge($arr_settings,array('onchange' => 'quotation_update_data()'))); ?>
							<input type="hidden" name="data[Quotation][our_rep_id]" id="QuotationOurRepId">

							<script type="text/javascript">
								<?php if($count>1){ ?>
								$(function () {
									$("#QuotationOurRep").combobox(<?php echo json_encode($arr_employee); ?>);
								});
								<?php } ?>
								function quotation_update_data(){
								var field = $("#quotation_field").val();
								  var type = $("#quotation_type").val();
									$.ajax({
										url: '<?php echo URL; ?>/homes/dashboard_quotations/' + $("#QuotationOurRepId").val(),
										timeout: 15000,
										type: 'POST',
										data: { field: field, type: type },
										success: function(html){
											$("#quotation_update_data").html(html);
											reload_scrollbar();
											if( type == "asc" ){
													 $("#quotation_type").val("desc");
											}else{
													$("#quotation_type").val("asc");
											}
										}
									});
								}

							</script>
							<a id="rm_search_icon_quotation" href="javascript:void(0)" onclick="$('#QuotationOurRepId').val('');$('#QuotationOurRep').val('');quotation_update_data();"><span class="icon_closef"></span></a>
						</div>
					</span>
				</span>
			</span>
			<input type="hidden" id="quotation_field">
			<input type="hidden" id="quotation_type">
			<ul class="ul_mag clear bg">
				<li class="hg_padd center_txt" style="width:25%">
					<?php echo translate('Due date'); ?>
					<span controller="quotation" class="click_sort desc" field="payment_due_date" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt " style="width: 33%;">
					<?php echo translate('Name'); ?>
					<span controller="quotation" class="click_sort desc" field="company_name" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt" style="width:27%"><?php echo translate('Total (bf.Tax)'); ?></li>
				<li class="hg_padd center_txt no_border" style="width: 9%;"><?php echo translate('Late'); ?></li>
			</ul>

			<div class="div_updated_data container_same_category" id="quotation_update_data">
				<?php

				echo $this->element('../Homes/dashboard_quotations');

				?>
			</div>

			<p class="clear"></p>
			<span class="title_block bo_ra2">
				<span class="float_right">Total &nbsp;<input readonly="true" id="quotations_total" class="input_w2 float_right right_txt" type="text" style="width:75px;margin-right:22px">
					<div class="clear"></div>
				</span>
			</span>
		</div><!--END Tab1 -->
	</div>

	 <div class="width_dash float_left">
		<div class="tab_1 full_width block_dent8">
			<span class="title_block bo_ra1">
				<span class="float_left h_form" style="width:20%">
					<span class="fl_dent"><h4><?php echo translate('Purchases'); ?></h4></span>
				</span>
				<span class="float_right">

					<span class="block_sear bl_t">
						<span class="bg_search_1"></span>
						<span class="bg_search_2"></span>
						<div class="box_inner_search float_left" style="width: 95px;">
							<a href="javascript:void(0)" onclick="purchaseorder_update_data()"><span class="icon_search"></span></a>

							<?php echo $this->Form->input('Purchaseorder.our_rep', array_merge($arr_settings,array('onchange' => 'purchaseorder_update_data()'))); ?>
							<input type="hidden" name="data[Purchaseorder][our_rep_id]" id="PurchaseorderOurRepId">

							<script type="text/javascript">
								<?php if($count>1){ ?>
								$(function () {
									$("#PurchaseorderOurRep").combobox(<?php echo json_encode($arr_employee); ?>);
								});
								<?php } ?>
								function purchaseorder_update_data(){
								 var field = $("#purchaseorder_field").val();
								 var type = $("#purchaseorder_type").val();


									$.ajax({
										url: '<?php echo URL; ?>/homes/dashboard_purchaseorders/' + $("#PurchaseorderOurRepId").val(),
										timeout: 15000,
										type: 'POST',
										data: { field: field, type: type },
										success: function(html){
											$("#purchaseorder_update_data").html(html);
											reload_scrollbar();
										   if( type == "asc" ){
													$("#purchaseorder_type").val("desc");
										   }else{
												   $("#purchaseorder_type").val("asc");
										   }
										}
									});
								}

							</script>
							<a id="rm_search_icon_purchaseorder" href="javascript:void(0)" onclick="$('#PurchaseorderOurRepId').val('');$('#PurchaseorderOurRep').val('');purchaseorder_update_data();"><span class="icon_closef"></span></a>
						</div>
					</span>

				</span>
			</span>
			<input type="hidden" id="purchaseorder_field">
			<input type="hidden" id="purchaseorder_type">
			<ul class="ul_mag clear bg">
				<li class="hg_padd center_txt" style="width:25%">
					<label>Required</label>
					<span controller="purchaseorder" class="click_sort desc" field="required_date" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt " style="width:34%">
					<label>Name</label>
					<span controller="purchaseorder" class="click_sort desc" field="company_name" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt" style="width:26%">Total (bf.Tax)</li>
				<li class="hg_padd center_txt no_border" style="width:9.5%">Late</li>
			</ul>

			<div class="div_updated_data container_same_category" id="purchaseorder_update_data">
				<?php

				echo $this->element('../Homes/dashboard_purchaseorders'); ?>
			</div>

			<p class="clear"></p>
			<span class="title_block bo_ra2">
				<span class="float_right">Total &nbsp;<input readonly="true" id="purchases_total" class="input_w2 float_right right_txt" type="text" style="width:75px;margin-right:23px">
					<div class="clear"></div>
				</span>
			</span>
		</div><!--END Tab1 -->
	</div>
	 <div class="width_dash float_left">
		<div class="tab_1 full_width block_dent8">
			<span class="title_block bo_ra1">
				<span class="float_left h_form" style="width:20%">
					<span class="fl_dent"><h4><?php echo translate('Shippings'); ?></h4></span>
				</span>
				<span class="float_right">

					<span class="block_sear bl_t">
						<span class="bg_search_1"></span>
						<span class="bg_search_2"></span>
						<div class="box_inner_search float_left" style="width: 95px;">
							<a href="javascript:void(0)" onclick="shipping_update_data()"><span class="icon_search"></span></a>

							<?php echo $this->Form->input('Shipping.our_rep', array_merge($arr_settings,array('onchange' => 'shipping_update_data()'))); ?>
							<input type="hidden" name="data[Shipping][our_rep_id]" id="ShippingOurRepId">

							<script type="text/javascript">
								<?php if($count>1){ ?>
								$(function () {
									$("#ShippingOurRep").combobox(<?php echo json_encode($arr_employee); ?>);
								});
								<?php } ?>
								function shipping_update_data(){
								var field = $("#shipping_field").val();
							   var type = $("#shipping_type").val();
									$.ajax({
										url: '<?php echo URL; ?>/homes/dashboard_shippings/' + $("#ShippingOurRepId").val(),
										timeout: 15000,
										type: 'POST',
										data: { field: field, type: type },
										success: function(html){
											$("#shipping_update_data").html(html);
											reload_scrollbar();
											if( type == "asc" ){
													$("#shipping_type").val("desc");
										   }else{
												   $("#shipping_type").val("asc");
										   }
										}
									});
								}

							</script>
							<a id="rm_search_icon_shipping" href="javascript:void(0)" onclick="$('#ShippingOurRepId').val('');$('#ShippingOurRep').val('');shipping_update_data();"><span class="icon_closef"></span></a>
						</div>
					</span>

				</span>
			</span>
			<ul class="ul_mag clear bg">
			 <input type="hidden" id="shipping_field">
			 <input type="hidden" id="shipping_type">
				<li class="hg_padd center_txt" style="width:25%">

				<label>Ship date<label>
				<span controller="shipping" class="click_sort desc" field="shipping_date" type="desc" ></span>

				</li>
				<li class="hg_padd center_txt no_border" style="width:50%">
					<label>Name</label>
					<span controller="shipping" class="click_sort desc" field="company_name" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt no_border" style="width:20%">
					<label>Status</label>
					<span controller="shipping" class="click_sort desc" field="shipping_status" type="desc" ></span>
				</li>
			   <!-- <li class="hg_padd center_txt" style="width:9.5%">Type</li> -->
			   <!--  <li class="hg_padd center_txt" style="width:26%">Carrier</li> -->
			</ul>

			<div class="div_updated_data container_same_category" id="shipping_update_data">
				<?php echo $this->element('../Homes/dashboard_shippings'); ?>
			</div>

			<p class="clear"></p>
			<span class="title_block bo_ra2">

			</span>
		</div><!--END Tab1 -->
	</div>
	<div class="width_dash float_left">
		<div class="tab_1 full_width block_dent8">
			<span class="title_block bo_ra1">
				<span class="float_left h_form" style="width:36%">
					<span class="fl_dent"><h4><?php echo translate('Sales Invoices'); ?></h4></span>
				</span>
				<span class="float_right">

					<span class="block_sear bl_t">
						<span class="bg_search_1"></span>
						<span class="bg_search_2"></span>
						<div class="box_inner_search float_left" style="width: 95px;">
							<a href="javascript:void(0)" onclick="salesinvoice_update_data()"><span class="icon_search"></span></a>

							<?php echo $this->Form->input('Salesinvoice.our_rep', array_merge($arr_settings,array('onchange' => 'salesinvoice_update_data()'))); ?>
							<input type="hidden" name="data[Salesinvoice][our_rep_id]" id="SalesinvoiceOurRepId">

							<script type="text/javascript">
								<?php if($count>1){ ?>
								$(function () {
									$("#SalesinvoiceOurRep").combobox(<?php echo json_encode($arr_employee); ?>);
								});
								<?php } ?>
								function salesinvoice_update_data(){
								var field = $("#salesinvoice_field").val();
								var type = $("#salesinvoice_type").val();
									$.ajax({
										url: '<?php echo URL; ?>/homes/dashboard_salesinvoices/' + $("#SalesinvoiceOurRepId").val(),
										timeout: 15000,
										type: 'POST',
										data: { field: field, type: type },
										success: function(html){
											$("#salesinvoice_update_data").html(html);
											reload_scrollbar();
											if( type == "asc" ){
													   $("#salesinvoice_type").val("desc");
											  }else{
													  $("#salesinvoice_type").val("asc");
											  }
										}
									});
								}

							</script>
							<a id="rm_search_icon_salesinvoice" href="javascript:void(0)" onclick="$('#SalesinvoiceOurRepId').val('');$('#SalesinvoiceOurRep').val('');salesinvoice_update_data();"><span class="icon_closef"></span></a>
						</div>
					</span>

				</span>
			</span>
			<ul class="ul_mag clear bg">
			<input type="hidden" id="salesinvoice_field">
						 <input type="hidden" id="salesinvoice_type">
				<li class="hg_padd center_txt" style="width:25%">
					<label>Due date</label>
					<span controller="salesinvoice" class="click_sort desc" field="payment_due_date" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt" style="width:34%">

				<label>Name</label>
					<span controller="salesinvoice" class="click_sort desc" field="company_name" type="desc" ></span>
				</li>
				<li class="hg_padd center_txt" style="width:26%">Balance</li>
				<li class="hg_padd center_txt no_border" style="width:9.5%">Late</li>
			</ul>

			<div class="div_updated_data container_same_category" id="salesinvoice_update_data">
				<?php

				echo $this->element('../Homes/dashboard_salesinvoices');
				?>
			</div>

			<p class="clear"></p>
			<span class="title_block bo_ra2">
				<span class="float_right">Total &nbsp;<input readonly="true" id="salesinvoices_total" class="input_w2 float_right right_txt" type="text" style="width:75px;margin-right:23px">
					<div class="clear"></div>
				</span>
			</span>
		</div>
	</div>

	<div style="clear:both"></div>
	<br>
</div>

<script type="text/javascript">
<?php if($count == 1){ ?>
$("#filter_all_by_my_name,#all_update_data_null").hide();
$("a[id^='rm_search_icon_']").remove();
$(".input-search-listbox").prop("disabled",true);
$(".block_sear",".title_block").parent().remove();
<?php } ?>
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