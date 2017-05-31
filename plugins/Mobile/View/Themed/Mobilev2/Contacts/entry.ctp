<style type="text/css">
.error_input{
  border: 1px solid red !important;
}
</style>

<script type="text/javascript">
/*$(function(){
	input_show_select_calendar(".JtSelectDate", "#form_contact_<?php echo $this->data['Contact']['_id']; ?>");
})*/
</script>
<form class="<?php echo $controller; ?>_form_auto_save" id="form_contact_<?php echo $this->data['Contact']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Contact']['_id']; ?>" />
    <?php echo $this->Form->hidden('Contact._id', array('value' => (string)$this->data['Contact']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="ContactNo"><?php echo __('Ref no'); ?></label>
        <?php echo $this->Form->input('Contact.no', array(
				'readonly' 	=> 'true',
				'class' 	=> 'contactField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ContactIsCustomer"><?php echo __('active'); ?></label>
        <label class="field-title" for="ContactIsCustomer"><?php echo __('Customer'); ?></label>
        <?php
            echo $this->Form->input('Contact.is_customer', array(
                    'type'      => 'checkbox',
            ));
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Contacttitle"><?php echo __('Title'); ?></label>
        <?php echo $this->Form->input('Contact.title', array(
            	'type'=>'select',
				'options' => $arr_contacts_title,
		)); ?>
		<?php echo $this->Form->input('Contact.title_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Firstname"><?php echo __('First name'); ?></label>
        <?php echo $this->Form->input('Contact.first_name', array(
				'class' 	=> 'contactField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Lastname"><?php echo __('Last name'); ?></label>
        <?php echo $this->Form->input('Contact.last_name', array(
				'class' 	=> 'contactField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Contacttype"><?php echo __('Type'); ?></label>
        <?php echo $this->Form->input('Contact.type', array(
            	'type'=>'select',
				'options' => $arr_contacts_type,
		)); ?>
		<?php echo $this->Form->input('Contact.type_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Direct_dial"><?php echo __('Direct Dial'); ?></label>
        <?php echo $this->Form->input('Contact.direct_dial', array(
				'class' 	=> 'contactField',
		)); ?>
    </div>

     <div class="ui-field-contain">
        <label class="field-title" for="mobile"><?php echo __('Mobile'); ?></label>
        <?php echo $this->Form->input('Contact.mobile', array(
				'class' 	=> 'contactField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Email"><?php echo __('Email'); ?></label>
        <?php echo $this->Form->input('Contact.email', array(
				'class' 	=> 'contactField',
		)); ?>
    </div>

	<div class="ui-field-contain">
        <label class="field-title" for="fax"><?php echo __('Fax'); ?></label>
        <?php echo $this->Form->input('Contact.fax', array(
				'class' 	=> 'contactField',
		)); ?>
    </div>

   <div class="ui-field-contain">
        <label class="field-title" for="home_phone"><?php echo __('Home phone'); ?></label>
        <?php echo $this->Form->input('Contact.home_phone', array(
				'class' 	=> 'contactField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ContactCompanyName"><?php echo __('Company'); ?></label>
		<?php
				echo $this->Form->input('Contact.company_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'companies',
						'data-popup-key' => 'company_name'
				));
				echo $this->Form->input('Contact.company_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="company_phone"><?php echo __('Company phone'); ?></label>
        <?php echo $this->Form->input('Contact.company_phone', array(
				'class' 	=> 'contactField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="position"><?php echo __('Position'); ?></label>
        <?php echo $this->Form->input('Contact.position', array(
				'type'=>'select',
				'options' => $arr_position,
		)); ?>
    </div>

     <div class="ui-field-contain">
        <label class="field-title" for="Department"><?php echo __('Department'); ?></label>
        <?php echo $this->Form->input('Contact.department', array(
				'type'=>'select',
				'options' => $arr_department,
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="extension_no"><?php echo __('Extension no'); ?></label>
        <?php echo $this->Form->input('Contact.extension_no', array(
				'class' 	=> 'contactField',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="ContactTownCity"><?php echo __('Address'); ?></label>
        <div data-role="collapsible">
            <h1>Address Detail</h1>
            <?php
                $default_address_key = isset($this->data['Contact']['addresses_default_key']) ? $this->data['Contact']['addresses_default_key'] : 0;
            ?>
            <ul data-role="listview" data-theme="a">
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 1</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Contact.addresses.address_1', array(
                                'value' => $this->data['Contact']['addresses'][ $default_address_key]['address_1'],
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 2</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Contact.addresses.address_2', array(
                                'value' => $this->data['Contact']['addresses'][ $default_address_key]['address_2'],
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 3</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Contact.addresses.address_3', array(
                                'value' => $this->data['Contact']['addresses'][ $default_address_key]['address_3'],
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Town / City</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Contact.addresses.town_city', array(
                                'value' => $this->data['Contact']['addresses'][$default_address_key]['town_city'],
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Province / State</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            echo $this->Form->input('Contact.addresses.province_state_id', array(
                                    'value' => $this->data['Contact']['addresses'][$default_address_key]['province_state_id'],
                                    'options' => $options['provinces'][$this->data['Contact']['addresses'][$default_address_key]['country_id']],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Zip / Post code</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Contact.addresses.zip_postcode', array(
                                'value' => $this->data['Contact']['addresses'][$default_address_key]['zip_postcode'],
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Country</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Contact.addresses.country_id', array(
                                'options' => $options['countries'],
                                'value' => $this->data['Contact']['addresses'][$default_address_key]['country_id'],
                                'onchange'  => 'change_pro()'
                        )); ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    

    <div class="ui-field-contain">
        <label class="field-title" for="ContactOurRep"><?php echo __('Responsible'); ?></label>
		<?php
				echo $this->Form->input('Contact.our_rep', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'our_rep'
				));
				echo $this->Form->input('Contact.our_rep_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
		<label class="field-title" for="ContactIsEmployee"><?php echo __('active'); ?></label>
		<label class="field-title" for="ContactIsEmployee"><?php echo __('Employee'); ?></label>
		<?php
		    echo $this->Form->input('Contact.is_employee', array(
	            	'type'		=> 'checkbox',
			));
		?>
	</div>

	<div class="ui-field-contain">
		<label class="field-title" for="ContactInactive"><?php echo __('active'); ?></label>
		<label class="field-title" for="ContactInactive"><?php echo __('Inactive'); ?></label>
		<?php
			echo $this->Form->input('Contact.inactive', array(
		            	'type'		=> 'checkbox',
			));
		?>
	</div>

	<?php echo $this->Form->input('Contact.salesorder_id', array(
       	'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Contact.our_rep_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Contact.enquiry_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Contact.quotation_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Contact.job_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Contact.purchaseorder_id', array(
		'type'=>'hidden'
	)); ?>
</form>
<script type="text/javascript">
	/*$(function() {
        contacts_update_entry_header("<?php echo $this->data['Contact']['_id']; ?>");
    });*/
</script>
<?php echo $this->element('../Contacts/js'); ?>