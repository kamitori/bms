<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_purchases)){
        foreach ($arr_purchases as $value):
?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo M_URL; ?>/purchaseorders/entry/<?php echo $value['_id']; ?>"><?php echo $value['code']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo $value['company_name'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 30%"><b> Ref no</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['code']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Required date </b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['required_date']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Delivery date</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['delivery_date']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Status</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['purchase_orders_status']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Supplier contact</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['ship_to_contact_name']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Our rep</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                    <?php echo $value['our_rep']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%;"><b>Total (bf.Tax)</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['sum_sub_total']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Heading</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                    <?php echo $value['name']; ?>
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
    $("#find-record").html('<?php echo $sum_amount; ?>').attr({"href":"javascript:void(0)","onclick":"return false;","title":"Sum Amount"});
    $("#entry-record").html('<?php echo $sum_tax; ?>').attr({"href":"javascript:void(0)","onclick":"return false;","title":"Sum Tax"});
    $("#list-record").html('<?php echo $sum_sub_total; ?>').attr({"href":"javascript:void(0)","onclick":"return false;","title":"Sum Sub Total"});

    $("#delete_button").click(function(){
        var ids = $("#hiddenId").val();
        $.ajax({
        url : "<?php echo M_URL.'/companies/purchases_delete/' ?>"+ids,
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
    $("#find-record").text('<?php echo $sum_amount; ?>').attr(attrObj).css("text-align","right");
    attrObj["title"] = "Sum Tax";
    $("#entry-record").text('<?php echo $sum_tax; ?>').attr(attrObj).css("text-align","right");
    attrObj["title"] = "Sum Sub Total";
    $("#list-record").text('<?php echo $sum_sub_total; ?>').attr(attrObj).css("text-align","right");
    $("#delete-record").text("Entry").attr("href","<?php echo M_URL.'/jobs/entry/'; ?>");
})
</script>