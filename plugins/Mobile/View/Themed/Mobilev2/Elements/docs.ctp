<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true"><?php
    if(!empty($arr_docs)){
        foreach ($arr_docs as $value):
?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo URL; ?>/mobile/docs/entry/<?php echo $value['_id']; ?>"><?php echo $value['no']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo isset($value['status'])?$value['status'] : '';?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
        	<li>
        		<div class="ui-block-a" style="width: 30%"><b>Code</b></div>
        		<div class="ui-block-b" style="width:70%">
        			<?php echo isset($value['no']) ? $value['no'] : ''; ?>
        		</div>
        	</li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Document / file name</b></div>
                <div class="ui-block-b" style="width:70%">
                	<?php echo isset($value['name']) ? $value['name'] : ''; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Category </b></div>
                <div class="ui-block-b" style="width:70%">
                   <?php echo isset($value['category']) ? $value['category'] : ''; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Extension </b></div>
                <div class="ui-block-b" style="width:70%">
                   <?php echo isset($value['ext']) ? $value['ext'] : ''; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Location</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo isset($value['location']) ? $value['location'] : ''; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Description</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo isset($value['description']) ? $value['description'] : ''; ?>
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
        url : "<?php echo M_URL.'/'.$this->params["controller"].'/docs_delete/' ?>"+ids,
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

    $(".ui-li-static").delegate("input,select","change",function(){
        var names = $(this).attr("name");
        var inval = $(this).val();
        var ids = names.split("_");
        ids = ids[ids.length - 1];
        names = names.replace("_"+ids,"");
        names = names.replace("cb_","");
        if(names=='doc_default'){
            if($(this).is(':checked')){
                inval = 1;
            }else{
                inval = 0;
            }
        }
        save_sub_tab(names,inval,ids,'',function(){},"docs");
    });

    $(".link-to-entry").click(function(){
        window.location.assign($(this).attr("href"));
    });
})
</script>