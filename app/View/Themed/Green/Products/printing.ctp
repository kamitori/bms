<style>
.p_paper{
	width:600px;
	height:280px;
	margin:auto;
	background:#FFC;
	font-size:24px;
}
.p_hor{
	width:180px;
	height:80px;
	margin-top:200px;
	background: #069;
	float:left;
	font-weight:bold;
	color:#fff;
	text-align:center;
	line-height:80px;
}
.p_ver{
	width:80px;
	height:180px;
	margin-top:0px;
	background:#C63;
	float:right;
	font-weight:bold;
	color:#fff;
	text-align:center;
	line-height:180px;
}

</style>
<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>

<script type="text/javascript">

function rebuild_img(paper_w,paper_h,poster_w,poster_h,type){
	var max_w = 600;
	var max_h = 280;
	var paper = $(".p_paper");
	var max_ra = max_w/max_h;
	var paper_ra = paper_w/paper_h;
	//rezise paper
	var pixel_ra = 1;//ty le phong to
	if(max_ra>paper_ra){
		paper_w = max_h*paper_ra;
		pixel_ra = max_h/paper_h;
		paper_h = max_h;

	}else{
		paper_h = max_w/paper_ra;
		pixel_ra = max_w/paper_w;
		paper_w = max_w;
	}
	paper.css("width",paper_w.toString()+"px");
	paper.css("height",paper_h.toString()+"px");


	poster_w = poster_w*pixel_ra;
	poster_h = poster_h*pixel_ra;

	var item_a = $(".p_hor");
	var item_b = $(".p_ver");

	item_a.css("width",poster_w.toString()+"px");
	item_a.css("height",poster_h.toString()+"px");
	item_a.css("line-height",poster_h.toString()+"px");
	var margintop = paper_h-poster_h;
	item_a.css("margin-top",margintop.toString()+"px");


	item_b.css("width",poster_h.toString()+"px");
	item_b.css("height",poster_w.toString()+"px");
	item_b.css("line-height",poster_w.toString()+"px");

	if(type=='B-vertical'){
		item_a.css("display","none");
		item_b.css("display","block");
	}else{
		item_b.css("display","none");
		item_a.css("display","block");
	}

}

$(function(){
	$("#is_printer_check").click(function(){
		$("#is_printer").click();
	});
	$("#is_printer").click(function(){
		if($(this).is(':checked'))
				is_printer = 1;
			else
				is_printer = 0;
		save_field('is_printer',is_printer,'');
	});
	$("#paper_size").change(function(){
		save_field('paper_size',$(this).val(),'');
	});
	$("#add_paper_size").click(function(){
		var w = $("#paper_size_w").val();
		var h = $("#paper_size_h").val();
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/add_paper_size',
			type:"POST",
			data: {w:Math.max(w, h),h:Math.min(w, h)},
			success: function(rq){
				reload_subtab('printing');
			}
		});
	});
	$("#calculator_button").click(function(){
		var paper_w = $("#paper_size").val();
		paper_w = paper_w.split("x");
		var paper_h = Math.min(paper_w[0], paper_w[1]);
		var paper_w = Math.max(paper_w[0], paper_w[1]);
		var poster_w = $("#poster_size_w").val();
		var poster_h = $("#poster_size_h").val();
		var paper_size = $("#paper_size").val();
		if(poster_w!='' && poster_h!='' && paper_size!=''){
			$.ajax({
				url: '<?php echo URL.'/'.$controller;?>/sheet_yield_calculator',
				type:"POST",
				dataType: "json",
				data: {w:paper_w,h:paper_h,wr:Math.max(poster_w,poster_h),hr:Math.min(poster_w,poster_h)},
				success: function(rq){
					$("#results_total_yield").val(rq.total_yield);
					$("#results_sheet_utilization").val(Math.round(rq.sheet_u)+'%');
					$("#results_type").val(rq.type);
					$("#row_col").val(rq.row_col);
					$("#cutting_amount").val(rq.cutting_amount);
					if(Math.round(rq.sheet_u)>0 && rq.total_yield>0)
						rebuild_img(paper_w,paper_h,Math.max(poster_w,poster_h),Math.min(poster_w,poster_h),rq.type);
					else{
						$(".p_ver").css("display","none");
						$(".p_hor").css("display","none");
					}
				}
			});
		}else
			alerts('Message','Please input data in Poster size and Paper size.');
	});
	$("#calculator_price").click(function(){
		var paper_w = $("#paper_size").val();
		paper_w = paper_w.split("x");
		var paper_h = Math.min(paper_w[0], paper_w[1]);
		var paper_w = Math.max(paper_w[0], paper_w[1]);
		var poster_w = $("#poster_size_w").val();
		var poster_h = $("#poster_size_h").val();
		var price_setup = {};
		price_setup['inkcolor'] = $("#inkcolorId").val();
		price_setup['rip'] 		= $("#rip").val();
		price_setup['packing']  = $("#packingId").val();
		price_setup['cutting']  = $("#cutting").val();
		var qt = $("#rel_quantity").val();
		if(poster_w!='' && poster_h!=''){
			$.ajax({
				url: '<?php echo URL.'/'.$controller;?>/printer_pricing',
				type:"POST",
				dataType: "json",
				data: {price_setup:price_setup,qt:qt,w:paper_w,h:paper_h,wr:Math.max(poster_w,poster_h),hr:Math.min(poster_w,poster_h),cutting_amount:$("#cutting_amount").val(),cut_ra:$("#rel_cut_ra").val()},
				success: function(rt){
					$("#rel_printing_price").val(rt);
				}
			});
		}else
			alerts('Message','Please input data in Poster size');
	});
});
</script>