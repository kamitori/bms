<script type="text/javascript">
$(function(){
	//Link Sub Tab
	var max_height = 0;
	$(".jt_panel").each(function(){
	    if($(this).height() > max_height)
	        max_height = $(this).height();
	});
	$(".jt_panel").each(function(){
	    if($(this).height() < max_height){
	        var height = max_height - $(this).height();
	        $(".jt_box_label:last",this).css('height',height+24);
	        $(".tab_1_inner",this).each(function(){
	    		var height = max_height - $(this).height();
	        	$(".jt_ppbot:last",this).css('height',height+24);
	    	})
	    } else {
	    	$(".tab_1_inner",this).each(function(){
	    		var height = max_height - $(this).height();
	        	$(".jt_ppbot:last",this).css('height',height+24);
	    	})
	    }
	});
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
			var arr_window = $('html').find(".k-window");
			var dem = 0;
			for(var i =0;i<arr_window.length;i++){
				if(arr_window[i].style.display=='block')
					dem++;
			}
			if(dem == 0)
				search_entry();
		}
	});

	$('#email').change(function(){
		var email =$(this).val();
		if(email=='' || check_format_email(email)){
			return true;
		}else{
			alerts('Message','Email not properly formatted.');
			$(this).val('');
		}
	});

});



// Hàm dùng chung:=========================================
/*
	search_entry()
	check_format_email(email)
	FortmatPrice(values)
	CheckNegative(values,toop)
	ChangeFormatId(str_id)
	ajax_note_set(txt)
	ajax_note(txt)
	get_para_employee()
	get_para_customer_company()
	Scrollbar(divname_scroll)

*/
function search_entry(){
	ajax_note_set("Finding...");
	$(".k-window").fadeOut('slow');
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/search_list',
		type:"POST",
		data: $("#search_form").serialize(),
		success: function(rets){
			if(rets=='0')
				confirms("Message","No found items. Go to entry ?",
					function(){
						window.location.assign("<?php echo URL.'/'.$controller;?>/entry");
					},function(){ ajax_note("");})
			else
				window.location.assign("<?php echo URL.'/'.$controller;?>/"+rets);
		}
	});
}

function check_format_email(email){
	var r = new RegExp("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?");
    return (email.match(r) == null) ? false : true;
}

function FortmatPrice(values){
	values = parseFloat(values);
	values = values.formatMoney(2, '.', ',');
	return values;
}

function CheckNegative(values,toop){
	vs = parseFloat(values);
	if(vs<0)
		$(""+toop).css("color","red");
	else{
		$(""+toop).css("color","");
		$(""+toop).css("color","#000000");
	}
}

Number.prototype.formatMoney = function(c, d, t){
var n = this,
    c = isNaN(c = Math.abs(c)) ? 2 : c,
    d = d == undefined ? "." : d,
    t = t == undefined ? "," : t,
    s = n < 0 ? "-" : "",
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };

function UpCaseFirst(str){
	return str.charAt(0).toUpperCase() + str.slice(1);
}
function ChangeFormatId(str_id){
	var arr = str_id.split("_");
	var retxt='';
	for(var i in arr){
		retxt += UpCaseFirst(arr[i]);
	}
	return retxt;
}


//Hien thong bao truoc khi ajax
function ajax_note_set(txt){
	$(".jt_ajax_note").stop().fadeIn(1);
	$(".jt_ajax_note").css("color","#852020");
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

function get_para_employee(){
	var para = '?is_employee=1';
	return para;
}

function get_para_customer_company(){
	var para = '?is_supplier=1';
	return para;
}

// Scrollbar
function Scrollbar(divname_scroll){
	$("#" + divname_scroll).mCustomScrollbar({
		scrollButtons:{
			enable:false
		},
		advanced:{
	        updateOnContentResize: true,
	        autoScrollOnFocus: false,
	    }
	});

}

</script>