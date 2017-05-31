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

		<?php if( $this->Session->check($controller.'_entry_search_cond') ){ ?>
        	<li><a href="<?php echo URL; ?>/<?php echo $controller; ?>/entry_search_all"><?php echo translate('Find all');?></a></li>

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

<div style="width: 76%; position: fixed;z-index: 800;top: 75px; margin-left: 7%;"  class="input_style">
	<form id="search_form" method="POST" action="javascript:void(0)">
	<div style="color: white; width: 17.3%; text-align: right;  text-indent: -1%; padding-right: 1%">From</div>
	<div style="width: 16%; text-align: right">
		<input type="text" name="data[Job][work_end_from]" id="date_from"  class="date_search_list" readonly="readonly" value="<?php echo (isset($date_from) ? $date_from : ''); ?>" />
		<div style="color: white; border: 0px; text-indent: 1%;">To</div>
	</div>
	<div style="width: 11.2%">
		<input type="text" name="data[Job][work_end_to]" id="date_to" class="date_search_list" readonly="readonly" value="<?php echo (isset($date_to) ? $date_to : ''); ?>"  style="width: 100%" />
	</div>
	</form>
	<div style="width: 9.9%; text-align: center;">
		<input type="button" onclick="search_date();" value="Search" style="margin-left: 4%; width: 96%;">
	</div>
	<div style="width: 14.3%">
		<input type="text" class="right_txt" id="quotations_sum" readonly="readonly" value="" style="width: 94%; margin-left: 2%;  margin-right: 2%" />
	</div>
	<div style="width: 14.5%">
		<input type="text" class="right_txt" id="salesorders_sum" readonly="readonly" value="" style="width: 94%; margin-left: 2%;  margin-right: 2%" />
	</div>
	<div style="width: 14.4%">
		<input type="text" class="right_txt" id="salesinvoices_sum" readonly="readonly" value="" style="width: 94%; margin-left: 2%;  margin-right: 2%" />
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
		url: "<?php echo URL.'/jobs/get_sum_jobs' ?>",
		success: function(result){
			result = JSON.parse(result);
			$("#quotations_sum").val(result.quotations_sum);
			$("#salesorders_sum").val(result.salesorders_sum);
			$("#salesinvoices_sum").val(result.salesinvoices_sum);
		}
	})
})
function search_date(){
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/entry_search',
		type:"POST",
		data: $("#search_form").serialize(),
		success: function(rets){
			if(rets=="no")
				alerts("Message","No record.");
			else
				location.reload();
		}
	});
}
</script>