<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_supplier)){
        foreach ($arr_supplier as $value):
?>
    <li id="list-<?php echo $value['company_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo M_URL; ?>/companies/entry/<?php echo $value['company_id']; ?>"><?php echo $value['company_name']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo $value['name'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Supplier</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['company_name']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>SKU</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['sku']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Name</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['name']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Size-W</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['sizew']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Size-H</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['sizeh']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 30%;"><b>Sold by unit</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['sell_by']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 30%;"><b>Cost Price</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['sell_price']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 30%;"><b>Unit Price</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['unit_price']; ?>
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
        url : "<?php echo M_URL.'/products/companies_delete/' ?>"+ids,
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

    var attrObj = {"href":"javascript:void(0)","onclick":"return false;"};
    attrObj["title"] = "Sum Amount";
   
    $("#find-record").text("List").attr("href","<?php echo M_URL.'/'.$controller.'/lists/'; ?>");

    $("#list-record").text('<?php echo number_format($v_average_plus,3); ?>').attr(attrObj).css("text-align","right").attr("id","qty");
})
</script>