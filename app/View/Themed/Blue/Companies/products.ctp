<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>
<span id="click_open_window_companiessearch_prefer_customer" style=" display:none;"></span>
<script type="text/javascript">
	$(function(){
		$('#container_keyword').mCustomScrollbar({
				scrollButtons:{
					enable:false
				},
				advanced:{
			        updateOnContentResize: true,
			        autoScrollOnFocus: false,
			    }
			});
		window_popup('products', 'Specify Product', '', 'bt_add_4', "?no_supplier=1");
		window_popup('companies', 'Specify Prefer customer','search_prefer_customer','click_open_window_companiessearch_prefer_customer','','no_auto_close');
		$("input","#editview_box_pricing_category").change(function(){
			var names = $(this).attr("name");
			var inval = $(this).val();
			names = names.replace("_cb", "");
			var ids = $("#mongo_id").val();
			save_data(names,inval,'',function(){});
		});

		$("#bt_add_keyword").click(function(){
			var datas = new Object;
			var user_name = $("#your_user_name").val();
			var user_id = $("#your_user_id").val();
				datas['keyword'] = 'Please leave keyword:';

			save_option('keyword',datas,'',1,'products','add');

		});

		$("#block_full_keyword").delegate(".rowedit input,.rowedit select","change",function(){
			var names = $(this).attr("name");
			var intext = 'box_test_'+names;
			var inval = $(this).val();
			var ids  = names.split("_");
			var index = ids.length;
			var ids = ids[index-1];
			//khoi tao gia tri luu
			names = names.replace("_"+ids,"");
			var datas = new Object();
				datas[names]=inval;
			//luu lai
			save_option('keyword',datas,ids,0,'general','update',function(){
			});
		});

		$(".link_to_pricing").click(function(){
			var ids = $(this).attr("id");
			ids =  ids.replace('txt_sku_','');
			window.location = "<?php echo URL; ?>/companies/products_pricing/" + ids ;
		});


	})
	function after_choose_products(product_id){
		var company_id = $("#mongo_id").val();
		$.ajax({
			url : "<?php echo URL.'/companies/pricing_add' ?>",
			type: "POST",
			data: {product_id : product_id},
			success: function(result){
				window.location = result;
			}
		})
	}

</script>