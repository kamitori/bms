<?php
	//FOR TESTING
	//echo $blockname;
	//pr($arr_subsetting[$blockname]);
	//pr($arr_subsetting[$blockname]['field']);
	//pr($subdatas);
	$nodisplay = array('hidden');


?>
<div class="tab_2_inner">
    <p class="clear">
        <span class="label_1 float_left minw_lab2">Pricing last updated by</span>
        <div class="width_in3a float_left indent_input_tp">
            <input type="hidden" name="our_rep_id" value="<?php if(isset($user_id)) echo $user_id;?>" id="otherpricing_update_price_by_id" />
            <input name="our_rep" class="input_1 float_left" readonly="readonly" onclick="$(&quot;#click_open_window_contactsupdate_price_by&quot;).click()" type="text" value="<?php if(isset($user_name)) echo $user_name;?>" id="otherpricing_update_price_by" />
            <!--<span id="click_open_window_contactsupdate_price_by" class="iconw_m indent_dw_m"></span>
            <script type="text/javascript">
				$(function(){
					window_popup('contacts', 'Specify Our rep','our_rep','click_open_window_contactsupdate_price_by','?is_employee=1');
				});
            </script>-->
        </div>
    </p>
    <p class="clear">
        <span class="label_1 float_left minw_lab2">Date update</span>
        <div class="width_in3a float_left indent_input_tp">
            <input name="date_update" class="input_1 float_left" type="text" value="<?php if(isset($subdatas['otherpricing']['update_price_date'])) echo $subdatas['otherpricing']['update_price_date']; else echo $this->Common->format_date();?>" id="date_update" readonly="readonly" />
        </div>
    </p>
    <p class="clear">
        <span class="label_1 float_left minw_lab2">Pricing method</span>
        <div class="width_in3a float_left indent_input_tp">
            <input class="input_select" name="pricing_method_list" id="pricing_method_list" style="margin: 0px 17px 0px 0px;" type="text" value="<?php if(isset($subdatas['otherpricing']['pricing_method'])) echo $subdatas['otherpricing']['pricing_method'];?>" readonly="readonly">
            <input type="hidden" id="pricing_method_listId" value="" />
			<?php if(isset($subdatas['otherpricing']['pricing_method_list'])){?>
            <script type="text/javascript">
				$(function () {
					$("#pricing_method_list").combobox(<?php  echo json_encode($subdatas['otherpricing']['pricing_method_list']);?>);
				});
			</script>
            <?php }?>

        </div>
    </p>
    <p class="clear">
        <span class="label_1 float_left minw_lab2">Pricing bleed</span>
        <div class="width_in3a float_left indent_input_tp">
            <input class="input_select" name="pricing_bleed_list" id="pricing_bleed_list" style="margin: 0px 17px 0px 0px;" type="text" value="<?php if(isset($subdatas['otherpricing']['pricing_bleed_list'][$subdatas['otherpricing']['pricing_bleed']])) echo $subdatas['otherpricing']['pricing_bleed_list'][$subdatas['otherpricing']['pricing_bleed']];?>" readonly="readonly">
            <input type="hidden" id="pricing_bleed_listId" value="" />
            <?php if(isset($subdatas['otherpricing']['pricing_bleed_list'])){?>
            <script type="text/javascript">
                $(function () {
                    $("#pricing_bleed_list").combobox(<?php  echo json_encode($subdatas['otherpricing']['pricing_bleed_list']);?>);
                });
            </script>
            <?php }?>

        </div>
    </p>
    <?php $pb_key = isset($subdatas['otherpricing']['price_breaks_type'])?$subdatas['otherpricing']['price_breaks_type']:'';?>
    <p class="clear">
        <span class="label_1 float_left minw_lab2 fixbor3">Price breaks type</span>
        <div class="width_in3a float_left indent_input_tp">
            <input class="input_select" name="price_breaks_type" id="price_breaks_type" style="margin: 0px 17px 0px 0px;" type="text" value="<?php if(isset($subdatas['otherpricing']['arr_price_breaks_type'][$pb_key])) echo $subdatas['otherpricing']['arr_price_breaks_type'][$pb_key];?>" readonly="readonly">
            <input type="hidden" id="price_breaks_typeId" value="<?php echo $pb_key;?>" />
            <?php if(isset($subdatas['otherpricing']['arr_price_breaks_type'])){?>
            <script type="text/javascript">
                $(function () {
                    $("#price_breaks_type").combobox(<?php  echo json_encode($subdatas['otherpricing']['arr_price_breaks_type']);?>);
                });
            </script>
            <?php }?>

        </div>
        <p class="clear"></p>
    </p>
    <div style="overflow:hidden; height:218px;">
        <span class="title_block">
            <span class="fl_dent">
                <h4>Pricing notes</h4>
            </span>
            <a title="Link a contact" href="">
                <span class="icon_down_tl top_f"></span>
            </a>
        </span>
        <form>
            <textarea class="area_t height_area" id="otherpricing_price_note"><?php if(isset($subdatas['otherpricing']['price_note'])) echo $subdatas['otherpricing']['price_note'];?></textarea>
        </form>
        <div class="block_txt" style="text-align:justify;">
            <p>Note: Customers can be a default price category on the 'Pricing' tab on the customer screen.If none is specified for the customer then the default category here will be used. Specific prising, including price break, can also be set per customer on their 'Pricing' tab.</p>
            <!--<div class="warning">
                Warning: Supplier cost does not match costings
            </div>-->
        </div>
    </div>
</div>