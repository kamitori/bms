<form class="<?php echo $controller; ?>_form_auto_save" id="form_product_<?php echo $this->data['Product']['_id']; ?>">
	<input type="hidden" id="mongoid" value="<?php echo $this->data['Product']['_id']; ?>" />
    <?php echo $this->Form->hidden('Product._id', array('value' => (string)$this->data['Product']['_id'])); ?>
	<div class="ui-field-contain">
        <label class="field-title" for="ProductCode"><?php echo __('Code'); ?></label>
        <?php echo $this->Form->input('Product.code', array(
				'readonly' 	=> 'true',
                'class'     => 'productField'
		)); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductSku"><?php echo __('SKU'); ?></label>
        <?php echo $this->Form->input('Product.sku', array(
        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductName"><?php echo __('Name'); ?></label>
        <?php echo $this->Form->input('Product.name', array(

        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductDescription"><?php echo __('Description'); ?></label>
        <?php echo $this->Form->input('Product.description', array(

        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductType"><?php echo __('Type'); ?></label>
        <?php 
            echo $this->Form->input('Product.product_type', array(
                'type'      => 'select',
                'options'   => $product_type,
            )); 
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ProductCompanyName"><?php echo __('Current supplier'); ?></label>
        <?php 
            echo $this->Form->input('Product.company_name', array(
                    'readonly'  => true,
                    'class'     => 'popup-input',
                    'data-popup-controller' => 'companies',
                    'data-popup-key' => 'company_name',
                    'data-popup-param' => '?is_supplier=1'
                ));
            echo $this->Form->input('Product.company_id', array(
                    'type'=>'hidden'
                ));
        ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ProductColor"><?php echo __('Color'); ?></label>
        <?php echo $this->Form->input('Product.color', array(
                'type'      => 'select',
                'options'   => $product_color,
        )); ?>
        <?php echo $this->Form->input('Product.colorId', array(
            'type' => 'hidden',
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ProductThickness"><?php echo __('Thickness'); ?></label>
        <div >
            <div style="width:60% !important; float:left; margin-right:1%">
                <?php echo $this->Form->input('Product.thickness', array(
                    'type' => 'number'
                )); ?>
            </div>
            <div style="width:39% !important;">
                <?php echo $this->Form->input('Product.thickness_unit', array(
                    'type'      => 'select',
                    'options'   => $product_oum_size,
                )); ?>
                <?php echo $this->Form->input('Product.thickness_unitId', array(
                    'type' => 'hidden',
                )); ?>
            </div>
        </div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ProductSizew"><?php echo __('Size-W'); ?></label>
        <div >
            <div style="width:60% !important; float:left; margin-right:1%">
                <?php echo $this->Form->input('Product.sizew', array(
                    'type' => 'number'
                )); ?>
            </div>
            <div style="width:39% !important;">
                <?php echo $this->Form->input('Product.sizew_unit', array(
                    'type'      => 'select',
                    'options'   => $product_oum_size,
                )); ?>
                <?php echo $this->Form->input('Product.sizew_unitId', array(
                    'type' => 'hidden',
                )); ?>
            </div>
        </div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ProductSizeh"><?php echo __('Size-H'); ?></label>
        <div >
            <div style="width:60% !important; float:left; margin-right:1%">
                <?php echo $this->Form->input('Product.sizeh', array(
                    'type' => 'number'
                )); ?>
            </div>
            <div style="width:39% !important;">
                <?php echo $this->Form->input('Product.sizeh_unit', array(
                    'type'      => 'select',
                    'options'   => $product_oum_size,
                )); ?>
                <?php echo $this->Form->input('Product.sizeh_unitId', array(
                    'type' => 'hidden',
                )); ?>
            </div>
        </div>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ProductIsCustomSize"><?php echo __('not allowed'); ?></label>
        <label class="field-title" for="ProductIsCustomSize"><?php echo __('Custom-Size'); ?></label>
        <?php echo $this->Form->input('Product.is_custom_size', array(
                'type'      => 'checkbox',
        )); ?>
    </div>



    <div class="ui-field-contain">
        <label class="field-title" for="ProductSellBy"><?php echo __('Sold by'); ?></label>
        <?php echo $this->Form->input('Product.sell_by', array(
                'type'      => 'select',
                'options'   => $product_sell_by,
        )); ?>
        <?php echo $this->Form->input('Product.sell_byId', array(
            'type' => 'hidden',
        )); ?>
    </div>


<?php if ($this->data['Product']['product_type'] == 'Vendor Stock'): ?>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductSellPrice"><?php echo __('Cost price'); ?></label>
        <?php echo $this->Form->input('Product.sell_price', array(
            "type" => "number",
        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductUom"><?php echo __('Purchase OUM'); ?></label>
        <?php echo $this->Form->input('Product.oum', array(
                'type'      => 'select',
                'options'   => $arr_product_oum,
        )); ?>
        <?php echo $this->Form->input('Product.oumId', array(
            'type' => 'hidden',
        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductCostPrice"><?php echo __('Unit price'); ?></label>
        <?php echo $this->Form->input('Product.unit_price', array(
            'type' => 'number'
        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductUom"><?php echo __('UOM'); ?></label>
        <?php echo $this->Form->input('Product.oum_depend', array(
                'type'      => 'select',
                'options'   => $arr_oum_depend,
        )); ?>
        <?php echo $this->Form->input('Product.oumId', array(
            'type' => 'hidden',
        )); ?>
    </div>
<?php  else: ?>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductSellPrice"><?php echo __('Sell price'); ?></label>
        <?php echo $this->Form->input('Product.sell_price', array(
            "type" => "number",
        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductUom"><?php echo __('OUM'); ?></label>
        <?php echo $this->Form->input('Product.oum', array(
                'type'      => 'select',
                'options'   => $arr_product_oum,
        )); ?>
        <?php echo $this->Form->input('Product.oumId', array(
            'type' => 'hidden',
        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductCostPrice"><?php echo __('Cost price'); ?></label>
        <?php echo $this->Form->input('Product.cost_price', array(
            "type" => "number",
        )); ?>
    </div>
<?php endif; ?>


    <div class="ui-field-contain">
        <label class="field-title" for="ProductGstTax"><?php echo __('GST tax %'); ?></label>
        <?php echo $this->Form->input('Product.gst_tax', array(
                'type'      => 'select',
                'options'   => $arr_taxtext,
        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductPstTax"><?php echo __('PST tax %'); ?></label>
        <?php echo $this->Form->input('Product.pst_tax', array(
                'type'      => 'select',
                'options'   => $arr_taxtext,
        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductStatus"><?php echo __('Status'); ?></label>
        <?php echo $this->Form->input('Product.status', array(
                'type'      => 'select',
                'options'   => $product_statuses,
        )); ?>
        <?php echo $this->Form->input('Product.pst_taxId', array(
            'type' => 'hidden',
        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductCategory"><?php echo __('Category'); ?></label>
        <?php echo $this->Form->input('Product.category', array(
                'type'      => 'select',
                'options'   => $product_category,
        )); ?>
        <?php echo $this->Form->input('Product.statusId', array(
            'type' => 'hidden',
        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductCategory"><?php echo __('Special Order'); ?></label>
        <?php echo $this->Form->input('Product.special_order', array(
                'type'      => 'select',
                'options'   => $product_yesno,
        )); ?>
        <?php echo $this->Form->input('Product.special_orderId', array(
            'type' => 'hidden',
        )); ?>
    </div>

    <div class="ui-field-contain">
        <label class="field-title" for="ProductAssemplyItem"><?php echo __('active'); ?></label>
        <label class="field-title" for="ProductAssemplyItem"><?php echo __('Assemply item'); ?></label>
        <?php echo $this->Form->input('Product.assemply_item', array(
                'type'      => 'checkbox',
        )); ?>
    </div>
    <div class="ui-field-contain">
        <label class="field-title" for="ProductApproved"><?php echo __('active'); ?></label>
        <label class="field-title" for="ProductApproved"><?php echo __('Approved'); ?></label>
        <?php echo $this->Form->input('Product.approved', array(
                'type'      => 'checkbox',
        )); ?>
    </div>
    
</form>
<?php echo $this->element('../Products/js'); ?>