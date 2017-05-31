	<div class="float_left" style="width:22%;margin:0;">
		<div id="jobs_entry_general_contacts" class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('Contacts related to this job'); ?></h4></span>
				<a id="click_open_window_contacts_jobs" href="javascript:void(0)" title="Link a contact"><span class="icon_down_tl top_f"></span></a>
			</span>
			<ul class="ul_mag clear bg3">
				<li class=" ct" ><?php echo translate('Contact'); ?></li>
				<li class=" mg"><?php echo translate('Manager'); ?></li>
				<li class=" emp" ></li>
			</ul>

			<?php

			$i = 1; $count = 0;

			if( isset($arr_job['contacts']) ){
				$view = $this->Common->check_permission('contacts_@_entry_@_view',$arr_permission);
				$delete = $this->Common->check_permission($controller.'_@_general_tab_@_delete',$arr_permission);
				foreach ($arr_job['contacts'] as $key => $value) {
					if( $value['deleted'] )continue;
					$i = 3 -$i; $count += 1;
				?>
				<ul class="ul_mag clear bg<?php echo $i; ?>" id="jobs_general_<?php echo $key; ?>">
					<li class="ct bof">
						<span class="input_inner_w float_left"><?php echo $value['contact_name']; ?></span>
						<?php if($view): ?>
						<a href="<?php echo URL; ?>/contacts/entry/<?php echo $value['contact_id']; ?>" title="view"><span class="icon_viw"></span></a>
						<?php endif; ?>
						<!-- <a href="javascript:void(0)" title="Create email"><span class="icon_emaili chan indent_viw2"></span></a> -->
					</li>
					<li class="mg bof">
						<input type="hidden" name="data[Company][is_supplier]" id="CompanyIsSupplier_" value="0">
						<label class="m_check2 cene">
							<?php echo $this->Form->input('Company.is_supplier', array(
									'type' => 'checkbox',
									'checked' => ($arr_job['contacts_default_key'] == $key)?true:false,
									'onchange' => 'jobs_entry_general_manager(this, '.$key.')'
							)); ?>
							<span></span>
						</label>
					</li>
					<li class="emp">
						<?php if($delete): ?>
						<div class="middle_check2">
							<a title="Delete link" href="javascript:void(0)" onclick="jobs_general_contact_delete(<?php echo $key; ?>)">
								<span class="icon_remove2"></span>
							</a>
						</div>
					<?php endif; ?>
					</li>
				</ul>
				<?php } ?>

			<?php } ?>

			<?php
			$count = 8 - $count;
			if( $count > 0 ){
				for ($j=0; $j < $count; $j++) { $i = 3 -$i;
					echo '<ul class="ul_mag clear bg'.$i.'"></ul>';
				}
			}
			?>

			<span class="hit"></span>
			<span class="title_block bo_ra2">
				<span class="icon_vwie indent_down_vwie2"><a href="javascript:void(0)"><?php echo translate('View contact'); ?></a></span>
			</span>
		</div>
	</div>

	<?php echo $this->Form->create('Job', array('id' => 'form_job_general')); ?>
	<?php echo $this->Form->hidden('Job._id', array('value' => $job_id)); ?>

	<div class="float_left" style=" width:40%; margin-left:1%;">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="float_left">
					<span class="fl_dent"><h4><?php echo translate('Job brief'); ?></h4></span>
					<!-- <a href="" title="Print"><span class="icon_print top_f"></span></a> -->
				</span>
				<span class="float_right">
					<span class="fl_dent"><h6><?php //echo translate('Inc. quote items on printed job brief'); ?></h6></span>
					<!-- <label class="m_check2">
						<input type="checkbox">
						<span></span>
					</label> -->
				</span>
			</span>
			<?php echo $this->Form->input('Job.brief', array(
					'rows' => 3,
					'cols' => 4,
					'style' => 'height: 196px;',
					'class' => 'area_t',
					'div' => false,
					'value' => (isset($arr_job['brief'])?$arr_job['brief']:'')
			 )); ?>
			<span class="title_block bo_ra2"></span>
		</div>
	</div>
	<div class="float_right" style=" width:36%; margin-left:1%;">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('Address details for this job'); ?></h4></span>
			</span>

			<?php echo $this->element('box_type/address', array(
			        'address_company_id'=>'JobCompanyId',
					'address_label' => array('Invoice address'),
					'address_more_line' => 0,
					'address_controller' => array('Job'),
					'address_country_id' => array((isset($arr_job['invoice_country_id']))?$arr_job['invoice_country_id']:39),
					'address_value' => array(
						'invoice' => array(
							(isset($arr_job['invoice_address_1']))?$arr_job['invoice_address_1']:'',
							(isset($arr_job['invoice_address_2']))?$arr_job['invoice_address_2']:'',
							(isset($arr_job['invoice_address_3']))?$arr_job['invoice_address_3']:'',
							(isset($arr_job['invoice_town_city']))?$arr_job['invoice_town_city']:'',
							(isset($arr_job['invoice_country']))?$arr_job['invoice_country']:'',
							(isset($arr_job['invoice_province_state']))?$arr_job['invoice_province_state']:'',
							(isset($arr_job['invoice_zip_postcode']))?$arr_job['invoice_zip_postcode']:''
						)
					),
					'address_key' => array('invoice'),
					'address_conner' => array(
						array(
							'top' => 'hgt ',
							'bottom' => 'fixbor3 fix_bottom_address'
						)
					),
					'address_more_line' => 1,
					'address_class_div_top' => 'float_left wid_col_re'
			)); ?>

			<?php echo $this->element('box_type/address', array(
			        'address_company_id'=>'JobCompanyId',
					'address_label' => array('Shipping address'),
					'address_more_line' => 0,
					'address_controller' => array('Job'),
					'address_country_id' => array((isset($arr_job['shipping_country_id']))?$arr_job['shipping_country_id']:39),
					'address_value' => array(
						'shipping' => array(
							(isset($arr_job['shipping_address_1']))?$arr_job['shipping_address_1']:'',
							(isset($arr_job['shipping_address_2']))?$arr_job['shipping_address_2']:'',
							(isset($arr_job['shipping_address_3']))?$arr_job['shipping_address_3']:'',
							(isset($arr_job['shipping_town_city']))?$arr_job['shipping_town_city']:'',
							(isset($arr_job['shipping_country']))?$arr_job['shipping_country']:'',
							(isset($arr_job['shipping_province_state']))?$arr_job['shipping_province_state']:'',
							(isset($arr_job['shipping_zip_postcode']))?$arr_job['shipping_zip_postcode']:''
						)
					),
					'address_conner' => array(
						array(
							'top' => 'hgt',
							'bottom' => 'fixbor3 fix_bottom_address '
						)
					),
					'address_key' => array('shipping'),
					'address_province' => $address_province_shipping,
					'address_more_line' => 1,
					'address_class_div_top' => 'float_left wid_col_re'
			)); ?>

			<p class="clear"></p>
			<span class="title_block bo_ra2">
				<p class="cent"><?php echo translate('Note: Leave delivery address blank to use invoice address for shipping'); ?></p>
			</span>
		</div>
	</div>
	<?php echo $this->Form->end(); ?>
	<div class="clear"></div>
	<div class="full_width block_dent9" style="margin-top:1%;">
		<?php echo $this->element('communications'); ?>
	</div>

