<?php
$lock = 0;
if(!isset($arr_view_st['edit']) || $arr_view_st['edit']!='1' )
   $lock = 1;
if(isset($arr_vls['xempty'][$viewkeys]) && $arr_vls['xempty'][$viewkeys]=='1')
	echo '';
else if(isset($arr_vls[$viewkeys]) && $arr_vls[$viewkeys]==1){?>
    <div class="middle_check" class="link_add">
        <a href="javascript:void(0)" <?php if(!$lock){ ?> class="options_popup" rel="<?php echo URL.'/'.$controller;?><?php if(isset($link_add_atction[$viewkeys])) echo '/'.$link_add_atction[$viewkeys];?><?php if(isset($iditem)) echo '/'.$iditem;?>/<?php echo $arr_vls['_id'];?>" <?php } ?> ><span class="icon_emp6" <?php if($lock) echo 'style="cursor: default;"'; ?> ></span></a>
    </div>

<?php }else{ ?>
    <div class="middle_check" class="link_view">
        <a><span class="icon_emp5" style="cursor: default;"></span></a>
    </div>
<?php } ?>