
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
<?php
  $totals = 0;
  if(empty($arr_receipt))
    for($i=0;$i<11;$i++){
      $bg = ($i%2 ==0 ? 'bg2' : 'bg1');
      echo '<ul class="ul_mag clear '.$bg.'  line_box"></ul>';

  }else{
    $i = 0;
    foreach($arr_receipt as $value)
      foreach($value['receipts'] as $val){
      $bg = ($i%2 ==0 ? 'bg2' : 'bg1');
      $val['amount'] = (isset($val['amount']) ? $val['amount'] : 0);
      $val['amount'] = (float)str_replace(',', '', $val['amount']);
      $totals += $val['amount'];
    ?>
<ul class="ul_mag clear <?php echo $bg; ?>  line_box" id="<?php echo $value['_id'] ?>">
  <li class="hg_padd " style="width:1%;" title="View detail" onclick=" window.location.assign('<?php echo URL.'/receipts/entry/'.$value['_id']; ?>');">
     <span class="icon_emp"></span>
  </li>
  <li class="hg_padd datainput_receipt " style="  text-align:center; width:7%;">
     <div class="receipt_no" id="receipt_no" title="Receipt no"><?php echo $value['code']; ?></div>
     <input type="hidden" id="id" name="id" value="<?php echo $value['_id']; ?>" />
     <input type="hidden" id="key" name="key" value="<?php echo $val['key']; ?>" />
  </li>
  <li class="hg_padd datainput_receipt " style="  text-align:center; width:10%;">
       <?php
           echo $this->Form->input('date'.$value['_id'],array(
               'class'=>'JtSelectDate input_inner jt_box_save',
               'style'=>'text-align:center',
               'name'=>'receipt_date',
               'value'=> date('m/d/Y', $value['date']->sec),
               ));
       ?>
  </li>
 <!--  <li class="hg_padd datainput_receipt " style="  text-align:left; width:12%;">
     <span class="float_left rowedit" id="box_edit_sizew_0" style="width:100%;">
        <?php echo $this->Form->input('paid_by_'.$value['_id'].$val['key'], array(
               'class' => 'input_inner jt_box_save',
               'value'=>(isset($value['paid_by']) ? $value['paid_by'] : '')
       )); ?>
       <input type="hidden" name="paid_by" id="paid_by_<?php echo $value['_id'].$val['key']; ?>Id" value="<?php echo (isset($value['paid_by']) ? $value['paid_by'] : ''); ?>" />
       <script type="text/javascript">
           $(function () {
               $("#paid_by_<?php echo $value['_id'].$val['key']; ?>").combobox(<?php echo json_encode($arr_data['paid_by']); ?>);
           });
        </script>
     </span>
  </li> -->
  <li class="hg_padd datainput_receipt " style=" overflow: visible !important; text-align:left; width:12%;">
     <span class="float_left rowedit" id="box_edit_sizew_0" style="width:100%;">
        <?php echo $this->Form->input('our_bank_account_'.$value['_id'].$val['key'], array(
               'class' => 'input_inner jt_box_save',
               'value'=>(isset($value['our_bank_account'])&&$value['our_bank_account']!='' ? $arr_data['account'][$value['our_bank_account']] : ''),
       )); ?>
       <input type="hidden" name="our_bank_account" id="our_bank_account_<?php echo $value['_id'].$val['key']; ?>Id" value="<?php echo (isset($value['our_bank_account']) ? $value['our_bank_account'] : ''); ?>" />
       <script type="text/javascript">
           $(function () {
               $("#our_bank_account_<?php echo $value['_id'].$val['key']; ?>").combobox(<?php echo json_encode($arr_data['account']); ?>);
           });
        </script>
     </span>
  </li>
  <li class="hg_padd datainput_receipt " style="  text-align:left; width:20%;">
     <span class="float_left rowedit" id="box_edit_sizew_0" style="width:100%;">
        <input type="text" name="note" id="note" value="<?php echo (isset($val['note']) ? $val['note'] : ''); ?>" class="input_inner jt_box_save">
     </span>
  </li>
  <li class="hg_padd datainput_receipt " style="  text-align:left; width:20%;">
     <span class="float_left rowedit" id="box_edit_sizew_0" style="width:100%;">
        <input type="text" name="notes" id="notes" value="<?php echo (isset($value['notes']) ? $value['notes'] : ''); ?>" class="input_inner jt_box_save">
     </span>
  </li>
  <li class="hg_padd datainput_receipt " style=" overflow: visible !important; text-align:left; width:8%;">
     <span class="float_left rowedit" id="box_edit_sizew_0" style="width:100%;">
        <input type="checkbox" name="write_off" id="write_off" value="<?php echo (isset($val['write_off'])&&$val['write_off']==1 ? '1':'0'); ?>" <?php echo (isset($val['write_off'])&&$val['write_off']==1 ? "checked":"") ?> class="input_inner jt_box_save">
     </span>
  </li>
  <li class="hg_padd datainput_receipt " style="text-align:right; width:11%;">
     <span class="float_left rowedit" style="width:100%;">
        <input type="text"  class="input_inner jt_box_save" name="amount" id="amount" onkeypress="return isPrice(event);" value="<?php echo $this->Common->format_currency((float)$val['amount']); ?>" style="text-align:right;">
    </span>
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
 <input type="hidden" id="totals_receipt" value = "<?php echo $this->Common->format_currency($totals); ?>" />
</div>
 <script type="text/javascript">
$(function(){
  <?php //START PERMISSION ?>
  <?php if(!$this->Common->check_permission('receipts_@_entry_@_edit',$arr_permission)): ?>
  $("input","#receipt_content").each(function(){
     $(this).attr('readonly',true);
     if($(this).attr('type')=='checkbox')
        $(this).attr('disabled',true);
  });
  $(".combobox","#receipt_content").each(function(){
    $(this).remove();
  });
  <?php endif; ?>
  <?php if(!$this->Common->check_permission('receipts_@_entry_@_view',$arr_permission)): ?>
  $("#receipt_content").find('[onclick]').each(function(){
     $(this).removeAttr('onclick');
     $(this).removeAttr('title');
     $(this).html('');
  });
  <?php endif; ?>
  <?php //END PERMISSION ?>
  <?php if($this->Common->check_permission('receipts_@_entry_@_edit',$arr_permission)
           ||$this->Common->check_permission('receipts_@_entry_@_add',$arr_permission)): ?>
  input_show_select_calendar(".JtSelectDate", "#load_subtab");
  <?php endif; ?>
  $('#load_subtab :input').change(function(){
    if($(this).attr('name')=="write_off")
    {
      $(this).val(0);
      if($(this).is(":checked"))
         $(this).val(1);
    }
    update_receipt($('input',$(this).closest('ul')).serialize());
    return false;
  });

});
 </script>