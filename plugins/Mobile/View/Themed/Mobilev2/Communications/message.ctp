<form class="<?php echo $controller; ?>_form_auto_save" id="form_company_<?php //echo $this->data['Company']['_id']; ?>">
    <input type="hidden" id="mongoid" value="<?php //echo $this->data['Company']['_id']; ?>" />
    <?php //echo $this->Form->hidden('Company._id', array('value' => (string)$this->data['Company']['_id'])); ?>
    <div class="ui-field-contain">
        <label class="field-title" for="RefNo"><?php echo __('Ref code'); ?></label>
        <?php echo $this->Form->input('Communication.code', array(
                'readonly'  => 'true',
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationType"><?php echo __('Type'); ?></label>
        <?php echo $this->Form->input('Communication.comms_type', array(
                'readonly'  => 'true',
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationContactFrom"><?php echo __('From'); ?></label>
        <?php 
                echo $this->Form->input('Communication.contact_from', array(
                    'readonly'  => true,
                    'class' => 'popup-input',
                    'data-popup-controller' => 'contacts',
                    'data-popup-key' => 'contact_from'
                )); 
                echo $this->Form->input('Communication.contact_from_id', array(
                    'type'=>'hidden'
                ));
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationContactTo"><?php echo __('To'); ?></label>
        <?php 
                echo $this->Form->input('Communication.contact_to', array(
                        'readonly'  => true,
                        'class' => 'popup-input',
                        'data-popup-controller' => 'contacts',
                        'data-popup-key' => 'contact_to'
                )); 
                echo $this->Form->input('Communication.contact_to_id', array(
                        'type'=>'hidden'
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
        <label class="field-title" for="CommunicationTime"><?php echo __('Time'); ?></label>
        <?php echo $this->Form->input('Communication.time', array(
                'readonly'  => 'true',
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="CommunicationModule"><?php echo __('Module'); ?></label>
        <?php
                echo $this->Form->input('Communication.module', array(
                        'readonly' => true,
                        'class' => 'popup-input',
                        'data-popup-controller' => 'contacts',
                        'data-popup-key' => 'module'
                ));
                echo $this->Form->input('Communication.contact_id', array(
                        'type'=>'hidden'
                ));

        ?>
    </div>

    <div data-role="fieldcontain">
        <label for="textarea">Message text</label>
        <?php echo $this->Form->textarea('Communication.content', array(
                 'name'=>"content",
                 'id'=>"content"
        )); ?>
    </div>
</form>
<?php echo $this->element('../communications/js'); ?>