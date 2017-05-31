<!--nocache-->
<?php echo $this->element('../'.$name.'/tab_option'); ?>
<!--/nocache-->
<div id="content">
    <div class="jt_ajax_note">Loading...</div>
    <div class="jt_ajax_email hidden" style="top:140px;">Sending emails, please waiting for a moment...</div>
    <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left">
            <h1>
                <span id="md_company_name">
                    <!--nocache-->
                    <?php echo $query['company_name']; ?>
                    <!--/nocache-->
                </span>
                <span class="md_center">
                    -
                </span>
                <span id="md_contact_name">
                    <!--nocache-->
                    <?php echo $query['contact_name']; ?>
                    <!--/nocache-->
                </span>
            </h1>
        </div>
        <div class="jt_module_title float_right jt_t_right">
            <h1>
                <span id="md_quotation_status">
                    <!--nocache-->
                    <?php echo $query['quotation_status']; ?>
                    <!--/nocache-->
                </span>
                <span class="md_center">
                    -
                </span>
                <span id="md_our_rep">
                    <!--nocache-->
                    <?php echo $query['our_rep']; ?>
                    <!--/nocache-->
                </span>
            </h1>
        </div>
    </div>
    <div id="quotations_form_auto_save">
        <!-- Add form -->
        <form class="form_quotations" action="" method="post">
            <div class="clear_percent">
                <!--Elememt Panel type 01-->
                <div class="jt_panel" style=" width:30%;float:left;">
                    <div class="jt_box" style=" width:100%;">
                        <div class="jt_box_line">
                            <div class=" jt_box_label fixbor" style=" width:25%;">
                                Ref no
                            </div>
                            <div class="jt_box_field " style=" width:30%;text-align:right;;">
                                <!--nocache-->
                                <input name="code" id="code" class="input_1 float_left  " type="text" value="<?php echo $query['code']; ?>" style=" width:50%; padding-left:6.5%;" readonly="readonly">
                                <!--/nocache-->
                                Type
                            </div>
                            <div class="jt_box_field" style=" width:41%;" id="field_after_quotetype" alt=";">
                                <!--nocache-->
                                <input class="input_select  " name="quotation_type" id="quotation_type" style="width: 110%; margin: 0px 18px 0px 0px;" type="text" value="<?php echo $query['quotation_status']; ?>" combobox_blank="1" readonly="readonly">
                                <input name="quotation_type_id" id="quotation_typeId" type="hidden" value="<?php echo $query['quotation_status']; ?>">
                                <!--/nocache-->
                                <script type="text/javascript">
                                    $(function () {
                                        $("#quotation_type").combobox(<?php echo json_encode($arr_combobox['quotations_type']); ?>);
                                    });
                                </script>
                            </div>
                        </div>
                        <input name="mongo_id" id="mongo_id" class="jthidden" type="hidden" value="<?php echo $query['_id'] ?>">
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:25%;">
                                <span class="jt_box_line_span link_to_company_name">
                                    Company
                                </span>
                            </div>
                            <div class="jt_box_field " style=" width:71%">
                                <!--nocache-->
                                <input name="company_name" id="company_name" class="input_1 float_left  " type="text" value="<?php echo $query['company_name'] ?>" style=" padding-left:2%;">
                                <input name="company_id" id="company_id" class="jthidden" type="hidden" value="<?php echo $query['company_id'] ?>">
                                <!--/nocache-->
                                <span class="iconw_m indent_dw_m" id="click_open_window_companiescompany_name" title="Specify Company"></span>
                                <script type="text/javascript">
                                    $(function(){
                                        window_popup('companies', 'Specify Company','company_name','click_open_window_companiescompany_name');
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:25%;">
                                <span class="jt_box_line_span link_to_contact_name">
                                    Contact
                                </span>
                            </div>
                            <div class="jt_box_field " style=" width:71%">
                                <!--nocache-->
                                <input name="contact_name" id="contact_name" class="input_1 float_left  " type="text" value="<?php echo $query['contact_name'] ?>" style=" padding-left:2%;">
                                <input name="contact_id" id="contact_id" class="jthidden" type="hidden" value="<?php echo $query['contact_id'] ?>">
                                <!--/nocache-->
                                <span class="iconw_m indent_dw_m" id="click_open_window_contactscontact_name" title="Specify Contact"></span>
                                <script type="text/javascript">
                                    $(function(){
                                        window_popup('contacts', 'Specify Contact','contact_name','click_open_window_contactscontact_name',get_para_contact());
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:25%;">
                                Phone
                            </div>
                            <div class="jt_box_field " style=" width:71%">
                                <!--nocache-->
                                <input name="phone" id="phone" class="input_1 float_left  " type="text" value="<?php echo $query['phone'] ?>" style=" padding-left:2%;" maxlength="20" onkeypress="return isPhone(event);">
                                <!--/nocache-->
                                <a href="javascript:void(0)" title="Dial phone - not yet implemented"><span class="icon_phonec"></span></a>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:25%;">
                                Email
                            </div>
                            <div class="jt_box_field " style=" width:71%">
                                <!--nocache-->
                                <input name="email" id="email" class="input_1 float_left  " type="text" value="<?php echo $query['email'] ?>" style=" padding-left:2%;" maxlength="100">
                                <!--/nocache-->
                                <a>
                                <span class="icon_emaili" title="Create email" style="cursor:pointer"></span>
                                </a>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:25%;">
                                Date
                            </div>
                            <div class="jt_box_field " style=" width:71%">
                                <!--nocache-->
                                <input name="quotation_date" id="quotation_date" class="input_1 float_left   hasDatepicker" type="text" value="<?php echo $this->Common->format_date($query['quotation_date']->sec); ?>" style=" padding-left:2%;" readonly="readonly">
                                <!--/nocache-->
                                <script>
                                    $(function() {
                                        $( "#quotation_date" ).datepicker({dateFormat: 'dd M, yy', changeMonth: true, changeYear: true, yearRange: "c-70:c+3" });
                                        // $( this ).datepicker({ changeMonth: true, changeYear: true, yearRange: "c-70:c+3"});
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:25%;">
                                <span class="jt_box_line_span link_to_our_rep">
                                    Our Rep
                                </span>
                            </div>
                            <div class="jt_box_field " style=" width:71%">
                                <!--nocache-->
                                <input name="our_rep" id="our_rep" class="input_1 float_left  " type="text" value="<?php echo $query['our_rep'] ?>" style=" " readonly="readonly">
                                <input name="our_rep_id" id="our_rep_id" class="jthidden" type="hidden" value="<?php echo $query['our_rep_id'] ?>">
                                <!--/nocache-->
                                <span class="iconw_m indent_dw_m" id="click_open_window_contactsour_rep" title="Specify Our Rep"></span>
                                <script type="text/javascript">
                                    $(function(){
                                        window_popup('contacts', 'Specify Our Rep','our_rep','click_open_window_contactsour_rep',get_para_employee());
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:25%;">
                                <span class="jt_box_line_span link_to_our_csr">Our CSR</span>
                            </div>
                            <div class="jt_box_field " style=" width:71%">
                                <!--nocache-->
                                <input name="our_csr" id="our_csr" class="input_1 float_left  " type="text" value="<?php echo $query['our_csr'] ?>" style=" " readonly="readonly">
                                <input name="our_csr_id" id="our_csr_id" class="jthidden" type="hidden" value="<?php echo $query['our_csr_id'] ?>">
                                <!--/nocache-->
                                <span class="iconw_m indent_dw_m" id="click_open_window_contactsour_csr" title="Specify Our CSR"></span>
                                <script type="text/javascript">
                                    $(function(){
                                        window_popup('contacts', 'Specify Our CSR','our_csr','click_open_window_contactsour_csr',get_para_employee());
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label fixbor2" style=" width:25%;">
                                &nbsp;
                            </div>
                            <div class="jt_box_field " style=" width:71%">
                                <span class="float_left">&nbsp;</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Elememt Panel type 02-->
                <div class="jt_panel" style=" width:69%;float:right;">
                    <?php echo $this->element('address'); ?>
                    <div class="jt_box" style=" width:33%;">
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:35%;">
                                Due date
                            </div>
                            <div class="jt_box_field " style=" width:61%">
                                <!--nocache-->
                                <input name="payment_due_date" id="payment_due_date" class="input_1 float_left   hasDatepicker" type="text" value="<?php if(is_object($query['payment_due_date'])) echo $this->Common->format_date($query['payment_due_date']); ?>" style=" padding-left:2.5%;" readonly="readonly">
                                <!--/nocache-->
                                <script>
                                    $(function() {
                                        $( "#payment_due_date" ).datepicker({dateFormat: 'dd M, yy', changeMonth: true, changeYear: true, yearRange: "c-70:c+3" });
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:35%;">
                                Status
                            </div>
                            <div class="jt_box_field " style=" width:61%">
                                <!--nocache-->
                                <input class="input_select  " name="quotation_status" id="quotation_status" style="margin: 0px 17px 0px 0px;" type="text" value="<?php echo $query['quotation_status'] ?>" combobox_blank="1" readonly="readonly">
                                <input name="quotation_status_id" id="quotation_statusId" type="hidden" value="<?php echo $query['quotation_status'] ?>">
                                <!--/nocache-->
                                <script type="text/javascript">
                                    $(function () {
                                        $("#quotation_status").combobox(<?php echo json_encode($arr_combobox['quotations_status']) ?>);
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:35%;">
                                Payment terms
                            </div>
                            <div class="jt_box_field " style=" width:41%;">
                                <!--nocache-->
                                <input class="input_select  " name="payment_terms" id="payment_terms" style="padding-left: 4.5%; margin: 0px 17px 0px 0px;" type="text" value="<?php echo $query['payment_terms'] ?>">
                                <input name="payment_terms_id" id="payment_termsId" type="hidden" value="<?php echo $query['payment_terms'] ?>">
                                <!--/nocache-->
                                <script type="text/javascript">
                                    $(function () {
                                        $("#payment_terms").combobox(<?php echo json_encode($arr_combobox['salesinvoices_payment_terms']) ?>);
                                    });
                                </script>
                            </div>
                            <div class="jt_after float_left" id="mx_payment_terms">&nbsp;days</div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:35%;">
                                Tax %
                            </div>
                            <div class="jt_box_field " style=" width:61%">
                                <!--nocache-->
                                <input class="input_select  " name="tax" id="tax" style="margin: 0px 17px 0px 0px;" type="text" value="<?php echo $arr_combobox['tax'][$query['tax']]; ?>">
                                <input name="tax_id" id="taxId" type="hidden" value="<?php echo $query['tax']; ?>">
                                <input name="taxval" id="taxval" class="jthidden" type="hidden" value="<?php echo $query['taxval']; ?>">
                                <!--/nocache-->
                                <script type="text/javascript">
                                    $(function () {
                                        $("#tax").combobox(<?php echo json_encode($arr_combobox['tax']) ?>);
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:35%;">
                                Customer PO no
                            </div>
                            <div class="jt_box_field " style=" width:61%">
                                <!--nocache-->
                                <input name="customer_po_no" id="customer_po_no" class="input_1 float_left  " type="text" value="<?php echo $query['customer_po_no'] ?>" style=" ">
                                <!--/nocache-->
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:35%;">
                                Heading
                            </div>
                            <div class="jt_box_field " style=" width:61%">
                                <!--nocache-->
                                <input name="heading" id="heading" class="input_1 float_left  " type="text" value="<?php echo $query['heading'] ?>" style=" ">
                                <input name="name" id="name" class="jthidden" type="hidden" value="<?php echo $query['name'] ?>">
                                <!--/nocache-->
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:35%;">
                                <span class="jt_box_line_span link_to_job_name">
                                    Job
                                </span>
                            </div>
                            <div style=" float:left;;margin-left:1%;border-right:1px solid #ddd;display:block; width:15%;">
                                <!--nocache-->
                                <input name="job_number" id="job_number" class="input_1 float_left  " type="text" value="<?php echo $query['job_number'] ?>" style=" width:91%;float:left;padding-left:5%;padding-right: 2%;" readonly="readonly">
                                <!--/nocache-->
                            </div>
                            <div class="jt_box_field " style=" width:44.5%;">
                                <!--nocache-->
                                <input name="job_name" id="job_name" class="input_1 float_left  " type="text" value="<?php echo $query['job_name'] ?>" style=" float:left;" readonly="readonly">
                                <input name="job_id" id="job_id" class="jthidden" type="hidden" value="<?php echo $query['job_id'] ?>">
                                <!--/nocache-->
                                <span class="iconw_m indent_dw_m" id="click_open_window_jobsjob_name" title="Specify Job"></span>
                                <script type="text/javascript">
                                    $(function(){
                                        window_popup('jobs', 'Specify Job','job_name','click_open_window_jobsjob_name');
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label " style=" width:35%;">
                                <span class="jt_box_line_span link_to_salesorder_name">
                                    Sales order
                                </span>
                            </div>
                            <div style=" float:left;;margin-left:1%;border-right:1px solid #ddd;display:block; width:15%;">
                                <!--nocache-->
                                <input name="salesorder_number" id="salesorder_number" class="input_1 float_left  " type="text" value="<?php echo $query['salesorder_number'] ?>" style=" width:91%;float:left;padding-left:5%;padding-right: 2%;" readonly="readonly">
                                <!--/nocache-->
                            </div>
                            <div class="jt_box_field " style=" width:44.5%;">
                                <!--nocache-->
                                <input name="salesorder_name" id="salesorder_name" class="input_1 float_left  " type="text" value="<?php echo $query['salesorder_name'] ?>" style=" float:left;" readonly="readonly">
                                <input name="salesorder_id" id="salesorder_id" class="jthidden" type="hidden" value="<?php echo $query['salesorder_id'] ?>">
                                <!--/nocache-->
                                <span class="iconw_m indent_dw_m" id="click_open_window_salesorderssalesorder_name" title="Specify Sales order"></span>
                                <script type="text/javascript">
                                    $(function(){
                                        window_popup('salesorders', 'Specify Sales order','salesorder_name','click_open_window_salesorderssalesorder_name');
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label fixbor3" style=" width:35%;">
                                &nbsp;
                            </div>
                            <div class="jt_box_field " style=" width:61%">
                                <span class="float_left">&nbsp;</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <!--Elememt Sub Tab -->
            <div class="clear block_dent3">
                <div class="box_inner">
                    <ul class="ul_tab">
                        <li id="line_entry">
                            <a style="cursor:pointer;">Line entry</a>
                        </li>
                        <li id="text_entry">
                            <a style="cursor:pointer;">Text entry</a>
                        </li>
                        <li id="rfqs">
                            <a style="cursor:pointer;">RFQ's</a>
                        </li>
                        <li id="documents">
                            <a style="cursor:pointer;">Documents</a>
                        </li>
                        <li id="other">
                            <a style="cursor:pointer;">Other</a>
                        </li>
                        <li id="asset_tags">
                            <a style="cursor:pointer;">Asset Tags</a>
                        </li>
                        <li id="costings" class="">
                            <a style="cursor:pointer;">Costings</a>
                        </li>
                        <p class="clear"></p>
                    </ul>
                </div>
            </div>
        </form>
    </div>
    <!--Load cont of sub tab -->
    <!--nocache-->
    <div class="clear_percent" id="load_subtab">
        <?php
            if($sub_tab!='' && $sub_tab !='documents'){
                // if($this->elementExists('../Themed/Default/'.$name.'/'.$sub_tab))
                         if(file_exists(APP.'View'.DS.'Themed'.DS.'Default'.DS.$name.DS.$sub_tab.'.ctp' ))
                    echo $this->element('..'.DS.$name.DS.$sub_tab);
                else
                    echo $this->element('../Elements/box_type/subtab_box_default');
            }else{
                         echo $this->element('..'.DS.$name.DS.$sub_tab);
                     }
            ?>
    </div>
    <!--/nocache-->
    <?php echo $this->element('../'.$name.'/js'); ?>
</div>
<input type="hidden" id="position_store" value="" />
<style>
    .k-select{
    display:none!important;
    }
    .k-numeric-wrap{
    border-radius:0!important;
    margin:0!important;
    padding:0!important;
    border:none!important;
    border-bottom: 1px solid #dddddd!important;
    }
</style>
<script type="text/javascript">
    $(function(){
        notifyTop('<?php echo $this->Session->flash(); ?>');
    })
</script>