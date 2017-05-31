<?php $i = 2; ?>
<br>
<?php foreach ($arr_docs as $value) : ?>
    <?php
    if ($i == 2)
        $i = $i - 1;
    else
        $i = $i + 1;
    ?>
    <ul class="ul_mag clear bg<?php echo $i; ?> " id="docs_<?php echo (string) $value['_id']; ?>">
        <li class="hg_padd" style="width:1%">
            <a style="color: blue" href="<?php echo URL; ?>/docs/entry/<?php echo (string) $value['_id']; ?>"><span class="icon_emp"></span></a>
        </li>
        <li class="hg_padd" style="width:27%"><?php echo $value['name']; ?></li>
        <li class="hg_padd" style="width:10%"><?php if (isset($value['category']) && isset($arr_docs_category[$value['category']])) echo $arr_docs_category[$value['category']]; ?></li>
        <li class="hg_padd" style="width:6%"><?php echo $value['ext']; ?></li>
        <li class="hg_padd" style="width:14%"><?php echo $value['type']; ?></li>
        <li class="hg_padd" style="width:17%"><?php echo $value['description']; ?></li>
        <li class="hg_padd center_text" style="width:6%"><?php echo $this->Common->format_date($value['_id']->getTimestamp()); ?></li>
        <li class="hg_padd bor_mt" style="width:3%">
            <div class="middle_check">
                <a href="javascript:void(0)" title="Delete link" onclick="docs_lists_delete('<?php echo (string) $value['_id']; ?>')">
                    <span class="icon_remove2"></span>
                </a>
            </div>
        </li>
    </ul>
<?php endforeach; ?>
<?php
    echo $this->element('popup/pagination_lists');
?>