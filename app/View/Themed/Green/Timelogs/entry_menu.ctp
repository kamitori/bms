<div class="logo float_left">
    <?php if ($action == 'entry') { ?>
        <ul class="top_control float_left">
            <li><a href="<?php echo URL; ?>/timelogs/entry/first" class="prev_1"></a></li>
            <li><a href="<?php if (isset($arr_prev['_id'])) { ?><?php echo URL; ?>/timelogs/entry/<?php echo (string) $arr_prev['_id']; ?>/<?php echo $num_position - 1; ?><?php } else { ?>#<?php } ?>" class="prev"></a></li>
            <li><a href="<?php if (isset($arr_next['_id'])) { ?><?php echo URL; ?>/timelogs/entry/<?php echo (string) $arr_next['_id']; ?>/<?php echo $num_position + 1; ?><?php } else { ?>#<?php } ?>" class="next"></a></li>
            <li><a href="<?php echo URL; ?>/timelogs/entry" class="next_1"></a></li>
            <!-- <li><a href="#" class="play"></a></li> -->
        </ul>
    <?php } ?>

    <div class="title_top_ctrl float_left">
        <h2>Doc</h2>
        <?php if ($action == 'entry') { ?>
            <span>Record <?php echo $num_position; ?> of <?php echo $sum; ?>. Total <?php echo $sum; ?></span>
        <?php } ?>
    </div>

</div>