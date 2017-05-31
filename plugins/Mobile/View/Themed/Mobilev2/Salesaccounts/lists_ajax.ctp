<?php foreach ($arr_salesaccounts as $value): ?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:40%">
                <a class="link-to-entry" href="<?php echo URL; ?>/mobile/salesaccounts/entry/<?php echo $value['_id']; ?>"><?php echo $value['no']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo isset($value['company_name'])?$value['company_name']:'' ;?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
        	<li>
        		<div class="ui-block-a" style="width: 40%"><b>Ref no</b></div>
        		<div class="ui-block-b" style="width:60%">
        			<?php echo $value['no']; ?>
        		</div>
        	</li>

            <li>
                <div class="ui-block-a" style="width:40%"><b>Type</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php if (isset($value['type']) && isset($arr_salesaccounts_type[$value['type']])) echo $arr_salesaccounts_type[$value['type']]; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 40%"><b>Account name</b></div>
                <div class="ui-block-b" style="width:60%">
                        <?php if (isset($value['name'])) { ?>
                        <?php echo $value['name']; ?>
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
        		<div class="ui-block-a" style="width: 40%"><b>Our rep</b></div>
        		<div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['contact_name'])?$value['contact_name']:'';?>
        		</div>
        	</li>

            <li>
                <div class="ui-block-a" style="width: 40%"><b>Status</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['status'])?$value['status']:'';?>
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