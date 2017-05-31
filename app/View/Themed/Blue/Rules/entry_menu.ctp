<div class="logo float_left">
    <ul class="top_control float_left">
        <li><a href="<?php echo URL.'/'.$controller;?>/first/" class="prev_1"></a></li>
        <li><a href="<?php echo URL.'/'.$controller;?>/prevs/" class="prev"></a></li>
        <li><a href="<?php echo URL.'/'.$controller;?>/nexts/" class="next"></a></li>
        <li><a href="<?php echo URL.'/'.$controller;?>/lasts/" class="next_1"></a></li>
    </ul>
    <div class="title_top_ctrl float_left">
        <h2><?php if(isset($arr_settings['module_label'])) echo $arr_settings['module_label'];?></h2>
        <span>Record <?php echo $entry_menu['page'];?> of <?php echo $entry_menu['total'];?>. Total <?php echo $entry_menu['total'];?></span>
    </div>
</div>