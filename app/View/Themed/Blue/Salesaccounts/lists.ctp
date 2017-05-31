<?php echo $this->element('entry_tab_option', array('no_show_delete' => true)); ?>
<?php echo $this->element('js/lists_view'); ?>
<div id="content" class="fix_magr">
    <form method="POST" id="sort_form">
        <div class="w_ul2 ul_res2">
            <ul class="ul_mag clear bg top_header_inner2 ul_res2" id="sort">
                <li class="hg_padd" style="width:1%"></li>
                <li class="hg_padd" style="width:6%">
                    <label>Type</label>
                    <span id="is_customer" class="desc"></span>
                </li>
                <li class="hg_padd" style="width:4%">
                    <label>Ref no</label>
                    <span id="no" class="desc"></span>
                </li>
                <li class="hg_padd" style="width:24%">
                    <label>Account name</label>
                    <span id="name" class="desc"></span>
                </li>
                <li class="hg_padd" style="width:6%">
                    <label>0 - 30 days</label>
                </li>
                <li class="hg_padd" style="width:6%">
                    <label>31 - 60 days</label>
                </li>
                <li class="hg_padd" style="width:6%">
                    <label>61 - 90 days</label>
                </li>
                <li class="hg_padd" style="width:6%">
                    <label>90+ days</label>
                </li>
                <li class="hg_padd" style="width:6%">
                    <label>Acc balance</label>
                </li>
                <li class="hg_padd" style="width:6%">
                    <label> Zip / Postcode</label>
                    <span id="our_rep"></span>
                </li>
                 <li class="hg_padd" style="width:8%">
                    <label>Our rep</label>
                    <span id="our_rep" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:6%">
                    <label>Status</label>
                    <span id="inactive" class="desc"></span>
                </li>
                <li class="hg_padd bor_mt" style="width:1%"></li>
            </ul>
            <div id="lists_view_content">
                <!-- goi lists ajax -->
                <?php echo $this->element('../Salesaccounts/lists_ajax') ?>
            </div>
        </form>

    </div>
</div>

<?php echo $this->element('js/permission_lists') ?>