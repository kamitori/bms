<?php echo $this->element('entry_tab_option'); ?>
<?php echo $this->element('js/quick_view'); ?>
<div class="menu">
    <div class="bg_menu">
        <span class="title_quick float_left">Quickview</span>
    </div>
</div>

<div id="content">
    <div class="clear_percent">
        <div class="tab_1 full_width">
            <!-- seach -->
            <form method="post" id="seach_form">
                <input type="hidden" name="offset" id="offset" value="<?php echo LIST_LIMIT ?>" />
                <input type="hidden" name="sort_type" id="sort_type" value="desc" />
                <input type="hidden" name="sort_key" id="sort_key" value="_id" />
                <span class="title_block bo_ra1 block_expand">
                    <span class="fl_dent"><h4>Tasks</h4></span>
                    <span class="float_right dent_bl_txt">
                        <span class="title_small2 float_left">Company name</span>
                        <div class="float_right">
                            <span class="block_sear bl_t">
                                <span class="bg_search_1"></span>
                                <span class="bg_search_2"></span>
                                <div class="box_inner_search float_left" style="width: 7%;">
                                    <span class="icon_search"></span>
                                    <input type="text" id="company_name" name="company_name" value="" class="input_select input-search-listbox">
                                    <span  id="company_name" class="icon_closef" style="margin-top: -15px"></span>
                                </div>
                            </span>
                        </div>
                    </span>

                    <span class="float_right">
                        <span class="title_small2 float_left">Filter by</span>
                        <div class="float_right">
                            <span class="block_sear bl_t no_size">
                                <span class="bg_search_1"></span>
                                <span class="bg_search_2"></span>
                                <div class="box_main_filter">
                                    <div class="menu_1">
                                        <span class="inactive">Customers</span>
                                        <label class="m_check2">
                                            <input type="checkbox" name="is_customer" value="1">
                                            <span></span>
                                            <input type="hidden" name="is_customer" value="0" >
                                        </label>
                                    </div>
                                    <div class="menu_1">
                                        <span class="inactive">Supplier</span>
                                        <label class="m_check2">
                                            <input type="checkbox" name="is_supplier" value="1">
                                            <span></span>
                                            <input type="hidden" name="is_supplier" value="0">
                                        </label>
                                    </div>
                                    <div class="menu_1 no_right">
                                        <span class="inactive">Inactive</span>
                                        <label class="m_check2">
                                            <input type="checkbox" name="is_inactive" value="1">
                                            <span></span>
                                            <input type="hidden" name="is_inactive" value="0">
                                        </label>
                                    </div>
                                    <p class="clear"></p>
                                </div>
                            </span>
                        </div>
                    </span>
                </span>
            </form>
            <!-- end seach-->
            <p class="clear"></p>

            <ul class="ul_mag clear bg3" id="sort">
                <li class="hg_padd center_txt" style="width: 1%"></li>
                <li class="hg_padd center_txt" style="width:12%">
                    <label>Company</label>
                    <span id="name" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:6%">
                    <label>Customer</label>
                    <span id="is_customer" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:6%">
                    <label>Supplier</label>
                    <span id="is_supplier" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:8%">
                    <label>Phone</label>
                    <span id="phone" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:14%">
                    <label>Company default address</label>
                    <span id="default_address" class="desc"></span>
                </li>
            </ul>

            <div class="links_decoration" id="quick_view_content" >
                <!-- goi form dung chung -->
                <?php echo $this->element('../Companies/quick_view_ajax') ?>

            </div>
            <span class="title_block bo_ra2">
                <span class="float_left bt_block">Click on a line to view full details</span>
            </span>
        </div><!--END Tab1 -->
    </div>
    <p class="clear"></p>
</div>
<?php echo $this->element('../Companies/js'); ?>