<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_noteactivity)){
        foreach ($arr_noteactivity as $value):
?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo URL; ?>/mobile/docs/entry/<?php echo $value['_id']; ?>"><?php echo $value['type']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo $this->Common->format_date($value['_id']->getTimestamp());?></div>
        </h2>
        <ul data-role="listview" data-theme="b">

            <li>
                <div class="ui-block-a" style="width:30%"><b>Type</b></div>
                <div class="ui-block-b" style="width:70%">
                	<?php echo isset($value['type']) ? $value['type'] : ''; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Date</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $this->Common->format_date($value['_id']->getTimestamp()); ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>By</b></div>
                <div class="ui-block-b" style="width:70%">
                     <?php if (isset($value['created_by'])){
                          if (is_object($value['created_by'])) {
                              if(!isset($arr_contact_tmp))$arr_contact_tmp = array();
                              if( !isset($arr_contact_tmp[(string)$value['created_by']]) ){
                                  $arr_contact = $model_contact->select_one(array('_id' => $value['created_by']), array('_id', 'first_name', 'last_name'));
                                  if(isset($arr_contact['first_name'])){
                                      $arr_contact_tmp[(string)$value['created_by']] = $arr_contact['first_name'].' '.$arr_contact['last_name'];
                                      echo $arr_contact['first_name'].' '.$arr_contact['last_name'];
                                  }
                              }else{
                                  echo $arr_contact_tmp[(string)$value['created_by']];
                              }
                          }
                      }
                      ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Details</b></div>
                <div class="ui-block-b" style="width:70%">
                    <input type="text" name="details_<?php echo $value['_id'] ?>" id="details_<?php echo $value['_id'] ?>" data-theme="a" value="<?php echo isset($value['content']) ? $value['content'] : ''; ?>" />
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
<?php //echo $this->element('js_subtab'); ?>
<?php echo $this->element('js'); ?>
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
            url : "<?php echo M_URL.'/'.$controller.'/noteactivity_delete/' ?>"+ids,
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

    $(".ui-li-static .ui-input-text").delegate("input,select","change",function(){
        loading("Saving...");
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
                $.mobile.loading( 'hide' );
                console.log('suscess');
            }
        })
    });
    $("#add-new-record").on("click",function(){
        $.ajax({
            url : "<?php echo M_URL.'/'.$controller.'/noteactivity_add/' ?>",
            success: function(result){
                location.reload(true);
            }
        })
    });
    $(".link-to-entry").click(function(){
        window.location.assign($(this).attr("href"));
    });
})
</script>