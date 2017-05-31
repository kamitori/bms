<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Quotation'),
	'module_label' 	=> __('Quotation'),
	'colection' 	=> 'tb_quotation',
	'title_field'	=> array('company_name','contact_name','quotation_status','our_rep'),
);


//============= *** FIELDS *** =============//

// Panel 1
$ModuleField['field']['panel_1'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '25',
			'blockcss' => 'width:30%;float:left;',
			),
	'code'	=>array(
			'name' 		=> __('Ref no'),
			'type' 		=> 'text',
			'moreclass' => 'fixbor',
			'width'		=> '30%;text-align:right;',
			'css'		=> 'width:50%; padding-left:6.5%;',
			'after_field'=>'quotation_type',
			'lock'		=> '1',
			'moreinline'=> 'Type',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort'=> '1',
							),
			),
	'quotation_type'	=>array(
			'name' 		=>  __('Type'),
			'type' 		=> 'select',
			'other_type' => 'after_other',
			'droplist'	=> 'quotations_type',
			'default'	=> 'Quotation',
			'classselect' =>'jt_after_field',
			'width'		=> '41%;" id="field_after_quotetype" alt="',
			'element_input' => 'combobox_blank="1"',
			'css'		=> ' width:110%;',
			'not_custom'=> '1',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
							'sort'=> '1',
						),
			),
	'mongo_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'date_modified'=>array(
			'type' 		=> 'hidden',
			),
	'created_by'=>array(
			'type' 		=> 'hidden',
			),
	'modified_by'=>array(
			'type' 		=> 'hidden',
			),
	'description'=>array(
			'name' 		=>  __('Description'),
			'type' 		=> 'hidden',
			),
	'company_name'	=>array(
			'name' 		=>  __('Company'),
			'type' 		=> 'relationship',
			'cls'		=> 'companies',
			'list_syncname' => 'company_name',
			'id'		=> 'company_id',
			'css'		=> 'padding-left:2%;',
			'lock'		=> '0',
			'readonly'		=> 'readonly',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'15',
							'css'	=>	'width:15%;',
							'sort'=> '1',
						),
			),
	'company_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'contact_name'	=>array(
			'name' 		=>  __('Contact'),
			'type' 		=> 'relationship',
			'list_syncname' => 'contact_name',
			'cls'		=> 'contacts',
			'id'		=> 'contact_id',
			'lock'		=> '0',
			'readonly'		=> 'readonly',
			'css'		=> 'padding-left:2%;',
			'para'		=> ',get_para_contact()',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'10',
							'css'	=>	'width:10%;',
							'sort'=> '1',
						),
			),
	'contact_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),

	'phone'	=>array(
			'name' 		=> __('Phone'),
			'type' 		=> 'phone',
			'css'		=> 'padding-left:2%;',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
							'sort'=> '1',
						),
			),
	'email'	=>array(
			'name' 		=> __('Email'),
			'type' 		=> 'email',
			'css'		=> 'padding-left:2%;',
			),

	'quotation_date'	=>array(
			'name' 		=> __('Date'),
			'type' 		=> 'date',
			'css'		=> 'padding-left:2%;',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
							'sort'=> '1',
						),
			),
	'our_rep'	=>array(
			'name' 		=>  __('Our Rep'),
			'type' 		=> 'relationship',
			'cls'		=> 'contacts',
			'list_syncname' => 'our_rep',
			'id'		=> 'our_rep_id',
			'para'		=> ',get_para_employee()',
			'not_custom'=> '1',
			'syncname'	=> 'first_name',
			'listview'	=>	array(
							'order'	=>	'1',
							'css'	=>	'width:10%;',
							'sort'=> '1',
						),
			),
	'our_rep_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'our_csr'	=>array(
			'name' 		=>  __('Our CSR'),
			'type' 		=> 'relationship',
			'cls'		=> 'contacts',
			'id'		=> 'our_csr_id',
			'para'		=> ',get_para_employee()',
			'not_custom'=> '1',
			'syncname'	=> 'first_name',
			),
	'our_csr_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'none'	=>array(
			'type' 		=> 'not_in_data',
			'moreclass' => 'fixbor2',
			),
);

