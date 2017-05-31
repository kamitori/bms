<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<?php if(!empty($options)){ ?>
<ul id="list-view" data-role="listview" data-inset="true">
    <?php foreach($options as $key => $value){ ?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <?php echo $value['code']; ?>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo $value['product_name'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width:30%"><b>Choice</b></div>
                <div class="ui-block-b ui-checkbox" style="width:70%">
                    <label for="checkbox-enhanced" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left" >Check</label>
                    <input name="choice_<?php echo $value['_id'] ?>"  id="choice_<?php echo $value['_id'] ?>" data-enhanced="true" <?php if($value['choice']) echo 'checked'; ?> <?php if(isset($value['xlock']['choice'])) echo 'reaonly="reaonly" onclick="return false;"'; ?> type="checkbox">
                    <input name="require_<?php echo $value['_id'] ?>"  id="require_<?php echo $value['_id'] ?>" data-enhanced="true" value="<?php echo isset($value['require'])&&$value['require'] ? 1 : 0 ?>" type="text">
                    <input name="product_id_<?php echo $value['_id'] ?>"  id="product_id_<?php echo $value['_id'] ?>" data-enhanced="true" value="<?php echo $value['product_id']; ?>" type="hidden">
                    <input name="proline_no_<?php echo $value['_id'] ?>"  id="proline_no_<?php echo $value['_id'] ?>" data-enhanced="true" value="<?php echo $value['proline_no']; ?>" type="hidden">
                    <input name="this_line_no_<?php echo $value['_id'] ?>"  id="this_line_no_<?php echo $value['_id'] ?>" data-enhanced="true" value="<?php echo $value['this_line_no']; ?>" type="hidden">
                    <input name="parent_line_no_<?php echo $value['_id'] ?>"  id="parent_line_no_<?php echo $value['_id'] ?>" data-enhanced="true" value="<?php echo $value['parent_line_no']; ?>" type="hidden">
                    <input name="same_parent_<?php echo $value['_id'] ?>"  id="same_parent_<?php echo $value['_id'] ?>" data-enhanced="true" value="<?php echo $value['same_parent']; ?>" type="hidden">
                    <?php if(isset($value['line_no'])){ ?>
                    <input name="line_no_<?php echo $value['_id'] ?>"  id="line_no_<?php echo $value['_id'] ?>" data-enhanced="true" value="<?php echo $value['line_no']; ?>" type="hidden">
                    <?php } ?>
                </div>
            </li>
        	<li>
        		<div class="ui-block-a" style="width: 30%"><b>Code</b></div>
        		<div class="ui-block-b" style="width:70%">
        			<?php echo $value['code']; ?>
        		</div>
        	</li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Name </b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['product_name'];?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Unit cost</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="unit_price_<?php echo $value['_id'] ?>" id="unit_price_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['unit_price']) ? $value['unit_price'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>OUM</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['oum'];?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Quantity</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="quantity_<?php echo $value['_id'] ?>" id="quantity_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['quantity']) ? $value['quantity'] : ''; ?>" />
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Sub total</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['sub_total'];?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Type</b></div>
                <div class="ui-block-b" style="width:70%">
                    <span id="group_type_<?php echo $value['_id'] ?>" class="group_type"><?php echo isset($value['group_type'])?$value['group_type']:'';?></span>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:30%"><b>Group</b></div>
                <div class="ui-block-b" style="width:70%">
                    <span id="option_group_<?php echo $value['_id'] ?>" class="option_group"><?php echo $value['option_group'];?></span>
                </div>
            </li>
            <li>
            	<a href="#popupDialog" class="callDelete" data-id="<?php echo $value['_id']; ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b">Delete</a>
            </li>
        </ul>
    </li>
    <?php } ?>
</ul>
<br />
<?php
    } else {
?>
<div class="ui-block-a" id="no-data" style="width:100%; text-align: center;"><b>No Data</b></div>
<?php
    }
