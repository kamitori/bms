<?php echo $this->element('entry_tab_option',array('no_redirect'=>true)); ?>
<style type="text/css">
    .late{
        color: #f00;
        font-weight: 900;
    }
    .ui_table_customes a{
        color: #000 !important;
        font-weight: normal;
    }
    #confirm p {
        text-align: center;
        font-size: inherit;
        margin-bottom: .75em;
    }
    #confirm{
        padding: 1em;
    }
</style>
<table data-role="table" data-mode="columntoggle" class="ui_table_customes ui_table_list popup ui-responsive ui-shadow ui-table ui-table-columntoggle table-stroke" id="list_<?php echo $controller?>">
    <thead>
        <tr>
            <th><?php echo __('#'); ?></span></th>
            <th data-priority="1" ><?php echo __('Task'); ?></th>
            <th data-priority="4" ><?php echo __('Job no'); ?></th>
            <th data-priority="4" style="text-align:center;"><?php echo __('Responsible'); ?></th>
            <th data-priority="2" style="text-align:center;"><?php echo __('Type'); ?></th>
            <th data-priority="3" style="text-align:center;"><?php echo __('Work Start'); ?></th>
            <th data-priority="3" style="text-align:center;"><?php echo __('Work End'); ?></th>
            <th data-priority="1" ><?php echo __('Status'); ?></th>
            <th data-priority="2" style="text-align:center;"><?php echo __('Late'); ?></th>
            <th style="text-align:center;"></th>
        </tr>
    </thead>
    <tbody>
        <?php echo $this->element('../'.ucfirst($controller).'/lists_ajax'); ?>
    </tbody>
</table>
<a id="loadmore" data-role="button">Load more</a>
<input type="hidden" id="offsetRecord" value="0" />
<div id="confirm" class="ui-content" data-role="popup" data-theme="a">
    <p id="question">Are you sure to delete this record?</p>
    <div class="ui-grid-a">
        <div class="ui-block-a">
            <a id="yes" class="ui-btn ui-corner-all ui-mini ui-btn-a" data-rel="back">Yes</a>
        </div>
        <div class="ui-block-b">
            <a id="cancel" class="ui-btn ui-corner-all ui-mini ui-btn-a" data-rel="back">Cancel</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $("#loadmore").click(function() {
            loading();
            var offset = eval($("#offsetRecord").val());
            offset+= 20;
            $.ajax({
                url: "<?php echo M_URL.'/'.$controller.'/lists/content_only' ?>",
                type: "POST",
                data: {"offset":offset},
                success: function(result){
                    $.mobile.loading("hide");
                    $("#offsetRecord").val(offset);
                    $("#list_<?php echo $controller ?> > tbody").append(result);
                    $( "#list_<?php echo $controller; ?>" ).table( "refresh" );
                }
            })

        });
    })
</script>