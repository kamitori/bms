<?php
	if(!isset($arr_view_st['edit']) || (isset($arr_view_st['edit']) && $arr_view_st['edit']!='1')){
		if(isset($arr_vls[$viewkeys])){?>

			<span id="txt_<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>"><?php echo $arr_vls[$viewkeys];?></span>

<?php	}
	}else if(isset($arr_vls) && is_array($arr_vls) && isset($viewkeys) && isset($arr_vls['_id'])){
			if(isset($arr_view_st['morekey']))
				$morekey = $arr_view_st['morekey'];
			else
				$morekey = '';

			if(isset($arr_vls[$viewkeys])){
				$textarea_value = str_replace("<br ></inval>","",$arr_vls[$viewkeys]);
				$textarea_value = str_replace('<br />',"\n",$arr_vls[$viewkeys]);
			}else
				$textarea_value = '';
?>

		<span class="float_left <?php echo $morekey;?>rowedit <?php echo $morekey;?>rowedit_<?php echo $arr_vls['_id'];?>" id="box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" style="width:100%;">
        	<textarea name="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" id="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="animated input_inner jt_box_save viewprice_<?php echo  $viewkeys;?>" style="text-align:<?php if(isset($arr_view_st['align'])) echo $arr_view_st['align'];else echo 'left';?>;overflow:hidden;<?php if(isset($arr_view_st['css'])) echo $arr_view_st['css'];?>"><?php echo $textarea_value; ?></textarea>
        </span>

       <!-- lock edit-->
		<span class="float_left jt_inedit <?php echo $morekey;?>rowtest_<?php echo $arr_vls['_id'];?>" style="width:100%; display:none;" id="box_test_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>">
            <?php if(isset($arr_vls[$viewkeys])) echo $arr_vls[$viewkeys];?>
        </span>
<?php }?>

