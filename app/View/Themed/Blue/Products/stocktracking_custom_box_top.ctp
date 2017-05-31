<?php 
	if(isset($subdatas['stocktracking']['check_stock_stracking']) && $subdatas['stocktracking']['check_stock_stracking']==1)
		$str = 'checked="checked"';
	else
		$str = '';
?>
<span class="float_right">
    <span class="fl_dent"><h6>Track stock qty for this item</h6></span>
    <label class="m_check2">
        <input type="checkbox" name="check_stock_stracking" id="check_stock_stracking" <?php echo $str;?> />
        <span></span>
    </label>
</span>
