<?php
	if(isset($arr_settings['relationship']['asset_tags']['block']))
	foreach($arr_settings['relationship']['asset_tags']['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>
<input type="hidden" id="custom_asset" value="" />
<script>
$(document).ready(function(){
	window_popup("equipments", "Specify assets");
	$(".viewprice_production_time",".production_time_custom").css({"font-weight":"bolder","font-size":"13px"});
	if($("#position_store").val()!=''){
		var id = $("#position_store").val();
		// var position = parseInt();
		$("#"+id).focus().val($("#"+id).val());
		$("#container_asset_tags").mCustomScrollbar("scrollTo",$("#"+id).offset().top);
	}
	<?php if(!$this->Common->check_permission('products_@_entry_@_view',$arr_permission)): ?>
	$("#block_full_asset_tags").find('[onclick],[href]').each(function(){
		if($(this).is("li"))
			$(this).removeAttr("onclick").removeAttr("title").html("");
		else
			$(this).replaceWith(function(){
				return $("<span>"+$(this).html()+"</span>");
			});
	});
	<?php endif; ?>
	<?php if($this->Common->check_permission($controller.'_@_entry_@_edit',$arr_permission)): ?>
	$("input","#block_full_asset_tags").change(function(){
		var id = $(this).attr("id");
		$("#position_store").val(id);
		var names = $(this).attr("name");
		var intext = 'box_test_'+names;
		var ids  = names.split("_");
		var index = ids.length;
		var ids = ids[index-1];
		names = names.replace("_"+ids,"");
		names = names.replace("cb_","");
		//khoi tao gia tri luu
		var asset_key = $("#asset_key_"+ids).val();
		var values = new Object();
		if(names!="products_name" && names != 'completed')
			values[names] = UnFortmatPrice($("#"+names+"_"+ids).val());
		else
			values[names] = $("#"+names+"_"+ids).val();
		//checkbox
		if(names == 'completed'){
			if($(this).is(':checked')){
				values[names] = 1;
			}else{
				values[names] = 0;
			}
		}
		values["last_change_field"] = names;
		values['key'] = $("#key_"+ids).val();
		values['asset_key'] = asset_key;
		$.ajax({
			url: "<?php echo URL.'/salesorders/save_asset_tag' ?>",
			type: "POST",
			data: values,
			success: function(result){
				if(names != 'completed'){
					if(names != 'products_name')
					$("#"+names+"_"+ids).val(FortmatPrice(values[names] ));
					$("#production_time_"+ids).val(FortmatPrice(result ));
					production_time_calculating();
				}
			}
		})
	});
	<?php else: ?>
	$("input","#block_full_asset_tags").each(function(){
		$(this).removeClass("jt_box_save ").attr("disabled",true).css("background-color","transparent");
	});
	<?php endif; ?>
	$(".del_asset_tags").click(function(){
		var rel = $(this).attr("rel");
		rel = rel.split("@");
		rel = rel[0];
		var key = $("#asset_key_"+rel).val();
		$.ajax({
			url: "<?php  echo URL.'/salesorders/delete_asset'?>",
			type: "POST",
			data: {key : key},
			success: function(result){
				if(result == "ok") {
					var production_time = parseFloat($("#production_time_"+rel).val().replace(/[,]/g,'')) || 0;
					$("#listbox_asset_tags_"+rel).fadeOut().remove();
					var total = parseFloat($("#cost_per_unit").val().replace(/[,]/g,''));
					$("#cost_per_unit").val(FortmatPrice(total - production_time));
				}
			}
		});
	})
});
function production_time_calculating(){
	var total = 0;
	$(".viewprice_production_time","#block_full_asset_tags").each(function(){
		total += parseFloat($(this).val().replace(/[,]/g,'')) || 0;
	});
	$("#cost_per_unit").val(FortmatPrice(total));
}
function open_custom_asset(key){
	$("#custom_asset").val(key);
	$('#window_popup_equipments').data('kendoWindow').open().center();
}
function after_choose_equipments(id){
	$.ajax({
		url: "<?php echo URL.'/salesorders/create_custom_asset' ?>",
		type: "POST",
		data: {key : $("#custom_asset").val(), id: id},
		success: function(result){
			$("#window_popup_equipments").data("kendoWindow").close();
			$("#asset_tags",".ul_tab").click();
		}
	});
}
</script>