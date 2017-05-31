<div class="bg_menu"></div>
<div class="tab_1 half_width" style="width:40%;margin:0 auto;">
        <span class="title_block bo_ra1">
            <span class="float_left">
                <span class="fl_dent">
                    <h4>Find option for report</h4>
                </span>
            </span>
        </span>
        <form id="product_find" action="<?php echo URL;?>/quotations/product_report/category_summary" method="POST">
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
                    Code</span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('product',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'product',
                            ));
                    ?>
                    <input type="hidden" value="" id="product_id" name="product_id" >
                    <span class="icon_down_new float_right" id="click_open_window_productsproduct_name" style=""></span>
                    <script type="text/javascript">
                        $(function(){
                            window_popup('products', 'Specify Product','product','click_open_window_productsproduct_name');
                        });
                    </script>
                </div>
            <p></p>
             <p class="clear">
                <span class="label_1 float_left minw_lab2">
                    Product name / details</span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <input name="name" class="input_4 float_left" type="text" id="name">
                </div>
            <p></p>
             <p class="clear">
                <span class="label_1 float_left minw_lab2">
                    Company</span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('company',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'company',
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
                <span class="label_1 float_left minw_lab2">
                    Contact</span>
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
                <span class="label_1 float_left minw_lab2">
                    Type</span>
                </p>
                <div class="width_in float_left indent_input_tp" style="width:61.5%">
                    <?php echo $this->Form->input('type', array(
                            'class' => 'input_select input_3 validate',
                            'name'=>'type',
                            'readonly' => true,
                            'style'=>'margin: 0px 16px 0px 0px;padding: 0 0px 0 2%;'
                    )); ?>
                    <span class="combobox_button" style="cursor:pointer;position:absolute; height:16px; width:16px; top:0; right: -12px;">
                        <div class="combobox_arrow" style="margin-left:35%"></div>
                    </span>
                    <input type="hidden" name="type_id" id="type_id" value="" />
                    <script type="text/javascript">
                        $(function () {
                            $("#type").combobox(<?php echo json_encode($arr_data['quotations_type']);
                            ?>);
                        });
                    </script>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">
                    Status</span>
                </p>
                <div class="width_in float_left indent_input_tp" style="width:61.5%">
                    <?php echo $this->Form->input('status', array(
                            'class' => 'input_select input_3 validate',
                            'name'=>'status',
                            'readonly' => true,
                            'style'=>'margin: 0px 16px 0px 0px;padding: 0 0px 0 2%;'
                    )); ?>
                    <span class="combobox_button" style="cursor:pointer;position:absolute; height:16px; width:16px; top:0; right: -12px;">
                        <div class="combobox_arrow" style="margin-left:35%"></div>
                    </span>
                    <input type="hidden" id="status_id" name="status_id" value="" />
                    <script type="text/javascript">
                        $(function () {
                            $("#status").combobox(<?php echo json_encode($arr_data['quotations_status']); ?>);
                        });
                    </script>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height: 50%">Exclude canc transactions</span>
                </p><div class="width_in3a float_left indent_input_tp" style="margin-top:1.7%">
                    <label class="m_check2">
                        <input type="checkbox" name="is_not_cancel" class="custoer-employee" id="is_not_cancel" checked='checked' value='1'>
                        <span style="margin-left:1%"></span>
                        <p style= "margin-left:37px;width:280px"></p>
                    </label>                     </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">
                    Category                            </span>
                </p>
               <div class="width_in float_left indent_input_tp" style="width:61.5%">
                    <?php echo $this->Form->input('category', array(
                            'class' => 'input_select input_3 validate',
                            'name'=>'category',
                            'readonly' => true,
                            'style'=>'margin: 0px 16px 0px 0px;padding: 0 0px 0 2%;'
                    )); ?>
                    <span class="combobox_button" style="cursor:pointer;position:absolute; height:16px; width:16px; top:0; right: -12px;">
                        <div class="combobox_arrow" style="margin-left:35%"></div>
                    </span>
                    <input type="hidden" name="category_id" id="categoryId" value="" />
                    <script type="text/javascript">
                        $(function () {
                            $("#category").combobox(<?php echo json_encode($arr_data['product_category']); ?>);
                        });
                    </script>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Employee</span>
                </p>
               <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('employee',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'employee'
                            ));
                    ?>
                    <span class="icon_down_new float_right" id="click_open_window_contactsemployee_name" style=""></span>
                    <input type="hidden" value="" id="employee_id" name="employee_id" />
                    <script type="text/javascript">
                        $(function(){
                            window_popup('contacts', 'Specify Contact','employee','click_open_window_contactsemployee_name','?is_employee=1');
                        });
                    </script>
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
                <span class="label_1 float_left minw_lab2" style="height: 50%">Date between from</span>
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
                <span class="label_1 float_left minw_lab2">Sell price</span>
                </p>
                <div class="indent_new width_in float_left" style="width:25%">
                    <input name="sell_price_from" class="input_4 float_left " style="padding:0 3%;" type="text" id="sell_price">
                </div>
                <span class="float_left" style="margin-top:2%">To</span>
                    <input name="sell_price_to" class="input_4 float_left " style="padding: .3% 1%;width: 30%;" type="text" id="sell_price">
                <p class="clear"></p>
            <p></p>
            <p class="clear"></p>
        </div>
        <div>
        <input type="hidden" name="report_type" id="report_type" value="category_summary" />
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
        </form>
    </div>
