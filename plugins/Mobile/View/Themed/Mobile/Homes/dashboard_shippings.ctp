<?php
$i = 1;
foreach ($arr_shipping as $key => $value) {
	$i = 3- $i;
?>
<ul class="ul_mag clear bg<?php echo $i; ?>" onclick="dashboard_run('shippings', '<?php echo $value['_id']; ?>')" title="View detail">
    <li class="hg_padd center_txt" style="width:25%">
    	<?php if(isset($value['shipping_date']) && is_object($value['shipping_date']))
    			echo $this->Common->format_date( $value['shipping_date']->sec, false); ?>
    </li>
    <li class="hg_padd no_border" style="width:72%"><?php echo $value['company_name']; ?></li>
    <!--<li class="hg_padd center_txt" style="width:10%"><?php echo $value['shipping_type']; ?></li>-->
    <!-- <li class="hg_padd" style="width:23%"><?php echo $value['carrier_name']; ?></li>-->
</ul>
<?php } ?>