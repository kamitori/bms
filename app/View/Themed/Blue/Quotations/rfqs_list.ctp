<?php echo $this->element('../'.$name.'/tab_rfqs');?>
<div id="content">
	<div class="jt_ajax_note">Loading...</div>
    <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left">
            <h1>
                <span id="md_code">
                    <?php if(isset($subdatas[$quoteline]['code'])) echo $subdatas[$quoteline]['code'];?>
                </span>
                <span class="md_center">:</span>
                <span id="md_name">
                    <?php if(isset($subdatas[$quoteline]['products_name'])) echo $subdatas[$quoteline]['products_name'];?>
                </span>
             </h1>
        </div>
        <div class="jt_module_title float_right jt_t_right"><h1>&nbsp;</h1></div>
    </div>

    <div id="rfqs_list_form_auto_save">
        <!-- Add form -->
        <form class="form_<?php echo $controller;?>" action="" method="post" class="float_left">
            <div class="clear_percent">
               <!--Elememt1 -->
               <?php echo $this->element('../'.$name.'/'.$quoteline);?>
               <!--Elememt2 -->
               <?php echo $this->element('box',array('panel'=>$arr_subsetting['rfqs'],'key'=>'rfqs','arr_val'=>$arr_subsetting['rfqs']['rfqs'])); ?>
            </div>
            <div class="clear"></div>
        </form>
	</div>
    <?php echo $this->element('../'.$name.'/js'); ?>
</div>
<?php if(!$this->Common->check_permission($controller.'_@_entry_@_edit',$arr_permission)): ?>
<script type="text/javascript">
    $(function(){
        $(".jt_right_check","#container_rfqs").each(function(){
            $(this).remove();
        });
    });
</script>
<?php endif; ?>