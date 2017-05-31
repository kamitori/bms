<form class="<?php echo $controller; ?>_form_auto_save" id="form_company">
	<input type="hidden" id="mongoid" value="<?php //echo $this->data['Company']['_id']; ?>" />
    <?php //echo $this->Form->hidden('Company._id', array('value' => (string)$this->data['Company']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="CompanyNo"><?php echo __('Company no'); ?></label>
        <?php echo $this->Form->input('Company.no', array(
				'readonly' 	=> 'false',
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
        <label class="field-title" for="CompanyType"><?php echo __('Type'); ?></label>
        <?php echo $this->Form->input('Company.type', array(
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
        <?php echo $this->Form->input('Company.our_rep', array(
      		'readonly'  => true,
      		'class' 	=> 'popup-input',
      		'data-popup-controller' => 'contacts',
      		'data-popup-key' => 'contact_name'
		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyOurCsr"><?php echo __('Our CSR'); ?></label>
        <?php echo $this->Form->input('Company.our_csr', array(
      		'readonly'  => true,
      		'class' 	=> 'popup-input',
      		'data-popup-controller' => 'contacts',
      		'data-popup-key' => 'contact_name'
		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyOurRep"><?php echo __('Default address'); ?></label>
        <?php echo $this->Form->input('Company.contact_name', array(
      		'readonly'  => true,
      		'class' 	=> 'popup-input',
      		'data-popup-controller' => 'contacts',
      		'data-popup-key' => 'contact_name'
		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyTownCity"><?php echo __('Town / City'); ?></label>
        <?php echo $this->Form->input('Company.town_city', array(

		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyProvince"><?php echo __('Province / State'); ?></label>
        <?php echo $this->Form->input('Company.province', array(
            	'type'		=> 'select',
				'options' 	=> $salesaccounts_status,
		)); ?>
		<?php echo $this->Form->input('Company.province_id', array(
			'type' => 'hidden',
		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyZipPostCode"><?php echo __('Zip / Post code'); ?></label>
        <?php echo $this->Form->input('Company.zip_postcode', array(

        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CompanyCountry"><?php echo __('Country'); ?></label>
        <?php echo $this->Form->input('Company.country', array(
                'type'      => 'select',
                'options'   => $salesaccounts_status,
        )); ?>
        <?php echo $this->Form->input('Company.country_id', array(
            'type' => 'hidden',
        )); ?>
    </div>
    <div class="ui-field-contain">
                <label class="field-title" for="CompanyInactive"><?php echo __('active'); ?></label>
                <label class="field-title" for="CompanyInactive"><?php echo __('Inactive'); ?></label>
                <?php echo $this->Form->input('Company.inactive', array(
                        'type'      => 'checkbox',
                )); ?>
    </div>
</form>
<?php //echo $this->element('../Companies/js'); ?>