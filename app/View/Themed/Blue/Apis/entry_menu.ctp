<div class="logo float_left">
    <ul class="top_control float_left">
        <li><a href="<?php echo URL.'/'.$controller;?>/first/" class="prev_1"></a></li>
        <li><a href="<?php echo URL.'/'.$controller;?>/prevs/" class="prev"></a></li>
        <li><a href="<?php echo URL.'/'.$controller;?>/nexts/" class="next"></a></li>
        <li><a href="<?php echo URL.'/'.$controller;?>/lasts/" class="next_1"></a></li>
    </ul>
    <div class="title_top_ctrl float_left">
        <h2><?php if(isset($arr_settings['module_label'])) echo $arr_settings['module_label'];?></h2>
        <span>Record <?php if(isset($entry_menu['page'])) echo $entry_menu['page']; else echo '0';?> of <?php if(isset($entry_menu['search'])) echo $entry_menu['search']; else echo '0';?>. Total <?php if(isset($entry_menu['total'])) echo $entry_menu['total']; else echo '0';?></span>
    </div>
</div>