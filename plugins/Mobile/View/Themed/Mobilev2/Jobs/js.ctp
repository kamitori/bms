<?php echo $this->element('js'); ?>
<script type="text/javascript">
	$(function() {
        $(".container").delegate("input,select","change",function(){
            job_auto_save();
        });
    });

    function job_auto_save(){
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

    function after_choose_companies(id, name, key){
        $("#JobCompanyName").val(name);
        $("#JobCompanyId").val(id);
        backToMain();
        job_auto_save();
    }
</script>