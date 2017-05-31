<form class="<?php echo $controller; ?>_form_auto_save" id="form_task_<?php echo $this->data['Task']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Task']['_id']; ?>" />
    <?php echo $this->Form->hidden('Task._id', array('value' => (string)$this->data['Task']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="TaskNo"><?php echo __('Task no'); ?></label>
        <?php echo $this->Form->input('Task.no', array(
				'readonly' 	=> 'true',
		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="TaskName"><?php echo __('Name'); ?></label>
        <?php echo $this->Form->input('Task.name', array(
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="TaskType"><?php echo __('Task type'); ?></label>
        <?php echo $this->Form->input('Task.type', array(
            	'type'=>'select',
				'options' => $arr_tasks_type,
		)); ?>
		<?php echo $this->Form->input('Task.type_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="TaskOurRep"><?php echo __('Responsible'); ?></label>
		<?php
				echo $this->Form->input('Task.our_rep', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'our_rep',
						'data-popup-param' => '?is_employee=1'
				));
				echo $this->Form->input('Task.our_rep_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
    	<?php for ($i=0; $i < 24; $i++) {
			$j = $i;
			if($j < 10)$j = '0'.$j;
			if($i > 7 && $i < 18){
				$arr_hour[$j.':00'] = array('name'=> $j.':00', 'value'=> $j.':00', 'class'=>'BgOptionHour');
				$arr_hour[$j.':30'] = array('name'=> $j.':30', 'value'=> $j.':30', 'class'=>'BgOptionHour');
			}else{
				$arr_hour[$j.':00'] = $j.':00';
				$arr_hour[$j.':30'] = $j.':30';
			}
		}
		?>
    	<label class="field-title" for="<?php echo 'work_start_'.$this->data['Task']['_id']; ?>"><?php echo __('Work start'); ?></label>
	    <div>
	        <div class="ui-block-a" style="width: 60%">
	        	<input type="text" name="data[Task][work_start]" class="date-picker" id="work_start_<?php echo $this->data['Task']['_id'] ?>" readonly value="<?php echo $this->data['Task']['work_start']  ?>"/>
	    	</div>
	    	<div class="ui-block-b" style="width: 40%">
	            <?php echo $this->Form->input('Task.work_start_hour', array(
					'options' => $arr_hour
				)); ?>
	    	</div>
	    </div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="<?php echo 'work_end'.$this->data['Task']['_id']; ?>"><?php echo __('Work end'); ?></label>
        <div>
	        <div class="ui-block-a" style="width: 60%">
        		<input type="text" name="data[Task][work_end]" class="date-picker"  id="work_end_<?php echo $this->data['Task']['_id'] ?>" readonly value="<?php echo $this->data['Task']['work_end']  ?>" />
	    	</div>
	    	<div class="ui-block-b" style="width: 40%">
	    		<?php echo $this->Form->input('Task.work_end_hour', array(
					'options' => $arr_hour
				)); ?>
	    	</div>
	    </div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="<?php echo 'work_end'.$this->data['Task']['_id']; ?>"><?php echo __('Priority'); ?></label>
        <?php echo $this->Form->input('Task.priority', array(
				'options' => $arr_priority
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="TaskStatus"><?php echo __('Status'); ?></label>
        <?php echo $this->Form->input('Task.status', array(
            	'type'=>'select',
				'options' => $arr_tasks_status,
		)); ?>
		<?php echo $this->Form->input('Task.status_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="<?php echo 'tasks_days_left_'.$this->data['Task']['_id']; ?>"><?php echo __('Days left'); ?></label>
        <?php echo $this->Form->input('Task.day_left', array(
				'id'		=> 'tasks_days_left_'.$this->data['Task']['_id'],
				'readonly' => true
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="TaskCompanyName"><?php echo __('Company'); ?></label>
		<?php
				echo $this->Form->input('Task.company_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'companies',
						'data-popup-key' => 'company_name'
				));
				echo $this->Form->input('Task.company_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>
    
    <div class="ui-field-contain">
        <label class="field-title" for="TaskContactName"><?php echo __('Contact'); ?></label>
		<?php
				echo $this->Form->input('Task.contact_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'contacts',
						'data-popup-key' => 'contact_name',
						'data-popup-param' => '?is_customer=1'
				));
				echo $this->Form->input('Task.contact_id', array(
		           	'type'=>'hidden'
		        ));
		?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="TaskEnquiryName"><?php echo __('Enquiry'); ?></label>
        <div>
        	<div class="ui-block-a" style="width: 40%">
                <?php 
                    echo $this->Form->input('Task.enquiry_no', array(
                            'readonly' => true,
                    )); ?>
            </div>
            <div class="ui-block-b" style="width: 60%">
				<?php
					echo $this->Form->input('Task.enquiry_name', array(
							'readonly' => true,
							'class' => 'popup-input',
							'data-popup-controller' => 'enquiries',
							'data-popup-key' => 'enquiry_name'
					));
					echo $this->Form->input('Task.enquiry_id', array(
			           	'type'=>'hidden'
			        ));
				?>
			</div>
		</div>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="TaskQuotationName"><?php echo __('Quotation'); ?></label>
        <div>
        	<div class="ui-block-a" style="width: 40%">
                <?php 
                    echo $this->Form->input('Task.quotation_no', array(
                            'readonly' => true,
                    )); ?>
            </div>
            <div class="ui-block-b" style="width: 60%">
				<?php
					echo $this->Form->input('Task.quotation_name', array(
							'readonly' => true,
							'class' => 'popup-input',
							'data-popup-controller' => 'quotations',
							'data-popup-key' => 'quotation_name'
					));
					echo $this->Form->input('Task.quotation_id', array(
			           	'type'=>'hidden'
			        ));
				?>
			</div>  
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="TaskJobName"><?php echo __('Job'); ?></label>
        <div>
        	<div class="ui-block-a" style="width: 40%">
                <?php 
                echo $this->Form->input('Task.job_no', array(
                        'readonly' => true,
                )); ?>
            </div>

            <div class="ui-block-b" style="width: 60%">
				<?php
				echo $this->Form->input('Task.job_name', array(
					'readonly' => true,
					'class' => 'popup-input',
					'data-popup-controller' => 'jobs',
					'data-popup-key' => 'job_name'
				));
				echo $this->Form->input('Task.job_id', array(
		           	'type'=>'hidden'
		        ));
				?>
			</div>  
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="TaskSalesorder_name"><?php echo __('Salesorder Name'); ?></label>
        <div>
         	<div class="ui-block-a" style="width: 40%">
     			<?php 
                echo $this->Form->input('Task.salesorder_no', array(
                        'readonly' => true,
                )); ?>
                
            </div>
            <div class="ui-block-b" style="width: 60%">
				<?php
				echo $this->Form->input('Task.salesorder_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'salesorders',
						'data-popup-key' => 'salesorder_name'
				));
				echo $this->Form->input('Task.salesorder_id', array(
		           	'type'=>'hidden'
		        ));
				?>
			</div>
		</div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="TaskPurchaseorderName"><?php echo __('Purchase Order'); ?></label>
        <div>
        	<div class="ui-block-a" style="width: 40%">
     			<?php 
                echo $this->Form->input('Task.purchaseorder_no', array(
                        'readonly' => true,
                )); ?>
            </div>
            <div class="ui-block-b" style="width: 60%">
				<?php
				echo $this->Form->input('Task.purchaseorder_name', array(
						'readonly' => true,
						'class' => 'popup-input',
						'data-popup-controller' => 'purchaseorders',
						'data-popup-key' => 'purchaseorder_name'
				));
				echo $this->Form->input('Task.purchaseorder_id', array(
		           	'type'=>'hidden'
		        ));
				?>
			</div>
		</div>
    </div>


	<?php echo $this->Form->input('Task.our_rep_id', array(
	   'type'=>'hidden'
	)); ?>
</form>
<?php echo $this->element('../Tasks/js'); ?>