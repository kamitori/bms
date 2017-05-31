<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent"><h4><?php echo translate('Shipping related to this job'); ?></h4></span>
		<?php if($this->Common->check_permission('shippings_@_entry_@_add',$arr_permission)): ?>
		<a href="<?php echo URL; ?>/jobs/shipping_add/<?php echo $job_id; ?>" title="Add new shipping">
			<span class="icon_down_tl top_f"></span>
		</a>
		<?php endif; ?>
	</span>
	<p class="clear"></p>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd" style="width:1%;"></li>
		<li class="hg_padd" style="width:3%;"><?php echo translate('Ref no'); ?></li>
		<li class="hg_padd center_txt" style="width:10%;"><?php echo translate('Type'); ?></li>
		<li class="hg_padd center_txt" style="width:8%;"><?php echo translate('Date Shipped'); ?></li>
		<li class="hg_padd center_txt" style="width:10%;"><?php echo translate('Return'); ?></li>
		<li class="hg_padd" style="width:8%;"><?php echo translate('Status'); ?></li>
		<li class="hg_padd" style="width:13%;"><?php echo translate('Our rep'); ?></li>
		<li class="hg_padd" style="width: 12%;"><?php echo translate('Shipper'); ?></li>
		<li class="hg_padd" style="width: 23%;"><?php echo translate('Tracking no'); ?></li>
		<li class="hg_padd bor_mt" style="width:1%;"></li>
	</ul>
	<div class="container_same_category" style="height: 200px;overflow-y: auto;">
		<?php
		$i = 1; $count = 0;
		foreach ($arr_shipping as $key => $value) {
		?>
		<ul class="ul_mag clear bg<?php echo $i; ?>" id="Job_shipping_<?php echo $value['_id']; ?>">
			<li class="hg_padd" style="width:1%;">
				<a href="<?php echo URL; ?>/shippings/entry/<?php echo $value['_id']; ?>">
					<span class="icon_emp"></span>
				</a>
			</li>
			<li class="hg_padd" style="width:3%;"><?php echo $value['code']; ?></li>
			<li class="hg_padd center_txt" style="width:10%;"><?php echo $value['shipping_type']; ?></li>
			<li class="hg_padd center_txt" style="width:8%;"><?php if(is_object($value['shipping_date']))echo $this->Common->format_date($value['shipping_date']->sec,false); ?></li>
			<li class="hg_padd center_txt" style="width:10%;"><?php if(isset($value['return_status']) && $value['return_status']) echo 'x'; ?></li>
			<li class="hg_padd" style="width:8%;"><?php echo isset($value['shipping_status']) ? $value['shipping_status'] : ''; ?></li>
			<li class="hg_padd" style="width:13%;"><?php echo $value['our_rep']; ?></li>
			<li class="hg_padd" style="width: 12%;"><?php echo (isset($value['shipper']) ? $value['shipper'] : ''); ?></li>
			<li class="hg_padd" style="width: 23%;"><?php if(isset($value['tracking_no']))echo $value['tracking_no']; ?></li>
			<?php if($this->Common->check_permission('shippings_@_entry_@_delete',$arr_permission)): ?>
			<li class="hg_padd bor_mt" style="width:1%;">
				<div class="middle_check">
					<a title="Delete link" href="javascript:void(0)" onclick="shipping_remove_shipping_job('<?php echo $value['_id']; ?>')">
						<span class="icon_remove2"></span>
					</a>
				</div>
			</li>
			<?php endif; ?>
		</ul>

		<?php $i = 3 - $i; $count += 1;
			}

			$count = 8 - $count;
			if( $count > 0 ){
				for ($j=0; $j < $count; $j++) { ?>
				<ul class="ul_mag clear bg<?php echo $i; ?>">
					<li class="hg_padd" style="width:1%;"></li>
					<li class="hg_padd" style="width:3%;"></li>
					<li class="hg_padd center_txt" style="width:10%;"></li>
					<li class="hg_padd" style="width:8%;"></li>
					<li class="hg_padd center_txt" style="width:10%;"></li>
					<li class="hg_padd" style="width:8%;"></li>
					<li class="hg_padd" style="width:13%;"></li>
					<li class="hg_padd" style="width: 12%;"></li>
					<li class="hg_padd" style="width: 23%;"></li>
					<li class="hg_padd bor_mt" style="width:1%;"></li>
				</ul>
		  <?php $i = 3 - $i;
				}
			}
		?>
	</div>

	<span class="title_block bo_ra2">
		<span class="float_left bt_block">
			<?php echo translate('Click to view full details'); ?>
		</span>
	</span>
</div>

<?php if($this->Common->check_permission('shippings_@_entry_@_delete',$arr_permission)): ?>
<script type="text/javascript">
function shipping_remove_shipping_job(shipping_id){
	confirms( "Message", "Are you sure you want to delete?",
		function(){

			$.ajax({
				 url: '<?php echo URL; ?>/jobs/shipping_delete/' + shipping_id + '/Salesorder',
				 timeout: 15000,
				 success: function(html){
					 if(html == "ok"){
						 $("#Job_shipping_" + shipping_id).fadeOut();
					 }else{
						 alerts("Error: ", html);
					 }
				 }
			 });
		},function(){
			//else do somthing
	});
}
</script>
<?php endif; ?>