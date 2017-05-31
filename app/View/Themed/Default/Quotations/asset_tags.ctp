<?php
	if(isset($arr_settings['relationship']['asset_tags']['block']))
	foreach($arr_settings['relationship']['asset_tags']['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>
<script>
$(document).ready(function(){
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
	$("#block_full_asset_tags").delegate(".rowedit input","change",function(){
		var id = $(this).attr("id");
		$("#position_store").val(id);
		var names = $(this).attr("name");
		var intext = 'box_test_'+names;
		var ids  = names.split("_");
		var index = ids.length;
		var ids = ids[index-1];
		//khoi tao gia tri luu
		var asset_key = $("#asset_key_"+ids).val();
		var factor = UnFortmatPrice($("#factor_"+ids).val());
		var mu = UnFortmatPrice($("#min_of_uom_"+ids).val());
		var values = new Object();
		values['factor'] = factor;
		values['min_of_uom'] = mu;
		values['key'] = $("#key_"+ids).val();
		save_option('asset_tags',values,asset_key,1,'asset_tags','update',function(ret){},names);
	});
	<?php else: ?>
	$("input","#block_full_asset_tags").each(function(){
		$(this).removeClass("jt_box_save ").attr("disabled",true).css("background-color","transparent");
	});
	<?php endif; ?>
});
</script>