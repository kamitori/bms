<div id="content" class="fix_magr">
    <div class="w_ul2 ul_res2">
        <ul class="ul_mag clear bg top_header_inner2 ul_res2">
            <li class="hg_padd" style="width:2%"></li>
            <li class="hg_padd center_txt" style="width:3%">Ref no</li>
            <li class="hg_padd center_txt" style="width:34%">Task</li>
            <li class="hg_padd center_txt" style="width:3%">Job no</li>
            <li class="hg_padd center_txt" style="width:9%">Responsible</li>
            <li class="hg_padd center_txt" style="width:8%">Type</li>
            <li class="hg_padd center_txt" style="width:8%">Work Start</li>
            <li class="hg_padd center_txt" style="width:8%">Work End</li>
            <li class="hg_padd center_txt" style="width:6%">Status</li>
            <li class="hg_padd center_txt" style="width:3%">Late</li>
            <li class="hg_padd bor_mt" style="width:3%"></li>
        </ul>
        <?php
        $i = 0;
        $k = 1;
        foreach ($arr_timelogs as $key => $value) {
            $i = 1 - $i;
            ?>
            <ul class="ul_mag clear <?php if ($k == 1) { ?>indent_ul_top<?php
                $k = 3;
            }
            ?> <?php if ($i == 1) { ?>bg1<?php } else { ?>bg2<?php } ?>" id="tasks_<?php echo $value['_id']; ?>">
                <li class="hg_padd" style="width:2%">
                    <a style="color: blue" href="<?php echo URL; ?>/tasks/entry/<?php echo $value['_id']; ?>"><span class="icon_emp"></span></a>
                </li>
                <li class="hg_padd center_txt" style="width:3%"><?php echo $value['no']; ?></li>
                <li class="hg_padd" style="width:34%"><?php echo $value['name']; ?></li>
                <li class="hg_padd" style="width:3%">
                    <?php if (isset($value['job_id']) && isset($arr_jobs[(string) $value['job_id']])) { ?>
                        <a href="<?php echo URL; ?>/jobs/entry/<?php echo $value['job_id']; ?>">
                            <?php echo $arr_jobs[(string) $value['job_id']]; ?>
                        </a>
                    <?php } ?>
                </li>
                <li class="hg_padd" style="width:9%">
                    <?php if (isset($arr_contacts[(string) $value['our_rep_id']])) { ?>
                        <a href="<?php echo URL; ?>/contacts/entry/<?php echo $value['our_rep_id']; ?>">
                            <?php echo $arr_contacts[(string) $value['our_rep_id']]; ?>
                        </a>
                    <?php } ?>
                </li>
                <li class="hg_padd" style="width:8%"><?php if (isset($value['type']) && isset($arr_tasks_type[$value['type']])) echo $arr_tasks_type[$value['type']]; ?></li>
                <li class="hg_padd center_txt" style="width:8%"><?php echo $this->Common->format_date($value['work_start']->sec); ?></li>
                <li class="hg_padd center_txt" style="width:8%"><?php echo $this->Common->format_date($value['work_end']->sec); ?></li>
                <li class="hg_padd" style="width:6%"><?php if (isset($value['status'])) echo $value['status']; ?></li>
                <li class="hg_padd" style="width:3%">
                    <div class="select_inner width_select" style="width: 100%; margin: 0;margin-left: 23px;">
                        <label class="m_check2">
                            <?php
                            if (isset($value['work_end']) && is_object($value['work_end'])) {
                                if ($value['work_end']->sec < strtotime('now')) {
                                    echo $this->Form->input('Address.default', array(
                                        'type' => 'checkbox',
                                        'checked' => true,
                                        'disabled' => true,
                                        'class' => 'checkbox-default'
                                    ));
                                } else {
                                    echo $this->Form->input('Address.default', array(
                                        'type' => 'checkbox',
                                        'checked' => false,
                                        'disabled' => true,
                                        'class' => 'checkbox-default'
                                    ));
                                }
                            }
                            ?>
                        </label>
                    </div>
                </li>
                <li class="hg_padd bor_mt" style="width:3%">
                    <div class="middle_check">
                        <a href="javascript:void(0)" title="Delete link" onclick="tasks_lists_delete('<?php echo $value['_id']; ?>')">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
        <?php } ?>

    </div>
</div>

<script type="text/javascript">
                            function tasks_lists_delete(id) {

                                confirms("Message", "Are you sure you want to delete?",
                                        function() {
                                            $.ajax({
                                                url: '<?php echo URL; ?>/tasks/lists_delete/' + id,
                                                timeout: 15000,
                                                success: function(html) {
                                                    if (html == "ok") {

                                                        $("#tasks_" + id).fadeOut();
                                                    }
                                                    console.log(html);
                                                }
                                            });
                                        }, function() {
                                    //else do somthing
                                });
                                return false;
                            }
</script>