<?php foreach ($arr_locations as $value): ?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:40%">
                <a class="link-to-entry" href="<?php echo URL; ?>/mobile/locations/entry/<?php echo $value['_id']; ?>"><?php echo $value['code']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo isset($value['name'])?$value['name']:'' ;?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
        	<li>
        		<div class="ui-block-a" style="width: 40%"><b>Ref no</b></div>
        		<div class="ui-block-b" style="width:60%">
        			<?php echo $value['code']; ?>
        		</div>
        	</li>
            
            <li>
                <div class="ui-block-a" style="width:40%"><b>Location</b></div>
                <div class="ui-block-b" style="width:60%"><?php echo $value['name']; ?></div>
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
                <div class="ui-block-a" style="width: 40%"><b>Contact</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['contact_name'])?$value['contact_name']:'';?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 40%"><b>Phone</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['company_phone'])?$value['company_phone']:'';?>
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