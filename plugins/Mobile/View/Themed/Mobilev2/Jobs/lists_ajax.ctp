<?php foreach ($arr_jobs as $value): ?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:40%">
                <a class="link-to-entry" href="<?php echo URL; ?>/mobile/jobs/entry/<?php echo $value['_id']; ?>"><?php echo $value['no']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo $value['name'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
        	<li>
        		<div class="ui-block-a" style="width: 40%"><b>Job no</b></div>
        		<div class="ui-block-b" style="width:60%">
        			<?php echo $value['no']; ?>
        		</div>
        	</li>

            <li>
                <div class="ui-block-a" style="width: 40%"><b>Company</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['company_id']) && is_object($value['company_id']) ) { ?>
                    <a data-ajax="false" href="<?php echo URL; ?>/mobile/companies/entry/<?php echo $value['company_id']; ?>">
                    <?php echo $value['company_name']; ?>
                    </a>
                    <?php } ?>
                </div>
            </li>

        	<li>
        		<div class="ui-block-a" style="width: 40%"><b>Job name</b></div>
        		<div class="ui-block-b" style="width:60%">
        			<?php echo $value['name'];?>
        		</div>
        	</li>

            <li>
                <div class="ui-block-a" style="width: 40%"><b>Status</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo $value['status'];?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:40%"><b>Type</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['type']) && isset($arr_jobs_type[$value['type']])) echo $arr_jobs_type[$value['type']]; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:40%"><b>Work Start</b></div>
                <div class="ui-block-b" style="width:60%"><?php echo $this->Common->format_date($value['work_start']->sec); ?></div>
            </li>
            <li>
                <div class="ui-block-a" style="width:40%"><b>Work End</b> </div>
                <div class="ui-block-b" style="width:60%"><?php echo $this->Common->format_date($value['work_end']->sec); ?></div>
            </li>
            <li>
                <div class="ui-block-a" style="width:40%"><b>Late</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php
                        if ( in_array( $value['status'], array('New', 'Confirmed')) && isset($value['work_end']) && is_object($value['work_end'])) {
                        	if ($value['work_end']->sec < strtotime('now')) {
                        		echo '<span style="color:red; font-weight:bold">X</span>';
                        	}
                        }
                        ?>
                </div>
            </li>

            <li>
            	<a href="#popupDialog" class="callDelete" data-id="<?php echo $value['_id']; ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b">Delete</a>
            </li>
        </ul>
    </li>
<?php endforeach ?>
<script type="text/javascript">
    $(".link-to-entry").click(function(){
        window.location.assign($(this).attr("href"));
        event.preventDefault();
    });
</script>