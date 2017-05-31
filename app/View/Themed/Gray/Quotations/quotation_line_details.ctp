<?php
	//FOR TESTING
	//pr($arr_subsetting[$blockname]);
	//pr($arr_subsetting);
	//pr($subdatas);	
	//pr($subdatas);
	if(isset($line_details_width))
		$w=$line_details_width.'%';
	else
		$w = '33%';
	$lines=0;
?>
<div class="tab_1" style="width:<?php echo $w;?>; float:left;">
    <span class="title_block bo_ra1">
        <span class="float_left">
            <span class="fl_dent"><h4>Quotation line details</h4></span>
        </span>
    </span>
    <div class="tab_2_inner">
    	<?php foreach($arr_subsetting[$quoteline] as $kk=>$vv){?>
        <p class="clear">
            <span class="label_1 float_left minw_lab2"><?php echo $vv;?></span>
            <div class="width_in3a float_left indent_input_tp">
                <?php if($vv == 'Approve') { ?>
                <input type="checkbox" style="margin-top: 7px;" <?php if(isset($subdatas[$quoteline][$kk])&&$subdatas[$quoteline][$kk]) echo 'checked' ?> name="<?php echo $kk; ?>" />
                <?php }else{ ?>
                <input class="input_1 float_left" type="text" value="<?php if(isset($subdatas[$quoteline][$kk])) echo $subdatas[$quoteline][$kk];?>" readonly="readonly" style="color:#888;" />
                <?php } ?>
            </div>
        </p>
        <?php $lines++; }?>
       	
       	<?php for($m=$lines;$m<$line_sum;$m++){?>        
        <p class="clear">
            <span class="label_1 float_left minw_lab2 fixbor3 color_hidden2">&nbsp;</span>
            <div class="width_in3a float_left indent_input_tp">&nbsp;</div>
        </p>
        <?php }?>
        <input type="hidden" id="itemid" value="<?php if(isset($iditem)) echo $iditem;?>" />
        <input type="hidden" id="subitems" value="<?php if(isset($subitems)) echo $subitems;?>" />
        <input type="hidden" id="employee_id" value="<?php if(isset($employee_id)) echo $employee_id;?>" />
        <input type="hidden" id="employee_name" value="<?php if(isset($employee_name)) echo $employee_name;?>" />
        <input type="hidden" id="quote_code" value="<?php if(isset($quote_code)) echo $quote_code;?>" />
        <input type="hidden" id="sumrfq" value="<?php if(isset($sumrfq)) echo $sumrfq;?>" />
        <p class="clear"></p>
    </div>
    <span class="title_block bo_ra2"></span>
</div>
<script type="text/javascript">
    $("input[name=rfq_approve]").change(function(){
        var value = 0;
        if($(this).is(":checked"))
            value = 1;
        var obj = {};
        obj["rfq_approve"] = value;
        save_option('products',obj,<?php echo $subitems;  ?>);
    });
</script>