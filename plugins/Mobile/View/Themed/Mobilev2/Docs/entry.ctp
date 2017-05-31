<form class="<?php echo $controller; ?>_form_auto_save" id="form_doc_<?php echo $this->data['Doc']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Doc']['_id']; ?>" />
    <?php echo $this->Form->hidden('Doc._id', array('value' => (string)$this->data['Doc']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="DocNo"><?php echo __('Ref no'); ?></label>
        <?php echo $this->Form->input('Doc.no', array(
				'readonly' 	=> 'true',
				'class' 	=> 'docField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DocName"><?php echo __('Document name'); ?></label>
        <?php echo $this->Form->input('Doc.name', array(
				'class' 	=> 'docField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DocLocation"><?php echo __('Location'); ?></label>
        <?php echo $this->Form->input('Doc.location', array(
				'class' 	=> 'docField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DocDescription"><?php echo __('Description'); ?></label>
        <?php echo $this->Form->input('Doc.description', array(
				'class' 	=> 'docField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DocCategory"><?php echo __('Category'); ?></label>
        <?php echo $this->Form->input('Doc.category', array(
            	'type'=>'select',
				'options' => $arr_docs_category,
		)); ?>
		<?php echo $this->Form->input('Doc.category_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DocType"><?php echo __('Doc Type'); ?></label>
        <?php echo $this->Form->input('Doc.type', array(
            	'type'=>'select',
				'options' => $arr_docs_type,
		)); ?>
		<?php echo $this->Form->input('Doc.type_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

     <div class="ui-field-contain">
        <label class="field-title" for="DocExt"><?php echo __('Extension'); ?></label>
        <?php echo $this->Form->input('Doc.ext', array(
				'class' 	=> 'docField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DocCreate_by_module"><?php echo __('Create by module'); ?></label>
        <?php echo $this->Form->input('Doc.create_by_module', array(
				'class' 	=> 'docField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DocModule_detail"><?php echo __('Module detail'); ?></label>
        <?php echo $this->Form->input('Doc.module_detail', array(
				'class' 	=> 'docField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DocNote"><?php echo __('Note'); ?></label>
        <?php echo $this->Form->input('Doc.note', array(
				'class' 	=> 'docField'
		)); ?>
    </div>


	<?php echo $this->Form->input('Doc.salesorder_id', array(
       	'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Doc.our_rep_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Doc.enquiry_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Doc.quotation_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Doc.doc_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Doc.purchaseorder_id', array(
		'type'=>'hidden'
	)); ?>
</form>
<?php echo $this->element('../Docs/js'); ?>