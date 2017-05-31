<?php $i = 1 ?><br>
<?php foreach ($arr_emailtemplates as $value):

    $i = 3 - $i;
?>

    <ul class="ul_mag clear bg<?php echo $i ?>" id="emailtemplates_<?php if(isset($value['_id']))echo (string) $value['_id']; ?>">

        <li class="hg_padd" style="width:1%">
            <a style="color: blue" href="<?php echo URL; ?>/emailtemplates/entry/<?php if(isset($value['_id']))echo $value['_id']; ?>"><span class="icon_emp"></span></a>
        </li>
        <li class="hg_padd center_txt" style="width:5%"><?php if(isset($value['no']))echo $value['no']; ?></li>
        <li class="hg_padd" style="width:31%"><?php if(isset($value['name']))echo $value['name']; ?></li>
        <li class="hg_padd" style="width:15%"><?php if(isset($value['type']))echo $value['type']; ?></li>
        <li class="hg_padd" style="width:15%"><?php if(isset($value['folder']))echo $value['folder']; ?></li>
        <li class="hg_padd" style="width:10%">
            <?php
                if(isset($value['created_by'])&&is_object($value['created_by'])){
                    $contact = $model_contact->select_one(array('_id'=> new MongoId($value['created_by'])));
                    echo (isset($contact['full_name'])?$contact['full_name'] : '');
                }
            ?>
        </li>
        <li class="hg_padd" style="width:10%">
            <?php
                if(isset($value['modified_by'])&&is_object($value['modified_by'])){
                    $contact = $model_contact->select_one(array('_id'=> new MongoId($value['modified_by'])));
                    echo (isset($contact['full_name'])?$contact['full_name'] : '');
                }
            ?>
        </li>
        <li class="hg_padd" style="width:4%">
            <div class="middle_check">
                <a href="javascript:void(0)" title="Delete link" onclick="emailtemplates_lists_delete('<?php if(isset($value['_id']))echo $value['_id']; ?>')">
                    <span class="icon_remove2"></span>
                </a>
            </div>
        </li>
    </ul>
<?php endforeach; ?>

<?php echo $this->element('popup/pagination_lists'); ?>