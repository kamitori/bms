<style type="text/css">
.error_input{
  border: 1px solid red !important;
}
</style>

<form class="<?php echo $controller; ?>_form_auto_save" id="form_company_<?php echo $this->data['Company']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Company']['_id']; ?>" />
    <?php echo $this->Form->hidden('Company._id', array('value' => (string)$this->data['Company']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="CompanyNo"><?php echo __('Company no'); ?></label>
        <?php echo $this->Form->input('Company.no', array(
				'readonly' 	=> 'true',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CompanyIsCustomer"><?php echo __('active'); ?></label>
    	<label class="field-title" for="CompanyIsCustomer"><?php echo __('Customer'); ?></label>
        <?php echo $this->Form->input('Company.is_customer', array(
            	'type'		=> 'checkbox',
		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyIsSupplier"><?php echo __('active'); ?></label>
        <label class="field-title" for="CompanyIsSupplier"><?php echo __('Supplier'); ?></label>
        <?php echo $this->Form->input('Company.is_supplier', array(
                'type'      => 'checkbox',
        )); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="CompanyName"><?php echo __('Company name'); ?></label>
        <?php echo $this->Form->input('Company.name', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CompanyTypeName"><?php echo __('Type'); ?></label>
        <?php echo $this->Form->input('Company.type_name', array(
            	'type'		=> 'select',
				'options' 	=> $salesaccounts_status,
		)); ?>
		<?php echo $this->Form->input('Company.type_id', array(
			'type' => 'hidden',
		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyPhone"><?php echo __('Phone'); ?></label>
        <?php echo $this->Form->input('Company.phone', array(

		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyFax"><?php echo __('Fax'); ?></label>
        <?php echo $this->Form->input('Company.fax', array(

		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyEmail"><?php echo __('Email'); ?></label>
        <?php echo $this->Form->input('Company.email', array(

		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyOurRep"><?php echo __('Our Rep'); ?></label>
        <?php 
        echo $this->Form->input('Company.our_rep', array(
          		'readonly'  => true,
          		'class' 	=> 'popup-input',
          		'data-popup-controller' => 'contacts',
          		'data-popup-key' => 'our_rep',
                'data-popup-param' => '?is_employee=1'
		)); 
        echo $this->Form->input('Company.our_rep_id', array(
                'type'=>'hidden'
        ));
        ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyOurCsr"><?php echo __('Our CSR'); ?></label>
        <?php echo $this->Form->input('Company.our_csr', array(
          		'readonly'  => true,
          		'class' 	=> 'popup-input',
          		'data-popup-controller' => 'contacts',
          		'data-popup-key' => 'our_csr',
                'data-popup-param' => '?is_employee=1'
		)); 
        echo $this->Form->input('Company.our_csr_id', array(
                'type'=>'hidden'
        ));
        ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyInactive"><?php echo __('active'); ?></label>
        <label class="field-title" for="CompanyInactive"><?php echo __('Inactive'); ?></label>
        <?php echo $this->Form->input('Company.inactive', array(
                'type'      => 'checkbox',
        )); ?>
    </div>
    
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyTownCity"><?php echo __('Address'); ?></label>
        <div data-role="collapsible">
            <h1>Address Detail</h1>
            <?php
                $default_address_key = isset($this->data['Company']['addresses_default_key']) ? $this->data['Company']['addresses_default_key'] : 0;

            ?>
            <ul data-role="listview" data-theme="a">
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 1</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Company.addresses.address_1', array(
                                'value' => $this->data['Company']['addresses'][ $default_address_key]['address_1'],
                                /*'readonly'  => 'true',*/
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 2</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Company.addresses.address_2', array(
                                'value' => $this->data['Company']['addresses'][ $default_address_key]['address_2'],
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 3</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Company.addresses.address_3', array(
                                'value' => $this->data['Company']['addresses'][ $default_address_key]['address_3'],
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Town / City</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Company.addresses.town_city', array(
                                'value' => $this->data['Company']['addresses'][$default_address_key]['town_city'],
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Province / State</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            echo $this->Form->input('Company.addresses.province_state_id', array(
                                    'value' => $this->data['Company']['addresses'][$default_address_key]['province_state_id'],
                                    'options' => $options['provinces'][$this->data['Company']['addresses'][$default_address_key]['country_id']],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Zip / Post code</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Company.addresses.zip_postcode', array(
                                'value' => $this->data['Company']['addresses'][$default_address_key]['zip_postcode'],
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Country</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Company.addresses.country_id', array(
                                'options' => $options['countries'],
                                'value' => $this->data['Company']['addresses'][$default_address_key]['country_id'],
                                'readonly'  => 'true',
                                'onchange'  => 'change_pro()'
                        )); ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>

</form>
<?php echo $this->element('../Companies/js'); ?>