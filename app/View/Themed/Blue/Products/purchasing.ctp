<?php
foreach ($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val) {

    echo $this->element('box', array('key' => $key, 'arr_val' => $arr_val));
}
?>
<input id="change_company_name" type="hidden" />
<input id="choicing_key" type="hidden" value="" />
<p class="clear"></p>
<script type="text/javascript">
    $(function(){
<?php
if(isset($subdatas['po_supplier'])){
	foreach($subdatas['po_supplier'] as $kk=>$vv){?>
		var company_id_<?php echo $vv['_id'];?> = $("#company_id_<?php echo $vv['_id'];?>").val();
		var company_name_<?php echo $vv['_id'];?> = $("#valuereturn_company_name_<?php echo $vv['_id'];?>").html();
		window_popup("products", "Specify vendor stock by seleted supplier", "changesupplier_<?php echo $vv['_id'];?>", "click_open_window_change_sku_<?php echo $vv['_id'];?>", "?lockproduct=1&products_product_type=Vendor Stock&products_company_id=" + company_id_<?php echo $vv['_id'];?> + "&products_company_name=" + company_name_<?php echo $vv['_id'];?>);
 <?php } }?>
		
        $('#bt_add_po_of_item').click(function(event) {
          window.location.href='<?php echo URL;?>/purchaseorders/create_pur_from_product/'+'<?php echo $product_id;?>';
        });
        // kiểm tra xem đã chọn company chưa
        company_init_popup_companies_subtab_supplier();
		company_init_popup_companies_subtab_change_company_name();
		
		$(".change_company_name").click(function(){
			var ids = $(this).attr("id");
			ids = ids.split("_");
			var ix = ids.length-1;
			ids = ids[ix];
			$("#choicing_key").val(ids);
			$("#change_company_name").click();
		});

        $(".rowedit input,.viewcheck_current").change(function(){
            //nhan id
            var isreload=0;
            var names = $(this).attr("name");
            var intext = 'box_test_'+names;
            var inval = $(this).val();
            var ids  = names.split("_");
            var index = ids.length;
            var ids = ids[index-1];
                names = names.replace("_"+ids,"");
                names = names.replace("cb_","");
            var data = new Object();
                data[names]=inval;
							
			if(names=='sizew' || names=='sizeh' || names=='sell_price'){
				var valf = FortmatPrice(inval);
				$('#'+names+'_'+ids).val(valf);
			}else
				$('#'+names+'_'+ids).val(inval);		
			
			//nếu là select box
            if($('#'+names+'_'+ids).parent().attr('class')=='combobox'){

               data[names]=$('#'+names+'_'+ids+'Id').val();

            }


            if(names == 'radio'){
                names = $(this).attr('rel');
                ids  = $(this).attr("id"); console.log(ids);
                ids  = ids.split("_");
                index = ids.length;
                ids  =  ids[index-1];
				
				//change current supplier
				var maincom = $("#valuereturn_company_name_"+ids).text();
				var maincomid = $("#company_id_"+ids).val();
				$("#company_name").val($.trim(maincom));
				$("#company_id").val($.trim(maincomid));
					   
                var arrvalue ={ 'current':''};
                update_all_option('supplier',arrvalue,function(){
                    var datac = new Object();
                    datac[names]=inval;
                    save_option('supplier', datac, ids, 0, 'purchasing', 'update',function(){
                        reload_subtab('purchasing');
						
                    });
                });

            }else{
                save_option('supplier', data, ids, 0, 'purchasing', 'update',function(){
                    if(names == 'sell_by'
                    ||names == 'sizew'
                    ||names == 'sizew_unit'
                    ||names == 'sizeh'
                    ||names == 'sizeh_unit'
                    ||names == 'sell_price')
                        reload_subtab('purchasing');
                });
            }
        });
    });

    function company_init_popup_companies_subtab_supplier(keys){
        var parameter_get = "?is_supplier=1";
        window_popup("companies", "Specify supplier", "supplier", "bt_add_po_supplier", parameter_get);
    }
	
	function company_init_popup_companies_subtab_change_company_name(keys){
        var parameter_get = "?is_supplier=1";
        window_popup("companies", "Specify supplier", "change_company_name", "change_company_name", parameter_get);
    }
  
</script>

