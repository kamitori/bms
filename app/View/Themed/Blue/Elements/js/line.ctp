<input type="hidden" id="click_open_window_products_option_popup" />
<script type="text/javascript">
$(function(){
	window_popup('products', 'Specify Current product','choice_option','click_open_window_products_option_popup','?products_product_type=Product','no_auto_close');
	$(".options_popup").click(function(){
		var url = $(this).attr("rel");
		var product_key = $(this).parents("ul").attr("id");
		product_key = product_key.split("_");
		product_key = product_key[product_key.length - 1];
		var key = "<?php echo $controller; ?>_"+$("#mongo_id").val()+"_"+product_key;
		if( $("#option_popup_window" ).attr("id") == undefined )
			$('<div id="option_popup_window" style="display:none; min-width:300px;"></div>').appendTo("body");
		var loading = '<span style="padding: 19% 0 0 50%;float: left;font-size: xx-large;" >Loading...</span>';
		$("#option_popup_window").html(loading);
		option_window = $("#option_popup_window");
        option_window.kendoWindow({
	    	iframe: false,
	        actions: ["Close"],
	        content: url,
	        visible: false,
	        resizable: false,
	        draggable: false,
	        width: "auto",
			title: 'Option List',
			pinned: true,
			close: function(event) {
				$("body").addClass("none_overflow");
				if(localStorage['<?php echo $controller; ?>_costing_submit'] == undefined
				   	|| localStorage['<?php echo $controller; ?>_costing_submit']==0){
					confirms("Message","Are you sure to close without save?",function(){
						$(".jt_ajax_note").css("top","120px");
						reload_subtab("line_entry");
						var temporary_products = $(".temporary_products");
						if(temporary_products != ""){
							var Obj = {};
							var html = '';
							for(var i=0; i < $(".temporary_products").length; i++){
								var tmp = $(".temporary_products")[i].outerHTML;
								html += tmp.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,"")+"\n";
							}
							Obj['lengths'] = $(".temporary_products").length;
							Obj['html'] = html;
							temporary_products = JSON.stringify(Obj);
							localStorage.setItem(key,temporary_products);
						}
						$("#option_popup_window").data("kendoWindow").destroy();
						$("body").removeClass("none_overflow");
					},function(){
						return false;
					});
					event.preventDefault();
				}
				localStorage.setItem("<?php echo $controller; ?>_costing_submit",0);
				$("body").removeClass("none_overflow");
            }
	    }).data("kendoWindow").open();
	    if($("#option_popup_window_buttons").attr("id")==undefined){
	    	var html = '<ul id="option_popup_window_buttons" class="menu_control float_right" style="margin-right: 50px; margin-bottom: 5px;">';
			html +=	    '<li style="position: absolute;margin-top: 45px;left: 83%;background-color: #003256;">';
			html +=          '<a href="javascript:void(0)" id="submit_form_<?php echo $controller;?>_option" >Submit for Line entry</a>';
			html +=	    '</li>';
			/*html +=	    '<li style="position: absolute;margin-top: 45px;left: 70%;background-color: #003256;">';
			html +=		     '<a style=" cursor:pointer;" id="entry_menu_save_custom_costing" onclick="save_custom_product();" >&nbspSave Custom Product&nbsp;</a>';
			html +=		'</li>';*/
			html += '</ul>';
			$("#option_popup_window_wnd_title").after(html);
	    }
	    $("#submit_form_<?php echo $controller;?>_option").unbind("click");
	    $("#submit_form_<?php echo $controller;?>_option").click(function(){
			<?php if($controller == 'salesorders' || IS_LOCAL){ ?>
	    	localStorage.removeItem(key);
			<?php } ?>
	    	$.ajax({
	    		url: url,
	    		type: "POST",
	    		data: $(".form_<?php echo $controller;?>_option").serialize(),
	    		success: function(result){
	    			localStorage.setItem("<?php echo $controller; ?>_costing_submit",0);
	    			if(result!='ok')
	    				alerts('Message',result);
	    			else{
	    				localStorage.setItem("<?php echo $controller; ?>_costing_submit",1);
	    				reload_subtab("line_entry");
	    				$("#option_popup_window").data("kendoWindow").close();
	    			}
					$(".jt_ajax_note").css("top","120px");
					$("body").removeClass("none_overflow");
	    		}
	    	});
	    });
	    option_window.data("kendoWindow").maximize();
	});
	$(".costings_popup").click(function(){
		var url = $(this).attr("rel");
		var product_key = $(this).parent().parent().attr("id");
		product_key = product_key.split("_");
		product_key = product_key[product_key.length - 1];
		var key = "<?php echo $controller; ?>_"+$("#mongo_id").val()+"_"+product_key;
		if( $("#costing_popup_window" ).attr("id") == undefined )
			$('<div id="costing_popup_window" style="display:none; min-width:300px;"></div>').appendTo("body");
		var loading = '<span style="padding: 19% 0 0 50%;float: left;font-size: xx-large;" >Loading...</span>';
		$("#costing_popup_window").html(loading);
		costing_window = $("#costing_popup_window");
        costing_window.kendoWindow({
	    	iframe: false,
	        actions: ["Close"],
	        content: url,
	        visible: false,
	        resizable: false,
	        draggable: false,
	        width: "auto",
			title: 'Making for this item',
			pinned: false,
			close: function(event) {
				$("body").addClass("none_overflow");
				if(localStorage['<?php echo $controller; ?>_costing_submit'] == undefined
				   	|| localStorage['<?php echo $controller; ?>_costing_submit']==0){
					confirms("Message","Are you sure to close without save?",function(){
						$(".jt_ajax_note").css("top","120px");
						reload_subtab("line_entry");
						var temporary_products = $(".temporary_products");
						if(temporary_products != ""){
							var Obj = {};
							var html = '';
							for(var i=0; i < $(".temporary_products").length; i++){
								var tmp = $(".temporary_products")[i].outerHTML;
								html += tmp.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,"")+"\n";
							}
							Obj['lengths'] = $(".temporary_products").length;
							Obj['html'] = html;
							temporary_products = JSON.stringify(Obj);
							localStorage.setItem(key,temporary_products);
						}
						$("#costing_popup_window").data("kendoWindow").destroy();
						$("body").removeClass("none_overflow");
					},function(){
						return false;
					});
					event.preventDefault();
				}
				localStorage.setItem("<?php echo $controller; ?>_costing_submit",0);
            }
	    }).data("kendoWindow").open();
	    if($("#costing_popup_window_buttons").attr("id")==undefined){
	    	var html = '<ul id="costing_popup_window_buttons" class="menu_control float_right" style="margin-right: 50px; margin-bottom: 5px;">';
			html +=	    '<li style="position: absolute;margin-top: 45px;left: 70%;background-color: #003256;">';
			html +=          '<a href="javascript:void(0)" id="submit_form_<?php echo $controller;?>_costing" >Submit for Line entry</a>';
			html +=	    '</li>';
			html +=	    '<li style="position: absolute;margin-top: 45px;left: 83%;background-color: #003256;">';
			html +=		     '<a style=" cursor:pointer;" id="entry_menu_save_custom_costing" onclick="save_custom_product();" >&nbspSave Custom Product&nbsp;</a>';
			html +=		'</li>';
			html += '</ul>';
			$("#costing_popup_window_wnd_title").after(html);
	    }
	    $("#submit_form_<?php echo $controller;?>_costing").unbind("click");
	    $("#submit_form_<?php echo $controller;?>_costing").click(function(){
	    	localStorage.removeItem(key);
	    	$.ajax({
	    		url: url,
	    		type: "POST",
	    		data: $(".form_<?php echo $controller;?>_costing").serialize(),
	    		success: function(result){
	    			localStorage.setItem("<?php echo $controller; ?>_costing_submit",0);
	    			if(result!='ok')
	    				alerts('Message',result);
	    			else{
	    				localStorage.setItem("<?php echo $controller; ?>_costing_submit",1);
	    				reload_subtab("line_entry");
						$("#costing_popup_window").data("kendoWindow").close();
	    			}
					$(".jt_ajax_note").css("top","120px");
	    		}
	    	});
	    });
	    costing_window.data("kendoWindow").maximize();
	});
});
function submit_alert(suffix){
	if(suffix == undefined)
		suffix = "option";
	var submit_alert = setInterval(function(){
		if($("#submit_form_<?php echo $controller; ?>_"+suffix).parent().parent().parent().parent().css("display") == undefined
			|| localStorage["<?php echo $controller; ?>_costing_submit"] == 1){
			clearInterval(submit_alert);
			$( "#submit_form_<?php echo $controller;?>_"+suffix ).css({"background-color":"#d7d7d7","color":"#5f5f5f" });
			return;
		}
		$( "#submit_form_<?php echo $controller;?>_"+suffix ).animate({
          backgroundColor: "#d7d7d7",
          color: "#5f5f5f",
        }, 500 );
		ajax_note('<span class="bold">Please save change before exit!</span>');
		setTimeout(function(){
			$( "#submit_form_<?php echo $controller;?>_"+suffix ).animate({
	          backgroundColor: "#852020",
	          color: "#fff",
	        }, 1000 );
		},1000);
	},2000);
}
function reset_bg(boxname){
	var sum = $("#container_"+boxname+" .ul_mag").length;
	sum = parseInt(sum);
	var strs='';var lengs = 0; var newbg ='';
	for(var i=0;i<=sum+1;i++){
		$("#container_"+boxname+" .ul_mag:eq("+i+")").removeClass('bg1').removeClass('bg2');
		$("#container_"+boxname+" .ul_mag:eq("+i+")").addClass(i%2==0 ? 'bg2' : 'bg1');
	}
}
</script>