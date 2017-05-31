<div class="menu">
    <div class="bg_menu" style="border-bottom: solid 3px #fff;">
        <span class="view_small_title float_left">&nbsp;<!--Click an arrow to view below.--></span>
    </div>
    <div class="logo_inner">
    </div>
</div>

<div id="content">&nbsp;</div>
<?php
    $url = URL.'/theme/'.$theme.'/images/Logo_icon_new.png';
    if(isset($logo['image_path']))
        $url = $logo['image_path'];
?>
<div id="new_homepage_box" style="background: url('<?php echo  $url; ?>') no-repeat;">
    <div id="new_homepage_box_menu">
        <ul class="navleft_footer">
            <li style="width:18%;">
            	<a class="center_txt" style="border-left: none;" href="<?php echo URL;?>/users/logout" onclick="window.open(window.location, '_self').close();">
                	<?php echo translate('Exit'); ?>
               	</a>
            </li>
            <li style="width:27%;">
            	<a href="<?php echo URL;?>/users/logout">
					<?php echo translate('Re-login'); ?>
                </a>
            </li>
            <?php if( $this->Common->check_permission('setup_@_entry_@_view', $arr_permission) ){ ?>
            <li style="width:20%;">
            	<a href="<?php echo URL;?>/settings" <?php if($controller == 'settings'){ ?>class="active"<?php } ?>>
					<?php echo translate('Setup'); ?>
               	</a>
            </li>
            <?php } ?>
            <li style="width:35%;">
            	<a href="<?php echo URL;?>/" <?php if($controller == 'infohelps'){ ?>class="active"<?php } ?>>
					<?php echo translate('Info'); ?> / <?php echo translate('Help'); ?>
                </a>
           	</li>
        </ul>
    </div>
</div>
