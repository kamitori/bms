<form class="<?php echo $controller; ?>_form_auto_save" id="form_job_<?php echo $this->data['Job']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Job']['_id']; ?>" />
    <?php echo $this->Form->hidden('Job._id', array('value' => (string)$this->data['Job']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="JobNo"><?php echo __('Job no'); ?></label>
        <?php echo $this->Form->input('Job.no', array(
				'readonly' 	=> 'true',
				'class' 	=> 'jobField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="JobName"><?php echo __('Job name'); ?></label>
        <?php echo $this->Form->input('Job.name', array(
				'class' 	=> 'jobField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="JobCompanyName"><?php echo __('Company'); ?></label>
		<?php
				echo $this->Form->input('Job.company_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'companies',
						'data-popup-key' => 'company_name'
				));
				echo $this->Form->input('Job.company_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="JobContactName"><?php echo __('Contact'); ?></label>
		<?php
				echo $this->Form->input('Job.contact_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'contact_name',
						'data-popup-param' => '?company_id='.$this->data['Job']['company_id'].'&company_name='.$this->data['Job']['company_name'].'&is_employee=1',
				));
				echo $this->Form->input('Job.contact_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="WorkStart"><?php echo __('Start date'); ?></label>
		<div>
			<div class="ui-block-a" style="width: 100%">
	        	<input type="text" name="data[Job][work_start]" class="date-picker" id="work_start_<?php echo $this->data['Job']['_id'] ?>" readonly value="<?php echo $this->data['Job']['work_start']  ?>"/>
	    	</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="WorkEnd"><?php echo __('Finish date'); ?></label>
		<div>
			<div class="ui-block-a" style="width: 100%">
	        	<input type="text" name="data[Job][work_end]" class="date-picker"  id="work_end_<?php echo $this->data['Job']['_id'] ?>" readonly value="<?php echo $this->data['Job']['work_end']  ?>" />
	    	</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="JobType"><?php echo __('Job Type'); ?></label>
        <?php echo $this->Form->input('Job.type', array(
            	'type'=>'select',
				'options' => $arr_jobs_type,
		)); ?>
		<?php echo $this->Form->input('Job.type_id', array(
			'type' => 'hidden',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="JobStatus"><?php echo __('Job Status'); ?></label>
        <?php echo $this->Form->input('Job.status', array(
            	'type'=>'select',
				'options' => $arr_jobs_status,
		)); ?>
		<?php echo $this->Form->input('Job.status_id', array(
			'type' => 'hidden',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="Email"><?php echo __('Email'); ?></label>
        <?php echo $this->Form->input('Job.email', array(
				'class' 	=> 'jobField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="company_phone"><?php echo __('Company phone'); ?></label>
        <?php echo $this->Form->input('Job.company_phone', array(
				'class' 	=> 'jobField',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="DirectPhone"><?php echo __('Direct Phone'); ?></label>
        <?php echo $this->Form->input('Job.direct_phone', array(
				'class' 	=> 'jobField',
		)); ?>
    </div>

     <div class="ui-field-contain">
        <label class="field-title" for="mobile"><?php echo __('Mobile'); ?></label>
        <?php echo $this->Form->input('Job.mobile', array(
				'class' 	=> 'jobField',
		)); ?>
    </div>


	<div class="ui-field-contain">
        <label class="field-title" for="fax"><?php echo __('Fax'); ?></label>
        <?php echo $this->Form->input('Job.fax', array(
				'class' 	=> 'jobField',
		)); ?>
    </div>

	<div class="ui-field-contain">
        <label class="field-title" for="CustomPoNo"><?php echo __('Custom PO no'); ?></label>
        <?php echo $this->Form->input('Job.custom_po_no', array(
				'class' 	=> 'jobField',
		)); ?>
    </div>



	<?php echo $this->Form->input('Job.salesorder_id', array(
       	'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Job.our_rep_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Job.enquiry_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Job.quotation_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Job.job_id', array(
	   'type'=>'hidden'
	)); ?>
	<?php echo $this->Form->input('Job.purchaseorder_id', array(
		'type'=>'hidden'
	)); ?>
</form>
<?php echo $this->element('../Jobs/js'); ?>