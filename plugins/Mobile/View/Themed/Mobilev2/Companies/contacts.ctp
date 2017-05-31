<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_contacts)){
        foreach ($arr_contacts as $value):
?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo M_URL; ?>/contacts/entry/<?php echo $value['_id']; ?>"><?php echo $value['no']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo $value['first_name'];?> <?php echo $value['last_name'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
        	<li>
        		<div class="ui-block-a" style="width: 30%"><b>Code</b></div>
        		<div class="ui-block-b" style="width:70%">
        			<?php echo $value['no']; ?>
        		</div>
        	</li>
        	<li>
        		<div class="ui-block-a" style="width: 30%;"><b>Title</b></div>
        		<div class="ui-block-b" style="width:70%">
                    <?php echo $this->Form->input('title',array(
                            'name'  => 'title',
                            'id'    => 'title_'.$value['_id'],
                            'empty' => '',
                            'options' => $options['contacts_title'],
                            'value'   => $value['title']
                        ));
                    ?>
        		</div>
        	</li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>First Name </b></div>
                <div class="ui-block-b" style="width:70%">
                	<input type="text" name="first_name" id="first_name_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['first_name']) ? $value['first_name'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Last Name </b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="last_name" id="last_name_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['last_name']) ? $value['last_name'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Default</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                	 <label for="checkbox-enhanced" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left ui-checkbox-off" >Check</label>
    				<input name="contact_default"  id="contact_default_<?php echo $value['_id'] ?>" <?php echo (isset($value['contact_default'])&& $value['contact_default'] ? 'checked' : ''); ?> data-enhanced="true" type="checkbox">
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Position</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $this->Form->input('position',array(
                            'name'  => 'position',
                            'id'    => 'position_'.$value['_id'],
                            'empty' => '',
                            'options' => $options['contacts_position'],
                            'value'   => $value['position']
                        ));
                    ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Direct dial </b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="direct_dial" id="direct_dial_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['direct_dial']) ? $value['direct_dial'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Ext no</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="extension_no" id="extension_no_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['extension_no']) ? $value['extension_no'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Mobile</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="mobile" id="mobile_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['mobile']) ? $value['mobile'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Email address </b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="email" id="email_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['email']) ? $value['email'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Inactive</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                	 <label for="checkbox-enhanced" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left ui-checkbox-off" >Check</label>
    				<input name="inactive"  id="inactive_<?php echo $value['_id'] ?>" <?php echo (isset($value['inactive'])&& $value['inactive'] ? 'checked' : ''); ?> data-enhanced="true" type="checkbox">
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