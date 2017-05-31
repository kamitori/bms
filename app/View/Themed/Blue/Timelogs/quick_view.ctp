<?php echo $this->element('entry_tab_option'); ?>
<script>
    $(function() {
        $('form').change(function() {
            $.ajax({
                url: '<?php echo URL . '/jobs/quick_view_seach' ?>',
                timeout: 15000,
                type: "post",
                data: $(this).serialize(),
                success: function(html) {
                    console.log(html);
                }
            });
        });
    });
</script>

<div class="menu">
    <div class="bg_menu">
        <span class="title_quick float_left">Quickview</span>
    </div>
</div>

<div id="content">
    <div class="clear_percent">
        <div class="tab_1 full_width">
            <!-- seach -->
            <span class="title_block bo_ra1 block_expand">
                <span class="fl_dent"><h4>Jobs / Projects</h4></span>
                <span class="float_right dent_bl_txt">
                    <span class="title_small2 float_left">Our rep</span>
                    <div class="float_right">
                        <span class="block_sear bl_t">
                            <span class="bg_search_1"></span>
                            <span class="bg_search_2"></span>
                            <div class="box_inner_search float_left">
                                <a href=""><span class="icon_search"></span></a>
                                <div class="styled_select2 float_left">
                                    <select>
                                        <option select="selected"></option>
                                        <option>Developper</option>
                                        <option>Production</option>
                                        <option>Manuafacture</option>
                                    </select>
                                </div>
                                <a href=""><span class="icon_closef"></span></a>
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
                                <a href=""><span class="icon_search"></span></a>
                                <div class="styled_select2 float_left">
                                    <select>
                                        <option select="selected"></option>
                                        <option>Developper</option>
                                        <option>Production</option>
                                        <option>Manuafacture</option>
                                    </select>
                                </div>
                                <a href=""><span class="icon_closef"></span></a>
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
                            <div class="box_inner_search float_left">
                                <a href=""><span class="icon_search"></span></a>
                                <div class="styled_select2 float_left">
                                    <select>
                                        <option select="selected"></option>
                                        <option>Developper</option>
                                        <option>Production</option>
                                        <option>Manuafacture</option>
                                    </select>
                                </div>
                                <a href=""><span class="icon_closef"></span></a>
                            </div>
                        </span>
                    </div>
                </span>

            </span>
            <!-- end seach-->
            <p class="clear"></p>

            <ul class="ul_mag clear bg3">
                <li class="hg_padd center_txt" style="width:4%">Date</li>
                <li class="hg_padd center_txt" style="width:10%">Customer</li>
                <li class="hg_padd center_txt" style="width:6%">Type</li>
                <li class="hg_padd center_txt" style="width:6%">Status</li>
                <li class="hg_padd late center_txt" style="width:3%">Late</li>
                <li class="hg_padd center_txt" style="width:10%">Job manager</li>
                <li class="hg_padd center_txt" style="width:3%">Job no</li>
                <li class="hg_padd no_border" style="width:30%">Job name</li>
            </ul>

            <div class="links_decoration">
                <?php $i = 2; ?>
                <?php foreach ($arr_jobs as $value): ?>
                    <?php
                    if ($i == 2)
                        $i = $i - 1;
                    else
                        $i = $i + 1;
                    ?>
                    <ul class="ul_mag clear bg<?php echo $i ?>">
                        <li class="hg_padd center_txt" style="width:4%"><a href="">05/09/13</a></li>
                        <li class="hg_padd" style="width:10%"><a href="">Customer</a></li>
                        <li class="hg_padd center_txt" style="width:6%"><a href="">Type</a></li>
                        <li class="hg_padd center_txt" style="width:6%"><a href="">Status</a></li>
                        <li class="hg_padd center_txt" style="width:3%">
                            <div class="middle_check">
                                <label class="m_check2">
                                    <input type="checkbox">
                                    <span class="bx_check"></span>
                                </label>
                            </div>
                        </li>
                        <li class="hg_padd" style="width:10%"><a href="">Job manager</a></li>
                        <li class="hg_padd center_txt" style="width:3%"><a href="">1</a></li>
                        <li class="hg_padd no_border" style="width:30%"><a href="">Job name</a></li>
                    </ul>
                <?php endforeach ?>
            </div>
            <span class="title_block bo_ra2">
                <span class="float_left bt_block">Click on a line to view full details</span>
            </span>
        </div><!--END Tab1 -->
    </div>
    <p class="clear"></p>
</div>
<?php echo $this->element('../Jobs/js'); ?>