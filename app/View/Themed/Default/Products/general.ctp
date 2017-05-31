<?php
$general = $arr_settings['relationship'][$sub_tab]['block'];
?>
<table cellpadding="0" width="100%" cellspacing="0" style="width:100%; margin:0; padding:0;">
	<tr>
    	<td>
        	<?php echo $this->element('box', array('key' => 'image', 'arr_val' => $general['image'])); ?>
            <?php echo $this->element('box', array('key' => 'productoptions', 'arr_val' => $general['productoptions'])); ?>
        </td>
    </tr>
    <tr>
    	<td>
        	<?php echo $this->element('box', array('key' => 'stocktracking', 'arr_val' => $general['stocktracking'])); ?>
            <input name="total_cost_temp" id="total_cost_temp" value="<?php if (isset($total_cost)) echo $total_cost;
            else echo '0'; ?>" type="hidden" />

            <input type="hidden" name="supplier_current" id="supplier_current" value="<?php if (isset($subdatas['supplier_current'])) echo $subdatas['supplier_current']; ?> " />

            <?php //echo $this->element('box', array('key' => 'pricing_method', 'arr_val' => $general['pricing_method'])); ?>

          	<?php echo $this->element('box', array('key' => 'same_category', 'arr_val' => $general['same_category'])); ?>
        </td>
    </tr>
</table>

<span id="groupstr" style="display:none;"><?php if(isset($groupstr)) echo $groupstr;?></span>

<?php if(isset($is_choice) && $is_choice=='1'){
/** Tính năng chọn popup. Render js cho tính năng chọn chung 1 popup
* Dùng chung cho các tab có chọn popup
* Use: set từ controller biến $is_choice để dùng
*/
$po_cls = 'products';
$po_title = 'Specify options for this item';
$po_para = '?products_product_type=Product';
$choice_key = 'option';

?>
<!--Tính năng tạo popup chung cho các product-->
<input type="hidden" id="id_choicing_for_popup" value="" />
<script>
	$(document).ready(function() {
		window_popup("<?php echo $po_cls;?>", "<?php echo $po_title;?>","choice_code_<?=$choice_key;?>", "id_choicing_for_popup","<?php echo $po_para;?>");

		$(".choice_codes").click(function(){
			var ids = $(this).attr("id");
			var key_click_open = ids;
			ids  = $("#"+key_click_open).attr("rel");
			var html = $('#'+key_click_open).html();
			ajax_note(" Change Code "+html);
			$("#id_choicing_for_popup").val(ids);
			$("#id_choicing_for_popup").click();
		});
        fixHiddenCombobox("container_productoptions");
	});
</script>
<?php }?>



