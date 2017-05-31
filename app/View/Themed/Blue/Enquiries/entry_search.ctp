<?php echo $this->element('entry_tab_option');?>
<div id="content" class="fix_magr">
	<div class="clear_percent">
		<div class="block_dent_a">
			<div class="title_1 float_left">
				<h1><span id="enquiry_company_header"></span><span id="enquiry_contact_header"></span></h1>
			</div>
			<div class="title_1 right_txt float_right">
				<h1><span id="enquiry_status_header"></span><span id="enquiry_responsible_header"></span></h1>
			</div>
		</div>
	</div>

	<div id="<?php echo $controller; ?>_entry_search">
		<form>
			<div class="clear_percent">
				<div class="clear_percent_1 float_left">
					<div class="tab_1 block_dent_a">
						<p class="clear">
							<span class="label_1 float_left fixbor"><?php echo translate('Ref no'); ?></span>
							<div class="width_in float_left indent_input_tp">
								<?php echo $this->Form->input('Enquiry.no', array(
										'class' => 'input_4 float_left',
										'placeholder' => 1
								)); ?>
							</div>
						</p>

						<p class="clear">
							<span class="label_1 float_left">
								<?php echo translate('Company'); ?>
							</span>

							<div class="width_in float_left indent_input_tp">
								<?php echo $this->Form->hidden('Enquiry.company_id'); ?>
								<?php echo $this->Form->input('Enquiry.company', array(
										'class' => 'input_4 float_left ',
										'placeholder' => 1
								)); ?>
								<span class="icon_down_new float_right" id="click_open_window_companies"></span>
								<script type="text/javascript">
									$(function(){
										window_popup('companies', 'Specify company');
									});

									function after_choose_companies(company_id, company_name){
										var json = $("#after_choose_companies" + company_id).val();
										$("#EnquiryCompanyId").val(company_id);
										$("#EnquiryCompany").val(JSON.parse(json).name);
										$("#window_popup_companies").data("kendoWindow").close();
										enquiries_init_popup_contacts( "force_re_install" );
										return false;
									}
								</script>
							</div>
						</p>

						<p class="clear">
							<span class="label_1 float_left">
								<span class="color_hidden none_block"><?php echo translate('Contact'); ?>:</span> <?php echo translate('Title'); ?>
							</span>
							<div class="width_in float_left indent_input_tp">
								<?php echo $this->Form->input('Enquiry.title', array(
									'class' => 'input_4 input_select',
									'readonly' => true,
									'placeholder' => 1
								)); ?>
								<?php echo $this->Form->hidden('Enquiry.title_id'); ?>

								<script type="text/javascript">
									$(function () {
										$("#EnquiryTitle").combobox(<?php echo json_encode($arr_enquiry_title); ?>);
									});
								</script>
							</div>
						</p>

						<p class="clear">
							<span class="label_1 float_left">
								<?php echo translate('Contact name'); ?>
							</span>
							<div class="indent_new width_in float_left">
								<?php echo $this->Form->hidden('Enquiry.contact_id'); ?>
								<?php echo $this->Form->input('Enquiry.contact_name', array(
										'class' => 'input_4 float_left ',
										'placeholder' => 1
								)); ?>
								<span class="icon_down_new float_right" id="click_open_window_contacts"></span>

								<script type="text/javascript">
									$(function(){
										// kiểm tra xem đã chọn company chưa
										enquiries_init_popup_contacts();
									});

									function enquiries_init_popup_contacts( force_re_install ){
										var parameter_get = "?is_customer=1";
										if( $("#EnquiryCompanyId").val() != "" ){
											parameter_get += "&company_id=" + $("#EnquiryCompanyId").val() + "&company_name=" + $("#EnquiryCompany").val();
										}
										if( force_re_install == "force_re_install" ){
											window_popup("contacts", "Specify contact", "", "", parameter_get, "force_re_install");

										}else{
											window_popup("contacts", "Specify contact", "", "", parameter_get);
										}
									}

									function after_choose_contacts(contact_id, contact_name){
										$("#EnquiryContactId").val(contact_id);
										$("#EnquiryContactName").val(contact_name);
										$("#window_popup_contacts").data("kendoWindow").close();

										enquiries_auto_save_entry();
										return false;
									}
								</script>
							</div>
						</p>

						<p class="clear">
							<span class="label_1 float_left"><?php echo translate('Date'); ?></span>
							<div class="width_in float_left indent_input_tp">
								<?php echo $this->Form->input('Enquiry.date', array(
										'class' => 'input_4 float_left JtSelectDate',
										'readonly' => true,
										'placeholder' => 1
								)); ?>
							</div>
						</p>
						<p class="clear">
							<span class="label_1 float_left"><?php echo translate('Status'); ?></span>
							<div class="width_in float_left indent_input_tp">
								<?php echo $this->Form->input('Enquiry.status', array(
										'class' => 'input_4 input_select',
										'readonly' => true,
										'placeholder' => 1
								)); ?>
								<?php echo $this->Form->hidden('Enquiry.status_id'); ?>

								<script type="text/javascript">
									$(function () {
										$("#EnquiryStatus").combobox(<?php echo json_encode($arr_enquiry_status); ?>);
									});
								</script>
							</div>
							<p class="clear"></p>
						</p>

						<p class="clear">
							<span class="label_1 float_left "><?php echo translate('Rating'); ?></span>
							<div class="width_in float_left indent_input_tp">
								<?php echo $this->Form->input('Enquiry.rating', array(
										'class' => 'input_4 input_select',
										'readonly' => true,
										'placeholder' => 1
								)); ?>
								<?php echo $this->Form->hidden('Enquiry.rating_id'); ?>

								<script type="text/javascript">
									$(function () {
										$("#EnquiryRating").combobox(<?php echo json_encode($arr_enquiry_rating); ?>);
									});
								</script>
							</div>
						</p>

						<p class="clear">
							<span class="label_1 float_left fixbor2">
								<?php echo translate('Web'); ?>
							</span>
							<div class="width_in float_left indent_input_tp">
								<?php echo $this->Form->input('Enquiry.web', array(
										'class' => 'input_4 float_left',
										'placeholder' => 1
								)); ?>
							</div>
						</p>

						<p class="clear"></p>
					</div><!--END Tab1 -->
				</div>
				<div class="clear_percent_2 float_right">
					<div class="tab_1 float_left block_dent8">
						<?php
							echo $this->element('box_type/address', array(
							    'address_mode' => 'search',
								'address_label' => array('Default address'),
								'address_more_line' => 1,
								'address_controller' => array('Enquiry'),
								'address_country_id' => array(),
								'address_value' => array(
									'default' => array(
										'',
										'',
										'',
										'',
										'',
										'',
										'',
										''
									)
								),
								'address_key' => array('default'),
								'address_conner' => array(
									array(
										'top' => 'hgt fixbor',
										'bottom' => 'fixbor3 fix_bottom_address fix_bot_bor'
									)
								)
						)); ?>


						<div class="tab_1_inner float_left">
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Company phone'); ?></span>
								<div class="indent_new width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.company_phone', array(
										'class' => 'input_4 float_left',
										'placeholder' => 1
									)); ?>
									<span class="icon_phonec"></span>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Direct phone'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.direct_phone', array(
										'class' => 'input_4 float_left',
										'placeholder' => 1
									)); ?>
									<span class="icon_phonec"></span>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Home phone'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.home_phone', array(
										'class' => 'input_4 float_left',
										'placeholder' => 1
									)); ?>
									<span class="icon_phonec"></span>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Mobile'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.mobile', array(
										'class' => 'input_4 float_left',
										'placeholder' => 1
									)); ?>
									<span class="icon_phonec"></span>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Company fax'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.company_fax', array(
										'class' => 'input_4 float_left',
										'placeholder' => 1
									)); ?>
									<span class="icon_down_pl"></span>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Direct fax'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.direct_fax', array(
										'class' => 'input_4 float_left',
										'placeholder' => 1
									)); ?>
									<span class="icon_down_pl"></span>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Contact email'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.contact_email', array(
										'class' => 'input_4 float_left',
										'placeholder' => 1
									)); ?>
									<span class="icon_emaili"></span>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab fixbor3"><?php echo translate('Company email'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.company_email', array(
										'class' => 'input_4 float_left',
										'placeholder' => 1
									)); ?>
									<span class="icon_emaili"></span>
								</div>
							</p>
						</div>
						<div class="tab_1_inner float_left">
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Position'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.position', array(
										'class' => 'input_4 input_select',
										'readonly' => true,
										'placeholder' => 1
									)); ?>
									<?php echo $this->Form->hidden('Enquiry.position_id'); ?>

									<script type="text/javascript">
										$(function () {
											$("#EnquiryPosition").combobox(<?php echo json_encode($arr_enquiry_position); ?>);
										});
									</script>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Department'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.department', array(
										'class' => 'input_4 input_select',
										'readonly' => true,
										'placeholder' => 1
									)); ?>
									<?php echo $this->Form->hidden('Enquiry.department_id'); ?>

									<script type="text/javascript">
										$(function () {
											$("#EnquiryDepartment").combobox(<?php echo json_encode($arr_enquiry_department); ?>);
										});
									</script>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Type'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.type', array(
										'class' => 'input_4 input_select',
										'readonly' => true,
										'placeholder' => 1
									)); ?>
									<?php echo $this->Form->hidden('Enquiry.type_id'); ?>

									<script type="text/javascript">
										$(function () {
											$("#EnquiryType").combobox(<?php echo json_encode($arr_enquiry_type); ?>);
										});
									</script>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Category'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.category', array(
										'class' => 'input_4 input_select',
										'readonly' => true,
										'placeholder' => 1
									)); ?>
									<?php echo $this->Form->hidden('Enquiry.category_id'); ?>

									<script type="text/javascript">
										$(function () {
											$("#EnquiryCategory").combobox(<?php echo json_encode($arr_enquiry_category); ?>);
										});
									</script>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Enquiry value'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.enquiry_value', array(
										'class' => 'input_4 float_left',
										'placeholder' => 1
									)); ?>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('No of staff'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.no_of_staff', array(
										'class' => 'input_4 float_left',
										'placeholder' => 1
									)); ?>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Referned by'); ?></span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->input('Enquiry.referred', array(
										'class' => 'input_4 input_select',
										'readonly' => true,
										'placeholder' => 1
									)); ?>
									<?php echo $this->Form->hidden('Enquiry.referred_id'); ?>

									<script type="text/javascript">
										$(function () {
											$("#EnquiryReferred").combobox(<?php echo json_encode($arr_enquiry_referred); ?>);
										});
									</script>
								</div>
							</p>
							<p class="clear">
								<span class="label_1 float_left minw_lab fixbor3">
									<?php echo translate('Our rep'); ?>
								</span>
								<div class="width_in3 float_left indent_input_tp">
									<?php echo $this->Form->hidden('Enquiry.our_rep_id'); ?>
									<?php echo $this->Form->input('Enquiry.our_rep', array(
											'class' => 'input_4 float_left ',
											'readonly' => true,
											'placeholder' => 1
									)); ?>
									<span class="icon_down_new float_right" id="click_open_window_contacts_responsible"></span>
									<script type="text/javascript">
										$(function(){
											// kiểm tra xem đã chọn company chưa
											enquiries_init_popup_contacts_responsible();
										});

										function enquiries_init_popup_contacts_responsible(){
											var parameter_get = "?is_employee=1";
											window_popup("contacts", "Specify employee", "_responsible", "", parameter_get);
										}

										function after_choose_contacts_responsible(contact_id, contact_name){
											$("#EnquiryOurRepId").val(contact_id);
											$("#EnquiryOurRep").val(contact_name);
											$("#window_popup_contacts_responsible").data("kendoWindow").close();
											return false;
										}
									</script>
								</div>
							</p>

						</div>
					</div><!--END Tab1 -->
				</div>
			</div>
		</form>
	</div><!--  END DIV enquiries_form_auto_save -->

	<div class="clear"></div>

	<div class="clear"></div>
</div>
<?php echo $this->element('../Enquiries/js_search'); ?>