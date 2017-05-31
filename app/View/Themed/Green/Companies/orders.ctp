<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>
<script type="text/javascript">
	$(function(){
		$('#bt_add_orders_cus').click(function(){
			var ids = $("#mongo_id").val();
			$.ajax({
				url:"<?php echo URL;?>/companies/orders_add_salesorder/" + ids,
				timeout: 15000,
				success: function(html){
					location.replace(html);
					reload_subtab('orders');
				}
			});
		})

		$("#bt_add_orders_supplier").click(function(){
			var ids=$("#mongo_id").val();
			$.ajax({
				url:"<?php echo URL; ?>/companies/orders_add_purchasesorder/" + ids,
				timeout:15000,
				success: function(html){
					location.replace(html);
					reload_subtab('orders');
				}
			});
		})

		$(".del_orders").click(function(){
            var names = $(this).attr("id");
            var ids = names.split("_");
            ids = ids[ ids.length - 1];
            confirms( "Message", "Are you sure you want to delete?",
            function(){
                $.ajax({
                    url: '<?php echo URL; ?>/companies/orders_delete_saleorder/' + ids,
                    success: function(html){
                        if(html == "ok"){
                            $(".del_orders_" + ids).fadeOut();
                            reload_subtab('orders');
                        }else{
                            console.log(html);
                        }
                    }
                });
            },function(){
                //else do somthing
            });
        });

        $(".del_purchase").click(function(){
            var names = $(this).attr("id");
            var ids = names.split("_");
            ids = ids[ ids.length - 1];
            confirms( "Message", "Are you sure you want to delete?",
            function(){
                $.ajax({
                    url: '<?php echo URL; ?>/companies/orders_delete_pur_order/' + ids,
                    success: function(html){
                        if(html == "ok"){
                            $(".del_purchase_" + ids).fadeOut();
                            reload_subtab('orders');
                        }else{
                            console.log(html);
                        }
                    }
                });
            },function(){
                //else do somthing
            });
        });
	})
</script>