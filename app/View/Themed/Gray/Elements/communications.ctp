<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent">
			<h4><?php echo translate('Comunications / notes'); ?></h4>
		</span>
			<?php if($this->Common->check_permission('communications_@_entry_@_add',$arr_permission)
				         	||$this->Common->check_permission($controller.'_@_communications_tab_@_add',$arr_permission)
							||$this->Common->check_permission($controller.'_@_other_tab_@_edit',$arr_permission)): ?>
			<form id="form_comms" class="float_left hbox_form" style="width:130px; height:10px; margin: -3px 16px 0px 0px;" >
				<input class="top_m float_left" name="comms_type" id="comms_type" type="text" style="width: 97% ">
				<input type="hidden" name="comms_type_id" id="comms_typeId" value="" />
				<script type="text/javascript">
					$("#comms_type").combobox(<?php if(isset($com_type))echo json_encode($com_type) ?>);
				</script>
				<a title="Create" id="comms_create" style="position: absolute;">
				<span class="icosp_sea indent_sea" style="margin: 2.4px 0 0 15px;"></span>
				</a>
			 </form>
			<?php endif; ?>
	</span>
	<p class="clear"></p>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd" style="width:1.5%; min-width: 19px;"></li>
		<li class="hg_padd" style="width:4%; min-width: 53px;"><?php echo translate('Type'); ?></li>
		<li class="hg_padd" style="width:13%; min-width: 70px;"><?php echo translate('Date'); ?></li>
		<li class="hg_padd" style="width:10%; min-width: 100px;"><?php echo translate('From'); ?></li>
		<li class="hg_padd" style="width:25%; min-width: 100px;"><?php echo translate('To'); ?></li>
		<li class="hg_padd" style="width:25%; min-width: 100px;"><?php echo translate('Details'); ?></li>
	</ul>
	<div class="container_same_category" id="comms_box"> <!-- style="height: auto;overflow: visible;" -->
		<?php $i = 1; $count = 0; ?>
	<?php
		if(isset($arr_communication)){
			foreach($arr_communication as $key => $value){?>
		<ul class="ul_mag clear bg<?php echo $i; ?>" id="comms_<?php echo $value['_id'] ?>">
			<li class="hg_padd center_txt" style="width:1.5%; min-width: 19px;">
				<a href="<?php echo URL; ?>/communications/entry/<?php echo $value['_id']; ?>">
					<span class="icon_emp"></span>
				</a>
			</li>
			<li class="hg_padd " style="width:4%; min-width: 53px;"><?php if(isset($value['comms_type'])) echo $value['comms_type'];?></li>
			<li class="hg_padd " style="width:13%; min-width: 70px;"><?php if(is_object($value['date_modified'])) echo $this->Common->format_date($value['date_modified']->sec,true);?></li>
			<li class="hg_padd" style="width:10%; min-width: 100px;">
				<?php
					if(isset($value['contact_from'])) echo $value['contact_from'];
				?>
			</li>
			<li class="hg_padd" style="width:25%; min-width: 100px;">
				<?php
					if(isset($value['comms_type']) ){
						if($value['comms_type'] == 'Email'){
							if(isset($value['contact_name']))
								echo $value['contact_name'];
							if(isset($value['email']))
								echo ' ['.$value['email'].']';
						} else if($value['comms_type'] == 'Note')
							echo $value['contact_name'];
					}
					else if(isset($value['contact_to'])) echo $value['contact_to'];
				?>
			</li>
			<li class="hg_padd" style="width:25%; min-width: 100px;">
				<?php
					if(isset($value['comms_type']) && $value['comms_type'] == 'Email'){
						if(isset($value['email_cc']) && $value['email_cc'])
							echo 'CC: '.$value['email_cc'];
					}
					else if(isset($value['message_content'])) echo $value['message_content'];
					else if(isset($value['note'])) echo $value['note'];
					else if(isset($value['content'])) echo $value['content'];
				?>
			</li>
			<li class="hg_padd bor_mt" style="width:1.5%">
				<div class="middle_check">
					<a title="Delete link" href="javascript:void(0)" onclick="remove_comms('<?php echo $value['_id']; ?>')">
						<span class="icon_remove2"></span>
					</a>
				</div>
			</li>
		</ul>

		<?php
		$i = 3 - $i; $count += 1;
	}
			$count = 8 - $count;
			if( $count > 0 ){
				for ($j=0; $j < $count; $j++) { ?>
				<ul class="ul_mag clear bg<?php echo $i; ?>">
				</ul>
		  <?php $i = 3 - $i;
				}
			}
	}
		?>
	</div>

	<span class="title_block bo_ra2">
		<span class="float_left bt_block">
			<?php echo translate('Click to view full details'); ?>
		</span>
	</span>
