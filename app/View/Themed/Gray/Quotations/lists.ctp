<?php
    echo $this->element('entry_tab_option', array('no_show_delete' => true));
?>
<?php echo $this->element('js/lists_view'); ?>
<div id="content" class="fix_magr">
    <form method="POST" id="sort_form">
    <div class="w_ul2 ul_res2">
        <ul class="ul_mag clear bg top_header_inner2 ul_res2" id="sort">
            <li class="hg_padd" style="width:.5%"></li>
            <li class="hg_padd " style="width:5%">
                <label>Ref no</label>
                <span id="code" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:5%">
                <label>Type</label>
                <span id="quotation_type" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:15%">
                <label>Company</label>
                <span id="contact_name" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:10%">
                <label>Contact</label>
                <span id="contact_name" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:8%">
                <label>Phone</label>
                <span id="phone" class="desc"></span>
            </li>
            <li class="hg_padd center_txt" style="width:5%">
                <label>Date</label>
                <span id="quotation_date" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:10%">
                <label>Our rep</label>
                <span id="our_rep" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:5%">
                <label>Status</label>
            </li>
            <li class="hg_padd " style="width:15%">
                <label>Sales order</label>
                <span id="salesorder_name" class="desc"></span>
            </li>
            <li class="hg_padd right_txt" style="width:7%">
                <label> QT Amount </label>
                <span id="sum_sub_total" class="desc"></span>
            </li>
            <li class="hg_padd bor_mt" style="width:.5%"></li>
        </ul>

        <div id="lists_view_content">
            <?php echo $this->element('../Quotations/lists_ajax') ?>
        </div>

    </div>
     </form>
</div>
<script type="text/javascript">
    function quotations_lists_delete(id) {
        confirms("Message", "Are you sure you want to delete?",
                function() {
                    $.ajax({
                        url: '<?php echo URL; ?>/quotations/delete/' + id,
                        timeout: 15000,
                        success: function(html) {
                            if (html == "ok") {
                                $("#quote_" + id).fadeOut();
                            }
                        }
                    });
                }, function() {
            //else do somthing
        });

        return false;
    }
</script>