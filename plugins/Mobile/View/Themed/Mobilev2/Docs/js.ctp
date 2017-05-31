<?php echo $this->element('js'); ?>
<script type="text/javascript">
	$(function() {
        $(".container").delegate("input,select","change",function(){
            doc_auto_save();
        });
    });

    function doc_auto_save(){
    	loading("Saving...");
        $.ajax({
            url:"<?php echo M_URL.'/'.$controller; ?>/auto_save",
            type:"POST",
            data:$(".<?php echo $controller; ?>_form_auto_save","#main-page").serialize(),
            success: function(result){
                $.mobile.loading( 'hide' );
                if(result!='ok')
                    alerts("Error",result);
            }
        });
    }
</script>