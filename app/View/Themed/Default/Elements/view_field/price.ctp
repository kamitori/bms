<?php
	$num = 2;
	if(isset($arr_view_st['element_input']))
		$class= $arr_view_st['element_input'];
	if(isset($arr_view_st['numformat']))
		$num = $arr_view_st['numformat'];

	if(isset($arr_vls[$viewkeys])&&is_string($arr_vls[$viewkeys]))
		$arr_vls[$viewkeys] = str_replace(',', '', $arr_vls[$viewkeys]);

	if(isset($arr_view_st['isInt']))
		$onkeypress = 'return isNumbers(event);';
	else
		$onkeypress = 'return isPrice(event);';

	if(isset($arr_vls['xempty'][$viewkeys]) && $arr_vls['xempty'][$viewkeys]=='1')
		echo '';
	else if(!isset($arr_view_st['edit'])|| (isset($arr_vls['xlock'][$viewkeys]) && $arr_vls['xlock'][$viewkeys]=='1')){
		$price = isset($arr_vls[$viewkeys]) ? (float)$arr_vls[$viewkeys] : 0;
		$price = $this->Common->format_currency($price,$num);
		if(isset($arr_view_st['getValue'])){
			$price = isset($arr_vls[$arr_view_st['getValue']]) ? (float)$arr_vls[$arr_view_st['getValue']] : 0;
			$price = $this->Common->format_currency($price,$num);
		}
?>

			<span id="txt_<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="price_<?php echo $viewkeys;?>_lv" style="text-align:right;"><?php echo $price;?></span>

<?php
	}else if(isset($arr_vls) && is_array($arr_vls) && isset($viewkeys) && isset($arr_vls['_id'])){
			if(isset($arr_view_st['morekey']))
				$morekey = $arr_view_st['morekey'];
			else
				$morekey = '';
?>

	<?php if(isset($arr_view_st['mod']) && $arr_view_st['mod']=='text'){ // text entry ?>
    	<span class="float_left <?php echo $morekey;?>rowedit <?php echo $morekey;?>rowedit_<?php echo $arr_vls['_id'];?>" id="box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" style="width:100%;height:55px!important;">
    		<?php 
    			$price = isset($arr_vls[$viewkeys]) ? (float)$arr_vls[$viewkeys] : 0;
				$price = $this->Common->format_currency($price,$num);
				if(isset($arr_view_st['getValue'])){
					$price = isset($arr_vls[$arr_view_st['getValue']]) ? (float)$arr_vls[$arr_view_st['getValue']] : 0;
					$price = $this->Common->format_currency($price,$num);
				}
    		?>
        	<textarea name="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" id="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="textae input_inner jt_box_save viewprice_<?php echo  $viewkeys;?>" style="text-align:right;<?php if(isset($arr_vls['css'][$viewkeys])) echo $arr_vls['css'][$viewkeys];?>" <?php if(isset($arr_vls['attr'][$viewkeys])) echo $arr_vls['attr'][$viewkeys];?> <?php if(isset($arr_vls['lock']) && $arr_vls['lock']=='1'){?>readonly="readonly"<?php }?> onkeypress="<?php echo $onkeypress;?>"><?php echo $price;?></textarea>
        </span>
        <span class="float_left jt_inedit <?php echo $morekey;?>rowtest_<?php echo $arr_vls['_id'];?>" style="width:100%;display:none; " id="box_test_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>">
            <?php echo $price;?>
        </span>


    <?php }else{?>
    	<span class="float_left <?php echo $morekey;?>rowedit <?php echo $morekey;?>rowedit_<?php echo $arr_vls['_id'];?>" id="box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" style="width:100%;">
    		<?php 
    			$price = isset($arr_vls[$viewkeys]) ? (float)$arr_vls[$viewkeys] : 0;
				$price = $this->Common->format_currency($price,$num);
				if(isset($arr_view_st['getValue'])){
					$price = isset($arr_vls[$arr_view_st['getValue']]) ? (float)$arr_vls[$arr_view_st['getValue']] : 0;
					$price = $this->Common->format_currency($price,$num);
				}
    		?>
            <input type="text" name="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" id="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="input_inner jt_box_save viewprice_<?php echo  $viewkeys;?>" value="<?php echo $price;?>" style="text-align:right;<?php if(isset($arr_view_st['css'])) echo $arr_view_st['css'];?><?php if(isset($arr_vls[$viewkeys]['css'])) echo $arr_vls[$viewkeys]['css'];?>" <?php if(isset($arr_vls[$viewkeys]['attr'])) echo $arr_vls[$viewkeys]['attr'];?> onkeypress="<?php echo $onkeypress;?>" <?php if(isset($arr_vls['attr'][$viewkeys])) echo $arr_vls['attr'][$viewkeys];?> <?php if(isset($arr_vls['lock']) && $arr_vls['lock']=='1'){?>readonly="readonly"<?php }?> />
        </span>
        <span class="float_left jt_inedit <?php echo $morekey;?>rowtest_<?php echo $arr_vls['_id'];?>" style="width:100%;display:none; " id="box_test_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>">
            <?php echo $price;?>
        </span>
    <?php }?>


<?php }?>