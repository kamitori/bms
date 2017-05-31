<?php echo $this->element('entry_tab_option'); ?>
<script type="text/javascript">
$(function(){
	input_show_select_calendar(".JtSelectDate", "#form_task_<?php echo $this->data['Task']['_id']; ?>");
})
</script>
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Task']['_id']; ?>" />
	<form class="<?php echo $controller; ?>_form_auto_save" id="form_task_<?php echo $this->data['Task']['_id']; ?>">
	<div class="table_nd">
	  <div class="ui-grid-a">
	    <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('Task no'); ?>
	      </div>
	      <div class="ui-block-b">
	      	<?php echo $this->Form->hidden('Task._id', array('value' => (string)$this->data['Task']['_id'])); ?>
	        <?php echo $this->Form->input('Task.no', array(
					'readonly' => 'true'
			)); ?>
			<p class="clear"></p>
	      </div>
	    </div>
	    <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('Heading'); ?>
	      </div>
	      <div class="ui-block-b">
	      	<?php echo $this->Form->input('Task.name', array(
			)); ?>
	      </div>
	    </div>
	    <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('Status'); ?>
	      </div>
	      <div class="ui-block-b">
	        <?php echo $this->Form->input('Task.status', array(
	            'type'=>'select',
				'options' => $arr_tasks_status,
			)); ?>
			<?php echo $this->Form->input('Task.status_id', array(
				'type' => 'hidden',
			)); ?>
	      </div>
	    </div>
	    <div class="bo_bottom"></div>
	     <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('Task type'); ?>
	      </div>
	      <div class="ui-block-b">
	        <?php echo $this->Form->input('Task.type', array(
                   'type'=>'select',
                   'options'=>$arr_tasks_type,
			)); ?>
			<?php echo $this->Form->input('Task.type_id', array(
				'type' => 'hidden',
			)); ?>
	      </div>
	    </div>
	    <div class="indent_block">
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
	      <div class="ui-block-a">
	        <?php echo __('Work start'); ?>
	      </div>
	      <div class="ui-block-b">
	        <?php echo $this->Form->input('Task.work_start', array(
				'class' => 'JtSelectDate',
				'id'	=> 'work_start_'.$this->data['Task']['_id'],
				'readonly' => true
			)); ?>
			<?php echo $this->Form->input('Task.work_start_hour', array(
				'class' => 'force_reload',
				'options' => $arr_hour
			)); ?>
	      </div>
	    </div>
	    <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('Work end'); ?>
	      </div>
	      <div class="ui-block-b">
	        <?php
	        echo $this->Form->input('Task.work_end', array(
				'class' => 'JtSelectDate',
				'id'=>'work_end_'.$this->data['Task']['_id'],
				'readonly' => true
			));
			?>
			<?php echo $this->Form->input('Task.work_end_hour', array(
				'class' => 'force_reload',
				'options' => $arr_hour
			)); ?>
	      </div>
	    </div>
	    <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('Priority'); ?>
	      </div>
	      <div class="ui-block-b">
	        <?php echo $this->Form->input('Task.priority', array(
				'options' => $arr_priority
			)); ?>
	      </div>
	    </div>

	    <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('Days left'); ?>
	      </div>
	      <div class="ui-block-b">
	        <input id="tasks_days_left_<?php echo $this->data['Task']['_id']; ?>"  type="text" value="" readonly="true">
	      </div>
	    </div>
	    <div class="indent_block">
	      <div class="ui-block-a indent_test">
	        <?php echo __('Company'); ?>
	        <div class="link_pop">
	        	<a href="#companies_popup" rel="TaskCompanyName" data-transition="flow" class="show_cp"></a>
	        </div>
	      </div>
	      <div class="ui-block-b">
	        <?php echo $this->Form->input('Task.company_name', array(
			)); ?>
			<?php echo $this->Form->input('Task.company_id', array(
			           'type'=>'hidden'
			)); ?>
			<?php if(!$request->is('ajax')){ ?>
			<script type="text/javascript">
		    	$(function(){
		    		window_popup('companies','Specify Company','companies_popup');
		    	})
		    </script>
		    <?php }?>
	      </div>
	    </div>
	    <div class="indent_block">
	      <div class="ui-block-a indent_test">
	        <?php echo __('Contact'); ?>
	        <div class="link_pop">
	        	<a href="#contacts_popup" rel="TaskContactName" data-transition="flow" class="show_cp"></a>
	        </div>
	      </div>
	      <div class="ui-block-b">
	        <?php echo $this->Form->input('Task.contact_name', array(
			)); ?>
			<?php echo $this->Form->input('Task.contact_id', array(
			           'type'=>'hidden'
			)); ?>
			<?php if(!$request->is('ajax')){ ?>
			<script type="text/javascript">
		    	$(function(){
		    		window_popup('contacts','Specify Contact','contacts_popup');
		    	})
		    </script>
		    <?php }?>
	      </div>
	    </div>
	    <div class="indent_block">
	      <div class="ui-block-a indent_test">
	        <?php echo __('Responsible'); ?>
	        <div class="link_pop">
	        	<a href="#equipments_popup" rel="TaskOurRep" data-transition="flow" class="show_cp"></a>
	        </div>
	      </div>
	      <div class="ui-block-b">
	        <?php echo $this->Form->input('Task.our_rep', array(
			)); ?>
			<?php echo $this->Form->input('Task.our_rep_id', array(
			           'type'=>'hidden'
			)); ?>
			<?php if(!$request->is('ajax')){ ?>
			<script type="text/javascript">
		    	$(function(){
		    		window_popup('equipments','Specify Asset','equipments_popup');
		    	})
		    </script>
		    <?php }?>
	      </div>
	    </div>
	    <!-- Minh -->
	    <div class="indent_block clearfix height_indent"></div>
	  </div>
	</div>
	<?php echo $this->Form->input('Task.salesorder_id', array(
			           'type'=>'hidden'
			)); ?>
	<?php echo $this->Form->input('Task.our_rep_id', array(
			           'type'=>'hidden'
			)); ?>
	<?php echo $this->Form->input('Task.enquiry_id', array(
			           'type'=>'hidden'
			)); ?>
	<?php echo $this->Form->input('Task.quotation_id', array(
			           'type'=>'hidden'
			)); ?>
	<?php echo $this->Form->input('Task.job_id', array(
			           'type'=>'hidden'
			)); ?>
	<?php echo $this->Form->input('Task.purchaseorder_id', array(
	           'type'=>'hidden'
	)); ?>
	</form>
<script type="text/javascript">
	$(function() {
        tasks_update_entry_header("<?php echo $this->data['Task']['_id']; ?>");
    });
</script>