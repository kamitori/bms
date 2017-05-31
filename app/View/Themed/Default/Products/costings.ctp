<?php
	$arr_setup = $arr_settings['relationship'][$sub_tab]['block'];
?>
<table cellpadding="0" cellspacing="0" id="jt_tb_sub" style="width:100%; margin-bottom:50px;">
	<tr>
    	<?php //cột trái : made up và use on?>
    	<td style="width:83%;" valign="top">
        	<?php if(isset($arr_setup['madeup'])) echo $this->element('box',array('key'=>'madeup','arr_val'=>$arr_setup['madeup']));?>
            <?php if(isset($arr_setup['useon'])) echo $this->element('box',array('key'=>'useon','arr_val'=>$arr_setup['useon']));?>
        </td>
        <td style="width:1%;" valign="top">&nbsp;</td>
        <?php //cột phải : Pricing summary ?>
        <td style="width:16%;" valign="top">
           <?php echo $this->element('box',array('key'=>'pricingsummary','arr_val'=>$arr_setup['pricingsummary']));?>
        </td>
    </tr>
</table>

<?php if(isset($is_choice) && $is_choice=='1'){
/** Tính năng chọn popup. Render js cho tính năng chọn chung 1 popup
* Dùng chung cho các tab có chọn popup
* Use: set từ controller biến $is_choice để dùng
*/
$po_cls = 'products';
$po_title = 'Specify Products';
$po_para = '?products_group_type=BUY&products_assemply_item=1';

?>
<!--Tính năng tạo popup chung cho các product-->
<input type="hidden" id="id_choicing_for_popup" value="" />
<script>
	$(document).ready(function() {
		window_popup("<?php echo $po_cls;?>", "<?php echo $po_title;?>","choice_code", "id_choicing_for_popup","?products_product_type=Vendor Stock&products_is_costing=1");



		$(".choice_codes").click(function(){
			var ids = $(this).attr("id");
			var key_click_open = ids;
			ids  = $("#"+key_click_open).attr("rel");
			var html = $('#'+key_click_open).html();
			ajax_note(" Change Code "+html);
			$("#id_choicing_for_popup").val(ids);
			$("#id_choicing_for_popup").click();
		});

	});
</script>
<?php }?>



<!--JS Dành cho phần Costings-->
<script>
<?php if(isset($subdatas['pricingsummary']['cost_price'])){ ?>
$("#cost_price",".form_products").val("<?php echo $subdatas['pricingsummary']['cost_price'] ?>");
$("input[name=cost_price_cb]",".form_products").val("<?php echo number_format($subdatas['pricingsummary']['cost_price'],2) ?>");
<?php } ?>
$(document).ready(function() {
	//tạo thêm 1 cost / item mới
	$("#bt_add_madeup").click(function() {
		$("#id_choicing_for_popup").val('add');
		$("#id_choicing_for_popup").click();
	});

	$(".rowedit input").focusout(function(){
		var ids = $(this).parent().attr("id");
		ids  = ids.split("_");
		var ind = ids.length;
		var idfield = ids[ind-2];
		if(idfield=='quantity'){
			ids  = parseInt(ids[ind-1])+1;
			$(".rowedit_"+ids).css('display','block');
			$(".rowtest_"+ids).css('display','none');
		}
		$("#madeup_name_"+ids).focus();
	});

	$(".del_madeup").focusin(function(){
		ajax_note_set("");
		var ids = $(this).attr("id");
		ids  = ids.split("_");
		var ind = ids.length;
		var idfield =  parseInt(ids[ind-1]);
		ajax_note_set(" Press ENTER to delete the line:"+(idfield+1));
	});
	$(".del_madeup").focusout(function(){
		ajax_note("");
		var ids = $(this).attr("id");
			ids = ids.split("_");
		var index = ids.length;
		ids  = parseInt(ids[index-1])+1;
		$(".jt_line_over").removeClass('jt_line_over');
		$("#listbox_madeup_"+ids).addClass('jt_line_over');
		$("#madeup_name_"+ids).focus();
	});


	$(".rowedit input,.viewcheck_view_in_detail").change(function(){
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
		if(names=='view_in_detail'){
			values[names]= 0;
			if($(this).is(':checked'))
				values[names]= 1;
			values['for_line'] = $("#subitems").val();
			values['deleted'] = false;
		}
		//luu lai
		save_option("madeup",values,ids,1,'costings');

	});


});


function change_total(ids,sum){
	var total = $('#total_madeup').val();
	var rowvl = $('#txt_sub_total_'+ids).html();
	rowvl = rowvl.replace("$","");
	total = parseFloat(total) - parseFloat(rowvl) + parseFloat(sum);

	sum = FortmatPrice(sum);
	var totalf = FortmatPrice(total);
	$('#txt_sub_total_'+ids).html(sum);
	$('#total_madeup').val(totalf);
	save_field('cost_price',total,'');
}

</script>




<!--===== THÊM VÀO CÁC MODULE DÙNG SUPPLIER POPUP PRODUCT  =====-->
<span id="click_open_window_companiessearch_company_name" style=" display:none;"></span>
<script type="text/javascript">
	$(function(){
		window_popup('companies', 'Specify Current supplier','search_company_name','click_open_window_companiessearch_company_name','?is_supplier=1','no_auto_close');
	});
</script>
