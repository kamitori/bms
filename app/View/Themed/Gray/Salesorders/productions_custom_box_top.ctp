<div style="float:right; margin-right: 15px">
    <a href="javascript:void(0)" id="asset_tag_report">
        <input class="btn_pur" onclick="generate_production()" type="button" value="Generate new production" style="width:99%;">
    </a>
</div>
<script type="text/javascript">
function generate_production()
{
    $.ajax({
        url: '<?php echo URL.'/salesorders/generate_productions' ?>',
        success: function(result) {
            if (result == 'ok') {
                $('#productions.active').trigger('click');
            } else {
                alerts('Message', result);
            }
        }
    })
}
</script>