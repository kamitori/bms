/*window.onbeforeunload = function (event) {
    var message = 'Important: Your data may be lost if you close or reload abruptly.';
    if (typeof event == 'undefined') {
        event = window.event;
    }
    if (event) {
        event.returnValue = message;
    }
    return message;
};

$(function () {
    $("a").not('#lnkLogOut').click(function () {
        window.onbeforeunload = null;
    });
});*/


$(function() {
    // SETUP DIALOG
    input_show_select_calendar(".JtSelectDate");

});

function refesh_ul_color(div) {
    var i = 1;
    $(div + " > ul").each(function() {
        $(this).removeClass("bg" + (3 - i)).addClass("bg" + i);
        i = 3 - i;
    });
    return true;
}

function noteactivity_update(object) {
    $.ajax({
        url: '/homes/noteactivity_update/' + $(object).attr("rel"),
        type: 'POST',
        data: {
            content: $(object).val()
        },
        success: function(html) {
            if (html != "ok") {
                alerts("Errors: ", html);
            }
        }
    });
}

function noteactivity_delete(id) {
    confirms("Message", "Are you sure you want to delete?",
        function() {
            $.ajax({
                url: '/homes/noteactivity_delete/' + id,
                success: function(html) {
                    if (html == "ok") {
                        $("#noteactivity_" + id).remove();
                    }
                }
            });
        }, function() {
            console.log("Cancel");
            return false;
        }
    );
}

// use in force_refesh.ctp
function force_refesh_if_click_back_button() {
    // check back button, we must reload because ajax save
    setTimeout(function() {
        var e = document.getElementById("refreshed");
        if (e.value == "no") e.value = "yes";
        else {
            e.value = "no";
            location.reload();
        }
    }, 1000);
}
// bind action click to input and show calendar when user focus
function input_show_select_calendar(class_selector, contain) {
    switch(localStorage.getItem('format_date')){
        case "d M, Y":
            date_format = "d M, yy";
            break;
        case "d-m-Y":
            date_format = "dd-mm-yy";
            break;
        case "d/m/Y":
            date_format = "dd/mm/yy";
            break;
        default:
            date_format = "d M, yy";
            break;
    }
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
    }
}

function show_warning_function_in_progress_develop(object_this) {
    $("#function_in_progress_develop").fadeOut(function() {
        $("#function_in_progress_develop").html("<br/>" + $(object_this).text() + " is being developed!!!").show();
    });
    setTimeout('$("#function_in_progress_develop").fadeOut()', 1800);

}


/*function confirms(title, message, yesCallback, noCallback,ButtonListArr){
	//cutom Option value
	buttons = '<input type="button" class="jt_confirms_window_cancel" value="Cancel" id="confirms_cancel" />';
	buttons += '<input type="button" class="jt_confirms_window_cancel" value="Ok" id="confirms_ok" />';

	if(ButtonListArr!=undefined)
	{
		if(ButtonListArr[0]=='')
			ButtonListArr[0] = 'Cancel';
		buttons = '<input type="button" class="jt_confirms_window_cancel" value=" '+ButtonListArr[0]+' " id="confirms_cancel" />';
		if(ButtonListArr[1]=='')
			ButtonListArr[1]='Ok';
		buttons += '<input type="button" class="jt_confirms_window_ok" value=" '+ButtonListArr[1]+' " id="confirms_ok" />';
	}

	if( $("#confirms_window").attr("id") == undefined ){
			$('<div id="confirms_window"><span class="jt_confirms_window_msg"></span>'+buttons+'</div>').appendTo("body");
			console.log('new');
	}
	var confirms_window = $("#confirms_window");
	confirms_window.kendoWindow({
		width: "400px",
		height: "150px",
		title: title,
		visible: false,
		activate: function(){
			$('#confirms_cancel').focus();
		}
	});

	//setup message
	if(message!=undefined)
		$(".jt_confirms_window_msg").html(message);
	else
		$(".jt_confirms_window_msg").html('Do you want to do this?');

	//show popup
	confirms_window.data("kendoWindow").center();
	confirms_window.data("kendoWindow").open();
	$('#confirms_ok').click(function() {
	   confirms_window.data("kendoWindow").destroy();
	   yesCallback();
	});
	$('#confirms_cancel').click(function() {
	   confirms_window.data("kendoWindow").destroy();
	   noCallback();
	});
}*/


