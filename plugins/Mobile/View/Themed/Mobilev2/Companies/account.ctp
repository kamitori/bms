<div id="account_related">
<div id="company_sc_related">
<form class="<?php echo $controller; ?>_form_auto_save" id="form_company_<?php echo $company_id; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $company_id; ?>" />
    <?php echo $this->Form->hidden('Comapny._id', array('value' => $company_id)); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="Account"><?php echo __('Account'); ?></label>
			<?php echo $this->Form->input('Salesaccount.status', array(
	            	'type'=>'select',
					'options' => $salesaccounts_status,
			)); ?>
			<?php echo $this->Form->input('Salesaccount.status_id', array(
				'type' => 'hidden',
			)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="AccountBalance"><?php echo __('Account balance'); ?></label>
        <?php echo $this->Form->input('Salesaccount.balance', array(

		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CreaditLimit"><?php echo __('Credit limit'); ?></label>
        <?php echo $this->Form->input('Salesaccount.credit_limit', array(

		)); ?>
    </div>

     <div class="ui-field-contain">
        <label class="field-title" for="Difference"><?php echo __('Difference'); ?></label>
        <?php echo $this->Form->input('Salesaccount.difference', array(

		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="PaymentTerms"><?php echo __('Payment terms'); ?></label>
			<?php echo $this->Form->input('Salesaccount.payment_terms', array(
	            	'type'=>'select',
					'options' => $salesaccounts_payment_terms,
			)); ?>
			<?php echo $this->Form->input('Salesaccount.payment_terms_id', array(
				'type' => 'hidden',
			)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DefaultTaxCode"><?php echo __('Default Tax code'); ?></label>
			<?php echo $this->Form->input('Salesaccount.tax_code', array(
	            	'type'=>'select',
					'options' => $arr_tax_code,
			)); ?>
			<?php echo $this->Form->input('Salesaccount.tax_code_id', array(
				'type' => 'hidden',
			)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DefaultNomialCode"><?php echo __('Default nominal code'); ?></label>
			<?php echo $this->Form->input('Salesaccount.nominal_code', array(
	            	'type'=>'select',
					'options' => $arr_nominal_code,
			)); ?>
			<?php echo $this->Form->input('Salesaccount.nominal_code_id', array(
				'type' => 'hidden',
			)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="TaxNo"><?php echo __('Tax no'); ?></label>
        <?php echo $this->Form->input('Salesaccount.tax_no', array(

		)); ?>
    </div>

     <div class="ui-field-contain">
        <label class="field-title" for="QuotationLimit"><?php echo __('Quotation limit'); ?></label>
        <?php echo $this->Form->input('Salesaccount.quotation_limit', array(

		)); ?>
    </div>


<!--     <div class="ui-field-contain">
    	<label class="field-title" for="QuotationLimit"></label>
    	<input class="btn_pur" type="button" value="Create account" onclick="company_sc_create()">
    </div> -->

</form>
</div>
</div>

<script type="text/javascript">
	$("#add-new-record").html('Create Account').unbind('click').click(function(){
		company_sc_create();
	});
	function company_sc_auto_save(){
		var ids = $("#mongoid").val();
		$(":input", "#account_related").change(function(){
			$.ajax({
				url: "<?php echo M_URL; ?>/companies/account_auto_save/" + ids,
				type: 'post',
				data: $(":input", "#account_related").serialize(),
				success: function(html){
					$("#company_sc_related").html(html);
					company_sc_auto_save();
				}
			});
		});
	}

	function company_sc_create(){
		var ids = $("#mongoid").val();
		$.ajax({
			url: "<?php echo M_URL; ?>/companies/account_create/" + ids,
			type: 'post',
			data: $(":input", "#account_related").serialize(),
			success: function(result){
				$("#add-new-record").html('View Account').unbind('click').click(function(){
					window.location.assign("<?php echo M_URL.'/salesaccounts/entry/' ?>"+result);
				});
			}
		});
	}
</script>
