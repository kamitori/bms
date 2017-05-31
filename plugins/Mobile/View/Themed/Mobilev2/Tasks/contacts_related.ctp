<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_contacts_related)){
        foreach ($arr_contacts_related as $key => $value):
?>
    <li id="list-<?php echo $key; ?>" class="ui-li-static ui-body-inherit" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo URL; ?>/mobile/contacts/entry/<?php echo $value['contact_id']; ?>"><?php echo $value['contact_name']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo isset($value['contact_name']) ? $value['contact_name'] : '';  ?></div>
        </h2>
        <ul data-role="listview" data-theme="b">

            <li>
                <div class="ui-block-a" style="width:30%"><b>Contact</b></div>
                <div class="ui-block-b" style="width:70%">
                	<?php echo isset($value['contact_name']) ? $value['contact_name'] : ''; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Main</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" readonly name="default" value="<?php if ($key == $contacts_default_key) echo 'Yes'; else echo 'No';?>">
                </div>
            </li>

            <li>
            	<a href="#popupDialog" class="callDelete" data-id="<?php echo $value['contact_id']; ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b" key="<?php echo $key; ?>">Delete</a>
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
<?php //echo $this->element('js'); ?>
<?php //echo $this->element('js_line'); ?>
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
        var key = $("#hiddenId").val();
        $.ajax({
            url: '<?php echo URL; ?>/tasks/general_delete_contact/<?php echo $task_id; ?>/'+ key,
            success: function(result){
                $("#list-"+key).fadeOut().remove();
                $("#hiddenId").val("");
            }
        })
    });
    $("#list-view").on("click",".callDelete",function(){
        var value = $(this).attr("key");
        $("#hiddenId").val(value);
    });

    $(".ui-li-static .ui-input-text").delegate("input,select","change",function(){
        //loading("Saving...");
        var names = $(this).attr("name");
        var inval = $(this).val();
        var ids = names.split("_");
        ids = ids[ids.length - 1];
        names = names.replace("_"+ids,"");
        names = names.replace("cb_","");
        //save_sub_tab(names,inval,ids,'',function(){},"docs");
        $.ajax({
            url : "<?php echo M_URL.'/'.$controller.'/noteactivity_update/' ?>" + ids,
            type:"POST",
            data: {content: $(this).val()},
            success: function(result){
                //$.mobile.loading( 'hide' );
                console.log('suscess');
            }
        })
    });
   
    $(".link-to-entry").click(function(){
        window.location.assign($(this).attr("href"));
    });


    $('#add-new-record').addClass('key_click');
    window_popup('contacts','contact_option','key_click','?is_employee=1');
})

function window_popup(controller, key, key_click_open, parameter_get, force_re_install){
    if($("#"+key).attr("id")!=undefined)
        return false;
    if( key == undefined ){
        key = "";
    }
    if( parameter_get == undefined ){
        parameter_get = "";
    }
    if( force_re_install == undefined ){
        force_re_install = "";
    }
    var window_popup = "window_popup_" + controller+"_"+key;
    $("."+key_click_open).click(function(){
        if(!$("#"+window_popup,"body").hasClass("page-content"))
            $.ajax({
                url : "<?php echo M_URL; ?>/"+ controller +"/popup/" + key + parameter_get,
                success: function(data){
                    var html = '<div data-role="page" class="page-content" id="'+window_popup+'" style="border-top:none;">'+data+'</div>';
                    $("body").append(html);
                    $.mobile.changePage($("#"+window_popup));
                }
            });
        else
            $.mobile.changePage($("#"+window_popup));
    });
}


function after_choose_contacts(contact_id, contact_name){
    $.ajax({
        url: "<?php echo URL; ?>/tasks/general_window_contact_choose/<?php echo $task_id; ?>/" + contact_id + "/" + contact_name,
        timeout: 15000,
        success: function(html){
            console.log(html);
            if(html == "ok"){
                location.reload(true);
            }else{
                alerts("Error: ", html);
            }
        }
    });
    return false;
}

</script>