</div>
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
        $('#date_from, #date_to').change(function(){
            $('#date_equals').val('');
        });
        $('#CancelButton').click(function(){
            window.location.replace('<?php echo URL; ?>/quotations/options');
        });
        $('#ContinueButton').click(function(){
            if(check_Empty())
                alerts('Message','The provided criteria is not valid. Enter a valid request before proceeding.');
            else{
                $.ajax({
                    url: '<?php echo URL; ?>/quotations/product_report/category_summary',
                    type: 'POST',
                    data: $('input','#product_find').serialize(),
                    success : function(result){
                        if(result=='empty')
                            alerts('Message','No record!');
                        else
                           $("#product_find").submit();
                    }
                });
            }
        });
        tasks_init_popup_contacts();

    });
    function check_Empty(){
        var status = true;
        $('.validate').each(function(){
            if($(this).val()!=''){
                status = false;
                return;
            }
        });
        return status;
    }
    function tasks_init_popup_contacts( force_re_install ){
        var parameter_get = "?is_customer=1";
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
    function after_choose_products(_id,product_name,keys){
        var product_info = $('#after_choose_productsproduct'+_id).val();
        product_info = jQuery.parseJSON(product_info);
        $('#'+keys+'').val(product_info.code);
        $('#name').val(product_info.name);
        $('#'+keys+'_id').val(_id);
        $.get('<?php echo URL; ?>/quotations/get_cate_product/'+product_info.category,function(result) {
            //console.log(result);
            $('#category').val(result);
        });
        $('#categoryId').val(product_info.category);
        $("#window_popup_products"  + keys).data("kendoWindow").close();
        return false;

    }
    function after_choose_companies(_id,company_name,keys){
        $('#'+keys+'').val(company_name);
        $('#'+keys+'_id').val(_id);
        $("#window_popup_companies"  + keys).data("kendoWindow").close();
        tasks_init_popup_contacts("force_re_install");
        return false;

    }
    function after_choose_contacts(_id,contact_name,keys){
        $('#'+keys+'').val(contact_name);
        $('#'+keys+'_id').val(_id);
        $("#window_popup_contacts"  + keys).data("kendoWindow").close();
        return false;
    }
    function after_choose_jobs(_id,contact_name,keys){
        var job_info = $('#after_choose_jobsjob_no'+_id).val();
        job_info = jQuery.parseJSON(job_info);
        $('#'+keys+'').val(job_info.no);
        $('#'+keys+'_id').val(_id);
        $("#window_popup_jobs"  + keys).data("kendoWindow").close();
        return false;
    }
</script>