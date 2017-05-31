<?php if(!isset($title_entry)){ ?>
    <div class="logo float_left">
        <ul class="top_control float_left">
            <li><a href="<?php echo URL.'/'.$controller;?>/first/" class="prev_1"></a></li>
            <li><a href="<?php echo URL.'/'.$controller;?>/prevs/" class="prev"></a></li>
            <li><a href="<?php echo URL.'/'.$controller;?>/nexts/" class="next"></a></li>
            <li><a href="<?php echo URL.'/'.$controller;?>/lasts/" class="next_1"></a></li>
        </ul>
        <div class="title_top_ctrl float_left">
            <h2><?php if(isset($arr_settings['module_label'])) echo $arr_settings['module_label'];?> (Find mode)</h2>
            <span>Request <?php echo $entry_menu['page'];?> of <?php if(isset($entry_menu['search'])) echo $entry_menu['search'];?> find requests. Presss 'Continue' or 'Enter' key to perform find.</span>
        </div>
    </div>
<?php }else{ ?>
    <div class="logo float_left">
        <div class="title_top_ctrl float_left">
            <h2><?php echo $title_entry; ?> (Find mode)</h2>
            <span>Request 1 of 1 find requests. Presss 'Continue' or 'Enter' key to perform find.</span>
        </div>
    </div>
<?php } ?>