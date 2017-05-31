<ul class="ul_mag clear <?php echo $bg; ?> temporary_products  line_box " id="listbox_option_<?php echo $option['this_line_no'] ?>" rel="<?php echo $option['this_line_no']; ?>" style="">
    <?php if(!isset($custom_product)){ ?>
    <li class="hg_padd " style="width:1%;background: #ECD9D9;" title="View detail" onclick=" window.location.assign('<?php echo URL.'/products/entry/'.$option['product_id']; ?>');">
        <span class="icon_emp"></span>
    </li>
    <?php } else { ?>
     <li class="hg_padd " style="width:1%;background: #ECD9D9;" ></li>
    <?php } ?>
    <!--CỘT-->
    <?php
        $check = false;
        if(IS_LOCAL)
            $check = true;

    ?>
    <?php if($check){ ?>
    <li class="hg_padd datainput_option " style="  text-align:center; width:5%;">
        <div class="middle_check">
            <label class="m_check2">
            <input type="checkbox" name="cb_choice_<?php echo $option['this_line_no'] ?>" id="cb_choice_<?php echo $option['this_line_no'] ?>" class="viewcheck_choice" checked="checked" value="1" />
            <span class="bx_check"></span>
            </label>
        </div>
    </li>
    <?php } else{ ?>
    <input id="choice_<?php echo $option['this_line_no'] ?>" name="choice_<?php echo $option['this_line_no'] ?>" type="hidden" value="1">
    <?php } ?>
    <li class="hg_padd datainput_option " style="  text-align:center; width:<?php echo $check ? 6 : 5 ?>%;">
        <span id="txt_code_<?php echo $option['this_line_no'] ?>" class="txt_code_lv"><?php echo $option['code']; ?></span>
    </li>
    <li class="hg_padd datainput_option " style="  text-align:left; width:<?php echo $check ? 26 : 28 ?>%;">
        <?php if(!isset($custom_product)){ ?>
        <span id="txt_product_name_<?php echo $option['this_line_no'] ?>" class="txt_product_name_lv"><?php echo $option['product_name']; ?></span>
        <input id="product_name_<?php echo $option['this_line_no'] ?>" name="product_name_<?php echo $option['this_line_no'] ?>" type="hidden" value="<?php echo $option['product_name']; ?>" />
        <?php } else { ?>
        <input id="product_name_<?php echo $option['this_line_no'] ?>" name="product_name_<?php echo $option['this_line_no'] ?>" type="text" class="input_inner jt_box_save" value="<?php echo $option['product_name']; ?>" />
        <?php } ?>
    </li>
    <input id="product_id_<?php echo $option['this_line_no'] ?>" name="product_id_<?php echo $option['this_line_no'] ?>" type="hidden" value="<?php echo $option['product_id']; ?>">
    <?php if($check){ ?>
    <li class="hg_padd datainput_option " style="  text-align:center; width:3%;">
        <div class="middle_check">
            <label class="m_check2">
            <input type="checkbox" name="cb_require_<?php echo $option['this_line_no'] ?>" id="cb_require_<?php echo $option['this_line_no'] ?>" class="viewcheck_require" checked="checked" value="1">
            <span class="bx_check"></span>
            </label>
        </div>
    </li>
    <?php } else { ?>
    <input id="require_<?php echo $option['this_line_no'] ?>" name="require_<?php echo $option['this_line_no'] ?>" type="hidden" value="1">
    <?php } ?>
    <li class="hg_padd datainput_option " style="  text-align:center; width:3%;">
        <div class="middle_check">
            <label class="m_check2">
            <input type="checkbox" name="cb_same_parent_<?php echo $option['this_line_no'] ?>" id="cb_same_parent_<?php echo $option['this_line_no'] ?>" value="0" class="viewcheck_same_parent">
            <span class="bx_check"></span>
            </label>
        </div>
    </li>
    <li class="hg_padd datainput_option " style="  text-align:right; width:7%;">
        <span class="float_left rowedit rowedit<?php echo $option['this_line_no'] ?>" id="box_edit_unit_price_<?php echo $option['this_line_no'] ?>" style="width:100%;">
        <input type="text" name="unit_price_<?php echo $option['this_line_no'] ?>" id="unit_price_<?php echo $option['this_line_no'] ?>" class="input_inner jt_box_save viewprice_unit_price" value="<?php if(isset($option['unit_price'])) echo $this->Common->format_currency($option['unit_price']); ?>" style="text-align:right;" onkeypress="return isPrice(event);">
        </span>
        <span class="float_left jt_inedit rowtest_<?php echo $option['this_line_no'] ?>" style="width:100%;display:none; " id="box_test_unit_price_<?php echo $option['this_line_no'] ?>"><?php if(isset($option['unit_price'])) echo $this->Common->format_currency($option['unit_price']); ?></span>
    </li>
    <li class="hg_padd datainput_option " style=" overflow: visible !important; text-align:center; width:5%;">
        <span id="txt_oum_<?php echo $option['this_line_no'] ?>" class="oum"><?php if(isset($option['oum'])) echo $option['oum']; ?></span>
    </li>
    <input id="discount_<?php echo $option['this_line_no'] ?>" name="discount_<?php echo $option['this_line_no'] ?>" type="hidden" value="0">
    <li class="hg_padd datainput_option " style="  text-align:right; width:5%;">
        <span class="float_left rowedit rowedit_<?php echo $option['this_line_no'] ?>" id="box_edit_quantity_<?php echo $option['this_line_no'] ?>" style="width:100%;">
        <input type="text" name="quantity_<?php echo $option['this_line_no'] ?>" id="quantity_<?php echo $option['this_line_no'] ?>" class="input_inner jt_box_save viewprice_quantity" value="1" style="  text-align:right; ">
        </span>
        <!-- lock edit-->
        <span class="float_left jt_inedit rowtest_<?php echo $option['this_line_no'] ?>" style="width:100%; display:none;" id="box_test_quantity_<?php echo $option['this_line_no'] ?>">1</span>
    </li>
    <li class="hg_padd datainput_option " style="  text-align:right; width:7%;">
        <span id="txt_sub_total_<?php echo $option['this_line_no'] ?>" class="price_sub_total_lv" style="text-align:right;"></span>
    </li>
    <li class="hg_padd datainput_option " style=" overflow: visible !important; text-align:left; width:<?php echo $check ? 7 : 5 ?>%;">
        <span class="float_left rowedit rowedit_<?php echo $option['this_line_no'] ?>" id="box_edit_group_type_<?php echo $option['this_line_no'] ?>" style="width:94%; margin: 0 3%;">
            <input type="text" name="group_type_<?php echo $option['this_line_no'] ?>" id="group_type_<?php echo $option['this_line_no'] ?>" class="input_inner jt_box_save viewprice_group_type" value="" style="text-align: left; margin: 0px 14px 0px 0px;">
        </span>
        <input name="group_type<?php echo $option['this_line_no'] ?>_id" id="group_type<?php echo $option['this_line_no'] ?>Id" type="hidden" value="" class="hidden_group_type">
        <script type="text/javascript">
            $(function () {
                $("#group_type_<?php echo $option['this_line_no'] ?>").combobox({"Inc":"Inc","Exc":"Exc"});
            });
        </script>
    </li>
    <li class="hg_padd datainput_option " style=" overflow: visible !important; text-align:left; width:7%;">
        <span class="float_left rowedit rowedit_<?php echo $option['this_line_no'] ?>" id="box_edit_option_group_<?php echo $option['this_line_no'] ?>" style="width:94%; margin: 0 3%;">
            <input type="text" name="option_group_<?php echo $option['this_line_no'] ?>" id="option_group_<?php echo $option['this_line_no'] ?>" class="input_inner jt_box_save viewprice_option_group" value="" style="text-align: left; margin: 0px 14px 0px 0px;">
        </span>
        <input type="hidden" name="original_unit_price_<?php echo $option['this_line_no'] ?>" id="original_unit_price_<?php echo $option['this_line_no'] ?>" value="<?php echo $this->Common->format_currency($option['unit_price']); ?>">
        </span>
        <input name="option_group_<?php echo $option['this_line_no'] ?>_id" id="option_group_<?php echo $option['this_line_no'] ?>Id" type="hidden" value="" class="hidden_option_group">
        <script type="text/javascript">
            $(function () {
                $("#option_group_<?php echo $option['this_line_no'] ?>").combobox({"":""});
            });
        </script>
    </li>
    <input id="is_tempory_product_<?php echo $option['this_line_no'] ?>" name="is_tempory_product_<?php echo $option['this_line_no'] ?>" type="hidden" value="1">
    <input id="sizew_<?php echo $option['this_line_no'] ?>" name="sizew_<?php echo $option['this_line_no'] ?>" type="hidden" value="<?php echo $option['sizew']; ?>">
    <input id="sizew_unit_<?php echo $option['this_line_no'] ?>" name="sizew_unit_<?php echo $option['this_line_no'] ?>" type="hidden" value="<?php echo $option['sizew_unit']; ?>">
    <input id="sizeh_<?php echo $option['this_line_no'] ?>" name="sizeh_<?php echo $option['this_line_no'] ?>" type="hidden" value="<?php echo $option['sizeh'] ?>">
    <input id="sizeh_unit_<?php echo $option['this_line_no'] ?>" name="sizeh_unit_<?php echo $option['this_line_no'] ?>" type="hidden" value="<?php echo $option['sizeh_unit']; ?>">
    <input id="sell_by_<?php echo $option['this_line_no'] ?>" name="sell_by_<?php echo $option['this_line_no'] ?>" type="hidden" value="<?php echo $option['sell_by']; ?>">
    <input id="discount_<?php echo $option['this_line_no'] ?>" name="discount_<?php echo $option['this_line_no'] ?>" type="hidden" value="0">
    <input id="sku_<?php echo $option['this_line_no'] ?>" name="sku_<?php echo $option['this_line_no'] ?>" type="hidden" value="<?php echo $option['sku']; ?>">
    <input id="code_<?php echo $option['this_line_no'] ?>" name="code_<?php echo $option['this_line_no'] ?>" type="hidden" value="<?php echo $option['code']; ?>">
    <input id="sub_total_<?php echo $option['this_line_no'] ?>" name="sub_total_<?php echo $option['this_line_no'] ?>" type="hidden" value="0">
    <input id="oum_<?php echo $option['this_line_no'] ?>" name="oum_<?php echo $option['this_line_no'] ?>" type="hidden" value="<?php echo $option['oum']; ?>">
    <input id="deleted_<?php echo $option['this_line_no'] ?>" name="deleted_<?php echo $option['this_line_no'] ?>" type="hidden" value="false">
    <li class="hg_padd datainput_option " style="  text-align:left; width:2%;">
        <div class="jt_right_check">
            <a title="Delete" rev="option" id="del_option_<?php echo $option['this_line_no'] ?>" class="deleteopt_link del_option" onclick="removeOnScreen(this);" href="javascript:void(0);">
            <span class="icon_remove2"></span>
            </a>
        </div>
    </li>
</ul>