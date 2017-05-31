<form class="<?php echo $controller; ?>_form_auto_save" id="form_receipt_<?php echo $this->data['Receipt']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Receipt']['_id']; ?>" />
    <?php echo $this->Form->hidden('Receipt._id', array('value' => (string)$this->data['Receipt']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="ReceiptCode"><?php echo __('Ref code'); ?></label>
        <?php echo $this->Form->input('Receipt.code', array(
				'readonly' 	=> 'true',
				'class' 	=> 'receiptField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ReceiptName"><?php echo __('Amount Received'); ?></label>
        <?php echo $this->Form->input('Receipt.amount_received', array(
                'readonly'  =>  true,
				'class' 	=> 'receiptField'
		)); ?>
    </div>

    
    <div class="ui-field-contain">
        <label class="field-title" for="ReceiptSalesaccountName"><?php echo __('Customer Account'); ?></label>
        <?php
            echo $this->Form->input('Receipt.salesaccount_name', array(
                    'readonly' => true,
                    'class' => 'popup-input',
                    'data-popup-controller' => 'salesaccounts',
                    'data-popup-key' => 'salesaccount_name'
            ));
            echo $this->Form->input('Receipt.salesaccount_id', array(
                     'type'=>'hidden'
            ));
        ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="ReceiptReceiptDate"><?php echo __('Date'); ?></label>
        <?php echo $this->Form->input('Receipt.receipt_date', array(
				'class' 	=> 'receiptField date-picker',
                'readonly'  =>  true,
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ReceiptPaidBy"><?php echo __('Paid by'); ?></label>
        <?php echo $this->Form->input('Receipt.paid_by', array(
            	'type'=>'select',
				'options' => $arr_receipts_paid_by,
		)); ?>
		<?php echo $this->Form->input('Receipt.category_id', array(
			'type' => 'hidden',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ReceiptType"><?php echo __('Our bank account'); ?></label>
        <div>
	        <div class="ui-block-a" style="width: 60%">
		        <?php echo $this->Form->input('Receipt.type', array(
		            	'type'=>'select',
						'options' => $arr_receipts_our_bank_account,
				)); ?>
			</div>
			<div class="ui-block-b" style="width: 40%">
				<?php echo $this->Form->input('Receipt.our_bank_account', array(
                        'readonly' => true,
				)); ?>
	    	</div>
	    </div>
    </div>

     <div class="ui-field-contain">
        <label class="field-title" for="ReceiptExt"><?php echo __('Reference'); ?></label>
        <?php echo $this->Form->input('Receipt.name', array(
				'class' 	=> 'receiptField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ReceiptOurRep"><?php echo __('Our rep'); ?></label>
        <?php
                echo $this->Form->input('Receipt.our_rep', array(
                        'readonly' => true,
                        'class' => 'popup-input',
                        'data-popup-controller' => 'contacts',
                        'data-popup-key' => 'our_rep',
                        'data-popup-param' => '?is_employee=1'
                ));
                echo $this->Form->input('Receipt.our_rep_id', array(
                    'type'=>'hidden'
                ));
        ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ReceiptOurCsr"><?php echo __('Our csr'); ?></label>
        <?php
                echo $this->Form->input('Receipt.our_csr', array(
                        'readonly' => true,
                        'class' => 'popup-input',
                        'data-popup-controller' => 'contacts',
                        'data-popup-key' => 'our_csr',
                        'data-popup-param' => '?is_employee=1'
                ));
                echo $this->Form->input('Receipt.our_csr_id', array(
                    'type'=>'hidden'
                ));
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ReceiptIdentity"><?php echo __('Identity'); ?></label>
        <?php echo $this->Form->input('Receipt.identity', array(
				'class' 	=> 'receiptField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ReceiptUseOwnLetterhead"><?php echo __('active'); ?></label>
    	<label class="field-title" for="ReceiptUseOwnLetterhead"><?php echo __('Use own letterhead'); ?></label>
        <?php echo $this->Form->input('Receipt.use_own_letterhead', array(
            	'type'		=> 'checkbox',
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ReceiptExtAccountsSync"><?php echo __('Approved'); ?></label>
    	<label class="field-title" for="ReceiptExtAccountsSync"><?php echo __('Ext Accounts sync'); ?></label>
        <?php echo $this->Form->input('Receipt.ext_accounts_sync', array(
            	'type'		=> 'checkbox',
		)); ?>
    </div>


    <div class="ui-field-contain">
        <label class="field-title" for="ReceiptNotes"><?php echo __('Notes'); ?></label>
        <?php echo $this->Form->input('Receipt.notes', array(
				'class' 	=> 'receiptField'
		)); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ReceiptComments"><?php echo __('Comments on receipt'); ?></label>
        <?php echo $this->Form->input('Receipt.comments', array(
				'class' 	=> 'receiptField'
		)); ?>
    </div>





	<?php echo $this->Form->input('Receipt.receipt_id', array(
	   'type'=>'hidden'
	)); ?>

</form>
<?php echo $this->element('../Receipts/js'); ?>