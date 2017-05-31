<?php
	if(isset($sum_sub_total))
		$sum_sub_total = $this->Common->format_currency($sum_sub_total);
	else
		$sum_sub_total = 0;

	if(isset($sum_tax))
		$sum_tax = $this->Common->format_currency($sum_tax);
	else
		$sum_tax = 0;

	if(isset($sum_amount))
		$sum_amount = $this->Common->format_currency($sum_amount);
	else
		$sum_amount = 0;
?>
<span class="bt_block float_right no_bg" style="margin:0 1% 0 0; width:18%;padding:0;">
    <input class="input_7 sum" type="text" style=" width:27%; text-align:right;padding:0 3% 0 0; margin:0 2% 0 2%; " id="sum_tax" value="<?php echo $sum_tax;?>" readonly onkeypress="return isNumbers(event);" />
    <input class="input_7 sum" type="text" style=" width:41%;text-align:right;padding:0 3% 0 0; margin:0%;" id="sum_amount" value="<?php echo $sum_amount;?>" readonly onkeypress="return isNumbers(event);" />
</span>
<span class="bt_block float_right no_bg" style=" width:18%;">
    <div class="float_left" style=" width:45%; text-align:right;">
        Totals
    </div>
    <div class="float_right" style=" width:55%;">
        <input class="input_7 sum" type="text" style=" width:76%; text-align:right; padding:0 3% 0 0; margin:0 0 0 0; float:right;" id="sum_sub_total" value="<?php echo $sum_sub_total;?>" readonly onkeypress="return isNumbers(event);" />
    </div>
</span>
<?php if($this->Common->check_permission('salesorders_@_change_sum_@_edit',$arr_permission)){ ?>
<script type="text/javascript">
$(".sum").prop("readonly",false);
$(".sum").change(function(){
	var fieldname = $(this).attr("id");
	var value = $(this).val();
	// value = FortmatPrice(value);
	$(".jt_ajax_note").html("Saving...       ");
	$(this).val(FortmatPrice(value));
	$.ajax({
		url : "<?php echo URL.'/'.$controller.'/ajax_save' ?>",
		type:"POST",
		data: {field:fieldname,value:value,func:'update',ids:$("#mongo_id").val()},
		success: function(){
			ajax_note("Saving...Saved !");
		}
	});
});
</script>
<?php } ?>