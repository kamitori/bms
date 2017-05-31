<?php
	$stock_summary = array();
	if(isset($subdatas['stock_summary']))
		$stock_summary = $subdatas['stock_summary'];
?>
<div class="clear_percent_10 float_left" style="width:25%;">
        <div class="tab_1 full_width">
            <div class="title_block bo_ra1">
                <span class="title_block_inner"><h4>Summary</h4></span>
                <span class="title_block_inner center_txt">Total</span>
                <span class="title_block_inner center_txt">Available</span>
            </div>
            <div class="tab_2_inner">
                <p class="clear">
                    <span class="label_1 float_left title_block_inner bold">In stock (sell)</span>
                    <div class="title_block_inner indent_input_tp bor_r">
                        <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['in_stock_total'])) echo $stock_summary['in_stock_total'];?>" />
                    </div>
                    <div class="title_block_inner indent_input_tp">
                        <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['in_stock_av'])) echo $stock_summary['in_stock_av'];?>" />
                        <span class="icon_search_ip float_right"></span>
                    </div>
                </p>
                <p class="clear">
                    <span class="label_1 float_left title_block_inner">Rent / loan assets</span>
                    <div class="title_block_inner indent_input_tp bor_r">
                        <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['loan_total'])) echo $stock_summary['loan_total'];?>" />
                    </div>
                    <div class="title_block_inner indent_input_tp">
                        <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['loan_av'])) echo $stock_summary['loan_av'];?>" />
                    </div>
                </p>
                <p class="clear">
                    <span class="label_1 float_left title_block_inner">Internal assets</span>
                    <div class="title_block_inner indent_input_tp bor_r">
                        <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['internal_assets'])) echo $stock_summary['internal_assets'];?>" />
                    </div>
                </p>
                <p class="clear">
                    <span class="label_1 float_left title_block_inner">Total</span>
                    <div class="title_block_inner indent_input_tp bor_r">
                        <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['total'])) echo $stock_summary['total'];?>" />
                    </div>
                </p>
                <p class="clear"></p>
                <div>
                    <div class="title_block">
                        <span class="title_block_inner"><h4>Breakdown</h4></span>
                        <span class="title_block_inner center_txt">Total</span>
                        <span class="title_block_inner center_txt">Current</span>
                    </div>
                    <div class="tab_2_inner">
                        <p class="clear">
                            <span class="label_1 float_left title_block_inner">Purchases</span>
                            <div class="title_block_inner indent_input_tp bor_r">
                                <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['purchases_total'])) echo $stock_summary['purchases_total'];?>" />
                            </div>
                            <div class="title_block_inner indent_input_tp">
                                <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['purchases_current'])) echo $stock_summary['purchases_current'];?>" />
                            </div>
                        </p>
                        <p class="clear">
                            <div class="title_block_inner2 float_left">
                                <span class="label_1 full_width">Used on jobs</span>
                                <span class="label_1 full_width">Used on stages</span>
                                <span class="label_1 full_width">Used on tasks</span>
                                <span class="label_1 full_width">Used on timelogs</span>
                                <span class="label_1 full_width">Staff expenses</span>
                            </div>
                            <div class="title_block_inner float_left mrg_left">
                                <div class="indent_input_tp bor_r heigt_ib">
                                    <input class="input_2 center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['used_on_jobs'])) echo $stock_summary['used_on_jobs'];?>" />
                                </div>
                                <div class="indent_input_tp bor_r heigt_ib">
                                    <input class="input_2 center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['used_on_stages'])) echo $stock_summary['used_on_stages'];?>" />
                                </div>
                                <div class="indent_input_tp bor_r heigt_ib">
                                    <input class="input_2 center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['used_on_tasks'])) echo $stock_summary['used_on_tasks'];?>" />
                                </div>
                                <div class="indent_input_tp bor_r heigt_ib">
                                    <input class="input_2 center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['used_on_timelogs'])) echo $stock_summary['used_on_timelogs'];?>" />
                                </div>
                                <div class="indent_input_tp bor_r heigt_ib">
                                    <input class="input_2 center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['staff_expenses'])) echo $stock_summary['staff_expenses'];?>" />
                                </div>
                            </div>
                            <div class="title_block_inner indent_input_tp">
                                <div class="block_current"></div>
                                <span class="text_ex">Express</span>
                            </div>
                        </p>
                        <p class="clear">
                            <span class="label_1 float_left title_block_inner">Assembly (add)</span>
                            <div class="title_block_inner indent_input_tp bor_r">
                                <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['assembly_add_total'])) echo $stock_summary['assembly_add_total'];?>" />
                            </div>
                            <div class="title_block_inner indent_input_tp">
                                <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['assembly_add_current'])) echo $stock_summary['assembly_add_current'];?>" />
                            </div>
                        </p>
                        <p class="clear">
                            <span class="label_1 float_left title_block_inner">Assembly (use)</span>
                            <div class="title_block_inner indent_input_tp bor_r">
                                <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['assembly_use_total'])) echo $stock_summary['assembly_use_total'];?>" />
                            </div>
                            <div class="title_block_inner indent_input_tp">
                                <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['assembly_use_current'])) echo $stock_summary['assembly_use_current'];?>" />
                            </div>
                        </p>
                        <p class="clear">
                            <span class="label_1 float_left title_block_inner">Sales</span>
                            <div class="title_block_inner indent_input_tp bor_r">
                                <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['sales_total'])) echo $stock_summary['sales_total'];?>" />
                            </div>
                            <div class="title_block_inner indent_input_tp">
                                <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['sales_current'])) echo $stock_summary['sales_current'];?>" />

                            </div>
                        </p>
                        <p class="clear">
                            <span class="label_1 float_left title_block_inner">Resource use</span>
                            <div class="title_block_inner indent_input_tp bor_r">
                                <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['resource_total'])) echo $stock_summary['resource_total'];?>" />
                            </div>
                            <div class="title_block_inner indent_input_tp">
                                <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['resource_current'])) echo $stock_summary['resource_current'];?>" />
                            </div>
                        </p>
                        <p class="clear"></p>
                    </div>
                </div>
                <div>
                    <div class="title_block">
                        <span class="title_block_inner"><h4>Purchasing</h4></span>
                        <span class="title_block_inner3 center_txt">
                            <input type="button" class="btn_pur" value="Create purchase order" onclick="location.href='<?php echo URL.'/purchaseorders/create_pur_from_product/'.$iditem;?>';" />
                        </span>
                    </div>
                    <div class="tab_2_inner">
                        <p class="clear">
                            <span class="label_1 float_left title_block_inner fixbor3">Purchases</span>
                            <div class="title_block_inner indent_input_tp bor_r">
                                <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['min_stock'])) echo $stock_summary['min_stock'];?>" />
                            </div>
                            <div class="title_block_inner indent_input_tp">
                                <input class="input_2 float_left center_txt" type="text" readonly="readonly" value="<?php if(isset($stock_summary['low'])) echo $stock_summary['low'];?>" />
                                <div class="block_wicon">
                                    <div class="middle_check box_ck">
                                        <label class="m_check2">
                                            <input type="checkbox">
                                            <span class="bx_check"></span>
                                        </label>
                                    </div>
                                </div>
                                <span class="icon_search_ip float_right"></span>
                            </div>
                        </p>
                    </div>
                </div>
                <p class="clear"></p>
            </div>
            <span class="title_block bo_ra2"></span>
        </div><!--END Tab1 -->
    </div>