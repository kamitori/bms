<?php echo $this->element('entry_tab_option'); ?>
<div id="content" class="fix_magr">
	<div class="clear">

		<div class="clear_percent">
			<div class="block_dent_a">
				<div class="title_1 float_left"><h1><span id="job_name_header"></span></h1></div>
				<div class="title_1 right_txt float_right">
					<h1 id="jobs_right_h1_header">
						<span id="job_company_name_header">
						</span> | <span id="job_status_header">
					</span></h1>
				</div>
			</div>
		</div>

		<div id="<?php echo $controller; ?>_entry_search">
			<?php echo $this->Form->create('Job'); ?>
			<div class="clear_percent">
				<div class="clear_percent_1 float_left">
					<div class="tab_1 block_dent_a">
						<p class="clear">
							<span class="label_1 float_left fixbor"><?php echo translate('Ref no'); ?></span>
						</p>
						<div class="width_in float_left indent_input_tp">
							<?php echo $this->Form->input('Job.no', array(
									'class' => 'input_4 float_left',
									'placeholder' => 1
							)); ?>
						</div>

						<p class="clear">
							<span class="label_1 float_left"><?php echo translate('Job name'); ?></span>
						</p>
						<div class="width_in float_left indent_input_tp">
							<?php echo $this->Form->input('Job.name', array(
									'class' => 'input_4 float_left',
									'placeholder' => 1
							)); ?>
						</div>

						<p class="clear">
							<?php
								$link = 'javascript:void(0)';
							?>
							<span class="label_1 float_left">
								<a id="link_to_companies" href="<?php echo $link; ?>"><?php echo translate('Company'); ?></a>
							</span>
						</p>
						<div class="width_in float_left indent_input_tp">
							<?php echo $this->Form->hidden('Job.company_id'); ?>
							<?php echo $this->Form->input('Job.company_name', array(
									'class' => 'input_4 float_left ',
									'readonly' => true,
									'placeholder' => 1
							)); ?>
							<span class="icon_down_new float_right" id="click_open_window_companies"></span>
							<script type="text/javascript">
								$(function(){
									window_popup('companies', 'Specify company');
								});

								function after_choose_companies(company_id, company_name){
									$("#link_to_companies").attr("href", "<?php echo URL; ?>/companies/entry/" + company_id);

									var json = $("#after_choose_companies" + company_id).val();

									$("#JobCompanyId").val(company_id);
									$("#JobCompanyName").val(JSON.parse(json).name);
                                    $("#window_popup_companies").data("kendoWindow").close();
									// jobs_init_popup_contacts( 'force_re_install' );
								}
							</script>
						</div>

						<p class="clear">
							<?php
								$link = 'javascript:void(0)';
							?>
							<span class="label_1 float_left fixbor2">
								<a id="link_to_contacts" href="<?php echo $link; ?>" ><?php echo translate('Contact'); ?></a>
							</span>
						</p>
						<div class="width_in float_left indent_input_tp">
							<?php echo $this->Form->hidden('Job.contact_id'); ?>
                            <?php echo $this->Form->input('Job.contact_name', array(
                                    'class' => 'input_4 float_left ',
                                    'readonly' => true,
                                    'placeholder' => 1
                            )); ?>
                            <span class="icon_down_new float_right" id="click_open_window_contacts" title="Open window choose contact"></span>
                            <script type="text/javascript">
                                $(function(){
                                    // kiểm tra xem đã chọn company chưa
                                    jobs_init_popup_contacts();
                                });

                                function jobs_init_popup_contacts( force_re_install ){
                                    var parameter_get = "?is_employee=1";
                                    if( $("#JobCompanyId").val() != "" ){
                                        parameter_get += "&company_id=" + $("#JobCompanyId").val() + "&company_name=" + $("#JobCompanyName").val();
                                    }

                                    if( force_re_install == "force_re_install" ){
                                        window_popup("contacts", "Specify contact", "", "", parameter_get, "force_re_install");

                                    }else{
                                        window_popup("contacts", "Specify contact", "", "", parameter_get);
                                    }

                                }

                                function after_choose_contacts(contact_id, contact_name){
                                    $("#link_to_contacts").attr("href", "<?php echo URL; ?>/contacts/entry/" + contact_id);
                                    $("#JobContactId").val(contact_id);
                                    $("#JobContactName").val(contact_name);
                                    $("#window_popup_contacts").data("kendoWindow").close();
                                }
                            </script>

						</div>

						<p class="clear"></p>
					</div><!--END Tab1 -->
				</div>
				<div class="clear_percent_2 float_right">
					<div class="tab_1 float_left block_dent8">
						<div class="tab_1_inner float_left">
							<p class="clear">
								<span class="label_1 float_left minw_lab fixbor"><?php echo translate('Start date'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->input('Job.work_start', array(
									'class' => 'JtSelectDate input_4 float_left',
									'placeholder' => 1,
									'readonly' => true
								)); ?>
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Finish date'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->input('Job.work_end', array(
									'class' => 'JtSelectDate input_4 float_left',
									'placeholder' => 1,
									'readonly' => true
								)); ?>
							</div>
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Job type'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->input('Job.type', array(
										'class' => 'input_select input_4',
										'placeholder' => 1
								)); ?>
								<?php echo $this->Form->hidden('Job.type_id'); ?>
								<script type="text/javascript">
							        $(function () {
							            $("#JobType").combobox(<?php echo json_encode($arr_jobs_type); ?>);
							        });
							    </script>
							</div>
							<p class="clear">
								<span class="label_1 float_left fixbor2 minw_lab"><?php echo translate('Status'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->input('Job.status', array(
										'class' => 'input_select input_4',
										'placeholder' => 1,
								)); ?>
								<?php echo $this->Form->hidden('Job.status_id'); ?>
								<script type="text/javascript">
							        $(function () {
							            $("#JobStatus").combobox(<?php echo json_encode($arr_jobs_status); ?>);
							        });
							    </script>
							</div>

						</div>
						<div class="tab_1_inner float_left">
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Email'); ?></span>
							</p>
							<div class="indent_new width_in3 float_left indent_input_tp">
								<?php echo $this->Form->input('Job.email', array(
									'class' => 'input_4 float_left',
									'placeholder' => 1
								)); ?>
								<a title="Create email" href="">
									<span class="icon_emaili"></span>
								</a>
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Company phone'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->input('Job.company_phone', array(
									'class' => 'input_4 float_left',
									'placeholder' => 1
								)); ?>
								<a title="Dial phone" href="">
									<span class="icon_phonec"></span>
								</a>
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Direct phone'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->input('Job.direct_phone', array(
									'class' => 'input_4 float_left',
									'placeholder' => 1
								)); ?>
								<a title="Dial phone" href="">
									<span class="icon_phonec"></span>
								</a>
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab fixbor3"><?php echo translate('Mobile'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->input('Job.mobile', array(
									'class' => 'input_4 float_left',
									'placeholder' => 1
								)); ?>
								<a title="Dial phone" href="">
									<span class="icon_phonec"></span>
								</a>
							</div>

						</div>
						<div class="tab_1_inner float_left">
							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Fax'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->input('Job.fax', array(
									'class' => 'input_4 float_left',
									'placeholder' => 1
								)); ?>
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Customer PO no'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->input('Job.custom_po_no', array(
									'class' => 'input_4 float_left',
									'placeholder' => 1
								)); ?>
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab"></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab fixbor3"></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
							</div>
						</div>
					</div><!--END Tab1 -->
				</div>
			</div>
			<?php echo $this->Form->end(); ?>
		</div>
	</div>
	<div class="clear"></div>
</div>

<?php echo $this->element('../Jobs/js_search'); ?>