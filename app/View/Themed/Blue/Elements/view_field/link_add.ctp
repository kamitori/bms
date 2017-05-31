<?php 
if(isset($arr_vls['xempty'][$viewkeys]) && $arr_vls['xempty'][$viewkeys]=='1')
	echo '';
else if(isset($arr_vls[$viewkeys]) && $arr_vls[$viewkeys]==0){?>
    <div class="middle_check" class="link_add" >
        <a href="<?php echo URL.'/'.$controller;?><?php if(isset($link_add_atction[$viewkeys])) echo '/'.$link_add_atction[$viewkeys];?><?php if(isset($iditem)) echo '/'.$iditem;?>/<?php echo $arr_vls['_id'];?>"><span class="icon_emp4" <?php if(isset($arr_vls['xstyle_element'][$viewkeys])) echo 'style="'.$arr_vls['xstyle_element'][$viewkeys].'"'; ?>></span></a>
    </div>

<?php }else{ ?>
    <div class="middle_check" class="link_view" >
        <a href="<?php echo URL.'/'.$controller;?><?php if(isset($link_add_atction[$viewkeys])) echo '/'.$link_add_atction[$viewkeys];?><?php if(isset($iditem)) echo '/'.$iditem;?>/<?php echo $arr_vls['_id'];?>"><span class="icon_emp6" <?php if(isset($arr_vls['xstyle_element'][$viewkeys])) echo 'style="'.$arr_vls['xstyle_element'][$viewkeys].'"'; ?>></span></a>
    </div>
<?php } ?>