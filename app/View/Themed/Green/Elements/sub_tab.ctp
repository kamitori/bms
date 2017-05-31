<div class="clear block_dent3">
	<div class="box_inner">
        <ul class="ul_tab">
            <?php foreach($arr_settings['relationship'] as $keys =>$arr_field){?>
                <?php if(isset($arr_field['hidden'])) continue; ?>
                <li <?php if($keys==$sub_tab) echo 'class="active"';?> id="<?php echo $keys;?>">
                    <a style="cursor:pointer;<?php if(isset($not_yet) && isset($not_yet[$keys])) echo 'color:red;';?>">
                        <?php echo $arr_field['name'];?>
                    </a>
                </li>
            <?php }?>
            <p class="clear"></p>
        </ul>
    </div>
</div>