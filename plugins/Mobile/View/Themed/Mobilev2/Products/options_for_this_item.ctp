<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($productoptions)){
        foreach ($productoptions as $key => $value):
?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo M_URL; ?>/products/entry/<?php echo $value['_id']; ?>"><?php echo $value['code']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo $value['product_name'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Code</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['code']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%;"><b>SKU</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['sku']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Name</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['product_name']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Type</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo isset($value['group_type']) ? $value['group_type'] : ''; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Group</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['option_group']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Req</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                     <label for="checkbox-enhanced" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left ui-checkbox-off" >Check</label>
                    <input name="req"  id="req_<?php echo $key ?>" <?php  ?> data-enhanced="true" type="checkbox">
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>S.P</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                     <label for="checkbox-enhanced" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left ui-checkbox-off" >Check</label>
                    <input name="same_parent"  id="same_parent_<?php echo $key ?>" <?php  ?> data-enhanced="true" type="checkbox">
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Unit cost</b></div>
                <div class="ui-block-b" style="width:70%">
                     <input type="text" name="unit_cost" id="unit_cost_<?php echo $key ?>" data-theme="a" value="<?php echo isset($value['unit_price']) ? $value['unit_price'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>%Discount</b></div>
                <div class="ui-block-b" style="width:70%">
                   <input type="text" name="discount" id="discount_<?php echo $key ?>" data-theme="a" value="<?php echo isset($value['discount']) ? $value['discount'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Quantity</b></div>
                <div class="ui-block-b" style="width:70%">
                   <input type="text" name="quantity" id="quantity_<?php echo $key ?>" data-theme="a" value="<?php echo isset($value['quantity']) ? $value['quantity'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Sub total</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['sub_total']; ?>
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
<?php echo $this->element('js_subtab'); ?>
<?php echo $this->element('js_line'); ?>
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

    $("#list-view").on("change","input, select",function(){
        var ids = $(this).attr("id");
        var inval = $(this).val();
        ids = ids.split("_");
        ids = ids[ids.length-1];
        var names = $(this).attr("name");
        if(names == "req" || names == 'same_parent'){
            if($(this).is(':checked')){
                inval = 1;
            }else{
                inval = 0;
            }
        }
        var data = {};
        data[names] = inval;
        saveOption({opname:"options", data: data, key: ids, controller: 'products'});
    });

    $('#add-new-record').addClass('option_product');
    window_popup('products','product_option','option_product','?product_type="Product"');

})

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

function after_choose_products( product_id, product_name, key){
        var count = "<?php echo count($productoptions); ?>";
        //$("#window_popup_products_product_option").attr("data-id",$(obj).attr("data-id"));
        if(key == "product_option"){
            var id = count;//$("#window_popup_products_product_option").attr("data-id");
            var key = null;
            if(id != undefined)
                key = id;
            var data = {};
            //data["choose"] = 1;
            data["deleted"] = "false";
            data["product_id"] = product_id;
            data["markup"] = 0;
            data["margin"] = 0;
            data["quantity"] = 1;
            data["option_group"] = '';
            data["require"] = 1;

            var extraField = ["options","company_id"];
            saveOption({opname: "options", extraField : extraField, data: data, key : key, controller:'<?php echo $controller; ?>', callBack: function(result){
                    location.reload();
                }
            });
        }
    }
</script>