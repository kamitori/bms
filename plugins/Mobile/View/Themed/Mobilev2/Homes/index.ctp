<form >
    <fieldset>
        <legend>Alert</legend>
        <table data-role="table" class="ui-responsive" id="home_table">
            <thead>
                <tr>
                    <th data-priority="6">ID</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th data-priority="1">Time</th>
                    <th data-priority="2">Detail</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $STT = 0;
                    foreach ($arr_data['alert']['task'] as $value) {
                    $STT += 1;
                ?>
                <tr class="view-detail" data-url="/tasks/entry/<?php echo $value['_id']; ?>" title="view detail">
                    <td><?php echo $STT; ?></td>
                    <td align="left"><?php echo translate('Task'); ?></td>
                    <td align="center">
                        <?php echo $this->Common->format_date($value['work_end']->sec, false); ?>
                    </td>
                    <td align="center"><?php echo date( "H:i", $value['work_end']->sec); ?></td>
                    <td><?php echo $value['name']; ?></td>
                </tr>
                <?php } ?>
                <?php
                    foreach ($arr_data['alert']['communication'] as $value) {
                    $STT += 1;
                ?>
                <tr class="view-detail" title="view detail">
                    <td><?php echo $STT; ?></td>
                    <td align="left"><?php echo translate('Message'); ?></td>
                    <td align="center">
                        <?php echo $this->Common->format_date($value['date_modified']->sec, false); ?>
                    </td>
                    <td align="center"><?php echo date( "H:i", $value['date_modified']->sec); ?></td>
                    <td ><?php echo $value['content']; ?></td>
                </tr>
                <?php } ?>
                <?php
                    if($STT==0)
                        echo '<td colspan="5">No data</td>';
                ?>
            </tbody>
        </table>
    </fieldset>
</form>
<script type="text/javascript">
    $(".view-detail").click(function(){
        var url = "<?php echo M_URL ?>"+$(this).attr("data-url");
        window.location.assign(url);
    });
</script>