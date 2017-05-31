<style type="text/css" media="screen">
    #sort li label{cursor: pointer;}
</style>
<script type="text/javascript">
    <?php if(!$this->request->is('ajax')){ ?>
    $(function() {
        mainjs_pagination_sort();
    });
    <?php } ?>
</script>
