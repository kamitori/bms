<script type="text/javascript">
$(function(){
	//Link Sub Tab
	$(".ul_tab li").click(function() {
		
		var val = $(this).attr("id");
		$(".ul_tab li").removeClass("active");
		$("#"+val).addClass("active");
		ajax_note_set("Loading...");
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/sub_tab/'+val+'/<?php echo $iditem;?>',
			success: function(html){
				$("#load_subtab").stop().html(html);
				ajax_note("");
			}
		});
		
	});
	$(".jt_ajax_note").html('');
	
	
	$('html').on('keyup', function(e) {
		if (e.which == 13) {
			search_entry();
		}
	});
		
});

function search_entry(){
	ajax_note_set("Finding...");
}

//Hien thong bao truoc khi ajax
function ajax_note_set(txt){
	$(".jt_ajax_note").stop().fadeIn(1);
	$(".jt_ajax_note").css("color","#222");
	$(".jt_ajax_note").html(txt);
}
//Hien thong bao sau khi ajax thanh cong
function ajax_note(txt){
	$(".jt_ajax_note").stop().html(txt);
	$(".jt_ajax_note").fadeOut(1500, function() {
		$(".jt_ajax_note").html("");
		$(".jt_ajax_note").fadeIn(100);
	});
}


// xử lý sau khi chọn company
function after_choose_companies(ids,names,keys){
	var m,changes;
	var arr = new Array();
	arr[0] = keys;
	arr[1] = 'company';
	arr[2] = names;
	arr[3] = ids;
	
	changes = keys.split("_");
	
	if(arr[0]=='company'){
		$("#"+arr[1]).val(names);
		$("#md_company").html(names);
		$("#"+arr[1]+'_id').val(ids);
		var supplier_default = $("#supplier_current").val();
		if($("#cb_current_"+supplier_default).length>0)
		$("#valuereturn_"+supplier_default).html(names);
		$(".k-window").fadeOut();
	
			
	}else if(arr[0]=='supplier'){
		$(".k-window").fadeOut();
		
	}else if(changes[0]=='change'){
		arr[0] =changes[0];
		arr[1] = changes[1];
		$("#valuereturn_"+arr[1]).html(names);
		
		// kiem tra neu la default thi set
		if($("#cb_current_"+arr[1]).attr("checked") == "checked"){
			$("#company").val(names);
			$("#md_company").html(names);
		}
		
		$(".k-window").fadeOut('slow', function() {
			$("#valuereturn_"+arr[1]).html(names);
		});
	};
	
	
}

function after_choose_contacts(ids,names,keys){
	if(keys=='assign'){
		$("#assign_id").val(ids);
		$("#assign").val(names);
	}
	if(keys=='update_price_by'){
		$("#otherpricing_update_price_by_id").val(ids);
		$("#otherpricing_update_price_by").val(names);
	}
	$(".k-window").fadeOut();
}

// Scrollbar
function Scrollbar(divname_scroll){
	$("#" + divname_scroll).mCustomScrollbar({
		scrollButtons:{
			enable:false
		}
	});	
	
}

</script>