</div>
<style type="text/css">
.indent_sea, .indent_sea2 {
margin: 2.4px 0 0 -26px;
}
</style>

<span id="click_open_window_contacts_comm_message"></span>
<?php if($this->Common->check_permission('communications_@_entry_@_add',$arr_permission)
         	||$this->Common->check_permission($controller.'_@_communications_tab_@_add',$arr_permission)
			||$this->Common->check_permission($controller.'_@_other_tab_@_edit',$arr_permission)): ?>
<script type="text/javascript">
	$(function(){
		$(".container_same_category").mCustomScrollbar({
			scrollButtons:{
				enable:false
			},
			advanced:{
		        updateOnContentResize: true,
		        autoScrollOnFocus: false,
		    }
		});
	})
	function after_choose_contacts_comm_message(contact_id, contact_name){
		$("#window_popup_contacts_comm_message").data("kendoWindow").close();

		window.location.assign('<?php echo URL; ?>/<?php echo $controller; ?>/add_from_module/<?php echo $module_id; ?>/' + $("input#comms_typeId").val()+"?contact_id=" + contact_id);
		return false;
	}

	function comms_do_action( comms_type ){
		if( comms_type == "Message" ){
			$("#click_open_window_contacts_comm_message").click();
		}else{
			window.location.assign('<?php echo URL; ?>/<?php echo $controller; ?>/add_from_module/<?php echo $module_id; ?>/' + $("input#comms_typeId").val());
		}

	}

	$(function(){
		window_popup("contacts", "Specify contact", "_comm_message", "", "?is_employee=1");
		$('#comms_create').click(function(){
		   	if($('#comms_type').val()=='')
		    {
			   alerts('Message','Please first specify a type of record you would like to create.');

		    }else
		    {
			   comms_do_action( $('#comms_type').val() );
			}
			return false;
		});
	});

	function check_add(){
		var arr1 = '';
		if($("#CompanyIsCustomer").is(':checked') && $("#CompanyIsSupplier").is(':checked'))
		{
			var arr = new Array();
			arr = ['Outgoing','Incoming',''];

			confirms3('Message',"Create an '<span class=\"bold\">Outgoing</span>' or '<span class=\"bold\">Incoming</span>' shipping/delivery?",arr,function(){
					arr1='Outgoing'; //Outgoing
					window.location.href='<?php echo URL; ?>/companies/shipping_add/<?php //echo $company_id; ?>/'+arr1;
			},function(){
					arr1='Incoming'; //Incoming
					 window.location.href='<?php echo URL; ?>/companies/shipping_add/<?php //echo $company_id; ?>/'+arr1;
			},function(){
					//
			});

		}else{
			window.location.href='<?php echo URL; ?>/companies/shipping_add/<?php //echo $company_id; ?>/'+arr1;
		}
	}

	function remove_comms(comm_id){
		confirms( "Message", "Are you sure you want to delete?",
			function(){
				$.ajax({
					 url: '<?php echo URL; ?>/communications/comm_delete/' + comm_id,
					 timeout: 15000,
					 success: function(html){
						 if(html == "ok"){
							 $("#comms_"+comm_id).fadeOut();
						 }else{
							 alerts("Error: ", html);
						 }
					 }
				 });
			},function(){
				//else do somthing
		});
	}
</script>
<?php endif; ?>
