<?php
	//FOR TESTING
	//echo $blockname;
	//pr($arr_subsetting[$blockname]);
	//pr($arr_subsetting[$blockname]['field']);
	//pr($subdatas);
	$nodisplay = array('hidden');
	if(isset($arr_subsetting[$blockname]['field']))
		$fields = (array)$arr_subsetting[$blockname]['field'];


?>
<div class="tab_2_inner" id="editview_box_<?php echo $blockname;?>">
	<?php if(isset($fields)){?>
		<?php foreach($fields as $kps=>$vps){?>
            <p class="clear">
                <span class="label_1 float_left minw_lab2"><?php if(isset($vps['name'])) echo $vps['name'];?></span>
                <div class="width_in3a float_left indent_input_tp">
                   <span class="input_1 <?php if(isset($vps['moreclass'])) echo $vps['moreclass'];?>" style="text-align:right;" id="<?php echo $blockname.'_'.$kps;?>">&nbsp;
                   		<?php if(isset($subdatas['pricingsummary'][$kps]) && $kps!='markup' && $kps!='margin'){
							echo $this->Common->format_currency((float)$subdatas['pricingsummary'][$kps]);
						}else if(isset($subdatas['pricingsummary'][$kps]) && ($kps=='markup' || $kps=='margin')) echo $this->Common->format_currency((float)$subdatas['pricingsummary'][$kps]).'%';
						?>
                   </span>
                </div>
            </p>
        <?php } ?>
    <?php } ?>

    <p class="clear">
        <span class="label_1 float_left minw_lab2"></span>
        <div class="width_in3a float_left indent_input_tp"></div>
    </p>
    <div class="block_warning" style="display:block;">
        <span class="label_bg float_left minw_lab2 fixbor3"></span>
        <div class="width_in3a float_left indent_input_tp">
              <div class="warning">
			  <?php if(isset($subdatas['pricingsummary_note'])) echo $subdatas['pricingsummary_note'];?>
              </div>
        </div>
    </div>
    <?php for($m=0;$m<6;$m++){?>
        <p class="clear">
            <span class="label_1 float_left minw_lab2" <?php echo ($m%5==0 ? 'style="height:29px"' : ''); ?>></span>
            <div class="width_in3a float_left indent_input_tp"></div>
        </p>
    <?php }?>
    <p class="clear"></p>
</div>