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
                    <span class="fl_dent"><h4>Jobs / Projects</h4></span>
                    <span class="float_right dent_bl_txt">
                        <span class="title_small2 float_left">Our rep</span>
                        <div class="float_right">
                            <span class="block_sear bl_t">
                                <span class="bg_search_1"></span>
                                <span class="bg_search_2"></span>
                                <div class="box_inner_search float_left" style="width: 7%;">
                                    <span class="icon_search"></span>
                                    <input type="text" id="our_rep" name="our_rep" class="input_select input-search-listbox">
                                    <script type="text/javascript">
                                        $(function() {
                                            $('#our_rep').combobox(<?php echo json_encode($arr_contact_list) ?>);
                                        });
                                    </script>
                                    <span class="icon_closef" id="our_rep" style="margin: -15px 0 0 90px"></span>
                                </div>
                            </span>
                        </div>
                    </span>

                    <span class="float_right dent_bl_txt">
                        <span class="title_small2 float_left">Filter list below</span>
                        <div class="float_right">
                            <span class="block_sear bl_t">
                                <span class="bg_search_1"></span>
                                <span class="bg_search_2"></span>
                                <div class="box_inner_search float_left">
                                    <span class="icon_search"></span>
                                    <div class="styled_select2 float_left">
                                        <input type="text" id="filter_list_below" name="filter_list_below" class="input_select input-search-listbox">
                                    </div>
                                    <span class="icon_closef" id="filter_list_below"></span>
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
                                    <input type="text" id="identity" name="identity" class="input_select input-search-listbox">
                                    <script type="text/javascript">
                                        $(function() {
                                            $('#identity').combobox(<?php echo json_encode($arr_jobs_type) ?>);
                                        });
                                    </script>
                                    <span class="icon_closef" id="identity" style="margin: -15px 0 0 90px"></span>
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
                <li class="hg_padd center_txt" style="width:4%">
                    <label>Date</label>
                    <span id="work_start" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:10%">
                    <label>Customer</label>
                    <span id="contact_name" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:6%">
                    <label>Type</label>
                    <span id="type" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:6%">
                    <label>Status</label>
                    <span id="status" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:10%">
                    <label>Job manager</label>
                    <span id="contact_name" class="desc"></span>
                </li>
                <li class="hg_padd center_txt" style="width:4%">
                    <label>Job no</label>
                    <span id="no" class="desc"></span>
                </li>
                <li class="hg_padd center_txt no_border" style="width:20%">
                    <label>Job name</label>
                    <span id="name" class="desc"></span>
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
