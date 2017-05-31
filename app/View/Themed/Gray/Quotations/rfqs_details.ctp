<?php
	//FOR TESTING
	//pr($arr_subsetting[$blockname]);
	//pr($arr_subsetting);
	//pr($subdatas);
	//pr($subdatas);
	$lines = 0; $line_sum = 10;
	if(isset($edit_box['line_sum']))
		$line_sum = (int)$edit_box['line_sum'];
?>
<div class="tab_1" style="width:100%; float:left;">
    <span class="title_block bo_ra1">
        <span class="float_left">
            <span class="fl_dent"><h4><?php if(isset($edit_box['title'])) echo $edit_box['title'];?></h4></span>
        </span>
    </span>
    <div class="tab_2_inner">
    	<?php foreach($arr_subsetting[$blockname]['field'] as $kk=>$vls){
				if(isset($vls['type']) && ($vls['type'] =='hidden' || $vls['type'] =='id' || $vls['type'] =='icon')){?>
					<input type="hidden" id="<?php echo $kk;?>" value="<?php if(isset($vls['default'])) echo $vls['default'];?>" />
		<?php	}else{
					$linkclass = $csslabel = '';
					if(!isset($vls['type'])) $vls['type'] = 'text';
					else if($vls['type'] == 'relationship' && isset($vls['default']) && $vls['default']!=''){
						$linkclass = 'link_to_'.$kk.' jt_link_on" cls="'.$vls['cls'].'" rel="'.$kk;
						$csslabel = 'cursor:pointer;';
				}
		?>
            <p class="clear">
                <span class="label_1 float_left minw_lab2 <?php echo $linkclass;?>" style=" <?php echo $csslabel;?>"><?php if(isset($vls['name'])) echo $vls['name'];?></span>
                <div class="width_in3a float_left indent_input_tp">
                    <?php echo $this->element('basic_field/'.$vls['type'],array('keys'=>$kk,'arr_field'=>$vls)); ?>
                </div>
            </p>
        <?php $lines++; } }?>

       	<?php for($m=$lines;$m<$line_sum;$m++){?>
        <p class="clear">
            <span class="label_1 float_left minw_lab2 fixbor3 color_hidden2">&nbsp;</span>
            <div class="width_in3a float_left indent_input_tp">&nbsp;</div>
        </p>
        <?php }?>
        <?php if(isset($edit_box['more_html'])) echo $edit_box['more_html'];?>
        <p class="clear"></p>
    </div>
    <span class="title_block bo_ra2"></span>
</div>