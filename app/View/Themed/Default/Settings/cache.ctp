<style type="text/css">
#confirms_window .jt_confirms_window_ok{
	width: 15%;
	height: 18%;
	margin-top: 14%;
	margin-left: 80%;
}
.changePadding ul li{
	padding-left: 4px !important;
}
.clear_percent_40 {
width: 39%;
}
.clear_percent_11 {
width: 77%;
}
ul.setup_menu li a {
	text-overflow: ellipsis;
	overflow: hidden;
}
</style>
<div class="clear_percent">
	<div class="clear_percent_40 float_left">
		<div class="tab_1 full_width changePadding">
			<?php
				$arr_cache_delete = array('all' => 'All','select_option_vl' => 'All select option','arr_permission|arr_menu' => 'All permission','minimun' => 'Minimun product','tax' => 'All tax','line' => 'All line entry','salesinvoice' => 'Invoice','product' => 'Product','salesorder' => 'Salesorder','quotation' => 'Quotation');
				asort($arr_cache_delete);
			?>
			<span class="title_block bo_ra1" >
				<span class="fl_dent"><h4><?php echo translate('All cached'); ?></h4></span>
				<span class="title_block bo_ra1" style="width:34%; float:left; margin-right:8px; margin-top:-6px">
			        <input type="text" id="delete_cache" combobox_blank="1"  style="float:left; width: 135px;" />
			        <input type="hidden" id="delete_cacheId" />
			     	<script type="text/javascript">
						$(function () {
							$("#delete_cache").combobox(<?php if(isset($arr_cache_delete)) echo json_encode($arr_cache_delete);?>);
						});
					</script>
			    </span>
			    <a title="Delete cache" href="javascript:void(0)" onclick="delete_cache()" style="text-decoration: none; font-weight: bold;"><span style="margin-top: 1px;">X</span></a>
			</span>

			<ul class="ul_mag clear bg3">
				<li class="hg_padd center_text no_border"><?php echo translate('Cached'); ?></li>
			</ul>
			<div id="cache_height" class="container_same_category" style="height: 449px;overflow-y: auto">
				<ul class="find_list setup_menu">
				<?php $i = 1;$count = 0;
				foreach ($arr_cache as $key => $value) { $count += 1; ?>
					<li>
						<a class="clickfirst" href="javascript:void(0)" id="cache_<?php echo $value; ?>" onclick="cache_detail(this, '<?php echo $value; ?>')"><?php echo $value; ?></a>
					</li>
				<?php $i = 3 - $i;
				}
				echo '</ul>';

				if ($count < 20) {
				$count = 20 - $count;
				for ($j = 0; $j < $count; $j++) {
					$i = 3 - $i;
					?>
					<ul class="find_list">
					</ul>
					<?php
				}
			}
			?>

			</div>
			<span class="title_block bo_ra2"></span>
		</div><!--END Tab1 -->
	</div>
	<div class="clear_percent_9_arrow float_left">
		<div class="full_width box_arrow">
			<span class="icon_emp" style="cursor:default"></span>
		</div>
	</div>
	<div class="float_left" style="width: 56%" id="cache_detail">
		<!-- Detail -->
	</div>
</div>

<style type="text/css">
#cache_height ul:hover, #cache_height ul:hover input{
	background-color: #B8B8B8;
}
</style>

<script type="text/javascript">
	$(window).load(function() {
		$('.container_same_category').mCustomScrollbar({
			scrollButtons:{
				enable:false
			}
		});
	});
</script>

<script type="text/javascript">

	$(function(){
		$('.container_same_category').mCustomScrollbar({
			scrollButtons:{
				enable:false
			}
		});
		$(".clickfirst:first", "#cache_height").click(); // click menu li dau tien khi page load xong
	});

	// click cot 2
	function cache_detail(object, name){

		$("#cache_height a").removeClass("active").parents("li").removeClass("active");
		$(object).addClass("active").parents("li").addClass("active");

		get_detail(name);
	}

	function get_detail(name){
		$.ajax({
			url: '<?php echo URL; ?>/settings/cache_detail/' + name,
			timeout: 15000,
			success: function(html){
				$("div#cache_detail").html(html);
				$('.container_same_category', "#cache_detail").mCustomScrollbar({
					scrollButtons:{
						enable:false
					}
				});
			}
		});
	}

	function delete_cache(name){
		if(name == undefined){
			name = $("#delete_cacheId").val();
			if(name == '' || name == undefined){
				alerts('Message','Please choose option!');
				return false;
			}
			if(name != "all")
				name = "@"+name;
		}
		confirms( "Message", "Are you sure you want to delete?",
		function(){
			$.ajax({
				url: '<?php echo URL; ?>/settings/cache_delete/' + name,
				timeout: 15000,
				success: function(result){
					if(result=="ok"){
						if(name == "all" || name.indexOf("@")!= -1)
							$("#cache","#settings_ul_nav_setup").click();
						else
							$("#cache_"+name).parent().fadeOut(400,function(){
								$(this).remove();
								$(".clickfirst:first", "#cache_height").click();
							});
					} else {
						alerts("Message",result);
					}
				}
			});
		},function(){
			//else do somthing
		});
	}

</script>