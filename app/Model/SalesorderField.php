<?php

$ModuleField = array();
$ModuleField = array(
	'module_name' => __('Salesorder'),
	'module_label' => __('Sales Order'),
	'colection' => 'tb_salesorder',
	'title_field' => array('company_name', 'contact_name', 'status', 'user_responsible'),
);


//============= *** FIELDS *** =============//
// Panel 1
$ModuleField['field']['panel_1'] = array(
	'setup' => array(
		'css' => 'width:100%;',
		'lablewith' => '25',
		'blockcss' => 'width:30%;float:left;',
	),
	'code' => array(
		'name' => __('Ref no'),
		'type' => 'text',
		'moreclass' => 'fixbor',
		'width' => '30%;text-align:right;',
		'css' => 'width:50%; padding-left:6.5%;',
		'after_field' => 'sales_order_type',
		'lock' => '',
		'moreinline' => 'Type',
		'element_input' => 'onkeypress="return isCode(event);"',
		'listview' => array(
			'order' => '1',
			'with' => '100',
			'align' => 'center',
			'css' => 'width:5%;',
			'sort' => '1',
		),
	),
	'sales_order_type' => array(
		'name' => __('Type'),
		'type' => 'select',
		'other_type' => 'after_other',
		'droplist' => 'sales_order_type',
		'default' => 'Sales Order',
		'classselect' => 'jt_after_field',
		'width' => '41%;" id="field_after_quotetype" alt="',
		'element_input' => 'combobox_blank="1"',
		'css' => ' width:110%;',
		'not_custom' => '1',
	),
	'mongo_id' => array(
		'type' => 'id',
		'element_input' => ' class="jthidden"',
	),
	'date_modified' => array(
		'type' => 'hidden',
	),
	'created_by' => array(
		'type' => 'hidden',
	),
	'modified_by' => array(
		'type' => 'hidden',
	),
	'description' => array(
		'name' => __('Description'),
		'type' => 'hidden',
	),
	'asset_status' => array(
		'name' => __('Asset Status'),
		'type' => 'hidden',
		'default' => 'New',
	),
	'company_name' => array(
		'name' => __('Company'),
		'type' => 'relationship',
		'cls' => 'companies',
		'list_syncname' => 'company_name',
		'id' => 'company_id',
		'css' => 'padding-left:2%;',
		'lock' => '0',
		'listview' => array(
			'order' => '1',
			'with' => '15',
			'css' => 'width:15%;',
			'sort' => '1',
		),
	),
	'company_id' => array(
		'type' => 'id',
		'element_input' => ' class="jthidden"',
	),
	'contact_name' => array(
		'name' => __('Contact'),
		'type' => 'relationship',
		'cls' => 'contacts',
		'list_syncname' => 'contact_name',
		'syncname' => 'first_name',
		'id' => 'contact_id',
		'css' => 'padding-left:2%;',
		'para' => ',get_para_contact()',
		'listview' => array(
			'order' => '1',
			'with' => '10',
			'css' => 'width:10%;',
			'sort' => '1',
		),
	),
	'contact_id' => array(
		'type' => 'id',
		'element_input' => ' class="jthidden"',
	),
	'phone' => array(
		'name' => __('Phone'),
		'type' => 'phone',
		'css' => 'padding-left:2%;',
	),
	'email' => array(
		'name' => __('Email'),
		'type' => 'email',
		'css' => 'padding-left:2%;',
	),
	'salesorder_date' => array(
		'name' => __('Order date'),
		'type' => 'date',
		'css' => 'padding-left:2%;',
		'listview' => array(
			'order' => '1',
			'with' => '6',
			'css' => 'width:6%;',
			'sort' => '1',
		),
	),
	'payment_due_date' => array(
		'name' => __('Due date'),
		'type' => 'date',
		'css' => 'padding-left:2.5%;',
		'listview' => array(
			'order' => '1',
			'with' => '6',
			'css' => 'width:6%;',
			'sort' => '1',
		),
	),
	'our_rep' => array(
		'name' => __('Our rep'),
		'type' => 'relationship',
		'cls' => 'contacts',
		'id' => 'our_rep_id',
		'syncname' => 'first_name',
		'para' => ',get_para_employee()',
		'not_custom' => '1',
	),
	'our_rep_id' => array(
		'type' => 'id',
		'element_input' => ' class="jthidden"',
	),
	'our_csr' => array(
		'name' => __('Our CSR'),
		'type' => 'relationship',
		'cls' => 'contacts',
		'id' => 'our_csr_id',
		'syncname' => 'first_name',
		'para' => ',get_para_employee()',
		'not_custom' => '1',
	),
	'our_csr_id' => array(
		'type' => 'id',
		'element_input' => ' class="jthidden"',
	),
	'datetime_pickup' => array(
		'name' => __('Pickup time'),
		'type' => 'text',
		'css' => 'padding-left:2%;'
	),
	'datetime_delivery' => array(
		'name' => __('Delivery time'),
		'type' => 'text',
		'css' => 'padding-left:2%;'
	)

);

$ModuleField['field']['panel_2'] = array(
	'setup' => array(
		'css' => 'width:70%;',
		'lablewith' => '35', //%
		'blockcss' => 'width:69%;float:right;',
		'blocktype' => 'address',
	),
	'invoice_address' => array(
		'name' => __('Invoice address'),
		'type' => 'text',
	),
	'shipping_address' => array(
		'name' => __('Shipping address'),
		'type' => 'text',
	),
);


