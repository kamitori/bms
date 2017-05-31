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
                'name'=>'type',
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
        <input type="text" <?php if($value['type']=='Credit') echo 'disabled="disabled"'; ?>  class="input_inner jt_box_save validate_<?php echo $key; ?>" name="paid" id="paid_<?php echo $key; ?>" onkeypress="return isPrice(event);" value="<?php echo (isset($value['paid'])&&$value['paid']!=0 ? $char.$this->Common->format_currency((float)$value['paid']) : ''); ?>" style="text-align:right; <?php if($value['type']=='Credit') echo 'color:red;'; ?>">
        </span>
    </li>
    <li class="hg_padd datainput_supplier_invoice " style="text-align:center; width:1%;">
        <?php if($delete): ?>
        <div class="middle_check delete_supplier_invoice_<?php echo $key; ?>">
            <a title="Delete link " href="javascript:void(0)" onclick="delete_supplier_invoice(<?php echo $key; ?>)"><span class="icon_remove2"></span></a>
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
<?php if($this->Common->check_permission($controller.'_@_supplier_invoice_tab_@_edit')):  ?>
<script type="text/javascript">
    $(function() {
        input_show_select_calendar(".JtSelectDate", "#supplier_invoice_content");
    });
</script>
<?php endif; ?>
