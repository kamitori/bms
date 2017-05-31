<?php echo $this->element('entry_tab_option'); ?>
<div id="content" class="fix_magr">
    <div class="clear">

        <div id="<?php echo $controller; ?>_entry_search">
            <form>
                <div class="clear_percent">
                    <div class="clear_percent_1 float_left">
                        <div class="tab_1 block_dent_a">
                            <p class="clear">
                                <span class="label_1 float_left fixbor"><?php echo translate('Task no'); ?></span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->input('Task.no', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                            </div>
                            <p class="clear"></p>

                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Task'); ?></span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->input('Task.name', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                            </div>
                            <p class="clear"></p>

                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Task type'); ?></span>
                                <div class="width_in float_left indent_input_tp" style="padding: 0 0 0 1%;">
                                    <?php echo $this->Form->input('Task.type', array(
                                            'class' => 'input_4 input_select',
                                            'readonly' => true,
                                            'placeholder' => 1
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
                                <span class="label_1 float_left fixbor2">
                                    <?php echo translate('Responsible'); ?>
                                </span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->hidden('Task.our_rep_id'); ?>
                                <?php echo $this->Form->input('Task.our_rep', array(
                                        'class' => 'input_4 float_left',
                                        'readonly' => true,
                                        'placeholder' => 1
                                )); ?>
                                <span id="click_open_window_contacts_responsible" class="iconw_m indent_dw_m"></span>

                                <script type="text/javascript">
                                $(function(){
                                    // kiểm tra xem đã chọn company chưa
                                    tasks_init_popup_contacts_responsible();
                                });

                                function tasks_init_popup_contacts_responsible( force_re_install ){
                                    var parameter_get = "";
                                    parameter_get = "?is_employee=1";

                                    if( force_re_install == "force_re_install" ){
                                        window_popup("contacts", "Specify user responsible", "_responsible", "", parameter_get, "force_re_install");

                                    }else{
                                        window_popup("contacts", "Specify user responsible", "_responsible", "", parameter_get);
                                    }

                                }
                                function after_choose_contacts_responsible(contact_id, contact_name) {
                                    $("#link_to_contacts_responsible").attr("href", "<?php echo URL; ?>/contacts/entry/" + contact_id);
                                    $("#TaskOurRepId").val(contact_id);
                                    $("#TaskOurRep").val(contact_name);
                                    $("#window_popup_contacts_responsible").data("kendoWindow").close();
                                    tasks_auto_save_entry();
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
                                    <span class="label_1 float_left fixbor minw_lab"><?php echo translate('Start'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Task.work_start', array(
                                        'class' => 'JtSelectDate input_4 float_left',
                                        'placeholder' => 1
                                    )); ?>
                                </div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"><?php echo translate('Finish'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Task.work_end', array(
                                        'class' => 'JtSelectDate input_4 float_left',
                                        'placeholder' => 1
                                    )); ?>
                                </div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"><?php echo translate('Priority'); ?></span>
                                    </p><div class="width_in3 float_left indent_input_tp">
                                        <?php echo $this->Form->input('Task.priority', array(
                                            'class' => 'input_4 input_select',
                                            'readonly' => true,
                                            'placeholder' => 1
                                        )); ?>
                                        <?php echo $this->Form->hidden('Task.priority_id'); ?>
                                        <script type="text/javascript">
                                            $(function () {
                                                $("#TaskPriority").combobox(<?php echo json_encode($arr_priority); ?>);
                                            });
                                        </script>
                                    </div>

                                <p class="clear">
                                    <span class="label_1 float_left fixbor2 minw_lab"><?php echo translate('Status'); ?></span>
                                    </p><div class="width_in3 float_left indent_input_tp">
                                        <?php echo $this->Form->input('Task.status', array(
                                            'class' => 'input_4 input_select',
                                            'readonly' => true,
                                            'placeholder' => 1
                                        )); ?>
                                        <?php echo $this->Form->hidden('Task.status_id'); ?>
                                        <script type="text/javascript">
                                            $(function () {
                                                $("#TaskStatus").combobox(<?php echo json_encode($arr_tasks_status); ?>);
                                            });
                                        </script>
                                    </div>

                            </div>
                            <div class="tab_1_inner float_left">
                                <p class="clear">
                                    <span class="label_1 float_left minw_lab">
                                        <?php echo translate('Company'); ?>
                                    </span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->hidden('Task.company_id'); ?>
                                    <?php echo $this->Form->input('Task.company_name', array(
                                            'class' => 'input_4 float_left ',
                                            'placeholder' => 1
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
                                            tasks_init_popup_salesorders("force_re_install");

                                            tasks_init_popup_contacts("force_re_install");

                                            return false;
                                        }
                                    </script>
                                </div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab">
                                        <?php echo translate('Contact'); ?>
                                    </span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->hidden('Task.contact_id'); ?>
                                    <?php echo $this->Form->input('Task.contact_name', array(
                                            'class' => 'input_4 float_left ',
                                            'placeholder' => 1
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
                                        <?php echo translate('Job'); ?>
                                    </span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->hidden('Task.job_id'); ?>

                                    <div style=" float:left;;margin-left:1%;border-right:1px solid #ddd;display:block; width:27%;">
                                        <?php echo $this->Form->input('Task.job_no', array(
                                            'class' => 'input_4 float_left ',
                                            'placeholder' => 1
                                        )); ?>
                                    </div>
                                    <div class="jt_box_field " style=" width:69%;height:21px;">
                                        <?php echo $this->Form->input('Task.job_name', array(
                                            'class' => 'input_4 float_left ',
                                            'placeholder' => 1
                                        )); ?>

                                    </div>
                                    <span id="click_open_window_jobs" class="iconw_m indent_dw_m"></span>

                                    <script type="text/javascript">
                                        $(function(){
                                            window_popup('jobs', 'Specify Job');
                                        });

                                        function after_choose_jobs(job_id, job_name){
                                            $("#link_to_jobs").attr("href", "<?php echo URL; ?>/jobs/entry/" + job_id);
                                            $("#TaskJobNo").val($("#window_popup_job_no_" + job_id).val());
                                            $("#TaskJobId").val(job_id);
                                            $("#TaskJobName").val(job_name);
                                            $("#window_popup_jobs").data("kendoWindow").close();
                                            tasks_auto_save_entry();
                                            return false;
                                        }
                                    </script>
                                </div>

                                <p class="clear">
                                    <span class="fixbor3 label_1 float_left minw_lab">
                                        <?php echo translate('Sales order'); ?>
                                    </span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->hidden('Task.salesorder_id'); ?>

                                    <div style=" float:left;;margin-left:1%;border-right:1px solid #ddd;display:block; width:27%;">
                                        <?php echo $this->Form->input('Task.salesorder_no', array(
                                            'class' => 'input_4 float_left ',
                                            'placeholder' => 1
                                        )); ?>
                                    </div>
                                    <div class="jt_box_field " style=" width:69%;height:21px;">
                                        <?php echo $this->Form->input('Task.salesorder_name', array(
                                            'class' => 'input_4 float_left ',
                                            'style' => 'width:89%',
                                            'placeholder' => 1
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
                                            $("#TaskSalesorderNo").val($("#window_popup_salesorder_no_" + salesorder_id).val());
                                            $("#TaskSalesorderId").val(salesorder_id);
                                            $("#TaskSalesorderName").val(salesorder_name);
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

                            </div>
                            <div class="tab_1_inner float_left">

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab">
                                        <?php echo translate('Enquiry'); ?>
                                    </span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->hidden('Task.enquiry_id'); ?>

                                    <div style=" float:left;;margin-left:1%;border-right:1px solid #ddd;display:block; width:27%;">
                                        <?php echo $this->Form->input('Task.enquiry_no', array(
                                            'class' => 'input_4 float_left ',
                                            'placeholder' => 1
                                        )); ?>
                                    </div>
                                    <div class="jt_box_field " style=" width:69%;height:21px;">
                                        <?php echo $this->Form->input('Task.enquiry_name', array(
                                                'class' => 'input_4 float_left ',
                                                'placeholder' => 1
                                        )); ?>
                                    </div>
                                    <span class="iconw_m indent_dw_m" id="click_open_window_enquiries"></span>
                                    <script type="text/javascript">
                                        $(function(){
                                            window_popup('enquiries', 'Specify enquiry');
                                        });

                                        function after_choose_enquiries(enquiry_id, enquiry_name){
                                            $("#link_to_enquiries").attr("href", "<?php echo URL; ?>/enquiries/entry/" + enquiry_id);
                                            $("#TaskEnquiryNo").val($("#window_popup_enquiry_no_" + enquiry_id).val());
                                            $("#TaskEnquiryId").val(enquiry_id);
                                            $("#TaskEnquiryName").val(enquiry_name);
                                            $("#window_popup_enquiries").data("kendoWindow").close();
                                            tasks_auto_save_entry();

                                            return false;
                                        }
                                    </script>
                                </div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <input id="tasks_days_left" class="input_1 float_left" type="text" value="" readonly="true">
                                </div>

                                 <p class="clear">
                                    <span class="label_1 float_left minw_lab"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">

                                </div>

                                <p class="clear">
                                    <span class="label_1 float_left fixbor3 minw_lab"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp"></div>

                            </div>
                        </div><!--END Tab1 -->
                    </div>
                </div>
            </form>
        </div><!--  END DIV tasks_form_auto_save -->

        <div class="clear"></div>

    </div>
    <div class="clear"></div>
</div>

<?php echo $this->element('../Tasks/js_search'); ?>