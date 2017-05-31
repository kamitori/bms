<?php echo $this->element('entry_tab_option', array('no_show_delete' => true)); ?>
<?php echo $this->element('js/lists_view'); ?>
<div id="content" class="fix_magr">
    <form method="POST" id="sort_form">
        <div class="w_ul2 ul_res2">
            <ul class="ul_mag clear bg top_header_inner2 ul_res2" id="sort">
                <li class="hg_padd" style="width:1%"></li>
                <li class="hg_padd center_txt" style="width:4%">
                    <label>Ref no</label>
                    <span id="no" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:33%">
                    <label>Task</label>
                    <span id="name" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:4%">
                    <label>Job no</label>
                    <span id="job_id" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:9%">
                    <label>Responsible</label>
                    <span id="our_rep_id" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:8%">
                    <label>Type</label>
                    <span id="type" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:8%">
                    <label>Work Start</label>
                    <span id="work_start" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:8%">
                    <label>Work End</label>
                    <span id="work_end" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:6%">
                    <label>Status</label>
                    <span id="status" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:3%">
                    <label>Late</label>
                </li>
                <li class="hg_padd bor_mt" style="width:3%"></li>
            </ul>
            <div id="lists_view_content">
                <?php echo $this->element('../Tasks/lists_ajax') ?>
            </div>
        </div>
    </form>
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