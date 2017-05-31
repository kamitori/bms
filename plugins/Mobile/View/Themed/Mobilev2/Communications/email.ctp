<?php echo $this->Minify->css(array('font-awesome.min','froala_editor.min')); ?>
<?php echo $this->Minify->script(array('froala_editor.min'));  ?>
<form class="<?php echo $controller; ?>_form_auto_save" id="form_communication_<?php echo $this->data['Communication']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Communication']['_id']; ?>" />
    <?php echo $this->Form->hidden('Communication._id', array('value' => (string)$this->data['Communication']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="RefNo"><?php echo __('Ref no'); ?></label>
        <?php echo $this->Form->input('Communication.code', array(
				'readonly' 	=> 'true',
		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationCommsType"><?php echo __('Type'); ?></label>
        <?php echo $this->Form->input('Communication.comms_type', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationTitle"><?php echo __('Contact:title'); ?></label>
        <?php echo $this->Form->input('Communication.title', array(

        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationContactName"><?php echo __('Contact name'); ?></label>
        <?php
                echo $this->Form->input('Communication.contact_name', array(
                        'readonly' => true,
                        'class' => 'popup-input',
                        'data-popup-controller' => 'contacts',
                        'data-popup-key' => 'contact_name'
                ));
                echo $this->Form->input('Communication.contact_id', array(
                    'type'=>'hidden'
                ));
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationEmail"><?php echo __('Email'); ?></label>
        <?php echo $this->Form->input('Communication.email', array(

        )); ?>
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
                'readonly' => true
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationFrom"><?php echo __('From'); ?></label>
        <?php echo $this->Form->input('Communication.contact_from', array(
                'readonly' => true
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
        <label class="field-title" for="CommunicationName"><?php echo __('Subject'); ?></label>
        <?php echo $this->Form->input('Communication.name', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationIncludeSignature"><?php echo __('Include signature'); ?></label>
        <label class="field-title" for="CommunicationyIsCustomer"><?php echo __('Include signature'); ?></label>
        <?php echo $this->Form->input('Communication.include_signature', array(
                'type'      => 'checkbox',
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationCommsStatus"><?php echo __('Status'); ?></label>
        <?php echo $this->Form->input('Communication.comms_status', array(
            "readonly" => true
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
        <label class="field-title" for="CommunicationToOther"><?php echo __('To Other'); ?></label>
        <?php echo $this->Form->input('Communication.toother', array(

        )); ?>
    </div>

     <div class="ui-field-contain">
        <label class="field-title" for="CommunicationCC"><?php echo __('CC'); ?></label>
        <?php echo $this->Form->input('Communication.email_cc', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationBCC"><?php echo __('BCC'); ?></label>
        <?php echo $this->Form->input('Communication.email_bcc', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationIdentity"><?php echo __('Identity'); ?></label>
        <?php echo $this->Form->input('Communication.identity', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationModule"><?php echo __('Module'); ?></label>
        <?php echo $this->Form->input('Communication.module', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationPhone"><?php echo __('Phone'); ?></label>
        <?php echo $this->Form->input('Communication.phone', array(

        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationIdentity"><?php echo __('Identity'); ?></label>
        <?php echo $this->Form->input('Communication.identity', array(

        )); ?>
    </div>

    <div data-role="fieldcontain">
        <label for="textarea">Email text</label>
         <?php echo $this->Form->input('Communication.content', array(
                //"class" => "selector",
                "id" => "content",
                "name" => "content",
                'type'=>'textarea'
        )); ?>
    </div>

    <div data-role="fieldcontain">
        <label for="textarea">Internal Notes</label>
         <?php echo $this->Form->input('Communication.internal_notes', array(
                "name" => "internal_notes",
                 "id" => "internal_notes",
                'type'=>'textarea'
        )); ?>
    </div>

</form>
<script>
    $(function() {
        $('.selector').editable({
            inlineMode: false,
            buttons: ['undo', 'redo' , 'bold', 'attach', 'clear', 'insertHTML'],
            customButtons: {
                attach : {
                    title : 'Attach file',
                    icon : {
                        type : 'font',
                        value : 'fa fa-file-archive-o'
                    },
                    callback: function (editor){
                        alert ("Hello!")
                    }
                }
            }
        });
    });
</script>
<?php echo $this->element('../Communications/js'); ?>