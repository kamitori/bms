<div class="bg_menu">

	<ul class="menu_control float_left">

		<?php
		if($this->Common->check_permission($controller.'_@_entry_@_add', $arr_permission)):?>
			<li><a href="<?php echo URL; ?>/<?php echo $controller; ?>/add"><?php echo translate('New');?></a></li>
		<?php endif; ?>

		<?php if( $action == 'lists' ){  ?>
			<li><a href="<?php echo URL; ?>/<?php echo $controller; ?>/entry_search"><?php echo translate('Find');?></a></li>

		<?php }elseif( $action == 'entry' || $action == 'options'){ ?>
			<?php if( $action == 'entry' && (!isset($no_show_delete) || !$no_show_delete) ){ ?>
				<?php if($this->Common->check_permission($controller.'_@_entry_@_delete', $arr_permission) ):?>
					<li><a onclick='!delete_entry_http(this); return false;' href="<?php echo URL; ?>/<?php echo $controller; ?>/delete/<?php echo isset($this->data[$model]['_id'])?$this->data[$model]['_id']:'';?>"><?php echo translate('Delete');?></a></li>
				<?php endif; ?>
			<?php } ?>
			<li><a href="<?php echo URL; ?>/<?php echo $controller; ?>/entry_search"><?php echo translate('Find');?></a></li>

		<?php } ?>

		<?php if( $this->Session->check($controller.'_entry_search_cond') ||  $this->Session->check(ucfirst($controller).'_where')){ ?>
			<?php
				$find_all = 'entry_search_all';
				if(in_array($controller, array('companies','contacts','salesinvoices','salesorders','quotations')))
					$find_all = 'find_all';
			?>
        	<li><a href="<?php echo URL.'/'.$controller.'/'.$find_all ?>"><?php echo translate('Find all');?></a></li>

        <?php } ?>
        <?php if($action!='lists'){ ?>
        <li><a href="<?php echo URL.'/'.$controller.'/support/'; ?>" class="icon support"><?php echo  translate('Support');?></a></li>
        <li><a href="<?php echo URL.'/'.$controller.'/history/'; ?>" class="icon history end"><?php echo translate('History');?></a></li>
        <?php } ?>

        <li class="input_style">


        	<script>
            $(function() {
                $( ".date_search_list" ).datepicker({dateFormat: 'dd M, yy', changeMonth: true, changeYear: true, yearRange: "c-70:c+3" });
            });
        </script>
        </li>
	</ul>



	<ul class="menu_control2 float_right">
		<?php if( $this->Common->check_permission($controller.'_@_entry_@_view', $arr_permission) ): ?>
		<li><a href="<?php echo URL; ?>/<?php echo $controller; ?>/entry" class="<?php if($action == 'entry'){ ?>active<?php } ?>"><?php echo translate('Entry');?></a></li>
		<li><a href="<?php echo URL; ?>/<?php echo $controller; ?>/lists" class="<?php if($action == 'lists'){ ?>active<?php } ?>"><?php echo translate('List');?></a></li>
		<?php endif; ?>
		<?php if($this->Common->check_permission($controller.'_@_options_@_',$arr_permission,true)): ?>
		<li class="com_den"><a  href="<?php echo URL; ?>/<?php echo $controller; ?>/options" class="<?php if($action == 'options'){ ?>active<?php } ?>"><?php echo translate('Options');?></a></li>
		<?php endif; ?>
	</ul>
</div>

<div style="width: 76%; position: fixed;z-index: 800;top: 75px; margin-left: 58%;"  class="input_style">
	<div style="width: 14.4%">
		<input type="text" class="right_txt" id="salesinvoices_sum" readonly="readonly" value="" style="width: 94%;" />
	</div>

</div>

<script type="text/javascript">
function delete_entry_http(object){
	!confirms( "Message", "Are you sure you want to delete?",
		function(){ location.href = $(object).attr("href"); }
	);
}
$(function(){
	$.ajax({
		url: "<?php echo URL.'/salesinvoices/get_sum_orders' ?>",
		success: function(result){
			$("#salesinvoices_sum").val(result);
		}
	});
});
</script>