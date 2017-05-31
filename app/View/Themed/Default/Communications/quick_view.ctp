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
                <input type="hidden" name="sort_type" id="sort_type" value="" />
                <input type="hidden" name="sort_key" id="sort_key" value="" />
                <span class="title_block bo_ra1 block_expand">
                    <span class="fl_dent"><h4>Jobs / Projects</h4></span>
                    <span class="float_right dent_bl_txt">
                        <span class="title_small2 float_left">Our rep</span>
                        <div class="float_right">
                            <span class="block_sear bl_t">
                                <span class="bg_search_1"></span>
                                <span class="bg_search_2"></span>
                                <div class="box_inner_search float_left" style="width: 7%;">
                                    <span class="icon_search"></span>
                                    <input type="text" id="seach_company_name" name="seach_our_rep" class="input_select input-search-listbox">
                                    <script type="text/javascript">
                                        $(function() {
                                            $('#seach_our_rep').combobox(<?php echo json_encode($arr_contact_list) ?>);
                                        });
                                    </script>
                                    <span class="icon_closef" id="seach_our_rep" style="margin: -15px 0 0 90px"></span>
                                </div>
                            </span>
                        </div>
                    </span>

                    <span class="float_right dent_bl_txt">
                        <span class="title_small2 float_left">Type</span>
                        <div class="float_right">
                            <span class="block_sear bl_t">
                                <span class="bg_search_1"></span>
                                <span class="bg_search_2"></span>
                                <div class="box_inner_search float_left">
                                    <span class="icon_search"></span>
                                    <div class="styled_select2 float_left">
                                        <input type="text" id="seach_filter_list_below" name="" class="input_select input-search-listbox">
                                    </div>
                                    <span class="icon_closef" id="seach_filter_list_below"></span>
                                </div>
                            </span>
                        </div>
                    </span>

                    <span class="float_right dent_bl_txt">
                        <span class="title_small2 float_left">Date</span>
                        <div class="float_right">
                            <span class="block_sear bl_t">
                                <span class="bg_search_1"></span>
                                <span class="bg_search_2"></span>
                                <div class="box_inner_search float_left">
                                    <span class="icon_search"></span>
                                    <div class="styled_select2 float_left">
                                        <input type="text" id="date" name="seach_date" class="input_select input-search-listbox JtSelectDate">
                                    </div>
                                    <span id="date" class="icon_closef"></span>
                                </div>
                            </span>
                        </div>
                    </span>

                    <span class="float_right">
                        <span class="title_small2 float_left">Identity</span>
                        <div class="float_right">
                            <span class="block_sear bl_t">
                                <span class="bg_search_1"></span>
                                <span class="bg_search_2"></span>
                                <div class="box_inner_search float_left" style="width: 7%;">
                                    <span class="icon_search"></span>
                                    <input type="text" id="seach_identity" name="seach_identity" class="input_select input-search-listbox">
                                    <script type="text/javascript">
                                        $(function() {
                                            $('#seach_identity').combobox(<?php echo json_encode($arr_jobs_type) ?>);
                                        });
                                    </script>
                                    <span class="icon_closef" id="seach_identity" style="margin: -15px 0 0 90px"></span>
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
                <li class="hg_padd center_txt" style="width:6%">
                    <label>Type</label>
                    <span id="type" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:4%">
                    <label>Date</label>
                    <span id="date" class="desc"></span>
                </li>
                <li class="hg_padd" style="width:16%">
                    <label>Form</label>
                    <span id="form" class="desc"></span>
                </li>
                <li class="hg_padd" style="width:16%">
                    <label>To</label>
                    <span id="to" class="desc"></span>
                </li>
                <li class="hg_padd no_border" style="width: 30%">
                    <label>Detail</label>
                </li>
            </ul>
            <div class="links_decoration" id="quick_view_content" >
                <?php echo $this->element('../jobs/quick_view_ajax') ?>
            </div>
            <span class="title_block bo_ra2">
                <span class="float_left bt_block">Click on a line to view full details</span>
            </span>
        </div><!--END Tab1 -->
    </div>
    <p class="clear"></p>
</div>
