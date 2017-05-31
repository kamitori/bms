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
                <label>No</label>
                <span id="no" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:15%">
                <label>Company Name</label>
                <span id="name" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:10%">
                <label>Type</label>
                <span id="is_customer" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:8%">
                <label>Phone</label>
                <span id="phone" class="desc"></span>
            </li>
             <li class="hg_padd " style="width:10%">
                <label>Fax</label>
                <span id="fax" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:10%">
                <label>Email</label>
                <span id="email" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:10%">
                <label>Our Rep</label>
                <span id="our_rep" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:10%">
                <label>Our CSR</label>
                <span id="our_csr" class="desc"></span>
            </li>
            <li class="hg_padd bor_mt" style="width:.5%"></li>
        </ul>

        <div id="lists_view_content">
            <?php echo $this->element('../Companies/lists_ajax') ?>
        </div>

    </div>
     </form>
</div>
<script type="text/javascript">
    function delete_record(id) {
        confirms("Message", "Are you sure you want to delete?",
                function() {
                    $.ajax({
                        url: '<?php echo URL; ?>/companies/delete/' + id,
                        timeout: 15000,
                        success: function(html) {
                            if (html == "ok") {
                                $("#company_" + id).fadeOut();
                            }
                        }
                    });
                }, function() {
            //else do somthing
        });

        return false;
    }
</script>