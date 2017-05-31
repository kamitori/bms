<?php
	//pr($arr_enquiry_keywords);die;
?>	
<form class="<?php echo $controller; ?>_form_auto_save" id="form_enquiry_<?php echo $this->data['Enquiry']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Enquiry']['_id']; ?>" />
    <?php echo $this->Form->hidden('Enquiry._id', array('value' => (string)$this->data['Enquiry']['_id'])); ?>

  	<label class="field-title" for="EnquiryKeywords"><?php echo __('Keywords'); ?></label>
    <div class="ui-field-contain">
        
        <?php echo $this->Form->input('Enquiry.keywords.0', array(
            	'type'=>'select',
				'options' => $arr_enquiry_keywords,
		)); ?>

		<?php echo $this->Form->input('Enquiry.keywords.1', array(
            	'type'=>'select',
				'options' => $arr_enquiry_keywords,
		)); ?>

		<?php echo $this->Form->input('Enquiry.keywords.2', array(
            	'type'=>'select',
				'options' => $arr_enquiry_keywords,
		)); ?>

		<?php echo $this->Form->input('Enquiry.keywords.3', array(
            	'type'=>'select',
				'options' => $arr_enquiry_keywords,
		)); ?>

		<?php echo $this->Form->input('Enquiry.keywords.4', array(
            	'type'=>'select',
				'options' => $arr_enquiry_keywords,
		)); ?>

		<?php echo $this->Form->input('Enquiry.keywords.5', array(
            	'type'=>'select',
				'options' => $arr_enquiry_keywords,
		)); ?>

		<?php echo $this->Form->input('Enquiry.keywords.6', array(
            	'type'=>'select',
				'options' => $arr_enquiry_keywords,
		)); ?>

		<?php echo $this->Form->input('Enquiry.keywords.7', array(
            	'type'=>'select',
				'options' => $arr_enquiry_keywords,
		)); ?>
    </div>

</form>
<?php echo $this->element('../Enquiries/js'); ?>
