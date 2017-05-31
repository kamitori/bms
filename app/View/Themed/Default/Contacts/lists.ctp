<?php
    echo $this->element('entry_tab_option', array('no_show_delete' => true));
?>
<?php echo $this->element('js/lists_view'); ?>
<div id="content" class="fix_magr">
    <form method="POST" id="sort_form">
    <div class="w_ul2 ul_res2">
        <ul class="ul_mag clear bg top_header_inner2 ul_res2" id="sort">
            <li class="hg_padd" style="width:.5%"></li>
            <li class="hg_padd " style="width:2%">
                <label>No</label>
                <span id="title" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:4%">
                <label>Title</label>
                <span id="title" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:8%">
                <label>First Name</label>
                <span id="first_name" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:8%">
                <label>Last Name</label>
                <span id="last_name" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:6%">
                <label>Type</label>
                <span id="type" class="desc"></span>
            </li>
             <li class="hg_padd " style="width:16%">
                <label>Email</label>
                <span id="direct_dial" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:8%">
                <label>Direct Dial</label>
                <span id="direct_dial" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:10%">
                <label>Mobile</label>
                <span id="mobile" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:8%">
                <label>Home Phone</label>
                <span id="home_phone" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:15%">
                <label>Company</label>
                <span id="company" class="desc"></span>
            </li>
            <li class="hg_padd bor_mt" style="width:.5%"></li>
        </ul>

        <div id="lists_view_content">
            <?php echo $this->element('../Contacts/lists_ajax') ?>
        </div>

    </div>
     </form>
</div>
<script type="text/javascript">
    function contacts_lists_delete(id) {
        confirms("Message", "Are you sure you want to delete?",
                function() {
                    $.ajax({
                        url: '<?php echo URL; ?>/contacts/delete/' + id,
                        timeout: 15000,
                        success: function(html) {
                            if (html == "ok") {
                                $("#contact_" + id).fadeOut();
                            }
                        }
                    });
                }, function() {
            //else do somthing
        });

        return false;
    }
</script>