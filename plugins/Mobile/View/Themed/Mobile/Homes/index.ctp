<div data-role="main" class="ui-content">
    <form >
      <fieldset data-role="collapsible" data-theme="b" data-content-theme="b">
        <legend>Alert</legend>
        <div data-role="controlgroup">
          <div data-role="main" class="ui-content">
            <table data-role="table" data-mode="columntoggle" class="ui-responsive ui-shadow ui_table_cs" id="home_table">
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
                    <tr title="view detail">
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
                        <tr title="view detail">
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
                        echo '<td></td><td>No data</td>';
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </fieldset>
      <!-- <fieldset data-role="collapsible" data-theme="b" data-content-theme="b">
        <legend>Dashboard</legend>
        <div data-role="controlgroup">
          This is Dashboard content [IN CONSTRUCT]
        </div>
      <input type="submit" data-inline="true" value="Submit" data-theme="b">
      </fieldset>
      <fieldset data-role="collapsible" data-theme="b" data-content-theme="b">
        <legend>Calendar</legend>
        <div data-role="controlgroup">
          This is Dashboard Calendar [IN CONSTRUCT]
        </div>
      <input type="submit" data-inline="true" value="Submit" data-theme="b">
      </fieldset>
      <fieldset data-role="collapsible" data-theme="b" data-content-theme="b">
        <legend>Setup</legend>
        <div data-role="controlgroup">
           This is Dashboard Setup [IN CONSTRUCT]
        </div>
      <input type="submit" data-inline="true" value="Submit" data-theme="b">
      </fieldset>
      <fieldset data-role="collapsible" data-theme="b" data-content-theme="b">
        <legend>Info / Help</legend>
        <div data-role="controlgroup">
          This is Dashboard Info / Help [IN CONSTRUCT]
        </div>
      <input type="submit" data-inline="true" value="Submit" data-theme="b">
      </fieldset> -->
    </form>
</div>
<script type="text/javascript">
  $(function(){
    $(".ui-collapsible-heading-toggle:first").click();
  })
</script>