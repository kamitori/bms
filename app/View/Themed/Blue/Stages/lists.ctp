<?php echo $this->element('entry_tab_option', array('no_show_delete' => true)); ?>
<div id="content" class="fix_magr">
    <div class="w_ul2 ul_res2">
        <ul class="ul_mag clear bg top_header_inner2 ul_res2">
            <li class="hg_padd" style="width:2%"></li>
            <li class="hg_padd center_txt" style="width:3%">Stage</li>
            <li class="hg_padd center_txt" style="width:17%">Responsible</li>
            <li class="hg_padd center_txt" style="width:24%">Job</li>
            <li class="hg_padd center_txt" style="width:8%">Customer</li>
            <li class="hg_padd center_txt" style="width:8%">Start</li>
            <li class="hg_padd center_txt" style="width:8%">Finish</li>
            <li class="hg_padd center_txt" style="width:6%">Day left</li>
            <li class="hg_padd center_txt" style="width:6%">Status</li>
            <li class="hg_padd center_txt" style="width:3%">Late</li>
            <li class="hg_padd bor_mt" style="width:3%"></li>
        </ul>

        <?php
            $i = 0; $k = 1;
            foreach ($arr_stages as $key => $value) {
            $i = 1 - $i;
         ?>
        <ul class="ul_mag clear <?php if( $k == 1 ){ ?>indent_ul_top<?php $k = 3; } ?> <?php if( $i == 1 ){ ?>bg1<?php }else{ ?>bg2<?php } ?>" id="stages_<?php echo $value['_id']; ?>">
            <li class="hg_padd" style="width:2%">
                <a style="color: blue" href="<?php echo URL; ?>/stages/entry/<?php echo $value['_id']; ?>"><span class="icon_emp"></span></a>
            </li>
            <li class="hg_padd center_txt" style="width:3%"><?php echo $value['no']; ?></li>
            <li class="hg_padd" style="width:17%"><?php if(isset($arr_stage_stage[$value['stage_id']]))echo $arr_stage_stage[$value['stage_id']]; ?></li>
            <li class="hg_padd" style="width:24%"><?php echo $value['job']; ?></li>
            <li class="hg_padd" style="width:8%"></li>
            <li class="hg_padd center_txt" style="width:8%"><?php echo $this->Common->format_date( $value['work_start']->sec, false); ?></li>
            <li class="hg_padd center_txt" style="width:8%"><?php echo $this->Common->format_date( $value['work_end']->sec, false); ?></li>

            <?php $dayleft = ($value['work_end']->sec - strtotime(date('Y-m-d')))/DAY; ?>
            <li class="hg_padd center_txt" style="width:6%; <?php if($dayleft < 0){ echo 'color:red'; } ?>" ><?php echo $dayleft; ?></li>

            <li class="hg_padd center_txt" style="width:6%"><?php if(isset($arr_stages_status[$value['status_id']]))echo $arr_stages_status[$value['status_id']]; ?></li>
            <li class="hg_padd" style="width:3%">
                <div class="select_inner width_select" style="width: 100%; margin: 0;margin-left: 23px;">
                    <label class="m_check2">
                        <?php
                        if($dayleft < 0) {
                            echo $this->Form->input('Stage.default', array(
                                        'type' => 'checkbox',
                                        'checked' => true,
                                        'disabled' => true,
                                        'class' => 'checkbox-default'
                            ));
                        }else{
                            echo $this->Form->input('Stage.default', array(
                                        'type' => 'checkbox',
                                        'checked' => false,
                                        'disabled' => true,
                                        'class' => 'checkbox-default'
                            ));
                        } ?>
                    </label>
                </div>
            </li>
            <li class="hg_padd bor_mt" style="width:3%">
                <div class="middle_check">
                    <a href="javascript:void(0)" title="Delete link" onclick="stages_lists_delete('<?php echo $value['_id']; ?>')">
                        <span class="icon_remove2"></span>
                    </a>
                </div>
            </li>
        </ul>
        <?php } ?>

    </div>
</div>

<script type="text/javascript">
function stages_lists_delete(id){
    confirms( "Message", "Are you sure you want to delete?",
        function(){
            $.ajax({
                url: '<?php echo URL; ?>/stages/lists_delete/' + id,
                timeout: 15000,
                success: function(html){
                    if(html == "ok"){
                        $("#stages_" +  id).fadeOut();
                    }
                    console.log(html);
                }
            });
        },function(){
            //else do somthing
    });

    return false;
}
</script>