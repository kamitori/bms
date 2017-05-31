<style type="text/css">
.error_input{
  border: 1px solid red !important;
}
</style>
<form class="<?php echo $controller; ?>_form_auto_save" id="form_salesinvoice_<?php echo $this->data['Salesinvoice']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Salesinvoice']['_id']; ?>" />
    <?php echo $this->Form->hidden('Salesinvoice._id', array('value' => (string)$this->data['Salesinvoice']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="SalesinvoiceCode"><?php echo __('Ref no'); ?></label>
        <?php echo $this->Form->input('Salesinvoice.code', array(
				'readonly' 	=> 'true',
				'class' 	=> 'salesinvoiceField'
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="SalesinvoiceInvoiceType"><?php echo __('Type'); ?></label>
        <?php echo $this->Form->input('Salesinvoice.invoice_type', array(
            	'type'=>'select',
				'options' => $arr_salesinvoices_type,
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesinvoiceCompanyName"><?php echo __('Company'); ?></label>
		<?php
				echo $this->Form->input('Salesinvoice.company_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'companies',
						'data-popup-key' => 'company_name'
				));
				echo $this->Form->input('Salesinvoice.company_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesinvoiceContactName"><?php echo __('Contact'); ?></label>
		<?php
            if(isset($this->data['Salesinvoice']['company_id']) && $this->data['Salesinvoice']['company_id']!='' )
				echo $this->Form->input('Salesinvoice.contact_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'contact_name',
                        'data-popup-param' => '?company_id='.$this->data['Salesinvoice']['company_id'].'&company_name='.$this->data['Salesinvoice']['company_name'].'&is_customer=1',
				));
            else
                echo $this->Form->input('Salesinvoice.contact_name', array(
                        'readonly' => true,
                        'class' => 'popup-input',
                        'data-popup-controller' => 'contacts',
                        'data-popup-key' => 'contact_name',
                        'data-popup-param' => '?is_customer=1',
                ));

			echo $this->Form->input('Salesinvoice.contact_id', array(
	           	'type'=>'hidden'
	        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Phone"><?php echo __('Phone'); ?></label>
        <?php echo $this->Form->input('Salesinvoice.phone', array(
				'class' 	=> 'salesinvoiceField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Email"><?php echo __('Email'); ?></label>
        <?php echo $this->Form->input('Salesinvoice.email', array(
				'class' 	=> 'salesinvoiceField',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="Date"><?php echo __('Date'); ?></label>
        <?php
	        /*echo $this->Form->input('Salesinvoice.invoice_date', array(
					'class' 	=> 'salesinvoiceField',
			)); */
		?>
		<div>
			<div class="ui-block-a" style="width: 100%">
	        	<input type="text" name="data[Salesinvoice][invoice_date]" class="date-picker" id="invoice_date_<?php echo $this->data['Salesinvoice']['_id'] ?>" readonly value="<?php echo $this->data['Salesinvoice']['invoice_date']  ?>"/>
	    	</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesinvoiceOurRep"><?php echo __('Our rep'); ?></label>
		<?php
				echo $this->Form->input('Salesinvoice.our_rep', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'our_rep',
                        'data-popup-param' => '?is_employee=1'
				));
				echo $this->Form->input('Salesinvoice.our_rep_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="SalesinvoiceOurCsr"><?php echo __('Our csr'); ?></label>
		<?php
				echo $this->Form->input('Salesinvoice.our_csr', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'our_csr',
                        'data-popup-param' => '?is_employee=1'
				));
				echo $this->Form->input('Salesinvoice.our_csr_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesinvoiceTownCity"><?php echo __('Invoice address'); ?></label>
        <div data-role="collapsible">
            <h1>Address Detail</h1>
            <ul data-role="listview" data-theme="a">
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Invoice address</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesinvoice.invoice_address.0.invoice_address_1', array(
                                'value' => isset($this->data['Salesinvoice']['invoice_address']['0']['invoice_address_1'])?$this->data['Salesinvoice']['invoice_address']['0']['invoice_address_1']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label></label></div> <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesinvoice.invoice_address.0.invoice_address_2', array(
                                'value' => isset($this->data['Salesinvoice']['invoice_address']['0']['invoice_address_2'])?$this->data['Salesinvoice']['invoice_address']['0']['invoice_address_2']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label></label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesinvoice.invoice_address.0.invoice_address_3', array(
                                'value' => isset($this->data['Salesinvoice']['invoice_address']['0']['invoice_address_3'])?$this->data['Salesinvoice']['invoice_address']['0']['invoice_address_3']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Town / City</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesinvoice.invoice_address.0.invoice_town_city', array(
                                'value' => isset($this->data['Salesinvoice']['invoice_address']['0']['invoice_town_city'])?$this->data['Salesinvoice']['invoice_address']['0']['invoice_town_city']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Province / State</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            if (!isset($this->data['Salesinvoice']['invoice_address']['0']['invoice_country_id']))
 	                            echo $this->Form->input('Salesinvoice.invoice_address.0.invoice_province_state_id', array(
                                    'value' => isset($this->data['Salesinvoice']['invoice_address']['0']['invoice_province_state_id'])?$this->data['Salesinvoice']['invoice_address']['0']['invoice_province_state_id']:'',
                                    'options' => $options['provinces']['CA'],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
 	                        else
                            	echo $this->Form->input('Salesinvoice.invoice_address.0.invoice_province_state_id', array(
                                    'value' => isset($this->data['Salesinvoice']['invoice_address']['0']['invoice_province_state_id'])?$this->data['Salesinvoice']['invoice_address']['0']['invoice_province_state_id']:'',
                                    'options' => $options['provinces'][$this->data['Salesinvoice']['invoice_address']['0']['invoice_country_id']],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Zip / Post code</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesinvoice.invoice_address.0.invoice_zip_postcode', array(
                                'value' => isset($this->data['Salesinvoice']['invoice_address']['0']['invoice_zip_postcode'])?$this->data['Salesinvoice']['invoice_address']['0']['invoice_zip_postcode']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Country</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php 
                        echo $this->Form->input('Salesinvoice.invoice_address.0.invoice_country_id', array(
                                'options' => $options['countries'],
                                'value' => isset($this->data['Salesinvoice']['invoice_address']['0']['invoice_country_id'])?$this->data['Salesinvoice']['invoice_address']['0']['invoice_country_id']:'CA',
                                'readonly'  => 'true',
                                'onchange'  => 'change_pro()'
                        )); 
                        ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="ui-field-contain">
        <label class="field-title" for="SalesinvoiceTownCity"><?php echo __('Ship to'); ?></label>
        <div data-role="collapsible">
            <h1>Address Detail</h1>
            <ul data-role="listview" data-theme="a">
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Ship to</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesinvoice.shipping_address.0.shipping_contact_name', array(
                                'value' => isset($this->data['Salesinvoice']['shipping_address']['0']['shipping_contact_name'])?$this->data['Salesinvoice']['shipping_address']['0']['shipping_contact_name']:'',
                        )); ?>
                    </div>
                </li>

                <li>
                    <div class="ui-block-a" style="width:30%"><label>Shipping address</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesinvoice.shipping_address.0.shipping_address_1', array(
                                'value' => isset($this->data['Salesinvoice']['shipping_address']['0']['shipping_address_1'])?$this->data['Salesinvoice']['shipping_address']['0']['shipping_address_1']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>(if different)</label></div> <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesinvoice.shipping_address.0.shipping_address_2', array(
                                'value' => isset($this->data['Salesinvoice']['shipping_address']['0']['shipping_address_2'])?$this->data['Salesinvoice']['shipping_address']['0']['shipping_address_2']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label></label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesinvoice.shipping_address.0.shipping_address_3', array(
                                'value' => isset($this->data['Salesinvoice']['shipping_address']['0']['shipping_address_3'])?$this->data['Salesinvoice']['shipping_address']['0']['shipping_address_3']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Town / City</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesinvoice.shipping_address.0.shipping_town_city', array(
                                'value' => isset($this->data['Salesinvoice']['shipping_address']['0']['shipping_town_city'])?$this->data['Salesinvoice']['shipping_address']['0']['shipping_town_city']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Province / State</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            if (!isset($this->data['Salesinvoice']['shipping_address']['0']['shipping_country_id']))
                               	echo $this->Form->input('Salesinvoice.shipping_address.0.shipping_province_state_id', array(
                                    'value' => isset($this->data['Salesinvoice']['shipping_address']['0']['shipping_province_state_id'])?$this->data['Salesinvoice']['shipping_address']['0']['shipping_province_state_id']:'',
                                    'options' => $options['provinces']['CA'],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                            else   
                            	echo $this->Form->input('Salesinvoice.shipping_address.0.shipping_province_state_id', array(
                                    'value' => isset($this->data['Salesinvoice']['shipping_address']['0']['shipping_province_state_id'])?$this->data['Salesinvoice']['shipping_address']['0']['shipping_province_state_id']:'',
                                    'options' => $options['provinces'][$this->data['Salesinvoice']['shipping_address']['0']['shipping_country_id']],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Zip / Post code</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesinvoice.shipping_address.0.shipping_zip_postcode', array(
                                'value' => isset($this->data['Salesinvoice']['shipping_address']['0']['shipping_zip_postcode'])?$this->data['Salesinvoice']['shipping_address']['0']['shipping_zip_postcode']:'',
                                
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Country</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesinvoice.shipping_address.0.shipping_country_id', array(
                                'options' => $options['countries'],
                                'value' => isset($this->data['Salesinvoice']['shipping_address']['0']['shipping_country_id'])?$this->data['Salesinvoice']['shipping_address']['0']['shipping_country_id']:'CA',
                                'readonly'  => 'true',
                                'onchange'  => 'change_pro()'
                        )); ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesinvoiceInvoiceStatus"><?php echo __('Status'); ?></label>
        <?php echo $this->Form->input('Salesinvoice.invoice_status', array(
            	'type'=>'select',
				'options' => $arr_salesinvoices_status,
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesinvoicePaymentTerms"><?php echo __('Payment Terms'); ?></label>
        <?php echo $this->Form->input('Salesinvoice.payment_terms', array(
            	'type'=>'select',
				'options' => $arr_salesinvoices_payment_terms,
		)); ?>
		<?php echo $this->Form->input('Salesinvoice.payment_terms_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DueDate"><?php echo __('Paid'); ?></label>
		<div>
			<div class="ui-block-a" style="width: 100%">
	        	<input type="text" name="data[Salesinvoice][paid_date]" class="date-picker" id="paid_date<?php echo $this->data['Salesinvoice']['_id'] ?>" readonly value="<?php echo $this->data['Salesinvoice']['paid_date']  ?>"/>
	    	</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Paymentduedate"><?php echo __('Payment due date'); ?></label>
        <?php
	        /*echo $this->Form->input('Salesinvoice.payment_due_date', array(
					'class' 	=> 'salesinvoiceField',
			)); */
		?>
		<div>
			<div class="ui-block-a" style="width: 100%">
	        	<input type="text" name="data[Salesinvoice][payment_due_date]" class="date-picker" id="payment_due_date_<?php echo $this->data['Salesinvoice']['_id'] ?>" readonly value="<?php echo $this->data['Salesinvoice']['payment_due_date']  ?>"/>
	    	</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesinvoiceTax"><?php echo __('Tax %'); ?></label>
        <?php
	        echo $this->Form->input('Salesinvoice.tax', array(
	            	'type'=>'select',
					'options' => $arr_salesinvoices_taxtext,
			));
			echo $this->Form->input('Salesinvoice.taxval', array(
	           		'type'=>'hidden'
	        ));
		 ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CustomerPoNo"><?php echo __('Customer PO No'); ?></label>
        <?php echo $this->Form->input('Salesinvoice.customer_po_no', array(
				'class' 	=> 'salesinvoiceField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="heading"><?php echo __('Heading'); ?></label>
        <?php echo $this->Form->input('Salesinvoice.heading', array(
				'class' 	=> 'salesinvoiceField',
		)); ?>
    </div>


   <div class="ui-field-contain">
        <label class="field-title" for="SalesinvoiceJob"><?php echo __('Job'); ?></label>
        <div>
            <div class="ui-block-a" style="width: 40%">
                <?php 
                    echo $this->Form->input('Salesinvoice.job_number', array(
                            'readonly' => true,
                    )); ?>
            </div>

            <div class="ui-block-b" style="width: 60%">
            <?php
                    echo $this->Form->input('Salesinvoice.job_name', array(
                            'readonly' => true,
                            'class' => 'popup-input',
                            'data-popup-controller' => 'Jobs',
                            'data-popup-key' => 'job_name'
                    ));
                    echo $this->Form->input('Salesinvoice.job_id', array(
                        'type'=>'hidden'
                    ));
            ?>
            </div>  
        </div>    
    </div>


   <div class="ui-field-contain">
        <label class="field-title" for="SalesinvoiceSalesorderName"><?php echo __('Sales Order'); ?></label>
        <div>
            <div class="ui-block-a" style="width: 40%">
                <?php 
                    echo $this->Form->input('Salesinvoice.salesorder_number', array(
                            'readonly' => true,
                    )); ?>
                
            </div>
            <div class="ui-block-b" style="width: 60%">
                <?php
                    echo $this->Form->input('Salesinvoice.salesorder_name', array(
                            'readonly' => true,
                            'class' => 'popup-input',
                            'data-popup-controller' => 'salesorders',
                            'data-popup-key' => 'salesorder_name'
                    ));
                    echo $this->Form->input('Salesinvoice.salesorder_id', array(
                             'type'=>'hidden'
                    ));
                ?>
            </div>  
        </div>  
    </div>




	<?php echo $this->Form->input('Salesinvoice.name', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Salesinvoice.salesinvoice_id', array(
	   'type'=>'hidden'
	)); ?>

</form>
<?php echo $this->element('../Salesinvoices/js'); ?>