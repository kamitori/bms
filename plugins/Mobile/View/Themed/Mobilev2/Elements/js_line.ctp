<script type="text/javascript">
$(function(){
    $("#list-view").on("change","input,select",function(){
        var ids = $(this).attr("id");
        var inval = $(this).val();
        ids  = ids.split("_");
        var ids = ids[ids.length-1];

        var price_key = new Array("sizew","sizeh","sell_price","area","unit_price","sub_total","taxper","tax","amount","custom_unit_price");
        var pricetext_key = new Array("unit_price","sub_total","tax","amount","adj_qty");
        //khoi tao gia tri luu
        names = $(this).attr("name");

        if(names=='sizew' || names=='sizeh' || names=='sell_price' || names=='area' || names=='sub_total' || names=='taxper' || names=='tax' || names=='amount' || names=='unit_price' || names=='adj_qty' || names=='custom_unit_price')
            inval = UnFortmatPrice(inval);
        var values = new Object();
            values[names]=inval;
        if(names == "products_name"){
            saveOption({opname: "products", data: values, key : ids, controller:'<?php echo $controller; ?>', callBack: function(){}});
            return false;
        }

        if(price_key.indexOf(names) != -1){
            $('#'+names+'_'+ids).val(number_format(inval));
        }else
            $('#'+names+'_'+ids).val(inval);
        if(names=='unit_price'){
            names='custom_unit_price';
            values = new Object();
            values[names]=inval;
        }

        if(names=='custom_unit_price'){
            var unit_price = parseFloat($("#txt_unit_price_"+ids).html());
            if(inval<unit_price){
                var old_value = $(this).attr('rel');
                if(old_value==undefined)
                    old_value = "";
                $(this).val(old_value);
                return false;
            }
            values['is_custom_unit_price'] = 1;
        }

        if($('#'+names+'_'+ids).parent().attr('class')=='combobox'){
            values[names]=$('#'+names+'_'+ids+'Id').val();
        }

        if(names=='sell_by'){
            var newval = $('#sell_by_'+ids+'Id').val();
            if(newval=='unit')
                values['oum'] = 'unit';//set default
            else
                values['oum'] = 'Sq.ft.';//set default
            change_uom_item(newval,ids);
        }
        values['id']=ids;

        if($("#is_printer_"+ids).val()==1 && names!='custom_unit_price'){
            console.log('This is printer');
            cal_price_printing(values,names,function(){
                //reload_subtab('line_entry');
            });
        }else
            cal_line_entry(values,names,function(result){
            result = $.parseJSON(result);
            //=====================================================
            var sum = result.sum;
            //EXTRA ROW============================================
            if(result.last_insert!=undefined){
                //$("#listbox_products_Extra_Row").html(storeExtraRow);
                $("#txt_sub_total_Extra_Row").html(number_format(result.last_insert['sub_total']));
                $("#txt_tax_Extra_Row").html(number_format(result.last_insert['tax'],3));
                $("#txt_amount_Extra_Row").html(number_format(result.last_insert['amount']));
            } else if(result.last_insert==undefined){
                $("#listbox_products_Extra_Row").html("");
            }
            //SUM==================================================
            $('#sum_sub_total').val(number_format(sum.sum_sub_total));
            $('#sum_tax').val(number_format(sum.sum_tax));
            $('#sum_amount').val(number_format(sum.sum_amount));
            //SELF==================================================
            var self = result.self;
            var textval = '';
            for(var i in self){
                if(i=='sizeh' && names!='sizeh'){
                    continue;
                }else if(i=='sell_price' && names=='sell_by'){
                    continue;
                }else if(i=='products_name'){
                    txtval = self[i];
                    if(self[i]!=''){
                        txtval = txtval.split("\n");
                        txtval = txtval[0];
                    }
                    $('#'+i+'_'+ids).val(txtval);
                }else if($('#'+i+'_'+ids).parent().attr('class')=='combobox'){
                    $('#'+i+'_'+ids+'Id').val(self[i]);
                }else if(i=='unit_price'){
                    txtval = parseFloat(self[i]);
                    txtval = number_format(txtval);
                    $('#'+i+'_'+ids).val(txtval);
                    $('#txt_'+i+'_'+ids).html(txtval);
                }else if(i=='custom_unit_price'){
                    txtval = parseFloat(self[i]);
                    txtval = number_format(txtval);
                    $('#'+i+'_'+ids).val(txtval);
                    $('#txt_'+i+'_'+ids).html(txtval);
                }else if(i=='tax'){
                    txtval = parseFloat(self[i]);
                    txtval = number_format(txtval);
                    $('#txt_'+i+'_'+ids).html(txtval);
                }else if(i=='sub_total' || i=='amount' || i=='adj_qty'){
                    txtval = parseFloat(self[i]);
                    txtval = number_format(txtval);
                    $('#txt_'+i+'_'+ids).html(txtval);
                }else if(price_key.indexOf(i) != -1){
                    $('#'+i+'_'+ids).val(number_format(self[i]));
                    if($('#txt_'+i+'_'+ids).prop("tagName")=='SPAN')
                        $('#txt_'+i+'_'+ids).html(number_format(self[i]));
                }else
                    $('#'+i+'_'+ids).val(self[i]);
            }
        });
    });
    $("#add-new-record").click(function(){
        $.ajax({
            type : "POST",
            data : {add : true},
            success : function(result){
                if(result.indexOf("<?php echo M_URL; ?>/") != -1){
                    window.location.assign(result);
                    return false;
                }
                var result = $.parseJSON(result);
                if( !length ){
                    $("#no-data").remove();
                }
                $("#list-view").prepend(getHtml(result));
                $("#list-view").listview().trigger("create");
                $("#list-view").stop().animate({ scrollTop : 0 }, 500);
            }
        })
    });
});
var arrData = <?php if (isset($arr_data)) echo json_encode($arr_data); else echo json_encode(array()); ?>;
var options = <?php if (isset($options)) echo json_encode($options); else echo json_encode(array()); ?>;
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
    $("."+key_click_open).click(function(){
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
    });
}

