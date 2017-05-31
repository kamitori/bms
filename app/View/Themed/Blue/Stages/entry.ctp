<?php echo $this->element('entry_tab_option'); ?>
<div id="content" class="fix_magr">
	<div class="clear">

		<div class="clear_percent">
			<div class="block_dent_a">
				<div class="title_1 float_left"><h1><span id="stage_stage_header"></span><span id="stage_job_header"></span></h1></div>
				<div class="title_1 right_txt float_right">
					<h1 id="stages_right_h1_header">
						<span id="stage_status_header"></span><span id="stage_responsible_header"></span></h1>
				</div>
			</div>
		</div>

		<div id="stages_form_auto_save">
			<?php echo $this->Form->create('Stage'); ?>
			<?php echo $this->Form->hidden('Stage._id', array('value' => (string)$this->data['Stage']['_id'])); ?>
			<div class="clear_percent">
                <div class="clear_percent_1 float_left">
                    <div class="tab_1 block_dent_a">
                        <p class="clear">
                            <span class="label_1 float_left fixbor"><?php echo translate('Ref no'); ?></span>
                            </p>
                            <div class="indent_new width_in float_left">
                            	<?php echo $this->Form->input('Stage.no', array(
									'class' => 'input_1 float_left'
								)); ?>
                            </div>

                        <p class="clear">
                            <span class="label_1 float_left"><?php echo translate('Stage'); ?></span>
                            </p><div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Stage.stage', array(
                                    'class' => 'input_select',
                                    'readonly' => true
                                )); ?>
                                <?php echo $this->Form->hidden('Stage.stage_id'); ?>
                                <script type="text/javascript">
                                    $(function () {
                                        $("#StageStage").combobox(<?php echo json_encode($arr_stage_stage); ?>);
                                    });
                                </script>
                            </div>

                        <p class="clear">
                        	<?php
								$link = 'javascript:void(0)';
								if(isset($this->data['Stage']['our_rep_id']) && is_object($this->data['Stage']['our_rep_id'])){
									$link = URL . '/contacts/entry/' . $this->data['Stage']['our_rep_id'];
								}
							?>
							<span class="label_1 float_left">
								<a id="link_to_contacts" href="<?php echo $link; ?>" ><?php echo translate('Responsible'); ?></a>
							</span>
                        </p>
                        <!-- <div class="indent_new width_in float_left"> -->
                        <div class="width_in float_left indent_input_tp">
							<?php echo $this->Form->hidden('Stage.our_rep_id'); ?>
                            <?php echo $this->Form->input('Stage.our_rep', array(
                                    'class' => 'input_1 float_left ',
                                    'readonly' => true
                            )); ?>
                            <span class="icon_down_new float_right" id="click_open_window_contacts" title="Open window choose responsible"></span>
                            <script type="text/javascript">
                                $(function(){
                                    // kiểm tra xem đã chọn company chưa
                                    stages_init_popup_responsibles();
                                });

                                function stages_init_popup_responsibles(){
                                    var parameter_get = "?is_employee=1";
                                    window_popup("contacts", "Specify responsible", "", "", parameter_get);
                                }

                                function after_choose_contacts(contact_id, contact_name){
                                    $("#link_to_contacts").attr("href", "<?php echo URL; ?>/contacts/entry/" + contact_id);
                                    $("#StageOurRepId").val(contact_id);
                                    $("#StageOurRep").val(contact_name);
                                    $("#window_popup_contacts").data("kendoWindow").close();
                                    stages_auto_save_entry();
                                    return false;
                                }
                            </script>

						</div>

						<p class="clear">
                        	<?php
								$link = 'javascript:void(0)';
								if(isset($this->data['Stage']['job_id']) && is_object($this->data['Stage']['job_id'])){
									$link = URL . '/jobs/entry/' . $this->data['Stage']['job_id'];
								}
							?>
							<span class="label_1 float_left fixbor2">
								<a id="link_to_jobs" href="<?php echo $link; ?>" ><?php echo translate('Job'); ?></a>
							</span>
                        </p>
                        <!-- <div class="indent_new width_in float_left"> -->
                        <div class="width_in float_left indent_input_tp">
							<?php echo $this->Form->hidden('Stage.job_id'); ?>
                            <?php echo $this->Form->input('Stage.job', array(
                                    'class' => 'input_1 float_left ',
                                    'readonly' => true
                            )); ?>
                            <span class="icon_down_new float_right" id="click_open_window_jobs" title="Open window choose job"></span>
                            <script type="text/javascript">
                                $(function(){
                                    // kiểm tra xem đã chọn company chưa
                                    stages_init_popup_job();
                                });

                                function stages_init_popup_job(){
                                    window_popup("jobs", "Specify job");

                                }

                                function after_choose_jobs(job_id, job_name){
                                    $("#link_to_jobs").attr("href", "<?php echo URL; ?>/jobs/entry/" + job_id);
                                    $("#StageJobId").val(job_id);
                                    $("#StageJob").val(job_name);
                                    $("#window_popup_jobs").data("kendoWindow").close();
                                    stages_auto_save_entry();
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
                                <span class="label_1 float_left fixbor minw_lab"><?php echo translate('Start date'); ?></span>
                                </p><div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Stage.work_start', array(
                                        'class' => 'JtSelectDate input_1 float_left',
                                    )); ?>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab"><?php echo translate('Finish date'); ?></span>
                                </p><div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Stage.work_end', array(
                                        'class' => 'JtSelectDate input_1 float_left',
                                    )); ?>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab"><?php echo translate('Status'); ?></span>
                                </p><div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Stage.status', array(
                                        'class' => 'input_select',
                                        'readonly' => true
                                    )); ?>
                                    <?php echo $this->Form->hidden('Stage.status_id'); ?>
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#StageStatus").combobox(<?php echo json_encode($arr_stages_status); ?>);
                                        });
                                    </script>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left fixbor2 minw_lab"><?php echo translate('Day left'); ?></span>
                                </p><div class="width_in3 float_left indent_input_tp">
                                    <input id="stages_days_left" class="input_1 float_left" type="text" value="" readonly="true">
                                </div>
                        </div>
                        <div class="tab_3_inner float_left">
                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Notes'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Stage.note', array(
                                        'class' => 'input_1 float_left',
                                        'type' => 'textarea',
                                        'style' => 'border: none'
                                    )); ?>
                                </div>

                        </div>
                    </div><!--END Tab1 -->
                </div>
            </div>
			<?php echo $this->Form->end(); ?>
		</div><!--  END DIV stages_form_auto_save -->

		<div class="clear"></div>
		<!-- DIV MENU NGANG -->
		<div class="clear block_dent3">
			<div class="box_inner">
				<ul id="stages_ul_sub_content" class="ul_tab">
					<li id="general" class="<?php if($sub_tab == 'general'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('General'); ?></a>
					</li>
					<li id="resources" class="<?php if($sub_tab == 'resources'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Resources'); ?></a>
					</li>
					<li id="stages" class="<?php if($sub_tab == 'stages'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Stages'); ?></a>
					</li>
					<li id="tasks" class="<?php if($sub_tab == 'tasks'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Tasks'); ?></a>
					</li>
					<li id="timeLog" class="<?php if($sub_tab == 'timeLog'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('TimeLog'); ?></a>
					</li>
					<li id="quotes" class="<?php if($sub_tab == 'quotes'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Quotes'); ?></a>
					</li>
					<li id="budgets" class="<?php if($sub_tab == 'budgets'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Budgets'); ?></a>
					</li>
					<li id="costs" class="<?php if($sub_tab == 'costs'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Costs'); ?></a>
					</li>
					<li id="orders" class="<?php if($sub_tab == 'orders'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Orders'); ?></a>
					</li>
					<li id="shipping" class="<?php if($sub_tab == 'shipping'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Shipping'); ?></a>
					</li>
					<li id="invoices" class="<?php if($sub_tab == 'invoices'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Invoices'); ?></a>
					</li>
					<li id="assembly" class="<?php if($sub_tab == 'assembly'){ ?>active<?php } ?>">
						<a href="javascript:void(0)"><?php echo translate('Assembly'); ?></a>
					</li>
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
		<div id="stages_sub_content">
			<?php echo $this->element('../Stages/' . $sub_tab); ?>
		</div>

	</div>
	<div class="clear"></div>
</div>

<?php echo $this->element('../Stages/js'); ?>