<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_coms)){
        foreach ($arr_coms as $value):
?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo M_URL; ?>/communications/entry/<?php echo $value['_id']; ?>"><?php echo $value['code']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo $value['comms_type'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Ref no</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['code']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%;"><b>Type</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['comms_type']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Date</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['comms_date']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>From</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['contact_from']; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>To</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo isset($value['to'])?$value['to']:''; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Details</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                    <?php echo isset($value['details'])?$value['details']:''; ?>
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
    $("a:first",".ui-navbar").replaceWith('<a href="#popupNested2" class="ui-link ui-btn" data-rel="popup" class="" data-transition="pop">Add</a>');

    $("#delete_button").click(function(){
        var ids = $("#hiddenId").val();
        $.ajax({
        url : "<?php echo M_URL.'/'.$this->params['controller'].'/coms_delete/' ?>"+ids,
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
})


</script>

<div data-role="popup" id="popupNested2" data-theme="none">
    <div data-role="collapsible-set" data-theme="b" data-content-theme="a" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" style="margin:0; width:250px;">
        <ul data-role="listview">
            <li><a href="<?php echo '#';//M_URL.'/communications/add_com/message'; ?>" rel="external" data-rel="dialog">Message</a></li>
            <li><a href="<?php echo M_URL.'/'.$this->params['controller'].'/add_from_module/'.$module_id.'/letter'; ?>" rel="external" data-rel="dialog">Letter</a></li>
            <li><a href="<?php echo M_URL.'/'.$this->params['controller'].'/add_from_module/'.$module_id.'/fax'; ?>" rel="external" data-rel="dialog">Fax</a></li>
            <li><a href="<?php echo M_URL.'/'.$this->params['controller'].'/add_from_module/'.$module_id.'/note'; ?>" rel="external" data-rel="dialog">Note</a></li>
            <li><a href="<?php echo M_URL.'/'.$this->params['controller'].'/add_from_module/'.$module_id.'/email'; ?>" rel="external" data-rel="dialog">Email</a></li>
        </ul>
    </div>
</div>
