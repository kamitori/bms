<?php
	$arr_pricing = $arr_settings['relationship'][$sub_tab]['block'];
?>

<?php echo $this->element('box',array('key'=>'sellprices','arr_val'=>$arr_pricing['sellprices'])); ?>
<div class="clear_percent_9_arrow float_left" style="margin:0px;padding:0px; width:1%;">
    <div class="box_arrow" style="padding:0px; margin:200px 0px 0px 0px;">
        <span class="icon_emp" style="padding:0px; margin:0px;"></span>
    </div>
</div>
<?php echo $this->element('box',array('key'=>'pricebreaks','arr_val'=>$arr_pricing['pricebreaks'])); ?>

<?php echo $this->element('box',array('key'=>'otherpricing','arr_val'=>$arr_pricing['otherpricing'])); ?>

<?php echo $this->element('box',array('key'=>'pricingsummary','arr_val'=>$arr_pricing['pricingsummary'])); ?>

<input type="hidden" name="sell_sum" id="sell_sum" value="<?php if(isset($sell_sum)) echo $sell_sum;?>" />

<input type="hidden" name="current_category" id="current_category" value="<?php if(isset($_SESSION['Products_current_category'])) echo $_SESSION['Products_current_category'];?>" />

<p class="clear"></p>
<!--JS Dành cho phần Pricing-->
<script>

function set_line_choiced(items){
	var current_category,items;
	if(items==undefined){
		current_category = $("#current_category").val();
		items = $(".sell_category_"+current_category).val();
	}
	if(items!=undefined){
		$("#block_full_sellprices .ul_mag").removeClass('active_sell_price');
		$("#listbox_sellprices_"+items).addClass('active_sell_price');
		console.log(items);
	}
}

