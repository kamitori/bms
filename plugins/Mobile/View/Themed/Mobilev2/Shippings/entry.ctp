<form class="<?php echo $controller; ?>_form_auto_save" id="form_shipping_<?php echo $this->data['Shipping']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Shipping']['_id']; ?>" />
    <?php echo $this->Form->hidden('Shipping._id', array('value' => (string)$this->data['Shipping']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="ShippingCode"><?php echo __('Ref no'); ?></label>
        <?php echo $this->Form->input('Shipping.code', array(
				'readonly' 	=> 'true',
				'class' 	=> 'shippingField'
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="ShippingType"><?php echo __('Type'); ?></label>
        <?php echo $this->Form->input('Shipping.shipping_type', array(
            	'type'=>'select',
				'options' => $arr_shippings_type,
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ShippingCompanyName"><?php echo __('Company'); ?></label>
		<?php
				echo $this->Form->input('Shipping.company_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'companies',
						'data-popup-key' => 'company_name'
				));
				echo $this->Form->input('Shipping.company_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ShippingContactName"><?php echo __('Contact'); ?></label>
		<?php
				echo $this->Form->input('Shipping.contact_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'contact_name',
						'data-popup-param' => '?company_id='.$this->data['Shipping']['company_id'].'&company_name='.$this->data['Shipping']['company_name'].'&is_customer=1',
				));
				echo $this->Form->input('Shipping.contact_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Phone"><?php echo __('Phone'); ?></label>
        <?php echo $this->Form->input('Shipping.phone', array(
				'class' 	=> 'shippingField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Email"><?php echo __('Email'); ?></label>
        <?php echo $this->Form->input('Shipping.email', array(
				'class' 	=> 'shippingField',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="Date"><?php echo __('Date'); ?></label>
        <?php
	        /*echo $this->Form->input('Shipping.shipping_date', array(
					'class' 	=> 'shippingField',
			)); */
		?>
		<div>
			<div class="ui-block-a" style="width: 60%">
	        	<input type="text" name="data[Shipping][shipping_date]" class="date-picker" id="shipping_date_<?php echo $this->data['Shipping']['_id'] ?>" readonly value="<?php echo $this->data['Shipping']['shipping_date']  ?>"/>
	    	</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ShippingOurRep"><?php echo __('Our rep'); ?></label>
		<?php
				echo $this->Form->input('Shipping.our_rep', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'our_rep',
						'data-popup-param' => '?is_employee=1'
				));
				echo $this->Form->input('Shipping.our_rep_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ShippingOurCsr"><?php echo __('Our csr'); ?></label>
		<?php
				echo $this->Form->input('Shipping.our_csr', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'our_csr',
						'data-popup-param' => '?is_employee=1'
				));
				echo $this->Form->input('Shipping.our_csr_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ShippingStatus"><?php echo __('Status'); ?></label>
        <?php echo $this->Form->input('Shipping.shipping_status', array(
            	'type'=>'select',
				'options' => $arr_shippings_status,
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ShippingShipper"><?php echo __('Shipper'); ?></label>
		<?php
				echo $this->Form->input('Shipping.shipper', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'companies',
						'data-popup-key' => 'shipper',
						'data-popup-param' => '?is_shipper=1'
				));
				echo $this->Form->input('Shipping.shipper_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="TrackingNo"><?php echo __('Tracking no'); ?></label>
        <?php echo $this->Form->input('Shipping.tracking_no', array(
				'class' 	=> 'shippingField',
		)); ?>
    </div>

     <div class="ui-field-contain">
        <label class="field-title" for="ShippingCostCb"><?php echo __('Shipping cost'); ?></label>
        <?php echo $this->Form->input('Shipping.shipping_cost_cb', array(
				'class' 	=> 'shippingField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DueDate"><?php echo __('Date received'); ?></label>
		<div>
			<div class="ui-block-a" style="width: 60%">
	        	<input type="text" name="data[Shipping][received_date]" class="date-picker" id="received_date<?php echo $this->data['Shipping']['_id'] ?>" readonly value="<?php echo $this->data['Shipping']['received_date']  ?>"/>
	    	</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="heading"><?php echo __('Heading'); ?></label>
        <?php echo $this->Form->input('Shipping.heading', array(
				'class' 	=> 'shippingField',
		)); ?>
    </div>

   <div class="ui-field-contain">
        <label class="field-title" for="ShippingJob"><?php echo __('Job'); ?></label>
        <div>
        	<div class="ui-block-a" style="width: 40%">
                <?php 
                    echo $this->Form->input('Shipping.job_number', array(
                            'readonly' => true,
                )); ?>
        		
            </div>  
            <div class="ui-block-b" style="width: 60%">
				<?php
					echo $this->Form->input('Shipping.job_name', array(
							'readonly' => true,
							'class' => 'popup-input',
							'data-popup-controller' => 'jobs',
							'data-popup-key' => 'job_name'
					));
					echo $this->Form->input('Shipping.job_id', array(
			           		'type'=>'hidden'
			        ));
				?>
			</div>
		</div>    
    </div>


   <div class="ui-field-contain">
        <label class="field-title" for="ShippingSalesorderName"><?php echo __('Sales Order'); ?></label>
        <div>
        	<div class="ui-block-a" style="width: 40%">
        		<?php 
                    echo $this->Form->input('Shipping.salesorder_number', array(
                        'readonly' => true,
                )); ?>
        	</div>
        	<div class="ui-block-b" style="width: 60%">
				<?php
					echo $this->Form->input('Shipping.salesorder_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'salesorders',
						'data-popup-key' => 'Salesorder_name'
					));
					echo $this->Form->input('Shipping.salesorder_id', array(
			           	'type'=>'hidden'
			        ));
				?>
			</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ShippingSalesInvoiceName"><?php echo __('Sales invoice'); ?></label>
        <div>
        	<div class="ui-block-a" style="width: 40%">
        		<?php 
                    echo $this->Form->input('Shipping.salesinvoice_number', array(
                        'readonly' => true,
                )); ?>
        	</div>
        	<div class="ui-block-b" style="width: 60%">
				<?php
					echo $this->Form->input('Shipping.salesinvoice_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'salesinvoices',
						'data-popup-key' => 'salesinvoice_name'
					));
					echo $this->Form->input('Shipping.salesinvoice_id', array(
			           	'type'=>'hidden'
			        ));
				?>
			</div>
		</div>
    </div>


	<?php echo $this->Form->input('Shipping.shipping_id', array(
	   'type'=>'hidden'
	)); ?>

</form>
<?php echo $this->element('../Shippings/js'); ?>