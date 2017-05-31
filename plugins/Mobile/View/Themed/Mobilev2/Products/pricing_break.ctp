<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($price_breaks) && isset($price_breaks['pricebreaks'])){
        foreach ($price_breaks['pricebreaks'] as $key => $value):
?>
    <li id="list-<?php //echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="#"><?php echo $value['range_from']; ?> - <?php echo $value['range_to'];?> </a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php //echo $value['range_to'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Range from</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="range_from" id="range_from_<?php echo $key ?>" data-theme="a" value="<?php echo isset($value['range_from']) ? $value['range_from'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%;"><b>Range to</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="range_to" id="range_to_<?php echo $key ?>" data-theme="a" value="<?php echo isset($value['range_to']) ? $value['range_to'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Unit price</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="unit_price" id="unit_price_<?php echo $key ?>" data-theme="a" value="<?php echo isset($value['unit_price']) ? $value['unit_price'] : ''; ?>" />
                </div>
            </li>
            <li>
                <a href="#popupDialog" class="callDelete" data-id="<?php echo $key; ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b">Delete</a>
            </li>
        </ul>
    </li>
<?php endforeach; ?>
</ul>
<br />
<?php
    } else {
?>
<div class="ui-block-a" id="no-data" style="width:100%; text-align: center;"><b>No Data</b></div>
<?php
    }
?>
<?php //echo $this->element('js_subtab'); ?>
<?php echo $this->element('js'); ?>
<div data-role="popup" id="popupDialog" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="max-width:300px;">
    <div role="main" class="ui-content">
        <h2 class="ui-title">Are you sure you want to delete this record?</h2>
    <p>This action cannot be undone.</p>
        <input type="hidden" id="hiddenId" value="" />
        <a href="#" id="cancel_button" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back">Cancel</a>
        <a href="#" id="delete_button" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back" data-transition="flow">Delete</a>
    </div>
</div>

<script type="text/javascript">
$(function(){
    $("#delete_button").click(function(){
        var ids = $("#hiddenId").val();
        $.ajax({
        url : "<?php echo M_URL.'/jobs/invoices_delete/' ?>"+ids,
            success: function(result){
                $("#list-"+ids).fadeOut().remove();
                $("#hiddenId").val("");
            }
        })
    });
    $("#list-view").on("click",".callDelete",function(){
        var value = $(this).attr("data-id");
        $("#hiddenId").val(value);
    });
    $(".link-to-entry").click(function(){
        window.location.assign($(this).attr("href"));
    });
    $("#list-view").on("change","input", function(){
        var ids = $(this).attr("id");
        var inval = $(this).val();
        ids = ids.split("_");
        ids = ids[ids.length - 1];
        var names = $(this).attr("name");
        var data = {};
        data[names] = inval;
        saveOption({opname: "pricebreaks", data: data, key: ids, controller: 'products'});
    });
    $("#add-new-record").click(function(){
        var sell_price = <?php echo $sell_price; ?>//$('#sell_price').val();
        var sell_category = 'Retail';//$('#current_category').val();
        var datas = {
                'sell_category' : sell_category,
                'range_from' : 0,
                'range_to' : 0,
                'unit_price' : sell_price
            };
        save_option('pricebreaks',datas,'',1,'pricing','add');
    });
})

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
                change_pro();
            }
        });
    }else return '';
}
function change_pro(){
        location.reload();
}
function saveOption(object){
    var opname = object.opname;
    var key = object.key;
    var data = object.data;
    var controller = object.controller;
    var callBack = object.callBack;
    if(controller ==undefined || controller =='')
        controller = '<?php echo $controller;?>';
    $.ajax({
        url: '<?php echo M_URL;?>/'+controller+'/save_option',
        type:"POST",
        data: {opname:opname,key:key,data:data},
        success: function(result){
            if(typeof callBack == "function")
                callBack(result);
        }
    });
}
</script>