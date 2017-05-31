<div class="bg_menu"></div>
<div class="tab_1 half_width" style="width:45%;margin:5% auto;">
        <span class="title_block bo_ra1">
            <span class="float_left">
                <span class="fl_dent">
                    <h4>Find option for report</h4>
                </span>
            </span>
        </span>
        <form id="form_quotation" target="_blank" action="<?php echo URL;?>/quotations/check_exist_area" method="POST">
        <div class="tab_2_inner">
            <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height:50px">
                    Report Heading</span>
                </p>
                <div class="width_in float_left indent_input_tp" style="width:61.5%">                                 
                    <?php echo $this->Form->input('heading', array(
                            'class' => 'input_4 float_left',
                            'name'=>'heading'
                    )); ?>                
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">
                    Status</span>
                </p>
                <div class="width_in float_left indent_input_tp" style="width:61.5%">                                   
                    <?php echo $this->Form->input('status', array(
                            'class' => 'input_select input_3 validate',
                            'readonly' => true,
                            'name' => 'status',
                            'style'=>'margin: 0px 16px 0px 0px;padding: 0 0px 0 2%;'
                    )); ?>
                    <span class="combobox_button" style="cursor:pointer;position:absolute; height:16px; width:16px; top:0; right: -12px;">
                        <div class="combobox_arrow" style="margin-left:35%"></div>
                    </span>
                    <script type="text/javascript">
                        $(function () {
                            $("#status").combobox(<?php echo json_encode($arr_data['quotations_status']); ?>);
                        });
                    </script>                 
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height: 50%">Exclude cancelled transactions</span>
                </p>
                <div class="width_in3a float_left indent_input_tp" style="margin-top:1.7%">
                    <label class="m_check2">
                        <?php 
                            echo $this->Form->input('is_not_cancel',array(
                                    'class'=>'custoer-employee',
                                    'type'=>'checkbox',
                                    'name'=>'is_not_cancel',
                                ));
                        ?> 
                        <span style="margin-left:1%"></span>
                        <p style= "margin-left:37px;width:280px">Also excludes Amended and Rejected transactions  </p>
                    </label>                     
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">
                    Type                            </span>
                </p>
               <div class="width_in float_left indent_input_tp" style="width:61.5%">                                   
                    <?php echo $this->Form->input('type', array(
                            'class' => 'input_select input_3 validate',
                            'readonly' => true,
                            'name'=>'type',
                            'style'=>'margin: 0px 16px 0px 0px;padding: 0 0px 0 2%;'
                    )); ?>
                    <span class="combobox_button" style="cursor:pointer;position:absolute; height:16px; width:16px; top:0; right: -12px;">
                        <div class="combobox_arrow" style="margin-left:35%"></div>
                    </span>
                    <script type="text/javascript">
                        $(function () {
                            $("#type").combobox(<?php echo json_encode($arr_data['quotations_type']);
                            ?>);
                        });
                    </script>  
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Company</span>
                </p>
               <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('company',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'company',
                                'disable'=>true
                            ));
                    ?>
                    <input type="hidden" value="" id="company_id" name="company_id" >
                    <span class="icon_down_new float_right" id="click_open_window_companiescompany_name" style=""></span>
                    <script type="text/javascript">
                        $(function(){
                            window_popup('companies', 'Specify Company','company','click_open_window_companiescompany_name');
                        });
                    </script>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Contact</span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('contact',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'contact'
                            ));
                    ?>
                    <span class="icon_down_new float_right" id="click_open_window_contactscontact_name" style=""></span>
                    <input type="hidden" value="" id="contact_id" name="contact_id" />
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Job no</span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('job_no',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'job_no'
                            ));
                    ?>
                    <span class="icon_down_new float_right" id="click_open_window_jobsjob_name" style=""></span>
                    <input type="hidden" value="" id="job_no_id" name="job_no_id"/>
                    <script type="text/javascript">
                        $(function(){
                            window_popup('jobs', 'Specify Job','job_no','click_open_window_jobsjob_name');
                        });
                    </script>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Date equal</span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <?php 
                        echo $this->Form->input('date_equals',array(
                            'class'=>'input_4 float_left JtSelectDate validate',
                            'name'=>'date_equals',
                            'readonly' => true,
                            ));
                    ?>                             
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height:50%">Date between from</span>
                </p>
                <div class="indent_new width_in float_left" style="width:25%">
                    <?php echo $this->Form->input('date_from', array(
                            'class' => 'input_4 float_left JtSelectDate validate',
                            'readonly' => true,
                            'name'=>'date_from',
                            'style' =>'padding:0 3%;'
                    )); ?>
                </div>
                <span class="float_left" style="margin-top:2%">To</span>
                    <?php echo $this->Form->input('date_to', array(
                            'class' => 'input_4 float_left jt_input_search JtSelectDate validate',
                            'readonly' => true,
                            'name'=>'date_to',
                            'style' =>'padding: .3% 1%;width: 30%;'
                    )); ?>
                <p class="clear"></p>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Our Rep</span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('our_rep',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'our_rep'
                            ));
                    ?>
                    <span class="icon_down_new float_right" id="click_open_window_contactsour_rep" style=""></span>
                    <input type="hidden" value="" id="our_rep_id" name="our_rep_id">
                    <script type="text/javascript">
                        $(function(){
                            window_popup('contacts', 'Specify Our rep','our_rep','click_open_window_contactsour_rep');
                        });
                    </script>
                </div>
            <p></p>
             <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height:50%">Our CSR</span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('our_csr',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'our_csr'
                            ));
                    ?>
                    <span class="icon_down_new float_right" id="click_open_window_contactsour_csr" style=""></span>
                    <input type="hidden" value="" id="our_csr_id" name="our_csr_id">
                    <script type="text/javascript">
                        $(function(){
                            window_popup('contacts', 'Specify Our CSR','our_csr','click_open_window_contactsour_csr');
                        });
                    </script>
                </div>
                <input type="hidden" name="report_type" value="summary" />
            <p></p>
            <p class="clear"></p>
        </div>
        <div>           
        <span class="title_block bo_ra2">
            <span class="icon_vwie indent_down_vwie2">
                <a href="">
                    Enter find criteria and click Continue
                </a>
            </span>
            <ul class="menu_control float_right" style="margin:-1% -5%;width:35%">
                <li><a href="javascript:void()" id="CancelButton" style="margin-top: 6%;font-size: 10px;line-height: 4px;border-radius: 3px;box-shadow: 0px 1px 2px">Cancel</a></li>
                <li style="margin-left:10%"><a style="margin-top: 6%;font-size: 10px;line-height: 4px;border-radius: 3px;box-shadow: 0px 1px 2px" id="ContinueButton" href="javascript:void()">Continue</a></li>
            </ul>
            <p class="clear"></p>
        </span>
    </div>