$ModuleField['field']['panel_2'] = array(
	'setup'	=> array(
			'css'	=> 'width:70%;',
			'lablewith' => '35',//%
			'blockcss' => 'width:69%;float:right;',
			'blocktype'=> 'address',
			),
	'invoice_address' =>array(
			'name' 		=> __('Invoice address'),
			'type' 		=> 'text',
			),
	'shipping_address' =>array(
			'name' 		=> __('Shipping address'),
			'type' 		=> 'text',
			),
);


// Panel 4
$ModuleField['field']['panel_4'] = array(
	'setup'	=> array(
			'css'	=> 'width:33%;',
			'lablewith' => '35',
			),
	'payment_due_date'	=>array(
			'name' 		=> __('Due date'),
			'type' 		=> 'date',
			'css'		=> 'padding-left:2.5%;',
			),
	'quotation_status'	=>array(
			'name' 		=>  __('Status'),
			'type' 		=> 'select',
			'droplist'	=> 'quotations_status',
			'default'	=> 'In progress',
			'not_custom'=> '1',
			'element_input' => 'combobox_blank="1"',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
							'sort'=> '1',
						),
			),
	'payment_terms'		=>array(
			'name' 		=>  __('Payment terms'),
			'type' 		=> 'select',
			'droplist'	=> 'salesinvoices_payment_terms',
			'width'		=> '41%',
			'css'		=> 'padding-left:4.5%;',
			'after'		=> '<div class="jt_after float_left" id="mx_payment_terms">&nbsp;days</div>',
			'default'	=> 0,
			),
	'tax'	=>array(
			'name' 		=>  __('Tax %'),
			'type' 		=> 'select',
			'droplist'	=> 'product_pst_tax',
			'default'	=> 'AB',
			),
	'taxval'	=>array(
			'name' 		=>  __('Tax'),
			'type' 		=> 'hidden',
			'default'	=> '5',
			),
	'customer_po_no'	=>array(
			'name' 		=>  __('Customer PO no'),
			'type' 		=> 'text',
			),
	'heading'	=>array(
			'name' 		=>  __('Heading'),
			'type' 		=> 'text',
			),
	'name'	=>array(
			'type' 		=> 'hidden',
			),
	'job_name'	=>array(
			'name' 		=>  __('Job'),
			'type' 		=> 'relationship',
			'cls'		=> 'jobs',
			'id'		=> 'job_id',
			'before_field'	=> 'job_number',
			'width'		=>	'44.5%',
			'css'		=> 'float:left;',
			'not_custom'=> '1',
			),
	'job_number'	=>array(
			'name' 		=> __(''),
			'type' 		=> 'text',
			'other_type'=> '1',
			'width'		=> '15%',
			'lock'		=> '1',
			'css'		=> 'width:91%;float:left;padding-left:5%;padding-right: 2%;',
			),
	'job_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'salesorder_name'	=>array(
			'name' 		=>  __('Sales order'),
			'type' 		=> 'relationship',
			'cls'		=> 'salesorders',
			'id'		=> 'salesorder_id',
			'before_field'	=> 'salesorder_number',
			'list_syncname' => 'salesorder_name',
			'width'		=>	'44.5%',
			'css'		=> 'float:left;',
			'not_custom'=> '1',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'15',
							'css'	=>	'width:15%;',
							'sort'=> '1',
						),
			),
	'salesorder_number'	=>array(
			'name' 		=> __(''),
			'type' 		=> 'text',
			'other_type'=> '1',
			'width'		=> '15%',
			'lock'		=> '1',
			'css'		=> 'width:91%;float:left;padding-left:5%;padding-right: 2%;',
			),
	'salesorder_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'none'	=>array(
			'type' 		=> 'not_in_data',
			'moreclass' => 'fixbor3',
			),
);

