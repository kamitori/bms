<?php
//echo $viewkeys.'_'.$arr_vls['_id'];
	$mkey = $viewkeys.'_'.$arr_vls['_id'];
	if(isset($arr_vls['xempty'][$viewkeys]) && $arr_vls['xempty'][$viewkeys]=='1')
		echo '';
	else if(!isset($arr_view_st['edit']) || (isset($arr_vls['xlock'][$viewkeys]) && $arr_vls['xlock'][$viewkeys]=='1')){
		if(isset($arr_vls[$viewkeys]) && $arr_vls[$viewkeys]!=''){ ?>
			<span id="txt_<?php echo  $mkey;?>">
				<?php
					if(isset($option_select_dynamic[$mkey][$arr_vls[$viewkeys]]))
						echo $option_select_dynamic[$mkey][$arr_vls[$viewkeys]];
					else
						echo $arr_vls[$viewkeys];
				?>
            </span>

<?php	}
	}else if(isset($arr_vls) && is_array($arr_vls) && isset($viewkeys) && isset($arr_vls['_id'])){
			if(isset($arr_view_st['morekey']))
				$morekey = $arr_view_st['morekey'];
			else
				$morekey = '';

			$arr_data = array(); $id_data='';
			if(isset($option_select_dynamic[$mkey])){
                $str = json_encode($option_select_dynamic[$mkey]);
				$arr_data = $option_select_dynamic[$mkey];
				if(isset($arr_vls[$viewkeys]))
				$id_data = $arr_vls[$viewkeys];
			}else
                $str = '';
?>
		<span class="float_left <?php echo $morekey;?>rowedit <?php echo $morekey;?>rowedit_<?php echo $arr_vls['_id'];?>" id="box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" style="width:94%; margin: 0 3%;">
        	<input type="text" name="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" id="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="input_inner jt_box_save viewprice_<?php echo  $viewkeys;?>" value="<?php if(isset($arr_vls[$viewkeys])) echo (isset($arr_data[$arr_vls[$viewkeys]]) ? $arr_data[$arr_vls[$viewkeys]] : $arr_vls[$viewkeys]);?>" style=" text-align:<?php if(isset($arr_view_st['align'])) echo $arr_view_st['align'];else echo 'left';?>; <?php if(isset($arr_view_st['css'])) echo $arr_view_st['css'];?>" <?php if((isset($arr_view_st['not_custom']) && $arr_view_st['not_custom']=='1')|| (isset($arr_view_st['lock']) && $arr_view_st['lock']=='1')){?>readonly="readonly"<?php }?> <?php if(isset($arr_view_st['element_input'])) echo $arr_view_st['element_input'];?> />
        </span>

<input name="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>_id" id="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>Id" type="hidden" value="<?php if(isset($arr_vls[$viewkeys])) echo $arr_vls[$viewkeys];?>" />

	<?php
	  if(isset($arr_view_st['lock']) && $arr_view_st['lock']=='1')
		$str = '';
	  else if(isset($option_select_dynamic[$mkey]))
		$str = json_encode($option_select_dynamic[$mkey]);
	  else
	  	$str = '';

	 // if($str!=''){
	?>
        <script type="text/javascript">
            $(function () {
                $("#<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>").combobox(<?php echo $str;?>);
            });
        </script>
    <?php //}?>


<?php }?>