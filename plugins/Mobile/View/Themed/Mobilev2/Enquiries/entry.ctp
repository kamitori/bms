<form class="<?php echo $controller; ?>_form_auto_save" id="form_enquiry_<?php echo $this->data['Enquiry']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Enquiry']['_id']; ?>" />
    <?php echo $this->Form->hidden('Enquiry._id', array('value' => (string)$this->data['Enquiry']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="EnquiryNo"><?php echo __('Enquiry no'); ?></label>
        <?php echo $this->Form->input('Enquiry.no', array(
				'readonly' 	=> 'true',
				'class' 	=> 'enquiryField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="EnquiryCompanyName"><?php echo __('Company'); ?></label>
		<?php
				echo $this->Form->input('Enquiry.company_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'companies',
						'data-popup-key' => 'company_name'
				));
				echo $this->Form->input('Enquiry.company_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

   <div class="ui-field-contain">
        <label class="field-title" for="Enquirytitle"><?php echo __('Title'); ?></label>
        <?php echo $this->Form->input('Enquiry.title', array(
            	'type'=>'select',
				'options' => $arr_enquiries_title,
		)); ?>
		<?php echo $this->Form->input('Enquiry.title_id', array(
			'type' => 'hidden',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="EnquiryContactName"><?php echo __('Contact'); ?></label>
		<?php
				echo $this->Form->input('Enquiry.contact_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'contact_name',
                        'data-popup-param' => '?is_customer=1'
				));
				echo $this->Form->input('Enquiry.contact_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="Date"><?php echo __('Date'); ?></label>
   	    <input type="text" name="data[Enquiry][date]" class="date-picker" id="date_<?php echo $this->data['Enquiry']['_id'] ?>" readonly value="<?php echo $this->data['Enquiry']['date']  ?>"/>

    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="EnquiryStatus"><?php echo __('Status'); ?></label>
        <?php echo $this->Form->input('Enquiry.status', array(
            	'type'=>'select',
				'options' => $arr_enquiries_status,
		)); ?>
		<?php echo $this->Form->input('Enquiry.status_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="EnquiryRating"><?php echo __('Rating'); ?></label>
        <?php echo $this->Form->input('Enquiry.rating', array(
            	'type'=>'select',
				'options' => $arr_enquiries_rating,
		)); ?>
		<?php echo $this->Form->input('Enquiry.rating_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Web"><?php echo __('Web'); ?></label>
        <?php echo $this->Form->input('Enquiry.web', array(
				'class' 	=> 'enquiryField',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="EnquiryTownCity"><?php echo __('Address'); ?></label>
        <div data-role="collapsible">
            <h1>Address Detail</h1>
            <ul data-role="listview" data-theme="a">
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 1</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Enquiry.default_address_1', array(
                                'value' => $this->data['Enquiry']['default_address_1'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 2</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Enquiry.default_address_2', array(
                                'value' => $this->data['Enquiry']['default_address_2'],
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 3</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Enquiry.default_address_3', array(
                                'value' => $this->data['Enquiry']['default_address_3'],
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Town / City</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Enquiry.default_town_city', array(
                                'value' => $this->data['Enquiry']['default_town_city'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Province / State</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            echo $this->Form->input('Enquiry.default_province_state_id', array(
                                    'value' => $this->data['Enquiry']['default_province_state_id'],
                                    'options' => $options['provinces'][$this->data['Enquiry']['default_country_id']],
                                    'empty' => '',
                                    'readonly'  => 'true',
                                ));
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Zip / Post code</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Enquiry.default_zip_postcode', array(
                                'value' => $this->data['Enquiry']['default_zip_postcode'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Country</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php echo $this->Form->input('Enquiry.default_country_id', array(
                                'options' => $options['countries'],
                                'value' => $this->data['Enquiry']['default_country_id'],
                                'readonly'  => 'true',
                        )); ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    

    <div class="ui-field-contain">
        <label class="field-title" for="company_phone"><?php echo __('Company phone'); ?></label>
        <?php echo $this->Form->input('Enquiry.company_phone', array(
				'class' 	=> 'enquiryField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DirectPhone"><?php echo __('Direct Phone'); ?></label>
        <?php echo $this->Form->input('Enquiry.direct_phone', array(
				'class' 	=> 'enquiryField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="home_phone"><?php echo __('Home Phone'); ?></label>
        <?php echo $this->Form->input('Enquiry.home_phone', array(
				'class' 	=> 'enquiryField',
		)); ?>
    </div>

     <div class="ui-field-contain">
        <label class="field-title" for="mobile"><?php echo __('Mobile'); ?></label>
        <?php echo $this->Form->input('Enquiry.mobile', array(
				'class' 	=> 'enquiryField',
		)); ?>
    </div>

     <div class="ui-field-contain">
        <label class="field-title" for="CompanyFax"><?php echo __('Company Fax'); ?></label>
        <?php echo $this->Form->input('Enquiry.company_fax', array(
				'class' 	=> 'enquiryField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DirectFax"><?php echo __('Direct Fax'); ?></label>
        <?php echo $this->Form->input('Enquiry.direct_fax', array(
				'class' 	=> 'enquiryField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ContactEmail"><?php echo __('Contact Email'); ?></label>
        <?php echo $this->Form->input('Enquiry.contact_email', array(
				'class' 	=> 'enquiryField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CompanyEmail"><?php echo __('Company Email'); ?></label>
        <?php echo $this->Form->input('Enquiry.company_email', array(
				'class' 	=> 'enquiryField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="EnquiryPosition"><?php echo __('Position'); ?></label>
        <?php echo $this->Form->input('Enquiry.position', array(
            	'type'=>'select',
				'options' => $arr_enquiries_position,
		)); ?>
		<?php echo $this->Form->input('Enquiry.position_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="EnquiryDepartment"><?php echo __('Department'); ?></label>
        <?php echo $this->Form->input('Enquiry.department', array(
            	'type'=>'select',
				'options' => $arr_enquiries_department,
		)); ?>
		<?php echo $this->Form->input('Enquiry.department_id', array(
			'type' => 'hidden',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="EnquiryType"><?php echo __('Type'); ?></label>
        <?php echo $this->Form->input('Enquiry.type', array(
            	'type'=>'select',
				'options' => $arr_enquiries_type,
		)); ?>
		<?php echo $this->Form->input('Enquiry.type_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="EnquiryCategory"><?php echo __('Category'); ?></label>
        <?php echo $this->Form->input('Enquiry.Category', array(
            	'type'=>'select',
				'options' => $arr_enquiries_category,
		)); ?>
		<?php echo $this->Form->input('Enquiry.category_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="EnquiryValue"><?php echo __('Enquiry value'); ?></label>
        <?php echo $this->Form->input('Enquiry.enquiry_value', array(
				'class' 	=> 'enquiryField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="NoOfStaff"><?php echo __('No of staff'); ?></label>
        <?php echo $this->Form->input('Enquiry.no_of_staff', array(
				'class' 	=> 'enquiryField',
		)); ?>
    </div>

     <div class="ui-field-contain">
        <label class="field-title" for="EnquiryReferred"><?php echo __('Referred'); ?></label>
        <?php echo $this->Form->input('Enquiry.referred', array(
            	'type'=>'select',
				'options' => $arr_enquiries_referred,
		)); ?>
		<?php echo $this->Form->input('Enquiry.referred_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="EnquiryOurRep"><?php echo __('Our rep'); ?></label>
		<?php
				echo $this->Form->input('Enquiry.our_rep', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'our_rep',
                        'data-popup-param' => '?is_employee=1'
				));
				echo $this->Form->input('Enquiry.our_rep_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>


	<?php echo $this->Form->input('Enquiry.enquiry_id', array(
	   'type'=>'hidden'
	)); ?>

</form>

<?php echo $this->element('../Enquiries/js'); ?>