?>
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
    $("#subtab-popup").text("<?php echo $products_name; ?>");
    $("#add-new-record").text("Back").attr({"href":"<?php echo M_URL.'/quotations/line_entry' ?>","rel":"external"});
    $("#delete-record").prop("disabled",true).text("_").attr("href","javascript:void(0)");
    $("#find-record").prop("disabled",true).text("_").attr("href","javascript:void(0)");
    $("#entry-record").prop("disabled",true).text("_").attr("href","javascript:void(0)");
    $("#list-record").text("Submit").attr({"href":"javascript:void(0)","id":"submit-button"}).css({"background-color":"#38c","color":"#fff"}).click(function(){

    });
    $("input,select","#list-view").change(function(){
        var this_ids = $(this).attr("id");
        this_ids = this_ids.split("_");
        this_ids = this_ids[this_ids.length-1];
        var name = $(this).attr("name");
        name = name.replace("_"+this_ids,"");
        //Xử lý EXC Group Type
        if(name == "group_type" || name == "option_group" || name == "choice"){
            var this_group_name = $("#option_group_"+this_ids).val();
            var this_group_type = $("#group_type_"+this_ids).val();
            var is_span = false;
            if(this_group_name==undefined || this_group_name==""){
                this_group_name = $.trim($("#option_group_"+this_ids).html());
                is_span = true;
            }
            if(this_group_type==undefined || this_group_type=="")
                this_group_type = $.trim($("#group_type_"+this_ids).html());
            if(this_group_name=="" || this_group_type=="Inc")
                return false;
            var option_group = $("#list-view").find(".option_group");
            var group = {};
            for(var i = 0; i < option_group.length; i++){
                if(!is_span)
                    var group_name = option_group[i].value;
                else
                    var group_name = option_group[i].innerText;
                var ids = option_group[i].id;
                var ids = ids.split("_");
                ids = ids[ids.length-1];
                if(group_name == this_group_name){
                    group[ids] = ids;
                }
            }
            var i = 0;
            var found = false;
            for(var ids in group){
                var type_group = $("#group_type_"+ids).val();
                if(type_group==undefined || type_group=="")
                    type_group = $.trim($("#group_type_"+this_ids).html());
                if(type_group!="Exc")
                    delete group[ids];
                else{
                    if(ids==this_ids)
                        found = true;
                    i++;
                }
            }
            if(name=="choice"&&!$(this).is(":checked")){
                if(this_group_type=='Exc'){
                    var require = false;
                    for(var ids in group){
                        if(parseInt($("#require_"+ids).val())==1){
                            require = true; break;
                        }
                    }
                    if(require){
                        for(var ids in group){
                            if(ids==this_ids) continue;
                            $("#choice_"+ids).prev().removeClass("ui-checkbox-off").addClass("ui-checkbox-on");
                            $("#choice_"+ids).prop("checked",true);break;
                        }
                    }
                }
                return false;
            }
            if(i>1){
                for(var ids in group){
                    $("#choice_"+ids).prop("checked",false);
                    $("#choice_"+ids).prev().removeClass("ui-checkbox-on").addClass("ui-checkbox-off");
                    console.log($("#choice_"+ids));
                }
                if(found){
                    $("#choice_"+this_ids).prop("checked",true);
                    $("#choice_"+this_ids).prev().removeClass("ui-checkbox-off").addClass("ui-checkbox-on");
                }
                else
                    for(var ids in group){
                        $("#choice_"+ids).prev().removeClass("ui-checkbox-off").addClass("ui-checkbox-on");
                        $("#choice_"+ids).prop("checked",true);break;
                    }
            }
        }
    });
    $("#submit-button").click(function(){
        var dataSend = $("input,select","#list-view").serialize();
        dataSend += "&submit=1&product_key=<?php echo $products_key ?>";
        $.ajax({
            type: "POST",
            data: dataSend,
            success: function(){
                window.location = "<?php echo M_URL.'/quotations/line_entry' ?>";
            }
        });
    });
})
</script>