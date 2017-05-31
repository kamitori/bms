<div class="bg_menu"></div>
<div class="tab_1 half_width" style="width:45%;margin:5% auto;">
        <span class="title_block bo_ra1">
            <span class="float_left">
                <span class="fl_dent">
                    <h4></h4><?php echo translate('Find option for report'); ?></h4>
                </span>
            </span>
        </span>
        <form id="form_receipt" target="_blank" action="<?php echo URL;?>/receipts/check_exist_receipts" method="POST">
        <div class="tab_2_inner">
            <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height:50px"><?php echo translate('Report Heading'); ?></span>
                </p>
                <div class="width_in float_left indent_input_tp" style="width:61.5%">
                    <?php echo $this->Form->input('heading', array(
                            'class' => 'input_4 float_left validate',
                            'name'=>'heading'
                    )); ?>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2"><?php echo translate('Customer'); ?></span>
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
                <span class="label_1 float_left minw_lab2"><?php echo translate('Full receipt amount'); ?></span>
                </p>
               <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('amount_received',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'amount_received',
                            ));
                    ?>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2"><?php echo translate('Date equal'); ?></span>
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
                <span class="label_1 float_left minw_lab2" style="height:50%"><?php echo translate('Date between from'); ?></span>
                </p>
                <div class="indent_new width_in float_left" style="width:25%">
                    <?php echo $this->Form->input('date_from', array(
                            'class' => 'input_4 float_left JtSelectDate validate',
                            'readonly' => true,
                            'name'=>'date_from',
                            'style' =>'padding:0 3%;'
                    )); ?>
                </div>
                <span class="float_left" style="margin-top:2%"><?php echo translate('To'); ?></span>
                    <?php echo $this->Form->input('date_to', array(
                            'class' => 'input_4 float_left jt_input_search JtSelectDate validate',
                            'readonly' => true,
                            'name'=>'date_to',
                            'style' =>'padding: .3% 1%;width: 30%;'
                    )); ?>
                <p class="clear"></p>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2"><?php echo translate('Payment method'); ?></span>
                </p>
                <div class="width_in float_left indent_input_tp" style="width:61.5%">
                    <?php echo $this->Form->input('paid_by', array(
                            'class' => 'input_select input_3 validate',
                            'readonly' => true,
                            'name' => 'paid_by',
                            'style'=>'margin: 0px 16px 0px 0px;padding: 0 0px 0 2%;'
                    )); ?>
                    <input id='paid_byId' name="paid_by_id" type="hidden" />
                    <script type="text/javascript">
                        $(function () {
                            $("#paid_by").combobox(<?php echo json_encode($arr_data['receipts_paid_by']); ?>);
                        });
                    </script>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2"><?php echo translate('Reference'); ?></span>
                </p>
               <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('amount_received',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'amount_received',
                            ));
                    ?>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2"><?php echo translate('Receipt notes'); ?></span>
                </p>
               <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('notes',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'notes',
                            ));
                    ?>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2"><?php echo translate('Employee'); ?></span>
                </p>
                <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('employee',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'employee'
                            ));
                    ?>
                    <span class="icon_down_new float_right" id="click_open_window_contactsemployee" style=""></span>
                    <input type="hidden" value="" id="employeeId" name="employee_id">
                    <script type="text/javascript">
                        $(function(){
                            window_popup('contacts', 'Specify employee','employee','click_open_window_contactsemployee','?is_employee=1');
                        });
                    </script>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2"><?php echo translate('Allocation amount'); ?></span>
                </p>
               <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('allocation_amount',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'allocation_amount',
                            ));
                    ?>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2"><?php echo translate('Sales invoice no'); ?></span>
                </p>
               <div class="indent_new width_in float_left" style="width:61%">
                    <?php
                        echo $this->Form->input('sales_invoice_no',array(
                                'class'=>'input_4 float_left validate',
                                'name'=>'sales_invoice_no',
                            ));
                    ?>
                </div>
            <p></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height: 50%"><?php echo translate('Write off'); ?></span>
                </p>
                <div class="width_in3a float_left indent_input_tp" style="margin-top:1.7%">
                    <label class="m_check2">
                        <?php
                            echo $this->Form->input('write_off',array(
                                    'type'=>'checkbox',
                                    'class'=>'tick validate',
                                    'name'=>'write_off',
                                ));
                        ?>
                        <span style="margin-left:1%"></span>
                        <p style= "margin-left:37px;width:280px;color: #ddd"><?php echo translate('Tick to find only amounts that were write off'); ?></p>
                    </label>
                </div>
            <p></p>
            <p class="clear"></p>
            <p class="clear">
                <span class="label_1 float_left minw_lab2" style="height: 50%"><?php echo translate('Not write off'); ?></span>
                </p>
                <div class="width_in3a float_left indent_input_tp" style="margin-top:1.7%">
                    <label class="m_check2">
                        <?php
                            echo $this->Form->input('not_write_off',array(
                                    'type'=>'checkbox',
                                    'class'=>'tick validate',
                                    'name'=>'not_write_off',
                                ));
                        ?>
                        <span style="margin-left:1%"></span>
                        <p style= "margin-left:37px;width:280px;color: #ddd"><?php echo translate('Tick to find only amounts that were NOT write off'); ?></p>
                    </label>
                </div>
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
        $('.tick').change(function() {
            if($(this).attr('id') == 'write_off')
            {
                $(this).val(0);
                if($(this).is(':checked'))
                {
                    $(this).val(1);
                    $('#not_write_off').attr('checked', false);
                    $('#not_write_off').val(0);
                }
            }
            else if($(this).attr('id') == 'not_write_off')
            {
                 $(this).val(0);
                if($(this).is(':checked'))
                {
                    $(this).val(1);
                    $('#write_off').attr('checked', false);
                    $('#write_off').val(0);
                }
            }
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
            window.location.replace('<?php echo URL; ?>/receipts/options');
        });
        $('#ContinueButton').click(function(){
            var empty = check_Empty();
            if(empty==true)
            {
                confirms('Message','The provided criteria is not valid. Enter a valid request before proceeding.');
                return false;
            }
            else
            {
                mywindow = window.open("about:blank", "Download PDF");
                $.ajax({
                    type: 'POST',
                    data: {data : $('#form_receipt').serialize()},
                    async: false,
                    success : function(result)
                    {
                        console.log(result);
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
        function after_choose_companies(_id,company_name,keys)
        {
            $('#'+keys+'').val(company_name);
            $('#'+keys+'Id').val(_id);
            $("#window_popup_companies"  + keys).data("kendoWindow").close();
            tasks_init_popup_contacts("force_re_install");
            return false;

        }
        function after_choose_contacts(_id,contact_name,keys)
        {
            $('#'+keys+'').val(contact_name);
            $('#'+keys+'Id').val(_id);
            $("#window_popup_contacts"  + keys).data("kendoWindow").close();
            return false;
        }
</script>