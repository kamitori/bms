<div class="reset_font">
    <div data-role="navbar" data-grid="d">
        <ul class="nav_reset">
            <li><a style="display: block;" data-ajax="false" href="<?php echo M_URL.'/'.$controller.'/add/' ?>">New</a></li>
            <li><a style="display: block;" data-ajax="false" href="<?php echo M_URL.'/'.$controller.'/delete/' ?><?php echo isset($mongoid) ? $mongoid : '';?>">Delete</a></li>
            <li><a style="display: block;" data-ajax="false" href="<?php echo M_URL.'/'.$controller.'/entry_search/' ?>" <?php if($action=='entry_search') {?>class="ui-btn-active"<?php } ?>>Find</a></li>
            <li><a style="display: block;" <?php if(!isset($no_redirect)){ ?>data-ajax="false" href="<?php echo M_URL.'/'.$controller.'/entry/' ?>" <?php } else{ ?> href="#main_page" <?php } ?> <?php if($action=='entry') {?>class="ui-btn-active ui-state-persist"<?php } ?>>Entry</a></li>
            <li><a style="display: block;" href="#listPage" <?php if($action=='lists') {?>class="ui-btn-active"<?php } ?>>List</a></li>
            <li><a style="display: block;" data-ajax="false" href="<?php echo M_URL.'/'.$controller.'/options' ?>" <?php if($action=='options') {?>class="ui-btn-active"<?php } ?>>Option</a></li>
        </ul>
    </div>
</div>