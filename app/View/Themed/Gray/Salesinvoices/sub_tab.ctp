<div class="clear block_dent3">
	<div class="box_inner">
        <ul class="ul_tab">
            <?php foreach($arr_settings['relationship'] as $keys =>$arr_field){?>
                <li <?php if($keys==$sub_tab) echo 'class="active"';?> id="<?php echo $keys;?>">
                    <a style="cursor:pointer;<?php if(isset($not_yet) && isset($not_yet[$keys])) echo 'color:red;';?>">
                        <?php echo $arr_field['name'];?>
                    </a>
                </li>
            <?php }?>
            <?php if(isset($shipping_id)){ ?>
            <li id="view_shipping">
                <a style="cursor:pointer;" target="_blank" href="<?php echo URL; ?>/shippings/entry/<?php echo $shipping_id; ?>" >
                    View Shipping
                </a>
            </li>
            <?php } ?>
            <p class="clear"></p>
        </ul>
    </div>
</div>