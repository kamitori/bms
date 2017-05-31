<?php foreach ($arr_communications as $value): ?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo URL; ?>/mobile/communications/entry/<?php echo $value['_id']; ?>"><?php echo $value['code']; ?></a>
            </div>
             <div class="ui-block-b" style="width:60%"><?php echo $value['comms_type'];?></div>
            <div class="ui-block-b" style="width:60%"><?php echo isset($value['name'])?$value['name']:'';?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
        	<li>
        		<div class="ui-block-a" style="width: 30%"><b>Ref no</b></div>
        		<div class="ui-block-b" style="width:70%">
        			<?php echo isset($value['code'])?$value['code']:''; ?>
        		</div>
        	</li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>File type</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php if (isset($value['comms_type'])) echo $value['comms_type']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Date</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $this->Common->format_date($value['date_modified']->sec);?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>From</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo isset($value['contact_from'])?$value['contact_from']:'';?>
                </div>
            </li>

        	<li>
        		<div class="ui-block-a" style="width: 30%"><b>Company</b></div>
        		<div class="ui-block-b" style="width:70%">
        			<?php echo isset($value['company_name'])?$value['company_name']:'';?>
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