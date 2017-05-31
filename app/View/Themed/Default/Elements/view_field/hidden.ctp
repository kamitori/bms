<?php
if(isset($arr_vls[$viewkeys.'_lock_field']))
{
	$name = str_replace('_id','_code',$viewkeys);
	echo '<input id="'.$name.'_'.$arr_vls['_id'].'Id'.'" type="hidden" value="'.$arr_vls[$viewkeys.'_code'].'" />';
}
else if(isset($arr_vls[$viewkeys]))
{
	$tmp = '';
	if(isset($arr_view_st['element_input']))
		$tmp = $arr_view_st['element_input'];
	echo '<input id="'.$viewkeys.'_'.$arr_vls['_id'].'" name="'.$viewkeys.'_'.$arr_vls['_id'].'" type="hidden" value="'.$arr_vls[$viewkeys].'" '.$tmp.' />';
}