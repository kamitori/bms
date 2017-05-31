<div id="footer">
    <div class="bg_footer">
        <?php echo $this->element('footer_right'); ?>
        <form class="float_right" action="" id="SalesorderFilterCalendar" method="post" accept-charset="utf-8">
           <!--  <div class="status3 float_left">
                <span class="title_small float_left">Job no</span>
                <span class="block_sear bl_t2 float_right">
                    <span class="bg_search_1"></span>
                    <span class="bg_search_2"></span>
                    <div class="box_inner_search float_left">
                        <span class="icon_search"></span>
                        <select class="sele3_fix_s float_left">
                            <option selected="selected"></option>
                            <option>Active</option>
                            <option>Disable</option>
                        </select>
                        <span class="icon_show"></span>
                        <span class="icon_close"></span>
                    </div>
                </span>
            </div> -->

            <?php if($type=="Contact"){ ?>
            <!-- <div class="status2 float_left">
                <span class="title_small float_left">Responsible</span>
                <span class="block_sear bl_t2 float_right">
                    <span class="bg_search_1"></span>
                    <span class="bg_search_2"></span>
                    <div class="box_inner_search float_left">
                        <span class="icon_search"></span>
                        <?php /*echo $this->Form->input('Resource.contact', array(
                                'options' => $arr_contact,
                                'class' => 'sele3_fix_s float_left',
                                'onchange' => 'resources_calendar_onchange_contact()',
                                'empty' => '   '
                        ));*/ ?>
                        <span class="icon_show"></span>
                        <span class="icon_close" onclick="$('#ResourceContact').val('');resources_calendar_onchange_contact()"></span>
                    </div>
                </span>
            </div> -->
            <?php }else{ ?>
            <!-- <div class="status2 float_left">
                <span class="title_small float_left">Equipment</span>
                <span class="block_sear bl_t2 float_right">
                    <span class="bg_search_1"></span>
                    <span class="bg_search_2"></span>
                    <div class="box_inner_search float_left">
                        <span class="icon_search"></span>
                        <?php /*echo $this->Form->input('Resource.asset', array(
                                'options' => $arr_asset,
                                'class' => 'sele3_fix_s float_left',
                                'onchange' => 'resources_calendar_onchange_asset()',
                                'empty' => '   '
                        )); */?>
                        <span class="icon_show"></span>
                        <span class="icon_close" onclick="$('#ResourceEquipment').val('');resources_calendar_onchange_asset()"></span>
                    </div>
                </span>
            </div> -->
            <?php } ?>

            <div class="status1 float_left">
                <span class="title_small float_left">Status</span>
                <span class="block_sear bl_t2 float_right">
                    <span class="bg_search_1"></span>
                    <span class="bg_search_2"></span>
                    <div class="box_inner_search float_left">
                        <span class="icon_search"></span>
                        <?php echo $this->Form->input('Resource.status_filter', array(
                                'options' => $arr_status,
                                'class' => 'sele3_fix_s float_left',
                                'onchange' => 'resources_calendar_onchange_status()',
                                'empty' => '   '
                        )); ?>
                        <span class="icon_show"></span>
                        <span class="icon_close" onclick="$('#ResourceStatusFilter').val('');resources_calendar_onchange_status();"></span>
                    </div>
                </span>
            </div>
        </form>
    </div>
</div>