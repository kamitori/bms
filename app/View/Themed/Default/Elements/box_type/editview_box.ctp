<?php
	//FOR TESTING
	//echo $blockname;
	//pr($arr_subsetting[$blockname]);
	//pr($arr_subsetting[$blockname]['field']);
	//pr($subdatas);
	$nodisplay = array('hidden','header');
	$maxheight = 0;
	if(isset($arr_subsetting[$blockname]['height']))
	$maxheight = (int)$arr_subsetting[$blockname]['height'];

	
?>
<div class="tab_2_inner" id="editview_box_<?php echo $blockname;?>">

	<?php 
		$m=0;
		foreach($arr_subsetting[$blockname]['field'] as $key=>$value){
			if(isset($subdatas[$blockname][0][$key]))
					$value['default'] = $subdatas[$blockname][0][$key];
			else if(isset($subdatas[$blockname][$key]))
                 $value['default'] = $subdatas[$blockname][$key];

			if(isset($value['type']) && $value['type']=='id')
			    echo $this->element('basic_field/'.$value['type'],array('keys'=>$key,'arr_field'=>$value));

			else if(isset($value['type']) && !in_array($value['type'],$nodisplay)){
	?>

		<div>
        <p class="clear">
            <span class="label_1 float_left minw_lab2 <?php if($value['type'] =='relationship'){?>jt_box_line_span<?php }?> link_to_<?php echo $key;?>">

            <?php echo $value['name']; $m++;?>

            </span>
            <div class="width_in3a float_left indent_input_tp">
                <?php echo $this->element('basic_field/'.$value['type'],array('keys'=>$key,'arr_field'=>$value));?>
            </div>
        </p>
        </div>
        
        
        <?php //?>
		<?php }else if(isset($value['type']) && $value['type']=='header'){?>
        	<p class="clear" style="height:5px;">
                <span class="label_1 float_left minw_lab2" style="height:12px;">&nbsp;</span>
                <div class="width_in3a float_left indent_input_tp" style="height:5px;">&nbsp;</div>
            </p>
            <span class="title_block" style="clear:both;">
            	<span>
             		<h4><?php echo $value['name']; $m++;?></h4>
            	</span>
            </span>
        
		<?php }else if(isset($value['type'])){?>
        		<?php echo $this->element('basic_field/'.$value['type'],array('keys'=>$key,'arr_field'=>$value));?>
		<?php }?>


        
    <?php }?>

	<?php 
		$blanca = $maxheight - $m*24;
		$row = $blanca/24;
		$du = $blanca%24;
		if($row>1){
			for($a=0;$a<$row;$a++){
		
	?>
     <p class="clear" <?php if($a==$row-1) echo 'style="height:"'.(24+$du).'px;';?>>
        <span class="label_1 float_left minw_lab2">&nbsp;</span>
        <div class="width_in3a float_left indent_input_tp">&nbsp;</div>
     </p>
     <?php
			}
		}
	?>
     
     
     
</div>