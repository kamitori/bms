<input id="product_choice_sku" type="hidden" value="" />
<?php
	$i = 0;
	if(!empty($expenses)){
		foreach($expenses as $key=>$expense){
?>
	<ul id="expense_<?php echo $key ?>" class="ul_mag clear bg<?php echo ($i%2==0? '2':'1'); ?>">
	    <li class="hg_padd" style="width:10%">
	    	<input type="hidden" id="id_<?php echo $key ?>" value="<?php echo $expense['expense_id']; ?>" />
		    <div class="jt_choice_2 choice_sku" id="choice_sku_0 " title="Choice sku" onclick="$('#product_choice_sku').click();$('#product_choice_sku').val('<?php echo $key; ?>');">
		    	<?php echo $expense['code']; ?>
	    	</div>
	    	<?php if(is_object($expense['expense_id'])){ ?>
    		<a href="<?php echo URL.'/products/entry/'.$expense['expense_id']; ?>">
        		<span class="icon_emp2 float_right"></span>
        	</a>
	        <?php } ?>
	    </li>
	    <li class="hg_padd" style="width:22%">
	    	<input class="input_inner jt_box_save" type="text" id="expense_name_<?php echo $key ?>" value="<?php echo $expense['expense_name']; ?>" />
	    </li>
	    <li class="hg_padd center_txt" style="width:9%">
	    	<input class="JtSelectDate  input_inner" type="text"  readonly="readonly" id="expense_date_<?php echo $key ?>" value="<?php echo (is_object($expense['expense_date']) ? date('m/d/Y',$expense['expense_date']->sec) : ''); ?>" />
	    </li>
	    <li class="hg_padd center_txt" style="width:8%">
	        <div class="middle_check">
	            <label class="m_check2">
	                <input type="checkbox" id="billable_<?php echo $key ?>" <?php if($expense['billable']==1) echo 'checked="checked"'; ?> />
	                <span class="bx_check"></span>
	            </label>
	        </div>
	    </li>
	    <li class="hg_padd center_txt" style="width:8%">
	    	<?php
	    		echo $expense['category'];
	    	?>
	    </li>
	    <li class="hg_padd right_txt" style="width:10%">
	    	<input class="input_inner jt_box_save right_txt" type="text" id="cost_price_<?php echo $key ?>" value="<?php echo $this->Common->format_currency($expense['cost_price']); ?>" />
	    </li>
	    <li class="hg_padd right_txt" style="width:11%">
	    	<input class="input_inner jt_box_save right_txt" type="text" id="quantity_<?php echo $key ?>" value="<?php echo $expense['quantity']; ?>" />
	    </li>
	    <li class="hg_padd right_txt" style="width:10%" id="sub_total_<?php echo $key; ?>"><?php echo $this->Common->format_currency($expense['sub_total']); ?></li>
	    <li class="hg_padd bor_mt" style="width:1.5%">
	        <div class="middle_check">
	            <a id="delete_<?php echo $key ?>" href="javascript:void(0)" onclick="delete_expense('<?php echo $key; ?>')" title="Delete expense">
	                <span class="icon_remove2"></span>
	            </a>
	        </div>
	    </li>
	</ul>
<?php
			$i++;
		}
	}
	if($i < 5){
		for($i; $i<5; $i++){
			echo '<ul class="ul_mag clear bg'.($i%2==0? '2':'1').'"></ul>';
		}
	}
?>
<script type="text/javascript">
$(function(){
	window_popup("products","Specify Product","","product_choice_sku");
	input_show_select_calendar(".JtSelectDate","#container_expense");
    $("#container_expense").mCustomScrollbar({
        scrollButtons:{
            enable:false
        },
        advanced:{
            updateOnContentResize: true,
            autoScrollOnFocus: false,
        }
    });
    $("#container_expense").mCustomScrollbar("scrollTo", "bottom");
    $("input","#container_expense").change(function(){
    	var name = id = $(this).attr("id");
    	id = id.split("_");
    	id = id[id.length - 1];
    	name = name.replace("_"+id,"");
    	var value = $(this).val();
    	if($(this).is(":checkbox")){
    		value = 0;
    		if($(this).is(":checked"))
    			value = 1;
    	}

    	save_expense(name,value,id);
    });
})
	function after_choose_products(product_id){
		var key = $('#product_choice_sku').val();
		$.ajax({
			url: "<?php echo URL.'/timelogs/expense_add' ?>",
			type: "POST",
			data: {product_id:product_id, key: key},
			success: function(html){
				$("#window_popup_products").data("kendoWindow").close();
				$("#container_expense").html(html);
			}
		});
	}
	function delete_expense(key){
		confirms("Confirm delete","Are you sure you want to delete this record?"
	         ,function(){
	         	$("#expense_"+key).animate({
				  opacity:'0.1',
				  height:'1px'
				},500,function(){$(this).remove();});
	         	$.ajax({
					url: "<?php echo URL.'/timelogs/expense_delete' ?>/"+key,
					success: function(result){
						result = $.parseJSON(result);
						$("#total_expense").val(FormatPrice(result.total_expense));
						reset_bg('expense');
					}
				});
	         },function(){
	         	return false;
	         });
	}
	function add_expense(){
		$.ajax({
			url: "<?php echo URL.'/timelogs/expense_add' ?>",
			success: function(html){
				$("#container_expense").html(html);
			}
		});
	}
	function save_expense(name,value,key){
		$.ajax({
			url: "<?php echo URL.'/timelogs/expense_save' ?>",
			type: "POST",
			data: {name:name, value:value, key: key},
			success: function(result){
				result = $.parseJSON(result);
				$("#cost_price_"+key).val(FormatPrice(result.cost_price));
				$("#quantity_"+key).val(result.quantity);
				$("#sub_total_"+key).html(FormatPrice(result.sub_total));
				$("#total_expense").val(FormatPrice(result.total_expense));
			}
		});
	}
</script>