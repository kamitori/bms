<?php
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		if($key=='stock_summary')
			echo $this->element('../Products/stock_summary');
		else
			echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<input type="hidden" value="<?php if(isset($first_amended)) echo $first_amended;?>" id="first_amended" />
<p class="clear"></p>
<script type="text/javascript">
    $(function(){
		//build popup
		build_popup_location('add_locations');
		build_popup_location('add_stocktakes');
		
		$("#block_full_locations input").change(function(){
			var ids = $(this).attr("id");
			var irr = ids.split("_");
			var ix = irr.length;
			var opid = irr[ix-1];
			var field = ids.replace("_"+opid,"");
			var datas = new Object();
			datas[field] = $(this).val();
			save_option('locations',datas,opid,1,'stock','update');
		});
		
		$("#block_full_stocktakes input").change(function(){
			var ids = $(this).attr("id");
			var irr = ids.split("_");
			var ix = irr.length;
			var opid = irr[ix-1];
			var field = ids.replace("_"+opid,"");
			var value = $(this).val();
			var datas = new Object();
			datas[field] = value;
			var amended = $("#first_amended").val();
			if(amended==opid){
				save_option('stocktakes',datas,opid,1,'stock','update');
			}else{
				alerts('Message','<?php msg('PRODUCT_NOT_CHANGE');?>',function(){
					reload_subtab('stock');
				});
			}
		});
		
	});
	
	function build_popup_location(keys){
		var parameter_get = "?is_supplier=1";
		window_popup("locations", "Specify location", keys, "bt_"+keys, parameter_get);
	}
	
	function after_choose_locations(ids,names,keys){
		if(keys=='add_locations'){
			var data_return = JSON.parse($("#after_choose_locations"+ keys + ids).val());
			var datas = new Object();
				datas['location_name'] 	= data_return.name;
				datas['location_id'] 	= ids;
				datas['min_stock'] 		= '';
				datas['location_type'] 	= data_return.location_type;
				datas['stock_usage'] 	= data_return.stock_usage;
			save_option('locations',datas,'',1,'stock','add');
		
		}else if(keys=='add_stocktakes'){
			var data_return = JSON.parse($("#after_choose_locations"+ keys + ids).val());
			var datas = new Object();
				datas['location_name'] 	= data_return.name;
				datas['location_id'] 	= ids;
				datas['stocktakes_date'] = '<?php echo time();?>';
				datas['location_type'] 	= data_return.location_type;
				datas['stock_usage'] 	= data_return.stock_usage;
				datas['qty_counted'] 	= '';
				datas['qty_amended'] 	= '';
			save_option('stocktakes',datas,'',1,'stock','add');
		}
	}
	
	
	
</script>



