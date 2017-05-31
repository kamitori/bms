<?php
    foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
        echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
    }
?>
<p class="clear"></p>
<script type="text/javascript">
$('.viewprice_material_width, .viewprice_material_length, .viewprice_bleed', '#block_full_productions').change(function() {
    var name = $(this).attr('name');
    var value = $(this).val();
    var key = name.split('_');
        key = key[key.length - 1];
    name = name.replace('_'+key, '');
    $.ajax({
        url: '<?php echo URL.'/salesorders/productions_auto_save' ?>',
        type: 'POST',
        data: {
            key: key,
            name: name,
            value: value
        },
        success: function(result) {
            var result = $.parseJSON(result);
            if (result.status == 'ok') {
                $('#txt_material_needed_'+ key).text(result.material_needed);
                $('#listbox_productions_'+ key +' li:last').text(result.cutting_policy);
            }
        }
    });
});
</script>