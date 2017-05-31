<?php
if(isset($arr_vls['xempty'][$viewkeys]) && $arr_vls['xempty'][$viewkeys]=='1')
	echo '';
else if((isset($arr_view_st['edit']) && $arr_view_st['edit']=='1' ) && !isset($arr_vls['xlock'][$viewkeys])){
		if(!isset($arr_vls[$viewkeys]))
			$arr_vls[$viewkeys] = 0;

		if($arr_vls[$viewkeys]==1)
			$checks = 'checked="checked"';
		else
			$checks = '';
?>
<div class="middle_check " <?php if(isset($arr_vls['xhidden'][$viewkeys])) echo 'style="display: none;"'; ?>>
    <label class="m_check2">
        <input type="checkbox" name="cb_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" id="cb_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" class="viewcheck_<?php echo $viewkeys;?>" <?php echo $checks;?> <?php if(isset($arr_view_st['element_input'])) echo $arr_view_st['element_input'];?> />
        <span class="bx_check" style="margin: 0px;"></span>
    </label>
</div>

<?php }else{
		if(!isset($arr_vls[$viewkeys]))
			$arr_vls[$viewkeys] = 0;
		if(isset($arr_vls[$viewkeys])&&$arr_vls[$viewkeys]==1)
			echo '<div class="middle_check"><label class="m_check2"><input type="checkbox" name="'.'cb_'.$viewkeys.'_'.$arr_vls['_id'].'" checked="checked" readonly="readonly" style=" cursor:default;" onclick="return false;"><span class="bx_check"  style=" cursor:default;background-color:#e5e5e5;"></span></label></div>';
		else
			echo '<div class="middle_check"><label class="m_check2"><input type="checkbox" name="'.'cb_'.$viewkeys.'_'.$arr_vls['_id'].'" readonly="readonly" onclick="return false;"><span class="bx_check"  style=" cursor:default;background-color:#e5e5e5;"></span></label></div>';
?>

<?php }?>
