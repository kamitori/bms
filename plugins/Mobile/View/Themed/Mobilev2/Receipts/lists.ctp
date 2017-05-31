<style type="text/css">
    .ui-popup-screen.in {
        position: fixed;
    }
</style>
<ul id="list-view" data-role="listview" data-inset="true">
    <?php echo $this->element('../'.ucfirst($controller).'/lists_ajax'); ?>
</ul>
<a id="loadmore" data-role="button">Load more</a>
<input type="hidden" id="offsetRecord" value="0" />
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
        $("#list-view").on("click",".callDelete",function(){
            var value = $(this).attr("data-id");
            $("#hiddenId").val(value);
        });
        $("#loadmore").click(function() {
            var offset = eval($("#offsetRecord").val());
            offset+= 20;
            $.ajax({
                url: "<?php echo M_URL.'/'.$controller.'/lists/content_only' ?>",
                type: "POST",
                data: {"offset":offset},
                success: function(result){
                    $("#offsetRecord").val(offset);
                    $("#list-view").append(result);
                    $("#list-view").listview().trigger("create");
                }
            })

        });
        $("#delete_button").click(function(){
            var id = $("#hiddenId").val();
            $.ajax({
                url : "<?php echo M_URL.'/receipts/delete/' ?>"+id,
                success: function(result){
                    $("#list-"+id).fadeOut().remove();
                    $("#hiddenId").val("");
                }
            })
        });
    })
</script>