</form>
<script type="text/javascript">
    $(function(){
        $('#is_not_cancel').change(function() {
            if($(this).is(':checked'))
                $(this).val(1);
            else
                $(this).val(0);
        });
        $('#date_equals').change(function() {
            $('#date_from').val('');
            $('#date_to').val('');
        });
        $('#date_from').change(function(){
            $('#date_equals').val('');
        });
        $('#date_to').change(function(){
            $('#date_equals').val('');
        });
        $('#CancelButton').click(function(){
            window.location.replace('<?php echo URL; ?>/quotations/options');
        });
        $('#ContinueButton').click(function(){
            var empty = check_Empty();
            if(empty==true)
            {
                confirms('Warning','The provided criteria is not valid. Enter a valid request before proceeding.');
                return false;
            }
            else
            {
                mywindow = window.open("about:blank", "Download PDF");
                $.ajax({
                    url: '<?php echo URL; ?>/quotations/check_exist_area',
                    type: 'POST',
                    data: {data : $('#form_quotation').serialize()},
                    async: false,
                    success : function(result)
                    {
                        if(result=='empty'){                            
                            mywindow.close();
                            alerts('Message','No record!');
                        }
                            
                        else if(result != 'empty')
                            console.log(result);
                           mywindow.location = result;
                           
                    }
                });                
                return false;
            }
        });
        tasks_init_popup_contacts();

    });
        function check_Empty()
        {
            var status;
            $('.validate').each(function(){
                if($(this).val()!='')
                {
                    status = false;
                    return false;
                }                 
            });
            if(status == false)
                return false;
            return true;
        }
        function tasks_init_popup_contacts( force_re_install ){
            var parameter_get = "?is_area=1";
            if( $("#company_id").val() != "" ){
                parameter_get += "&company_id=" + $("#company_id").val() + "&company_name=" + $("#company").val();
            }

            if( force_re_install == "force_re_install" ){
                //window_popup("contacts", "Specify contact", "", "", parameter_get, "force_re_install");
                window_popup('contacts', 'Specify Contact','contact','click_open_window_contactscontact_name', parameter_get, "force_re_install");
            }else{
                //window_popup("contacts", "Specify contact", "", "", parameter_get);
                window_popup('contacts', 'Specify Contact','contact','click_open_window_contactscontact_name', parameter_get);

            }


        }
        function after_choose_companies(_id,company_name,keys)
        {
            $('#'+keys+'').val(company_name);
            $('#'+keys+'_id').val(_id);
            $("#window_popup_companies"  + keys).data("kendoWindow").close();
            tasks_init_popup_contacts("force_re_install");
            return false;

        }
        function after_choose_contacts(_id,contact_name,keys)
        {
            $('#'+keys+'').val(contact_name);
            $('#'+keys+'_id').val(_id);
            $("#window_popup_contacts"  + keys).data("kendoWindow").close();
            return false;
        }
        function after_choose_jobs(_id,contact_name,keys)
        {
            var job_info = $('#after_choose_jobsjob_no'+_id).val();
            job_info = jQuery.parseJSON(job_info);
            $('#'+keys+'').val(job_info.no);
            $('#'+keys+'_id').val(_id);
            $("#window_popup_jobs"  + keys).data("kendoWindow").close();
            return false;
        }
</script>