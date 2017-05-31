<?php echo $this->element('menu_calendar'); ?>
<?php echo $this->element('../Salesorderdds/calendar_css'); ?>

<style type="text/css">
ul.list_rs li {
	overflow: hidden;
}
ul.list_day_rs li.active:after {
    color: rgba(0, 0, 0, 0);
    content: "x";
    cursor: pointer;
    right: 48px;
    position: absolute;
    z-index: 999;
}
ul.list_day_rs li:hover:after, ul.list_day_rs li:active:after, ul.list_day_rs li.active:after {
    color: #6A1515 !important;
}
#calendar-left ul li {
	position: relative;
}
</style>

<div id="calendar-left">
	<ul id="have_data" class="list_day_rs">
		<?php
		foreach ($arr_contact as $key => $value) { ?>
			<li id="<?php echo $key; ?>" onclick="salesorderdds_calendar_onchange_type(this, '<?php echo $key; ?>')">
				<?php echo $value; ?>
			</li>
		<?php } ?>
	</ul>

	<ul id="no_data" class="list_day_rs" style="display:none">
		<li><?php echo translate('There are no resources is used'); ?></li>
	</ul>
</div>

<div id="calendar-right">
	<div class="top_header_inner2 fx_ul5 " style="position: absolute;">
		<ul class="dent_top list_day22">
			<li class="fix_width"><?php echo date( 'D, F d', $date_from_sec ); ?></li>
			<li class="fix_width"><?php echo date( 'D, F d', $date_from_sec + DAY ); ?></li>
			<li class="fix_width"><?php echo date( 'D, F d', $date_from_sec + DAY*2 ); ?></li>
			<li class="fix_width"><?php echo date( 'D, F d', $date_from_sec + DAY*3 ); ?></li>
			<li class="fix_width"><?php echo date( 'D, F d', $date_from_sec + DAY*4 ); ?></li>
			<li class="fix_width"><?php echo date( 'D, F d', $date_from_sec + DAY*5 ); ?></li>
			<li class="fix_width bor_mt"><?php echo date( 'D, F d', $date_from_sec + DAY*6 ); ?></li>
		</ul>
		<p class="clear"></p>
	</div><!--END top_header_inner -->
	<div id="content_of_calendar" class="" style="height:80%; overflow-y: auto;overflow-x: hidden;padding-top: 21px;">

		<?php

		$check_in_current_week = false;
		$time_current = strtotime('now');
		if( $date_from_sec < $time_current && $time_current < $date_to_sec )$check_in_current_week = true;

		$i=0;
		foreach ($arr_salesorderdds as $key => $arr_salesorderdd) {
			$i++;
		?>

		<ul class="list_rs" id="ul_cal_<?php echo $i; ?>">
			<?php
			$check_empty_ul_row = true;
			for ($j=0; $j < 7; $j++) {  ?>
			<li class="<?php if( $j == 0 ){ ?>child_bor<?php } ?> <?php if( $check_in_current_week && date('N') == ($j+1) ){ ?>active<?php } ?>">
				<?php
				if( isset($arr_salesorderdd['_id']) ){
					$time_current_day = $date_from_sec+$j*DAY; //echo $arr_salesorderdd['name']; //echo date('d/m/Y',$arr_salesorderdd['salesorder_date']->sec);
					if($time_current_day >= $arr_salesorderdd['salesorder_date']->sec && ( !is_object($arr_salesorderdd['payment_due_date']) || $time_current_day <= $arr_salesorderdd['payment_due_date']->sec ) ){

						$check_empty_ul_row = false;

					?>
					<div class="box_3 <?php echo str_replace(' ', '_', $arr_salesorderdd['status']); ?> <?php echo $arr_salesorderdd['our_rep_id']; ?>" ondblclick="salesorderdds_go_to_entry('<?php echo $arr_salesorderdd['_id']; ?>')" <?php if( $arr_salesorderdd['our_rep_id'] != $_SESSION['arr_user']['contact_id'] ){ ?>style="display:none"<?php } ?>>
						<span class="title_nv">
							<h5><?php echo $arr_salesorderdd['name']; ?></h5>
						</span>
						<p><?php
							if( trim($arr_salesorderdd['company_name']) != '' ){
								echo $arr_salesorderdd['company_name'];
							}
						?></p>
					</div>

				<?php }
				} ?>
			</li>
			<?php }  ?>
		</ul>

		<?php } ?>

		<?php
		$for_next = 24 - $i - 1;
		if( $for_next > 0 ){
			for ($i=0; $i < $for_next; $i++) { ?>
			<ul class="list_rs">
				<?php for ($j=0; $j < 7; $j++) {  ?>
				<li class="<?php if( $j == 0 ){ ?>child_bor<?php } ?> <?php if( $check_in_current_week && date('N') == ($j+1) ){ ?>active<?php } ?>">
				</li>
				<?php }  ?>
			</ul>
			<?php }
		}
		?>


	</div>
	<p class="clear_height"></p>
</div>

<div style="clear:both;"></div>
<script type="text/javascript">
	function salesorderdds_go_to_entry(id){
		location.href = "<?php echo URL; ?>/salesorders/entry/" + id;
	}

	function salesorderdds_calendar_onchange_status(){
		var contain = $("#calendar-right");

		var status = $("#SalesorderStatusFilter").val();
		status = status.replace(" ","_");

		if( $.trim( status ).length < 1 ){
			$("li div", contain).show();
		}else{
			$("li div", contain).hide();
			$("li div." + status, contain).show();
		}

	}

	function salesorderdds_calendar_onchange_type(object, item_id) {

		if($(object).hasClass("active")){
			$("li", "#calendar-left").removeClass("active");
			$("div", "#content_of_calendar").show();

		}else{
			$("li", "#calendar-left").removeClass("active");
			$(object).addClass("active");

			$("div", "#content_of_calendar").hide();
			$("." + item_id).show();
		}
	}

	$(function(){
	    $("#<?php echo $_SESSION['arr_user']['contact_id'];?>").click();
	});
</script>