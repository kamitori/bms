<?php $edit =  $this->Common->check_permission($controller.'_@_entry_@_edit',$arr_permission); ?>
<style type="text/css">
    ul.ul_mag li.hg_padd {
    overflow: visible !important;
    }
    .bg4 {
    background: none repeat scroll 0 0 #949494;
    color: #fff;
    }
    .bg4 span h4 {
    margin-left: 1%;
    width: 100%;
    }
</style>
<div class="clear_percent" id="supplier_invoice" style="width:100%; margin-left: 0;">
    <div class="float_left " style=" width:100%;margin-top:0;">
        <div class="tab_1 full_width" id="block_full_receipt">
            <!-- Header-->
            <span class="title_block bo_ra1">
                <span class="fl_dent">
                    <h4><?php echo translate('Supplier invoices received linked to this order'); ?></h4>
                </span>
                <?php if($edit): ?>
                <a title="Add line" id="bt_add_supplier_invoice" href="javascript:void(0)">
                <span class="icon_down_tl top_f"></span>
                </a>
                <?php endif; ?>
                <!-- <div class="float_left hbox_form" style="width:auto; margin-left:5px;">
                    <a href="javascript:void(0)">
                    <input class="btn_pur" id="supplier_invoice_report" type="button" value="Print invoice receipt" style="width:99%;">
                    </a>
                </div> -->
            </span>
            <!--CONTENTS-->
            <div class="jt_subtab_box_cont" style=" height:282px;">
                <ul class="ul_mag clear bg3">
                    <li class="hg_padd" style="text-align:center;width:9%;">
                        <?php echo translate('Suppier invoice no'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:center;width:5%;">
                        <?php echo translate('Date'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:left;width:4%;">
                        <?php echo translate('Type'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:left;width:4%;">
                        <?php echo translate('Status'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:left;width:3%;">
                        <?php echo translate('Term'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:center;width:5%;">
                        <?php echo translate('Due Day'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:center;width:4%;">
                        <?php echo translate('Days left'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:center;width:2%;">
                        <?php echo translate('Due'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:left;width:8%;">
                        <?php echo translate('Notes'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:center;width:3%;">
                        <?php echo translate('N/C'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:right;width:6%;">
                        <?php echo translate('Amount'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:right;width:6%;">
                        <?php echo translate('Tax'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:right;width:6%;">
                        <?php echo translate('Total inc. tax'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:right;width:4%;">
                        <?php echo translate('Approved'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:right;width:6%;">
                        <?php echo translate('Approved by'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:right;width:5%">
                        <?php echo translate('Paid'); ?>
                    </li>
                    <li class="hg_padd" style="text-align:center;width:1%">
                    </li>
                </ul>
                <div class="clear" id="container_receipt" style=" overflow-y:auto;height:300px;">
                    <span id="click_open_window_contacts" style="height: 1px; width: 1px;"></span>
                    <input type="hidden" id="key_popup" />
                    <?php if($edit): ?>
                    <script type="text/javascript">
                        $(function(){
                            var parameter_get = "?is_employee=1";
                            window_popup("contacts", "Specify employee",'_supplier_invoice', "click_open_window_contacts",parameter_get);
                        });
                        function after_choose_contacts_supplier_invoice(contact_id,contact_name,blank){
                             $("#window_popup_contacts_supplier_invoice").data("kendoWindow").close();
                             var key = $('#key_popup').val();
                             $('#approved_by_'+key).val(contact_name);
                             $('#approved_by_'+key+'Id').val(contact_id);
                             var data = $('input','ul#'+key).serialize();
                                console.log(data);
                              if(key!=undefined)
                                update_supplier_invoice(key,data);
                            return false;
                        }
                    </script>
                    <?php endif; ?>
                    <div class="mCSB_container mCS_no_scrollbar" id="supplier_invoice_content" style="position: relative; top: 0px;">
                        <!--- Supplier_
                            invoice se hien o day, load,update,add -->
                        <?php
                            if(empty($po['supplier_invoice']))
                              for($i=0;$i<12;$i++){
                                $bg = ($i%2 ==0 ? 'bg2' : 'bg1');
                                echo '<ul class="ul_mag clear '.$bg.'  line_box"></ul>';

                            }else{
                              $i = 0;
                               $total_invoice = 0;
                               $total_paid = 0;
                               $total_credit = 0;
                              foreach($po['supplier_invoice'] as $key=>$value){
                                  if(!is_array($value)) continue;
                                  $bg = ($i%2 ==0 ? 'bg2' : 'bg1');
                                  if($value['type']!='Credit'){
                                    $total_invoice += (isset($value['total_with_tax'])&&$value['total_with_tax']!='' ? (float)$value['total_with_tax'] : 0);
                                    $total_paid += (isset($value['paid'])&&$value['paid']!='' ? (float)$value['paid'] : 0);
                                 } else
                                    $total_credit += (isset($value['paid'])&&$value['paid']!='' ? (float)$value['paid'] : 0);
                              ?>
                        <ul class="ul_mag clear <?php echo $bg; ?>  line_box" id="<?php echo $key ?>">
                            <li class="hg_padd datainput_supplier_invoice " style="  text-align:center; width:9%;">
                                <?php
                                    echo $this->Form->input('no_'.$key,array(
                                        'class'=>'input_inner jt_box_save validate_'.$key,
                                        'name'=>'no',
                                        'value'=> $value['no']
                                        ,
                                        ));
                                    ?>
                                <input type="hidden" id="key" name="key" value="<?php echo $key; ?>" />
                            </li>
                            <li class="hg_padd datainput_supplier_invoice " style="  text-align:center; width:5%;">
                                <?php
                                    echo $this->Form->input('date_'.$key,array(
                                        'class'=>'JtSelectDate input_inner jt_box_save validate_'.$key,
                                        'style'=>'text-align:center',
                                        'name'=>'date',
                                        'value'=> date('m/d/Y', $value['date']->sec),
                                        ));
                                    ?>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice supplier_invoice_type_<?php echo $key; ?>" style="  text-align:left; width:4%;">
                                <span class="float_left rowedit" id="box_edit_sizew_0" style="width:100%;">
                                    <?php echo $this->Form->input('type_'.$key, array(
                                        'class' => 'input_inner jt_box_save validate_'.$key,
                                        'value'=> $value['type'],
                                        'name'=>'type'
                                        )); ?>
                                    <input type="hidden" name="type" id="type_<?php echo $key; ?>Id" value="<?php echo $value['type']; ?>" />
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#type_<?php echo $key; ?>").combobox(<?php echo json_encode($arr_data['salesinvoices_type']); ?>);
                                        });
                                    </script>
                                </span>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice " style=" overflow: visible !important; text-align:left; width:4%;">
                                <span class="float_left rowedit" id="box_edit_sizew_0" style="width:100%;">
                                    <?php echo $this->Form->input('status_'.$key, array(
                                        'class' => 'input_inner jt_box_save supplier_invoice_status',
                                        'value'=>$value['status']
                                        )); ?>
                                    <input type="hidden" name="status" id="status_<?php echo $key; ?>Id" value="<?php echo $value['status']; ?>" />
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#status_<?php echo $key; ?>").combobox(<?php echo json_encode($arr_data['salesinvoices_status']); ?>);
                                        });
                                    </script>
                                </span>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice " style="  text-align:left; width:3%;">
                                <span class="float_left rowedit" id="box_edit_sizew_0" style="width:100%;">
                                <input type="text" name="term" value="<?php echo (isset($value['term']) ? $value['term'] : 0) ?>" class="input_inner jt_box_save validate_<?php echo $key; ?>" id="term_<?php echo $key ?>" onkeypress="return isPrice(event);" />
                                </span>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice " style="  text-align:center; width:5%;">
                                <span id="due_date_<?php echo $key; ?>">
                                <?php echo $value['due_date']!='' ? $this->Common->format_date($value['due_date']->sec) : ''; ?>
                                </span>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice " style="  text-align:center; width:4%;">
                                <span id="day_left_<?php echo $key; ?>" <?php if($value['day_left']<0) echo 'style="color:red"'; ?>>
                                <?php echo $value['day_left']; ?>
                                </span>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice " style="text-align:right; width:2%;">
                                <span class="float_left rowedit" style="width:100%;text-align: center">
                                <input type="checkbox" name="due" value="<?php echo $value['due'] ?>" <?php echo ($value['due']==1 ? "checked" : ''); ?>  disabled />
                                </span>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice " style=" width:8%;">
                                <?php
                                    echo $this->Form->input('notes_'.$key,array(
                                        'class'=>'input_inner jt_box_save validate_'.$key,
                                        'style'=>'text-align:left',
                                        'name'=>'notes',
                                        'value'=> $value['notes'],
                                        ));
                                    ?>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice" style="text-align:left; width:3%;">
                                <span class="float_left rowedit" id="box_edit_sizew_0" style="width:100%;">
                                    <?php echo $this->Form->input('nc_'.$key, array(
                                        'class' => 'input_inner jt_box_save validate_'.$key,
                                        )); ?>
                                    <input type="hidden" name="nc" id="nc_<?php echo $key; ?>Id" value="<?php echo $value['nc']; ?>" />
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#nc_<?php echo $key; ?>").combobox(<?php echo json_encode(@$arr_data['']); ?>);
                                        });
                                    </script>
                                </span>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice " style="text-align:right; width:6%;">
                                <span class="float_left rowedit" style="width:100%;">
                                <input type="text"  class="input_inner jt_box_save validate_<?php echo $key; ?>" name="amount" id="amount_<?php echo $key; ?>" onkeypress="return isPrice(event);" value="<?php echo (isset($value['amount'])&&$value['amount']!='' ? $this->Common->format_currency((float)$value['amount']) : '0.00'); ?>" style="text-align:right;">
                                </span>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice" style="text-align:right; width:6%;">
                                <span class="float_left rowedit" id="box_edit_sizew_0" style="width:100%;">
                                <input type="text"  class="input_inner jt_box_save validate_<?php echo $key; ?>" name="tax" id="tax_<?php echo $key; ?>" onkeypress="return isPrice(event);" value="<?php echo (isset($value['tax'])&&$value['tax']!='' ? $this->Common->format_currency((float)$value['tax']) : '0.00'); ?>" style="text-align:right;">
                                </span>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice " style="text-align:right; width:6%;">
                                <span class="float_left rowedit" style="width:100%;">
                                <input type="text" readonly="readonly"  class="input_inner jt_box_save validate_<?php echo $key; ?>" name="total_with_tax" id="total_with_tax_<?php echo $key; ?>" onkeypress="return isPrice(event);" value="<?php echo (isset($value['total_with_tax'])&&$value['total_with_tax']!='' ? $this->Common->format_currency((float)$value['total_with_tax']) : '0.00'); ?>" style="text-align:right;">
                                </span>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice " style="text-align:right; width:4%;">
                                <span class="float_left rowedit" style="width:100%;text-align: center">
                                <input type="checkbox" class="validate_<?php echo $key; ?>" name="approved" id="approved_<?php echo $key; ?>" value="<?php echo $value['approved'] ?>" <?php echo ($value['approved']==1 ? "checked" : ''); ?> />
                                </span>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice" style="text-align:left; width:6%;">
                                <span class="float_left rowedit" id="box_edit_sizew_0" style="width:100%;">
                                <?php echo $this->Form->input('approved_by_'.$key, array(
                                    'class' => 'input_2 float_left validate '.$bg,
                                    'name'=>'approved_by_name',
                                    'style'=> 'margin-top: 0;border: none;',
                                    'value'=>(isset($value['approved_by_name']) ? $value['approved_by_name'] : '' )
                                    ));
                                    ?>
                                <input type="hidden" name="approved_by_id" id="approved_by_<?php echo $key; ?>Id" value="<?php echo (isset($value['approved_by_id']) ? $value['approved_by_id'] : '' ) ?>" />
                                <span class="icon_down_new float_right" style="margin-top: -13px;" id="click_window_popup_<?php echo $key; ?>" onclick="$('#key_popup').val(<?php echo $key; ?>);$('#click_open_window_contacts').click()"></span>
                                </span>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice " style="text-align:right; width:5%;">
                                <span class="float_left rowedit" style="width:100%;">
                                <?php $char= ''; if($value['type']=='Credit') $char='-';?>
                                <input type="text" <?php if($value['type']=='Credit') echo 'disabled="disabled"'; ?> class="input_inner jt_box_save validate_<?php echo $key; ?>" name="paid" id="paid_<?php echo $key; ?>" onkeypress="return isPrice(event);" value="<?php echo (isset($value['paid'])&&$value['paid']!=0 ? $char.$this->Common->format_currency((float)$value['paid']) : ''); ?>" style="text-align:right;<?php if($value['type']=='Credit') echo 'color:red;'; ?>">
                                </span>
                            </li>
                            <li class="hg_padd datainput_supplier_invoice " style="text-align:center; width:1%;">
                                <?php if($edit): ?>
                                <div class="middle_check delete_supplier_invoice_<?php echo $key; ?>">
                                    <a title="Delete link" href="javascript:void(0)" onclick="delete_supplier_invoice(<?php echo $key; ?>)"><span class="icon_remove2"></span></a>
                                </div>
                            <?php endif; ?>
                            </li>
                        </ul>
                        <?php
                            $i++;
                            }
                                if($i<12)
                                {
                                  $j = 11 - $i;
                                  for($k = 0; $k < $j; $k++)
                                  {
                                     $bg = ($i%2 ==0 ? 'bg2' : 'bg1');
                                    echo '<ul class="ul_mag clear '.$bg.'  line_box"></ul>';
                                    $i++;
                                  }
                                }
                            }
                            ?>
                        <input type="hidden" id="t_credit" value="<?php echo (isset($total_credit)&&$total_credit!=''? $total_credit : 0) ?>" />
                        <input type="hidden" id="t_invoice" value="<?php echo (isset($total_invoice)&&$total_invoice!=''? $total_invoice : 0) ?>" />
                        <input type="hidden" id="t_paid" value="<?php echo (isset($total_paid)&&$total_paid!=0? $total_paid : 0) ?>" />
                    </div>
                </div>
                <script>
                    $(window).load(function(){
                        $("#container_receipt").mCustomScrollbar({
                            scrollButtons:{
                                enable:false
                            }
                        });
                    });

                    $(function(){
                        Scrollbar('container_receipt');
                    });
                </script>
            </div>
            <!--<span class="hit"></span>-->
            <!--Footer-->
            <span class="title_block bo_ra2" style="line-height: 25px">
                <input class="input_w2 float_right" id="balance_invoice" type="text" style="width:6%; margin:0 5% 0 0;color:#444;text-align: right" value="" readonly="readonly">
                <span class="float_right" style="width:95px;font: 11px arial,verdana,sans-serif;"><?php echo translate('Balance (invoice)'); ?></span>
                <input class="input_w2 float_right" id="balance_order" type="text" style="width:6%; margin:0 1% 0 0;color:#444;text-align: right" value="" readonly="readonly">
                <span class="float_right" style="width:80px;font: 11px arial,verdana,sans-serif;"><?php echo translate('Balance (order)'); ?></span>
                <input class="input_w2 float_right" id="total_paid" type="text" style="width:6%; margin:0 1% 0 0;color:#444;text-align: right" value="" readonly="readonly">
                <span class="float_right" style="width:50px;font: 11px arial,verdana,sans-serif;"><?php echo translate('Total paid'); ?></span>
                <input class="input_w2 float_right" id="balance" type="text" style="width:6%; margin:0 8% 0 0;color:#444;text-align: right" value="" readonly="readonly">
                <span class="float_right" style="width:50px;font: 11px arial,verdana,sans-serif;"><?php echo translate('Balance'); ?></span>
                <input class="input_w2 float_right" id="total_invoices" type="text" style="width:6%; margin:0 1% 0 0;color:#444;text-align: right" value="" readonly="readonly">
                <span class="float_right" style="width:75px;font: 11px arial,verdana,sans-serif;"><?php echo translate('Total invoices'); ?></span>
                <input class="input_w2 float_right" id="total_order"  type="text" style="width:6%; margin:0 1% 0 0;color:#444;text-align: right" value="<?php echo (isset($po['sum_amount'])&&$po['sum_amount']!= '' ? $this->Common->format_currency($po['sum_amount']) : '' ); ?>" readonly="readonly">
                <span class="float_right" style="width:75px;font: 11px arial,verdana,sans-serif;"><?php echo translate('Total order'); ?></span>
                <div class="clear"></div>
            </span>
        </div>
    </div>
    <p class="clear"></p>
</div>
<script type="text/javascript">
    function lockInput(){
        $('.supplier_invoice_status').each(function(){
            if($(this).val()=='Paid'){
                var key = $(this).closest('ul').attr('id');
                $('.validate_'+key).each(function(){
                    $(this).css('background','rgba(255, 255, 255, 0)');
                    $(this).attr('disabled', 'disabled');
                })
                $('#click_window_popup_'+key).attr('style', 'display:none');
                $('.supplier_invoice_type_'+key+' .combobox_button').remove();
                $('.supplier_invoice_type_'+key+' .combobox_selector').remove();
                $('.delete_supplier_invoice_'+key).remove();
            }

        })
    }
    function unlockInput(key){
        $('.validate_'+key).each(function(){
            $(this).removeAttr('disabled');
        })
        $('#paid_'+key).attr('disabled', 'disabled');
    }
    <?php if($edit): ?>
    function update_supplier_invoice(key,data){
        $.ajax({
            url: '<?php echo URL; ?>/purchaseorders/supplier_invoice_ajax',
            data: {act: 'update',key: key, data: data},
            type: 'POST',
            success: function(html){
                $("#load_subtab").html(html);
                cal_total();
                lockInput();
            }
        });
    }
    function delete_supplier_invoice(key){
        confirms('Message','Are you sure to delete this record?',
            function(){
                 $.ajax({
                    url: '<?php echo URL; ?>/purchaseorders/supplier_invoice_ajax',
                    data: {act: 'delete', key: key},
                    type: 'POST',
                    success: function(html){
                        $("#load_subtab").html(html);
                        cal_total();
                        lockInput();
                    }
                });
            });
        return false;
    }
    function add_supplier_invoice(type){
        $.ajax({
            url: '<?php echo URL; ?>/purchaseorders/supplier_invoice_ajax',
            data: {act: 'add',type: type},
            type: 'POST',
            success: function(html){
                $("#load_subtab").html(html);
                cal_total();
            }
        });
        return false;
    }
    <?php endif; ?>
    function cal_total(){
       $('#balance').css('color');
       $('#balance_order').css('color');
       $('#balance_invoice').css('color');
      var total_invoice = parseFloat($('#t_invoice').val());
      var total_paid = parseFloat($('#t_paid').val());
      var total_credit = parseFloat($('#t_credit').val());
      var balance = 0;
      var balance_invoice = total_invoice - total_paid - total_credit;
      var value = 0;
      if($('#total_order').val()!='')
      {
          value = $('#total_order').val();
          value = value.replace(',','');
      }
      balance = parseFloat(value) - total_invoice;
      var balance_order = parseFloat(value) - total_paid;
      $('#total_invoices').val(total_invoice.formatMoney(2, '.', ','));
       $('#total_paid').val(total_paid.formatMoney(2, '.', ','));
      $('#balance').val(balance.formatMoney(2, '.', ','));
      $('#balance_order').val(balance_order.formatMoney(2, '.', ','));
      $('#balance_invoice').val(balance_invoice.formatMoney(2, '.', ','));
      if(parseFloat(balance)<0)
           $('#balance').css('color','red');
      if(parseFloat(balance_order)<0)
           $('#balance_order').css('color','red');
      if(parseFloat(balance_invoice)<0)
           $('#balance_invoice').css('color','red');
    }
    function print_report(){
        $.ajax({
            url: '<?php echo URL ?>/purchaseorders/supplier_invoice_report',
            success: function(url){
                console.log(url);
                // if(url)
                //     window.location.replace(url);
            }
        });

    }
    $(function() {
        cal_total();
        lockInput();
        <?php if($edit):  ?>
        input_show_select_calendar(".JtSelectDate", "#supplier_invoice_content");
        $('#bt_add_supplier_invoice').click(function() {
            if($('#company_id').val()=='')
                alerts('Message','This function cannot be performed as there is no supplier linked to this purchase order.');
            else{
                $.ajax({
                        url: '<?php echo URL; ?>/purchaseorders/check_fully_supplier_invoice',
                        success: function(result){
                            if(result=='false'){
                                var arr = ['Yes','No',''];
                                confirms3('Message','Create a purchase invoice for the full order?',arr,
                                    function(){//YES
                                        add_supplier_invoice('full');
                                    },function(){
                                        add_supplier_invoice('part');
                                    },function(){
                                        return false;
                                    },function(){
                                        return false;
                                    });
                            }
                            else
                                add_supplier_invoice('part');
                      }
                  });

            }
        });
        $('#supplier_invoice_report').click(function(){
            if($('.datainput_supplier_invoice').length == 0)
                alerts('Message','There are no supplier invoices received for this purchase order.');
            else
                window.location.replace('<?php echo URL ?>/purchaseorders/supplier_invoice_report');
            return false;
        });
        $('#supplier_invoice_content').on('change','input',function(){
            var key = $(this).closest('ul').attr('id');
            if(key!=undefined){
                $('#amount_'+key).val(parseFloat($('#amount_'+key).val().replace(/[,]/g,'')));
                $('#tax_'+key).val(parseFloat($('#tax_'+key).val().replace(/[,]/g,'')));
                $('#total_with_tax_'+key).val(parseFloat($('#total_with_tax_'+key).val().replace(/[,]/g,'')));
                if($('#paid_'+key).val()!='')
                    $('#paid_'+key).val(parseFloat($('#paid_'+key).val().replace(/[,]/g,'')));

                if($(this).attr('name') == 'approved'){
                    $(this).val(0);
                    if($(this).is(':checked')){
                        if($('#approved_by_'+key+'Id').val()=='' ){
                            $('#approved_by_'+key).val("<?php echo $arr_data['current_user']; ?>");
                            $('#approved_by_'+key+'Id').val("<?php echo $arr_data['current_user_id']; ?>");
                        }
                        $(this).val(1);
                    }
                }
                else if($(this).attr('id') == 'status_'+key){

                    if($(this).val()!='Paid'){
                        unlockInput(key);
                    }
                }
                var data = $('input','ul#'+key).serialize();
                update_supplier_invoice(key,data);
            }
        });
        <?php else: ?>
        $("input","#supplier_invoice_content").each(function(){
            $(this).removeClass("jt_box_save ").attr("disabled",true).css("background-color","transparent");
        });
        <?php endif; ?>
    });
</script>
