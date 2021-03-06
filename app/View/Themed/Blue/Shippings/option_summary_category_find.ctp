<div class="bg_menu"></div>
<div class="tab_1 half_width" style="width:40%;margin:0 auto 100px;">
        <span class="title_block bo_ra1">
            <span class="float_left">
                <span class="fl_dent">
                    <h4>Find option for report</h4>
                </span>
            </span>
        </span>
        <div class="tab_2_inner">
            <form id="shipping_product" action="<?php echo URL; ?>/shippings/product_report/category_summary" method="POST">
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
                        echo $this->Form->input('product_code',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'product',
                            ));
                    ?>
                    <input type="hidden" value="" id="product_id" name="product_id" >
                    <span class="icon_down_new float_right" id="click_open_window_productsproduct_code" style=""></span>
                    <script type="text/javascript">
                        $(function(){
                            window_popup('products', 'Specify Product','product_code','click_open_window_productsproduct_code');
                        });
                    </script>
                </div>
            <p></p>
             <p class="clear">
                <span class="label_1 float_left minw_lab2">
                    Product name / details</span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <input name="product_name" class="input_4 float_left"  type="text" id="product_name">
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Company (receiver)</span>
                </p>
               <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('company_receiver',array(
                                'class'=>'input_4 float_left validate receiver',
                                'name'=>'company_receiver',
                                'disable'=>true
                            ));
                    ?>
                    <span class="icon_down_new float_right receiver_click" id="click_open_window_companiescompany_receiver" style=""></span>
                    <input type="hidden" id="company_receiver_id" />
                    <script type="text/javascript">
                        $(function(){
                            window_popup('companies', 'Specify Company','company_receiver','click_open_window_companiescompany_receiver','?is_customer=1');
                        });
                    </script>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Contact (receiver)</span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('contact_receiver',array(
                                'class'=>'input_4 float_left validate receiver',
                                'name'=>'contact_receiver'
                            ));
                    ?>
                    <span class="icon_down_new float_right receiver_click" id="click_open_window_contactscontact_receiver" style=""></span>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Company (sender)</span>
                </p>
               <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('company_sender',array(
                                'class'=>'input_4 float_left validate sender',
                                'name'=>'company_sender',
                                'disable'=>true
                            ));
                    ?>
                    <span class="icon_down_new float_right sender_click" id="click_open_window_companiescompany_sender" style=""></span>
                    <input type="hidden" id="company_sender_id" />
                    <script type="text/javascript">
                        $(function(){
                            window_popup('companies', 'Specify Company','company_sender','click_open_window_companiescompany_sender','?is_supplier=1');
                        });
                    </script>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Contact (sender)</span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('contact_sender',array(
                                'class'=>'input_4 float_left validate sender',
                                'name'=>'contact_sender'
                            ));
                    ?>
                    <span class="icon_down_new float_right sender_click" id="click_open_window_contactscontact_sender" style=""></span>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">Carrier</span>
                </p>
               <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('carier',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'carier',
                                'disable'=>true
                            ));
                    ?>
                    <span class="icon_down_new float_right sender_click" id="click_open_window_companiescarier" style=""></span>
                    <script type="text/javascript">
                        $(function(){
                            window_popup('companies', 'Specify Company','carier','click_open_window_companiescarier');
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
                    <script type="text/javascript">
                        $(function () {
                            $("#type").combobox(<?php echo json_encode($arr_data['shippings_type']);
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
                            'readonly' => true,
                            'name' => 'status',
                            'style'=>'margin: 0px 16px 0px 0px;padding: 0 0px 0 2%;'
                    )); ?>
                    <span class="combobox_button" style="cursor:pointer;position:absolute; height:16px; width:16px; top:0; right: -12px;">
                        <div class="combobox_arrow" style="margin-left:35%"></div>
                    </span>
                    <script type="text/javascript">
                        $(function () {
                            $("#status").combobox(<?php echo json_encode($arr_data['shippings_status']); ?>);
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
                                    'checked'=>'true',
                                    'value'=>1
                                ));
                        ?>
                        <span style="margin-left:1%"></span>
                        <p style= "margin-left:37px;width:280px"></p>
                    </label>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height: 50%">Return</span>
                </p><div class="width_in3a float_left indent_input_tp" style="margin-top:1.7%">
                    <label class="m_check2">
                        <input type="checkbox" name="" class="custoer-employee" value="1" id="is_return" name="is_return">
                        <span style="margin-left:1%"></span>
                        <p style= "margin-left:37px;width:280px"></p>
                    </label>                     </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2">
                    Category                            </span>
                </p>
               <div class="width_in product_cate float_left indent_input_tp" style="width:61.5%">
                    <?php echo $this->Form->input('product_category', array(
                            'class' => 'input_select input_3 validate',
                            'name'=>'product_category',
                            'readonly' => true,
                            'style'=>'margin: 0px 16px 0px 0px;padding: 0 0px 0 2%;'
                    )); ?>
                    <span class="combobox_button" style="cursor:pointer;position:absolute; height:16px; width:16px; top:0; right: -12px;">
                        <div class="combobox_arrow" style="margin-left:35%"></div>
                    </span>
                    <input type="hidden" name="product_category_id" id="product_categoryId" value="" />
                    <script type="text/javascript">
                        $(function () {
                            $("#product_category").combobox(<?php echo json_encode($arr_data['product_category']); ?>);
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
            <span class="title_block bo_ra2">
            <span class="icon_vwie indent_down_vwie2">
                <a href="">
                    Enter find criteria and click Continue
                </a>
            </span>
            <ul class="menu_control float_right" style="margin:-1% -5%;width:35%">
                <li><a href="javascript:void(0)" id="CancelButton" style="margin-top: 6%;font-size: 10px;line-height: 4px;border-radius: 3px;box-shadow: 0px 1px 2px">Cancel</a></li>
                <li style="margin-left:10%"><a style="margin-top: 6%;font-size: 10px;line-height: 4px;border-radius: 3px;box-shadow: 0px 1px 2px" id="ContinueButton" href="javascript:void(0)">Continue</a></li>
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
        $('#is_return').change(function() {
            if($(this).is(':checked'))
                $(this).val(1);
            else
                $(this).val(0);
        });
        $('#date_equals').change(function() {
            $('#date_from').val('');
            $('#date_to').val('');
        });
        $('#date_from,#date_to').change(function(){
            $('#date_equals').val('');
        });
        $('#CancelButton').click(function(){
            window.location.replace('<?php echo URL; ?>/shippings/options');
        });
        $('.sender_click').click(function(){
            $('.receiver').val('');
        });
        $('.receiver_click').click(function(){
            $('.sender').val('');
        });
        $('#ContinueButton').click(function(){
            if(check_Empty==true)
                confirms('Warning','The provided criteria is not valid. Enter a valid request before proceeding.');
            else
                $.ajax({
                    url: '<?php echo URL; ?>/shippings/product_report/category_summary',
                    type: 'POST',
                    data: $('#shipping_product').serialize(),
                    success : function(result){
                        if(result=='empty')
                            alerts('Message','No record!');
                        else
                            $('#shipping_product').submit();
                    }
                });
        });

        init_popup_contacts_receiver();
        init_popup_contacts_sender();

    });
         function check_Empty(){
            var status = true;
            $('.validate').each(function(){
                if($(this).val()!=''){
                    status = false;
                    return ;
                }
            });
            return status;
        }
         function init_popup_contacts_sender( force_re_install ){
            var parameter_get = "?is_customer=1";
            if( $("#company_sender_id").val() != "" )
                parameter_get += "&company_id=" + $("#company_sender_id").val() + "&company_name=" + $("#company").val();
            if( force_re_install == "force_re_install" ){
                //window_popup("contacts", "Specify contact", "", "", parameter_get, "force_re_install");
                window_popup('contacts', 'Specify Contact','contact_sender','click_open_window_contactscontact_sender', parameter_get, "force_re_install");
            }else{
                //window_popup("contacts", "Specify contact", "", "", parameter_get);
                window_popup('contacts', 'Specify Contact','contact_sender','click_open_window_contactscontact_sender', parameter_get);

            }


        }
        function init_popup_contacts_receiver( force_re_install ){
            var parameter_get = "?is_customer=1";
            if( $("#company_receiver_id").val() != "" )
                parameter_get += "&company_id=" + $("#company_receiver_id").val() + "&company_name=" + $("#company").val();
            if( force_re_install == "force_re_install" )
                //window_popup("contacts", "Specify contact", "", "", parameter_get, "force_re_install");
                window_popup('contacts', 'Specify Contact','contact_receiver','click_open_window_contactscontact_receiver', parameter_get, "force_re_install");
            else
                //window_popup("contacts", "Specify contact", "", "", parameter_get);
                window_popup('contacts', 'Specify Contact','contact_receiver','click_open_window_contactscontact_receiver', parameter_get);
        }
        function after_choose_products(_id,product_name,keys)
        {
            var product_info = $('#after_choose_productsproduct_code'+_id).val();
            product_info = jQuery.parseJSON(product_info);
            $('#product_code').val(product_info.code);
            $('#product_name').val(product_info.name);
            $('#product_id').val(_id);
            $.get('<?php echo URL; ?>/shippings/get_cate_product/'+product_info.category,function(result) {
                console.log(result);
                $('#product_category').val(result);
            });
            $('#product_categoryId').val(product_info.category);
            $("#window_popup_products"  + keys).data("kendoWindow").close();
            return false;

        }
        function after_choose_companies(_id,company_name,keys)
        {
            $('#'+keys+'').val(company_name);
            $('#'+keys+'_id').val(_id);
            $("#window_popup_companies"  + keys).data("kendoWindow").close();
            init_popup_contacts_sender("force_re_install");
            init_popup_contacts_receiver("force_re_install");
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