<?php echo $this->element('entry_tab_option', array('no_show_delete' => true)); ?>
<?php echo $this->element('js/lists_view') ?>
<div id="content" class="fix_magr">
    <form method="POST" id="sort_form">
        <input type="hidden" name="offset" id="offset" value="<?php echo LIST_LIMIT ?>" />
        <input type="hidden" name="sort_type" id="sort_type" value="desc" />
        <input type="hidden" name="sort_key" id="sort_key" value="_id" />
   
        <div class="w_ul2 ul_res2">
            <ul class="ul_mag clear bg top_header_inner2 ul_res2" id="sort">
                <li class="hg_padd" style="width:1%"></li>
                <li class="hg_padd" style="width:27%">
                    <label>Document Name</label>
                    <span id="name" class="desc"></span>
                </li>
                <li class="hg_padd" style="width:10%">
                    <label>Category</label>
                    <span id="category" class="desc"></span>
                </li>
                <li class="hg_padd" style="width:6%">
                    <label>Ext</label>
                    <span id="ext" class="desc"></span>
                </li>
                <li class="hg_padd" style="width:14%">
                    <label>File type</label>
                    <span id="type" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:17%">
                    <label>Description</label>
                    <span id="description" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:6%">
                    <label>Date</label>
                    <span id="_id" class="desc"></span>
                </li>
                <li class="hg_padd bor_mt" style="width:3%"></li>
            </ul>

            <div id="lists_view_content">
                <!-- goi lists ajax -->
                <?php echo $this->element('../Docs/lists_ajax') ?>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">

    function docs_lists_delete(id) {
        confirms("Message", "Are you sure you want to delete?",
                function() {
                    $.ajax({
                        url: '<?php echo URL; ?>/docs/lists_delete/' + id,
                        timeout: 15000,
                        success: function(html) {
                            if (html == "ok") {
                                $("#docs_" + id).fadeOut();
                            } else {
                                console.log(html);
                            }
                        }
                    });
                }, function() {
            //else do somthing
        });
        return false;
    }
</script>