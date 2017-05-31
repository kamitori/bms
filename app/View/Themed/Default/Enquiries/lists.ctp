<?php echo $this->element('entry_tab_option', array('no_show_delete' => true)); ?>
<?php echo $this->element('js/lists_view') ?>
<div id="content" class="fix_magr">
    <form method="POST" id="sort_form">


    <div class="w_ul2 ul_res2">
        <ul class="ul_mag clear bg top_header_inner2 ul_res2" id="sort">
            <li class="hg_padd" style="width:1%"></li>
            <li class="hg_padd" style="width:12%">
                <label>Company</label>
                <span id="company" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:10%">
                <label>Contact</label>
                <span id="contact_name" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:5%">
                <label>Phone</label>
                <span id="direct_phone" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:6%">
                <label>Date</label>
                <span id="date" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:10%">
                <label>Our rep</label>
                <span id="our_rep" class="desc"></span>
            </li>
            <li class="hg_padd" style="width:10%">
                <label>Enquiry value</label>
                <span id="enquiry_value" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:7%">
                <label>Status</label>
                <span id="status" class="desc"></span>
            </li>
            <li class="hg_padd center_text" style="width:3%">
                <label>Rating</label>
                <span id="rating" class="desc"></span>
            </li>
            <li class="hg_padd " style="width:17%">
                <label>Requirements</label>
                <span id="requirements" class="desc"></span>
            </li>
            <li class="hg_padd bor_mt" style="width:2%"></li>
        </ul>

        <div id="lists_view_content">
            <!-- goi lists ajax -->
            <?php echo $this->element('../Enquiries/lists_ajax') ?>
        </div>

    </div>
    </form>
</div>
<?php echo $this->element('js/permission_lists') ?>