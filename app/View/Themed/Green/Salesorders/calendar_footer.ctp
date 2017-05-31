<div id="footer">
    <div class="bg_footer">

        <?php echo $this->element('footer_right'); ?>

        <form class="float_right" action="" id="<?php echo $model; ?>FilterCalendar" method="post" accept-charset="utf-8">
            <div class="status1 float_left">
                <span class="title_small float_left">Status</span>
                <span class="block_sear bl_t2 float_right">
                    <span class="bg_search_1"></span>
                    <span class="bg_search_2"></span>
                    <div class="box_inner_search float_left">
                        <span class="icon_search"></span>
                        <?php echo $this->Form->input($model.'.status_filter', array(
                                'options' => $arr_status,
                                'class' => 'sele3_fix_s float_left',
                                'onchange' => $controller.'_calendar_onchange_status()',
                                'empty' => '   '
                        )); ?>
                        <span class="icon_show"></span>
                        <span class="icon_close" onclick="$('#<?php echo $model; ?>StatusFilter').val('');<?php echo $controller; ?>_calendar_onchange_status();"></span>
                    </div>
                </span>
            </div>
        </form>
    </div>
</div>