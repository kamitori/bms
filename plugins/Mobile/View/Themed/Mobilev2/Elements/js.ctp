<script type="text/javascript">
$(function(){
	input_calendar(".date-picker");
    input_popup();
});
function input_popup(){
    $(".popup-input").each(function(){
        var parent = $(this).parent();
        parent.addClass("ui-input-has-clear");
        if(!$(".choose-popup",parent).hasClass("choose-popup")){
            var controller = $(this).attr("data-popup-controller");
            var popupKey = $(this).attr("data-popup-key");
            var param = $(this).attr("data-popup-param");
            $(this).after('<a href="#" id="'+controller+popupKey+"Clicker"+'" class="choose-popup ui-input-clear ui-btn ui-icon-bars ui-btn-icon-notext" title="Click to choose">Choose</a>');
            window_popup(controller, popupKey,controller+popupKey+"Clicker",param);
        }
    });
}
function window_popup(controller, key, key_click_open, parameter_get, force_re_install){
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
	var window_popup = "window_popup_" + controller+"_"+key;
	$("#"+key_click_open).click(function(){
		if(!$("#"+window_popup,"body").hasClass("page-content"))
			$.ajax({
				url : "<?php echo M_URL; ?>/"+ controller +"/popup/" + key + parameter_get,
				success: function(data){
					var html = '<div data-role="page" class="page-content" id="'+window_popup+'" style="border-top:none;">'+data+'</div>';
					$("body").append(html);
					$.mobile.changePage($("#"+window_popup));
				}
			});
		else
			$.mobile.changePage($("#"+window_popup));
	})
}
function backToMain(){
	$.mobile.changePage($("#main-page").parent());
}
function loading(text,textVisible,textonly,theme){
	$.mobile.loading( 'show',{
        text: (text!=undefined ? text : 'Loading...'),
        textVisible: (textVisible!=undefined ? textVisible : true),
        textonly: (textonly!=undefined ? textonly : false),
        theme: (theme!=undefined ? theme : 'b'),
    } );
}
function input_calendar(class_selector, contain) {
    var date_format = "d M, yy";
    if (contain != undefined) {
        $(class_selector, contain).each(function() {
            var date_default = $(this).val();
            if ($.trim(date_default).length == 10 || $.trim(date_default).length == 0) {
                // $( this ).datepicker();
                $(this).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "c-70:c+3"
                });
                if ($.trim(date_default) != "") {
                    $(this).datepicker("setDate", date_default);
                }
                $(this).datepicker("option", "showAnim", "slideDown");
                $(this).datepicker("option", "dateFormat", date_format);
            }

        });
    } else {
        $(class_selector).each(function() {
            var date_default = $(this).val();
            if ($.trim(date_default).length == 10 || $.trim(date_default).length == 0) {
                $(this).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "c-70:c+3"
                });
                if ($.trim(date_default) != "") {
                    $(this).datepicker("setDate", date_default);
                }
                $(this).datepicker("option", "showAnim", "slideDown");
                $(this).datepicker("option", "dateFormat", date_format);

            }

        });
    }
}
function save_option(opname,arr_value,opid,isreload,subtab,keys,handleData,fieldchage,module_id){
    if(opname != undefined ){
        if(keys == undefined  || keys == '')
            keys  = 'update';
        var arr = {
                'keys' : keys,
                'opname' : opname,
                'value_object' : arr_value,
                'opid' : opid
            };
        var jsonString = JSON.stringify(arr);
        if(fieldchage == undefined )
            fieldchage = '';
        if(module_id == undefined )
            module_id = '';
        var url = '<?php echo URL.'/'.$controller;?>/save_option';

        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/save_option',
            type:"POST",
            data: {arr:jsonString,fieldchage:fieldchage,mongo_id:module_id},
            success: function(rtu){
                $.mobile.loading( 'hide' );//ajax_note("Saving ... Saved.");
                if(handleData!=undefined)
                    handleData(rtu);
            }
        });
    }else return '';
}
</script>