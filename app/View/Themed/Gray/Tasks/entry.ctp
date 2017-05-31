<style type="text/css">
.input_switch_popup {
	width: 99%;
	padding: 3px 10px;
	border-radius: 9px;
}
</style>
<?php echo $this->element('entry_tab_option'); ?>
<div id="content" class="fix_magr">
	<div class="clear">

		<div class="clear_percent">
			<div class="block_dent_a">
				<div class="title_1 float_left">
					<h1>
						<span id="task_work_start_header"></span> | <span id="task_name_header"></span>
					</h1>
				</div>
				<div class="title_1 right_txt float_right">
					<h1><span id="task_assign_to_header"></span> | <span id="task_status_header"></span></h1>
				</div>
			</div>
		</div>

		<div id="tasks_form_auto_save">
			<?php echo $this->Form->create('Task'); ?>
			<?php echo $this->Form->hidden('Task._id', array('value' => (string)$this->data['Task']['_id'])); ?>

			<div class="clear_percent">
				<div class="clear_percent_1 float_left">
					<div class="tab_1 block_dent_a">
						<p class="clear">
							<span class="label_1 float_left fixbor"><?php echo translate('Task no'); ?></span>
						</p>
						<div class="width_in float_left indent_input_tp">
							<?php echo $this->Form->input('Task.no', array(
									'class' => 'input_1 float_left'
							)); ?>
						</div>
						<p class="clear"></p>

						<p class="clear">
							<span class="label_1 float_left"><?php echo translate('Heading'); ?></span>
						</p>
						<div class="width_in float_left indent_input_tp">
							<?php echo $this->Form->input('Task.name', array(
									'class' => 'input_1 float_left'
							)); ?>
						</div>
						<p class="clear"></p>

						<p class="clear">
							<span class="label_1 float_left"><?php echo translate('Task type'); ?></span>
							<div class="width_in float_left indent_input_tp" style="padding: 0 0 0 1%;">
								<?php echo $this->Form->input('Task.type', array(
										'class' => 'input_select',
										'readonly' => true
								)); ?>
								<?php echo $this->Form->hidden('Task.type_id'); ?>
								<script type="text/javascript">
									$(function () {
										$("#TaskType").combobox(<?php echo json_encode($arr_tasks_type); ?>);
									});
								</script>
							</div>
							<p class="clear"></p>
						</p>

						<p class="clear">
							<span class="label_1 float_left ">
								<?php
								if( $this->data['Task']['our_rep_type'] == 'contacts' ){

									$link = 'javascript:void(0)';
									if( isset($this->data['Task']['our_rep_id']) && is_object($this->data['Task']['our_rep_id'])){
										$link = URL . '/contacts/entry/' . $this->data['Task']['our_rep_id'];
									}
								?>
								<a id="link_to_contacts_assets" href="<?php echo $link; ?>"><?php echo translate('Responsible'); ?></a>
								<?php }else{
									echo translate('Responsible');
								} ?>
							</span>
						</p>

						<div class="width_in float_left indent_input_tp">
							<?php echo $this->Form->hidden('Task.our_rep_type'); ?>
							<?php echo $this->Form->hidden('Task.our_rep_id'); ?>
							<?php echo $this->Form->input('Task.our_rep', array(
									'class' => 'input_1 float_left',
									'readonly' => true,
							)); ?>
							<span id="click_open_window_contacts_tab_tasks" class="iconw_m indent_dw_m"></span>
							<span id="click_open_window_equipments_tab_tasks" style="left:0;top:0;width:1px;height:1px;"></span>

							<script type="text/javascript">
							$(function(){
								// kiểm tra xem đã chọn company chưa
								tasks_init_popup_contacts_assets();

								window_popup("equipments", "Specify asset", "_tab_tasks");
							});

							function tasks_init_popup_contacts_assets( force_re_install ){
								var parameter_get = "";
								parameter_get = "?is_employee=1";

								if( force_re_install == "force_re_install" ){
									window_popup("contacts", "Specify responsible", "_tab_tasks", "", parameter_get, "force_re_install");

								}else{
									window_popup("contacts", "Specify responsible", "_tab_tasks", "", parameter_get);
								}

							}

							function after_choose_contacts_tab_tasks(contact_id, contact_name) {
								$("#link_to_contacts_assets").attr("href", "<?php echo URL; ?>/contacts/entry/" + contact_id);
								$("#TaskOurRepId").val(contact_id);
								$("#TaskOurRep").val(contact_name);
								$("#TaskOurRepType").val("contacts");

								$("#window_popup_contacts_tab_tasks").data("kendoWindow").close();
								tasks_auto_save_entry();
								return false;
							}

							function after_choose_equipments_tab_tasks(equipment_id, equipment_name) {
								$("#link_to_contacts_assets").attr("href", "javascript:void(0)");
								$("#TaskOurRepId").val(equipment_id);
								$("#TaskOurRep").val(equipment_name);
								$("#TaskOurRepType").val("assets");

								$("#window_popup_equipments_tab_tasks").data("kendoWindow").close();
								tasks_auto_save_entry();
								return false;
							}

							</script>
						</div>

						<p class="clear">
							<span class="label_1 float_left fixbor2"><?php echo translate('POS Task'); ?></span>
						</p>
						<div class="width_in float_left indent_input_tp">
							<?php echo $this->Form->hidden('Task.pos_task'); ?>
							<?php echo $this->Form->input('Task.pos_task', array(
								    'type' => 'checkbox',
								    'class' => 'float_left ',
								    'selected' => $this->data['Task']['pos_task'],
								    'default' => 0,
								    'style' => 'margin: 5px 0 0;',
								    'onclick' => 'change_pos_task(this)'
							));?>
							<script type="text/javascript">
							function change_pos_task(obj)
							{
								if(!$(this).is(':checked'))
								{
									$('#TaskPosTask').val(0);	
								}
								else
								{
									$('#TaskPosTask').val(1);		
								}
							}
							</script>
						</div>

						<p class="clear"></p>

					</div><!--END Tab1 -->
				</div>

				<?php for ($i=0; $i < 24; $i++) {
					$j = $i;
					if($j < 10)$j = '0'.$j;
					if($i > 7 && $i < 18){
						$arr_hour[$j.':00'] = array('name'=> $j.':00', 'value'=> $j.':00', 'class'=>'BgOptionHour');
						$arr_hour[$j.':30'] = array('name'=> $j.':30', 'value'=> $j.':30', 'class'=>'BgOptionHour');
					}else{
						$arr_hour[$j.':00'] = $j.':00';
						$arr_hour[$j.':30'] = $j.':30';
					}
				}
				?>

				<div class="clear_percent_2 float_right">
					<div class="tab_1 float_left block_dent8">
						<div class="tab_1_inner float_left">

							<div id="entry_udpate_date"><!-- use to update date -->

							<p class="clear">
								<span class="label_1 float_left fixbor minw_lab"><?php echo translate('Work start'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<div class="two_colum border_right">
									<?php echo $this->Form->input('Task.work_start', array(
										'class' => 'JtSelectDate input_1 float_left force_reload',
										'readonly' => true
									)); ?>
								</div>
								<div class="once_colum top_se">
									<div class="styled_select">
										<?php echo $this->Form->input('Task.work_start_hour', array(
											'class' => 'force_reload',
											'options' => $arr_hour
										)); ?>
									</div>
								</div>
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Work end'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<div class="two_colum border_right">
									<?php echo $this->Form->input('Task.work_end', array(
										'class' => 'JtSelectDate input_1 float_left force_reload force_reload',
										'readonly' => true
									)); ?>
								</div>
								<div class="once_colum top_se">
									<div class="styled_select">
										<?php echo $this->Form->input('Task.work_end_hour', array(
											'class' => 'force_reload',
											'options' => $arr_hour
										)); ?>
									</div>
								</div>
							</div>

							</div><!-- end tasks_update_date -->


							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Priority'); ?></span>
								</p><div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Task.priority', array(
										'class' => 'input_select',
										'readonly' => true
									)); ?>
									<?php echo $this->Form->hidden('Task.priority_id'); ?>
									<script type="text/javascript">
										$(function () {
											$("#TaskPriority").combobox(<?php echo json_encode($arr_priority); ?>);
										});
									</script>
								</div>

							<p class="clear">
								<span class="label_1 float_left  minw_lab"><?php echo translate('Status'); ?></span>
								</p><div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Task.status', array(
										'class' => 'input_select',
										'readonly' => true
									)); ?>
									<?php echo $this->Form->hidden('Task.status_id'); ?>
									<script type="text/javascript">
										$(function () {
											$("#TaskStatus").combobox(<?php echo json_encode($arr_tasks_status); ?>);
										});
									</script>
								</div>

							<p class="clear">
								<span class="label_1 float_left fixbor2 minw_lab"><?php echo translate('Days left'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<input id="tasks_days_left" class="input_1 float_left" type="text" value="" readonly="true">
							</div>

						</div>
						<div class="tab_1_inner float_left">
							<p class="clear">
								<span class="label_1 float_left minw_lab">
									<?php
										$link = 'javascript:void(0)';
										if(isset($this->data['Task']['company_id']) && is_object($this->data['Task']['company_id'])){
											$link = URL . '/companies/entry/' . $this->data['Task']['company_id'];
										}
									?>
									<a id="link_to_companies" href="<?php echo $link; ?>"><?php echo translate('Company'); ?></a>
								</span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->hidden('Task.company_id'); ?>
								<?php echo $this->Form->input('Task.company_name', array(
										'class' => 'input_1 float_left ',
								)); ?>
								<span class="iconw_m indent_dw_m" id="click_open_window_companies"></span>
								<script type="text/javascript">
									$(function(){
										window_popup('companies', 'Specify company');
									});

									function after_choose_companies(company_id, company_name){
										$("#link_to_companies").attr("href", "<?php echo URL; ?>/companies/entry/" + company_id);

										var json = $("#after_choose_companies" + company_id).val();

										$("#TaskCompanyId").val(company_id);
										$("#TaskCompanyName").val(JSON.parse(json).name);

										$("#window_popup_companies").data("kendoWindow").close();

										tasks_auto_save_entry();

										// khởi tạo lại kendo window của sales order tương ứng với company mới
										tasks_init_popup_contacts("force_re_install");

										tasks_init_popup_enquiries("force_re_install");

										tasks_init_popup_quotations("force_re_install");

										tasks_init_popup_jobs("force_re_install");

										tasks_init_popup_salesorders("force_re_install");

										tasks_init_popup_purchaseorders("force_re_install");

										return false;
									}
								</script>
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab">
									<?php
										$link = 'javascript:void(0)';
										if(isset($this->data['Task']['contact_id']) && is_object($this->data['Task']['contact_id'])){
											$link = URL . '/contacts/entry/' . $this->data['Task']['contact_id'];
										}
									?>
									<a id="link_to_contacts" href="<?php echo $link; ?>"><?php echo translate('Contact'); ?></a>
								</span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->hidden('Task.contact_id'); ?>
								<?php echo $this->Form->input('Task.contact_name', array(
										'class' => 'input_1 float_left ',
										'readonly' => true,
								)); ?>
								<span id="click_open_window_contacts" class="iconw_m indent_dw_m"></span>
								<script type="text/javascript">
									$(function(){
										// kiểm tra xem đã chọn company chưa
										tasks_init_popup_contacts();
									});

									function tasks_init_popup_contacts( force_re_install ){
										var parameter_get = "?is_customer=1";
										if( $("#TaskCompanyId").val() != "" ){
											parameter_get += "&company_id=" + $("#TaskCompanyId").val() + "&company_name=" + $("#TaskCompanyName").val();
										}

										if( force_re_install == "force_re_install" ){
											window_popup("contacts", "Specify contact", "", "", parameter_get, "force_re_install");

										}else{
											window_popup("contacts", "Specify contact", "", "", parameter_get);
										}

									}

									function after_choose_contacts(contact_id, contact_name){
										$("#link_to_contacts").attr("href", "<?php echo URL; ?>/contacts/entry/" + contact_id);
										$("#TaskContactId").val(contact_id);
										$("#TaskContactName").val(contact_name);
										$("#window_popup_contacts").data("kendoWindow").close();
										tasks_auto_save_entry();
										return false;
									}
								</script>
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab">
								</span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<input  class="input_1 float_left " readonly="readonly" type="text">
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab">
								</span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<input  class="input_1 float_left " readonly="readonly" type="text">
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab fixbor3">
								</span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<input  class="input_1 float_left " readonly="readonly" type="text">
							</div>

						</div>
						<div class="tab_1_inner float_left">

							<!-- ENQUIRY -->
							<p class="clear">
								<span class="label_1 float_left minw_lab">
									<?php
										$link = 'javascript:void(0)';
										if(isset($this->data['Task']['enquiry_id']) && is_object($this->data['Task']['enquiry_id'])){
											$link = URL . '/enquiries/entry/' . $this->data['Task']['enquiry_id'];
										}
									?>
									<a id="link_to_enquiries" href="<?php echo $link; ?>"><?php echo translate('Enquiry'); ?></a>
								</span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->hidden('Task.enquiry_id'); ?>

								<div style=" float:left;;margin-left:1%;border-right:1px solid #ddd;display:block; width:27%;">
									<?php echo $this->Form->input('Task.enquiry_no', array(
										'class' => 'input_1 float_left ',
										'readonly' => true,
									)); ?>
								</div>
								<div class="jt_box_field " style=" width:69%;height:21px;">
									<?php echo $this->Form->input('Task.enquiry_name', array(
											'class' => 'input_1 float_left ',
											'readonly' => true,
									)); ?>
								</div>
								<span class="iconw_m indent_dw_m" id="click_open_window_enquiries"></span>
								<script type="text/javascript">
									$(function(){
										tasks_init_popup_enquiries();
									});

									function tasks_init_popup_enquiries( force_re_install ){
										var parameter_get = "";
										if( $("#TaskCompanyId").val() != "" && $("#TaskCompanyName").val() != "" ){
											parameter_get += "?company_id=" + $("#TaskCompanyId").val() + "&company_name=" + $("#TaskCompanyName").val();
										}

										if( force_re_install == "force_re_install" ){
											window_popup("enquiries", "Specify enquiry", "", "", parameter_get, "force_re_install");

										}else{
											window_popup("enquiries", "Specify enquiry", "", "", parameter_get);
										}

									}

									function after_choose_enquiries(enquiry_id, enquiry_name){
										$("#link_to_enquiries").attr("href", "<?php echo URL; ?>/enquiries/entry/" + enquiry_id);

										var json = JSON.parse($("#after_choose_enquiries" + enquiry_id).val());
										$("#TaskEnquiryNo").val(json.no);
										$("#TaskEnquiryId").val(enquiry_id);
										$("#TaskEnquiryName").val(json.company);
										$("#window_popup_enquiries").data("kendoWindow").close();
										tasks_auto_save_entry();

										return false;
									}
								</script>
							</div>

							<!-- QUOTATION -->
							<p class="clear">
								<span class="label_1 float_left minw_lab">
									<?php
										$link = 'javascript:void(0)';
										if(isset($this->data['Task']['quotation_id']) && is_object($this->data['Task']['quotation_id'])){
											$link = URL . '/quotations/entry/' . $this->data['Task']['quotation_id'];
										}
									?>
									<a id="link_to_quotations" href="<?php echo $link; ?>"><?php echo translate('Quotation'); ?></a>
								</span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->hidden('Task.quotation_id'); ?>

								<div style=" float:left;;margin-left:1%;border-right:1px solid #ddd;display:block; width:27%;">
									<?php echo $this->Form->input('Task.quotation_no', array(
										'class' => 'input_1 float_left ',
										'readonly' => true,
									)); ?>
								</div>
								<div class="jt_box_field " style=" width:69%;height:21px;">
									<?php echo $this->Form->input('Task.quotation_name', array(
										'class' => 'input_1 float_left ',
										'readonly' => true,
									)); ?>

								</div>
								<span id="click_open_window_quotations" class="iconw_m indent_dw_m"></span>

								<script type="text/javascript">
									$(function(){
										tasks_init_popup_quotations();
									});

									function tasks_init_popup_quotations( force_re_install ){
										var parameter_get = "";
										if( $("#TaskCompanyId").val() != "" && $("#TaskCompanyName").val() != "" ){
											parameter_get += "?company_id=" + $("#TaskCompanyId").val() + "&company_name=" + $("#TaskCompanyName").val();
										}

										if( force_re_install == "force_re_install" ){
											window_popup("quotations", "Specify quotation", "", "", parameter_get, "force_re_install");

										}else{
											window_popup("quotations", "Specify quotation", "", "", parameter_get);
										}

									}

									function after_choose_quotations(quotation_id, quotation_name){
										$("#link_to_quotations").attr("href", "<?php echo URL; ?>/quotations/entry/" + quotation_id);

										var json = JSON.parse($("#after_choose_quotations" + quotation_id).val());
										$("#TaskQuotationNo").val(json.code);
										$("#TaskQuotationId").val(quotation_id);
										$("#TaskQuotationName").val(json.name);
										$("#window_popup_quotations").data("kendoWindow").close();
										tasks_auto_save_entry();

										return false;
									}

								</script>
							</div>

							<!-- JOB -->
							<p class="clear">
								<span class="label_1 float_left minw_lab">
									<?php
										$link = 'javascript:void(0)';
										if(isset($this->data['Task']['job_id']) && is_object($this->data['Task']['job_id'])){
											$link = URL . '/jobs/entry/' . $this->data['Task']['job_id'];
										}
									?>
									<a id="link_to_jobs" href="<?php echo $link; ?>"><?php echo translate('Job'); ?></a>
								</span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->hidden('Task.job_id'); ?>

								<div style=" float:left;;margin-left:1%;border-right:1px solid #ddd;display:block; width:27%;">
									<?php echo $this->Form->input('Task.job_no', array(
										'class' => 'input_1 float_left ',
										'readonly' => true,
									)); ?>
								</div>
								<div class="jt_box_field " style=" width:69%;height:21px;">
									<?php echo $this->Form->input('Task.job_name', array(
										'class' => 'input_1 float_left ',
										'readonly' => true,
									)); ?>

								</div>
								<span id="click_open_window_jobs" class="iconw_m indent_dw_m"></span>

								<script type="text/javascript">
									$(function(){
										window_popup('jobs', 'Specify Job');
									});

									function after_choose_jobs(job_id, job_name){
										$("#link_to_jobs").attr("href", "<?php echo URL; ?>/jobs/entry/" + job_id);

										var json = JSON.parse($("#after_choose_jobs" + job_id).val());

										$("#TaskJobNo").val(json.no);
										$("#TaskJobId").val(job_id);
										$("#TaskJobName").val(json.name);

										$("#window_popup_jobs").data("kendoWindow").close();
										tasks_auto_save_entry();
										return false;
									}
								</script>
							</div>

							<!-- SALES ORDER -->
							<p class="clear">
								<span class="label_1 float_left minw_lab">
									<?php
										$link = 'javascript:void(0)';
										if(isset($this->data['Task']['salesorder_id']) && is_object($this->data['Task']['salesorder_id'])){
											$link = URL . '/salesorders/entry/' . $this->data['Task']['salesorder_id'].'?sub_tab=tasks';
										}
									?>
									<a id="link_to_salesorders" href="<?php echo $link; ?>" onclick="tasks_entry_salesorder_input()"><?php echo translate('Sales order'); ?></a>
								</span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->hidden('Task.salesorder_id'); ?>

								<div style=" float:left;;margin-left:1%;border-right:1px solid #ddd;display:block; width:27%;">
									<?php echo $this->Form->input('Task.salesorder_no', array(
										'class' => 'input_1 float_left ',
										'readonly' => true,
									)); ?>
								</div>
								<div class="jt_box_field " style=" width:69%;height:21px;">
									<?php echo $this->Form->input('Task.salesorder_name', array(
										'class' => 'input_1 float_left ',
										'style' => 'width:89%',
										'readonly' => true
									)); ?>

								</div>
								<span id="click_open_window_salesorders" class="iconw_m indent_dw_m"></span>

								<script type="text/javascript">
									$(function(){
										// kiểm tra xem đã chọn company chưa
										tasks_init_popup_salesorders();
									});

									function tasks_init_popup_salesorders( force_re_install ){
										var parameter_get = "";
										if( $("#TaskCompanyId").val() != "" ){
											parameter_get = "?company_id=" + $("#TaskCompanyId").val() + "&company_name=" + $("#TaskCompanyName").val();
										}

										if( force_re_install == "force_re_install" ){
											window_popup("salesorders", "Specify Sales Order", "", "", parameter_get, "force_re_install");

										}else{
											window_popup("salesorders", "Specify Sales Order", "", "", parameter_get);
										}

									}

									function after_choose_salesorders(salesorder_id, salesorder_name){
										$("#link_to_salesorders").attr("href", "<?php echo URL; ?>/salesorders/entry/" + salesorder_id);

										var json = JSON.parse($("#after_choose_salesorders" + salesorder_id).val());

										$("#TaskSalesorderNo").val(json.code);
										$("#TaskSalesorderId").val(salesorder_id);
										$("#TaskSalesorderName").val(json.name);

										$("#window_popup_salesorders").data("kendoWindow").close();
										tasks_auto_save_entry();
										return false;
									}

									function tasks_entry_salesorder_input(){
										if( $("#TaskSalesorderId").val() == "" ){
											alerts("Warning", "This number is not connected to any specific sales order");
										}
										return false;
									}
								</script>
							</div>

							<!-- PURCHASE ORDER -->
							<p class="clear">
								<span class="label_1 fixbor3 float_left minw_lab">
									<?php
										$link = 'javascript:void(0)';
										if(isset($this->data['Task']['purchaseorder_id']) && is_object($this->data['Task']['purchaseorder_id'])){
											$link = URL . '/purchaseorders/entry/' . $this->data['Task']['purchaseorder_id'];
										}
									?>
									<a id="link_to_purchaseorders" href="<?php echo $link; ?>" onclick="tasks_entry_purchaseorder_input()"><?php echo translate('Purchase Order'); ?></a>
								</span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->hidden('Task.purchaseorder_id'); ?>

								<div style=" float:left;;margin-left:1%;border-right:1px solid #ddd;display:block; width:27%;">
									<?php echo $this->Form->input('Task.purchaseorder_no', array(
										'class' => 'input_1 float_left ',
										'readonly' => true,
									)); ?>
								</div>
								<div class="jt_box_field " style=" width:69%;height:21px;">
									<?php echo $this->Form->input('Task.purchaseorder_name', array(
										'class' => 'input_1 float_left ',
										'style' => 'width:89%',
										'readonly' => true
									)); ?>

								</div>
								<span id="click_open_window_purchaseorders" class="iconw_m indent_dw_m"></span>

								<script type="text/javascript">
									$(function(){
										// kiểm tra xem đã chọn company chưa
										tasks_init_popup_purchaseorders();
									});

									function tasks_init_popup_purchaseorders( force_re_install ){
										var parameter_get = "";
										if( $("#TaskCompanyId").val() != "" ){
											parameter_get = "?company_id=" + $("#TaskCompanyId").val() + "&company_name=" + $("#TaskCompanyName").val();
										}

										if( force_re_install == "force_re_install" ){
											window_popup("purchaseorders", "Specify Sales Order", "", "", parameter_get, "force_re_install");

										}else{
											window_popup("purchaseorders", "Specify Sales Order", "", "", parameter_get);
										}

									}

									function after_choose_purchaseorders(purchaseorder_id, purchaseorder_name){
										$("#link_to_purchaseorders").attr("href", "<?php echo URL; ?>/purchaseorders/entry/" + purchaseorder_id);

										var json = JSON.parse($("#after_choose_purchaseorders" + purchaseorder_id).val());

										$("#TaskPurchaseorderNo").val(json.code);
										$("#TaskPurchaseorderId").val(purchaseorder_id);
										$("#TaskPurchaseorderName").val(json.name);

										$("#window_popup_purchaseorders").data("kendoWindow").close();
										tasks_auto_save_entry();
										return false;
									}

									function tasks_entry_purchaseorder_input(){
										if( $("#TaskPurchaseorderId").val() == "" ){
											alerts("Warning", "This number is not connected to any specific sales order");
										}
										return false;
									}
								</script>
							</div>

						</div>
					</div><!--END Tab1 -->
				</div>
			</div>
			<?php echo $this->Form->end(); ?>
		</div><!--  END DIV tasks_form_auto_save -->

		<div class="clear"></div>
		<!-- DIV MENU NGANG -->
		<div class="clear block_dent3">
			<div class="box_inner">
				<ul id="tasks_ul_sub_content" class="ul_tab">
					<li id="general" class="<?php if($sub_tab == 'general'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('General'); ?></a>
					</li>
					<li id="resources" class="<?php if($sub_tab == 'resources'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Resources'); ?></a>
					</li>
					<li id="timelog" class="<?php if($sub_tab == 'timelog'){ ?>active<?php } ?>">
						<a href="javascript:void(0)" style="color:red"><?php echo translate('TimeLog'); ?></a>
					</li>
					<li id="expensive" class="<?php if($sub_tab == 'expensive'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Expensive'); ?></a>
					</li>
					<li id="documents" class="<?php if($sub_tab == 'documents'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Documents'); ?></a>
					</li>
					<li id="other" class="<?php if($sub_tab == 'other'){ ?>active<?php } ?>">
						<a href="javascript:void(0)" style="color:red"><?php echo translate('Other'); ?></a>
					</li>
					<p class="clear"></p>
				</ul>
			</div>
		</div>

		<!-- DIV NOI DUNG CUA CAC SUBTAB -->
		<div id="tasks_sub_content" class="clear_percent">
        	<span style="padding: 50%;"><img src="<?php echo URL.'/theme/'.$theme.'/images/ajax-loader.gif'; ?>" title="Loading..." /></span>
			<?php //echo $this->element('../Tasks/' . $sub_tab); ?>
		</div>

	</div>
	<div class="clear"></div>
</div>

<?php echo $this->element('../Tasks/js'); ?>
<script type="text/javascript">
    $(function(){
        if(window.location.href.indexOf("#") != -1) {
            var id = window.location.href.split("#");
            id = id[1];
            $("#"+id,".ul_tab").click();
        } else
            $("#<?php echo $sub_tab ?>",".ul_tab").click();
    })
</script>