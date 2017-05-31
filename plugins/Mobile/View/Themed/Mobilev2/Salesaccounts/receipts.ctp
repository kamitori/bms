<?php //pr($arr_receipt);die; ?>
<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_receipt)){
        foreach ($arr_receipt as $key=>$value):
?>
    <li id="list-<?php echo $key; ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo M_URL; ?>/receipts/entry/<?php echo $key; ?>"><?php echo $value['code']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo $value['reference'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Ref no</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['code']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%;"><b>Date</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['date']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Paid by</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['paid_by']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Reference</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['reference']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Notes</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['notes']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Total receipt</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                    <?php echo $value['amount_received']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Allocated</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['total_allocated']; ?>
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
    $("a:first",".ui-navbar").replaceWith('<a href="#" class="ui-link ui-btn" id="bt_add_receipt" data-rel="popup" class="" data-transition="pop">Add</a>');
    $("#delete_button").click(function(){
        var ids = $("#hiddenId").val();
        $.ajax({
        url : "<?php echo M_URL.'/companies/receipts_delete/' ?>"+ids,
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

    $('#bt_add_receipt').click(function(){
            $.ajax({
                url:"<?php echo URL;?>/salesaccounts/receipt_add/" + "<?php echo $salesaccount_id ?>",
                timeout: 15000,
                success: function(html){
                    location.replace(html);
                }
            });
    })

    var attrObj = {"href":"javascript:void(0)","onclick":"return false;"};
    attrObj["title"] = "Total receipt";
    $("#list-record").text('<?php echo number_format($unallocated,2); ?>').attr(attrObj).css("text-align","right").attr("id","sum-amount");
    attrObj["title"] = "Total Allocated";
    $("#entry-record").text('<?php echo number_format($total_allocated,3); ?>').attr(attrObj).css("text-align","right").attr("id","sum-tax");
    attrObj["title"] = "Total Unallocated";
    $("#find-record").text('<?php echo number_format($total_receipt,2); ?>').attr(attrObj).css("text-align","right").attr("id","sum-sub-total");
    $("#delete-record").text("Entry").attr("href","<?php echo M_URL.'/'.$controller.'/entry/'; ?>");
})
</script>