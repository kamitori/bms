<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent"><h4><?php echo translate('Quotations for this enquiry'); ?></h4></span>
		<?php if( $this->Common->check_permission('quotations_@_entry_@_add', $arr_permission) ){ ?>
		<a href="<?php echo URL; ?>/enquiries/quotes_add/<?php echo $enquiry_id; ?>" title="Add new quotation">
			<span class="icon_down_tl top_f"></span>
		</a>
		<?php } ?>
	</span>
	<p class="clear"></p>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd" style="width:1%;"></li>
		<li class="hg_padd" style="width:4%;"><?php echo translate('Ref no'); ?></li>
		<li class="hg_padd" style="width:8%;"><?php echo translate('Type'); ?></li>
		<li class="hg_padd center_txt" style="width:6%;"><?php echo translate('Date'); ?></li>
		<li class="hg_padd center_txt" style="width:6%;"><?php echo translate('Due date'); ?></li>
		<li class="hg_padd" style="width:8%;"><?php echo translate('Status'); ?></li>
		<li class="hg_padd" style="width:10%;"><?php echo translate('Our Rep'); ?></li>
		<li class="hg_padd" style="width:10%;"><?php echo translate('Our CSR'); ?></li>
		<li class="hg_padd " style="width:6%;"><?php echo translate('Total (bf.Tax)'); ?></li>
		<li class="hg_padd" style="width:25%;"><?php echo translate('Heading'); ?></li>
		<li class="hg_padd bor_mt" style="width:1%;"></li>
	</ul>
	<div class="container_same_category" style="height: 176px; overflow-y: auto;">
		<?php
		$i = 1; $count = 0;
		foreach ($arr_quotation as $key => $value) {
		?>
		<ul class="ul_mag clear bg<?php echo $i; ?>" id="Company_Quotation_<?php echo $value['_id']; ?>">

			<li class="hg_padd" style="width:1%;">
				<a href="<?php echo URL; ?>/quotations/entry/<?php echo $value['_id']; ?>">
					<span class="icon_emp"></span>
				</a>
			</li>
			<li class="hg_padd" style="width:4%;"><?php echo $value['code']; ?></li>
			<li class="hg_padd" style="width:8%;"><?php echo $value['quotation_type']; ?></li>
			<li class="hg_padd center_txt" style="width:6%;"><?php echo $this->Common->format_date($value['quotation_date']->sec,false); ?></li>
			<li class="hg_padd center_txt" style="width:6%;"><?php if(is_object($value['payment_due_date']))echo $this->Common->format_date($value['payment_due_date']->sec,false); ?></li>
			<li class="hg_padd" style="width:8%;"><?php echo $value['quotation_status']; ?></li>
			<li class="hg_padd" style="width:10%;">
			<?php
				if(isset($value['our_rep_id'])&&is_object($value['our_rep_id'])){
					$v_contact_rep=$model_contact->select_one(array('_id' => new MongoId($value['our_rep_id'])));
					echo $v_contact_rep['first_name'].' '.$v_contact_rep['last_name'];
				}
			?>
			</li>
			<li class="hg_padd" style="width:10%;">
			<?php
				if(isset($value['our_csr_id'])&&is_object($value['our_csr_id'])){
					$v_contact_csr=$model_contact->select_one(array('_id' => new MongoId($value['our_csr_id'])));
					echo $v_contact_csr['first_name'].' '.$v_contact_csr['last_name'];
				}
			?>
			</li>
			<li class="hg_padd right_txt" style="width:6%;"><?php

			if(isset( $value['sum_sub_total']))
			{
				 $v_sum_sub_total=$value['sum_sub_total'];
				 settype($v_sum_sub_total,'float');
				 echo $this->Common->format_currency($v_sum_sub_total);
			}


			 ?></li>
			<li class="hg_padd" style="width:25%;"><?php echo $value['name']; ?></li>
			<?php if( $this->Common->check_permission('quotations_@_entry_@_delete', $arr_permission) ){ ?>
			<li class="hg_padd bor_mt" style="width:1%;">
				<div class="middle_check">
					<a title="Delete link" href="javascript:void(0)" onclick="resource_remove_quotation_enquiry('<?php echo $value['_id']; ?>')">
						<span class="icon_remove2"></span>
					</a>
				</div>
			</li>
			<?php } ?>
		</ul>

		<?php $i = 3 - $i; $count += 1;
			}

			$count = 8 - $count;
			if( $count > 0 ){
				for ($j=0; $j < $count; $j++) { ?>
				<ul class="ul_mag clear bg<?php echo $i; ?>">
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

<?php if( $this->Common->check_permission('quotations_@_entry_@_delete', $arr_permission) ){ ?>
<script type="text/javascript">
function resource_remove_quotation_enquiry(quotation_id){
	confirms( "Message", "Are you sure you want to delete?",
		function(){
			$.ajax({
				 url: '<?php echo URL; ?>/enquiries/quotes_delete/' + quotation_id,
				 timeout: 15000,
				 success: function(html){
					 if(html == "ok"){
						 $("#Company_Quotation_" + quotation_id).fadeOut();
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
<?php } ?>