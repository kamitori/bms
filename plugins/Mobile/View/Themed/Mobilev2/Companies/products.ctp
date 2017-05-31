<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_products)){
        foreach ($arr_products as $value):
?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo M_URL; ?>/companies/products_pricing/<?php echo $value['_id']; ?>"><?php echo $value['code']; ?></a></div>
            <div class="ui-block-b" style="width:60%"><?php echo $value['name'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Code</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['code']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%;"><b>Name</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['name']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Range</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['range']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Unit Price</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['unit_price']; ?>
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
    window_popup("products", "products","add-new-record");
    $("#delete_button").click(function(){
        var ids = $("#hiddenId").val();
        saveOption({opname : "pricing", key : ids, data : {deleted : "true"}, controller : "companies", callBack : function(){
            $("#list-"+ids).fadeOut().remove();
            $("#hiddenId").val("");
        }});
    });
    $("#list-view").on("click",".callDelete",function(){
        var value = $(this).attr("data-id");
        $("#hiddenId").val(value);
    });
    $(".link-to-entry").click(function(){
        window.location.assign($(this).attr("href"));
    });
})
function after_choose_products(product_id,product_name){
    var jsonData = $("#after_choose_products_"+product_id).val();
    jsonData = $.parseJSON(jsonData);
    var data = {
        deleted : "false",
        code    : jsonData.code,
        name : product_name,
        product_id : product_id,
        notes   : "",
        price_break : "",
    };
    saveOption({opname : "pricing", key : null, data : data, controller : "companies", callBack : function(result){
        result = $.parseJSON(result);
        for(var i in result["pricing"]){
            break;
        }
        window.location.assign("<?php echo M_URL.'/companies/products_pricing/' ?>"+i);
    }});
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