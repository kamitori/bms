<?php foreach ($arr_docs as $value): ?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:30%">
                <a class="link-to-entry" href="<?php echo URL; ?>/mobile/docs/entry/<?php echo $value['_id']; ?>"><?php echo $value['no']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo $value['name'];?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
        	<li>
        		<div class="ui-block-a" style="width: 30%"><b>Doc no</b></div>
        		<div class="ui-block-b" style="width:70%">
        			<?php echo $value['no']; ?>
        		</div>
        	</li>

        	<li>
        		<div class="ui-block-a" style="width: 30%"><b>Document name</b></div>
        		<div class="ui-block-b" style="width:70%">
        			<?php echo $value['name'];?>
        		</div>
        	</li>

            <li>
                <div class="ui-block-a" style="width: 30%"><b>Category</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['category'];?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Ext</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['ext'];?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>File type</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php if (isset($value['type']) && isset($arr_docs_type[$value['type']])) echo $arr_docs_type[$value['type']]; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Description</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $value['description'];?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:30%"><b>Date</b></div>
                <div class="ui-block-b" style="width:70%">
                    <?php echo $this->Common->format_date($value['date_modified']->sec);?>
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