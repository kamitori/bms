<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_tasks)){
        foreach ($arr_tasks as $value):
?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo M_URL; ?>/tasks/entry/<?php echo $value['_id']; ?>"><?php echo $value['no']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo $value['name'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 30%"><b>No</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['no']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%;"><b>Task</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['name']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Type</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['type']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Responsible</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['our_rep']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Work start</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['work_start']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Work end</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                    <?php echo $value['work_end']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Status</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['status']; ?>
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
    $("a:first",".ui-navbar").replaceWith('<a href="#" class="ui-link ui-btn" id="bt_add_task" data-rel="popup" class="" data-transition="pop">Add</a>');
    $("#delete_button").click(function(){
        var ids = $("#hiddenId").val();
        $.ajax({
        url : "<?php echo M_URL.'/companies/tasks_delete/' ?>"+ids,
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

    $('#bt_add_task').click(function(){
            $.ajax({
                url:"<?php echo URL;?>/mobile/companies/tasks_add/" + "<?php echo $company_id ?>",
                timeout: 15000,
                success: function(html){
                    location.replace(html);
                    reload_subtab('task');
                }
            });
    })
})
</script>