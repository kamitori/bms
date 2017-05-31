<div class="title_block bo_ra1">
	<span class="title_block_inner">
		<h4><?php echo translate('Account related'); ?></h4>
	</span>
	<span class="title_block_inner3 center_txt">
		<?php if(isset($this->data['Salesaccount'])){ ?>
		<input class="btn_pur" type="button" value="View account" onclick="window.location='<?php echo URL; ?>/salesaccounts/entry/<?php echo $this->data['Salesaccount']['_id']; ?>'">
		<?php } ?>
	</span>
</div>
<div class="tab_2_inner" id="input_sc_disabled">
	<p class="clear">
		<span class="label_1 float_left minw_lab2"><?php echo translate('Account'); ?></span>
		</p><div class="width_in3a float_left indent_input_tp">
			<?php echo $this->Form->input('Salesaccount.status', array(
					'class' => 'input_select',
			)); ?>
			<?php echo $this->Form->hidden('Salesaccount.status_id'); ?>
			<script type="text/javascript">
				$(function () {
					$("#SalesaccountStatus").combobox(<?php echo json_encode($arr_status); ?>);
				});
			</script>
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
			)); ?>
		</div>
	<p></p>
	<p class="clear">
		<span class="label_1 float_left minw_lab2"><?php echo translate('Difference'); ?></span>
		</p><div class="width_in3a float_left indent_input_tp">
			<?php echo $this->Form->input('Salesaccount.difference', array(
					'class' => 'input_1 float_left',
					'style' => (($this->data['Salesaccount']['difference'] < 0)?'color:red':''),
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
				)); ?>
				<?php echo $this->Form->hidden('Salesaccount.payment_terms_id'); ?>
				<script type="text/javascript">
					$(function () {
						$("#SalesaccountPaymentTerms").combobox(<?php echo json_encode($arr_payment_terms); ?>);
					});
				</script>
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
				)); ?>
				<?php echo $this->Form->hidden('Salesaccount.tax_code_id'); ?>
				<script type="text/javascript">
					$(function () {
						$("#SalesaccountTaxCode").combobox(<?php echo json_encode($arr_tax_code); ?>);
					});
				</script>
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
				)); ?>
				<?php echo $this->Form->hidden('Salesaccount.nominal_code_id'); ?>
				<script type="text/javascript">
					$(function () {
						$("#SalesaccountNominalCode").combobox(<?php echo json_encode($arr_nominal_code); ?>);
					});
				</script>
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
					'style' => 'width: 99%;'
			)); ?>
		</div>
	<p></p>
	<p class="clear">
				<span class="label_1 float_left minw_lab2 fixbor3" style="padding-bottom: 6px;"><?php echo translate('Quotation limit'); ?></span>
				</p><div class="width_in3a float_left indent_input_tp">
					<?php echo $this->Form->input('Salesaccount.quotation_limit', array(
							'class' => 'input_select',
							'style' => 'width: 99%;'
					)); ?>
				</div>
			<p></p>
	<p class="clear"></p>
</div>
<span class="title_block bo_ra2"></span>