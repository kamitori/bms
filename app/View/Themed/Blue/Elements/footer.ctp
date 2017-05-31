<?php if(!isset($print_pdf)): ?>
<div id="footer">
	<div class="bg_footer footer_res3">

		<?php echo $this->element('footer_right'); ?>

		<?php if(isset($action) && ($action == 'entry' || $action == 'lists')){ ?>

			<?php if( isset($arr_info_footer) ){ ?>
			<ul class="text_foot float_left">
				<li><span class="bold"><?php echo translate('Created'); ?>:</span> <?php echo $arr_info_footer['date_created']; ?></li>
				<li><?php echo $arr_info_footer['date_created_hour']; ?></li>
				<li><span class="bold"><?php echo translate('By'); ?>:</span> <?php echo $arr_info_footer['created_by']; ?></li>
			</ul>

			<ul class="text_foot float_left">
				<li><span class="bold"><?php echo translate('Modified'); ?>:</span> <?php echo $arr_info_footer['date_modified']; ?></li>
				<li><?php echo $arr_info_footer['date_modified_hour']; ?></li>
				<li><span class="bold"><?php echo translate('By'); ?>:</span> <?php echo $arr_info_footer['modified_by']; ?></li>
			</ul>
			<?php } ?>

		<?php }else{?>

			<p style="float:left;border-right-width:1px;padding:7px 30px 8px;text-align:center; margin-right:1px;color: #333333;  font-weight: bold;  font-size: 11px;line-height: 100%;  font-family: Arial;">
            	#103, 3016 - 10th Ave. NE, Calgary, Alberta, Canada T2A 6K4 Tel: 403.291.2244 Fax: 403.291.2246
           	</p>


			<?php if(isset($action) && !in_array($action, array('dashboard','viewchart','lists')) ){?>
			<p style="float: left;border-right-width: 1px; padding: 7px 50px 8px;  text-align: center;margin-right: 1px;  color: #333333;font-weight: bold;  font-size: 11px; line-height: 100%;  font-family: Arial;">
				Copyright © 2013 Anvy Digital All rights reserved.
           </p>
           <?php }?>

		<?php }?>

	</div>
</div>
<?php endif; ?>
<!--END Footer -->