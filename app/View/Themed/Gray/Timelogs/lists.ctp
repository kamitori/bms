<?php echo $this->element('entry_tab_option', array('no_show_delete' => true)); ?>
<?php echo $this->element('js/lists_view'); ?>
<div id="content" class="fix_magr">
    <form method="POST" id="sort_form">
    <div class="w_ul2 ul_res2">
        <ul class="ul_mag clear bg top_header_inner2 ul_res2">
            <li class="hg_padd center_txt" style="width:1%"></li>
            <li class="hg_padd center_txt" style="width:8%">
                Employee
                <span id="employee_name" class="desc"></span>
            </li>
            <li class="hg_padd center_txt" style="width:5%">
                Date
                <span id="date" class="desc"></span>
            </li>
            <li class="hg_padd center_txt" style="width:6%">Category</li>
            <li class="hg_padd center_txt" style="width:20%">
                Comment
                <span id="comment" class="desc"></span>
            </li>
            <li class="hg_padd center_txt" style="width:4%">
                Time
                <span id="total_time" class="desc"></span>
            </li>
            <li class="hg_padd center_txt" style="width:4%">
                Task no
                <span id="task_no" class="desc"></span>
            </li>
            <li class="hg_padd center_txt" style="width:4%">
                Stage no
                <span id="stage_no" class="desc"></span>
            </li>
            <li class="hg_padd center_txt" style="width:4%">
                Job no
                <span id="job_no" class="desc"></span>
            </li>
            <li class="hg_padd center_txt" style="width:8%">
                Customer
                <span id="customer" class="desc"></span>
            </li>
            <li class="hg_padd center_txt" style="width:8%">
                Job name
                <span id="jonb_name" class="desc"></span>
            </li>
            <li class="hg_padd center_txt" style="width:3%">
                Bill
                <span id="billed" class="desc"></span>
            </li>
            <li class="hg_padd bor_mt" style="width:3%"></li>
        </ul>
        <div id="lists_view_content">
            <?php echo $this->element('../Timelogs/lists_ajax') ?>
        </div>
    </div>
    </form>
</div>

<script type="text/javascript">
    function lists_delete(id) {
        confirms("Message", "Are you sure you want to delete?",
                function() {
                    $.ajax({
                        url: '<?php echo URL; ?>/timelogs/lists_delete/' + id,
                        timeout: 15000,
                        success: function(html) {
                            if (html == "ok") {
                                $("#timelog_" + id).fadeOut();
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