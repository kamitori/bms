<script type="text/javascript">
    $(function(){
      //  delete_message_detail_null('<?php echo $message_type; ?>');
     });
</script>
<div class="tab_1 full_width">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4>
                <?php echo $message_type;?>
            </h4>
        </span>
        <a title="Add new message" href="javascript:void(0)" onclick="settings_listmenu_detail_add('<?php echo $message_type; ?>')">
            <span class="icon_down_tl top_f"></span>
        </a>


    </span>
    <ul class="ul_mag clear bg3">

        <li class="hg_padd" style="width:62%">Content</li>
        <li class="hg_padd" style="width:35%">Key</li>


    </ul>
    <input type="hidden" id="all_field_of_option" >
    <div class="container_same_category" style="height:449px">
        <?php
        $i = 1;
        foreach ($arr_setting as $key) {

                $i = 3 - $i;
            ?>

            <?php echo $this->Form->create('Setting', array('id' => 'SettingForm_content')); ?>

            <?php echo $this->Form->hidden('Setting._id', array('value' => $key['_id'])); ?>

            <ul class="ul_mag clear bg<?php echo $i; ?>">
                <li class="hg_padd line_mg" style="width:62%; position: relative">
                    <?php
                    echo $this->Form->input('Setting.content', array(
                        'class' => 'input_inner input_inner_w bg' . $i,
                        'value' => $key['content']
                    ));
                    ?>
                </li>
                <li class="hg_padd line_mg" style="width:35%; position: relative">
                    <?php
                    echo $this->Form->input('Setting.key', array(
                        'class' => 'input_inner input_inner_w bg' . $i,
                        'value' => $key['key']
                    ));
                    ?>
                </li>
            </ul>
            <?php echo $this->Form->end(); ?>
        <?php } ?>
    </div>

    <span class="title_block bo_ra2">
        <span class="float_left bt_block"><?php echo translate('Edit or create values for list'); ?>.</span>
    </span>
</div>

<script type="text/javascript">

    $(function(){

        $('.container_same_category').mCustomScrollbar({
            scrollButtons:{
                enable:false
            }
        });
    });
</script>