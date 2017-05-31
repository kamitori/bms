<form class="<?php echo $controller; ?>_form_auto_save" id="form_company_<?php //echo $this->data['Company']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php //echo $this->data['Company']['_id']; ?>" />
    <?php //echo $this->Form->hidden('Company._id', array('value' => (string)$this->data['Company']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="RefNo"><?php echo __('Ref code'); ?></label>
        <?php echo $this->Form->input('Communication.code', array(
				'readonly' 	=> 'true',
		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationCommsType"><?php echo __('Type'); ?></label>
        <?php echo $this->Form->input('Communication.comms_type', array(
                'readonly'  => 'true',
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationCompanyName"><?php echo __('Company'); ?></label>
        <?php
                echo $this->Form->input('Communication.company_name', array(
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
        <label class="field-title" for="CommunicationTitle"><?php echo __('Contact:title'); ?></label>
        <?php echo $this->Form->input('Communication.title', array(

        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationContactName"><?php echo __('First name'); ?></label>
        <?php
                echo $this->Form->input('Communication.contact_name', array(
                        'readonly' => true,
                        'class' => 'popup-input',
                        'data-popup-controller' => 'contacts',
                        'data-popup-key' => 'contact_name'
                ));
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationLastName"><?php echo __('Last name'); ?></label>
        <?php
                echo $this->Form->input('Communication.last_name', array(
                ));
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationCommsDate"><?php echo __('Date'); ?></label>
        <div>
            <div class="ui-block-a" style="width: 100%">
                <input type="text" name="data[Communication][comms_date]" class="date-picker" id="comms_date_<?php echo $this->data['Communication']['_id'] ?>" readonly value="<?php echo $this->data['Communication']['comms_date']; ?>"/>
            </div>
        </div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationSignOff"><?php echo __('Sign off'); ?></label>
        <?php echo $this->Form->input('Communication.sign_off', array(
                'type'      => 'select',
                'options'   => $com_sign_off,
        )); ?>
        <?php echo $this->Form->input('Communication.sign_off_id', array(
                'type' => 'hidden',
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationFrom"><?php echo __('From'); ?></label>
        <?php echo $this->Form->input('Communication.contact_from', array(
                'readonly' => true,
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationPosition"><?php echo __('Position'); ?></label>
        <?php echo $this->Form->input('Communication.position', array(
                'type'      => 'select',
                'options'   => $contacts_position,
        )); ?>
        <?php echo $this->Form->input('Communication.sign_off_id', array(
            'type' => 'hidden',
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationSalutation"><?php echo __('Salutation'); ?></label>
        <?php echo $this->Form->input('Communication.salutation', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationName"><?php echo __('Reference'); ?></label>
        <?php echo $this->Form->input('Communication.name', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationCC"><?php echo __('CC'); ?></label>
        <?php echo $this->Form->input('Communication.email_cc', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationIncludeSignature"><?php echo __('Include signature'); ?></label>
        <label class="field-title" for="CommunicationIsCustomer"><?php echo __('Include signature'); ?></label>
        <?php echo $this->Form->input('Communication.include_signature', array(
                'type'      => 'checkbox',
        )); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationTownCity"><?php echo __('Address'); ?></label>
        <div data-role="collapsible">
            <h1>Address Detail</h1>
           
            <ul data-role="listview" data-theme="a">
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 1</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php 
                            if (isset($this->data['Communication']['contact_address'][ "0"]['contact_address_1']))
                            echo $this->Form->input('Communication.contact_address.0.contact_address_1', array(
                                'value' => $this->data['Communication']['contact_address'][ "0"]['contact_address_1'],
                            )); else
                            echo $this->Form->input('Communication.contact_address.0.contact_address_1', array(
                                'value' => '',
                            )); 
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 2</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            if (isset($this->data['Communication']['contact_address'][ "0"]['contact_address_2'])) 
                            echo $this->Form->input('Communication.contact_address.0.contact_address_2', array(
                                'value' => $this->data['Communication']['contact_address']["0"]['contact_address_2'],
                        )); else
                            echo $this->Form->input('Communication.contact_address.0.contact_address_2', array(
                                'value' => '',
                            ));  
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Address 3</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php 
                            if (isset($this->data['Communication']['contact_address'][ "0"]['contact_address_3'])) 
                            echo $this->Form->input('Communication.contact_address.0.contact_address_3', array(
                                'value' => $this->data['Communication']['contact_address']["0"]['contact_address_3'],
                        )); else
                            echo $this->Form->input('Communication.contact_address.0.contact_address_3', array(
                                'value' => '',
                            ));  
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Town / City</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            if (isset($this->data['Communication']['contact_address'][ "0"]['contact_town_city']))  
                            echo $this->Form->input('Communication.contact_address.0.contact_town_city', array(
                                'value' => $this->data['Communication']['contact_address']["0"]['contact_town_city'],
                        )); else
                            echo $this->Form->input('Communication.contact_address.0.contact_town_city', array(
                                'value' => '',
                            ));   
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Province / State</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            if (isset($this->data['Communication']['contact_address'][ "0"]['contact_province_state_id'])) 
                            echo $this->Form->input('Communication.contact_address.0.contact_province_state_id', array(
                                    'value' => $this->data['Communication']['contact_address']["0"]['contact_province_state_id'],
                                    'options' => $options['provinces'][$this->data['Communication']['contact_address']["0"]['contact_country_id']],
                                    'empty' => '',
                            )); else
                            echo $this->Form->input('Communication.contact_address.0.contact_province_state_id', array(
                                'value' => '',
                            ));  
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Zip / Post code</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php 
                            if (isset($this->data['Communication']['contact_address'][ "0"]['contact_zip_postcode'])) 
                            echo $this->Form->input('Communication.contact_address.0.contact_zip_postcode', array(
                                'value' => $this->data['Communication']['contact_address']["0"]['contact_zip_postcode'],
                        )); else
                            echo $this->Form->input('Communication.contact_address.0.contact_zip_postcode', array(
                                'value' => '',
                            ));   
                        ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:30%"><label>Country</label></div>
                    <div class="ui-block-b" style="width:70%">
                        <?php
                            if (isset($this->data['Communication']['contact_address'][ "0"]['contact_country_id']))  
                            echo $this->Form->input('Communication.contact_address.0.contact_country_id', array(
                                'options' => $options['countries'],
                                'value' => $this->data['Communication']['contact_address']["0"]['contact_country_id'],
                        )); else
                            echo $this->Form->input('Communication.contact_address.0.contact_country_id', array(
                                'value' => '',
                            ));  
                        ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationPhone"><?php echo __('Phone'); ?></label>
        <?php echo $this->Form->input('Communication.phone', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationFax"><?php echo __('Fax'); ?></label>
        <?php echo $this->Form->input('Communication.fax', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationEmail"><?php echo __('Email'); ?></label>
        <?php echo $this->Form->input('Communication.email', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationJobName"><?php echo __('Job'); ?></label>
        <?php
                echo $this->Form->input('Communication.job_number', array(
                    'type'=>'hidden'
                ));
                echo $this->Form->input('Communication.job_name', array(
                    'readonly' => true,
                    'class' => 'popup-input',
                    'data-popup-controller' => 'jobs',
                    'data-popup-key' => 'job_name'
                ));
                echo $this->Form->input('Communication.job_id', array(
                    'type'=>'hidden'
                ));
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationPages"><?php echo __('Pages'); ?></label>
        <?php echo $this->Form->input('Communication.pages', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationIdentity"><?php echo __('Identity'); ?></label>
        <?php echo $this->Form->input('Communication.identity', array(

        )); ?>
    </div>

</form>
<?php echo $this->element('../communications/js'); ?>