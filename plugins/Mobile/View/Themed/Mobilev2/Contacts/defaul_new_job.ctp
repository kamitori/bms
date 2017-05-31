<form class="<?php echo $controller; ?>_form_auto_save" id="form_contact_<?php echo $this->data['Contact']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Contact']['_id']; ?>" />
    <?php echo $this->Form->hidden('Contact._id', array('value' => (string)$this->data['Contact']['_id'])); ?>
	

    <div class="ui-field-contain">
        <label class="field-title" for="ContactNo"><?php echo __('Contact no'); ?></label>
        <?php echo $this->Form->input('Contact.no', array(
				'readonly' 	=> 'true',
				'class' 	=> 'contactField'
		)); ?>
    </div>



    <div class="ui-field-contain">
        <label class="field-title" for="MarkupRate"><?php echo __('Markup Rate'); ?></label>
        <?php echo $this->Form->input('Contact.markup_rate', array(
				'class' 	=> 'contactField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="Rateperhour"><?php echo __('Rate per hour'); ?></label>
        <?php echo $this->Form->input('Contact.rate_per_hour', array(
				'class' 	=> 'contactField',
		)); ?>
    </div>




</form>
<script type="text/javascript">
	/*$(function() {
        contacts_update_entry_header("<?php echo $this->data['Contact']['_id']; ?>");
    });*/
</script>
<?php echo $this->element('../Contacts/js'); ?>