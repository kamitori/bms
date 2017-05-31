<?php
    $i = 2;
    $delete = $this->Common->check_permission($controller.'_@_entry_@_delete',$arr_permission);
?><br>
<?php foreach ($arr_users as $user) {
        if ($i == 2)
            $i = $i - 1;
        else
            $i = $i + 1;
        $user = array_merge(array(
                                    'no'    => '',
                                    'user_name'  => ''
                                ), $user);
?>
    <ul class="ul_mag clear bg<?php echo $i ?>" id="user_<?php echo $user['_id']; ?>">
        <li class="hg_padd" style="width:.5%">
            <a href="<?php echo URL; ?>/users/entry/<?php echo $user['_id']; ?>"><span class="icon_emp"></span></a>
        </li>
        <li class="hg_padd " style="width:25%">
            <?php echo $user['no'];  ?>
        </li>
        <li class="hg_padd" style="width:35%">
            <?php echo $user['user_name'];  ?>
        </li>
        <li class="hg_padd bor_mt" style="width:.5%">
            <?php if($delete){ ?>
            <div class="middle_check">
                <a href="javascript:void(0)" title="Delete link" onclick="delete_record('<?php echo $user['_id']; ?>')">
                    <span class="icon_remove2"></span>
                </a>
            </div>
        <?php } ?>
        </li>
    </ul>
<?php }
echo $this->element('popup/pagination_lists');
?>