<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>

<script type="text/javascript">
$(function(){
	$("#bt_add_finished_size_list").click(function(){
		var count = $(".new-ul", "#container_finished_size_list").length;
		var html = ['<ul class="new-ul ul_mag clear bg2">',
						'<li style="width:18%">',
							'<input type="hidden" name="materials['+count+'][name]" value="Size '+count+'" />',
							'<input type="hidden" name="materials['+count+'][id]" value="'+count+'" />',
							'<input class="input_inner jt_box_save" name="materials['+count+'][width]" onkeypress="return isPrice(event);" type="text" value="12" />',
						'</li>',
						'<li style="width:18%">',
							'<input class="input_inner jt_box_save" name="materials['+count+'][height]" onkeypress="return isPrice(event);" type="text" value="12" />',
						'</li>',
						'<li style="width:48%">',
							'<input  class="input_inner jt_box_save" name="materials['+count+'][quantity]" onkeypress="return isPrice(event);" type="text" value="1" />',
						'</li>',
						'<li style="width:5%">',
							'<div class="jt_right_check">',
								'<a title="Delete" onclick="deleteLine(this);" href="javascript:void(0);">',
									'<span class="icon_remove2"></span>',
								'</a>',
                            '</div>',
                        '</li>',
                    '</ul>'].join("");
		$("ul[class!=new-ul]:last", "#container_finished_size_list").remove();
		$(".mCSB_container","#container_finished_size_list").prepend(html);
		$("input:first",".new-ul").focus();
		changeColor();
	});
$("#button", "#block_full_sheetimage").click(function(){
	if( !checkValid() ){
		return false;
	}
	$("#result-link", "#block_full_sheetimage").remove();
	$("#results_type").val("");
	$("#results_total_sheet").val("");
	var data = $("input", "#container_finished_size_list").serialize();
	data += "&poster_size_w="+$("#poster_size_w").val()+"&poster_size_h="+$("#poster_size_h").val();
	$(".jt_subtab_box_cont", "#block_full_sheetimage").html('<span style=" width:100%; height:100%; display:table; text-align:center; font-size:11px; vertical-align:middle; height:290px;padding: 10px 0px;"><img src="<?php echo URL.'/theme/'.$theme.'/images/ajax-loader.gif'; ?>" title="Loading..." /></span>');
	$.ajax({
		url: "<?php echo URL.'/products/cutting_process' ?>",
		type: "POST",
		data: data,
		success: function(result){
			result = $.parseJSON(result);
			if(result.status == "error"){
				alerts("Message", result.message);
			} else {
				$(".icon_down_tl ", "#block_full_sheetimage").after('<a  id="result-link" style="float: right;font-weight: bold;" href="'+result.image+'" target="_blank" >Open image in new tab</a>');
				$(".jt_subtab_box_cont", "#block_full_sheetimage").html('<img src="'+result.image+'" style=" width:100%; height:100%; display:table; text-align:center; font-size:11px; vertical-align:middle; height:290px;padding: 10px 0px;" />');
				$("#results_type").val(result.type);
				$("#results_total_sheet").val(result.sheet);
			}
		}
	});
});
});
function deleteLine(obj)
{
	$(obj).closest("ul").fadeOut().remove();
	changeColor();
}
function changeColor()
{
	var i = 0;
	$(".ul_mag","#container_finished_size_list").each(function(){
		$(this).removeClass("bg2").removeClass("bg1");
		if( i % 2 == 0 )
			$(this).addClass("bg2");
		else
			$(this).addClass("bg1");
		i++;
	});
}
function checkValid()
{
	var arr = ["poster_size_w", "poster_size_h"];
	for( i in arr ){
		if( $("#"+arr[i]).val().trim() == "" || $("#"+arr[i]).val() == 0 ){
			alerts("Message", "Please enter valid infomation.", function(){
				$("#"+arr[i]).focus();
			});
			return false;
		}
	}
	if( ! $(".new-ul", "#container_finished_size_list").length ){
		alerts("Message", "Size list is not allowed empty.")
		return false;
	}
	return true;
}
</script>