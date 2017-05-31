<style type="text/css">
	.choice_type{
		float:right;
		line-height: 20px;
		width: 50%;
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
		width: 36%;
		margin: 10px 1% 20px 1%;
		border:1px solid #ddd;
	}
	.tb_priceranges td{
		border:1px solid #ddd;
	}
	.tb_priceranges input{
		width: 90%;
		border:none;
		text-align: right;
	}
</style>
<div class="tab_1 full_width" style="min-height:518px;display:table;">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4>
                Pricing Tablelookup
            </h4>
        </span>
        <a title="Add new message" href="javascript:void(0)" onclick="add_price_config()">
            <span class="icon_down_tl top_f"></span>
        </a>
    </span>
    <div class="tab_backup_restore" style="margin:12px;">
	    <div class="choice_type">
	    	<input class="input_select" name="choice_type" id="choice_type" type="text" value="<?php echo isset($cf_price_select[$cf_price_id])?$cf_price_select[$cf_price_id]:'';?>" readonly="readonly" combobox_blank="1" />
	    	<input name="choice_type_id" id="choice_typeId" type="hidden" value="<?php echo $cf_price_id;?>" />
	    	<script type="text/javascript">
				$(function () {
					$("#choice_type").combobox(<?php echo json_encode($cf_price_select);?>);
				});
			</script>
			<span class="combobox" style="margin-right:20px;">Choice a type to edit:  </span>
	    </div>
	    <div class="table_edit_type">
	    	<ul class="ul_mag clear bg2">
				<li style="font-weight:bold;">
					<b>Config price break</b>
					<span style="float:right;cursor:pointer;" onclick="del_config_pr();"><a>Remove this config</a></span>
				</li>
			</ul>
			<?php foreach ($cf_option as $key => $value) { ?>
	    	<ul class="ul_mag clear bg1">
				<li>
					<span class="price_config_left"><?php echo $value['title'];?></span>
					<span class="price_config_right">
						<input type="text" value="<?php echo isset($value['value']) ? $value['value'] : '';?>" name="<?php echo $key;?>" id="<?php echo $key;?>" class="price_config_input"  />
					</span>
				</li>
			</ul>
			<?php }?>
			<ul class="ul_mag clear bg1" style="border:none;">
				<li>
					<span class="price_config_left">Price Ranges</span>
					<span class="price_config_right">
						&nbsp;&nbsp;&nbsp;<a onclick="add_line_td();" style="cursor:pointer;">  Add line</a>
						<form id="price_ranges_form">
							<table class="tb_priceranges" id="tb_priceranges">
								<?php if(count($cf_table_ranges)>0) 
									foreach ($cf_table_ranges as $m => $value) { ?>
								<tr>
									<td>
										<input type="text" value="<?php echo $value['range_from'];?>" name="data[<?php echo $m;?>][range_from]" id="range_from_<?php echo $m;?>" class="in_ranges"  />
									</td>
									<td>
										<input type="text" value="<?php echo $value['range_to'];?>" name="data[<?php echo $m;?>][range_to]" id="range_to_<?php echo $m;?>" class="in_ranges"  />
									</td>
									<td>
										<span class="icon_remove2" style="margin:2px 12px 2px 50px;" onclick="del_line_pr(<?php echo $m;?>)"></span>
									</td>
								</tr>
								<?php }?>
							</table>
							
						</form>
					</span>
				</li>
			</ul>

			
	    </div>
    </div>
</div>
<div id="content_backup_restore"></div>

<script type="text/javascript">
	$(function(){
		$(".price_config_input").change(function(){
			$.ajax({
				url: '<?php echo URL; ?>/settings/sale_price_save',
				type:"POST",
				data:{id:$("#choice_typeId").val(),field:$(this).attr('name'),value:$(this).val()},
				success: function(html){
					show_box_detail($("#sale_price_list_type"),'sale_price_list_type',$("#choice_typeId").val());
				}
			});
		});
		$("#choice_type").change(function(){
			show_box_detail($("#sale_price_list_type"),'sale_price_list_type',$("#choice_typeId").val());
		});
		$(".in_ranges").change(function(){
			var postData = $("#price_ranges_form").serialize();
			postData += '&id='+$("#choice_typeId").val();
			$.ajax({
				url: '<?php echo URL; ?>/settings/sale_pb_price_ranges',
				type:"POST",
				data:postData,
				success: function(html){
					console.log(html);
				}
			});
		});
	});

	function add_price_config(){
		$.ajax({
			url: '<?php echo URL; ?>/settings/sale_price_save',
			type:"POST",
			data:{type:'add'},
			timeout: 15000,
			success: function(html){
				show_box_detail($("#sale_price_list_type"),'sale_price_list_type');
			}
		});
	}
	function add_line_td_view(){
		var table = document.getElementById("tb_priceranges");
		var rownum = table.getElementsByTagName("tr").length;
	    var row = table.insertRow();	    
	    var range_from = row.insertCell(0);
	    var range_to = row.insertCell(1);
	    range_from.innerHTML = "<input type=\"text\" value=\"\" name=\"\" id=\"data["+rownum+"]['range_from']\" class=\"in_ranges\"  />";
	    range_to.innerHTML = "<input type=\"text\" value=\"\" name=\"\" id=\"data["+rownum+"]['range_to']\" class=\"in_ranges\"  />";
	}
	function add_line_td(){
		$.ajax({
			url: '<?php echo URL; ?>/settings/sale_pb_price_ranges',
			type:"POST",
			data:{id:$("#choice_typeId").val(),type:'add'},
			timeout: 15000,
			success: function(html){
				show_box_detail($("#sale_price_list_type"),'sale_price_list_type',$("#choice_typeId").val());
			}
		});
	}
	function del_line_pr(ids){
		$.ajax({
			url: '<?php echo URL; ?>/settings/sale_pb_price_ranges',
			type:"POST",
			data:{id:$("#choice_typeId").val(),type:'delete',ids:ids},
			timeout: 15000,
			success: function(html){
				show_box_detail($("#sale_price_list_type"),'sale_price_list_type',$("#choice_typeId").val());
			}
		});
	}
	function del_config_pr(){
		$.ajax({
			url: '<?php echo URL; ?>/settings/sale_pb_price_ranges',
			type:"POST",
			data:{id:$("#choice_typeId").val(),type:'delete_config'},
			timeout: 15000,
			success: function(html){
				show_box_detail($("#sale_price_list_type"),'sale_price_list_type',$("#choice_typeId").val());
			}
		});
	}

</script>