function confirms(title, message, yesCallback, noCallback) {
    //cutom Option value
    if ($("#confirms_window").attr("id") == undefined) {
        $('<div id="confirms_window"><span class="jt_confirms_window_msg"></span><input type="button" class="jt_confirms_window_cancel" id="confirms_cancel" value=" Cancel " /><input type="button" class="jt_confirms_window_ok" value=" Ok " id="confirms_ok" /></div>').appendTo("body");
        console.log('new');
    }
    var confirms_window = $("#confirms_window");
    confirms_window.kendoWindow({
        width: "400px",
        height: "150px",
        title: title,
        visible: false,
        activate: function() {
            $('#confirms_ok').select();
        },
        open: function() {
            this.wrapper.css({ top: 200 });
        }
    });

    //setup message
    if (message != undefined)
        $(".jt_confirms_window_msg").html(message);
    else
        $(".jt_confirms_window_msg").html('Do you want to do this?');

    //show popup
    confirms_window.data("kendoWindow").center();
    confirms_window.data("kendoWindow").open();
    $('#confirms_ok').click(function() {
        yesCallback();
        confirms_window.data("kendoWindow").destroy();
    });
    $('#confirms_cancel').click(function() {
        noCallback();
        confirms_window.data("kendoWindow").destroy();
    });
}

function popup_show(title, html) {
    if ($("#alerts_right_footer_window").attr("id") == undefined) {
        $('<div id="alerts_right_footer_window">' + html + '</div>').appendTo("body");
        var alerts_right_footer_window = $("#alerts_right_footer_window");

        alerts_right_footer_window.kendoWindow({
            width: "auto",
            title: title,
            visible: true,
            activate: function() {
                // $('#confirms_cancel').focus();
            }
        }).data("kendoWindow").center();

    } else {
        $("#alerts_right_footer_window").html(html).data("kendoWindow").open();
        $("#alerts_right_footer_window_wnd_title").html(title);
    }

}


function alerts(title, message, myCallback) {
    //cutom Option value
    if ($("#confirms_window").attr("id") == undefined) {
        $('<div id="confirms_window"><span class="jt_confirms_window_msg"></span><input type="button" class="jt_confirms_window_ok" value=" Ok " id="alerts_ok" /></div>').appendTo("body");
    }
    var confirms_window = $("#confirms_window");
    confirms_window.kendoWindow({
        width: "400px",
        height: "150px",
        title: title,
        visible: false,
        activate: function() {
            $('#alerts_ok').focus();
        },
        open: function() {
            this.wrapper.css({ top: 200 });
        }
    });

    //setup message
    if (message != undefined)
        $(".jt_confirms_window_msg").html(message);
    else
        $(".jt_confirms_window_msg").html('Do you want to do this?');

    //show popup
    confirms_window.data("kendoWindow").center();
    confirms_window.data("kendoWindow").open();
    $('#alerts_ok').click(function() {
        confirms_window.data("kendoWindow").close();
        confirms_window.data("kendoWindow").destroy();

        if (typeof myCallback == 'function') {
            myCallback();
        }
    });


}


function confirms3(title, message, ButtonListArr, Callback1, Callback2, Callback3, noCallback) {
    //cutom Option value
    if ($("#confirms_window").attr("id") == undefined) {
        var buttons = '';
        for (var i = 0; i < 3; i++) {
            if (ButtonListArr[i] != '')
                buttons += '<input type="button" class="jt_confirms_window_ok" value=" ' + ButtonListArr[i] + ' " id="confirms_ok' + i + '" />';
        }

        $('<div id="confirms_window"><span class="jt_confirms_window_msg"></span><input type="button" class="jt_confirms_window_cancel" id="confirms_cancel" value=" Cancel " />' + buttons + '</div>').appendTo("body");
    }
    var confirms_window = $("#confirms_window");
    confirms_window.kendoWindow({
        width: "400px",
        height: "160px",
        title: title,
        visible: false,
        activate: function() {
            $('#confirms_ok0').focus();
        },
        open: function() {
            this.wrapper.css({ top: 200 });
        }
    });

    //setup message
    if (message != undefined)
        $(".jt_confirms_window_msg").html(message);
    else
        $(".jt_confirms_window_msg").html('Do you want to do this?');

    //show popup
    confirms_window.data("kendoWindow").center();
    confirms_window.data("kendoWindow").open();
    $('#confirms_ok0').click(function() {
        confirms_window.data("kendoWindow").destroy();
        Callback1();
    });
    $('#confirms_ok1').click(function() {
        confirms_window.data("kendoWindow").destroy();
        Callback2();
    });
    $('#confirms_ok2').click(function() {
        confirms_window.data("kendoWindow").destroy();
        Callback3();
    });
    $('#confirms_cancel').click(function() {
        confirms_window.data("kendoWindow").destroy();
        noCallback();
    });
}

function jt_link_module(object, msg, link_add) {
    var a = $(object).attr('href');
    if (a != "javascript:void(0)") {
        window.location = a;
        // window.open(a);
    } else {
        confirms("Message", msg,
            function() {
                window.location = link_add;
                // window.open(link_add);
            }, function() {
                //else do somthing
            });
    }
}


/**
 * Check key for phone when typing
 * @author vu.nguyen
 * @since v1.0
 * @event onkeypress
 * @using <input name="phone" id="phone" onkeypress="return isPhone(event);" />
 */
function isPhone(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    console.log(charCode);
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 45 && charCode != 46 && charCode != 40 && charCode != 41 && charCode != 35 && charCode != 32)
        return false;
    return true;
}


