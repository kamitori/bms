<?php
	$option = array(
						'back'=>translate('Back'),
					);
    $actionlist = array(
						'options' =>translate('Options'),
					);
?>

<div class="bg_menu">
    <ul class="menu_control float_left">
    	<?php foreach($option as $ks=>$vls){
				$link = 'href="/"';
		?>
        	<li>
            	<a <?php echo $link;?>  class="<?php if($ks!='support'&&$ks!='history') {?>entry_menu_<?php }else{ ?>icon <?php } ?><?php echo $ks;?> <?php if($ks==$action) echo 'active';?>">
            		<?php echo $vls;?>
                </a>
            </li>
        <?php }?>
    </ul>
    <ul class="menu_control2 float_right">
    	<?php foreach($actionlist as $ks=>$vls){?>
        	<li>
            	<a href="<?php echo URL.'/'.$controller.'/'.$ks; ?>" class="<?php if($action == $ks) echo 'active';?>">
					<?php echo $vls;?>
                 </a>
            </li>
         <?php }?>
    </ul>
</div>
