<?php

	if(isset($arr_vls['xempty'][$viewkeys]) && $arr_vls['xempty'][$viewkeys]=='1')
		echo '';
	else if(!isset($arr_view_st['edit']) || (isset($arr_view_st['edit']) && $arr_view_st['edit']!='1') || (isset($arr_vls['xlock'][$viewkeys]) && $arr_vls['xlock'][$viewkeys]=='1')){

		if(isset($arr_vls['icon'][$viewkeys])){
            	echo '	<a href="'.$arr_vls['icon'][$viewkeys].'">
                			<span class="icon_emp" style="float:left; margin-left:5%;"></span>
                		</a>';
       }

		if(isset($arr_vls[$viewkeys])){?>

			<span id="txt_<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="txt_<?php echo $viewkeys;?>_lv"><?php echo ($viewkeys=='amount'&&(float)$arr_vls[$viewkeys]==0 ? '' : $arr_vls[$viewkeys]);?></span>

<?php	}
	}else if(isset($arr_vls) && is_array($arr_vls) && isset($viewkeys) && isset($arr_vls['_id'])){
			if(isset($arr_view_st['morekey']))
				$morekey = $arr_view_st['morekey'];
			else
				$morekey = '';
?>


	<?php if(isset($arr_view_st['mod']) && $arr_view_st['mod']=='text'){ // text entry ?>
        <span class="float_left <?php echo $morekey;?>rowedit <?php echo $morekey;?>rowedit_<?php echo $arr_vls['_id'];?>" id="box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" style="width:100%;height:55px!important;">
        	<textarea name="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" id="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="textae input_inner jt_box_save viewprice_<?php echo  $viewkeys;?>" style="text-align:right;<?php if(isset($arr_vls['css'][$viewkeys])) echo $arr_vls['css'][$viewkeys];?>" <?php if(isset($arr_vls['attr'][$viewkeys])) echo $arr_vls['attr'][$viewkeys];?>><?php if(isset($arr_vls[$viewkeys])) echo $arr_vls[$viewkeys];?></textarea>
        </span>
        <span class="float_left jt_inedit <?php echo $morekey;?>rowtest_<?php echo $arr_vls['_id'];?>" style="width:100%;display:none; " id="box_test_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>">
            <?php if(isset($arr_vls[$viewkeys])) echo $arr_vls[$viewkeys];?>
        </span>


    <?php }else{?>
		<span class="float_left <?php echo $morekey;?>rowedit <?php echo $morekey;?>rowedit_<?php echo $arr_vls['_id'];?>" id="box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" style="width:100%;">

			<?php if(isset($arr_vls['icon'][$viewkeys])){?>
            	<a href="<?php echo $arr_vls['icon'][$viewkeys];?>">
                	<span class="icon_emp" style="float:left; margin-left:5%;"></span>
                </a>
            <?php }?>

            <input type="text" name="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" id="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="input_inner jt_box_save viewprice_<?php echo  $viewkeys;?>" value="<?php if(isset($arr_vls[$viewkeys])) echo htmlentities($arr_vls[$viewkeys]);?>" style=" <?php if(isset($arr_vls['icon'][$viewkeys])) echo 'width:85%;';?> text-align:<?php if(isset($arr_view_st['align'])) echo $arr_view_st['align'];else echo 'left';?>; <?php if(isset($arr_view_st['css'])) echo $arr_view_st['css'];?>" <?php if(isset($arr_vls['attr'][$viewkeys])) echo $arr_vls['attr'][$viewkeys];?> />
        </span>

        <!-- lock edit-->
        <span class="float_left jt_inedit <?php echo $morekey;?>rowtest_<?php echo $arr_vls['_id'];?>" style="width:100%; display:none;" id="box_test_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>">
            <?php if(isset($arr_vls[$viewkeys])) echo $arr_vls[$viewkeys];?>
        </span>
     <?php }?>


<?php }?>