// Panel 4
$ModuleField['field']['panel_4'] = array(
	'setup' => array(
		'css' => 'width:33%;',
		'lablewith' => '35',
	),

	'status' => array(
		'name' => __('Status'),
		'type' => 'select',
		'droplist' => 'salesorders_status',
		'default' => 'New',
		'not_custom' => '1',
		'element_input' => 'combobox_blank="1"',
		'listview' => array(
			'order' => '3',
			'with' => '5',
			'css' => 'width:5%;',
			'sort' => '1',
		),
	),
	'payment_terms' => array(
		'name' => __('Payment terms'),
		'type' => 'select',
		'droplist' => 'salesinvoices_payment_terms',
		'element_input' => 'combobox_blank="1"',
		'width' => '41%',
		'css' => 'padding-left:4.5%;',
		'after' => '<div class="jt_after float_left" id="mx_payment_terms">&nbsp;days</div>',
		'default' => 0,
	),
	'tax' => array(
		'name' => __('Tax %'),
		'type' => 'select',
		'droplist' => 'product_pst_tax',
		'default' => 'AB',
		'element_input' => 'combobox_blank="1"',
	),
	'taxval' => array(
		'name' => __('Tax'),
		'type' => 'hidden',
		'default' => '5',
	),
	'customer_po_no' => array(
		'name' => __('Customer PO no'),
		'type' => 'text',
	),
	'heading' => array(
		'name' => __('Heading'),
		'type' => 'text',
		'listview' => array(
			'order' => '1',
			'css' => 'width:15%;',
			'sort' => '1',
		),
	),
	'name' => array(
		'type' => 'hidden',
	),
	'job_name' => array(
		'name' => __('Job'),
		'type' => 'relationship',
		'cls' => 'jobs',
		'id' => 'job_id',
		'before_field' => 'job_number',
		'list_syncname' => 'job_number',
		'width' => '44.5%',
		'css' => 'float:left;',
		'not_custom' => '1',
		'listview' => array(
			'order' => '2',
			'with' => '5',
			'css' => 'width:5%;text-align: center;',
			'sort' => '1',
		),
	),
	'job_number' => array(
		'name' => __(''),
		'type' => 'text',
		'other_type' => '1',
		'width' => '15%',
		'lock' => '1',
		'css' => 'width:91%;float:left;padding-left:5%;padding-right: 2%;',
	),
	'job_id' => array(
		'type' => 'id',
		'element_input' => ' class="jthidden"',
	),
	'quotation_name' => array(
		'name' => __('Quotation'),
		'type' => 'relationship',
		'cls' => 'quotations',
		'id' => 'quotation_id',
		'syncname' => 'name',
		'before_field' => 'quotation_number',
		'width' => '44.5%',
		'css' => 'float:left;',
		'not_custom' => '1',
		'listview' => array(
			'order' => '1',
			'with' => '15',
			'css' => 'width:15%;',
			'sort' => '1',
		),
	),
	'quotation_number' => array(
		'type' => 'text',
		'other_type' => '1',
		'width' => '15%',
		'lock' => '1',
		'css' => 'width:91%;float:left;padding-left:5%;padding-right: 2%;',
	),
	'quotation_id' => array(
		'type' => 'id',
		'element_input' => ' class="jthidden"',
	),
	'delivery_method' => array(
		'name' => __('Delivery method'),
		'droplist' => 'salesorder_delivery_method',
		'element_input' => 'combobox_blank="1"',
		'type' => 'select',
	),
	'shipper' => array(
		'name' => __('Shipper'),
		'type' => 'relationship',
		'cls' => 'companies',
		'id' => 'shipper_id',
		'para' => ',get_company_is_shipper()',
	),
	'shipper_id' => array(
		'name' => __(''),
		'type' => 'id',
		'element_input' => ' class="jthidden"',
	),
	'shipper_account' => array(
		'name' => __('Shipper account'),
		'type' => 'text',
	),
	'had_paid'	=>array(
			'name' 		=> __('Have paid'),
			'type' 		=> 'checkbox',
			'css'		=> 'padding-left:2%; border-bottom:none',
			'default'	=> 0,
	),

	'products' => array(
		'type' => 'fieldsave',
		'rel_name' => 'products',
	),
	'none1'	=>array(
			'type' 		=> 'not_in_data',
			),
);

$ModuleField['field']['panel_5'] = array(
	'setup' => array(
		'css' => 'width:33%;',
		'lablewith' => '35',
	),
	'quotation_name' => array(
		'name' => __('Total Quotation'),
		'type' => 'relationship@price',
		'cls' => 'quotations',
		'id' => 'quotation_id',
		'width' => '30.5%',
		'list_syncname'=>'sum_sub_total',
		'not_custom' => '1',
		'isInt'=>1,
		'numformat'=>2,
		'listview' => array(
			'order' => '1',
			'with' => '15',
			'css' => 'width:15%;text-align: right;',
			'sort' => '1',
		),
	),
	'sum_amount' => array(
		'type' => 'price',
		'name' => __(''),

	),
	'sum_sub_total' => array(
		'type' => 'price',
		'name' => __('Total sales'),
		'element_input' => ' class="jthidden"',
		'listview' => array(
			'order' => '2',
			'css' => 'width:10%; text-align: right',
			'sort' => 1,
		),
	),
	'sum_tax' => array(
		'type' => 'fieldsave',
	),
);


