<?php
$i = 2;
?>
<?php foreach ($arr_jobs as $value): ?>
    <?php
    if ($i == 2) {
        $i = $i - 1;
    } else {
        $i = $i + 1;
    }
    $contact = $value['contacts'][$value['contacts_default_key']]['contact_name'];
    ?>

    <ul class="ul_mag clear bg<?php echo $i ?>">
        <li class="hg_padd" style="width: 1%"> <a href="<?php echo URL . '/jobs/entry/' . $value['_id'] ?>"><span class="icon_emp float_left"></span></a></li>
        <li class="hg_padd center_txt" style="width:4%"><?php echo date('m/d/y', $value['work_start']->sec) ?></li>
        <li class="hg_padd" style="width:10%"><?php echo $value['contact_name'] ?></li>
        <li class="hg_padd center_txt" style="width:6%"><?php echo $value['type'] ?></li>
        <li class="hg_padd center_txt" style="width:6%"><?php echo $value['status'] ?></li>
        <li class="hg_padd" style="width:10%"><?php echo $contact ?></li>
        <li class="hg_padd center_txt" style="width:4%"><?php echo $value['no'] ?></li>
        <li class="hg_padd no_border" style="width:30%"><?php echo $value['name'] ?></li>
    </ul>
<?php endforeach ?>