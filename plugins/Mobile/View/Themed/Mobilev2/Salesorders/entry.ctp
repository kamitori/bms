<form class="<?php echo $controller; ?>_form_auto_save" id="form_salesorder_<?php echo $this->data['Salesorder']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Salesorder']['_id']; ?>" />
    <?php echo $this->Form->hidden('Salesorder._id', array('value' => (string)$this->data['Salesorder']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="SalesorderCode"><?php echo __('Ref no'); ?></label>
        <?php echo $this->Form->input('Salesorder.code', array(
				'readonly' 	=> 'true',
				'class' 	=> 'salesorderField'
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="SalesOrderType"><?php echo __('SO Type'); ?></label>
        <?php echo $this->Form->input('Salesorder.sales_order_type', array(
            	'type'=>'select',
				'options' => $arr_salesorders_type,
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesorderCompanyName"><?php echo __('Company'); ?></label>
		<?php
				echo $this->Form->input('Salesorder.company_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'companies',
						'data-popup-key' => 'company_name'
				));
				echo $this->Form->input('Salesorder.company_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesorderContactName"><?php echo __('Contact'); ?></label>
		<?php
				echo $this->Form->input('Salesorder.contact_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'contact_name',
                        'data-popup-param' => '?company_id='.$this->data['Salesorder']['company_id'].'&company_name='.$this->data['Salesorder']['company_name'].'&is_customer=1',
				));
				echo $this->Form->input('Salesorder.contact_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Phone"><?php echo __('Phone'); ?></label>
        <?php echo $this->Form->input('Salesorder.phone', array(
				'class' 	=> 'salesorderField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Email"><?php echo __('Email'); ?></label>
        <?php echo $this->Form->input('Salesorder.email', array(
				'class' 	=> 'salesorderField',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="Date"><?php echo __('Order date'); ?></label>
        <?php
	        /*echo $this->Form->input('Salesorder.salesorder_date', array(
					'class' 	=> 'salesorderField',
			)); */
		?>
		<div>
			<div class="ui-block-a" style="width: 100%">
	        	<input type="text" name="data[Salesorder][salesorder_date]" class="date-picker" id="salesorder_date_<?php echo $this->data['Salesorder']['_id'] ?>" readonly value="<?php echo $this->data['Salesorder']['salesorder_date']  ?>"/>
	    	</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="PaymentDueDate"><?php echo __('Due Date'); ?></label>
		<div>
			<div class="ui-block-a" style="width: 100%">
	        	<input type="text" name="data[Salesorder][payment_due_date]" class="date-picker" id="payment_due_date<?php echo $this->data['Salesorder']['_id'] ?>" readonly value="<?php echo $this->data['Salesorder']['payment_due_date']  ?>"/>
	    	</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesorderOurRep"><?php echo __('Our rep'); ?></label>
		<?php
				echo $this->Form->input('Salesorder.our_rep', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'our_rep',
                        'data-popup-param' => '?is_employee=1'
				));
				echo $this->Form->input('Salesorder.our_rep_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="SalesorderOurCsr"><?php echo __('Our csr'); ?></label>
		<?php
				echo $this->Form->input('Salesorder.our_csr', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'our_csr',
                        'data-popup-param' => '?is_employee=1'
				));
				echo $this->Form->input('Salesorder.our_csr_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesorderTownCity"><?php echo __('Invoice address'); ?></label>
        <div data-role="collapsible">
            <h1>Address Detail</h1>
            <ul data-role="listview" data-theme="a">
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Invoice address</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesorder.invoice_address.0.invoice_address_1', array(
                                'value' => isset($this->data['Salesorder']['invoice_address']['0']['invoice_address_1'])?$this->data['Salesorder']['invoice_address']['0']['invoice_address_1']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label></label></div> <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesorder.invoice_address.0.invoice_address_2', array(
                                'value' => isset($this->data['Salesorder']['invoice_address']['0']['invoice_address_2'])?$this->data['Salesorder']['invoice_address']['0']['invoice_address_2']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label></label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesorder.invoice_address.0.invoice_address_3', array(
                                'value' => isset($this->data['Salesorder']['invoice_address']['0']['invoice_address_3'])?$this->data['Salesorder']['invoice_address']['0']['invoice_address_3']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Town / City</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesorder.invoice_address.0.invoice_town_city', array(
                                'value' => isset($this->data['Salesorder']['invoice_address']['0']['invoice_town_city'])?$this->data['Salesorder']['invoice_address']['0']['invoice_town_city']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Province / State</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            if (!isset($this->data['Salesorder']['invoice_address']['0']['invoice_province_state_id']))
                                $this->data['Salesorder']['invoice_address']['0']['invoice_province_state_id'] = 'CA';

                            echo $this->Form->input('Salesorder.invoice_address.0.invoice_province_state_id', array(
                                    'value' => isset($this->data['Salesorder']['invoice_address']['0']['invoice_province_state_id'])?$this->data['Salesorder']['invoice_address']['0']['invoice_province_state_id']:'',
                                    'options' => $options['provinces'][$this->data['Salesorder']['invoice_address']['0']['invoice_country_id']],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Zip / Post code</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesorder.invoice_address.0.invoice_zip_postcode', array(
                                'value' => isset($this->data['Salesorder']['invoice_address']['0']['invoice_zip_postcode'])?$this->data['Salesorder']['invoice_address']['0']['invoice_zip_postcode']:'',
                                //'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Country</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesorder.invoice_address.0.invoice_country_id', array(
                                'options' => $options['countries'],
                                'value' => isset($this->data['Salesorder']['invoice_address']['0']['invoice_country_id'])?$this->data['Salesorder']['invoice_address']['0']['invoice_country_id']:'',
                                'readonly'  => 'true',
                                'onchange'  => 'change_pro()'
                        )); ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="ui-field-contain">
        <label class="field-title" for="SalesorderTownCity"><?php echo __('Ship to'); ?></label>
        <div data-role="collapsible">
            <h1>Address Detail</h1>
            <ul data-role="listview" data-theme="a">
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Ship to</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesorder.shipping_address.0.shipping_contact_name', array(
                                'value' => isset($this->data['Salesorder']['shipping_address']['0']['shipping_contact_name'])?$this->data['Salesorder']['shipping_address']['0']['shipping_contact_name']:'',
                        )); ?>
                    </div>
                </li>

                <li>
                    <div class="ui-block-a" style="width:30%"><label>Shipping address</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesorder.shipping_address.0.shipping_address_1', array(
                                'value' => isset($this->data['Salesorder']['shipping_address']['0']['shipping_address_1'])?$this->data['Salesorder']['shipping_address']['0']['shipping_address_1']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>(if different)</label></div> <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesorder.shipping_address.0.shipping_address_2', array(
                                'value' => isset($this->data['Salesorder']['shipping_address']['0']['shipping_address_2'])?$this->data['Salesorder']['shipping_address']['0']['shipping_address_2']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label></label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesorder.shipping_address.0.shipping_address_3', array(
                                'value' => isset($this->data['Salesorder']['shipping_address']['0']['shipping_address_3'])?$this->data['Salesorder']['shipping_address']['0']['shipping_address_3']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Town / City</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesorder.shipping_address.0.shipping_town_city', array(
                                'value' => isset($this->data['Salesorder']['shipping_address']['0']['shipping_town_city'])?$this->data['Salesorder']['shipping_address']['0']['shipping_town_city']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Province / State</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            if (!isset($this->data['Salesorder']['shipping_address']['0']['shipping_country_id']))
                                $this->data['Salesorder']['shipping_address']['0']['shipping_country_id'] = 'CA';
                            echo $this->Form->input('Salesorder.shipping_address.0.shipping_province_state_id', array(
                                    'value' => isset($this->data['Salesorder']['shipping_address']['0']['shipping_province_state_id'])?$this->data['Salesorder']['shipping_address']['0']['shipping_province_state_id']:'',
                                    'options' => $options['provinces'][$this->data['Salesorder']['shipping_address']['0']['shipping_country_id']],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Zip / Post code</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesorder.shipping_address.0.shipping_zip_postcode', array(
                                'value' => isset($this->data['Salesorder']['shipping_address']['0']['shipping_zip_postcode'])?$this->data['Salesorder']['shipping_address']['0']['shipping_zip_postcode']:'',
                                
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Country</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Salesorder.shipping_address.0.shipping_country_id', array(
                                'options' => $options['countries'],
                                'value' => isset($this->data['Salesorder']['shipping_address']['0']['shipping_country_id'])?$this->data['Salesorder']['shipping_address']['0']['shipping_country_id']:'',
                                'readonly'  => 'true',
                                'onchange'  => 'change_pro()'
                        )); ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>  
    
    <div class="ui-field-contain">
        <label class="field-title" for="SalesorderStatus"><?php echo __('Status'); ?></label>
        <?php echo $this->Form->input('Salesorder.status', array(
            	'type'=>'select',
				'options' => $arr_salesorders_status,
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesorderPaymentTerms"><?php echo __('Payment Terms (Day)'); ?></label>
            <?php echo $this->Form->input('Salesorder.payment_terms', array(
                	'type'=>'select',
    				'options' => $arr_salesorders_payment_terms,
    		)); ?>
    		<?php echo $this->Form->input('Salesorder.payment_terms_id', array(
    			'type' => 'hidden',
    		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesorderTax"><?php echo __('Tax %'); ?></label>
        <?php echo $this->Form->input('Salesorder.tax', array(
            	'type'=>'select',
				'options' => $arr_salesorders_tax,
		)); ?>
        <?php echo $this->Form->input('Salesorder.taxval', array(
                'type' => 'hidden',
        )); ?>
        <?php echo $this->Form->input('Salesorder.sum_tax', array(
                'type' => 'hidden',
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CustomerPoNo"><?php echo __('Customer PO No'); ?></label>
        <?php echo $this->Form->input('Salesorder.customer_po_no', array(
				'class' 	=> 'salesorderField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="heading"><?php echo __('Heading'); ?></label>
        <?php echo $this->Form->input('Salesorder.heading', array(
				'class' 	=> 'salesorderField',
		)); ?>
    </div>

   <div class="ui-field-contain">
        <label class="field-title" for="SalesorderJob"><?php echo __('Job'); ?></label>
        <div>
            <div class="ui-block-a" style="width: 40%">
                <?php 
                    echo $this->Form->input('Salesorder.job_number', array(
                            'readonly' => true,
                    )); ?>
            </div>

            <div class="ui-block-b" style="width: 60%">
    		<?php
    				echo $this->Form->input('Salesorder.job_name', array(
    						'readonly' => true,
    						'class' => 'popup-input',
    						'data-popup-controller' => 'Jobs',
    						'data-popup-key' => 'job_name'
    				));
    				echo $this->Form->input('Salesorder.job_id', array(
    		           	'type'=>'hidden'
    		        ));
    		?>
            </div>  
        </div>    
    </div>


   <div class="ui-field-contain">
        <label class="field-title" for="SalesorderQuotation"><?php echo __('Quotation'); ?></label>
        <div>
            <div class="ui-block-a" style="width: 40%">
                <?php 
                    echo $this->Form->input('Salesorder.quotation_number', array(
                            'readonly' => true,
                    )); ?>
            </div>      

    		<div class="ui-block-b" style="width: 60%">
            <?php
    				echo $this->Form->input('Salesorder.quotation_name', array(
    						'readonly' => true,
    						'class' => 'popup-input',
    						'data-popup-controller' => 'quotations',
    						'data-popup-key' => 'quotation_name'
    				));
    				echo $this->Form->input('Salesorder.quotation_id', array(
    		           	'type'=>'hidden'
    		        ));
    		?>
            </div>  
        </div>   
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesorderDelivery"><?php echo __('Delivery method'); ?></label>
        <?php echo $this->Form->input('Salesorder.delivery_method', array(
            	'type'=>'select',
				'options' => $arr_delivery_method,
		)); ?>
    </div>

   <div class="ui-field-contain">
        <label class="field-title" for="SalesorderSalesorderName"><?php echo __('Shipper'); ?></label>
		<?php
				echo $this->Form->input('Salesorder.shipper', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'companies',
						'data-popup-key' => 'shipper_name',
                        'data-popup-param' => '?is_shipper=1'
				));
				echo $this->Form->input('Salesorder.shipper_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SalesorderShipperAccoun"><?php echo __('Shipper account'); ?></label>
        <?php echo $this->Form->input('Salesorder.shipper_account', array(
				'class' 	=> 'salesorderField',
		)); ?>
    </div>





	<?php echo $this->Form->input('Salesorder.our_rep_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Salesorder.salesorder_id', array(
	   'type'=>'hidden'
	)); ?>

</form>
<?php echo $this->element('../Salesorders/js'); ?>