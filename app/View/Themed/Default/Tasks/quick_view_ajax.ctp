<?php $i = 2 ?>
<?php foreach ($arr_tasks as $value): ?>
    <?php
    if ($i == 2)
        $i = $i - 1;
    else
        $i = $i + 1;
    ?>
    <ul class="ul_mag clear bg<?php echo $i ?>">
        <li class="hg_padd" style="width: 1%"> <a href="<?php echo URL . '/tasks/entry/' . $value['_id'] ?>"><span class="icon_emp float_left"></span></a></li>
        <li class="hg_padd center_txt" style="width:5%">
            <?php echo date('m/d/y', $value['work_start']->sec) ?>
        </li>
        <li class="hg_padd center_txt" style="width:4%"><?php echo $value['work_start_hour'] ?></li>
        <li class="hg_padd" style="width:16%"><?php echo $value['name'] ?></li>
        <li class="hg_padd" style="width:6%"><?php echo $value['type'] ?></li>
        <li class="hg_padd" style="width:6%"><?php echo $value['status'] ?></li>
        <li class="hg_padd center_txt" style="width:4%">
            <div class="middle_check">
                <label class="m_check2">
                    <?php
                    if (isset($value['work_end']) && is_object($value['work_end'])) {
                        if ($value['work_end']->sec < strtotime('now')) {
                            echo '<input type="checkbox" disabled="" checked="">';
                        } else {
                            echo '<input type="checkbox" disabled="">';
                        }
                    }
                    ?>
                    <span class="bx_check"></span>
                </label>
            </div>
        </li>
        <li class="hg_padd" style="width:12%"><?php echo $value['our_rep'] ?></a></li>
        <li class="hg_padd border_left" style="width:4%">
            <a href="<?php echo URL . '/jobs/entry/' . $value['job_id'] ?>">
                <span class="icon_emp float_left"></span>
                <?php echo $value['job_no'] ?>
            </a>
        </li>
        <li class="hg_padd" style="width:10%; text-align: right"><?php echo $value['job_name'] ?></li>
    </ul>
<?php endforeach ?>
