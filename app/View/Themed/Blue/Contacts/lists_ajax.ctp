<?php
    $i = 2;
    $delete = $this->Common->check_permission($controller.'_@_entry_@_delete',$arr_permission);
?><br>
<?php foreach ($arr_contacts as $contact){
    if ($i == 2)
        $i = $i - 1;
    else
        $i = $i + 1;
    ?>
    <ul class="ul_mag clear bg<?php echo $i ?>" id="contact_<?php echo (string) $contact['_id']; ?>">
        <li class="hg_padd" style="width:.5%">
            <a href="<?php echo URL; ?>/contacts/entry/<?php echo $contact['_id']; ?>"><span class="icon_emp"></span></a>
        </li>
        <li class="hg_padd" style="width:2%"><?php if(isset($contact['no'])) echo $contact['no']; ?></li>
        <li class="hg_padd" style="width:4%"><?php if(isset($contact['title'])) echo $contact['title']; ?></li>
        <li class="hg_padd" style="width:8%"><?php if(isset($contact['first_name'])) echo $contact['first_name']; ?></li>
        <li class="hg_padd" style="width:8%"><?php if(isset($contact['last_name'])) echo $contact['last_name']; ?></li>
        <li class="hg_padd" style="width:6%"><?php if(isset($contact['type'])) echo $contact['type']; ?></li>
        <li class="hg_padd" style="width:16%"><?php if(isset($contact['email'])) echo $contact['email']; ?></li>
        <li class="hg_padd" style="width:8%"><?php if(isset($contact['direct_dial'])) echo $contact['direct_dial']; ?></li>
        <li class="hg_padd" style="width:10%"><?php if(isset($contact['mobile'])) echo $contact['mobile']; ?></li>
        <li class="hg_padd" style="width:8%"><?php if(isset($contact['home_phone'])) echo $contact['home_phone']; ?></li>
        <li class="hg_padd" style="width:15%">
            <?php if (isset($contact['company_id']) && is_object($contact['company_id']) ) { ?>
                <a style="text-decoration: none;" href="<?php echo URL; ?>/companies/entry/<?php echo $contact['company_id']; ?>">
                    <?php echo $contact['company']; ?>
                </a>
            <?php } ?>
        </li>
        <li class="hg_padd bor_mt" style="width:.5%">
            <?php if($delete){ ?>
            <div class="middle_check">
                <a href="javascript:void(0)" title="Delete link" onclick="contacts_lists_delete('<?php echo $contact['_id']; ?>')">
                    <span class="icon_remove2"></span>
                </a>
            </div>
        <?php } ?>
        </li>
    </ul>
<?php }
echo $this->element('popup/pagination_lists');
?>