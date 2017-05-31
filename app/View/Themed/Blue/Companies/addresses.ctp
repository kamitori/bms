<style type="text/css" media="screen">
.datainput_addresses   {
	overflow: visible !important;
}
</style>
<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>

<script type="text/javascript">
$(function(){
	var arrCountries = <?php echo json_encode($arr_countries); ?>;
	var arrProvinces = <?php echo json_encode($arr_provines); ?>;
	<?php foreach($subdatas['addresses'] as $key => $address){ ?>
		$("#country_<?php echo $key ?>").combobox(arrCountries);
		$("#country_<?php echo $key ?>").after('<input type="hidden" id="country_<?php echo $key ?>Id" value="" />');
		$("#province_state_<?php echo $key ?>").combobox(arrProvinces["<?php echo $address['country_id']; ?>"]);
		$("#province_state_<?php echo $key ?>").after('<input type="hidden" id="province_state_<?php echo $key ?>Id" value="" />');

	<?php } ?>
	//add addresses
	$('#bt_add_addresses').click(function(){
		var ids = $("#mongo_id").val();
		$.ajax({
			url:"<?php echo URL;?>/companies/addresses_add/" + ids,
			timeout: 15000,
			success: function(html){
				console.log(html);
				reload_subtab('addresses');
			}
		});
	})

	//change input
	$("#container_addresses").on("change", "input", function(){
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
			}else{
				$(this).prop("checked",true);
				return false;
			}
		}
		if( $.inArray(names, ["province_state", "country"]) != -1 ) {
			values[names+"_id"] = $("#"+names+"_"+ids+"Id").val();
		}
		values[names] = inval;
		if($("#cb_default_"+ids).is(":checked")){
			var tmp_name = names;
			var key_name = "Company";
			tmp_name = tmp_name.split("_");
			for(var i in tmp_name){
				if(tmp_name[i] == "id")
					continue;
				key_name += UpCaseFirst(tmp_name[i]);
			}
			$("#"+key_name).val(inval);// = #CompanyAddress1
		}

		save_option("addresses",values,ids,1,'addresses', "update",function(){
			if( names == "country" ) {
				$("#province_state_"+ids+"Id").val("");
				$("#province_state_"+ids).val("").trigger("change");

			}
		});
	});
})

function change_address(ids){
	$("#CompanyAddress1").val($("#address_1_"+ids).val());
	$("#CompanyAddress2").val($("#address_2_"+ids).val());
	$("#CompanyAddress3").val($("#address_3_"+ids).val());
	$("#CompanyTownCity").val($("#town_city_"+ids).val());
	$("#CompanyProvinceState").val($("#province_state_"+ids).val());
	$("#CompanyZipPostcode").val($("#zip_postcode_"+ids).val());
	$("#CompanyCountry").val($("#country_"+ids).val());
	//$("#CompanyCountryId").val($("#country_id_"+ids+"Id").val());
	window_popup('addresses', 'Specify address','company','click_open_window_addressescompany','?company_id='+$("#mongo_id").val(),'force_re_install');
}
</script>