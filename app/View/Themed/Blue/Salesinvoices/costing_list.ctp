<div id="content">
	<div class="jt_ajax_note">Loading...</div>
    <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left">
            <h1>
                <span class="md_center"></span>
                <span id="md_name">
                    <?php if(isset($subdatas[$salesinvoiceline]['products_name'])) echo $subdatas[$salesinvoiceline]['products_name'];?>
                </span>
                <input type="hidden" id="costing_product_key" value="<?php echo $subdatas[$salesinvoiceline]['key']; ?>" />
             </h1>
        </div>
        <div class="jt_module_title float_right jt_t_right"><h1>&nbsp;</h1></div>
    </div>

    <div id="option_list_form_auto_save">
        <!-- Add form -->
        <form class="form_<?php echo $controller;?>_costing" action="" method="POST" class="float_left">
            <div class="clear_percent">
               <?php echo $this->element('box',array('panel'=>$arr_field_options['option'],'key'=>'option','arr_val'=>$arr_field_options['option']['option'])); ?>
            </div>
            <div class="clear"></div>
        	<input type="submit" class="hidden" id="submit" name="submit" />
            <input type="hidden" name="submit" />
            <input type="hidden" id="products_id" value="<?php if(isset($products_id)) echo $products_id;?>" />
            <input type="hidden" name="product_key" id="subitems" value="<?php if(isset($subitems)) echo $subitems;?>" />
        </form>
        <span id="groupstr" style="display:none;"><?php if(isset($groupstr)) echo $groupstr;?></span>
	</div>
    <?php echo $this->element('../'.$name.'/js',array('no_alert_input'=>true)); ?>
</div>
<span class="hidden" id="option_popup"></span>
<span class="hidden" id="save_custom_product"></span>
<?php echo $this->element('js/costing_list_js');  ?>