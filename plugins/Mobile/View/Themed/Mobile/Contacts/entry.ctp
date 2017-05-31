<?php echo $this->element('entry_tab_option'); ?>
<script type="text/javascript">
$(function(){
	input_show_select_calendar(".JtSelectDate", "#form_contact_<?php echo $this->data['Contact']['_id']; ?>");
})
</script>
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Contact']['_id']; ?>" />
	<form class="<?php echo $controller; ?>_form_auto_save" id="form_contact_<?php echo $this->data['Contact']['_id']; ?>">
	<div class="table_nd">
	  <div class="ui-grid-a">
	    <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('Contact no 111'); ?>
	      </div>
	      <div class="ui-block-b">
	      	<?php echo $this->Form->hidden('Contact._id', array('value' => (string)$this->data['Contact']['_id'])); ?>
	        <?php echo $this->Form->input('Contact.no', array(
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
	      	<?php echo $this->Form->input('Contact.name', array(
			)); ?>
	      </div>
	    </div>
	    <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('Status'); ?>
	      </div>
	      <div class="ui-block-b">
	        <?php echo $this->Form->input('Contact.status', array(
	            'type'=>'select',
				'options' => $arr_contacts_status,
			)); ?>
			<?php echo $this->Form->input('Contact.status_id', array(
				'type' => 'hidden',
			)); ?>
	      </div>
	    </div>

	    <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('First name 111'); ?>
	      </div>
	      <div class="ui-block-b">
	    		 	<?php echo $this->Form->hidden('Contact._id', array('value' => (string)$this->data['Contact']['_id'])); ?>
			        <?php echo $this->Form->input('Contact.first_name', array(
					)); ?>
					<p class="clear"></p>
	      </div>
	    </div>


	     <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('First name 111'); ?>
	      </div>
	      <div class="ui-block-b">
	    		 	<?php echo $this->Form->hidden('Contact._id', array('value' => (string)$this->data['Contact']['_id'])); ?>
			        <?php echo $this->Form->input('Contact.last_name', array(
					)); ?>
					<p class="clear"></p>
	      </div>
	    </div>

	    <div class="bo_bottom"></div>
	     <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('Contact type'); ?>
	      </div>
	      <div class="ui-block-b">
	        <?php echo $this->Form->input('Contact.type', array(
                   'type'=>'select',
                   'options'=>$arr_contacts_type,
			)); ?>
			<?php echo $this->Form->input('Contact.type_id', array(
				'type' => 'hidden',
			)); ?>
	      </div>
	    </div>


	    <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('Priority'); ?>
	      </div>
	      <div class="ui-block-b">
	        <?php echo $this->Form->input('Contact.priority', array(
				'options' => $arr_priority
			)); ?>
	      </div>
	    </div>

	    <div class="indent_block">
	      <div class="ui-block-a">
	        <?php echo __('Days left'); ?>
	      </div>
	      <div class="ui-block-b">
	        <input id="contacts_days_left_<?php echo $this->data['Contact']['_id']; ?>"  type="text" value="" readonly="true">
	      </div>
	    </div>
	    <div class="indent_block">
	      <div class="ui-block-a indent_test">
	        <?php echo __('Company'); ?>
	        <div class="link_pop">
	        	<a href="#companies_popup" rel="ContactCompanyName" data-transition="flow" class="show_cp"></a>
	        </div>
	      </div>
	      <div class="ui-block-b">
	        <?php echo $this->Form->input('Contact.company_name', array(
			)); ?>
			<?php echo $this->Form->input('Contact.company_id', array(
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
	        	<a href="#contacts_popup" rel="ContactContactName" data-transition="flow" class="show_cp"></a>
	        </div>
	      </div>
	      <div class="ui-block-b">
	        <?php echo $this->Form->input('Contact.contact_name', array(
			)); ?>
			<?php echo $this->Form->input('Contact.contact_id', array(
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
	        	<a href="#equipments_popup" rel="ContactOurRep" data-transition="flow" class="show_cp"></a>
	        </div>
	      </div>
	      <div class="ui-block-b">
	        <?php echo $this->Form->input('Contact.our_rep', array(
			)); ?>
			<?php echo $this->Form->input('Contact.our_rep_id', array(
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
	<?php echo $this->Form->input('Contact.salesorder_id', array(
			           'type'=>'hidden'
			)); ?>
	<?php echo $this->Form->input('Contact.our_rep_id', array(
			           'type'=>'hidden'
			)); ?>
	<?php echo $this->Form->input('Contact.enquiry_id', array(
			           'type'=>'hidden'
			)); ?>
	<?php echo $this->Form->input('Contact.quotation_id', array(
			           'type'=>'hidden'
			)); ?>
	<?php echo $this->Form->input('Contact.job_id', array(
			           'type'=>'hidden'
			)); ?>
	<?php echo $this->Form->input('Contact.purchaseorder_id', array(
	           'type'=>'hidden'
	)); ?>
	</form>
<script type="text/javascript">
	$(function() {
        contacts_update_entry_header("<?php echo $this->data['Contact']['_id']; ?>");
    });
</script>