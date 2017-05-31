<form class="<?php echo $controller; ?>_form_auto_save" id="form_purchaseorder_<?php echo $this->data['Purchaseorder']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Purchaseorder']['_id']; ?>" />
    <?php echo $this->Form->hidden('Purchaseorder._id', array('value' => (string)$this->data['Purchaseorder']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="PurchaseorderCode"><?php echo __('Ref no'); ?></label>
        <?php echo $this->Form->input('Purchaseorder.code', array(
				'readonly' 	=> 'true',
				'class' 	=> 'purchaseorderField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="PurchaseorderCompanyName"><?php echo __('Company'); ?></label>
		<?php
				echo $this->Form->input('Purchaseorder.company_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'companies',
						'data-popup-key' => 'company_name',
						'data-popup-param' => '?is_supplier=1'
				));
				echo $this->Form->input('Purchaseorder.company_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="PurchaseorderContactName"><?php echo __('Contact'); ?></label>
		<?php
				echo $this->Form->input('Purchaseorder.contact_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'contact_name',
						'data-popup-param' => '?company_id='.$this->data['Purchaseorder']['company_id'].'&company_name='.$this->data['Purchaseorder']['company_name'].'&is_customer=1',
				));
				echo $this->Form->input('Purchaseorder.contact_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Phone"><?php echo __('Phone'); ?></label>
        <?php echo $this->Form->input('Purchaseorder.phone', array(
				'class' 	=> 'purchaseorderField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Fax"><?php echo __('Fax'); ?></label>
        <?php echo $this->Form->input('Purchaseorder.fax', array(
				'class' 	=> 'purchaseorderField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Email"><?php echo __('Email'); ?></label>
        <?php echo $this->Form->input('Purchaseorder.email', array(
				'class' 	=> 'purchaseorderField',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="Date"><?php echo __('Date'); ?></label>
        <?php
	        /*echo $this->Form->input('Purchaseorder.purchord_date', array(
					'class' 	=> 'purchaseorderField',
			)); */
		?>
		<div>
			<div class="ui-block-a" style="width: 60%">
	        	<input type="text" name="data[Purchaseorder][purchord_date]" class="date-picker" id="purchord_date_<?php echo $this->data['Purchaseorder']['_id'] ?>" readonly value="<?php echo $this->data['Purchaseorder']['purchord_date']  ?>"/>
	    	</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="PurchaseorderOurRep"><?php echo __('Our rep'); ?></label>
		<?php
				echo $this->Form->input('Purchaseorder.our_rep', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'our_rep',
						'data-popup-param' => '?is_employee=1'
				));
				echo $this->Form->input('Purchaseorder.our_rep_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="name"><?php echo __('Heading'); ?></label>
        <?php echo $this->Form->input('Purchaseorder.name', array(
				'class' 	=> 'purchaseorderField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DueDate"><?php echo __('Required date'); ?></label>
        <?php
	        /*echo $this->Form->input('Purchaseorder.required_date', array(
					'class' 	=> 'purchaseorderField',
			)); */
		?>
		<div>
			<div class="ui-block-a" style="width: 60%">
	        	<input type="text" name="data[Purchaseorder][required_date]" class="date-picker" id="required_date_<?php echo $this->data['Purchaseorder']['_id'] ?>" readonly value="<?php echo $this->data['Purchaseorder']['required_date']  ?>"/>
	    	</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="SupplierQuoteRef"><?php echo __('Supplier quote ref'); ?></label>
        <?php echo $this->Form->input('Purchaseorder.supplier_quote_ref', array(
				'class' 	=> 'purchaseorderField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="PurchaseorderShipToCompanyName"><?php echo __('Ship to: Company'); ?></label>
		<?php
				echo $this->Form->input('Purchaseorder.ship_to_company_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'companies',
						'data-popup-key' => 'ship_to_company_name',
						'data-popup-param' => '?name=anvy'
				));
				echo $this->Form->input('Purchaseorder.ship_to_company_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="PurchaseorderShipToContactName"><?php echo __('Ship to: Contact'); ?></label>
		<?php
				echo $this->Form->input('Purchaseorder.ship_to_contact_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'ship_to_contact_name',
						'data-popup-param' => '?company_id='.$this->data['Purchaseorder']['ship_to_company_id'].'&company_name='.$this->data['Purchaseorder']['ship_to_company_name'].'&t=1',
				));
				echo $this->Form->input('Purchaseorder.ship_to_contact_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="TrackingNo"><?php echo __('Tracking no'); ?></label>
        <?php echo $this->Form->input('Purchaseorder.tracking_no', array(
				'class' 	=> 'purchaseorderField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="PurchaseorderShipperCompanyName"><?php echo __('Shipper'); ?></label>
		<?php
				echo $this->Form->input('Purchaseorder.shipper_company_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'companies',
						'data-popup-key' => 'shipper_company_name',
						'data-popup-param' => '?is_shipper=1'
				));
				echo $this->Form->input('Purchaseorder.shipper_company_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title"><?php echo __('Shipping address'); ?></label>
        <div data-role="collapsible">
            <h1>Address Detail</h1>
            <ul data-role="listview" data-theme="a">

                <li>
                    <div class="ui-block-a" style="width:30%"><label>Shipping address</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Purchaseorder.shipping_address.0.shipping_address_1', array(
                                'value' => isset($this->data['Purchaseorder']['shipping_address']['0']['shipping_address_1'])?$this->data['Purchaseorder']['shipping_address']['0']['shipping_address_1']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label></label></div> <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Purchaseorder.shipping_address.0.shipping_address_2', array(
                                'value' => isset($this->data['Purchaseorder']['shipping_address']['0']['shipping_address_2'])?$this->data['Purchaseorder']['shipping_address']['0']['shipping_address_2']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label></label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Purchaseorder.shipping_address.0.shipping_address_3', array(
                                'value' => isset($this->data['Purchaseorder']['shipping_address']['0']['shipping_address_3'])?$this->data['Purchaseorder']['shipping_address']['0']['shipping_address_3']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Town / City</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Purchaseorder.shipping_address.0.shipping_town_city', array(
                                'value' => isset($this->data['Purchaseorder']['shipping_address']['0']['shipping_town_city'])?$this->data['Purchaseorder']['shipping_address']['0']['shipping_town_city']:'',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Province / State</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            if (!isset($this->data['Purchaseorder']['shipping_address']['0']['shipping_country_id']))
                                echo $this->Form->input('Purchaseorder.shipping_address.0.shipping_province_state_id', array(
                                    'value' => isset($this->data['Purchaseorder']['shipping_address']['0']['shipping_province_state_id'])?$this->data['Purchaseorder']['shipping_address']['0']['shipping_province_state_id']:'',
                                    'options' => $options['provinces']['CA'],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                            else   
                                echo $this->Form->input('Purchaseorder.shipping_address.0.shipping_province_state_id', array(
                                    'value' => isset($this->data['Purchaseorder']['shipping_address']['0']['shipping_province_state_id'])?$this->data['Purchaseorder']['shipping_address']['0']['shipping_province_state_id']:'',
                                    'options' => $options['provinces'][$this->data['Purchaseorder']['shipping_address']['0']['shipping_country_id']],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Zip / Post code</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Purchaseorder.shipping_address.0.shipping_zip_postcode', array(
                                'value' => isset($this->data['Purchaseorder']['shipping_address']['0']['shipping_zip_postcode'])?$this->data['Purchaseorder']['shipping_address']['0']['shipping_zip_postcode']:'',
                                
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Country</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Purchaseorder.shipping_address.0.shipping_country_id', array(
                                'options' => $options['countries'],
                                'value' => isset($this->data['Purchaseorder']['shipping_address']['0']['shipping_country_id'])?$this->data['Purchaseorder']['shipping_address']['0']['shipping_country_id']:'CA',
                                'readonly'  => 'true',
                                'onchange'  => 'change_pro()'
                        )); ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="PurchaseOrderStatus"><?php echo __('Status'); ?></label>
        <?php echo $this->Form->input('Purchaseorder.purchase_orders_status', array(
            	'type'=>'select',
				'options' => $arr_purchaseorders_status,
		)); ?>
    </div>

     <div class="ui-field-contain">
        <label class="field-title" for="PurchaseorderReceivedByContactName"><?php echo __('Received by'); ?></label>
		<?php
				echo $this->Form->input('Purchaseorder.received_by_contact_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'received_by_contact_name',
						'data-popup-param' => '?company_id='.$this->data['Purchaseorder']['ship_to_company_id'].'&company_name='.$this->data['Purchaseorder']['ship_to_company_name'].'&t=1',
				));
				echo $this->Form->input('Purchaseorder.received_by_contact_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="PurchaseorderPaymentTerms"><?php echo __('Payment Terms'); ?></label>
        <?php echo $this->Form->input('Purchaseorder.payment_terms', array(
            	'type'=>'select',
				'options' => $arr_purchaseorders_payment_terms,
		)); ?>
		<?php echo $this->Form->input('Purchaseorder.payment_terms_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="PurchaseorderTax"><?php echo __('Tax %'); ?></label>
        <?php
	        echo $this->Form->input('Purchaseorder.tax', array(
            	'type'=>'select',
				'options' => $arr_purchaseorders_tax,
			));
			echo $this->Form->input('Purchaseorder.taxval', array(
	           		'type'=>'hidden'
	        ));
		 ?>
    </div>

   <div class="ui-field-contain">
        <label class="field-title" for="PurchaseorderJob"><?php echo __('Job'); ?></label>
        <div>
        	<div class="ui-block-a" style="width: 40%">
                <?php 
                    echo $this->Form->input('Purchaseorder.job_number', array(
                            'readonly' => true,
                )); ?>
            </div>  
            <div class="ui-block-b" style="width: 60%">
				<?php
					echo $this->Form->input('Purchaseorder.job_name', array(
							'readonly' => true,
							'class' => 'popup-input',
							'data-popup-controller' => 'jobs',
							'data-popup-key' => 'job_name'
					));
					echo $this->Form->input('Purchaseorder.job_id', array(
			           	'type'=>'hidden'
			        ));
				?>
			</div>
		</div>
    </div>


   <div class="ui-field-contain">
        <label class="field-title" for="PurchaseorderSalesorder"><?php echo __('Sales Order'); ?></label>
        <div>
        	<div class="ui-block-a" style="width: 40%">
        		<?php 
                    echo $this->Form->input('Purchaseorder.salesorder_number', array(
                        'readonly' => true,
                )); ?>
                
            </div>
            <div class="ui-block-b" style="width: 60%">
				<?php
					echo $this->Form->input('Purchaseorder.salesorder_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'salesorders',
						'data-popup-key' => 'salesorder_name'
					));
					echo $this->Form->input('Purchaseorder.salesorder_id', array(
			           	'type'=>'hidden'
			        ));
				?>
			</div>
		</div>
    </div>




	<?php echo $this->Form->input('Purchaseorder.our_rep_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Purchaseorder.purchaseorder_id', array(
	   'type'=>'hidden'
	)); ?>

</form>
<?php echo $this->element('../Purchaseorders/js'); ?>