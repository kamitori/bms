<?php
    echo $this->element('../Salesinvoices/entry_tab_option', array('no_show_delete' => true));
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
                <label>Contact</label>
                <span id="contact_name" class="desc"></span>
            </li>
            <li class="hg_padd center_txt" style="width:5%">
                <label>Date</label>
                <span id="invoice_date" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:15%">
                <label>Heading</label>
                <span id="heading" class="desc"></span>
            </li>
            <li class="hg_padd right_txt" style="width:15%">
                <label>Total Sales order</label>
            </li>
            <li class="hg_padd " style="width:10%">
                <label>Job</label>
                <span id="job_number" class="desc"></span>
            </li>
            <li class="hg_padd right_txt" style="width:8%">
                <label>Total sales</label>
                <span id="sum_sub_total" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:5%">
                <label>Status</label>
                <span id="invoice_status" class="desc"></span>
            </li>
            <li class="hg_padd bor_mt" style="width:.5%"></li>
        </ul>

        <div id="lists_view_content">
            <?php echo $this->element('../Salesinvoices/lists_ajax') ?>
        </div>

    </div>
     </form>
</div>
<script type="text/javascript">
    function salesinvoices_lists_delete(id) {
        confirms("Message", "Are you sure you want to delete?",
                function() {
                    $.ajax({
                        url: '<?php echo URL; ?>/salesinvoices/delete/' + id,
                        timeout: 15000,
                        success: function(html) {
                            if (html == "ok") {
                                $("#invoice_" + id).fadeOut();
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