<!--JS Dành cho phần general-->
<script>
    $(function() {
        //canh lại hình
        if ($(".box_image").has("img").length) {
            var imgheight_box = $(".box_image").height();
            var imgheight = $(".box_image img").height();
            caoi = (imgheight_box - imgheight) / 2;
            caoi = Math.round(caoi);
            $(".box_image img").css('margin-top', caoi);
        }
        //upload
        $("#<?php echo $controller; ?>_upload").change(function() {
            $("#<?php echo $controller; ?>_upload_form").submit();
        });

		//phan auto load cua stock tracking
		$("#block_full_stocktracking").delegate("input","change",function(){
			var vls = $(this).val();
            var ids = $(this).attr("name");
			var types = $(this).attr("type");
			var valueid = '';
			console.log(vls+'--'+ids);

			//custom value
			if(types=='checkbox'){
				if($(this).is(':checked'))
					vls = 1;
				else
					vls = 0;
			}
			if($("#"+ids).parent().attr("class") == 'combobox'){
				vls = $("#"+ids+"Id").val();
			}
			if(ids=='qty_in_stock' || ids=='qty_on_so' || ids=='qty_balance' || ids=='serial_length'){
				$("#rel_"+ids).val(vls);
			}
			save_data(ids,vls,'',valueid,function(arr_return){
				console.log('da save.');
				reload_subtab('general');
			})

		});



		//Pricing Method
		/*$("#add_more_rules").click(function() {
            window.location.assign("<?php//echo URL . '/' ?>rules/add/");
        });
        $("#pricing_method_name").change(function() {
            //option,arr_value(key1@value1,key2@value2),opid,isreload
            var pmid = $("#pricing_method_id").val();
            var note = $("#pricing_method_name").val();
            var arr = 'pricing_method_name@' + note;
            update_option('pricing_method', arr, pmid, 0);
        });
        $("#pricing_rule_unit").change(function() {
            //option,arr_value(key1@value1,key2@value2),opid,isreload
            var pmid = $("#pricing_method_id").val();
            var rule_unit = $("#pricing_rule_unit").val();
            var arr = 'pricing_rule_unit@' + rule_unit;
            update_option('pricing_method', arr, pmid, 0);
        });
        $("#remove_rules").click(function() {
            $.ajax({
                url: '<?php//echo URL . '/' . $controller; ?>/deleteopt/0@pricing_method',
                type: "POST",
                success: function(txt) {
                    $("#pricing_method_id").val('');
                    $("#pricing_method_name").val('');
                    $("#rule_name").val('');
                    $("#rule_id").val('');
                    $("#rule_formula").html('');
                    $("#rule_description").html('');
                    ajax_note(" Deleted !");
                }
            });
        });*/



		//tạo thêm 1 option items
		$("#bt_add_productoptions").click(function() {
			$("#id_choicing_for_popup").val('add');
			$("#id_choicing_for_popup").click();
		});

		// $("#container_productoptions input").focusout(function(){
		// 	var ids = $(this).attr("id");
		// 	if(ids.split("_")!=undefined)
		// 		ids  = ids.split("_");
		// 	else
		// 		return true;
		// 	var ind = ids.length;
		// 	var idfield = ids[ind-2];
		// 	if(idfield=='quantity'){
		// 		ids  = parseInt(ids[ind-1])+1;
		// 		$(".rowedit_"+ids).css('display','block');
		// 		$(".rowtest_"+ids).css('display','none');
		// 	}
		// 	$("#madeup_name_"+ids).focus();
		// });

		$(".del_productoptions").focusin(function(){
			ajax_note_set("");
			var ids = $(this).attr("id");
			ids  = ids.split("_");
			var ind = ids.length;
			var idfield =  parseInt(ids[ind-1]);
			ajax_note_set(" Press ENTER to delete the line:"+(idfield+1));
		});
		$(".del_productoptions").focusout(function(){
			ajax_note("");
			var ids = $(this).attr("id");
				ids = ids.split("_");
			var index = ids.length;
			ids  = parseInt(ids[index-1])+1;
			$(".jt_line_over").removeClass('jt_line_over');
			$("#listbox_madeup_"+ids).addClass('jt_line_over');
			$("#madeup_name_"+ids).focus();
		});


		$("#container_productoptions input").change(function(){
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


			//neu la group
			var is_new = 0;
			if(names=='option_group'){
				var group_str = $("#groupstr").text();
				var group = new Array();
					is_new = 1;
				if(group_str!=''){
					group = group_str.split(",");
					for(var i=0;i<group.length;i++){
						if(inval==group[i]){
							is_new = 0; break;
						}
					}
				}
			}
			if($(this).is(":checkbox")){
				if($(this).is(':checked'))
					inval = 1;
				else
					inval = 0;
			}
			var idp = $(this).attr("id");
			if($("#"+idp).parent().attr("class") == 'combobox' && names!='option_group'){
				inval = $("#"+idp+"Id").val();
			}
			var values = new Object();
				values[names]=inval;

			if(is_new==1){
				var ButtonListArr = ['','Exclusive','Inclusive'];
				confirms3('Messages',"This is the new group. Which Group Type do you want to save?\n <span class='bold'>Exclusive</span>: Only choose one, <br /><span class='bold'>Inclusive</span>: Choose many item",ButtonListArr,function(){
					values['group_type']='';
					save_option("options",values,ids,1,'general');
				},function(){
					values['group_type']='Exc';
					save_option("options",values,ids,1,'general');
				},function(){
					values['group_type']='Inc';
					save_option("options",values,ids,1,'general');
				},function(){

				});

			}else{
				save_option("options",values,ids,1,'general');
			}
			//luu lai

		});


    });





    function after_choose_rules(ids, names, keys) {
        $("#rule_name").val(names);
        $("#rule_id").val(ids);
        var pmid = $("#pricing_method_id").val();
        var note = $("#pricing_method_name").val();
        var unit_price = $("#pricing_rule_unit").val();
        var rule_id = ids;
        var func = '';
        if (pmid != '')
            func = 'update';
        else
            func = 'add';
        $(".k-window").fadeOut();
        $.ajax({
            url: '<?php echo URL . '/' . $controller; ?>/ajax_pricing_method',
            type: "POST",
            data: {func: func, rule_id: rule_id, unit_price: unit_price, note: note, pmid: pmid},
            success: function(rtu) {
                ajax_note("Saved !");
                reload_subtab('general');
            }
        });
    }




</script>





<!--===== THÊM VÀO CÁC MODULE DÙNG SUPPLIER POPUP PRODUCT  =====-->
<span id="click_open_window_companiessearch_company_name" style=" display:none;"></span>
<span id="click_open_window_companiessearch_prefer_customer" style=" display:none;"></span>
<script type="text/javascript">
	$(function(){
		window_popup('companies', 'Specify Current supplier','search_company_name','click_open_window_companiessearch_company_name','?is_supplier=1','no_auto_close');
		window_popup('companies', 'Specify Prefer customer','search_prefer_customer','click_open_window_companiessearch_prefer_customer','','no_auto_close');
	});
</script>
