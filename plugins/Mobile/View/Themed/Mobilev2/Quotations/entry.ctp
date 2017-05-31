<form class="<?php echo $controller; ?>_form_auto_save" id="form_quotation_<?php echo $this->data['Quotation']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Quotation']['_id']; ?>" />
    <?php echo $this->Form->hidden('Quotation._id', array('value' => (string)$this->data['Quotation']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="QuotationCode"><?php echo __('Quotation code'); ?></label>
        <?php echo $this->Form->input('Quotation.code', array(
				'readonly' 	=> 'true',
				'class' 	=> 'quotationField'
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="QuotationType"><?php echo __('Type'); ?></label>
        <?php echo $this->Form->input('Quotation.quotation_type', array(
            	'type'=>'select',
				'options' => $arr_quotations_type,
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="QuotationCompanyName"><?php echo __('Company'); ?></label>
		<?php
				echo $this->Form->input('Quotation.company_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'companies',
						'data-popup-key' => 'company_name'
				));
				echo $this->Form->input('Quotation.company_id', array(
		           		'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="QuotationContactName"><?php echo __('Contact'); ?></label>
		<?php
				echo $this->Form->input('Quotation.contact_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'contact_name',
                        'data-popup-param' => '?is_customer=1'
				));
				echo $this->Form->input('Quotation.contact_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Phone"><?php echo __('Phone'); ?></label>
        <?php echo $this->Form->input('Quotation.phone', array(
				'class' 	=> 'quotationField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Email"><?php echo __('Email'); ?></label>
        <?php echo $this->Form->input('Quotation.email', array(
				'class' 	=> 'quotationField',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="Date"><?php echo __('Date'); ?></label>
        <?php
	        /*echo $this->Form->input('Quotation.quotation_date', array(
					'class' 	=> 'quotationField',
			)); */
		?>
		<div>
			<div class="ui-block-a" style="width: 100%">
	        	<input type="text" name="data[Quotation][quotation_date]" class="date-picker" id="quotation_date_<?php echo $this->data['Quotation']['_id'] ?>" readonly value="<?php echo $this->data['Quotation']['quotation_date']  ?>"/>
	    	</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="QuotationOurRep"><?php echo __('Our rep'); ?></label>
		<?php
				echo $this->Form->input('Quotation.our_rep', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'our_rep',
                        'data-popup-param' => '?is_employee=1'
				));
				echo $this->Form->input('Quotation.our_rep_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="QuotationOurCsr"><?php echo __('Our csr'); ?></label>
		<?php
				echo $this->Form->input('Quotation.our_csr', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'our_csr',
                        'data-popup-param' => '?is_employee=1'
				));
				echo $this->Form->input('Quotation.our_csr_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="QuotationTownCity"><?php echo __('Invoice address'); ?></label>
        <div data-role="collapsible">
            <h1>Address Detail</h1>
            <ul data-role="listview" data-theme="a">
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Invoice address</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Quotation.invoice_address.0.invoice_address_1', array(
                                'value' => isset($this->data['Quotation']['invoice_address']['0']['invoice_address_1'])?$this->data['Quotation']['invoice_address']['0']['invoice_address_1']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label></label></div> <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Quotation.invoice_address.0.invoice_address_2', array(
                                'value' => isset($this->data['Quotation']['invoice_address']['0']['invoice_address_2'])?$this->data['Quotation']['invoice_address']['0']['invoice_address_2']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label></label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Quotation.invoice_address.0.invoice_address_3', array(
                                'value' => isset($this->data['Quotation']['invoice_address']['0']['invoice_address_3'])?$this->data['Quotation']['invoice_address']['0']['invoice_address_3']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Town / City</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Quotation.invoice_address.0.invoice_town_city', array(
                                'value' => isset($this->data['Quotation']['invoice_address']['0']['invoice_town_city'])?$this->data['Quotation']['invoice_address']['0']['invoice_town_city']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Province / State</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            echo $this->Form->input('Quotation.invoice_address.0.invoice_province_state_id', array(
                                    'value' => isset($this->data['Quotation']['invoice_address']['0']['invoice_province_state_id'])?$this->data['Quotation']['invoice_address']['0']['invoice_province_state_id']:'',
                                    'options' => $options['provinces'][$this->data['Quotation']['invoice_address']['0']['invoice_country_id']],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Zip / Post code</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Quotation.invoice_address.0.invoice_zip_postcode', array(
                                'value' => isset($this->data['Quotation']['invoice_address']['0']['invoice_zip_postcode'])?$this->data['Quotation']['invoice_address']['0']['invoice_zip_postcode']:'',
                                //'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Country</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Quotation.invoice_address.0.invoice_country_id', array(
                                'options' => $options['countries'],
                                'value' => isset($this->data['Quotation']['invoice_address']['0']['invoice_country_id'])?$this->data['Quotation']['invoice_address']['0']['invoice_country_id']:'',
                                'readonly'  => 'true',
                                'onchange'  => 'change_pro()'
                        )); ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
	
    <div class="ui-field-contain">
        <label class="field-title" for="QuotationTownCity"><?php echo __('Ship to'); ?></label>
        <div data-role="collapsible">
            <h1>Address Detail</h1>
            <ul data-role="listview" data-theme="a">
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Ship to</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Quotation.shipping_address.0.shipping_contact_name', array(
                                'value' => isset($this->data['Quotation']['shipping_address']['0']['shipping_contact_name'])?$this->data['Quotation']['shipping_address']['0']['shipping_contact_name']:'',
                        )); ?>
                    </div>
                </li>

                <li>
                    <div class="ui-block-a" style="width:30%"><label>Shipping address</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Quotation.shipping_address.0.shipping_address_1', array(
                                'value' => isset($this->data['Quotation']['shipping_address']['0']['shipping_address_1'])?$this->data['Quotation']['shipping_address']['0']['shipping_address_1']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>(if different)</label></div> <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Quotation.shipping_address.0.shipping_address_2', array(
                                'value' => isset($this->data['Quotation']['shipping_address']['0']['shipping_address_2'])?$this->data['Quotation']['shipping_address']['0']['shipping_address_2']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label></label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Quotation.shipping_address.0.shipping_address_3', array(
                                'value' => isset($this->data['Quotation']['shipping_address']['0']['shipping_address_3'])?$this->data['Quotation']['shipping_address']['0']['shipping_address_3']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Town / City</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Quotation.shipping_address.0.shipping_town_city', array(
                                'value' => isset($this->data['Quotation']['shipping_address']['0']['shipping_town_city'])?$this->data['Quotation']['shipping_address']['0']['shipping_town_city']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Province / State</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            echo $this->Form->input('Quotation.shipping_address.0.shipping_province_state_id', array(
                                    'value' => isset($this->data['Quotation']['shipping_address']['0']['shipping_province_state_id'])?$this->data['Quotation']['shipping_address']['0']['shipping_province_state_id']:'',
                                    'options' => $options['provinces'][$this->data['Quotation']['shipping_address']['0']['shipping_country_id']],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Zip / Post code</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Quotation.shipping_address.0.shipping_zip_postcode', array(
                                'value' => isset($this->data['Quotation']['shipping_address']['0']['shipping_zip_postcode'])?$this->data['Quotation']['shipping_address']['0']['shipping_zip_postcode']:'',
                                
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Country</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Quotation.shipping_address.0.shipping_country_id', array(
                                'options' => $options['countries'],
                                'value' => isset($this->data['Quotation']['shipping_address']['0']['shipping_country_id'])?$this->data['Quotation']['shipping_address']['0']['shipping_country_id']:'',
                                'readonly'  => 'true',
                                'onchange'  => 'change_pro()'
                        )); ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>    


    <div class="ui-field-contain">
        <label class="field-title" for="DueDate"><?php echo __('Due Date'); ?></label>
        <?php
	        /*echo $this->Form->input('Quotation.payment_due_date', array(
					'class' 	=> 'quotationField',
			)); */
		?>
		<div>
			<div class="ui-block-a" style="width: 100%">
	        	<input type="text" name="data[Quotation][payment_due_date]" class="date-picker" id="payment_due_date_<?php echo $this->data['Quotation']['_id'] ?>" readonly value="<?php echo $this->data['Quotation']['payment_due_date']  ?>"/>
	    	</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="QuotationStatus"><?php echo __('Status'); ?></label>
        <?php echo $this->Form->input('Quotation.quotation_status', array(
            	'type'=>'select',
				'options' => $arr_quotations_status,
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="QuotationPaymentTerms"><?php echo __('Payment Terms'); ?></label>
        <?php echo $this->Form->input('Quotation.payment_terms', array(
            	'type'=>'select',
				'options' => $arr_quotations_payment_terms,
		)); ?>
		<?php echo $this->Form->input('Quotation.payment_terms_id', array(
			'type' => 'hidden',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="QuotationTax"><?php echo __('Tax %'); ?></label>
        <?php echo $this->Form->input('Quotation.tax', array(
                'type'=>'select',
                'options' => $arr_quotations_tax,
        )); ?>
        <?php echo $this->Form->input('Quotation.taxval', array(
                'type' => 'hidden',
        )); ?>
        <?php echo $this->Form->input('Quotation.sum_tax', array(
                'type' => 'hidden',
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CustomerPoNo"><?php echo __('Customer PO No'); ?></label>
        <?php echo $this->Form->input('Quotation.customer_po_no', array(
				'class' 	=> 'quotationField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="heading"><?php echo __('Heading'); ?></label>
        <?php echo $this->Form->input('Quotation.heading', array(
				'class' 	=> 'quotationField',
		)); ?>
    </div>

   <div class="ui-field-contain">
        <label class="field-title" for="QuotationJob"><?php echo __('Job'); ?></label>
        <div>
            <div class="ui-block-a" style="width: 40%">
                <?php 
                    echo $this->Form->input('Quotation.job_number', array(
                            'readonly' => true,
                    )); ?>
        		
            </div>  
            <div class="ui-block-b" style="width: 60%">
               <?php
                    echo $this->Form->input('Quotation.job_name', array(
                            'readonly' => true,
                            'class' => 'popup-input',
                            'data-popup-controller' => 'jobs',
                            'data-popup-key' => 'job_name'
                    ));
                    echo $this->Form->input('Quotation.job_id', array(
                            'type'=>'hidden'
                    ));
                ?>
            </div>  
        </div>    
    </div>


   <div class="ui-field-contain">
        <label class="field-title" for="QuotationSalesorderName"><?php echo __('Sales Order'); ?></label>
        <div>
            <div class="ui-block-a" style="width: 40%">
                <?php 
                    echo $this->Form->input('Quotation.salesorder_number', array(
                            'readonly' => true,
                )); ?>
                
            </div>
            <div class="ui-block-b" style="width: 60%">
        		<?php
    				echo $this->Form->input('Quotation.salesorder_name', array(
    						'readonly' => true,
    						'class' => 'popup-input',
    						'data-popup-controller' => 'salesorders',
    						'data-popup-key' => 'salesorder_name'
    				));
    				echo $this->Form->input('Quotation.salesorder_id', array(
    		           	     'type'=>'hidden'
    		        ));
        		?>
            </div>  
        </div>  
    </div>





	<?php echo $this->Form->input('Quotation.quotation_id', array(
	   'type'=>'hidden'
	)); ?>
</form>
<?php echo $this->element('../Quotations/js'); ?>