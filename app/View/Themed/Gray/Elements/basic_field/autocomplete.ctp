<div id="example" class="k-content">
<input name="<?php echo $keys;?>" id="<?php echo $keys;?>" class="jt_box_input" type="text" value="<?php if(isset($arr_field['default'])) echo $arr_field['default'];?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> style="border:none;border-bottom:1px solid #ddd;border-radius:0; <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" />
<input type="hidden" name="<?php echo $keys;?>_id" id="<?php echo $keys;?>_id" />
<a title="Select list" id="sl_<?php echo $keys;?>" rel="<?php if(isset($arr_field['mongo_id'])) echo $arr_field['mongo_id'];?>"><span class="jt_icon_autoselect"></span></a>


<div id="list_popup_<?php echo $keys;?>" style="display:none; min-width:300px;"></div>
<script>
	$(document).ready(function() {
		var autocomplete = $("#<?php echo $keys;?>").kendoAutoComplete({
			minLength: 1,
			dataTextField: "ContactName",
			template: '<img src=\"http://demos.kendoui.com/content/web/Customers/${data.CustomerID}.jpg\" alt=\"${data.CustomerID}\" />' +
					'<h3>${ data.ContactName }</h3>' +
					'<p>${ data.CompanyName }</p>',
			dataSource: {
				transport: {
					read:{
						dataType: "jsonp",
						url: "http://demos.kendoui.com/service/Customers"
					}
				}
			},
			height: 400,
		}).data("kendoAutoComplete");
		
		
		var list_popup = $("#list_popup_<?php echo $keys;?>"),
			undo = $("#sl_<?php echo $keys;?>")
					.bind("click", function() {
						$(".k-window").fadeOut('slow');
						list_popup.data("kendoWindow").open();
						var leftpos = ($(window).width() - $(".k-window").width())/2;
						var toppos = ($(window).height() - $(".k-window").height())/2;
						$(".k-window").css('left',leftpos);
						$(".k-window").css('top',toppos);
					});
		var onShows = function() {
			undo.show();
		}
		list_popup.kendoWindow({
			width: "auto",
			title: "Select One <?php echo $arr_field['name'];?>",
			<?php if($arr_field['cls']=='companies') $ss = 'm';else $ss='';?>
			content: "<?php echo URL.'/'.$ss.$arr_field['cls'].'/popup/rel@'.$keys; ?>",
			close: onShows
		});
	});
</script>

<style scoped>
	.k-autocomplete{
		width:90%!important;
		border:none!important;
	}
	#<?php echo $keys;?>_listbox .k-item {
		overflow: hidden; /* clear floated images */
		cursor:pointer;
		
	}
	#<?php echo $keys;?>_listbox .k-item:hover,#<?php echo $keys;?>_option_selected{
		background:#bbb;

	}
	#<?php echo $keys;?>_listbox img {
		-moz-box-shadow: 0 0 2px rgba(0,0,0,.4);
		-webkit-box-shadow: 0 0 2px rgba(0,0,0,.4);
		box-shadow: 0 0 2px rgba(0,0,0,.4);
		float: left;
		margin: 1% 3% 1% 1%;
		width:20%;
	}
	#<?php echo $keys;?>_listbox .k-item h3 {
		margin: 5% 0 2% 0;
		font-size: 1em;
		line-height:10px;
		font-family:Verdana, Geneva, sans-serif;
	}
	#<?php echo $keys;?>_listbox p {
		margin: 0;
		font-size: 0.8em;
		font-family:Verdana, Geneva, sans-serif;
	}
</style>


</div>
<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>