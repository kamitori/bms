<?php
//pr($option_select);die;
	//nếu KHÔNG cho phép edit
	if(isset($option_select_custom[$viewkeys]))
		$option_select[$viewkeys] = $option_select_custom[$viewkeys];//custom lai list select
	if(isset($arr_vls['xempty'][$viewkeys]) && $arr_vls['xempty'][$viewkeys]=='1')
		echo '';
	else if(!isset($arr_view_st['edit']) || (isset($arr_vls['xlock'][$viewkeys]) && $arr_vls['xlock'][$viewkeys]=='1')){
		if(isset($arr_vls[$viewkeys]) && $arr_vls[$viewkeys]!=''){?>
			<span id="txt_<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="<?php echo  $viewkeys; ?>">
				<?php
					if(isset($option_select[$viewkeys][$arr_vls[$viewkeys]]))
						echo $option_select[$viewkeys][$arr_vls[$viewkeys]];
					else
						echo $arr_vls[$viewkeys];
				?>
            </span>

<?php	}
	}else if(isset($arr_vls[$viewkeys]) && $arr_vls[$viewkeys]!=''
			&&isset($arr_vls[$viewkeys.'_lock_field'])&&$arr_vls[$viewkeys.'_lock_field']==true){?>
			<span id="txt_<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>">
				<?php
					if(isset($option_select[$viewkeys][$arr_vls[$viewkeys]]))
						echo $option_select[$viewkeys][$arr_vls[$viewkeys]];
					else
						echo $arr_vls[$viewkeys.'_name'];
				?>
            </span>

<?php
	}
	//nếu cho phép edit
	else if(isset($arr_vls) && is_array($arr_vls) && isset($viewkeys) && isset($arr_vls['_id'])){
			if(isset($arr_view_st['morekey']))
				$morekey = $arr_view_st['morekey'];
			else
				$morekey = '';

			$arr_data = array(); $id_data='';
			if(isset($option_select[$viewkeys])){
                $str = json_encode($option_select[$viewkeys]);
				$arr_data = $option_select[$viewkeys];
				if(isset($arr_vls[$viewkeys]))
				$id_data = $arr_vls[$viewkeys];
			}else
                $str = '';
?>
		<span class="float_left <?php echo $morekey;?>rowedit <?php echo $morekey;?>rowedit_<?php echo $arr_vls['_id'];?>" id="box_edit_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" style="width:94%; margin: 0 3%;<?php if(isset($arr_view_st['outcss'])) echo $arr_view_st['outcss'];?>">
        	<input type="text" name="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" id="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" class="input_inner jt_box_save viewprice_<?php echo  $viewkeys;?>" value="<?php if(isset($id_data) && isset($arr_data[(string)$id_data])) echo $arr_data[(string)$id_data];?>" style=" text-align:<?php if(isset($arr_view_st['align'])) echo $arr_view_st['align'];else echo 'left';?>; <?php if(isset($arr_view_st['css'])) echo $arr_view_st['css'];?>" <?php if((isset($arr_view_st['not_custom']) && $arr_view_st['not_custom']=='1')|| (isset($arr_view_st['lock']) && $arr_view_st['lock']=='1')){?>readonly="readonly"<?php }?> <?php if(isset($arr_view_st['element_input'])) echo $arr_view_st['element_input'];?> />
        </span>

<input name="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>_id" id="<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>Id" type="hidden" value="<?php if(isset($arr_vls[$viewkeys])) echo $arr_vls[$viewkeys];?>" class="hidden_<?php echo $viewkeys; ?>" />

	<?php
	  if(isset($arr_view_st['lock']) && $arr_view_st['lock']=='1')
		$str = '';
	  else if(isset($option_select[$viewkeys]))
		$str = json_encode($option_select[$viewkeys]);
	  else
	  	$str = '';

	 // if($str!=''){
	?>
	<script type="text/javascript">
        $(function () {
            $("#<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>").combobox(<?php echo $str;?>);
        });
    </script>


<?php }?>