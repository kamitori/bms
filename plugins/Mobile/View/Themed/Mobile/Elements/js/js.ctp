<script type="text/javascript">
$( function(){
	<?php if(!in_array($controller,array('homes','settings'))){ ?>
	getLists();
	getPrevRecord();
	getNextRecord();
	$(".contain").delegate("a.show_cp","click",function(){
		var popup_id = $(this).attr("href");
		var inputHolder = $(this).attr("rel");
		$("#inputHolder",popup_id).val(inputHolder);
	});
	<?php } ?>
});
var header_html = $("#main_header")[0].outerHTML;
var footer_html = $("#main_footer")[0].outerHTML;
function removeHeaderFooter(){
    $("#main_header").remove();
    $("#main_footer").remove();
}
function checkUndefinedHeaderFooter(div_id){
	if(div_id==undefined)
		div_id = "main_page";
	if($("#"+div_id+" > #main_header").attr("id")==undefined
			&&$("#"+div_id+" > #main_footer").attr("id")==undefined)
		return true;
	return false;
}
function triggerCreate(div_id){
	$("#"+div_id).trigger('create');
}
function getLists(){
	var	div_id = "listPage";
	if($("#"+div_id).attr("id")==undefined){
		$(".app").append($('<div data-role="page" class="pages_content pages_reset_a" id="'+div_id+'"></div'));
	}
	var url = "<?php echo M_URL.'/'.$controller.'/lists'; ?>/";
	$("#"+div_id).load(url,function(){
		if(checkUndefinedHeaderFooter("listPage"))
			$("#listPage").prepend(header_html);
		$("#myPanel","#listPage").attr("id","myPanel_listPage");
		$("#openPanel","#listPage").attr("href","#myPanel_listPage");
		$("#listPage").trigger('pagecreate');
    	$.mobile.loading("hide");
    });
}
function getNextRecord(id,div_id){
	if(div_id==undefined)
		div_id = "nextPage";
	if($("#"+div_id).attr("id")==undefined){
		$(".app").append($('<div data-role="page" class="pages_content" id="'+div_id+'"><div class="contain"></div></div'));
	}
	var url = "<?php echo M_URL.'/'.$controller.'/nexts'; ?>/";
	if(id!=undefined){
		url += id;
	}
	$("#"+div_id+" > .contain").load(url,function(){
        if(checkUndefinedHeaderFooter(div_id)){
        	$("#"+div_id).prepend(header_html).append(footer_html);
			$("#myPanel","#"+div_id).attr("id","myPanel_"+div_id);
			$("#openPanel","#"+div_id).attr("href","#myPanel_"+div_id);
		}
        $("#"+div_id).trigger("pagecreate");
    	$.mobile.loading("hide");
    });
}
function getPrevRecord(id,div_id){
	if(div_id==undefined)
		div_id = "prevPage";
	if($("#"+div_id).attr("id")==undefined){
		$(".app").append($('<div data-role="page" class="pages_content" id="'+div_id+'"><div class="contain"></div></div'));
	}
	var url = "<?php echo M_URL.'/'.$controller.'/prevs'; ?>/";
	if(id!=undefined){
		url += id;
	}
	$("#"+div_id+" .contain").load(url,function(){
        // triggerCreate(div_id);
        if(checkUndefinedHeaderFooter(div_id)){
        	$("#"+div_id).prepend(header_html).append(footer_html);
			$("#myPanel","#"+div_id).attr("id","myPanel_"+div_id);
			$("#openPanel","#"+div_id).attr("href","#myPanel_"+div_id);
		}
        $("#"+div_id).trigger("pagecreate");
    	$.mobile.loading("hide");
    });
}
$( document ).on( "swipeleft", "#main_page", function( event ) {
	if($("#nextPage > .contain").html().trim()==''){
		return false;
	}
	console.log("swipeleft");
    $.mobile.changePage("#nextPage",{
        // transition: "slide",
        changeHash: false,
    });
    $("#main_page").attr("id","tmp_prevPage");
	$("#nextPage").attr({"id":"main_page","data-url":"main_page"});
    triggerCreate('main_page');
    $("#prevPage").attr({"id":"nextPage","data-url":"nextPage"});
    $("#tmp_prevPage").attr({"id":"prevPage","data-url":"prevPage"});
    var id = $("#mongoid","#main_page").val();
    loading();
    getNextRecord(id,"nextPage");
});
$( document ).on( "swiperight", "#main_page", function( event ) {
	if($("#prevPage > .contain").html().trim()==''){
		return false;
	}
    $.mobile.changePage("#prevPage",{
        // transition: "slide",
        // reverse: true,
        changeHash: false,
    });
    $("#main_page").attr("id","tmp_nextPage");
    $("#prevPage").attr({"id":"main_page","data-url":"main_page"});
    triggerCreate('main_page');
    $("#nextPage").attr({"id":"prevPage","data-url":"prevPage"});
    $("#tmp_nextPage").attr({"id":"nextPage","data-url":"nextPage"});
    var id = $("#mongoid","#main_page").val();
    loading();
    getPrevRecord(id,"prevPage");
});
function loading(text,textVisible,textonly,theme){
	$.mobile.loading( 'show',{
        text: (text!=undefined ? text : 'Loading...'),
        textVisible: (textVisible!=undefined ? textVisible : true),
        textonly: (textonly!=undefined ? textonly : false),
        theme: (theme!=undefined ? theme : 'b'),
    } );
}
function window_popup(controller, title, key, key_click_open, parameter_get, force_re_install){

	// ---- set default ----
	if($("#"+key).attr("id")!=undefined)
		return false;
	if( key == undefined ){
		key = "";
	}

	if( parameter_get == undefined ){
		parameter_get = "";
	}

	if( force_re_install == undefined ){
		force_re_install = "";
	}

	var div_popup_id = controller + key;

	if( controller == undefined ){
		alert("You must set a controller for function: window_popup(controller, title, key) . Thanks.");
	}

	if( title == undefined ){
		alert("You must set a title for function: window_popup(controller, title, key) . Thanks.");
	}

	var window_popup = $("#window_popup_" + div_popup_id);


	var undo = $("#click_open_window_" + div_popup_id);


	// refesh lại kendo window để hiển thị các giá trị chọn mới
	if( force_re_install == "force_re_install" ){

		if( window_popup.attr("id") == undefined || $.trim(window_popup.html()) == "" ){
			console.log("Bạn chưa khai báo window popup '" + "#window_popup_" + div_popup_id + "', vui lòng kiểm tra lại code, thanks.");
		}else{
			$.ajax({
				url : "<?php echo M_URL; ?>/"+ controller +"/popup/" + key + parameter_get,
				success: function(data){
					$("#"+key).html(data);
				}
			});
		    return true;
		}

	}
	$.ajax({
		url : "<?php echo M_URL; ?>/"+ controller +"/popup/" + key + parameter_get,
		success: function(data){
			var html = '<div data-role="page" class="pages_content" id="'+key+'" style="border-top:none;">'+data+'</div>';
			$(".app").append(html);
		}
	});
}
function confirmAndDelete( object ) {
	var listitem = $(object).parent().parent();
    // Highlight the list item that will be removed
    listitem.addClass("active_td");
    // Show the confirmation popup
    $( "#confirm" ).popup( "open" );
    // Proceed when the user confirms
    $( "#confirm #yes" ).on( "click", function() {
    	var id = listitem.attr("id").replace("<?php echo $controller; ?>_","");
    	$.ajax({
    		url: "<?php echo M_URL; ?>/<?php echo $controller ?>/delete/"+id,
    		success: function(result){
    			if(result=='ok')
        			listitem.remove();
    		}
    	})
    });
    // Remove active state and unbind when the cancel button is clicked
    $( "#confirm #cancel" ).on( "click", function() {
        listitem.removeClass("active_td");
        $( "#confirm #yes" ).off();
    });
}
function changeIDValue(){
	$("select","#main_page").each(function(){
        var id = $(this).attr("id");
        var value = $(this).find('option:selected').text();
        var hidden_value = $(this).find('option:selected').val();
        $(this).find('option:selected').val(value);
        $("#"+id+"Id","#main_page").val(hidden_value);
    });
}
</script>