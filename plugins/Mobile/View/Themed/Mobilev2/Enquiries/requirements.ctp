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
        <label class="field-title" for="MarkupRate"><?php echo __('Requirements'); ?></label>
        <?php echo $this->Form->input('Enquiry.detail', array(
				'class' 	=> 'enquiryField',
		)); ?>
    </div>


</form>
<script type="text/javascript">
	/*$(function() {
        enquirys_update_entry_header("<?php echo $this->data['Enquiry']['_id']; ?>");
    });*/
</script>
<?php echo $this->element('../Enquiries/js'); ?>