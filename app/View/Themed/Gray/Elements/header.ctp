<?php if(!isset($print_pdf)): ?>
<div id="header">
	<?php if($action == 'entry' || $action == 'lists'|| $action == 'options'): ?>
	<div class="float_left">
		<a href="<?php echo URL; ?>/" >
		    <div class="back home_icon">
		        <span>&nbsp;</span>
		    </div>
	    </a>
	    <div class="top_opt float_left" style="max-width: 316px;">
	    </div>
	</div>
<?php endif; ?>
    <?php
		if(substr($action, 0, 8) == 'calendar'){
			echo $this->element('../'.ucfirst($controller).'/calendar_menu_change_time');

		}elseif($action == 'entry'){
			echo $this->element('entry_menu');

		}elseif($action == 'options' || $action == 'lists'){
			echo $this->element('lists_menu');

		}elseif($action == 'entry_search'){
			echo $this->element('entry_search_menu');

		}elseif(isset($return_mod)){
			echo $this->element('header/return_mod');

		}else{ ?>
			<div class="float_left">
                <a href="<?php echo URL; ?>/" >
                    <div class="back home_icon">
                        <span>&nbsp;</span>
                    </div>
                </a>
                <div class="top_opt float_left" style="max-width: 316px;">
                </div>
            </div>
	<?php } ?>

	<?php
		//inactive: 0-hoạt động, 1-ẩn, 2-hiện nhưng không hoạt động
		if(!isset($arr_menu))
		$arr_menu = array(
			'crm' => array(
				'name'	=> translate('CRM'),
				'companies' => array(
									'name' => translate('Company'),
									'class' => 'company',
									'inactive' => '0',
								),
				'contacts' => array(
									'name' => translate('Contact'),
									'class' => 'contacts',
									'inactive'	=> '0',
								),
				'communications' => array(
									'name' => translate('Comm'),
									'class' => 'comms',
									'inactive'	=> '0',
								),
				'docs' => array(
									'name' => translate('Doc'),
									'class' => 'docs',
									'inactive'	=> '0',
								),
				'jobs' => array(
									'name' => translate('Job'),
									'class' => 'jobs',
									'inactive'	=> '0',
								),
				'tasks' => array(
									'name' => translate('Task'),
									'class' => 'tasks',
									'inactive'	=> '0',
								),
				'timelogs' => array(
				  					'name' => translate('TimeLog'),
				  					'class' => 'timelog',
				  					'inactive'	=> '1',
				  				),
				'stages' => array(
				 					'name' => translate('Stages'),
				 					'class' => 'stages',
				 					'inactive'	=> '1',
				 				),
			),
			'sales' => array(
				'name'	=> translate('Sales'),
				'enquiries' => array(
									'name' => translate('Enquiry'),
									'class' => 'enquiries',
									'inactive'	=> '0',
								),
				'quotations' => array(
									'name' => translate('Quote'),
									'class' => 'quotes',
									'inactive'	=> '0',
								),
				'salesorders' => array(
									'name' => translate('Sales Ord'),
									'class' => 'sales_ord',
									'inactive'	=> '0',
								),
				'salesaccounts' => array(
									'name' => translate('Sales Acc'),
									'class' => 'sales_acc',
									'inactive'	=> '0',
								),
				'salesinvoices' => array(
									'name' => translate('Sales Inv'),
									'class' => 'sales_inv',
									'inactive'	=> '0',
								),
				'receipts' => array(
									'name' => translate('Receipt'),
									'class' => 'receipts',
									'inactive'	=> '0',
								)
			),
			'inventory' => array(
				'name'	=> translate('Inventory'),

				'products' => array(
								'name' => translate('Product'),
								'class' => 'products',
								'inactive'	=> '0',
								),
				'locations' => array(
								'name' => translate('Location'),
								'class' => 'locations',
								'inactive'	=> '0',
								),
				'units' => array(
								'name' => translate('Units'),
								'class' => 'units',
								'inactive'	=> '0',
								),
				'batches' => array(
								'name' => translate('Batches'),
								'class' => 'batches',
								'inactive'	=> '0',
								),
				'purchaseorders' => array(
								'name' => translate('Pur Order'),
								'class' => 'purch_ord',
								'inactive'	=> '0',
								),
				'shippings' => array(
								'name' => translate('Shipping'),
								'class' => 'shipping',
								'inactive'	=> '0',
								),
			)
		);

		$arr_menu_show_hide = array( 'CRM','Sales','Inventory');
		if( $this->Session->check('arr_menu_show_hide') ){
			$arr_menu_show_hide = $this->Session->read('arr_menu_show_hide');
		}
		if( empty($arr_menu_show_hide) ){
			$arr_menu_show_hide[] = 'CRM';
		}

		$arr_menu = array_reverse($arr_menu);
	?>

    <ul class="nav_header float_right" id="header_nav">
		<?php
			foreach($arr_menu as $keys => $vls){

				$display = ''; $same_class = $vls['name'];

				if( !in_array(trim($same_class), $arr_menu_show_hide) ){ $display = 'style="display:none"'; }

				$vls = array_reverse($vls);
				foreach($vls as $ks => $vs){
		?>
					<?php ?>

					<?php if($ks=='name'){?>

                        <li class="show-group <?php if( $display != '' ){ ?>active<?php } ?>">
                            <a href="javascript:void(0)" class="next" onclick="header_show_item('<?php echo $vs; ?>', this)">
                                <?php echo $vs;?>
                            </a>
                        </li>

                	<?php }else if(isset($vs['inactive']) && $vs['inactive']=='0'){?>

                        <li class="<?php echo $same_class; ?> <?php if($controller == $ks && $action != 'calendar'){ ?>active<?php } ?>" <?php echo $display; ?>>
                            <a href="<?php echo URL.'/'.$ks;?>/entry" class="<?php echo $vs['class'];?>">
                            	<?php echo $vs['name'];?>
                            </a>
                        </li>

					<?php }else if(isset($vs['inactive']) && $vs['inactive']=='2'){?>
                    	<li class="<?php echo $same_class; ?> <?php if($controller == $ks && $action != 'calendar'){ ?>active<?php } ?>" <?php echo $display; ?>>
                        	<a onclick="show_warning_function_in_progress_develop(this); return false;" class="<?php echo $vs['class'];?>">
                            	<?php echo $vs['name'];?>
                            </a>
                        </li>
                    <?php }?>

       <?php
				}
			}
		?>
    </ul>
</div>
<p class="clear_fix">&nbsp;</p>

<script type="text/javascript">
	function header_show_item(myclass, object){
		var contain = $("#header_nav");

		if( $(object).parent("li").hasClass("active") ){
			$("li." + myclass, contain).show();
			$(object).parent("li").removeClass("active");

			var url = '<?php echo URL; ?>/homes/arr_menu_show_hide/1/' + myclass; // active
		}else{
			$("li." + myclass, contain).hide();
			$(object).parent("li").addClass("active");

			var url = '<?php echo URL; ?>/homes/arr_menu_show_hide/0/' + myclass; // hide
		}

		// Gởi ajax về server lưu session header
		$.ajax({
	        url: url,
	        timeout: 15000,
	        success: function(html){
	        	console.log(html);
	        }
	    });
	}
</script>
<?php endif; ?>