<div class="hidden" id="div_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>"><?php if(isset($arr_vls[$viewkeys])) echo $arr_vls[$viewkeys]; ?></div>
<?php
$lock = 0;
if(!isset($arr_view_st['edit']) || $arr_view_st['edit']!='1' )
   $lock = 1;
if(isset($arr_vls['xempty'][$viewkeys]) && $arr_vls['xempty'][$viewkeys]=='1')
	echo '';
else{?>
    <div class="middle_check" style="<?php echo isset($arr_vls['xstyle_element'][$viewkeys]) ? $arr_vls['xstyle_element'][$viewkeys] : '' ?>" class="link_detail">
        <a href="javascript:void(0)" class="details"  id="<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" ><span class="link_detail"></span></a>
    </div>

<?php }