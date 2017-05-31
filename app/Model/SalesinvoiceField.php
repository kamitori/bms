<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Salesinvoice'),
	'module_label' 	=> __('Sales Invoice'),
	'colection' 	=> 'tb_salesinvoice',
	'title_field'	=> array('company_name','contact_name','invoice_status','our_rep'),
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
			'after_field'=>'invoice_type',
			'lock'		=> '',
			'element_input' => 'onkeypress="return isCode(event);"',
			'moreinline'=> 'Type',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort'=> '1',
							),
			),
	'invoice_type'	=>array(
			'name' 		=>  __('Type'),
			'type' 		=> 'select',
			'other_type' => 'after_other',
			'droplist'	=> 'salesinvoices_type',
			'default'	=> 'Invoice',
			'classselect' =>'jt_after_field',
			'width'		=> '41%;" id="field_after_quotetype" alt="',
			'element_input' => 'combobox_blank="1"',
			'css'		=> ' width:110%;',
			'not_custom'=> '1',
			),
	'mongo_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'company_name'	=>array(
			'name' 		=>  __('Company'),
			'type' 		=> 'relationship',
			'list_syncname' => 'company_name',
			'cls'		=> 'companies',
			'id'		=> 'company_id',
			'css'		=> 'padding-left:2%;',
			'lock'		=> '0',
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
			'cls'		=> 'contacts',
			'list_syncname' => 'contact_name',
			'syncname'	=> 'first_name',
			'id'		=> 'contact_id',
			'css'		=> 'padding-left:2%;',
			'para'		=> ',get_para_contact()',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'10',
							'css'	=>	'width:7%;',
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
			),
	'email'	=>array(
			'name' 		=> __('Email'),
			'type' 		=> 'email',
			'css'		=> 'padding-left:2%;',
			),

	'invoice_date'	=>array(
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
			'name' 		=>  __('Our rep'),
			'type' 		=> 'relationship',
			'cls'		=> 'contacts',
			'id'		=> 'our_rep_id',
			'syncname'	=>	'first_name',
			'para'		=> ',get_para_employee()',
			'not_custom'=> '1',
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
			'syncname'	=>	'first_name',
			'para'		=> ',get_para_employee()',
			'not_custom'=> '1',
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
	'invoice_status'	=>array(
			'name' 		=>  __('Status'),
			'type' 		=> 'select',
			'droplist'	=> 'salesinvoices_status',
			'default'	=> 'In Progress',
			'not_custom'=> '1',
			'element_input' => 'combobox_blank="1"',
			'listview'	=>	array(
							'order'	=>	'3',
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
			),
	'paid_date'	=>array(
			'name' 		=> __('Paid'),
			'type' 		=> 'date',
			'css'		=> 'padding-left:2.5%;',
			'moreinline' =>'<a href="javascript:void(0)" title="Auto pay (creates receipt) - Not yet implemented"><span class="jt_icon_check"></span></a>',
			'default' => '&nbsp;',
			),
	'payment_due_date'	=>array(
			'name' 		=>  __('Payment due date'),
			'type' 		=> 'date',//'display',
			'moreinline' =>'<a href="javascript:void(0)" title="Find outstanding invoices - Not yet implemented"><span class="jt_icon_search"></span></a>',
			),
	'tax'	=>array(
			'name' 		=>  __('Tax %'),
			'type' 		=> 'select',
			'droplist'	=> 'product_pst_tax',
			'default'	=> 'AB',
			'element_input' => 'combobox_blank="1"',
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
			'listview'	=>	array(
							'order'	=>	'1',
							'css'	=>	'width:15%;',
							'sort'=> '1',
						),
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
			'list_syncname' => 'job_number',
			'width'		=>	'44.5%',
			'css'		=> 'float:left;',
			'not_custom'=> '1',
			'listview'	=>	array(
							'order'	=>	'2',
							'with'	=>	'15',
							'css'	=>	'width:10%;',
							'sort'=> '1',
						),
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
			'width'		=>	'44.5%',
			'css'		=> 'float:left;',
			'not_custom'=> '1',
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
	'quotation_name'	=>array(
			'name' 		=>  __('Quotation'),
			'type' 		=> 'relationship',
			'cls'		=> 'quotations',
			'id'		=> 'quotation_id',
			'before_field'	=> 'quotation_code',
			'width'		=>	'44.5%',
			'css'		=> 'float:left;',
			'not_custom'=> '1',
			'lock'		=> '1',
			),
	'quotation_code'	=>array(
			'name' 		=> __(''),
			'type' 		=> 'text',
			'other_type'=> '1',
			'width'		=> '15%',
			'lock'		=> '1',
			'css'		=> 'width:91%;float:left;padding-left:5%;padding-right: 2%;',
			),
	'quotation_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			'moreclass' => 'fixbor3',
			),
	'products' => array(
			'type'	=>'fieldsave',
			'rel_name'	=>'products',
			),
	'is_part_invoice' => array(
		    'name' 		=>  __('Part Invoice'),
			'type'		=> 'hidden',
		),
	'invoice_status_old' => array(
		'type' => 'hidden'
	)
);
$ModuleField['field']['panel_5'] = array(
	'setup' => array(
		'css' => 'width:33%;',
		'lablewith' => '35',
	),
	'salesorder_name' => array(
		'name' => __('Total Sales order'),
		'type' => 'relationship@price',
		'cls' => 'salesorders',
		'id' => 'salesorder_id',
		'width' => '44.5%',
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
			'css' => 'width:8%; text-align: right',
			'sort' => 1,
		),
	),
	'sum_tax' => array(
		'type' => 'fieldsave',
	),
);

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
					'width'=>'15',
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
					'edit'	=> '1',
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
				'currency' => array(
					'name' 		=>  __('Currency'),
					'type' => 'hidden',
					'droplist'	=> 'currency_type',
					'align' => 'center',
					'width'=>'6',
					'edit'	=> '0',
					'default' => 'cad',
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
					'numformat' => 3,
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

//====== Receipts =======//
$ModuleField['relationship']['receipt']['name'] =  __("Receipts");
$ModuleField['relationship']['receipt']['block']['receipts'] = array(
	'title' => 'Stock currently at this location',
    'css' => 'width:70%;margin-bottom:1%;',
    'height' => '200',
    //'custom_box_bottom' => '1',
	//'custom_box_top' => '1',
   // 'link' => array('w' => '1', 'cls' => 'products'),
    'add' => 'Add receipt',
    'reltb' => 'tb_salesinvoice@receipts', //tb@option
    'type' => 'listview_box',
    'delete' => '1',
	'field'=> array(
		'no' => array(
			'name' => __('Receipt'),
			'type' => 'text',
			'edit' => '1',
			'width' => '6',
		),
		'receipt_date' => array(
			'name' => __('Date'),
			'type' => 'text',
			'edit' => '1',
			'width' => '6',
		),
		'paid_by' => array(
			'name' => __('Paid by'),
			'type' => 'text',
			'edit' => '1',
			'width' => '14',
		),
		'account' => array(
			'name' => __('Account'),
			'type' => 'text',
			'edit' => '1',
			'width' => '6',
		),
		'note' => array(
			'name' => __('Notes'),
			'type' => 'text',
			'edit' => '1',
			'width' => '30',
		),
		'wirte_off' => array(
			'name' => __('Wirte off'),
			'type' => 'checkbox',
			'edit' => '1',
			'width' => '6',
			'align' => 'center'
		),
		'amount' => array(
			'name' => __('Amount'),
			'type' => 'text',
			'edit' => '1',
			'align' => 'right',
			'width' => '10',
		),
	),
);


//====== DOCUMENTS =======//
// Dung chung ham document cua anh nam
$ModuleField['relationship']['documents']['name'] =  __('Documents');
// end document

$ModuleField['relationship']['salesorders']['name'] =  __('Salesorders');
//Line entry Details
$ModuleField['relationship']['salesorders']['block']['salesorders'] = array(
	'title'	=>__('Sales orders'),
	'type'	=>'listview_box',
	'add'	=> __('Add Sales order'),
	'css'	=>'width:100%;margin-top:0;',
	'height' => '282',
	'reltb'		=> 'tb_salesinvoice@salesorders',//tb@option
	'field'=> array(
	             'salesorder_id' => array(
					'type'	=> 'hidden',
				),
	            'link' => array(
					'type'	=> 'link_icon',
					'link_field'	=> 'salesorder_id',
					'module_rel'	=> 'salesorders',
					'width'=>'2',
					'align' => 'left',
				),
				'code' => array(
					'name' 		=>  __('Ref no'),
					'width'=>'10',
					'edit'	=> '0',
				),
				'contact_name' => array(
					'name' 		=>  __('Contact'),
					'width'=>'10',
					'edit'	=> '0',
				),
				'salesorder_date' => array(
					'name' 		=>  __('Date in'),
					'type' 		=> 'text',
					'width'		=>'10',
					'edit'		=>'0',
				),
				'payment_due_date'	=>array(
						'name' 		=>  __('Due date'),
						'type' 		=> 'text',
						'width'		=>'10',
						'edit'		=>'0',
						),
				'status'	=>array(
						'name' 		=> __('Status'),
						'type' 		=> 'text',
						'edit'		=>'0',
						'width'		=>'10',
						),
				'our_rep'	=>array(
						'name' 		=>  __('Assign to'),
						'type' 		=> 'text',
						'width'		=>'10',
						'edit'		=>'0',
						),
				'sum_sub_total'	=>array(
						'name' 		=> __('Sub total'),
						'type' 		=> 'text',
						'width'		=>'7',
						'edit'		=>'0',
						'align' => 'right',
						),
				'sum_tax'	=>array(
						'name' 		=> __('Tax'),
						'type' 		=> 'text',
						'width'		=>'7',
						'edit'		=>'0',
						'align' => 'right',
						),
				'sum_amount'	=>array(
						'name' 		=> __('Amount'),
						'type' 		=> 'text',
						'width'		=>'7',
						'edit'		=>'0',
						'align' => 'right',
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
$SalesinvoiceField = $ModuleField;