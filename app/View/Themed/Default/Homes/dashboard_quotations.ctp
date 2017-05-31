<?php
$i = 1;
$total_box=0;
foreach ($arr_quotation as $key => $value) {
	$i = 3- $i;
	$one_record=0;
	 if(isset($value['sum_sub_total'])){
				 $one_record=$value['sum_sub_total'];
						settype($one_record,'float');
						 $total_box += $value['sum_sub_total'];
			}
?>
<ul class="ul_mag clear bg<?php echo $i; ?>" onclick="dashboard_run('quotations', '<?php echo $value['_id']; ?>')" title="View detail">
	<li class="hg_padd center_txt" style="width:25%">
		<?php if(is_object($value['payment_due_date']) && isset($value['payment_due_date']))
					echo $this->Common->format_date( $value['payment_due_date']->sec, false); ?>
	</li>
	<li class="hg_padd " style="width:33%"><?php if ( isset($value['salesorder_number']) && $value['salesorder_number']!='') echo $value['salesorder_number']; else echo 'XXX'; ?>-<?php if ( isset($value['company_name']) && $value['company_name']!='') echo $value['company_name']; else echo 'Empty Company'; ?></li>
	<li class="hg_padd right_txt" style="width:27%"><?php if(isset($one_record))echo $this->Common->format_currency($one_record); ?></li>
	<li class="hg_padd center_txt no_border" style="width:9%">
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
	$("#quotations_total").val("<?php echo $this->Common->format_currency($total_box); ?>");
});
</script>