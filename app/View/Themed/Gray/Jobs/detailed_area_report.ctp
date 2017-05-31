<div class="bg_menu"></div>
<div class="tab_1 half_width" style="width:45%;margin:5% auto;">
<span class="title_block bo_ra1">
    <span class="float_left">
        <span class="fl_dent">
            <h4>Find option for report</h4>
        </span>
    </span>
</span>
<form id="form_contact" action="<?php echo URL;?>/jobs/area_report/detail" method="POST">
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
                Product Code
            </span>
            </p>
            <div class="indent_new width_in float_left" style="width:61%">
                <?php
                    echo $this->Form->input('product_code',array(
                            'class'=>'input_4 float_left validate',
                            'name'=>'product_code',
                            'readonly' => true,
                            'value'     => ''
                        ));
                ?>
                <span class="icon_down_new float_right" id="click_open_window_productsproduct_name" style=""></span>
                <script type="text/javascript">
                    $(function(){
                        window_popup('products', 'Specify Product','product_code','click_open_window_productsproduct_name');
                    });
                </script>
            </div>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab2" style="height: 100px;">
                Product name
            </span>
            </p>
            <div class="width_in float_left indent_input_tp" style="width:61.5%">
                <?php echo $this->Form->input('product_name', array(
                    'type'      => 'textarea',
                    'class'     => 'input_4 float_left',
                    'name'      =>'product_name',
                    'readonly' => true,
                    'value'     => ''
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
                        $("#status").combobox(<?php echo json_encode($arr_data['jobs_status']); ?>);
                    });
            </script>
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
                <input type="hidden" id="typeId" name="type_id" value="" />
                <script type="text/javascript">
                    $(function () {
                        $("#type").combobox(<?php echo json_encode($arr_data['jobs_type']);
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
            <span class="label_1 float_left minw_lab2">Our Rep</span>
        </p>
        <div class="indent_new width_in float_left" style="width:61%">
            <?php
                echo $this->Form->input('our_rep',array(
                        'class'=>'input_4 float_left validate',
                        'name'=>'our_rep'
                    ));
                ?>
            <span class="icon_down_new float_right" id="click_open_window_contactscontact_name" style=""></span>
            <input type="hidden" value="" id="our_rep_id" name="our_rep_id" />
        </div>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab2">Our CSR</span>
        </p>
        <div class="indent_new width_in float_left" style="width:61%">
            <?php
                echo $this->Form->input('our_csr',array(
                        'class'     => 'input_4 float_left validate',
                        'name'      => 'our_csr',
                        'readonly'  => true,
                    ));
                ?>
            <span class="icon_down_new float_right" id="click_open_window_contactsour_csr" style=""></span>
            <input type="hidden" value="" id="our_csr_id" name="our_csr_id" />
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
            <span class="label_1 float_left minw_lab2"><b style="font-weight: 900; color: blue;">Job</b> Date equal</span>
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
            <span class="label_1 float_left minw_lab2" style="height:50%"><b style="font-weight: 900; color: blue;">Job</b> Date between from</span>
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
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab2" style="height:50%"><b style="font-weight: 900; color: blue;">Order</b> Date between from</span>
        </p>
        <div class="indent_new width_in float_left" style="width:25%">
            <?php echo $this->Form->input('order_date_from', array(
                'class' => 'input_4 float_left JtSelectDate validate',
                'readonly' => true,
                'name'=>'order_date_from',
                'style' =>'padding:0 3%;'
                )); ?>
        </div>
        <span class="float_left" style="margin-top:2%">To</span>
        <?php echo $this->Form->input('order_date_to', array(
            'class' => 'input_4 float_left jt_input_search JtSelectDate validate',
            'readonly' => true,
            'name'=>'order_date_to',
            'style' =>'padding: .3% 1%;width: 30%;'
            )); ?>
        <span class="icon_remove2" style="margin: -18px 20px 0; float: right;" onclick="emptyDate()" title="Empty"></span>
        <p class="clear"></p>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab2">Tax</span>
        </p>
        <div class="indent_new width_in float_left" style="width:61%">
            <?php
                echo $this->Form->input('tax',array(
                        'class' => 'input_select input_3 validate',
                        'readonly' => true,
                        'name'=>'tax',
                        'style'=>'margin: 0px 16px 0px 0px;padding: 0 0px 0 2%;'
                    ));
                ?>
                <span class="combobox_button" style="cursor:pointer;position:absolute; height:16px; width:16px; top:0; right: -12px;">
                        <div class="combobox_arrow" style="margin-left:35%"></div>
                </span>
                <input id="taxId" name="tax_id" value="" type="hidden" />
                 <script type="text/javascript">
                    $(function () {
                        $("#tax").combobox(<?php echo json_encode($arr_data['jobs_tax']); ?>);
                    });
            </script>
        </div>
        <p></p>
        <input type="hidden" name="report_type" value="detailed" />
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
            window.location.replace('<?php echo URL; ?>/jobs/options');
        });
        $('#ContinueButton').click(function(){
            var empty = check_Empty();
            if(empty==true)
            {
                confirms('Warning','The provided criteria is not valid. Enter a valid request before proceeding.');
                return false;
            } else{
                $.ajax({
                    url: '<?php echo URL; ?>/jobs/area_report',
                    type: 'POST',
                    data:  $('input','#form_contact').serialize(),
                    success : function(result) {
                        if(result=='empty')
                            alerts('Message','No record!');
                        else
                           $("#form_contact").submit();
                    }
                 });
            }
        });
         window_popup('contacts', 'Specify Our Rep','our_rep','click_open_window_contactscontact_name','?is_employee=1');
         window_popup('contacts', 'Specify Our CSR','our_csr','click_open_window_contactsour_csr','?is_employee=1');

    });
    function check_Empty(){
        var status = true;
        $('.validate').each(function(){
            if($(this).val()!=''){
                status = false;
                return false;
            }
        });
        return status;
    }
    function after_choose_companies(_id,company_name,keys)
    {
        $('#'+keys+'').val(company_name);
        $('#'+keys+'_id').val(_id);
        $("#window_popup_companies"  + keys).data("kendoWindow").close();
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
    function after_choose_products(_id,name,keys){
        var product_info = $('#after_choose_productsproduct_code'+_id).val();
        product_info = $.parseJSON(product_info);
        var product_code = $('#product_code').val();
        if(product_code != "")
            product_code += ", "+product_info.code;
        else
            product_code = product_info.code;
        $('#product_code').val(product_code);
        var product_name = $('#product_name').val();
        if(product_name!= "")
            product_name += ", "+product_info.name;
        else
            product_name = product_info.name;
        $('#product_name').val(product_name);
        $("#window_popup_products"  + keys).data("kendoWindow").close();
    }
    function emptyDate()
    {
        $("#order_date_from").val("");
        $("#order_date_to").val("");
    }
</script>