<?php
$i = 1;$total_box=0;
foreach ($arr_enquiry as $key => $value) {
	$i = 3- $i;
	$one_record=0;
	if(isset($value['enquiry_value']))
	{
	    $one_record=$value['enquiry_value'];
	    settype($one_record,'float');
        $total_box+=$one_record;
	}
?>
<ul class="ul_mag clear bg<?php echo $i; ?>" onclick="dashboard_run('enquiries', '<?php echo $value['_id']; ?>')" title="View detail">
    <li class="hg_padd center_txt" style="width:26%;"><?php if(isset($value['date'])&&is_object($value['date']))echo $this->Common->format_date( $value['date']->sec, false); ?></li>
    <li class="hg_padd " style="width:41%"><?php if(isset($value['company']))echo $value['company']; ?></li>
    <li class="hg_padd right_txt no_border" style="width:29%"><?php if(isset($one_record))echo number_format($one_record,2); ?></li>
</ul>
<?php } ?>

<script type="text/javascript">
$(function(){
    $("#enquiries_total").val("<?php echo number_format($total_box, 2); ?>");
});
</script>