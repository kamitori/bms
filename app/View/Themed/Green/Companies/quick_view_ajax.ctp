<?php $i = 2 ?>
<?php foreach ($arr_companys as $value): ?>
    <?php
    if ($i == 2)
        $i = $i - 1;
    else
        $i = $i + 1;
    ?>
    <?php $address_1 = $value['addresses'][$value['addresses_default_key']]['address_1']?>
    <ul class="ul_mag clear bg<?php echo $i ?>">
        <li class="hg_padd" style="width: 1%"> <a href="<?php echo URL . '/companys/entry/' . $value['_id'] ?>"><span class="icon_emp float_left"></span></a></li>
        <li class="hg_padd" style="width:12%"><?php echo $value['name'] ?></li>
        <li class="hg_padd center_txt" style="width:6%">
            <div class="middle_check">
                <label class="m_check2">
                    <?php if ($value['is_customer'] == 1): ?>
                        <input type="checkbox" disabled="" checked="">
                    <?php endif ?>
                    <span class="bx_check"></span>
                </label>
            </div>  
        </li>
        <li class="hg_padd" style="width:6%">
            <div class="middle_check">
                <label class="m_check2">
                    <?php if ($value['is_supplier'] == 1): ?>
                        <input type="checkbox" disabled="" checked="">
                    <?php endif ?>
                    <span class="bx_check"></span>
                </label>
            </div>  
        </li>
        <li class="hg_padd" style="width:8%"><?php echo $value['phone'] ?></li>
        <li class="hg_padd" style="width:14%"><?php echo $address_1 ?></li>
    </ul>
<?php endforeach ?>
