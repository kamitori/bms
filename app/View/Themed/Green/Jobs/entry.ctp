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
		<div id="jobs_form_auto_save">
			<?php echo $this->Form->create('Job'); ?>
			<?php echo $this->Form->hidden('Job._id', array('value' => (string)$this->data['Job']['_id'])); ?>
			<div class="clear_percent">
				<div class="clear_percent_1 float_left">
					<div class="tab_1 block_dent_a">
						<p class="clear">
							<span class="label_1 float_left fixbor"><?php echo translate('Ref no'); ?></span>
						</p>
						<div class="width_in float_left indent_input_tp">
							<?php
								echo $this->Form->input('Job.no', array(
									'class' => 'input_1 float_left',
									'readonly'=>true,
									'disabled'	=>true,
								));
							?>
						</div>
						<input type="hidden" id="password_store" value="" />
						<input type="hidden" id="password_jobs" name="password_jobs" value="" />
						<p class="clear">
							<span class="label_1 float_left"><?php echo translate('Job heading'); ?></span>
						</p>
						<div class="width_in float_left indent_input_tp">
							<?php echo $this->Form->input('Job.name', array(
									'class' => 'input_1 float_left'
							)); ?>
						</div>

						<p class="clear">
							<?php
								$link = 'javascript:void(0)';
								if(isset($this->data['Job']['company_id']) && is_object($this->data['Job']['company_id'])){
									$link = URL . '/companies/entry/' . $this->data['Job']['company_id'];
								}
							?>
							<span class="label_1 float_left">
								<a id="link_to_companies" href="<?php echo $link; ?>"><?php echo translate('Company'); ?></a>
							</span>
						</p>
						<div class="width_in float_left indent_input_tp">
							<?php echo $this->Form->hidden('Job.company_id'); ?>
							<?php echo $this->Form->input('Job.company_name', array(
									'class' => 'input_1 float_left ',
									'readonly' => true
							)); ?>
							<input type="hidden" id="JobOurRep" name="data[Job][our_rep]" value="" />
							<input type="hidden" id="JobOurRepId" name="data[Job][our_rep_id]" value="" />
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
									$("#JobCompanyPhone").val(JSON.parse(json).phone);
							        $("#JobFax").val(JSON.parse(json).fax);
							        $("#JobOurRep").val(JSON.parse(json).our_rep);
							        $("#JobOurRepId").val(JSON.parse(json).our_rep_id.$id);

									$("#window_popup_companies").data("kendoWindow").close();
									jobs_auto_save_entry($("#JobCompanyName"),function(){
										$.ajax({
											url: '<?php echo URL; ?>/jobs/save_company_address/',
											success: function(result){
												if(result!='ok')
													alerts('Message',result);
												else
													location.reload();
											}
										});
									});
									jobs_init_popup_contacts( 'force_re_install' );
									return false;
								}
							</script>
						</div>

						<p class="clear">
							<?php
								$link = 'javascript:void(0)';
								if(isset($this->data['Job']['contact_id']) && is_object($this->data['Job']['contact_id'])){
									$link = URL . '/contacts/entry/' . $this->data['Job']['contact_id'];
								}
							?>
							<span class="label_1 float_left fixbor2">
								<a id="link_to_contacts" href="<?php echo $link; ?>" ><?php echo translate('Contact'); ?></a>
							</span>
						</p>
						<div class="width_in float_left indent_input_tp">
							<?php echo $this->Form->hidden('Job.contact_id'); ?>
                            <?php echo $this->Form->input('Job.contact_name', array(
                                    'class' => 'input_1 float_left ',
                                    'readonly' => true
                            )); ?>
                            <span class="icon_down_new float_right" id="click_open_window_contacts" title="Open window choose contact"></span>
                            <script type="text/javascript">
                                $(function(){
                                    // kiểm tra xem đã chọn company chưa
                                    jobs_init_popup_contacts();
                                });

                                function jobs_init_popup_contacts( force_re_install ){
                                    var parameter_get = "?is_customer=1";
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
                                    $("#JobEmail").val($("#window_popup_contact_email_" + contact_id).val());
                                    $("#JobContactId").val(contact_id);
                                    $("#JobContactName").val(contact_name);
                                    $("#window_popup_contacts").data("kendoWindow").close();

                                    $("#JobDirectPhone").val($("#window_popup_contact_direct_dial_" + contact_id).val());
                                    $("#JobMobile").val($("#window_popup_contact_mobile_" + contact_id).val());

                                    jobs_auto_save_entry();
                                    return false;
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
									'class' => 'JtSelectDate input_1 float_left',
									'readonly' => true
								)); ?>
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Finish date'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->input('Job.work_end', array(
									'class' => 'JtSelectDate input_1 float_left',
									'readonly' => true
								)); ?>
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Job type'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->input('Job.type', array(
										'class' => 'input_select',
										'readonly' => true
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
										'class' => 'input_select',
										'readonly' => true
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
									'class' => 'input_1 float_left'
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
									'class' => 'input_1 float_left',
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
									'class' => 'input_1 float_left',
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
									'class' => 'input_1 float_left',
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
									'class' => 'input_1 float_left',
								)); ?>
							</div>

							<p class="clear">
								<span class="label_1 float_left minw_lab"><?php echo translate('Customer PO no'); ?></span>
							</p>
							<div class="width_in3 float_left indent_input_tp">
								<?php echo $this->Form->input('Job.custom_po_no', array(
									'class' => 'input_1 float_left',
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
		</div><!--  END DIV jobs_form_auto_save -->

		<div class="clear"></div>
		<!-- DIV MENU NGANG -->
		<div class="clear block_dent3">
			<div class="box_inner">
				<ul id="jobs_ul_sub_content" class="ul_tab">
					<li id="general" class="<?php if($sub_tab == 'general'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('General'); ?></a>
					</li>
					<li id="resources" class="<?php if($sub_tab == 'resources'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Resources'); ?></a>
					</li>
					<!-- <li id="stages" class="<?php if($sub_tab == 'stages'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Stages'); ?></a>
					</li> -->
					<li id="tasks" class="<?php if($sub_tab == 'tasks'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Tasks'); ?></a>
					</li>
					<!-- <li id="timelog" class="<?php if($sub_tab == 'timelog'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('TimeLog'); ?></a>
					</li> -->
					<li id="quotes" class="<?php if($sub_tab == 'quotes'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Quotes'); ?></a>
					</li>
					<!-- <li id="budgets" class="<?php if($sub_tab == 'budgets'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Budgets'); ?></a>
					</li> -->
					<!-- <li id="costs" class="<?php if($sub_tab == 'costs'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Costs'); ?></a>
					</li> -->
					<li id="orders" class="<?php if($sub_tab == 'orders'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Orders'); ?></a>
					</li>

					<li id="invoices" class="<?php if($sub_tab == 'invoices'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Invoices'); ?></a>
					</li>
					<li id="purchaseorder" class="<?php if($sub_tab == 'purchaseorder'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Purchase orders'); ?></a>
					</li>
					<li id="shipping" class="<?php if($sub_tab == 'shipping'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Shipping'); ?></a>
					</li>
                    <!-- <li id="assembly" class="<?php if($sub_tab == 'assembly'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Assembly'); ?></a>
					</li> -->
					<li id="documents" class="<?php if($sub_tab == 'documents'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Documents'); ?></a>
					</li>
					<li id="other" class="<?php if($sub_tab == 'other'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Other'); ?></a>
					</li>
					<p class="clear"></p>
				</ul>
			</div>
		</div>

		<!-- DIV NOI DUNG CUA CAC SUBTAB -->
		<div id="jobs_sub_content" class="clear_percent">
        	<span style="padding: 50%;"><img src="<?php echo URL.'/theme/'.$theme.'/images/ajax-loader.gif'; ?>" title="Loading..." /></span>
			<?php //echo $this->element('../Jobs/' . $sub_tab); ?>
		</div>

	</div>
	<div class="clear"></div>
</div>

<?php echo $this->element('../Jobs/js'); ?>
<script type="text/javascript">
    $(function(){
        if(window.location.href.indexOf("#") != -1) {
            var id = window.location.href.split("#");
            id = id[1];
            $("#"+id,".ul_tab").click();
        } else
            $("#<?php echo $sub_tab ?>",".ul_tab").click();
        notifyTop('<?php echo $this->Session->flash(); ?>');
    })
</script>