/**
 * Check key for Price type when typing
 * @author vu.nguyen
 * @since v1.0
 * @event onkeypress
 * @using <input name="price" id="price" onkeypress="return isPrice(event);" />
 */
function isPrice(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if(charCode==45)
        return true
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 44 && charCode != 46)
        return false;
    return true;
}

function isCode(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if(charCode==45)
        return true
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 44 && charCode != 46)
        return false;
    return true;
}

/**
 * Check key for Price type when typing
 * @author vu.nguyen
 * @since v1.0
 * @event onkeypress
 * @using <input name="price" id="price" onkeypress="return isNumbers(event);" />
 */
function isNumbers(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    // console.log(charCode);
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46 && charCode != 45)
        return false;
    return true;
}



//Hien thong bao truoc khi ajax
function ajax_note_set(txt) {
    $(".jt_ajax_note").stop().fadeIn(1);
    $(".jt_ajax_note").css("color", "#852020");
    $(".jt_ajax_note").html(txt);
}
//Hien thong bao sau khi ajax thanh cong
function ajax_note(txt) {
    $(".jt_ajax_note").stop().fadeIn(100, function() {
        $(".jt_ajax_note").html(txt);
        $(".jt_ajax_note").delay(2000).fadeOut(100);
        /*$(".jt_ajax_note").fadeOut(1500, function() {
			$(".jt_ajax_note").html("");
			$(".jt_ajax_note").fadeIn(100);
		});*/
    });
}

function mainjs_entry_search_ajax(controller) {
    var check_window_select_popup = false;
    $(".k-window-content").each(function() {
        if ($(this).is(":visible")) {
            check_window_select_popup = true;
        }
    });
    if (!check_window_select_popup) {
        $.ajax({
            // url: "/" + controller + "/entry_search",
            timeout: 15000,
            type: "post",
            data: $(":input", "#" + controller + "_entry_search").serialize(),
            success: function(html) {
                html = $.trim(html);
                if (html.indexOf('yes_1_') != -1) {
                    window.location = "/" + controller + "/entry/" + html.substring(6);
                } else if (html == "yes") {
                    window.location = "/" + controller + "/lists";
                } else if (html == "no") {
                    alerts("Error: ", "There are no records that match with your find criteria");
                } else if (html != "ok") {
                    alerts("Error: ", html);
                }
                console.log(html);
            }
        });
    }
}

// --------------------- PAGINATION ---------  dung trong js/lists_view.ctp:  -------------
// kiem tra bat su kien sort
function mainjs_pagination_sort() {
    $("#sort li").click(function() {
        // phat hien su kien click li
        var span = $("span", this);
        // sort_key la ten cua field trong db
        sort_key = span.attr('id');
        // sort_type la desc hay asc
        sort_type = span.attr('class');

        // kiem tra no la desc hay asc de gan class tuong ung
        if ($.trim(sort_type) == 'desc') {
            span.attr('class', 'asc');
            mainjs_pagination_ajax_sort(sort_key, sort_type);
        }
        if ($.trim(sort_type) == 'asc') {
            span.attr('class', 'desc');
            mainjs_pagination_ajax_sort(sort_key, sort_type);
        }
        // reset tat ca cac class khac ve desc , ngoai tru class dang chon
        $('#sort span').each(function() {
            id_reset_class = $(this).attr('id');
            if (id_reset_class !== sort_key) {
                $(this).attr('class', 'desc');
            }
        });
    });
}
// xu ly sort
function mainjs_pagination_ajax_sort(sort_key, sort_type) {
    $("#pagination_sort_field").val(sort_key);
    $("#pagination_sort_type").val(sort_type);
    $("#num").val(1);
    $.ajax({
        type: 'POST',
        data: $('#sort_form').serialize(),
        success: function(data) {
            $("#lists_view_content").html(data);
        }
    });
}

// dung trong cac file popup.ctp bung window
function window_popup_extra(contain) {
    $(".container_same_category", contain).mCustomScrollbar({
        scrollButtons: {
            enable: false
        }
    });
}


// BaoNam: GET PARA CONTACT
function get_para_contact() {
    var para = '?is_customer=1';
    var company_id = $("#company_id").val();
    var company_name = $("#company_name").val();
    if (company_id != '')
        para += '&company_id=' + company_id;
    if (company_name != '')
        para += '&company_name=' + company_name;
    return para;
}

var focused_selector;
function jobtraq_loading(onof){
    var w,h;
    h = $( window  ).height();
    w = $( document ).width();
    $("#jobtraq_loading").css({'width':w,'height':h});
    $("#jobtraq_loading > img").css({'margin-top':(h / 2 )});
    if(onof=='off'){
        if(focused_selector != undefined){
            $(focused_selector).focus();
        }
        $("#jobtraq_loading").hide();
    }else{
        focused_selector = $("input[type=text]:focus");
        $("#jobtraq_loading").show();
    }
}