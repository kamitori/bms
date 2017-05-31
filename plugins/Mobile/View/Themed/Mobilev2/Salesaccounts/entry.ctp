<form class="<?php echo $controller; ?>_form_auto_save" id="form_salesaccount_<?php echo $this->data['Salesaccount']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Salesaccount']['_id']; ?>" />
    <?php echo $this->Form->hidden('Salesaccount._id', array('value' => (string)$this->data['Salesaccount']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="SalesaccountNo"><?php echo __('Salesaccount no'); ?></label>
        <?php echo $this->Form->input('Salesaccount.no', array(
				'readonly' 	=> 'true',
				'class' 	=> 'salesaccountField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountInvoiceType"><?php echo __('Type'); ?></label>
        <?php echo $this->Form->input('Salesaccount.invoice_type', array(
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountName"><?php echo __('Account name'); ?></label>
		<?php
				echo $this->Form->input('Salesaccount.name', array(
						'readonly' => true,
				));
				echo $this->Form->input('Salesaccount.name_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountStatus"><?php echo __('Account Status'); ?></label>
        <?php echo $this->Form->input('Salesaccount.status', array(
            	'type'=>'select',
				'options' => $arr_salesaccounts_status,
		)); ?>
		<?php echo $this->Form->input('Salesaccount.status_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="phone"><?php echo __('Phone'); ?></label>
        <?php echo $this->Form->input('Salesaccount.phone', array(
				'class' 	=> 'SalesaccountField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountFax"><?php echo __('Fax'); ?></label>
        <?php echo $this->Form->input('Salesaccount.fax', array(
				'class' 	=> 'salesaccountField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountEmail"><?php echo __('Email'); ?></label>
        <?php echo $this->Form->input('Salesaccount.email', array(
				'class' 	=> 'salesaccountField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountContactName"><?php echo __('Account contact'); ?></label>
		<?php
				echo $this->Form->input('Salesaccount.contact_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'contact_name'
				));
				echo $this->Form->input('Salesaccount.contact_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DirectPhone"><?php echo __('Direct detail'); ?></label>
        <?php echo $this->Form->input('Salesaccount.direct_dial', array(
				'class' 	=> 'salesaccountField',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountTownCity"><?php echo __('Address'); ?></label>
        <div data-role="collapsible">
            <h1>Address Detail</h1>
            <ul data-role="listview" data-theme="a">
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 1</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Company.address_1', array(
                                'value' => $this->data['Company']['address_1'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 2</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Company.address_2', array(
                                'value' => $this->data['Company']['address_2'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 3</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Company.address_3', array(
                                'value' => $this->data['Company']['address_3'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Town / City</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Company.town_city', array(
                                'value' => $this->data['Company']['town_city'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Province / State</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            echo $this->Form->input('Company.province_state_id', array(
                                    'value' => $this->data['Company']['province_state_id'],
                                    'options' => $options['provinces'][$this->data['Company']['country_id']],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Zip / Post code</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Company.zip_postcode', array(
                                'value' => $this->data['Company']['zip_postcode'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Country</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Company.country_id', array(
                                'options' => $options['countries'],
                                'value' => $this->data['Company']['country_id'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>



    <div class="ui-field-contain">
        <label class="field-title" for="InvoicesCredits"><?php echo __('Invoices / Credits'); ?></label>
        <?php echo $this->Form->input('Salesaccount.invoices_credits', array(
				'class' 	=> 'SalesaccountField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Receipts"><?php echo __('Receipts'); ?></label>
        <?php echo $this->Form->input('Salesaccount.receipts', array(
				'class' 	=> 'salesaccountField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Balance"><?php echo __('Account balance'); ?></label>
        <?php echo $this->Form->input('Salesaccount.balance', array(
				'class' 	=> 'salesaccountField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CreditLimit"><?php echo __('Credit Limit'); ?></label>
        <?php echo $this->Form->input('Salesaccount.credit_limit', array(
				'class' 	=> 'salesaccountField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Difference"><?php echo __('Difference'); ?></label>
        <?php echo $this->Form->input('Salesaccount.difference', array(
				'class' 	=> 'salesaccountField',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountPaymentTerms"><?php echo __('Payment terms'); ?></label>
        <?php echo $this->Form->input('Salesaccount.payment_terms', array(
            	'type'=>'select',
				'options' => $arr_salesaccounts_payment_terms,
		)); ?>
		<?php echo $this->Form->input('Salesaccount.position_id', array(
			'type' => 'hidden',
		)); ?>
    </div>


    

    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountTaxcode"><?php echo __('Tax code'); ?></label>
        <?php echo $this->Form->input('Salesaccount.tax_code', array(
            	'type'=>'select',
				'options' => $arr_salesaccounts_tax_code,
		)); ?>
		<?php echo $this->Form->input('Salesaccount.tax_code_id', array(
			'type' => 'hidden',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountNominalCode"><?php echo __('Nominal code'); ?></label>
        <?php echo $this->Form->input('Salesaccount.nominal_code', array(
            	'type'=>'select',
				'options' => $arr_salesaccounts_nominal_code,
		)); ?>
		<?php echo $this->Form->input('Salesaccount.nominal_code_id', array(
			'type' => 'hidden',
		)); ?>
    </div>



    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountUsuallyPayBy"><?php echo __('Usually Pay By'); ?></label>
        <?php echo $this->Form->input('Salesaccount.usually_pay_by', array(
				'type'=>'select',
				'options' => $arr_usually_pay_by,
		)); ?>
		<?php echo $this->Form->input('Salesaccount.usually_pay_by_id', array(
				'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountCardType"><?php echo __('Card Type'); ?></label>
        <?php echo $this->Form->input('Salesaccount.card_type', array(
				'type'=>'select',
				'options' => $arr_salesaccounts_card_type,
		)); ?>
		<?php echo $this->Form->input('Salesaccount.card_type_id', array(
				'type' => 'hidden',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountCardNumber"><?php echo __('Card Number'); ?></label>
        <?php echo $this->Form->input('Salesaccount.card_number', array(
				'class' 	=> 'salesaccountField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountExpiresMonth"><?php echo __('Expires: Month'); ?></label>
        <?php echo $this->Form->input('Salesaccount.expires_month', array(
				'class' 	=> 'salesaccountField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountSecurityId"><?php echo __('Security Id'); ?></label>
        <?php echo $this->Form->input('Salesaccount.security', array(
				'class' 	=> 'salesaccountField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountCardHolder"><?php echo __('Card holde'); ?></label>
        <?php echo $this->Form->input('Salesaccount.card_holder', array(
            	'type'=>'select',
				'options' => $arr_salesaccounts_card_holder,
		)); ?>
		<?php echo $this->Form->input('Salesaccount.card_holder_id', array(
				'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesaccountAddress"><?php echo __('Address'); ?></label>
        <?php echo $this->Form->input('Salesaccount.address', array(
				'class' 	=> 'salesaccountField',
		)); ?>
    </div>


</form>

<?php echo $this->element('../Salesaccounts/js'); ?>