//============ *** RELATIONSHIP *** =============//
//====== LINE ENTRY =======//
$ModuleField['relationship']['line_entry']['name'] =  __('Line entry');

//Line entry Details
$ModuleField['relationship']['line_entry']['block']['products'] = array(
	'title'	=>__('Details'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '0; height: auto !important; min-height: 250px;',
	'add'	=> __('Add line'),
	'custom_box_bottom' => '1',
	'custom_box_top' => '1',
	//'link'		=> array('w'=>'1', 'cls'=>'products'),
	'reltb'		=> 'tb_salesorder@products',//tb@option
	'delete' => '1',
	'field'=> array(
				'code' => array(
					'name' 		=>  __('Code'),
					'type'	=> 'hidden',
				),
				'sku' => array(
					'name' 		=>  __('SKU'),
					'type'	=> 'link_icon',
					'link_field'	=> 'products_id',
					'module_rel'	=> 'products',
					'popup_title'	=> 'Specify Products',
					'popup_key'	=> 'change',
					'width'=>'5',
					'align' => 'left',
					'indata' => '0',
					'edit'=>'1',
					'para'=>'"?no_supplier=1&products_product_type=Product"',
				),
				'products_name' => array(
					'name' 		=>  __('Name / details'),
					'width'=>'17',
					'edit'	=> '1',
					'default'=> 'Click for edit',
					//comment
				),
				'products_id' => array(
					'name' 		=>  __('Products ID'),
					'type' =>'hidden',
				),
				'details'	=> array(
				    'name' 	=>  __('Details'),
					'type' 	=> 'link_detail',
					'width'	=> '1',
					'align' => 'center',
					'edit'=>'1',
				),
				'option'	=>array(
					'name' 	=>  __('Opt'),
					'type' 	=> 'link_plus',
					'width'	=> '2',
					'align' => 'center',
					'edit'=>'1',
				),
				'sizew'	=>array(
						'name' 		=>  __('Size-W'),
						'type' 		=> 'price',
						'width'		=>'3',
						'edit'		=>'1',
						),
				'sizew_unit'	=>array(
						'name' 		=> __(''),
						'type' 		=> 'select',
						'droplist'	=> 'product_oum_size',
						'default'	=> 'in',
						'element_input' => 'combobox_blank="1"',
						'not_custom'=>'1',
						'edit'		=>'1',
						'width'		=>'2',
						),
				'sizeh'	=>array(
						'name' 		=>  __('Size-H'),
						'type' 		=> 'price',
						'width'		=>'3',
						'edit'		=>'1',
						),
				'sizeh_unit'	=>array(
						'name' 		=> __(''),
						'type' 		=> 'select',
						'droplist'	=> 'product_oum_size',
						'default'	=> 'in',
						'element_input' => 'combobox_blank="1"',
						'not_custom'=>'1',
						'edit'		=>'1',
						'width'		=>'2',
						),
				'area'	=>array(
						'name' 		=>  __('Area'),
						'type' 		=> 'hidden',
						),
				// 'receipts'	=>array(
				// 	'name' 	=>  __('RFQ'),
				// 	'type' 	=> 'link_add',//'link_add',
				// 	'width'	=>'2',
				// 	'align' => 'center',
				// 	'edit'	=> '1',
				// 	),
				'sell_by'		=>array(
					'name' 		=>  __('Sold by'),
					'type' 		=> 'select',
					'width'		=>'4',
					'default'	=> 'area',
					'element_input' => 'combobox_blank="1"',
					'droplist'	=> 'product_sell_by',
					'edit'		=> '1',
					),
				'vip' =>array(
					'name' 		=>  __('VIP'),
					'type' 	 	=> 'checkbox',
					'width'		=> 1,
					'align' 	=> 'center',
					'edit'		=> 1
				),
				'sell_price'	=>array(
					'name' 		=>  __('Sell price'),
					'type' 		=> 'hidden',
					'width'		=> '5',
					'align' 	=> 'right',
					'edit'		=> '1',
					),
				'plus_sell_price'	=>array(
					'name' 		=>  __('Plus Sell price'),
					'type' 		=> 'hidden',
					'width'		=> '5',
					'align' 	=> 'right',
					'edit'		=> '1',
					),

				'oum'		=>array(
					'name' 		=>  __(''),
					'type' 		=> 'select_dynamic',
					'droplist'	=> 'product_oum_area',
					'default'	=> 'Sq.ft.',
					'element_input' => 'combobox_blank="1"',
					'width' 	=>'4',
					'edit'		=> '1',
					),
				'unit_price'		=>array(
					'name' 		=>  __('Rec. price'),
					'title'		=> 'Recommended price',
					// 'getValue' => 'custom_unit_price',
					'type' 		=> 'price',
					'width'=>'5',
					'default'=> '0',
					'align' => 'right',
					'edit'		=> '1',
					),
				'plus_unit_price'	=>array(
					'name' 		=>  __('Plus Unit price'),
					'type' 		=> 'hidden',
					'width'		=> '5',
					'align' 	=> 'right',
					'edit'		=> '1',
					),
				'quantity' => array(
					'name' 		=>  __('Quantity'),
					'type' => 'price',
					'align' => 'right',
					'width'=>'3',
					'edit'	=> '1',
					'numformat' => 1,
					'isInt' => '1',
					'default'=> '1',
				),
				'custom_unit_price'		=>array(
					'name' 		=>  __('Unit price'),
					'type' 		=> 'price',
					'width'=>'5',
					'default'=> '0',
					'edit'		=>1,
					'align' => 'right',
					'numformat' => 3,
				),
				'adj_qty' => array(
					'name' 		=>  __('Adj Qty'),
					'type' => 'price',
					'align' => 'right',
					'width'=>'2',
					'numformat' => 2,
				),
				'sub_total' => array(
					'name' 		=>  __('Sub total'),
					'width'=>'5',
					'align' => 'right',
					'type' => 'text',
					'default'=> '0',
				),
				'taxper' => array(
					'name' 		=>  __('Tax %'),
					'type' => 'hidden',
					'align' => 'right',
					'default'=> '0',
					'edit'	=> '1',
				),
				'tax' => array(
					'name' 		=>  __('Tax'),
					'width'=>'4',
					'align' => 'right',
					'default'=> '0',
				),
				'amount' => array(
					'name' 		=>  __('Amount'),
					'width'=>'5',
					'align' => 'right',
					'default'=> '0',
				),
				'docket_check' => array(
					'name' 		=>  '',
					'width'=>'1',
					'type'		=>'checkbox',
					'default'=> '0',
					'edit'	=> '1',
				),
				'option_for' => array(
					'type' => 'hidden',
					'width'=>'0',
				),
			),
);


//====== TEXT ENTRY =======//
$ModuleField['relationship']['text_entry']['name'] =  __('Text entry');

//Text entry Details
$ModuleField['relationship']['text_entry']['block']['products'] = array(
	'title'	=>__('Details'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '264',
	'add'	=> __('Add line'),
	'custom_box_bottom' => '1',
	'custom_box_top' => '1',
	//'link'		=> array('w'=>'1', 'cls'=>'products'),
	'reltb'		=> 'tb_salesorder@products',//tb@option
	'delete' => '1',
	'linecss'=>'h_entry',
	// 'cellcss'=>'h_entryin',
	'full_height' => '1',
	'field'=> array(
				'products_name' => array(
					'name' 		=>  __('Name / details'),
					'width'=>'27',
					'edit'	=> '1',
					'type' => 'textarea',
					'default'=> 'Click for edit',

				),
				'products_costing_name' => array(
					'name' 		=>  __('Costing name'),
					'width'=>'',
					'edit'	=> '0',
					'type' => 'hidden',
					'mod'		=>'text',
				),
				'products_id' => array(
					'name' 		=>  __('Products ID'),
					'type' =>'id',
				),
				'option'	=>array(
					'name' 	=>  __('Opt'),
					'type' 	=> 'link_plus',
					'width'	=> '2',
					'align' => 'center',
				),
				'sizew'	=>array(
						'name' 		=>  __('Size-W'),
						'type' 		=> 'price',
						'width'		=>'3',
						'edit'		=>'1',
						'mod'		=>'text',
						),
				'sizew_unit'	=>array(
						'name' 		=> __(''),
						'type' 		=> 'select',
						'droplist'	=> 'product_oum_size',
						'default'	=> 'inch',
						'not_custom'=>'1',
						'edit'		=>'1',
						'width'		=>'2',
						'mod'		=>'text',
						),
				'sizeh'	=>array(
						'name' 		=>  __('Size-H'),
						'type' 		=> 'price',
						'width'		=>'3',
						'edit'		=>'1',
						'mod'		=>'text',
						),
				'sizeh_unit'	=>array(
						'name' 		=> __(''),
						'type' 		=> 'select',
						'droplist'	=> 'product_oum_size',
						'default'	=> 'inch',
						'not_custom'=>'1',
						'edit'		=>'1',
						'width'		=>'2',
						'mod'		=>'text',
						),

				'receipts'	=>array(
					'name' 	=>  __('RFQ'),
					'type' 	=> 'link_add',
					'width'	=>'2',
					'align' => 'center',
					'edit'	=> '1',
					),

				'sell_by'		=>array(
					'name' 		=>  __('Sold by'),
					'type' 		=> 'select',
					'width'		=>'4',
					'droplist'	=> 'product_sell_by',
					'edit'		=> '1',
					'mod'		=>'text',
					),

				'sell_price'		=>array(
					'name' 		=>  __('Sell price'),
					'type' 		=> 'price',
					'width'=>'5',
					'align' => 'right',
					'edit'	=> '1',
					'mod'		=>'text',
					),
				'oum'		=>array(
					'name' 		=>  __(''),
					'type' 		=> 'select_dynamic',
					'droplist'	=> 'product_oum_area',
					'width' 	=>'4',
					'edit'		=> '1',
					'mod'		=>'text',
					),
				'unit_price'		=>array(
					'name' 		=>  __('Unit price'),
					'type' 		=> 'price',
					'width'=>'5',
					'align' => 'right',
					'mod'		=>'text',
					),
				'quantity' => array(
					'name' 		=>  __('Quantity'),
					'type' => 'price',
					'align' => 'right',
					'width'=>'3',
					'edit'	=> '1',
					'numformat' => 1,
					'isInt' => '1',
					'default'=> '0',
					'mod'		=>'text',
				),
				'adj_qty' => array(
					'name' 		=>  __('Adj Qty'),
					'type' => 'price',
					'align' => 'right',
					'width'=>'3',
					'numformat' => 2,
				),
				'sub_total' => array(
					'name' 		=>  __('Sub total'),
					'width'=>'5',
					'align' => 'right',
					'type' => 'text',
					'mod'		=>'text',
				),
				'taxper' => array(
					'name' 		=>  __('Tax %'),
					'type' => 'hidden',
					'mod'  =>'text',
				),
				'tax' => array(
					'name' 		=>  __('Tax'),
					'indata' => '0',
					'width'=>'5',
					'align' => 'right',
					'mod'		=>'text',
				),
				'amount' => array(
					'name' 		=>  __('Amount'),
					'width'=>'5',
					'align' => 'right',
					'mod'		=>'text',
				),
			),
);


//====== TASK =======//
$ModuleField['relationship']['tasks']['name'] = __('Task');
//dùng hàm cũ của Nam
//====== SHIP/INVOICE =======//
$ModuleField['relationship']['shippings']['name'] = __('Shippings');

$ModuleField['relationship']['shippings']['block']['shippings'] = array(
	'title' => __('Shipping for this sales order'),
	'type' => 'listview_box',
//	'add' => __('Add line'),
	'footlink' => array('label'=>'Click to view'),
	'custom_box_bottom' => '1',
	'custom_box_top' => '1',
	'link' => array('w' => '2', 'cls' => 'shippings','field' => '_id',),
	'css' => 'width:77%;margin-top:0;',
	'height' => '192',
	'reltb' => 'tb_shippings@products',
	'field' => array(
		'code' => array(
			'name' => __('Ref no'),
			'width' => '6',
			'align'=>'center',
			'default' => 'Click for edit',
			'type' => 'text',
		),
		'id' => array(
			'type'=>'id',
		),
		'shipping_type' => array(
			'name' => __('Type'),
			'width' => '6',
			'align'=>'center',
			'default' => 'Click for edit',
		),
		'return_status' => array(
			'name' => __('Return'),
			'type'=>'checkbox',
			'width' => '5',
			'align'=>'center',
			'default' => 'Click for edit',
		),
		'shipping_date' => array(
			'name' => __('Date'),
			'width' => '10',
			'align'=>'center',
			'type'=>'date',
			'default' => 'Click for edit',
		),
		'shipping_status' => array(
			'name' => __('Status'),
			'width' => '7',
			'align'=>'left',
			'default' => 'Click for edit',
		),
		'our_rep' => array(
			'name' => __('Our Rep'),
			'width' => '10',
			'align'=>'left',
			'default' => 'Click for edit',
		),
		'shipper' => array(
			'name' => __('Shipper'),
			'width' => '10',
			'align'=>'left',
			'default' => 'Click for edit',
		),
		'heading' => array(
			'name' => __('Heading'),
			'width' => '30',
			'align'=>'left',
			'default' => 'Click for edit',
		),
	),
);
$ModuleField['relationship']['shippings']['block']['shipping_comment'] = array(
		'title' => __('Comments on shipping'),
		'type' => 'text_box',
		'css'	=>'width:22%;margin-top:0px;margin-left:1%; float: left',
		'height' => '192',
		'textarea_css'	=> 'height:178px;padding: 10px 1%;',
		'field' =>array(
			'shipping_comment' =>array(
				'name' =>'',
				'type'=>'text',
			),
		),
	);
$ModuleField['relationship']['invoices']['name'] = __('Invoices');
$ModuleField['relationship']['invoices']['block']['invoices'] = array(
	'title' => __('Invoices for this sales order'),
	'type' => 'listview_box',
	'css' => 'width:100%;margin-top:0;',
	'height' => '192',
	'custom_box_top' => '1',
	'custom_box_bottom' => '1',
//	'add' => __('Add line'),
	'footlink' => array('label'=>'Click to view'),
	'link' => array('w' => '2', 'cls' => 'salesinvoices','field'=>'_id'),
	'reltb' => 'tb_salesinvoices@products', //tb@option
	'field' => array(
		'code' => array(
			'name' => __('Ref no'),
			'width' => '6',
			'align'=>'center',
			'default' => 'Click for edit',
		),
		'invoice_type' => array(
			'name' => __('Type'),
			'width' => '10',
			'align'=>'center',
			'default' => 'Click for edit',
		),
		'invoice_date' => array(
			'name' => __('Date'),
			'type'=>'date',
			'align'=>'center',
			'width' => '10',
			'default' => 'Click for edit',
		),
		'sum_amount' => array(
			'name' => __('Total'),
			'width' => '15',
			'type'=>'price',
			'numformat'=>'2',
			'align'=>'right',
			'default' => 'Click for edit',
		),
		'invoice_status' => array(
			'name' => __('Status'),
			'width' => '10',
			'align'=>'center',
			'default' => 'Click for edit',
		),
		'our_rep' => array(
			'name' => __('Our rep'),
			'width' => '15',
			'align'=>'center',
			'default' => 'Click for edit',
		),
		'other_comment' => array(
			'name' => __('Comments'),
			'width' => '22',
			'align'=>'left',
			'default' => 'Click for edit',
		),


	),
);

//====== DOCUMENTS =======//
$ModuleField['relationship']['documents']['name'] = __('Documents');
//dùng hàm cũ của Nam
//Line entry Details
$ModuleField['relationship']['documents']['block']['docs'] = array(
	'title' => __('Document / file management'),
	'type' => 'listview_box',
	'css' => 'width:100%;margin-top:0;',
	'height' => '150',
	'add' => __('Add document'),
	'link' => array('w' => '2', 'cls' => 'docs'),
	'reltb' => 'tb_document@docs', //tb@option
	'delete' => '1',
	'field' => array(
		'docs_id' => array(
			'name' => __('Document ID'),
			'type' => 'id',
		),
		'file_name' => array(
			'name' => __('Document / file name'),
			'width' => '26',
			'indata' => '0',
		),
		'location' => array(
			'name' => __('Location'),
			'width' => '10',
			'indata' => '0',
		),
		'error' => array(
			'name' => __('Error'),
			'width' => '9',
			'indata' => '0',
		),
		'category' => array(
			'name' => __('Category'),
			'width' => '8',
			'indata' => '0',
		),
		'ext' => array(
			'name' => __('Ext'),
			'type' => 'checkbox',
			'width' => '3',
			'indata' => '0',
		),
		'types' => array(
			'name' => __('Type'),
			'width' => '5',
			'indata' => '0',
		),
		'version' => array(
			'name' => __('Version'),
			'indata' => '0',
			'width' => '5',
		),
		'description' => array(
			'name' => __('Description'),
			'indata' => '0',
			'width' => '20',
		),
	),
);




//====== Commission =======//
// $ModuleField['relationship']['commission']['name'] = __('Commission');
// //Line entry Details
// $ModuleField['relationship']['commission']['block']['commission'] = array(
// 	'title' => __('Commission list'),
// 	'type' => 'listview_box',
// 	'css' => 'width:100%;margin-top:0;',
// 	'height' => '150',
// 	//'add' => __('Add Employee'),
// 	'link' => array('w' => '2', 'cls' => 'contacts','field'=>'employee_id'),
// 	'reltb' => 'tb_salesorder@commission', //tb@option
// 	//'delete' => '1',
// 	'field' => array(
// 		'no' => array(
// 			'name' => __('No.'),
// 			'width' => '5',
// 			'indata' => '0',
// 		),
// 		'fullname' => array(
// 			'name' => __('Fullname'),
// 			'width' => '15',
// 		),
// 		'employee_id' => array(
// 			'name' => __('employee_id'),
// 			'width' => '0',
// 			'type'=>'hidden',
// 		),
// 		'per_com' => array(
// 			'name' => __('Commission (%)'),
// 			'width' => '12',
// 			'align'=>'right',
// 			'type' => 'price',
// 			'indata' => '0',
// 		),
// 		'price_com' => array(
// 			'name' => __('Commission (CAD)'),
// 			'width' => '12',
// 			'align'=>'right',
// 			'type' => 'price',
// 			'indata' => '0',
// 		),
// 		'group' => array(
// 			'name' => __('Group'),
// 			'width' => '8',
// 			'type' => 'select',
// 			//'edit'=>'1',
//         	'droplist' => 'employee_type',
// 		),
// 		'note' => array(
// 			'name' => __('Note'),
// 			'width' => '20',
// 			'edit'=>'1',
// 		),
// 	),
// );



//Production
$ModuleField['relationship']['asset_tags']['name'] =  __('Asset Tags');
$ModuleField['relationship']['asset_tags']['block']['asset_tags'] = array(
	'title'	=>__('Asset Tags of products'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'link'	=> array('w'=>'1', 'cls'=>'products','field'=>'product_id'),
	'height' => '280',
	'custom_box_bottom' => '1',
	'custom_box_top' => '1',
	'reltb'		=> 'tb_salesorder@production',
	'field'=> array(
	            'product_id' => array(
	                'name' 	=>  __('Product ID'),
					'type'  =>'hidden',
	            ),
	            'key' => array(
	                'name' 	=>  __('Product Key'),
					'type'  =>'hidden',
	            ),
	            'asset_key' => array(
	                'name' 	=>  __('Product Key'),
					'type'  =>'hidden',
	            ),
				'code' => array(
					'name' 	=>  __('Code'),
					'type'  =>'text',
					'width'=>'4',
					'align'=>'center',
				),
				'products_name' => array(
					'name' 		=>  __('Name'),
					'type'  =>'text',
					'width'=>'19',
					'edit' => 1
				),
				'product_type' => array(
					'name' 		=>  __('Type'),
					'width'=>'8',
					'type' => 'select',
        			'droplist' => 'product_type',
				),
				'tag' => array(
					'name' 		=>  __('Asset Tags'),
					'width'=>'12',
					'type' => 'text',
				),
				'factor' => array(
					'name' 		=>  __('Factor'),
					'width'=>'5',
					'type' => 'price',
					'align'=>'right',
					'edit'=>'1',
				),
				'min_of_uom' => array(
					'name' 		=>  __('Min minute/UOM'),
					'width'=>'7',
					'type' => 'price',
					'align'=>'right',
					'edit'=>'1',
				),
				'sizew'		=>array(
					'name' 		=>  __('Width'),
					'width'=>'5',
					'type'  =>'hidden',
					'align'=>'right',
					),
				'sizeh'		=>array(
					'name' 		=>  __('Height'),
					'width'=>'5',
					'type'  =>'hidden',
					'align'=>'right',
					),
				'oum' => array(
					'name' 		=>  __('Sold by'),
					'width' =>'5',
					'type'  => 'hidden',
        			'droplist' => 'product_oum_unit',
				),
				'quantity' => array(
					'name' =>  __('Quantity'),
					'type' => 'hidden',
					'width'=>'5',
					'align'=>'right',
					'numformat'=>0,
				),
				'production_time' => array(
					'name' 		=>  __('Production time'),
					'width'=>'8',
					'type' => 'price',
					'align'=>'right',
					'edit' => 1
				),
				'completed' => array(
					'name' 		=>  __('Completed'),
					'width'=>'8',
					'type' => 'checkbox',
					'align'=>'center',
					'edit' => 1
				),
				'delete' => array(
					'name' 		=>  __(''),
					'width'		=> '5',
					'edit'		=> '1',
					'align'		=> 'center',
					'type'      => 'delete_another',
					'node'		=> 'asset_tags',
					'rev'		=> 'asset_tags',
				)
			),
);


$ModuleField['relationship']['costings']['name'] =  __('Costings');
//Line entry Details
$ModuleField['relationship']['costings']['block']['costings'] = array(
	'title'	=>__('Costing details for line entry'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '282',
	'custom_box_bottom' => '1',
	//'link'		=> array('w'=>'1', 'cls'=>'products'),
	'reltb'		=> 'tb_quotation@products',//tb@option
	'field'=> array(
				'code' => array(
					'name' 		=>  __('Code'),
					'type'	=> 'hidden',
				),
				'sku' => array(
					'name' 		=>  __('SKU'),
					'type'	=> 'text',
					'width'=>'7',
					'align' => 'left',
					'indata' => '0',
					'edit'=>'0',
				),
				'products_name' => array(
					'name' 		=>  __('Name / details'),
					'width'=>'30',
					'edit'	=> '0',
				),
				'products_id' => array(
					'name' 		=>  __('Products ID'),
					'type' =>'hidden',
				),
				'sizew'	=>array(
						'name' 		=>  __('Size-W'),
						'type' 		=> 'price',
						'width'		=>'3',
						'edit'		=>'0',
						),
				'sizew_unit'	=>array(
						'name' 		=> __(''),
						'type' 		=> 'text',
						'edit'		=>'0',
						'width'		=>'2',
						),
				'sizeh'	=>array(
						'name' 		=>  __('Size-H'),
						'type' 		=> 'price',
						'width'		=>'3',
						'edit'		=>'0',
						),
				'sizeh_unit'	=>array(
						'name' 		=> __(''),
						'type' 		=> 'text',
						'width'		=>'3',
						'edit'		=>'0',
						),
				'area'	=>array(
						'name' 		=>  __('Area'),
						'type' 		=> 'hidden',
						),
				'sell_by'		=>array(
					'name' 		=>  __('Sold by'),
					'type' 		=> 'text',
					'width'		=>'4',
					'edit'		=> '0',
					),
				'oum'		=>array(
					'name' 		=>  __(''),
					'type' 		=> 'text',
					'width' 	=>'4',
					'edit'		=> '0',
					),
				'quantity' => array(
					'name' 		=>  __('Quantity'),
					'type' => 'text',
					'align' => 'right',
					'width'=>'5',
					'edit'	=> '0',
					'numformat' => 0,
				),
				'cost_amount'		=>array(
					'name' 		=>  __('Cost amount'),
					'type' 		=> 'price',
					'width'=>'7',
					'default'=> '0',
					'align' => 'right',
					),
				'sales_amount'		=>array(
					'name' 		=>  __('Sales price'),
					'type' 		=> 'price',
					'width'=>'7',
					'default'=> '0',
					'align' => 'right',
					),
			),
);
$ModuleField['relationship']['part_shipping']['name'] =  __(' ');
$ModuleField['relationship']['part_shipping']['hidden'] =  true;
$ModuleField['relationship']['part_shipping']['block']['part_shipping'] = array(
	'title'	=>__('Part Shipping'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:50px;',
	'height' => '450',
	'reltb'		=> 'tb_salesorder@products',//tb@option
	'field'=> array(
				'sku' => array(
					'name' 		=>  __('SKU'),
					'type'	=> 'text',
					'width'=>'10',
					'align' => 'left',
				),
				'products_name' => array(
					'name' 		=>  __('Name / details'),
					'width'=>'40',
					'default'=> 'Click for edit',
				),
				'quantity' => array(
					'name' 		=>  __('Quantity'),
					'type' 		=> 'price',
					'width'=>'7',
					'default'=> '0',
					'align' => 'right',
				),
				'shipped' => array(
					'name' 		=>  __('Shipped'),
					'width'=>'7',
					'align' => 'right',
				),
				'balance_shipped' => array(
					'name' 		=>  __('Balance'),
					'width'=>'7',
					'align' => 'right',
				),
				'ship_qty' => array(
					'name' 		=>  __('Ship Now'),
					'type' => 'price',
					'align' => 'right',
					'width'=>'7',
					'edit'	=> '1',
					'numformat' => 1,
				),
			),
);

$ModuleField['relationship']['part_invoice']['name'] =  __(' ');
$ModuleField['relationship']['part_invoice']['hidden'] =  true;

//Line entry Details
$ModuleField['relationship']['part_invoice']['block']['part_invoice'] = array(
	'title'	=>__('Details'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '282',
	//'add'	=> __('Add line'),
	'custom_box_bottom' => '1',
	//'custom_box_top' => '1',
	//'link'		=> array('w'=>'1', 'cls'=>'products'),
	'reltb'		=> 'tb_salesorder@products',//tb@option
	'delete' => '1',
	'field'=> array(
				'code' => array(
					'name' 		=>  __('Code'),
					'type'	=> 'hidden',
				),
				'view_costing' => array(
					'name' 		=>  __(''),
					'type'	=> 'icon_link',
					'label' => 'View costings',
					'url' 	=> 'quotations/costing_list',
					'id'	=> '',
					'width' => '1',
				),
				'sku' => array(
					'name' 		=>  __('SKU'),
					'type'	=> 'link_icon',
					'link_field'	=> 'products_id',
					'module_rel'	=> 'products',
					'popup_title'	=> 'Specify Products',
					'popup_key'	=> 'change',
					'width'=>'5',
					'align' => 'left',
					'indata' => '0',
					'edit'=>'1',
					'para'=>'"?no_supplier=1&products_product_type=Product"',
				),
				'products_name' => array(
					'name' 		=>  __('Name / details'),
					'width'=>'17',
					'edit'	=> '1',
					'default'=> 'Click for edit',
				),
				'products_id' => array(
					'name' 		=>  __('Products ID'),
					'type' =>'hidden',
				),
				'quantity' => array(
					'name' 		=>  __('Quantity'),
					'type' 		=> 'price',
					'width'=>'5',
					'default'=> '0',
					'align' => 'right',
				),
				'invoiced' => array(
					'name' 		=>  __('Invoiced'),
					'type' => 'price',
					'align' => 'right',
					'width'=>'3',
					'edit'	=> '1',
					'numformat' => 1,
					'default'=> '1',
				),
				'balance_invoiced' => array(
					'name' 		=>  __('Balance'),
					'width'=>'5',
					'align' => 'right',
				),
				'shipped' => array(
					'name' 		=>  __('Shipped'),
					'width'=>'5',
					'align' => 'right',
				),
				'balance_shipped' => array(
					'name' 		=>  __('Balance'),
					'width'=>'5',
					'align' => 'right',
				),
				'ship_inv' => array(
					'name' 		=>  __('Ship/Inv Now'),
					'type' => 'price',
					'align' => 'right',
					'width'=>'3',
					'edit'	=> '1',
					'numformat' => 1,
				),
			),
);

//====== OTHER =======//
$ModuleField['relationship']['other']['name'] = __('Other');
//Line entry Details
$ModuleField['relationship']['other']['block']['other'] = array(
	'title' => __('Other'),
	'type' => 'listview_box',
	'css' => 'width:100%;margin-top:0;',
	'height' => '150',
	'add' => __('Add document'),
	'link' => array('w' => '2', 'cls' => 'docs'),
	'reltb' => 'tb_document@docs', //tb@option
	'delete' => '1',
	'field' => array(
		'docs_id' => array(
			'name' => __('Document ID'),
			'type' => 'id',
		),
		'file_name' => array(
			'name' => __('Document / file name'),
			'width' => '26',
			'indata' => '0',
		),
		'location' => array(
			'name' => __('Location'),
			'width' => '10',
			'indata' => '0',
		),
		'error' => array(
			'name' => __('Error'),
			'width' => '9',
			'indata' => '0',
		),
		'category' => array(
			'name' => __('Category'),
			'width' => '8',
			'indata' => '0',
		),
		'ext' => array(
			'name' => __('Ext'),
			'type' => 'checkbox',
			'width' => '3',
			'indata' => '0',
		),
		'types' => array(
			'name' => __('Type'),
			'width' => '5',
			'indata' => '0',
		),
		'version' => array(
			'name' => __('Version'),
			'indata' => '0',
			'width' => '5',
		),
		'description' => array(
			'name' => __('Description'),
			'indata' => '0',
			'width' => '20',
		),
	),
);
$ModuleField['relationship']['productions']['name'] =  __('Production Report');
$ModuleField['relationship']['productions']['block']['productions'] = array(
	'title'	=>__('Details'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '264',
	'reltb'		=> 'tb_salesorder@productions',//tb@option
	'full_height' => '1',
	'custom_box_top' => '1',
	'link' => array('w' => '1', 'cls' => 'products','field'=>'products_id'),
	'field'=> array(
		'products_id' => array(
			'name' 		=>  __('Products ID'),
			'type' =>'hidden',
		),
		'sku' => array(
			'name' 		=>  __('SKU'),
			'type'	=> 'text',
			'width'=> 4,
			'align' => 'left',
			'edit'=> 0,
		),
		'products_name' => array(
			'name' 		=>  __('Name'),
			'type'	=> 'text',
			'width'=> 22,
			'edit'	=> 0,
		),
		'vendor_stock' => array(
			'name' 		=>  __('Vendor Stock'),
			'type'	=> 'text',
			'width'=> '24',
			'edit'	=> 0,
		),
		'total_oum' => array(
			'name' 		=>  __('Total Products Area'),
			'width'=> 8,
			'lock'	=> 1,
			'type'	=> 'price',
			'align' => 'right',
		),
		'material_width' => array(
			'name' 		=>  __('Material Width'),
			'type'	=> 'price',
			'width'=> 7,
			'edit'	=> 1,
			'align' => 'right',
		),
		'material_length' => array(
			'name' 		=>  __('Material Length'),
			'type'	=> 'price',
			'width'=> 7,
			'edit'	=> 1,
			'align' => 'right',
		),
		'bleed'	=> array(
			'name' 		=>  __('Bleed'),
			'type'	=> 'price',
			'width'=> 3,
			'edit'	=> 1,
			'align' => 'right',
		),
		'material_needed' => array(
			'name' 		=>  __('Material Needed'),
			'width'=> 7,
			'lock'	=> 1,
			'type'	=> 'price',
			'align' => 'right',
		),
		'cutting_policy' => array(
			'name'	=> 'Cutting Policy',
			'edit'	=> 0,
			'type'	=> 'text',
			'width'	=> 6
		)
	)
);
$SalesorderField = $ModuleField;
