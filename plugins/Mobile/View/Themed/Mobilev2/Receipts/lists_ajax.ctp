<?php foreach ($arr_receipts as $value): ?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo URL; ?>/mobile/receipts/entry/<?php echo $value['_id']; ?>"><?php echo $value['code']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo isset($value['salesaccount_name'])?$value['salesaccount_name']:'';?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
        	<li>
        		<div class="ui-block-a" style="width: 30%"><b>Receipt no</b></div>
        		<div class="ui-block-b" style="width:70%">
        			<?php echo $value['code']; ?>
        		</div>
        	</li>

        	<li>
        		<div class="ui-block-a" style="width: 30%"><b>Account</b></div>
        		<div class="ui-block-b" style="width:70%">
        			<?php echo isset($value['salesaccount_name'])?$value['salesaccount_name']:'';?>
        		</div>
        	</li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Date</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $this->Common->format_date($value['receipt_date']->sec);?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 30%"><b>Paid by</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['paid_by'];?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Our rep</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['our_rep'];?>
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