$(document).ready(function() {

	set_line_choiced();

	//click chọn line
	$("#block_full_sellprices ul").click(function(){
		var items = $(this).attr("id");
		items = items.split("_");
		var lengths = items.length;
		items = items[lengths-1];
		var	category = $("#category_text_"+items).val();
		$("#current_category").val(category);
		set_line_choiced(items);
		$.ajax({
			url: '<?php echo URL; ?>/products/writeCate',
			type: 'POST',
			data: {category: category},
			success: function(result){
				console.log(result);
				reload_box('pricebreaks');
			}
		});
	});

	//Sell prices for this item
	$("#bt_add_sellprices").click(function() {
		var sum = $("#sell_sum").val();
		sum = parseInt(sum);
		var datas;
		if(sum>0){
			datas = {
				'sell_category' : '',
				'sell_default' : 0
			};
		}else{
			datas = {
				'sell_category' : 'Retail',
				'sell_default' : 1
			};
		}
		save_option('sellprices',datas,'',1,'pricing','add'
			,function(result){
				if(result=='reach_limit')
					alerts('Message','You reached limit of product category.');
			});
	});



	$('#block_full_sellprices').delegate(".rowedit input,.rowedit select","change",function(){
		$(this).removeClass('error_input');
		//nhan id
		var isreload=0;
		var names = $(this).attr("name");
		var intext = 'box_test_'+names;
		var inval = $(this).val();
		var ids  = names.split("_");
		var index = ids.length;
		var ids = ids[index-1];

		//khoi tao gia tri luu
		names = names.replace("_"+ids,"");
		names = names.replace("cb_","");
		var values = new Object();
			values[names]=inval;

		//luu lai
		save_option('sellprices',values,ids,isreload,'pricing','update'
			,function(result){
				if(result=='existed'){
					$("#sell_category_"+ids).addClass('error_input');
					alerts('Message','This tag has been included in the list. Please select another one.',function(){
						reload_subtab('pricing');
					});
					return false;
				}else if(names=="sell_category"){
					console.log(inval);
					$("#current_category").val(inval);
					$.ajax({
						url: '<?php echo URL; ?>/products/writeCate',
						type: 'POST',
						data: {category: inval},
						success: function(){
							set_line_choiced(ids);
							reload_subtab('pricing');
						}
					});

				}else if(names=="sell_unit_price"){
					var	category = $("#category_text_"+ids).val();
					if(category=='Retail'){
						$("#sell_price").val(inval);
						$("#rel_sell_price").val(FortmatPrice(inval));
						reload_subtab('pricing');
					}
					$("#sell_unit_price_"+ids).val(FortmatPrice(inval));
					$("#box_test_sell_unit_price_"+ids).val(inval);
				}



			});

	});





	//Price breaks: Retail
	$("#bt_add_pricebreaks").click(function() {
		var sell_price = $('#sell_price').val();
		var sell_category = $('#current_category').val();
		var datas = {
				'sell_category' : sell_category,
				'range_from' : 0,
				'range_to' : 0,
				'unit_price' : sell_price
			};
		save_option('pricebreaks',datas,'',1,'pricing','add');
	});

	$(".breaks_rowedit input").focusout(function(){
		var ids = $(this).attr("id");
		var names = ids;
		ids  = ids.split("_");
		var ind = ids.length;
		var ids =  ids[ind-1];
			names = names.replace("_"+ids,"");
		if(names=='breaks_unit_price'){
			ids  = parseInt(ids)+1;
			$(".breaks_rowedit_"+ids).css('display','block');
			$(".breaks_rowtest_"+ids).css('display','none');
		}
	});

	$("#block_full_pricebreaks input,#block_full_pricebreaks select").change(function(){
		//nhan id
		$(this).removeClass('error_input');
		var isreload=1;
		var names = $(this).attr("name");
		var intext = 'box_test_'+names;
		var inval = $(this).val();
		if(inval.trim()==''||inval==0)
		{
			$(this).addClass('error_input');
			alerts('Message','This value must be not empty.');
			return false;
		}
		inval = parseFloat(inval);
		var ids  = names.split("_");
		var index = ids.length;
		var ids = ids[index-1];
		names = names.replace("_"+ids,"");
		names = names.replace("cb_","");
		var values = new Object();
			values[names]=inval;
		var current = $(this);
		//luu lai
		save_option('pricebreaks',values,ids,isreload,'pricing','update',function(result){
			if(result=='range_from_greater_than_range_to'){
				current.addClass('error_input');
				alerts('Message','The \'range from\' value must be lesser than the \'range to\' value.');
				return false;
			}
			else if(result=='range_to_in_range' || result=='range_from_equals_range_to'){
				current.addClass('error_input');
				alerts('Message','The \'range to\' value must not in range of any existed \'range from\' to \'range to\'.');
				return false;
			}
			else if(result=='range_from_in_range'){
				current.addClass('error_input');
				alerts('Message','The \'range from\' value must not in range of any existed \'range from\' to \'range to\'.');
				return false;
			}
			if(names=='unit_price'){
				var valuesf = FortmatPrice(inval);
				$('#unit_price_'+ids).val(valuesf);
			}
		},names);

	});

	//input_show_select_calendar(".JtSelectDate");

	$("#date_update").change(function(){
		var datas = $(this).val();
		save_field('update_price_date',datas,'');
	});


	$("#otherpricing_price_note").change(function(){
		var datas = $(this).val();
		save_field('price_note',datas,'');
	});
	$("#pricing_method_list").change(function(){
		var datas = $("#pricing_method_listId").val();
		save_field('pricing_method',datas,'');
	});
	$("#pricing_bleed_list").change(function(){
		var datas = $("#pricing_bleed_listId").val();
		save_field('pricing_bleed',datas,'');
	});
	$("#price_breaks_type").change(function(){
		var datas = $("#price_breaks_typeId").val();
		save_data('price_breaks_type',datas,'',function(){
			$("#price_breaks_type").val('');
			reload_subtab('pricing');
		});
	});


});

</script>