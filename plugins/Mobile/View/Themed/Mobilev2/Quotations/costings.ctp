<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($costings)){
        foreach ($costings as $value):
?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo M_URL; ?>/contacts/entry/<?php echo $value['_id']; ?>"><?php echo $value['sku']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo $value['products_name'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 30%"><b>SKU</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['sku']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Name / details </b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="products_name" id="products_name" data-theme="a" value="<?php echo isset($value['products_name']) ? $value['products_name'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Size-W</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="sizew" id="sizew" data-theme="a" value="<?php echo isset($value['sizew']) ? $value['sizew'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Size-H</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="sizeh" id="sizeh" data-theme="a" value="<?php echo isset($value['sizeh']) ? $value['sizeh'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Sold by</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="sell_by" id="sell_by" data-theme="a" value="<?php echo isset($value['sell_by']) ? $value['sell_by'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Cost price</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="unit_price" id="unit_price" data-theme="a" value="<?php echo isset($value['unit_price']) ? $value['unit_price'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Quantity</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="quantity" id="quantity" data-theme="a" value="<?php echo isset($value['quantity']) ? $value['quantity'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Adj Qty</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="adj_qty" id="adj_qty" data-theme="a" value="<?php echo isset($value['adj_qty']) ? $value['adj_qty'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Amount</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="amount" id="amount" data-theme="a" value="<?php echo isset($value['amount']) ? $value['amount'] : ''; ?>" />
                </div>
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
        url : "<?php echo M_URL.'/companies/contacts_delete/' ?>"+ids,
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
    $("#list-view").on("change","input,select",function(){
        var ids = $(this).attr("id");
        var inval = $(this).val();
        ids = ids.split("_");
        ids = ids[ids.length - 1];
        var names = $(this).attr("name");
        if(names=='contact_default' || names == 'inactive'){
            if($(this).is(':checked')){
                inval = 1;
            }else{
                inval = 0;
            }
        }
        saveData({field: names, value: inval, ids : ids, controller:'contacts', callBack: function(){
                if(names == "first_name" || names == "last_name"){
                    var text = $("#first_name_"+ids).val();
                    text += " "+$("#last_name_"+ids).val();
                    $(".ui-block-b:first","#list-"+ids).html(text);
                } else if(names == "contact_default" && inval == 1) {
                    $("input[name='contact_default']:checked","#list-view").prev().removeClass("ui-checkbox-on").addClass("ui-checkbox-off");
                    $("input[name='contact_default']:checked","#list-view").prop("checked",false);
                    $("#contact_default_"+ids).prop("checked",true);
                    $("#contact_default_"+ids).prev().removeClass("ui-checkbox-off").addClass("ui-checkbox-on");
                }
            }
        });
    });
    $(".link-to-entry").click(function(){
        window.location.assign($(this).attr("href"));
    });
})
</script>