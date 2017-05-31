<?php
$i = 0;
$k = 1;
foreach ($arr_timelogs as $key => $value) {
    $i = 1 - $i;
    ?>
    <ul class="ul_mag clear <?php if ($k == 1) { ?>indent_ul_top<?php
        $k = 3;
    }
    ?> <?php if ($i == 1) { ?>bg1<?php } else { ?>bg2<?php } ?>" id="timelog_<?php echo $value['_id']; ?>">
        <li class="hg_padd" style="width:1%">
            <a href="<?php echo URL; ?>/timelogs/entry/<?php echo $value['_id'] ?>"><span class="icon_emp"></span></a>
        </li>
        <li class="hg_padd" style="width:8%"><?php echo $value['employee_name'] ?></li>
        <li class="hg_padd center_txt" style="width:5%"><?php echo $this->Common->format_date( $value['date']->sec) ?></li>
        <li class="hg_padd" style="width:6%"><?php echo $value['category'] ?></li>
        <li class="hg_padd" style="width:20%"><?php echo $value['comment'] ?></li>
        <li class="hg_padd center_txt" style="width:4%"><?php echo $value['total_time'] ?></li>
        <li class="hg_padd center_txt" style="width:4%"><?php echo $value['task_no'] ?></li>
        <li class="hg_padd center_txt" style="width:4%"><?php echo $value['stage_no'] ?></li>
        <li class="hg_padd center_txt" style="width:4%"><?php echo $value['job_no'] ?></li>
        <li class="hg_padd" style="width:8%"><?php echo $value['customer'] ?></li>
        <li class="hg_padd" style="width:8%"><?php echo $value['job_name'] ?></li>
        <li class="hg_padd center_txt" style="width:3%">
            <div class="select_inner width_select" style="margin:2px 0 0 18px;">
                <label class="m_check2">
                    <input type="checkbox" <?php if ($value['billed']) echo 'checked="checked"' ?> disabled="disabled"/>
                    <span></span>
                </label>
            </div>
        </li>
        <li class="hg_padd bor_mt" style="width:3%">
            <div class="middle_check">
                <a href="javascript:void(0)" title="Delete link" onclick="lists_delete('<?php echo $value['_id']; ?>')">
                    <span class="icon_remove2"></span>
                </a>
            </div>
        </li>
    </ul>
<?php }
    echo $this->element('popup/pagination_lists');
?>
