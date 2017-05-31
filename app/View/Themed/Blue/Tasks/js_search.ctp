<script type="text/javascript">
    $(function() {
        $(window).keypress(function(e) {
            if( e.keyCode == 13 ){
                mainjs_entry_search_ajax('<?php echo $controller; ?>');
            }
        });
    });
</script>