<style type="text/css">
	.choice_type{
		float:right;
		line-height: 20px;
		margin-right:12px;
	}
	.choice_type .combobox{
		width: auto;
		float: right;
	}
	.choice_type input{
		border: 1px solid #ddd;
	}
	.table_edit_type{
		margin-top:20px;
		width: 100%;
		float: left;
	}
	.table_edit_type ul, .table_edit_type li{
		height: 30px;
		line-height:30px;
		padding: 0px 5px 0px 5px;
	}
	.price_config_left{
		float:left;
		width: 30%;
		text-align: right;
	}
	.price_config_right{
		float:left;
		width: 70%;
	}
	.price_config_right input{
		float:left;
		width: 35%;
		margin: 3px 1% 2px 1%;
		border: 1px solid #ddd;
		padding:3px;
	}
	.tb_priceranges{
		width: 98%;
		margin: 10px 1% 20px 1%;
		border:1px solid #ddd;
	}
	.tb_priceranges td{
		border:1px solid #ddd;
		padding: 0 10px 0 10px;
	}
	.tb_priceranges input{
		width: 90%;
		line-height: 30px;
		border:none;
		text-align: left;
	}
	.tb_priceranges > thead > tr > td{
		background-color: #ddd;
		text-align: center;
		vertical-align: middle;
		font-size: 12px;
		height: 30px;
	}
	.push_button{
		position: relative;
		width: 90px;
		color: #797979;
		display: block;
		text-decoration: none;
		margin: 0 auto;
		border-radius: 11px;
		border: solid 1px #D94E3B;
		background: #F1F1F1;
		text-align: center;
		padding: 8px 5px;
		-webkit-transition: all 0.1s;
		-moz-transition: all 0.1s;
		transition: all 0.1s;
		-webkit-box-shadow: 0px 3px 0px #797979;
		-moz-box-shadow: 0px 3px 0px #797979;
		box-shadow: 0px 3px 0px #797979;
		cursor: pointer;
		font-weight: bold;
	}
	.push_button:hover{
		background-color: #fff;
	}
	.push_button:active{
	    -webkit-box-shadow: 0px 1px 0px #797979;
	    -moz-box-shadow: 0px 1px 0px #797979;
	    box-shadow: 0px 1px 0px #797979;
	    position:relative;
	    top:4px;
	}
	.combobox_selector{
		top:26px!important;
	}
	.combobox_selector ul{
		height: inherit;
	}
	.combobox input{
		height: 20px;
	}
</style>
<div class="tab_1 full_width" style="min-height:518px;display:table;">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4>
                <?php echo isset($cf_bleed['name'])?$cf_bleed['name']:'';?>
            </h4>
        </span>
        <a title="Add new message" href="javascript:void(0)" onclick="add_price_config()">
            <span class="icon_down_tl top_f"></span>
        </a>
    </span>
    <div class="tab_backup_restore" style="margin:12px;">
    	<div class="choice_type">
	    	<input class="push_button" name="add_line" id="add_line" onclick="add_line_td();" type="button" value="Add Bleed" />
	    	<input type="hidden" id="stuff_id" value="<?php echo isset($cf_bleed['_id'])?(string)$cf_bleed['_id']:'';?>" />
	    </div>
	    <div class="table_edit_type">
			<form id="price_ranges_form">
				<table class="tb_priceranges" id="tb_priceranges" padding=0>
					<thead>
						<tr>
							<td>Bleed name</td>
							<td>Bleed value</td>
							<td>UOM</td>
							<td></td>
						</tr>
					</thead>
					<body>
					<?php
						if(isset($cf_bleed['option'])){
							foreach ($cf_bleed['option'] as $key => $value) {
						?>
						<tr>
							<td>
								<input type="text" value="<?php echo $value['name'];?>" name="bleed_name" class="in_ranges" rel="<?php echo $key;?>" />
							</td>
							<td>
								<input type="text" value="<?php echo $value['sizew'];?>" name="bleed_sizew" class="in_ranges" rel="<?php echo $key;?>"  />
							</td>
							<td>
								<input type="text" value="<?php echo $cf_uom[$value['sizew_unit']];?>" id="uom_<?php echo $key;?>" class="in_ranges" readonly=readonly rel="<?php echo $key;?>" name="bleed_sizew_unit"  />
								<input type="hidden" value="<?php echo $value['sizew_unit'];?>" id="uom_<?php echo $key;?>Id" />
								<script type="text/javascript">
									$(function () {
										$("#uom_<?php echo $key;?>").combobox(<?php echo json_encode($cf_uom);?>);
									});
								</script>
							</td>
							<td>
								<span class="icon_remove2" style="margin: 9px 10px 1px 10px;height: 14px;" onclick="del_line_td(<?php echo $key;?>)"></span>
							</td>
						</tr>
					<?php } }?>
					</body>
				</table>
			</form>
	    </div>
    </div>
</div>
<div id="content_backup_restore"></div>

<script type="text/javascript">
	$(function(){
		$(".in_ranges").change(function(){
			var field = $(this).attr('name');
			var value = $(this).val();
			var ids = $(this).attr('rel');
			field = field.replace("bleed_","");
			if(field=='sizew_unit')
				value = $("#uom_"+ids+"Id").val();
			$.ajax({
				url: '<?php echo URL; ?>/settings/sale_bleed',
				type:"POST",
				data:{id:$("#stuff_id").val(),type:'update',ids:ids,field:field,value:value},
				success: function(html){
					console.log(html);
				}
			});
		});
	});
	
	
	function add_line_td(){
		$.ajax({
			url: '<?php echo URL; ?>/settings/sale_bleed',
			type:"POST",
			data:{id:$("#stuff_id").val(),type:'add'},
			timeout: 15000,
			success: function(html){
				show_box_detail($("#bleed_calculation"),'bleed_calculation');
			}
		});
	}
	function del_line_td(ids){
		$.ajax({
			url: '<?php echo URL; ?>/settings/sale_bleed',
			type:"POST",
			data:{id:$("#stuff_id").val(),type:'delete',ids:ids},
			timeout: 15000,
			success: function(html){
				show_box_detail($("#bleed_calculation"),'bleed_calculation');
			}
		});
	}
	

</script>