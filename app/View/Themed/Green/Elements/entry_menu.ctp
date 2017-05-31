<?php if(!isset($title_entry) || $controller=='salesorders' || $controller=='salesinvoices'){ ?>

    <div class="logo float_left">
        <ul class="top_control float_left">
            <li><a href="<?php echo URL.'/'.$controller;?>/first/" class="prev_1"></a></li>
            <li><a href="<?php echo URL.'/'.$controller;?>/prevs/" class="prev"></a></li>
            <li><a href="<?php echo URL.'/'.$controller;?>/nexts/" class="next"></a></li>
            <li><a href="<?php echo URL.'/'.$controller;?>/lasts/" class="next_1"></a></li>
        </ul>
        <div class="title_top_ctrl float_left">
            <h2><?php if(isset($arr_settings['module_label'])) echo $arr_settings['module_label'];?></h2>
            <span><?php echo translate('Record'); ?> <?php if(isset($entry_menu['page'])) echo $entry_menu['page']; else echo '0';?> of <?php if(isset($entry_menu['search'])) echo $entry_menu['search']; else echo '0';?>. <?php echo translate('Total');?> <?php if(isset($entry_menu['total'])) echo $entry_menu['total']; else echo '0';?></span>
        </div>
    </div>

<?php }else{ ?>

    <div class="logo float_left">
        <ul class="top_control float_left">
            <li><a href="<?php echo URL; ?>/<?php echo $controller; ?>/entry/first" class="prev_1"></a></li>
            <li><a href="<?php if(isset($arr_prev['_id'])){ ?><?php echo URL; ?>/<?php echo $controller; ?>/entry/<?php echo (string)$arr_prev['_id']; ?>/<?php echo $num_position - 1; ?><?php }else{ ?>#<?php } ?>" class="prev"></a></li>
            <li><a href="<?php if(isset($arr_next['_id'])){ ?><?php echo URL; ?>/<?php echo $controller; ?>/entry/<?php echo (string)$arr_next['_id']; ?>/<?php echo $num_position + 1; ?><?php }else{ ?>#<?php } ?>" class="next"></a></li>
            <li><a href="<?php echo URL; ?>/<?php echo $controller; ?>/entry/last" class="next_1"></a></li>
            <!-- <li><a href="#" class="play"></a></li> -->
        </ul>

        <div class="title_top_ctrl float_left">
            <h2><?php echo $title_entry; ?></h2>
            <span><?php echo translate('Record'); ?> <?php echo isset($num_position)?$num_position:0; ?> <?php echo translate('of'); ?> <?php echo isset($sum)?$sum:0; ?>. <?php echo translate('Total')?> <?php echo isset($sum)?$sum:0; ?></span>
        </div>
    </div>

<?php } ?>