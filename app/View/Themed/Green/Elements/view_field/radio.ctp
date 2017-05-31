<?php

	if(isset($arr_view_st['edit']) && $arr_view_st['edit']=='1'){
		if(isset($arr_vls[$viewkeys]) && $arr_vls[$viewkeys]== 'on')
			$checks = 'checked="checked"';
		else
			$checks = '';
?>

        <input type="radio" rel="<?php echo $viewkeys; ?>" name="radio_group" id="cb_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" class="viewcheck_<?php echo $viewkeys;?>" <?php echo $checks;?> />
       

<?php }else{

		if($arr_vls[$viewkeys]==1)
			echo 'X';
		else
			echo '';
?>

<?php }?>
