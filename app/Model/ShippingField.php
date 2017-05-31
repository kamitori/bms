<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Shipping'),
	'module_label' 	=> __('Shipping'),
	'colection' 	=> 'tb_shipping',
	'title_field'	=> array('shipping_type','company_name','shipping_status','our_rep'),
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
			'width'		=> '20%',
			// 'after'		=> '<div class="jttype">Type: <span class="bold">IN</span></div>',
			'after_field'=> 'return_status',
			'lock'		=> '1',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort'=> '1',
							),
			),
	'shipping_type'	=>array(
			'name' 		=>  __('Type'),
			'type' 		=> 'select',
			'other_type' => 'after_other',
			'droplist'	=> 'shipping_types',
			'default'	=> 'In',
			'classselect' =>'jt_after_field',
			'width'		=> '38%',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
						),
			),
	'return_status'	=>array(
			'label' 	=>  __('Return'),
			'type' 		=> 'checkbox',
			'other_type'=> 'after_other',
			'default'	=> '0',
			'classselect' =>'jt_after_field',
			'width'		=> '25%',
			//'css'		=> 'position:absolute;margin-top:4px;',
			'checkcss' => 'float:right;margin-left:34px;margin-top:2px;margin-bottom:2px;',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
						),
			),
	'mongo_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'company_name'	=>array(
			'name' 		=>  __('Company'),
			'type' 		=> 'relationship',
			'cls'		=> 'companies',
			'id'		=> 'company_id',
			'list_syncname'=>'company_name',
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
			'syncname'	=> 'first_name',
			'list_syncname'=>'contact_name',
			'id'		=> 'contact_id',
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

	'shipping_date'	=>array(
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
			'list_syncname'=>'our_rep',
			'syncname'	=>	'first_name',
			'para'		=> ',get_para_employee()',
			'not_custom'=> '1',
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
			'syncname'	=>	'first_name',
			'para'		=> ',get_para_employee()',
			'not_custom'=> '1',
			),
	'our_csr_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'customer_po_no' =>array(
                 'name' => __(''),
                 'type' => 'text',
                 ),
	'none1'	=>array(
			'type' 		=> 'not_in_data',
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

	'shipping_address' =>array(
			'name' 		=> __('Shipping address'),
			'type' 		=> 'text',
			),
	'invoice_address' =>array(
			'name' 		=> __('Invoice address'),
			'type' 		=> 'text',
			),
);


// Panel 4
$ModuleField['field']['panel_4'] = array(
	'setup'	=> array(
			'css'	=> 'width:33%;',
			'lablewith' => '35',
			),
	'shipping_status'	=>array(
			'name' 		=>  __('Status'),
			'type' 		=> 'select',
			'droplist'	=> 'shipping_statuses',
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

	'shipper'	=>array(
			'name' 		=>  __('Shipper'),
			'type' 		=> 'relationship',
			'cls'		=> 'companies',
			'id'		=> 'shipper_id',
			'para'		=> ',get_para_customer_shipper()',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'15',
							'css'	=>	'width:15%;',
						),
			),
	'tracking_no'	=>array(
		'name' 		=>  __('Tracking no'),
		'type' 		=> 'text',
		'default'	=> '',
	),
	'shipper_id'	=>array(
			'type' 			=> 'id',
			'element_input' => ' class="jthidden"',
			),

	'shipping_cost'	=>array(
			'name' 		=>  __('Shipping Cost'),
			'type' 		=> 'price',
			'default'	=> '0',
			'css'		=> 'text-align:left;',
			),
	'received_date'	=>array(
			'name' 		=> __('Date received'),
			'type' 		=> 'date',
			),
	'heading'	=>array(
			'name' 		=>  __('Heading'),
			'type' 		=> 'text',
			),
	'name'	=>array(
			'type' 		=> 'hidden',
			),
	'customer_po_no' => array(
		'name' => __('Customer PO no'),
		'type' => 'text',
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
			'moreclass' => 'fixbor3',
			),
	'salesinvoice_name'	=>array(
			'name' 		=>  __('Sales invoice'),
			'type' 		=> 'relationship',
			'cls'		=> 'salesinvoices',
			'id'		=> 'salesinvoice_id',
			'before_field'	=> 'salesinvoice_number',
			'width'		=>	'44.5%',
			'css'		=> 'float:left;',
			'not_custom'=> '1',
			),
	'salesinvoice_number'	=>array(
			'name' 		=> __(''),
			'type' 		=> 'text',
			'other_type'=> '1',
			'width'		=> '15%',
			'lock'		=> '1',
			'css'		=> 'width:91%;float:left;padding-left:5%;padding-right: 2%;',
			),
	'salesinvoice_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			'moreclass' => 'fixbor3',
			),

	'products' => array(
			'type'	=>'fieldsave',
			'rel_name'	=>'products',
			),
	'tax'	=>array(
			'name' 		=>  __('Tax %'),
			'type' 		=> 'hidden',
			'droplist'	=> 'product_pst_tax',
			'default'	=> 'AB',
			),
	'taxval'	=>array(
			'name' 		=>  __('Tax'),
			'type' 		=> 'hidden',
			'default'	=> '5',
			),

);




