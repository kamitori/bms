<?php
    if(isset($arr_settings['relationship'][$sub_tab]['block']))
    foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
        echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
    }
?>
<p class="clear"></p>
<script type="text/javascript">
    $(function(){
        $('#bt_add_tasks').click(function(){
            var ids = $("#mongo_id").val();
            $.ajax({
                url:"<?php echo URL;?>/companies/tasks_add/" + ids,
                timeout: 15000,
                success: function(html){
                     location.replace(html);
                }
            });
        })

        $(".del_tasks").click(function(){
            var names = $(this).attr("id");
            var ids = names.split("_");
            ids = ids[ ids.length - 1];
            confirms( "Message", "Are you sure you want to delete?",
            function(){
                $.ajax({
                    url: '<?php echo URL; ?>/companies/tasks_delete/' + ids,
                    success: function(html){
                        if(html == "ok"){
                            $(".del_tasks_" + ids).fadeOut();
                            reload_subtab('tasks');
                        }else{
                            console.log(html);
                        }
                    }
                });
            },function(){
                //else do somthing
            });
        });
    })
</script>