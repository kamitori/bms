<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_addresses)){
        foreach ($arr_addresses as $key => $value):
            if(isset($value['deleted']) && $value['deleted']) continue;
?>
    <li id="list-<?php echo $key ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%"><?php echo isset($value['address_1']) ? $value['address_1'] : ''; ?></div>
            <div class="ui-block-b" style="width:60%"><?php echo isset($options['contacts_addresses_name'][$value['name']]) ? $options['contacts_addresses_name'][$value['name']] : '' ?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
        	<li>
        		<div class="ui-block-a" style="width: 30%"><b>Name</b></div>
        		<div class="ui-block-b" style="width:70%">
                    <?php
                        echo $this->Form->input('name',array(
                                                'name'  => 'name',
                                                'id'    => 'name_'.$key,
                                                'empty' => '',
                                                'value' => (isset($value['name']) ? $value['name'] : ''),
                                                'options'=>$options['contacts_addresses_name']
                                                ));
                    ?>
        		</div>
        	</li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Default</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                     <label for="checkbox-enhanced" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left ui-checkbox-off" >Check</label>
                    <input name="default"  id="default_<?php echo $key ?>" <?php echo ( $key == $addresses_default_key ? 'checked' : ''); ?> data-enhanced="true" type="checkbox">
                </div>
            </li>
        	<li>
                <div class="ui-block-a" style="width: 30%"><b>Address 1</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="address_1" id="address_1_<?php echo $key ?>" data-theme="a" value="<?php echo isset($value['address_1']) ? $value['address_1'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Address 2</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="address_2" id="address_2_<?php echo $key ?>" data-theme="a" value="<?php echo isset($value['address_2']) ? $value['address_2'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Address 3</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="address_3" id="address_3_<?php echo $key ?>" data-theme="a" value="<?php echo isset($value['address_3']) ? $value['address_3'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Town / City</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="town_city" id="town_city_<?php echo $key ?>" data-theme="a" value="<?php echo isset($value['town_city']) ? $value['town_city'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Province / State</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php
                        $province = isset($options['provinces'][$value['country_id']]) ? $options['provinces'][$value['country_id']] : array();
                        echo $this->Form->input('province_state_id',array(
                                                'name'  => 'province_state_id',
                                                'id'    => 'province_state_id_'.$key,
                                                'value' => (isset($value['province_state_id']) ? $value['province_state_id'] : ''),
                                                'empty' => '',
                                                'options'=> $province
                                                ));
                    ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Zip / Post code</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="zip_postcode" id="zip_postcode_<?php echo $key ?>" data-theme="a" value="<?php echo isset($value['zip_postcode']) ? $value['zip_postcode'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width: 30%"><b>Country</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php
                        echo $this->Form->input('country_id',array(
                                                'name'  => 'country_id',
                                                'id'    => 'country_id_'.$key,
                                                'empty' => '',
                                                'value' => (isset($value['country_id']) ? $value['country_id'] : ''),
                                                'options'=>$options['countries']
                                                ));
                    ?>
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
<?php echo $this->element('js_subtab',array('isOptions' => true)); ?>
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
        saveOption({opname: "addresses", data: {"deleted" : 1}, key : key, controller:'companies', callBack: function(){
                $("#list-"+key).fadeOut().remove();
                $("#hiddenId").val("");
            }
        });
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
        if(names=='default'){
            if($(this).is(':checked')){
                inval = 1;
            }else{
                inval = 0;
            }
        }
        var data = {};
        data[names] = inval;
        if($(this).prop("tagName")=="SELECT"){
            otherName = names.replace("_id","");
            data[otherName] = $("#"+names+"_"+ids+" option:selected").text();
            if(otherName == "country"){
                var provinces = options["provinces"];
                var curProvince = provinces[inval];
                var html = "";
                html += '<option value=""></option>';
                for(var i in curProvince){
                    html += '<option value="'+i+'">'+curProvince[i]+'</option>';
                }
                $("#province_state_id_"+ids).val("").text("").html(html);
                data["province_state_id"] = "";
                data["province_state"] = "";
            }
        }
        loading("Saving...");
        saveOption({opname: "addresses", data: data, key : ids, controller:'companies', callBack: function(){
                if(names == "name" ){
                    var text = $("#name_"+ids+" option:selected").text();
                    $(".ui-block-b:first","#list-"+ids).html(text);
                } else if(names == "default" && inval == 1) {
                    $("input[name='default']:checked","#list-view").prev().removeClass("ui-checkbox-on").addClass("ui-checkbox-off");
                    $("input[name='default']:checked","#list-view").prop("checked",false);
                    $("#default_"+ids).prop("checked",true);
                    $("#default_"+ids).prev().removeClass("ui-checkbox-off").addClass("ui-checkbox-on");
                }
                $.mobile.loading( 'hide' );
            }
        });
    });
})
function loading(text,textVisible,textonly,theme){
    $.mobile.loading( 'show',{
        text: (text!=undefined ? text : 'Loading...'),
        textVisible: (textVisible!=undefined ? textVisible : true),
        textonly: (textonly!=undefined ? textonly : false),
        theme: (theme!=undefined ? theme : 'b'),
    } );
}
</script>