<script type="text/javascript">
$(function(){
    // $("form :input", "#salesorders_sub_content").change(function() {
    //     // var filters = {};
    //     // filters[$(this).attr('name')] = $(this).val();
    //     $.ajax({
    //         url: '<?php echo URL; ?>/salesorders/resources_auto_save',
    //         timeout: 15000,
    //         type:"post",
    //         data: $(this).closest('form').serialize(),
    //         success: function(html){
    //             if( html != "ok" )alerts("Error: ", html);
    //         }
    //     });
    // });

    window_popup('contacts', 'Specify contact', '_jobs', "", "?is_employee=1");

    $(":input", "#form_job_general").change(function() {
		job_general_auto_save();
	});
});

function job_general_auto_save(){

	$.ajax({
		url: '<?php echo URL; ?>/jobs/general_auto_save',
		timeout: 15000,
		type:"post",
		data: $("#form_job_general").serialize(),
		success: function(html){
			if(html != "ok"){
				alerts("Error: ", html);

			}
			console.log(html); // view log when debug
		}
	});
}

function after_choose_contacts_jobs(contact_id, contact_name){

    var job_id = "<?php echo $job_id; ?>";
    $.ajax({
        url: "<?php echo URL; ?>/jobs/general_window_contact_choose/" + job_id + "/" + contact_id + "/" + contact_name,
        timeout: 15000,
        success: function(html){
        	console.log(html);
            if(html == "ok"){
                $("#general").click();
            }else{
                alerts("Error: ", html);
            }
        }
    });
    return false;
}

function jobs_entry_general_manager(object, option_id){

    if( !$( object ).prop("checked") ){
        $( object ).prop("checked", true);
        return false;
    }

    var job_id = "<?php echo $job_id; ?>";

    $( "input[type=checkbox]", "#jobs_entry_general_contacts").prop("checked", false);
    $( object ).prop("checked", true);

    $.ajax({
        url: "<?php echo URL; ?>/jobs/general_choose_manager/" + job_id + "/" + option_id,
        timeout: 15000,
        success: function(html){
        	console.log(html);
            if(!html == "ok"){
                alerts("Error: ", html);
            }
        }
    });
    return false;
}

function jobs_general_contact_delete(key){
	confirms( "Message", "Are you sure you want to delete?",
	    function(){
	        $.ajax({
				url: '<?php echo URL; ?>/jobs/general_delete_contact/<?php echo $job_id; ?>/'+ key,
				success: function(html){
					if(html == "ok"){
						$("#jobs_general_" + key).fadeOut();
					}else{
						console.log(html);
					}
				}
			});
	    },function(){
	        //else do somthing
	});
}
function after_choose_addresses(key,company,type){
	var arr = ['name','address_1','address_2','address_3','town_city','province_state','province_state_id','zip_postcode','country','country_id'];
	var value = keyString = "";
	var arr_tmp = [];
	for(var i in arr){
		if(arr[i] == "name") continue;
		arr_tmp = arr[i].split("_");
		keyString = "";
		for(var j in arr_tmp)
			keyString += ucfirst(arr_tmp[j]);
		value = $("#window_popup_addresses_company_"+arr[i]+"_"+key+type).val();
		$("#"+ucfirst(type)+keyString).val(value);
	}
	$("#"+ucfirst(type)+keyString).trigger('change');
	$("#window_popup_addresses"+type).data("kendoWindow").close();
}
function ucfirst(string){
    return string.charAt(0).toUpperCase() + string.slice(1);
}
</script>