function number_format(n,c){
    var c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

function loading(text,textVisible,textonly,theme){
    $.mobile.loading( 'show',{
        text: (text!=undefined ? text : 'Loading...'),
        textVisible: (textVisible!=undefined ? textVisible : true),
        textonly: (textonly!=undefined ? textonly : false),
        theme: (theme!=undefined ? theme : 'b'),
    } );
}
function cal_line_entry(data,fieldchange,callBack){
    loading("Saving...");
    $.ajax({
        url : "<?php echo M_URL.'/'.$controller;?>/cal_price_line",
        type:"POST",
        data: {data:JSON.stringify(data),fieldchange:fieldchange},
        success: function(result){
            if (fieldchange=='quantity') location.reload();
            $.mobile.loading( 'hide' );
            if(typeof callBack == "function")
                callBack(result);
        }
    });
}

function cal_price_printing(data,fieldchange,callBack){
    $.ajax({
        url : "<?php echo M_URL.'/'.$controller;?>/cal_price_printing",
        type:"POST",
        data: {data:JSON.stringify(data),fieldchange:fieldchange},
        success: function(result){
            if(typeof callBack == "function")
                callBack();
        }
    });

}

function UnFortmatPrice(number){
    number = number.replace(/,/g,'');
    number = number.replace('.',',');
    return number;
}

function change_uom_item(val,ids){
    var units = '';
    if(val=='unit')
        units = 'Unit';
    else
        units = 'Sq.ft.';
    var old_html = $("#box_edit_oum"+"_"+ids).html();
        old_html = old_html.split("<span");
        old_html = old_html[1].split(">");
        old_html = old_html[1];
        old_html = old_html.replace('value','value="'+units+'" title');
        $("#box_edit_oum"+"_"+ids+" .combobox").remove();
        $("#box_edit_oum"+"_"+ids).prepend(old_html+"  />");

    $.ajax({
        url: '<?php echo M_URL.'/';?>products/select_render',
        dataType: "json", //luu y neu dung json
        type:"POST",
        data: {sell_by:val},
        success: function(jsondata){
            $("#oum"+"_"+ids).combobox(jsondata);
        }
    });
}
function open_product_popup(obj){
    var window_popup = "window_popup_products_line";
    if(!$("#"+window_popup,"body").hasClass("page-content"))
        $.ajax({
            url : "<?php echo M_URL; ?>/products/popup/line",
            async : false,
            success: function(data){
                var html = '<div data-role="page" class="page-content" id="'+window_popup+'" style="border-top:none;">'+data+'</div>';
                $("body").append(html);
                $.mobile.changePage($("#"+window_popup));
            }
        });
    else
        $.mobile.changePage($("#"+window_popup));
    $("#window_popup_products_line").attr("data-id",$(obj).attr("data-id"));
}

function getHtml(data)
{
    var html = "";
    var field = arrData.field;
    for(var i in data){
        var subData = data[i];
        if($("#list-"+subData._id,"#list-view").attr("id")!=undefined) continue;
        html += '<li id="list-'+subData._id+'" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">';
        html += '<h2><div class="ui-block-a" style="width:30%"><a class="open-popup" data-id="'+subData._id+'" onclick="open_product_popup(this);" href="javascript:void(0)">'+subData.sku+'</a></div>';
        html += '<div class="ui-block-b" style="width:60%">'+subData.products_name+'</div></h2>';
        html += '<ul data-role="listview" data-theme="b">';
        for(var j in field){
            html += getField(field[j], j, subData[j], subData._id);
        }
        html += ' <li><a href="#popupDialog" class="callDelete" data-id="'+subData._id+'" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b">Delete</a></li>';
        html += '</ul>';
        html += '</li>';
    }
    return html;
}
function getField(fieldData, key, value, id)
{
    if(value == undefined)
        value = "";
    var html = '<li>';
    html += '<div class="ui-block-a" style="width: 30%"><b>'+fieldData.label+'</b></div>';
    switch(fieldData.type){
        case '':
            html += '<div class="ui-block-b" style="width:70%">'+value+'</div>';
            break;
        case 'text':
            html +=  '<div class="ui-block-b" style="width:70%"><input type="text" name="'+key+'" id="'+key+'_'+id+'" data-theme="a" value="'+value+'"  /></div>';
            break;
        case 'checkbox':
            var check = "";
            if(value == 1)
                check = "checked";
            html += '<div class="ui-block-b ui-checkbox" style="width:70%"><label for="checkbox-enhanced" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left" >Check</label><input name="'+key+'" id="'+key+'_'+id+'" data-enhanced="true" type="checkbox" '+check+'></div>';
            break;
        case 'select':
            var optionsString = fieldData.options;
            if(optionsString.indexOf("@") != -1){
                var tmpOptions = optionsString.split("@");
                var extraKey = tmpOptions[1];
                tmpOptions = tmpOptions[0];
                var curOption = options[tmpOptions];
                curOption = curOption[extraKey];
            }
            else
                var curOption = options[fieldData.options];
            html += '<div class="ui-block-b" style="width:70%"><select name="'+key+'" id="'+key+'_'+id+'">';
            html += '<option value=""></option>';
            for(var i in curOption){
                isSelected = '';
                if( $.trim(i) == $.trim(value) )
                    isSelected = 'selected="selected"';
                html += '<option value="'+i+'" '+isSelected+'>'+curOption[i]+'</option>';
            }
            html += '</select></div>';
            break;
        case 'button':
            html += '<div class="ui-block-b" style="width:70%">';
            html += '<a href="#" id="'+key+'_'+id+'" class="popup-line" data-role="button"  data-theme="a" data-mini="true">'+value+'</a>';
            html += '</div>'
            break;
        default:
            html += '<div class="ui-block-b" style="width:70%">'+value+'</div>';
            break;
    }
    html += '</li>';
    return html;
}
function after_choose_products( product_id, product_name, key){
    if(key == "line"){
        var id = $("#window_popup_products_line").attr("data-id");
        var key = null;
        if(id != undefined)
            key = id;
        var data = {};
        data["choose"] = 1;
        data["deleted"] = "false";
        data["products_id"] = product_id;
        data["products_name"] = product_name;
        var extraField = ["options","company_id"];
        saveOption({opname: "products", extraField : extraField, data: data, key : key, controller:'<?php echo $controller; ?>', callBack: function(result){
                location.reload();
            }
        });
    }
}

function saveOption(object){
    var opname = object.opname;
    var key = object.key;
    var data = object.data;
    var controller = object.controller;
    var callBack = object.callBack;
    if(controller ==undefined || controller =='')
        controller = '<?php echo $controller;?>';
    var dataSend = {controller:controller,opname:opname,key:key,data:data};
    if(object.extraField != undefined)
        dataSend["extra_field"] = object.extraField;
    $.ajax({
        url: '<?php echo M_URL;?>/'+controller+'/save_option',
        type:"POST",
        data: dataSend,
        dataType: "json",
        success: function(result){
            if(typeof callBack == "function")
                callBack(result);
        }
    });
}
</script>