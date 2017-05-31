<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>



<div id="account_related" class="clear_percent_10 float_left no_right">
	<div class="tab_1 full_width" id="company_sc_related">

		<!-- NEU CO ACCOUNT ROI-->
		<?php if(isset($this->data['Salesaccount'])){
			echo $this->element('..'.DS.'Companies'.DS.'account_related');
		}else{ ?>
		<!-- NEU CHUA CO ACCOUNT -->
		<div class="title_block bo_ra1">
			<span class="title_block_inner">
				<h4><?php echo translate('Account related'); ?></h4>
			</span>
			<span class="title_block_inner3 center_txt">
				<input class="btn_pur" type="button" value="Create account" onclick="company_sc_create()">
			</span>
		</div>
		<div class="tab_2_inner">
			<p class="clear">
				<span class="label_1 float_left minw_lab2"><?php echo translate('Account'); ?></span>
				</p><div class="width_in3a float_left indent_input_tp">
					<?php echo $this->Form->input('Salesaccount.status', array(
							'class' => 'input_select',
							'readonly' => true
					)); ?>
				</div>
			<p></p>
			<p class="clear">
				<span class="label_1 float_left minw_lab2"><?php echo translate('Account balance'); ?></span>
				</p><div class="width_in3a float_left indent_input_tp">
					<?php echo $this->Form->input('Salesaccount.balance', array(
							'class' => 'input_1 float_left',
							'readonly' => true
					)); ?>
				</div>
			<p></p>
			<p class="clear">
				<span class="label_1 float_left minw_lab2"><?php echo translate('Credit limit'); ?></span>
				</p><div class="width_in3a float_left indent_input_tp">
					<?php echo $this->Form->input('Salesaccount.credit_limit', array(
							'class' => 'input_1 float_left',
							'readonly' => true
					)); ?>
				</div>
			<p></p>
			<p class="clear">
				<span class="label_1 float_left minw_lab2"><?php echo translate('Difference'); ?></span>
				</p><div class="width_in3a float_left indent_input_tp">
					<?php echo $this->Form->input('Salesaccount.difference', array(
							'class' => 'input_1 float_left',
							'readonly' => true
					)); ?>
					<span class="icon_search_ip float_right" title="Not implemented yet"></span>
				</div>
			<p></p>
			<p class="clear">
				<span class="label_1 float_left minw_lab2"><?php echo translate('Payment terms'); ?></span>
				</p><div class="width_in4 float_left indent_input_tp">
					<div class="once_colum top_se">
						<?php echo $this->Form->input('Salesaccount.payment_terms', array(
							'class' => 'input_select',
							'readonly' => true
						)); ?>
					</div>
					<div class="two_colum">
						<input class="input_1 float_left" type="text" value="days" readonly="true">
						<span class="icon_search_ip float_right" title="Not implemented yet"></span>
					</div>
				</div>
			<p></p>
			<p class="clear">
				<span class="label_1 float_left minw_lab2"><?php echo translate('Default Tax code'); ?></span>
				</p><div class="width_in4 float_left indent_input_tp">
					<div class="once_colum top_se">
						<?php echo $this->Form->input('Salesaccount.tax_code', array(
							'class' => 'input_select',
							'readonly' => true
						)); ?>
					</div>
					<div class="two_colum">
						<input class="input_1 float_left" type="text" readonly="true">
					</div>
				</div>
			<p></p>
			<p class="clear">
				<span class="label_1 float_left minw_lab2"><?php echo translate('Default nominal code'); ?></span>
				</p><div class="width_in4 float_left indent_input_tp">
					<div class="once_colum top_se">
						<?php echo $this->Form->input('Salesaccount.nominal_code', array(
							'class' => 'input_select',
							'readonly' => true
						)); ?>
					</div>
					<div class="two_colum">
						<input class="input_1 float_left" type="text" readonly="true">
					</div>
				</div>
			<p></p>
			<p class="clear">
				<span class="label_1 float_left minw_lab2 fixbor3" style="padding-bottom: 6px;"><?php echo translate('Tax no'); ?></span>
				</p><div class="width_in3a float_left indent_input_tp">
					<?php echo $this->Form->input('Salesaccount.tax_no', array(
							'class' => 'input_select',
							'readonly' => true,
							'style' => 'width: 99%;'
					)); ?>
				</div>
			<p></p>
			<p class="clear">
				<span class="label_1 float_left minw_lab2 fixbor3" style="padding-bottom: 6px;"><?php echo translate('Quotation limit'); ?></span>
				</p><div class="width_in3a float_left indent_input_tp">
					<?php echo $this->Form->input('Salesaccount.quotation_limit', array(
							'class' => 'input_select',
							'readonly' => true,
							'style' => 'width: 99%;'
					)); ?>
				</div>
			<p></p>
			<p class="clear"></p>
		</div>
		<span class="title_block bo_ra2"></span>
		<?php } ?>

	</div><!--END Tab1 -->
</div>

<script type="text/javascript">
	$(function(){

		<?php if(isset($this->data['Salesaccount'])){?>
			company_sc_auto_save();
		<?php }?>

		$('#bt_add_sales_invoice_this_company').click(function(){
			var ids = $("#mongo_id").val();
			$.ajax({
				url:"<?php echo URL;?>/companies/salesinvoice_add/" + ids,
				timeout: 15000,
				success: function(html){
					location.replace(html);
					reload_subtab('account');
				}
			});
		});
		$(".del_invoices").click(function(){
			var orgirin_id = $(this).attr("id");
			confirms("Message","Are you sure you want to delete?",
				function(){
					id = orgirin_id.split("_");
					id = id[id.length -1];
					$.ajax({
						url : "<?php echo URL.'/companies/invoice_delete/' ?>",
						type: "POST",
						data: {id : id},
						success: function(result){
							if(result == "ok")
								$("#listbox_sales_invoice_this_company_"+id).fadeOut('400', function() {
									$("li#account").click();
								});
							else
								alerts("Message",result);
						}
					})
				}, function(){
					return false;
				});
		})
	})

	function company_sc_auto_save(){
		var ids = $("#mongo_id").val();
		$(":input", "#account_related").change(function(){
			$.ajax({
				url: "<?php echo URL; ?>/companies/account_auto_save/" + ids,
				type: 'post',
				data: $(":input", "#account_related").serialize(),
				success: function(html){
					if(html == 'ok'){
						$("#account",".ul_tab").click();
					}
					/*$("#company_sc_related").html(html);
					company_sc_auto_save();*/
				}
			});
		});
	}

	function company_sc_create(){
		var ids = $("#mongo_id").val();
		$.ajax({
			url: "<?php echo URL; ?>/companies/account_create/" + ids,
			type: 'post',
			data: $(":input", "#account_related").serialize(),
			success: function(html){
				if(html == 'ok'){
					$("#account",".ul_tab").click();
				}
				/*$("#company_sc_related").html(html);
				company_sc_auto_save();*/
			}
		});
	}
</script>