<?php if(!in_array($controller,array('homes'))){ ?>
<div data-role="navbar">
    <ul>
        <li>
        	<?php if(!in_array($action,array('entry','lists'))){ ?>
        	<a style="display: block;" id="add-new-record" href="javascript:void(0)">Add</a>
        	<?php } else { ?>
        	<a style="display: block;" data-ajax="false" href="<?php echo M_URL.'/'.$controller.'/add/' ?>">New</a>
        	<?php } ?>
        </li>

        <li>
            <a style="display: block;" id="delete-record" href="#popupDialog_entry" class="callDelete <?php if($action=='lists') {?>ui-state-disabled<?php } ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b">Delete</a>
        </li>

        <li><a id="find-record" style="display: block;" data-ajax="false" href="<?php echo M_URL.'/'.$controller.'/entry_search/' ?>" <?php if($action=='entry_search') {?>class="ui-btn-active"<?php } ?>>Find</a></li>
        <li><a id="entry-record" style="display: block;" <?php if(!isset($no_redirect)){ ?>data-ajax="false" href="<?php echo M_URL.'/'.$controller.'/entry/' ?>" <?php } else{ ?> href="#main_page" <?php } ?> <?php if($action=='entry') {?>class="ui-btn-active ui-state-persist"<?php } ?>>Entry</a></li>
        <li><a id="list-record" style="display: block;" href="<?php echo M_URL.'/'.$controller.'/lists' ?>" rel="external" <?php if($action=='lists') {?>class="ui-btn-active"<?php } ?>>List</a></li>
    </ul>
</div>
<?php } ?>


<div data-role="popup" id="popupDialog_entry" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="max-width:300px;">
    <div role="main" class="ui-content">
        <h2 class="ui-title">Are you sure you want to delete this record entry?</h2>
    <p>This action cannot be undone.</p>
        <input type="hidden" id="hiddenId" value="" />
        <a href="#" id="cancel_button" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back">Cancel</a>
        <a href="#" id="delete_button_entry" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back" data-transition="flow">Delete</a>
    </div>
</div>
<script type="text/javascript">
    $('#delete_button_entry').click(function(){
        window.location.assign("<?php echo M_URL.'/'.$controller.'/delete/'.(isset($mongoid) ? $mongoid : ''); ?>");return false;
    });
</script>