$ModuleField['field']['panel_5'] = array(
	'setup' => array(
		'css' => 'width:33%;',
		'lablewith' => '35',
	),
	'sum_amount' => array(
		'type' => 'price',
		'name' => __('QT Amount'),
		'listview' => array(
			'order' => '1',
			'css' => 'width:7%; text-align: right',
			'sort' => 1,
		),
	),
	'sum_tax' => array(
		'type' => 'hidden',
		'name' => __('Sum tax'),
		'edit' => '1',
	),
	'sum_sub_total' => array(
		'type' => 'hidden',
		'name' => __('Sum sub total'),
		'edit' => '1',
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
	'reltb'		=> 'tb_quotation@products',//tb@option
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
				'receipts'	=>array(
					'name' 	=>  __('RFQ'),
					'type' 	=> 'link_add',//'link_add',
					'width'	=>'2',
					'align' => 'center',
					'edit'	=> '1',
					),
				'sell_by'		=>array(
					'name' 		=>  __('Sold by'),
					'type' 		=> 'select',
					'width'		=>'4',
					'default'	=> 'area',
					'element_input' => 'combobox_blank="1"',
					'droplist'	=> 'product_sell_by',
					'edit'		=> '1',
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
					'name' 		=>  __('OUM'),
					'type' 		=> 'select_dynamic',
					'droplist'	=> 'product_oum_area',
					'default'	=> 'Sq.ft.',
					'element_input' => 'combobox_blank="1"',
					'width' 	=>'4',
					'edit'		=> '1',
					),
				'vip' =>array(
					'name' 		=>  __('VIP'),
					'type' 	 	=> 'checkbox',
					'width'		=> 1,
					'align' 	=> 'center',
					'edit'		=> 1
				),
				'unit_price'		=>array(
					'name' 		=>  __('Rec. price'),
					'title'		=> 'Recommended price',
					'type' 		=> 'price',
					'width'=>'5',
					'default'=> '0',
					'align' => 'right',
					// 'getValue' => 'custom_unit_price',
					'numformat' => 3,
					'edit'		=> '1',
					),
				'plus_unit_price'	=>array(
					'name' 		=>  __('Plus Unit price'),
					'type' 		=> 'hidden',
					'width'		=> '5',
					'align' 	=> 'right',
					'numformat' => 3,
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
					'numformat' => 3,
					'align' => 'right',
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
					'width'=>'5',
					'align' => 'right',
					'default'=> '0',
				),
				'amount' => array(
					'name' 		=>  __('Amount'),
					'width'=>'5',
					'align' => 'right',
					'default'=> '0',
				),
				'option_for' => array(
					'type' => 'hidden',
					'width'=>'0',
				),
				'is_printer' =>array(
					'type' 	 => 'hidden',
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
	'reltb'		=> 'tb_quotation@products',//tb@option
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
					'name' 		=>  __('OUM'),
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

//====== RFQ'S =======//
$ModuleField['relationship']['rfqs']['name'] =  __("RFQ's");
$ModuleField['relationship']['rfqs']['block']['rfqs'] = array(
	'title'	=>__("RFQ's for items on this quote"),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '282',
	'link'		=> array('w'=>'2', 'cls'=>'rfqs'),
	'reltb'		=> 'tb_quotation@rfqs',//tb@option
	'custom_box_bottom' => '1',
	'custom_box_top' => '1',
	'delete'	=> '1',
	'field'=> array(
				'print' => array(
					'name' 		=>  __(' '),
					//'type' =>'icon',
					'type' =>'id',
					'width'=>'2',
					'indata' => '0',
				),
				'rfq_status' => array(
					'name' 		=>  __('Status'),
					'type' 		=>'select',
					'droplist'	=> 'rfq_status',
					'width'=>'5',
					'default'=>'Not sent',
				),
				'rfq_no' => array(
					'name' 		=>  __('RFQ no'),
					'width'=>'4',
				),
				'rfq_code' => array(
					'name' 		=>  __('Code'),
					'width'=>'4',
				),
				'name_details'		=>array(
					'name' 		=>  __('Name/details'),
					'width'=>'20',
					'indata' => '0',
					),
				'company_name' => array(
						'name' 		=>  __('Supplier'),
						'width'=>'15',
						'type' => 'text',
						'cls'=>'companies',
						'para'		=> ',get_para_customer_company()',
					),
				'company_id' => array(
					'type' => 'hidden',
				),
				'employee_name' => array(
						'name' 		=>  __('Employee'),
						'type' 		=> 'hidden',
						'cls'		=> 'contacts',
						'id'		=> 'employee_id',
						'syncname'	=> 'first_name',
						'para'		=> ',get_para_employee()',
						'not_custom'=> '1',
					),
				'employee_id' => array(
					'type' => 'id',
				),
				'unit_price_quoted' => array(
					'name' 		=>  __('Unit price quoted'),
					'type'		=> 'price',
					'align'		=> 'right',
					'width'		=>'8',
				),
				'rfq_date' => array(
					'name' 		=>  __('Date'),
					'type'		=> 'date',
					'align'		=> 'right',
					'width'		=>'5',
				),
				'deadline_date' => array(
					'name' 		=>  __('Deadline'),
					'type'		=> 'date',
					'align'		=> 'right',
					'width'		=>'5',
				),
				'late' => array(
					'name' 		=>  __('Late'),
					'type'		=> 'checkbox',
					'default'	=> 0,
					'width'		=>'3',
				),
				'include_name_details' => array(
					'name' 		=>  __('Include name/details'),
					'type'		=> 'hidden',
					'label'		=> '&nbsp;',
					'css'		=> 'width:98%;margin-left:0%;',
					'checkcss'	=> 'margin-left:3%;',
					'default'	=> 0,
					'width'		=>'3',
				),
				'include_signature' => array(
					'name' 		=>  __('Include signature'),
					'type'		=> 'hidden',
					'label'		=> '&nbsp;',
					'css'		=> 'width:98%;margin-left:0%;',
					'checkcss'	=> 'margin-left:3%;',
					'default'	=> 0,
					'width'		=>'3',
				),
				'first_name'	=>array(
						'name' 		=>  __('First name'),
						'type' 		=> 'hidden',
						'cls'		=> 'contacts',
						'id'		=> 'first_name_id',
						'syncname'	=>	'first_name',
						'para'		=> ',get_para_is_customer()',
						'not_custom'=> '1',
						),
				'first_name_id'	=>array(
						'type' 		=> 'id',
						),
				'last_name' => array(
					'name' 		=>  __('Last name'),
					'type'		=> 'hidden',
				),
				'supplier_email' => array(
					'name' 		=>  __('Supplier email'),
					'type'		=> 'hidden',
				),
				'supplier_quote_ref' => array(
					'name' 		=>  __('Supplier quote ref'),
					'type'		=> 'hidden',
				),
				'internal_notes' => array(
					'name' 		=>  __('Internal notes'),
					'type'		=> 'text',
					'width'		=>'12',
					'block'		=> array(
									'internal_notes' => array(
										'title'	=>__('Internal note'),
										'type'	=>'text_box',
										'css'	=>'width:100%; clear:left; float:left;margin-top:3%;margin-left:0;',
										'height' => '200',
										'insert_timestamp' => __('Insert Timestamp'),
										'textarea_css'	=> 'height:200px; padding: 10px 2%;width:96%;',
									),
					),
				),
				'details_for_request' => array(
					'name' 		=>  __('Details'),
					'type'		=> 'hidden',
					'block'		=> array(
									'details_for_request' => array(
										'title'	=>__('Details for request'),
										'type'	=>'text_box',
										'css'	=>'width:100%; float:right;',
										'height' => '630',
										'insert_timestamp' => __('Insert Timestamp'),
										'textarea_css'	=> 'height:600px; padding: 10px 2%;width:96%;',
									),
					),
				),

			),
);

//====== DOCUMENTS =======//
// Dung chung ham document cua anh nam
$ModuleField['relationship']['documents']['name'] =  __('Documents');
// end document


//$ModuleField['relationship']['production']['name'] =  __('Production');

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
	'reltb'		=> 'tb_quotation@production',
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
					'type'  =>'text',
					'align'=>'right',
					),
				'sizeh'		=>array(
					'name' 		=>  __('Height'),
					'width'=>'5',
					'type'  =>'text',
					'align'=>'right',
					),
				'oum' => array(
					'name' 		=>  __('Sold by'),
					'width' =>'5',
					'type'  => 'select',
        			'droplist' => 'product_oum_unit',
				),
				'quantity' => array(
					'name' =>  __('Quantity'),
					'type' => 'price',
					'width'=>'5',
					'align'=>'right',
					'numformat'=>0,
				),
				'production_time' => array(
					'name' 		=>  __('Production time'),
					'width'=>'8',
					'type' => 'price',
					'align'=>'right',
				),
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
						),
				'sizeh_unit'	=>array(
						'name' 		=> __(''),
						'type' 		=> 'text',
						'width'		=>'3',
						),
				'area'	=>array(
						'name' 		=>  __('Area'),
						'type' 		=> 'hidden',
						),
				'sell_by'		=>array(
					'name' 		=>  __('Sold by'),
					'type' 		=> 'text',
					'width'		=>'4',
					),
				'oum'		=>array(
					'name' 		=>  __(''),
					'type' 		=> 'text',
					'width' 	=>'4',
					),
				'cost_price'		=>array(
					'name' 		=>  __('Cost price'),
					'type' 		=> 'text',
					'width'=>'7',
					'default'=> '0',
					'align' => 'right',
					),
				'quantity' => array(
					'name' 		=>  __('Quantity'),
					'type' => 'text',
					'align' => 'right',
					'width'=>'5',
					'edit'	=> '0',
					'numformat' => 0,
				),
				'adj_qty' => array(
					'name' 		=>  __('Adj Qty'),
					'type' => 'price',
					'align' => 'right',
					'width'=>'5',
					'numformat' => 2,
				),
				'amount' => array(
					'name' 		=>  __('Amount'),
					'width'=>'10',
					'type' => 'price',
					'align' => 'right',
					'default'=> '0',
				),
			),
);