<!--<!--<div class="clear_percent" style="width:100%;margin:0;">
    
    <div class="clear_percent_11 float_left" style="margin-left:1%; width: 68%;">
        <div class="tab_1 full_width">
            <span class="title_block bo_ra1">
                <span class="fl_dent"><h4>Location for this item</h4></span>
                <a href="" title="Link a contact"><span class="icon_down_tl top_f"></span></a>
                <form>
                    <div class="float_left hbox_form dent_left_form">
                        <input class="btn_pur size_width" type="button" value="Transfer stock">
                    </div>
                </form>
            </span>
            <p class="clear"></p>
            <ul class="ul_mag clear bg3">
                <li class="hg_padd" style="width:1.5%"></li>
                <li class="hg_padd" style="width:20%">Location</li>
                <li class="hg_padd" style="width:8%">Type</li>
                
                <div class="float_left" style="width:26%;">
                    <div class="tab_title_purchasing2">
                        <span class="block_purcharsing">In use as resources (job /tasks)</span>
                    </div>
                    <div>
                        <li class="hg_padd line_mg" style="width:24%">Usage</li>
                        <li class="hg_padd center_txt line_mg" style="width:24%">To. stock</li>
                        <li class="hg_padd right_txt line_mg" style="width:24%">On SO's</li>
                        <li class="hg_padd right_txt line_mg" style="width:22%">In use</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">In Assembly</li>
                <li class="hg_padd right_txt">Avaliable</li>
                <div class="float_left" style="width:21%;">
                    <div class="tab_title_purchasing">
                        <span class="block_purcharsing">Purcharsing related</span>
                    </div>
                    <div>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Min stock</li>
                        <li class="hg_padd center_txt line_mg" style="width:31.7%">Low</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">On PO's</li>
                    </div>
                </div>
                <li class="hg_padd bor_mt" style="width:1.5%"></li>
            </ul>
            <ul class="ul_mag clear bg1">
                <li class="hg_padd" style="width:1.5%"><span class="icon_emp"></span></li>
                <li class="hg_padd" style="width:20%">Location</li>
                <li class="hg_padd" style="width:8%">Type</li>
                
                <div class="float_left" style="width:26%;">
                    <div>
                        <li class="hg_padd line_mg" style="width:24%">Usage</li>
                        <li class="hg_padd center_txt line_mg" style="width:24%">To. stock</li>
                        <li class="hg_padd right_txt line_mg" style="width:24%">On SO's</li>
                        <li class="hg_padd right_txt line_mg" style="width:22%">In use</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">In Assembly</li>
                <li class="hg_padd right_txt">Avaliable</li>
                <div class="float_left" style="width:21%;">
                    <div>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Min stock</li>
                        <li class="hg_padd center_txt line_mg" style="width:31.7%">Low</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">On PO's</li>
                    </div>
                </div>
                <li class="hg_padd bor_mt" style="width:1.5%">
                    <div class="middle_check">
                        <a title="Delete link" href="">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="ul_mag clear bg2">
                <li class="hg_padd" style="width:1.5%"><span class="icon_emp"></span></li>
                <li class="hg_padd" style="width:20%">Location</li>
                <li class="hg_padd" style="width:8%">Type</li>
                
                <div class="float_left" style="width:26%;">
                    <div>
                        <li class="hg_padd line_mg" style="width:24%">Usage</li>
                        <li class="hg_padd center_txt line_mg" style="width:24%">To. stock</li>
                        <li class="hg_padd right_txt line_mg" style="width:24%">On SO's</li>
                        <li class="hg_padd right_txt line_mg" style="width:22%">In use</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">In Assembly</li>
                <li class="hg_padd right_txt">Avaliable</li>
                <div class="float_left" style="width:21%;">
                    <div>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Min stock</li>
                        <li class="hg_padd center_txt line_mg" style="width:31.7%">Low</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">On PO's</li>
                    </div>
                </div>
                <li class="hg_padd bor_mt" style="width:1.5%">
                    <div class="middle_check">
                        <a title="Delete link" href="">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="ul_mag clear bg1">
                <li class="hg_padd" style="width:1.5%"><span class="icon_emp"></span></li>
                <li class="hg_padd" style="width:20%">Location</li>
                <li class="hg_padd" style="width:8%">Type</li>
                
                <div class="float_left" style="width:26%;">
                    <div>
                        <li class="hg_padd line_mg" style="width:24%">Usage</li>
                        <li class="hg_padd center_txt line_mg" style="width:24%">To. stock</li>
                        <li class="hg_padd right_txt line_mg" style="width:24%">On SO's</li>
                        <li class="hg_padd right_txt line_mg" style="width:22%">In use</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">In Assembly</li>
                <li class="hg_padd right_txt">Avaliable</li>
                <div class="float_left" style="width:21%;">
                    <div>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Min stock</li>
                        <li class="hg_padd center_txt line_mg" style="width:31.7%">Low</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">On PO's</li>
                    </div>
                </div>
                <li class="hg_padd bor_mt" style="width:1.5%">
                    <div class="middle_check">
                        <a title="Delete link" href="">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="ul_mag clear bg2">
                <li class="hg_padd" style="width:1.5%"><span class="icon_emp"></span></li>
                <li class="hg_padd" style="width:20%">Location</li>
                <li class="hg_padd" style="width:8%">Type</li>
                
                <div class="float_left" style="width:26%;">
                    <div>
                        <li class="hg_padd line_mg" style="width:24%">Usage</li>
                        <li class="hg_padd center_txt line_mg" style="width:24%">To. stock</li>
                        <li class="hg_padd right_txt line_mg" style="width:24%">On SO's</li>
                        <li class="hg_padd right_txt line_mg" style="width:22%">In use</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">In Assembly</li>
                <li class="hg_padd right_txt">Avaliable</li>
                <div class="float_left" style="width:21%;">
                    <div>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Min stock</li>
                        <li class="hg_padd center_txt line_mg" style="width:31.7%">Low</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">On PO's</li>
                    </div>
                </div>
                <li class="hg_padd bor_mt" style="width:1.5%">
                    <div class="middle_check">
                        <a title="Delete link" href="">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="ul_mag clear bg1">
                <li class="hg_padd" style="width:1.5%"><span class="icon_emp"></span></li>
                <li class="hg_padd" style="width:20%">Location</li>
                <li class="hg_padd" style="width:8%">Type</li>
                
                <div class="float_left" style="width:26%;">
                    <div>
                        <li class="hg_padd line_mg" style="width:24%">Usage</li>
                        <li class="hg_padd center_txt line_mg" style="width:24%">To. stock</li>
                        <li class="hg_padd right_txt line_mg" style="width:24%">On SO's</li>
                        <li class="hg_padd right_txt line_mg" style="width:22%">In use</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">In Assembly</li>
                <li class="hg_padd right_txt">Avaliable</li>
                <div class="float_left" style="width:21%;">
                    <div>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Min stock</li>
                        <li class="hg_padd center_txt line_mg" style="width:31.7%">Low</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">On PO's</li>
                    </div>
                </div>
                <li class="hg_padd bor_mt" style="width:1.5%">
                    <div class="middle_check">
                        <a title="Delete link" href="">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="ul_mag clear bg2">
                <li class="hg_padd" style="width:1.5%"><span class="icon_emp"></span></li>
                <li class="hg_padd" style="width:20%">Location</li>
                <li class="hg_padd" style="width:8%">Type</li>
                
                <div class="float_left" style="width:26%;">
                    <div>
                        <li class="hg_padd line_mg" style="width:24%">Usage</li>
                        <li class="hg_padd center_txt line_mg" style="width:24%">To. stock</li>
                        <li class="hg_padd right_txt line_mg" style="width:24%">On SO's</li>
                        <li class="hg_padd right_txt line_mg" style="width:22%">In use</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">In Assembly</li>
                <li class="hg_padd right_txt">Avaliable</li>
                <div class="float_left" style="width:21%;">
                    <div>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Min stock</li>
                        <li class="hg_padd center_txt line_mg" style="width:31.7%">Low</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">On PO's</li>
                    </div>
                </div>
                <li class="hg_padd bor_mt" style="width:1.5%">
                    <div class="middle_check">
                        <a title="Delete link" href="">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
            <span class="hit"></span>
            <span class="title_block bo_ra2">
                <span class="bt_block float_right no_bg">
                    <input class="input_w2 float_right" type="text">
                    <div class="float_left">
                        <div class="dent_input float_right">
                            <input class="input_w2 resize_input" type="text">
                            <input class="input_w2" type="text">
                            <input class="input_w2" type="text">
                            <input class="input_w2" type="text">
                            <input class="input_w2" type="text">
                            <input class="input_w2" type="text">
                        </div>
                        <span class="float_left">Totals</span>
                    </div>
                </span>
                <span class="float_left bt_block">View</span>
                <span class="float_left left_text">Note: Locations are created by movements.</span>
            </span>
        </div>
        END Tab1 
        
        
        <div class="tab_1 full_width block_dent9">
            <span class="title_block bo_ra1">
                <span class="fl_dent"><h4>Amendments / Stocktakes for this item</h4></span>
                <a href="" title="Link a contact"><span class="icon_down_tl top_f"></span></a>
            </span>
            <p class="clear"></p>
            <ul class="ul_mag clear bg3">
                <li class="hg_padd" style="width:1.5%"></li>
                <li class="hg_padd" style="width:5%">Date</li>
                <li class="hg_padd" style="width:30%">Details</li>
                
                <div class="float_left" style="width:26%;">
                    <div class="tab_title_purchasing2">
                        <span class="block_purcharsing">At time of creating amendment</span>
                    </div>
                    <div>
                        <li class="hg_padd line_mg" style="width:31.7%">Location</li>
                        <li class="hg_padd left_txt line_mg" style="width:31.7%">Usage</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Qty in stock</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">Qty counted</li>
                <li class="hg_padd right_txt" style="width:11%">Qty amended</li>
                <li class="hg_padd bor_mt" style="width:1.5%"></li>
            </ul>
            <ul class="ul_mag clear bg1">
                <li class="hg_padd" style="width:1.5%"><span class="icon_emp"></span></li>
                <li class="hg_padd" style="width:5%">dsfdsfds</li>
                <li class="hg_padd" style="width:30%">Details</li>
                <div class="float_left" style="width:26%;">
                    <div>
                        <li class="hg_padd line_mg" style="width:31.7%">Location</li>
                        <li class="hg_padd left_txt line_mg" style="width:31.7%">Usage</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Qty in stock</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">Qty counted</li>
                <li class="hg_padd right_txt" style="width:11%">Qty amended</li>
                <li class="hg_padd bor_mt" style="width:1.5%">
                    <div class="middle_check">
                        <a title="Delete link" href="">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="ul_mag clear bg2">
                <li class="hg_padd" style="width:1.5%"><span class="icon_emp"></span></li>
                <li class="hg_padd" style="width:5%">dsfdsfds</li>
                <li class="hg_padd" style="width:30%">Details</li>
                <div class="float_left" style="width:26%;">
                    <div>
                        <li class="hg_padd line_mg" style="width:31.7%">Location</li>
                        <li class="hg_padd left_txt line_mg" style="width:31.7%">Usage</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Qty in stock</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">Qty counted</li>
                <li class="hg_padd right_txt" style="width:11%">Qty amended</li>
                <li class="hg_padd bor_mt" style="width:1.5%">
                    <div class="middle_check">
                        <a title="Delete link" href="">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="ul_mag clear bg1">
                <li class="hg_padd" style="width:1.5%"><span class="icon_emp"></span></li>
                <li class="hg_padd" style="width:5%">dsfdsfds</li>
                <li class="hg_padd" style="width:30%">Details</li>
                <div class="float_left" style="width:26%;">
                    <div>
                        <li class="hg_padd line_mg" style="width:31.7%">Location</li>
                        <li class="hg_padd left_txt line_mg" style="width:31.7%">Usage</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Qty in stock</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">Qty counted</li>
                <li class="hg_padd right_txt" style="width:11%">Qty amended</li>
                <li class="hg_padd bor_mt" style="width:1.5%">
                    <div class="middle_check">
                        <a title="Delete link" href="">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="ul_mag clear bg2">
                <li class="hg_padd" style="width:1.5%"><span class="icon_emp"></span></li>
                <li class="hg_padd" style="width:5%">dsfdsfds</li>
                <li class="hg_padd" style="width:30%">Details</li>
                <div class="float_left" style="width:26%;">
                    <div>
                        <li class="hg_padd line_mg" style="width:31.7%">Location</li>
                        <li class="hg_padd left_txt line_mg" style="width:31.7%">Usage</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Qty in stock</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">Qty counted</li>
                <li class="hg_padd right_txt" style="width:11%">Qty amended</li>
                <li class="hg_padd bor_mt" style="width:1.5%">
                    <div class="middle_check">
                        <a title="Delete link" href="">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="ul_mag clear bg1">
                <li class="hg_padd" style="width:1.5%"><span class="icon_emp"></span></li>
                <li class="hg_padd" style="width:5%">dsfdsfds</li>
                <li class="hg_padd" style="width:30%">Details</li>
                <div class="float_left" style="width:26%;">
                    <div>
                        <li class="hg_padd line_mg" style="width:31.7%">Location</li>
                        <li class="hg_padd left_txt line_mg" style="width:31.7%">Usage</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Qty in stock</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">Qty counted</li>
                <li class="hg_padd right_txt" style="width:11%">Qty amended</li>
                <li class="hg_padd bor_mt" style="width:1.5%">
                    <div class="middle_check">
                        <a title="Delete link" href="">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="ul_mag clear bg2">
                <li class="hg_padd" style="width:1.5%"><span class="icon_emp"></span></li>
                <li class="hg_padd" style="width:5%">dsfdsfds</li>
                <li class="hg_padd" style="width:30%">Details</li>
                <div class="float_left" style="width:26%;">
                    <div>
                        <li class="hg_padd line_mg" style="width:31.7%">Location</li>
                        <li class="hg_padd left_txt line_mg" style="width:31.7%">Usage</li>
                        <li class="hg_padd right_txt line_mg" style="width:31.7%">Qty in stock</li>
                    </div>
                </div>
                <li class="hg_padd right_txt" style="width:9%;">Qty counted</li>
                <li class="hg_padd right_txt" style="width:11%">Qty amended</li>
                <li class="hg_padd bor_mt" style="width:1.5%">
                    <div class="middle_check">
                        <a title="Delete link" href="">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>
            <span class="hit"></span>
            <span class="title_block bo_ra2">
                <span class="bt_block float_right no_bg">
                    Total amended
                    <input class="input_w2" type="text">
                </span>
                <span class="float_left bt_block">Click to view</span>
            </span>
            </span>
        </div>END Tab2
    </div>
</div>-->