//============ *** RELATIONSHIP *** =============//

//====== LINE ENTRY =======//
$ModuleField['relationship']['line_entry']['name'] =  __('Line entry');
$ModuleField['relationship']['line_entry']['block']['products'] = array(
	'title'	=>__('Details'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '282',
	'add'	=> __('Add line'),
	'custom_box_top' => '1',
	'link'		=> array('w'=>'1', 'cls'=>'products'),
	'reltb'		=> 'tb_shipping@products',//tb@option
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
				'sizew'	=>array(
						'name' 		=>  __('Size-W'),
						'type' 		=> 'price',
						'width'		=>'5',

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
						'width'		=>'3',
						),
				'sizeh'	=>array(
						'name' 		=>  __('Size-H'),
						'type' 		=> 'price',
						'width'		=>'5',
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
						'width'		=>'3',
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
				'area'	=>array(
						'name' 		=>  __('Area'),
						'type' 		=> 'hidden',
						),
				'quantity' => array(
					'name' 		=>  __('Qty.'),
					'type' => 'price',
					'align' => 'right',
					'width'=>'5',
					'edit'	=> '1',
					'default'=> '0',
					'isInt' => '1',
					'numformat'=>0,
				),
				'prev_shipped' => array(
					'name' 		=>  __('Prev.'),
					'type' => 'text',
					'align' => 'right',
					'width'=>'5',
					'default'=> '1',
				),
				'shipped' => array(
					'name' 		=>  __('Now'),
					'type' => 'price',
					'align' => 'right',
					'width'=>'5',
					'edit'	=> '1',
					'default'=> '0',
					'isInt' => '1',
					'numformat'=>0,
				),
				'balance_shipped' => array(
					'name' 		=>  __('Balance'),
					'type' => 'text',
					'align' => 'right',
					'width'=>'5',
					'edit'	=> '0',
					'default'=> '0',
				),
			),
);

//====== TEXT ENTRY =======//
$ModuleField['relationship']['text_entry']['name'] =  __('Text entry');
$ModuleField['relationship']['text_entry']['block']['products'] = array(
	'title'	=>__('Details'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '264',
	'add'	=> __('Add line'),
	'custom_box_top' => '1',
	'link'		=> array('w'=>'1', 'cls'=>'products'),
	'reltb'		=> 'tb_shipping@products',//tb@option
	'delete' => '1',
	'linecss'=>'h_entry',
	// 'cellcss'=>'h_entryin',
	'full_height' => '1',
	'field'=> array(
				'products_name' => array(
					'name' 		=>  __('Name / details'),
					'width'=>'46',
					'edit'	=> '1',
					'type' => 'textarea',
					'default'=> 'Click for edit',
				),
				'products_id' => array(
					'name' 		=>  __('Products ID'),
					'type' =>'id',
				),
				'sizew'	=>array(
						'name' 		=>  __('Size-W'),
						'type' 		=> 'price',
						'width'		=>'5',
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
						'width'		=>'3',
						'mod'		=>'text',
						),
				'sizeh'	=>array(
						'name' 		=>  __('Size-H'),
						'type' 		=> 'price',
						'width'		=>'5',
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
						'width'		=>'3',
						'mod'		=>'text',
						),
				'quantity' => array(
						'name' 		=>  __('Qty.'),
						'type' => 'text',
						'align' => 'right',
						'width'=>'5',
						'edit'	=> '1',
						'default'=> '0',
						'mod'		=>'text',
						),
				'prev_shipped' => array(
						'name' 		=>  __('Prev.'),
						'type' => 'text',
						'align' => 'right',
						'width'=>'5',
						'edit'	=> '0',
						'default'=> '0',
						'mod'		=>'text',
						),
				'shipped' => array(
						'name' 		=>  __('Now'),
						'type' => 'text',
						'align' => 'right',
						'width'=>'5',
						'edit'	=> '1',
						'default'=> '0',
						'mod'		=>'text',
						),
				'balance_shipped' => array(
						'name' 		=>  __('Balance'),
						'type' => 'text',
						'align' => 'right',
						'width'=>'5',
						'edit'	=> '0',
						'default'=> '0',
						),
			),
);

//====== Tracking =======//
$ModuleField['relationship']['tracking']['name'] =  __('Tracking');
$ModuleField['relationship']['tracking']['block']['tracking_detail'] = array(
	'title'	=>__('Tracking details'),
	'type'	=>'editview_box',
	'css'	=>'width:35%;margin-top:0;',
	'height' => '350',
	'field'=> array(
		'shipper_tracking' => array(
			'name' => __('Shipper'),
			'type' => 'relationship',
			'cls' => 'companies',
			'id' => 'shipper_id',
			'css'=>'width: 95%;padding: 0 3%;',
			'para'		=> ',get_para_customer_shipper()',
		),
		'shipper_id_tracking' => array(
			'type' => 'id',
			'element_input' => ' class="jthidden"',
		),
		'tracking_no' => array(
			'name' 		=>  __('Tracking no'),
			'width'=>'26',
			'type' =>'text',
		),
		'shipping_method_detail' => array(
			'name' 		=>  __('Shipping method'),
			'type' 		=> 'select',
			'droplist'	=> 'shipping_method',
			'default'	=> 'Standard',
			'not_custom'=>'1',
			'edit'		=>'1',
		),
		'date_received_detail' => array(
			'name' 		=>  __('Date received'),
			'width'=>'26',
			'type' 		=> 'date'
		),
		'signed_by_detail' => array(
			'name' 		=>  __('Signed by'),
			'type' =>'relationship',
			'cls' => 'contacts',
			'id' => 'signed_by_detail_id',
		),
		'signed_by_detail_id' => array(
			'type' => 'id',
			'element_input' => ' class="jthidden"',
		),
	),
);
$ModuleField['relationship']['tracking']['block']['web_tracker'] = array(
	'title' => __('Web tracker : '),
	'type' => 'web_tracker',
	'css' => 'width:64%;margin-left:1%;',
	'height' => '360'
);

//====== DOCUMENTS =======//
// Dung chung ham document cua anh nam
$ModuleField['relationship']['documents']['name'] =  __('Documents');
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

//====== OTHER =======//
$ModuleField['relationship']['other']['name'] =  __('Other');
$ModuleField['relationship']['other']['block']['other_pricing'] = array(
	'title'	=>__('Other pricing detail'),
	'type'	=>'editview_box',
	'css'	=>' height:178px; width:23%; float: left',
	'height' => '178',
	'link'		=> array('w'=>'2', 'cls'=>'shippings'),
	'reltb'		=> 'tb_shipping@shippings',//tb@option
	'field'=> array(
		'record_entry_type' => array(
			'name' => __('Record entry type'),
			'type' => 'select',
			'droplist' => 'record_entry_type_droplist',
			'default' => '',
		),
		'fax' => array(
			'name' => __('Fax'),
			'type' => 'text'
		),
		'name' => array(
			'name' => __('Heading'),
			'type' => 'text'
		),
		'use_own_letter_head' => array(
			'name' => __('Use own letter head'),
			'type' => 'checkbox',
			'default' => 0
		),
	),
);
$ModuleField['relationship']['other']['block']['other_comment'] = array(
		'title' => __('Comments on shipping'),
		'type' => 'text_box',
		'css'	=>'width:22%;margin-top:0px;margin-left:1%; float: left',
		'height' => '178',
		'textarea_css'	=> 'height:178px;padding: 10px 1%;',
		'field' =>array(
			'other_comment' =>array(
				'name' =>'',
				'type'=>'text',
			),
		),
	);



$ShippingField = $ModuleField;