//====== OTHER =======//
$ModuleField['relationship']['other']['name'] =  __('Other');
//Line entry Details
$ModuleField['relationship']['other']['block']['other'] = array(
	'title'	=>__('Other'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '150',
	'add'	=> __('Add document'),
	'link'		=> array('w'=>'2', 'cls'=>'docs'),
	'reltb'		=> 'tb_document@docs',//tb@option
	'delete' => '1',
	'field'=> array(
				'docs_id' => array(
					'name' 		=>  __('Document ID'),
					'type' =>'id',
				),
				'file_name' => array(
					'name' 		=>  __('Document / file name'),
					'width'=>'26',
					'indata' => '0',
				),
				'location' => array(
					'name' 		=>  __('Location'),
					'width'=>'10',
					'indata' => '0',
				),
				'error' => array(
					'name' 		=>  __('Error'),
					'width'=>'9',
					'indata' => '0',
				),
				'category'		=>array(
					'name' 		=>  __('Category'),
					'width'=>'8',
					'indata' => '0',
					),
				'ext'		=>array(
					'name' 		=>  __('Ext'),
					'type'		=> 'checkbox',
					'width'=>'3',
					'indata' => '0',
					),
				'types' => array(
					'name' 		=>  __('Type'),
					'width'=>'5',
					'indata' => '0',
				),
				'version' => array(
					'name' 		=>  __('Version'),
					'indata' => '0',
					'width'=>'5',
				),
				'description' => array(
					'name' 		=>  __('Description'),
					'indata' => '0',
					'width'=>'20',
				),
			),
);

$QuotationField = $ModuleField;