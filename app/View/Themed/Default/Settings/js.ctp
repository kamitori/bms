<script type="text/javascript">
$(function(){
    // initialize widgets
    kendo.init($("#jt_setup_salesorders_status"));
    // log events
    $(".ColorPicker").each(function(){
        var colorPicker = $(this).data("kendoColorPicker");

        colorPicker.bind({
            change: function(e) {console.log(e.value);console.log(e);
            	// save color when there is any change
            	$.ajax({
					url: "<?php echo URL; ?>/settings/salesorders_status_pickcolor/" + this.element.attr("rel") + "/" + this.element.attr("rel_idx") + "/" + e.value.substr(1),
					timeout: 15000,
					success: function(html){
						if(html != "ok")alerts("Error: ", html);
					}
				});
				return false;
            }
        });
    });
});
</script>