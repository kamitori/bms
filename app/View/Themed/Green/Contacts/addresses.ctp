<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>

<script type="text/javascript">
$(function(){
	//add addresses
	var provinces = <?php echo json_encode($provinces) ?>;
	$('#bt_add_addresses').click(function(){
		var ids = $("#mongo_id").val();
		$.ajax({
			url:"<?php echo URL;?>/contacts/addresses_add/" + ids,
			timeout: 15000,
			success: function(html){
				reload_subtab('addresses');
			}
		});
	})

	$(".viewcheck_default").click(function(){
		var names = $(this).attr('name');
		names = names.split("_");
		names = names[names.length - 1];
		$("#addresses_default_key").val(names);
	})

	//change input
	$("input","#container_addresses").change(function(){
		var fieldChange = '';
		var names = $(this).attr("name");
		var inval = $(this).val();
		var ids = names.split("_");
		ids = ids[ ids.length - 1];
		names = names.replace("_"+ids,"");
		names = names.replace("cb_","");
		var values = new Object();
		if(names == 'default'){
			if($(this).is(':checked')){
				inval = true;
				change_address(ids);
				$("input[type=checkbox]","#container_addresses").each(function(){
					if($(this).is(":checked")){
						var checkbox_values = new Object();
						checkbox_values['default'] = 0;
						var checkbox_ids = $(this).attr("name");
						checkbox_ids = checkbox_ids.split("_");
						checkbox_ids = checkbox_ids[ checkbox_ids.length - 1];
						if(ids != checkbox_ids) {
							save_option("addresses",checkbox_values,checkbox_ids,0,'addresses');
						}
					}
				})
				save_field('addresses_default_key',ids,'');
				fieldChange = "default";
			}else{
				$(this).prop("checked",true);
				return false;
			}
		}
		if($(this).parent().hasClass("combobox")){
			var thisID = $(this).attr("id");
			values[names] = $("#"+thisID+"Id").val();
			values[names.replace("_id","")] = inval;
			if(provinces[values[names]] == undefined)
				values[names] = inval;

			if(thisID.match("country_id")){
				var province = $("#province_state_id_"+ids);
				$("#province_state_id_"+ids+"Id").val("");
				province.parent().parent().html(province);
				province.val("");
				province.combobox(provinces[values[names]]);
				//
				if($("#cb_default_"+ids).is(":checked")){
					var entry_province = $("#InvoiceProvinceState");
					entry_province.parent().parent().html(entry_province);
					entry_province.val("").attr("onchange","save_address_function();");
					entry_province.combobox(provinces[values[names]]);
				}
			}
		}
		else
			values[names] = inval;
		if($("#cb_default_"+ids).is(":checked")){
			var tmp_name = names;
			var key_name = "Invoice";
			tmp_name = tmp_name.split("_");
			for(var i in tmp_name){
				if(tmp_name[i] == "id")
					continue;
				key_name += UpCaseFirst(tmp_name[i]);
			}
			$("#"+key_name).val(inval);// = #CompanyAddress1
		}
		save_option("addresses",values,ids,1,'addresses','',function(){
		},fieldChange);
	});
})
function change_address(ids){
	$("#InvoiceAddress1").val($("#address_1_"+ids).val());
	$("#InvoiceAddress2").val($("#address_2_"+ids).val());
	$("#InvoiceAddress3").val($("#address_3_"+ids).val());
	$("#InvoiceTownCity").val($("#town_city_"+ids).val());
	$("#InvoiceProvinceState").val($("#province_state_"+ids).val());
	$("#InvoiceZipPostcode").val($("#zip_postcode_"+ids).val());
	$("#InvoiceCountry").val($("#country_id_"+ids).val());
	$("#InvoiceCountryId").val($("#country_id_"+ids+"Id").val());
	window_popup('addresses', 'Specify address','company','click_open_window_addressescompany','?contact_id='+$("#mongo_id").val(),'force_re_install');
}
</script>