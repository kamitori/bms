<?php
$i = 1;$total_box=0;
foreach ($arr_salesinvoice as $key => $value) {
	$i = 3- $i;
	$one_record=0;
	 if(isset($value['sum_sub_total'])){
						 $one_record=$value['sum_sub_total'];
								settype($one_record,'float');
								 $total_box += $value['sum_sub_total'];
					}
?>
<ul class="ul_mag clear bg<?php echo $i; ?>" onclick="dashboard_run('salesinvoices', '<?php echo $value['_id']; ?>')" title="View detail">
	<li class="hg_padd center_txt" style="width:25%">
		<?php if(isset($value['payment_due_date']) && is_object($value['payment_due_date']))
				echo $this->Common->format_date( $value['payment_due_date']->sec, false); ?>
	</li>
	<li class="hg_padd " style="width:34%"><?php echo $value['code']; ?>-<?php echo $value['company_name']; ?></li>
	<li class="hg_padd right_txt" style="width:26%"><?php if(isset($one_record))echo number_format($one_record,2); ?></li>
	<li class="hg_padd center_txt no_border" style="width:9.4%">
		<?php
		if(isset($value['payment_due_date']) && is_object($value['payment_due_date'])){
			if( ($value['payment_due_date']->sec + 86400) < strtotime('now')){
				echo '<span class="Late">X</span>';
			}
		}
		?>
	</li>
</ul>
<?php } ?>
<script type="text/javascript">
$(function(){
	$("#salesinvoices_total").val("<?php echo number_format($total_box, 2); ?>");
});
</script>