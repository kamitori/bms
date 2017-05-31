<?php $i = 2; ?><br>
<?php foreach ($arr_enquiries as $value) : ?>
    <?php
    if ($i == 2)
        $i = $i - 1;
    else
        $i = $i + 1;
    ?>
    <ul class="ul_mag clear bg<?php echo $i; ?> " id="enquiries_<?php echo (string) $value['_id']; ?>">
        <li class="hg_padd" style="width:1%"><a style="color: blue" href="<?php echo URL; ?>/enquiries/entry/<?php if(isset($value['_id']))echo $value['_id']; ?>"><span class="icon_emp"></span></a></li>
        <li class="hg_padd" style="width:12%"><?php if(isset($value['company']))echo $value['company']; ?></li>
        <li class="hg_padd" style="width:10%"><?php if(isset($value['contact_name']))echo $value['contact_name']; ?></li>
        <li class="hg_padd" style="width:5%"><?php if(isset($value['direct_phone']))echo $value['direct_phone']; ?></li>
        <li class="hg_padd" style="width:6%"><?php if (isset($value['date']) && is_object($value['date'])) echo $this->Common->format_date($value['date']->sec); ?></li>
        <li class="hg_padd" style="width:10%"><?php if(isset($value['our_rep']))echo $value['our_rep']; ?></li>
        <li class="hg_padd" style="width:10%"><?php if (isset($value['enquiry_value'])) echo $value['enquiry_value']; ?></li>
        <li class="hg_padd" style="width:7%"><?php if (isset($value['status'])) echo $value['status']; ?></li>
        <li class="hg_padd" style="width:3%"><?php if (isset($value['rating'])) echo $value['rating']; ?></li>
        <li class="hg_padd" style="width:17%"><?php if (isset($value['detail'])) echo $value['detail']; ?></li>
        <?php if( $this->Common->check_permission($controller.'_@_entry_@_delete', $arr_permission) ){ ?>
            <li class="hg_padd bor_mt" style="width:2%">
                <div class="middle_check">
                    <a href="javascript:void(0)" title="Delete this" onclick="enquiries_lists_delete('<?php if(isset($value['_id']))echo $value['_id']; ?>')">
                        <span class="icon_remove2"></span>
                    </a>
                </div>
            </li>
         <?php } ?>
    </ul>
<?php endforeach; echo $this->element('popup/pagination_lists');?>