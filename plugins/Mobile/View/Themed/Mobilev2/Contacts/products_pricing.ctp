<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
    .unselectable {
       -moz-user-select: -moz-none;
       -khtml-user-select: none;
       -webkit-user-select: none;
       -ms-user-select: none;
       user-select: none;
    }
</style>
<div class="ui-field-contain">
    <label class="field-title" for="code"><?php echo __('Code'); ?></label>
    <input class="unselectable" id="code" id="text" type="text" value="<?php echo $arr_data['code']; ?>" readonly />
</div>
<div class="ui-field-contain">
    <label class="field-title" for="name"><?php echo __('Name'); ?></label>
    <input class="unselectable"  id="name" type="text" value="<?php echo $arr_data['name']; ?>" readonly />
</div>
<div class="ui-field-contain">
    <label class="field-title" for="contact_name"><?php echo __('Customer'); ?></label>
    <input class="unselectable"  id="contact_name" type="text" value="<?php echo $arr_data['contact_name']; ?>" readonly />
</div>
<div class="ui-field-contain">
    <label class="field-title" for="note"><?php echo __('Notes'); ?></label>
    <textarea id="notes"><?php echo $arr_data['notes']; ?></textarea>
</div>
<ul id="list-view" data-role="listview" data-inset="true">
<?php
    if(!empty($arr_pricebreaks)){
        foreach ($arr_pricebreaks as $value):
?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%"><?php echo $value['range_from'].' - '.$value['range_to'] ?></div>
            <div class="ui-block-b" style="width:60%"><?php echo number_format($value['unit_price'],2) ;?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Range From</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" data-theme="a" name="range_from" id="range_from_<?php echo $value['_id'] ?>" value="<?php echo $value['range_from']; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%;"><b>Range To</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" data-theme="a" name="range_to" id="range_to_<?php echo $value['_id'] ?>" value="<?php echo $value['range_to']; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Unit Price</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" data-theme="a" name="unit_price" id="unit_price_<?php echo $value['_id'] ?>" value="<?php echo $value['unit_price']; ?>" />
                </div>
            </li>
            <li>
                <a href="#popupDialog" class="callDelete" data-id="<?php echo $value['_id']; ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b">Delete</a>
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
<div data-role="popup" id="popupDialog" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="max-width:300px;">
    <div role="main" class="ui-content">
        <h2 class="ui-title">Are you sure you want to delete this record?</h2>
    <p>This action cannot be undone.</p>
        <input type="hidden" id="hiddenId" value="" />
        <a href="#" id="cancel_button" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back">Cancel</a>
        <a href="#" id="delete_button" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back" data-transition="flow">Delete</a>
    </div>
</div>
<?php echo $this->element('js_subtab', array('isOptions' => 1)); ?>
<script type="text/javascript">
$(function(){
    $("#delete-record").text("Return").attr("href","<?php echo M_URL.'/contacts/products' ?>");
    $("#delete_button").click(function(){
        var ids = $("#hiddenId").val();
        var subData = {};
        subData[ids] = {
            deleted : "true"
        };
        var data = {price_break : subData};
        saveOption({opname : "pricing", key : "<?php echo $pricing_key; ?>", data : data, controller : "contacts", callBack : function(){
            $("#list-"+ids).fadeOut().remove();
            $("#hiddenId").val("");
        }});
    });
    $("#list-view").on("click",".callDelete",function(){
        var value = $(this).attr("data-id");
        $("#hiddenId").val(value);
    });
    $("#notes").change(function(){
        var data = {notes : $(this).val()};
        saveOption({opname : "pricing", key : "<?php echo $pricing_key; ?>", data : data, controller : "contacts"});
    });
    $("#list-view").on("change","input",function(){
        var id = $(this).attr("id");
        var name = $(this).attr("name");
        var value = $(this).val();
        id = id.replace(name+"_","");
        var subData = {};
        var objVal = {};
        objVal[name] = value;
        subData[id] = objVal;
        var data = {price_break : subData};
        saveOption({opname : "pricing", key : "<?php echo $pricing_key; ?>", data : data, controller : "contacts", callBack : function(){
            var text = $("#range_from_"+id).val() + " - " + $("#range_to_"+id).val();
            $(".ui-block-a:first","#list-"+id).text(text);
            $(".ui-block-b:first","#list-"+id).text($("#unit_price_"+id).val());
        }});
    });
});
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