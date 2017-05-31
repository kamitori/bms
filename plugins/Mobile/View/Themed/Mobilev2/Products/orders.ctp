<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_orders)){
        foreach ($arr_orders as $value):
?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo M_URL; ?>/salesorders/entry/<?php echo $value['_id']; ?>"><?php echo $value['code']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo isset($value['status'])?$value['status'] : '';?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Ref no</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['code']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Date</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['date_modified']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Company</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['company_name']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Contact</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                    <?php echo $value['contact_name']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Our rep</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                    <?php echo $value['our_rep']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Job name</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                    <?php echo $value['job_name']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Status</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['status']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Quantity</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                    <?php echo $value['quantity']; ?>
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
<?php //echo $this->element('js_subtab'); ?>

<script type="text/javascript">
$(function(){
    $("#delete_button").click(function(){
        var ids = $("#hiddenId").val();
        $.ajax({
        url : "<?php echo M_URL.'/products/orders_delete/' ?>"+ids,
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
   
    $("#list-record").html('<?php echo $total; ?>').attr({"href":"javascript:void(0)","onclick":"return false;","title":"Total (not inc. cancelled"});
    $("#delete-record").html('<?php echo "Entry"; ?>').attr({"href":"http://jobtraq.local.com/mobile/products/entry","id":"entry-record", "class":"ui-link ui-btn","data-ajax":"false"});

})
</script>