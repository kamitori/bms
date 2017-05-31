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

	<div id="enquiries_form_auto_save">
	<?php echo $this->Form->create('Enquiry'); ?>
	<?php echo $this->Form->hidden('Enquiry._id', array('value' => (string)$this->data['Enquiry']['_id'])); ?>

	<div class="clear_percent">
		<div class="clear_percent_1 float_left">
			<div class="tab_1 block_dent_a">
				<p class="clear">
					<span class="label_1 float_left fixbor"><?php echo translate('Ref no'); ?></span>
					<div class="width_in float_left indent_input_tp">
						<?php echo $this->Form->input('Enquiry.no', array(
								'class' => 'input_1 float_left'
						)); ?>
					</div>
				</p>

				<p class="clear">
					<span class="label_1 float_left">
						<?php
							$link = 'javascript:void(0)';
							if(isset($this->data['Enquiry']['company_id']) && is_object($this->data['Enquiry']['company_id'])){
								$link = URL . '/companies/entry/' . $this->data['Enquiry']['company_id'];
							}
						?>
						<a class="link_Contact" id="link_to_companies" href="<?php echo $link; ?>" ><?php echo translate('Company'); ?></a>
					</span>

					<div class="width_in float_left indent_input_tp">
						<?php echo $this->Form->hidden('Enquiry.company_id'); ?>
						<?php echo $this->Form->input('Enquiry.company', array(
								'class' => 'input_1 float_left '
						)); ?>
						<span class="icon_down_new float_right" id="click_open_window_companies"></span>
						<script type="text/javascript">
							$(function(){
								window_popup('companies', 'Specify company');
							});

							function after_choose_companies(company_id, company_name){
								$("#link_to_companies").attr("href", "<?php echo URL; ?>/companies/entry/" + company_id);

								var json = $("#after_choose_companies" + company_id).val();

								$("#EnquiryCompanyId").val(company_id);
								$("#EnquiryCompany").val(JSON.parse(json).name);
								$("#EnquiryCompanyPhone").val(JSON.parse(json).phone);
								$("#EnquiryCompanyFax").val(JSON.parse(json).fax);
								$("#EnquiryWeb").val(JSON.parse(json).web);

								var addresses_default_key = JSON.parse(json).addresses_default_key;
								$("#DefaultAddress1").val(JSON.parse(json).addresses[addresses_default_key].address_1);
								$("#DefaultAddress2").val(JSON.parse(json).addresses[addresses_default_key].address_2);
								$("#DefaultAddress3").val(JSON.parse(json).addresses[addresses_default_key].address_3);
								$("#DefaultTownCity").val(JSON.parse(json).addresses[addresses_default_key].town_city);
								$("#DefaultProvinceState").val(JSON.parse(json).addresses[addresses_default_key].province_state);
								$("#DefaultZipPostcode").val(JSON.parse(json).addresses[addresses_default_key].zip_postcode);
								$("#DefaultCountry").val(JSON.parse(json).addresses[addresses_default_key].country);
								$("#DefaultProvinceStateId").val(JSON.parse(json).addresses[addresses_default_key].province_state_id);
								$("#DefaultCountryId").val(JSON.parse(json).addresses[addresses_default_key].country_id);

								$("#window_popup_companies").data("kendoWindow").close();
								enquiries_auto_save_entry();
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
							'class' => 'input_select',
							'readonly' => true
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
						<?php
							$link = 'javascript:void(0)';
							if(isset($this->data['Enquiry']['contact_id']) && is_object($this->data['Enquiry']['contact_id'])){
								$link = URL . '/contacts/entry/' . $this->data['Enquiry']['contact_id'];
							}
						?>
						<a class="link_Contact" id="link_to_contacts" href="<?php echo $link; ?>" ><?php echo translate('Contact name'); ?></a>
					</span>
					<div class="indent_new width_in float_left">
						<?php echo $this->Form->hidden('Enquiry.contact_id'); ?>
						<?php echo $this->Form->input('Enquiry.contact_name', array(
								'class' => 'input_1 float_left '
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
								$("#link_to_contacts").attr("href", "<?php echo URL; ?>/contacts/entry/" + contact_id);
								$("#EnquiryContactId").val(contact_id);
								$("#EnquiryContactName").val(contact_name);
								$("#window_popup_contacts").data("kendoWindow").close();

								$("#EnquiryContactEmail").val($("#window_popup_contact_email_" + contact_id).val());
								$("#EnquiryMobile").val($("#window_popup_contact_mobile_" + contact_id).val());
								$("#EnquiryHomePhone").val($("#window_popup_contact_home_phone_" + contact_id).val());

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
								'class' => 'input_1 float_left JtSelectDate',
								'readonly' => true
						)); ?>
					</div>
				</p>
				<p class="clear">
					<span class="label_1 float_left"><?php echo translate('Status'); ?></span>
					<div class="width_in float_left indent_input_tp">
						<?php echo $this->Form->input('Enquiry.status', array(
							'class' => 'input_select',
							'default'=>'Hot',
							'readonly' => true
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
							'class' => 'input_select',
							'readonly' => true
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
						<?php $link = 'javascript:void(0)';
								if(strlen(trim($this->data['Enquiry']['web'])) > 0){
									$link = ' http://'. str_replace(' http://', '', $this->data['Enquiry']['web']);
								} ?>
						<a id="enquiries_web" href="<?php echo $link; ?>"><?php echo translate('Web'); ?></a>
					</span>
					<div class="width_in float_left indent_input_tp">
						<?php echo $this->Form->input('Enquiry.web', array(
							'class' => 'input_1 float_left'
						)); ?>
					</div>
				</p>

				<p class="clear"></p>
			</div><!--END Tab1 -->
		</div>
		<div class="clear_percent_2 float_right">
			<div class="tab_1 float_left block_dent8">
				<?php echo $this->element('box_type/address', array(
							'address_label' => array('Address'),
							'address_more_line' => 0,
							'address_controller' => array('Enquiry'),
							'address_country_id' => array($this->data['Enquiry']['default_country_id']),
							'address_value' => array(
								'default' => array(
									$this->data['Enquiry']['default_address_1'],
									$this->data['Enquiry']['default_address_2'],
									$this->data['Enquiry']['default_address_3'],
									$this->data['Enquiry']['default_town_city'],
									$this->data['Enquiry']['default_country_id'],
									$this->data['Enquiry']['default_province_state'],
									$this->data['Enquiry']['default_zip_postcode']
								)
							),
							'address_key' => array('default'),
							'address_conner' => array(
								array(
									'top' => 'hgt fixbor',
									'bottom' => 'fixbor3 fix_bottom_address fix_bot_bor'
								)
							),
							'address_more_line' => 1
					)); ?>


				<div class="tab_1_inner float_left">
					<p class="clear">
						<span class="label_1 float_left minw_lab"><?php echo translate('Company phone'); ?></span>
						<div class="indent_new width_in3 float_left indent_input_tp">
							<?php echo $this->Form->input('Enquiry.company_phone', array(
								'class' => 'input_1 float_left'
							)); ?>
							<span class="icon_phonec"></span>
						</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab"><?php echo translate('Direct phone'); ?></span>
						<div class="width_in3 float_left indent_input_tp">
							<?php echo $this->Form->input('Enquiry.direct_phone', array(
								'class' => 'input_1 float_left'
							)); ?>
							<span class="icon_phonec"></span>
						</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab"><?php echo translate('Home phone'); ?></span>
						<div class="width_in3 float_left indent_input_tp">
							<?php echo $this->Form->input('Enquiry.home_phone', array(
								'class' => 'input_1 float_left'
							)); ?>
							<span class="icon_phonec"></span>
						</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab"><?php echo translate('Mobile'); ?></span>
						<div class="width_in3 float_left indent_input_tp">
							<?php echo $this->Form->input('Enquiry.mobile', array(
								'class' => 'input_1 float_left'
							)); ?>
							<span class="icon_phonec"></span>
						</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab"><?php echo translate('Company fax'); ?></span>
						<div class="width_in3 float_left indent_input_tp">
							<?php echo $this->Form->input('Enquiry.company_fax', array(
								'class' => 'input_1 float_left'
							)); ?>
							<span class="icon_down_pl"></span>
						</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab"><?php echo translate('Direct fax'); ?></span>
						<div class="width_in3 float_left indent_input_tp">
							<?php echo $this->Form->input('Enquiry.direct_fax', array(
								'class' => 'input_1 float_left'
							)); ?>
							<span class="icon_down_pl"></span>
						</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab"><?php echo translate('Contact email'); ?></span>
						<div class="width_in3 float_left indent_input_tp">
							<?php echo $this->Form->input('Enquiry.contact_email', array(
								'class' => 'input_1 float_left'
							)); ?>
							<span class="icon_emaili"></span>
						</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab fixbor3"><?php echo translate('Company email'); ?></span>
						<div class="width_in3 float_left indent_input_tp">
							<?php echo $this->Form->input('Enquiry.company_email', array(
								'class' => 'input_1 float_left'
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
								'class' => 'input_select',
								'readonly' => true
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
								'class' => 'input_select',
								'readonly' => true
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
								'class' => 'input_select',
								'readonly' => true
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
								'class' => 'input_select',
								'readonly' => true
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
								'class' => 'input_1 float_left',
							)); ?>
						</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab"><?php echo translate('No of staff'); ?></span>
						<div class="width_in3 float_left indent_input_tp">
							<?php echo $this->Form->input('Enquiry.no_of_staff', array(
								'class' => 'input_1 float_left',
							)); ?>
						</div>
					</p>
					<p class="clear">
						<span class="label_1 float_left minw_lab"><?php echo translate('Referned by'); ?></span>
						<div class="width_in3 float_left indent_input_tp">
							<?php echo $this->Form->input('Enquiry.referred', array(
								'class' => 'input_select',
								'readonly' => true
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
						<span class="label_1 float_left minw_lab fixbor3  ">
							<?php
								$link = 'javascript:void(0)';
								if(isset($this->data['Enquiry']['our_rep_id']) && is_object($this->data['Enquiry']['our_rep_id'])){
									$link = URL . '/contacts/entry/' . $this->data['Enquiry']['our_rep_id'];
								}
							?>
							<a class="link_Contact" id="link_to_contacts_responsible " href="<?php echo $link; ?>" ><?php echo translate('Our rep'); ?></a>
						</span>
						<div class="width_in3 float_left indent_input_tp">
							<?php echo $this->Form->hidden('Enquiry.our_rep_id'); ?>
							<?php echo $this->Form->input('Enquiry.our_rep', array(
									'class' => 'input_1 float_left ',
									'readonly' => true
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
									$("#link_to_contacts_responsible").attr("href", "<?php echo URL; ?>/contacts/entry/" + contact_id);
									$("#EnquiryOurRepId").val(contact_id);
									$("#EnquiryOurRep").val(contact_name);
									$("#window_popup_contacts_responsible").data("kendoWindow").close();

									enquiries_auto_save_entry();
									return false;
								}
							</script>
						</div>
					</p>

				</div>
			</div><!--END Tab1 -->
		</div>
	</div>

	<?php echo $this->Form->end(); ?>
	</div><!--  END DIV enquiries_form_auto_save -->

	<div class="clear"></div>
	<div class="clear block_dent3">
		<div class="box_inner">
			<ul id="enquiries_ul_sub_content" class="ul_tab">
				<li id="general" class="<?php if($sub_tab == 'general'){ ?>active<?php } ?>">
					<a href="javascript:void(0)"><?php echo translate('General'); ?></a>
				</li>
				<li id="quotes" class="<?php if($sub_tab == 'quotes'){ ?>active<?php } ?>">
					<a href="javascript:void(0)"><?php echo translate('Quotes'); ?></a>
				</li>
				<li id="tasks" class="<?php if($sub_tab == 'tasks'){ ?>active<?php } ?>">
					<a href="javascript:void(0)"><?php echo translate('Task'); ?></a>
				</li>
				<li id="documents" class="<?php if($sub_tab == 'documents'){ ?>active<?php } ?>">
					<a href="javascript:void(0)"><?php echo translate('Documents'); ?></a>
				</li>
				<li id="other" class="<?php if($sub_tab == 'other'){ ?>active<?php } ?>">
					<a href="javascript:void(0)"><?php echo translate('Other'); ?></a>
				</li>
			</ul>
		</div>
	</div>

	<!-- DIV NOI DUNG CUA CAC SUBTAB -->
	<div id="enquiries_sub_content" class="clear_percent">
		<?php echo $this->element('../Enquiries/' . $sub_tab); ?>
	</div>

	<div class="clear"></div>
</div>

<?php echo $this->element('../Enquiries/js'); ?>