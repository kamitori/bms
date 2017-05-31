<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
    #list-view{
        background-color: #fff;
        border-left: solid 1px rgba(0,0,0,.15);
    }
    .popup-line{
        background-color: #38c !important;
        color: #fff !important;
    }
    .ui-block-b > span{
        color: #fff;
        font-weight: 900;
        font-size: 14px;
    }
    .parent-line{
        margin-top: 5px !important;
        border-left: none !important;
        border-top: solid 2px rgba(0,0,0,.15) !important;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true">
<?php
    if(!empty($arr_allocated)){
        foreach ($arr_allocated as $key => $value){
?>
    <li id="list" class="list-line-item ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="open-popup" href="javascript:void(0)"><?php echo (isset($value['salesinvoice_code']) ? $value['salesinvoice_code'] : '').'&nbsp;&nbsp;&nbsp;'; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo isset($value['note'])?$value['note']:''; ?></div>
            <div class="ui-block-b" style="width:10%"><?php echo $value['amount'];?></div>
        </h2>

        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width:30%"><b>Invoice no</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo isset($value['salesinvoice_code']) ? $value['salesinvoice_code'] : ''; ?>   
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Reference</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="note" class="note" id="note" index="<?php echo $key; ?>" data-theme="a" value="<?php echo isset($value['note']) ? $value['note'] : ''; ?>" />
                </div>
            </li>
           
            <li>
                <div class="ui-block-a" style="width:30%"><b>Amount</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="amount" class="amount" id="amount" index="<?php echo $key; ?>" data-theme="a" value="<?php echo isset($value['amount']) ? $value['amount'] : ''; ?>"/>
                </div>
            </li>


            <li>
                <a href="#popupDialog" class="callDelete" data-id="<?php echo $value['salesinvoice_code']; ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b">Delete</a>
            </li>

        </ul>
    </li>
<?php } ?>
</ul>
<br />
<?php
    } 
?>

<div data-role="popup" id="popupDialog" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="max-width:300px;">
    <div role="main" class="ui-content">
        <h2 class="ui-title">Are you sure you want to delete this record?</h2>
    <p>This action cannot be undone.</p>
        <input type="hidden" id="hiddenId" value="" />
        <a href="#" id="cancel_button" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back">Cancel</a>
        <a href="#" id="delete_button" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back" data-transition="flow">Delete</a>
    </div>
</div>

<?php //echo $this->element('js_line'); ?>
<script type="text/javascript">
$(function(){
    $("#list-view").on("click",".callDelete",function(){
        var value = $(this).attr("data-id");
        $("#hiddenId").val(value);
    });
    $("#delete_button").click(function(){
        var ids = $("#hiddenId").val();
        var data = {};
        data['deleted'] = true;
        saveOption({opname: "products", data: data, key : ids, controller:'<?php echo $controller ?>', callBack: function(result){
                $("#list-"+ids).remove();
                $(".list-line-item[data-option-for='"+ids+"']").remove();
                $("#sum-amount").text(number_format(result.sum_amount,2));
                $("#sum-tax").text(number_format(result.sum_tax,3));
                $("#sum-sub-total").text(number_format(result.sum_sub_total,2));
            }
        });
    });

    jQuery.fn.ForceNumericOnly =
    function()
    {
        return this.each(function()
        {
            $(this).keydown(function(e)
            {
                var key = e.charCode || e.keyCode || 0;
                // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
                // home, end, period, and numpad decimal
                return (
                    key == 8 || 
                    key == 9 ||
                    key == 13 ||
                    key == 46 ||
                    key == 110 ||
                    key == 190 ||
                    (key >= 35 && key <= 40) ||
                    (key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105));
            });
        });
    };
    $(".amount").ForceNumericOnly();


    var attrObj = {"href":"javascript:void(0)","onclick":"return false;"};
    attrObj["title"] = "Total Allocated";
    $("#list-record").text('<?php echo number_format($total_allocated,2); ?>').attr(attrObj).css("text-align","right").attr("id","total_allocated");

    $("#list-view").on("change","input,select",function(){
        var ids = $(this).attr("id");
        var inval = $(this).val();
        var index = $(this).attr("index");
       
        //khoi tao gia tri luu
        names = $(this).attr("name");


        if(names=='amount')
            inval = UnFortmatPrice(inval);
        var values = new Object();
            values[names]=inval;

        saveOption({opname: "allocation", data: values, key : ids, controller:'<?php echo $controller; ?>', index : index,callBack: function(){}});
        return false;

    });

    function saveOption(object){
        var opname = object.opname;
        var key = object.key;
        var data = object.data;
        var index = object.index;
        var controller = object.controller;
        var callBack = object.callBack;
        if(controller ==undefined || controller =='')
            controller = '<?php echo $controller;?>';
        var dataSend = {controller:controller,opname:opname,key:key,data:data};
        var arr = {"keys":"update","opname":opname,"value_object":data,"opid":index};
        data.amount = parseInt(data.amount);

        loading("Saving...");
        $.ajax({
            url: '<?php echo URL;?>/'+controller+'/save_option',
            type:"POST",
            data: {"arr":JSON.stringify(arr),"fieldchage":key},
            dataType: "json",
            async : false,
            success: function(result){
                if(typeof callBack == "function")
                    callBack(result);
                $.mobile.loading( 'hide' );
            }
        });
    }
    function loading(text,textVisible,textonly,theme){
        $.mobile.loading( 'show',{
            text: (text!=undefined ? text : 'Loading...'),
            textVisible: (textVisible!=undefined ? textVisible : true),
            textonly: (textonly!=undefined ? textonly : false),
            theme: (theme!=undefined ? theme : 'b'),
        } );
    }
    function UnFortmatPrice(number){
        number = number.replace(/,/g,'');
        number = number.replace('.',',');
        return number;
    }

})
</script>