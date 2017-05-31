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
            <li class="hg_padd " style="width:15%">
                <label>Company</label>
                <span id="company_name" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:7%">
                <label>Our CSR</label>
                <span id="our_csr" class="desc"></span>
            </li>
            <li class="hg_padd center_txt" style="width:5%">
                <label>Order date</label>
                <span id="salesorder_date" class="desc"></span>
            </li>
            <li class="hg_padd center_txt" style="width:5%">
                <label>Due date</label>
                <span id="payment_due_date" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:20%">
                <label>Heading</label>
                <span id="heading" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:8%">
                <label>Job</label>
                <span id="job_number" class="desc"></span>
            </li>
            <li class="hg_padd right_txt" style="width:5%">
                <label>Total sales</label>
                <span id="sum_sub_total" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:15%">
                <label>Status</label>
                <span id="asset_status" class="desc"></span>
            </li>
            <li class="hg_padd bor_mt" style="width:.5%"></li>
        </ul>

        <div id="lists_view_content">
            <?php echo $this->element('../Salesorders/lists_ajax') ?>
        </div>
    </div>
     </form>
</div>
<script type="text/javascript">
    function salesorders_lists_delete(id) {
        confirms("Message", "Are you sure you want to delete?",
                function() {
                    $.ajax({
                        url: '<?php echo URL; ?>/salesorders/delete/' + id,
                        timeout: 15000,
                        success: function(html) {
                            if (html == "ok") {
                                $("#order_" + id).fadeOut();
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