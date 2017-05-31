<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<?php 
    if (!empty($arr_expense)) {
        end($arr_expense);         // move the internal pointer to the end of the array
        $last_key = key($arr_expense);
    } else {
        $last_key = -1;
    }
?>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_expense)){
        foreach ($arr_expense as $key => $value):
?>
    <li id="list-<?php echo $key; ?>" class="ui-li-static ui-body-inherit" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <?php if (isset($value['heading']) && $value['heading']!='') echo $value['heading'];else echo "New"; ?>
            </div>
            <div class="ui-block-b" style="width:60%">
                <?php echo isset($value['details']) ? $value['details'] : '';  ?></div>
        </h2>
        <ul data-role="listview" data-theme="b">

            <li>
                <div class="ui-block-a" style="width:30%"><b>Heading</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="heading" key="<?php echo $key; ?>" data-theme="a" value="<?php echo isset($value['heading']) ? $value['heading'] : ''; ?>">
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Details</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="details" key="<?php echo $key; ?>" data-theme="a" value="<?php echo isset($value['details']) ? $value['details'] : ''; ?>">
                </div>
            </li>

            <li>
            	<a href="#popupDialog" class="callDelete" data-id="<?php echo $key; ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b" key="<?php echo $key; ?>">Delete</a>
            </li>
        </ul>
    </li>
<?php endforeach; ?>
<br />
<?php
    } else {
?>
<div class="ui-block-a" id="no-data" style="width:100%; text-align: center;"><b>No Data</b></div><br />
<?php
    }
?>
</ul>
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
<?php echo $this->element('js'); ?>
<script type="text/javascript">
$(function(){
    $("#delete_button").click(function(){
        var key = $("#hiddenId").val();
        $.ajax({
            url: '<?php echo M_URL; ?>/tasks/expense_delete/<?php echo $task_id; ?>/'+ key,
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

    $("ul#list-view").on("change","input",function(){
        loading("Saving...");
        var names = $(this).attr("name");
        var inval = $(this).val();
        var key = $(this).attr("key");
        $.ajax({
            url: '<?php echo M_URL; ?>/tasks/expense_auto_save',
            type:"POST",
            data: {key:key, field:names, value:inval},
            success: function(result){
                $.mobile.loading( 'hide' );
                console.log('suscess');
            }
        })
    });
   
    $(".link-to-entry").click(function(){
        window.location.assign($(this).attr("href"));
    });
    var index = <?php echo ($last_key + 1); ?>;
    //alert(index);
    $("#add-new-record").on("click",function(){
        $.ajax({
            url : "<?php echo M_URL.'/'.$controller.'/expensive_add/'.$task_id ?>",
            success: function(result){
                //location.reload(true);
                var str = 
'<li id="list-'+ index + '" data-inset="false" data-iconpos="right" data-expanded-icon="arrow-u" data-collapsed-icon="arrow-d" data-role="collapsible" class="ui-li-static ui-body-inherit">' + 
    '<h2>'+
            '<div class="ui-block-a" style="width:30%">New' + 
            ' </div>' + 
            
            '<div class="ui-block-b" style="width:60%">New' +
            '</div>' + 
    '</h2>'+

    '<ul data-role="listview" data-theme="b">' + 
        '<li>' + 
            '<div class="ui-block-a" style="width:30%"><b>Heading</b></div>' + 
            '<div class="ui-block-b" style="width:70%">' + 
                '<input type="text" value="" data-theme="a" key="'+ index +'" name="heading">' + 
            '</div>' + 
        '</li>' + 

        '<li>' +
            '<div class="ui-block-a" style="width:30%"><b>Details</b></div>' + 
            '<div class="ui-block-b" style="width:70%" >' +
                '<input type="text" value="" data-theme="a" key="'+ index +'" name="details">' +
            '</div>' + 
        '</li>' +

        '<li>' + 
            '<a href="#popupDialog" class="callDelete" data-id="'+ index +'" data-rel="popup" key="'+ index +'" data-transition="pop" data-position-to="window" >Delete</a>' + 
        '</li>' + 

    '</ul>' +

'</li>';
                $("#list-view").append(str);
                $("#list-view").listview().trigger("create");
                $("#list-view").stop().animate({ scrollTop : 0 }, 500);
                //alert(index);
                //alert(str);
                index = index + 1;
                //$('#list-2[data-role=collapsible]').collapsible({refresh:true});
            }
        })
    });
})

</script>