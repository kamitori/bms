<?php if(!isset($title_entry)){ ?>

    <div class="logo float_left">
        <div class="title_top_ctrl float_left">
            <h2><?php if(isset($arr_settings['module_label'])) echo $arr_settings['module_label'];?></h2>
        </div>
    </div>

<?php }else{ ?>

    <div class="logo float_left">
        <div class="title_top_ctrl float_left">
            <h2><?php echo $title_entry; ?></h2>
            <?php if(isset($sum)){ ?>
                <span>Total <?php echo $sum; ?></span>
            <?php } ?>
        </div>
    </div>

<?php } ?>