<?php

$ModuleField = array();
$ModuleField = array(
    'module_name' => __('Purchaseorder'),
    'module_label' => __('Purchase Order'),
    'colection' => 'tb_purchaseorder',
    'title_field' => array('company_name', 'contact_name', 'invoice_status', 'our_rep'),
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
        'css' => 'padding-left:2%;',
        'moreclass' => 'fixbor',
        'lock' => '1',
        'listview' => array(
            'order' => '1',
            'css' => 'width:5%;',
            'sort' => '1',
        ),
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
    'company_name' => array(
        'name' => __('Company'),
        'type' => 'relationship',
        'list_syncname'=> 'company_name',
        'cls' => 'companies',
        'id' => 'company_id',
        'css' => 'padding-left:2%;',
        'para' => ',"?is_supplier=1"',
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
        'id' => 'contact_id',
        'list_syncname'=> 'contact_name',
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
    'contact_last_name' => array(
        'type' => 'hidden'
    ),
    'phone' => array(
        'name' => __('Phone'),
        'type' => 'phone',
        'css' => 'padding-left:2%;',
    ),
    'fax' => array(
        'name' => __('Fax'),
        'type' => 'phone',
        'css' => 'padding-left:2%;',
    ),
    'email' => array(
        'name' => __('Email'),
        'type' => 'email',
        'css' => 'padding-left:2%;',
    ),
    'purchord_date' => array(
        'name' => __('Date'),
        'type' => 'date',
        'css' => 'padding-left:2%;',
        'listview' => array(
            'order' => '1',
            'with' => '5',
            'css' => 'width:5%;',
            'sort' => '1',
        ),
    ),
    'our_rep' => array(
        'name' => __('Our rep'),
        'type' => 'relationship',
        'cls' => 'contacts',
        'id' => 'our_rep_id',
        'list_syncname'=> 'our_rep',
        'para' => ',get_para_employee()',
        'not_custom' => '1',
        'listview' => array(
            'order' => '1',
            'css' => 'width:10%;',
            'sort' => '1',
        ),
    ),
    'our_rep_id' => array(
        'type' => 'id',
        'element_input' => ' class="jthidden"',
    ),
    'none' => array(
        'type' => 'not_in_data',
        'moreclass' => 'fixbor2',
    ),
);

// Panel 3
$ModuleField['field']['panel_2'] = array(
    'setup' => array(
        'css' => 'width:33%;',
        'lablewith' => '45', //%
        'blockcss' => 'width:69%;float:right;',
    ),
    'name' => array(
        'name' => __('Heading'),
        'type' => 'text',
        'moreclass' => 'fixbor',
    ),
    'required_date' => array(
        'name' => __('Required date'),
        'type' => 'date',
        'moreclass' => 'fixbor3',
    ),
    'supplier_quote_ref' => array(
        'name' => __('Supplier quote ref'),
        'type' => 'text',
    ),
    'ship_to_company_name' => array(
        'name' => __('Ship to: Company'),
        'type' => 'relationship',
        'cls' => 'companies',
        'id' => 'ship_to_company_id',
        'css' => 'padding-left:2%;',
        'para' => ',"?name=anvy"',
        'lock' => '0',
        'not_custom' => '1',
    ),
    'ship_to_company_id' => array(
        'type' => 'id',
        'element_input' => ' class="jthidden"',
    ),
    'ship_to_contact_name' => array(
        'name' => __('Ship to: Contact'),
        'type' => 'relationship',
        'cls' => 'contacts',
        'id' => 'ship_to_contact_id',
        'css' => 'padding-left:2%;',
        'para' => ',get_para_ship_contact()',
        'lock' => '0',
        'not_custom' => '1',
    ),
    'ship_to_contact_id' => array(
        'type' => 'id',
        'element_input' => ' class="jthidden"',
    ),
    'tracking_no' => array(
        'name' => __('Tracking no'),
        'type' => 'text',
    ),
    'shipper_company_name' => array(
        'name' => __('Shipper'),
        'type' => 'relationship',
        'cls' => 'companies',
        'id' => 'shipper_company_id',
        'css' => 'padding-left:2%;',
        'para' => ',get_company_is_shipper()',
        'lock' => '0',
        'not_custom' => '1',
    ),
    'shipper_company_id' => array(
        'type' => 'id',
        'element_input' => ' class="jthidden"',
    ),
    'none4' => array(
        'type' => 'not_in_data',
    ),
    'none5' => array(
        'type' => 'not_in_data',
        'moreclass' => 'fixbor2'
    ),
);

// Panel 3
$ModuleField['field']['panel_3'] = array(
    'setup' => array(
        'css' => 'width:35%;',
        'lablewith' => '35', //%
        'blockcss' => 'width:35%;float:right;',
        'blocktype' => 'address',
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
    'purchase_orders_status' => array(
        'name' => __('Status'),
        'type' => 'select',
        'droplist' => 'purchase_orders_status',
        'default' => 'In progress',
        'css' => 'width:100%;padding-left:3%;',
        'width' => '60%',
        'not_custom' => '1',
        'listview' => array(
            'order' => '1',
            'with' => '5',
            'css' => 'width:5%;',
            'sort' => '1',
        ),
    ),
    'delivery_date' => array(
        'name' => __('Received on'),
        'type' => 'date',
        'css' => 'padding-left:2.5%;',
    ),
    'received_by_contact_name' => array(
        'name' => __('Received by'),
        'type' => 'relationship',
        'cls' => 'contacts',
        'id' => 'received_by_contact_id',
        'css' => 'padding-left:2%;',
        'para' => ',get_para_ship_contact()',
        'lock' => '0',
        'not_custom' => '1',
    ),
    'received_by_contact_id' => array(
        'type' => 'id',
        'element_input' => ' class="jthidden"',
    ),
    'payment_terms' => array(
        'name' => __('Payment terms'),
        'type' => 'select',
        'droplist' => 'salesinvoices_payment_terms',
        'width' => '41%',
        'css' => 'padding-left:4.5%;',
        'after' => '<div class="jt_after float_left" id="mx_payment_terms">&nbsp;days</div>',
        'default' => 0,
    ),
    'tax' => array(
        'name' => __('Tax %'),
        'type' => 'select',
        'droplist' => 'product_pst_tax',
        'css' => 'width:100%;padding-left:3%;',
        'width' => '60%',
        'default' => 'AB',
		'lock'=>'0',
		'element_input' => 'combobox_blank="1"',
    ),
    'job_name' => array(
        'name' => __('Job'),
        'type' => 'relationship',
        'cls' => 'jobs',
        'id' => 'job_id',
        'before_field' => 'job_number',
        'width' => '44.5%',
        'css' => 'float:left;',
        'not_custom' => '1',
    ),
    'job_number' => array(
        'name' => __(''),
        'type' => 'text',
        'lock' => '1',
        'other_type' => '1',
        'width' => '15%',
        'css' => 'width:91%;float:left;padding-left:5%;padding-right: 2%;',
    ),
    'job_id' => array(
        'type' => 'id',
        'element_input' => ' class="jthidden"',
    ),
    'salesorder_name' => array(
        'name' => __('Sales order'),
        'type' => 'relationship',
        'cls' => 'salesorders',
        'id' => 'salesorder_id',
        'syncname' => 'heading',
        'list_syncname'=> 'salesorder_name',
        'before_field' => 'salesorder_number',
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
    'salesorder_number' => array(
        'name' => __(''),
        'type' => 'text',
        'other_type' => '1',
        'width' => '15%',
        'lock' => '1',
        'css' => 'width:91%;float:left;padding-left:5%;padding-right: 2%;',
    ),
    'salesorder_id' => array(
        'type' => 'id',
        'element_input' => ' class="jthidden"',
    ),
    'none0' => array(
        'type' => 'not_in_data',
    ),
//	'none1'	=>array(
//		'type' 		=> 'not_in_data',
//	),
    'none2' => array(
        'type' => 'not_in_data',
        'moreclass' => 'fixbor3', //3 : không bo 2: bo
    ),
    'products' => array(
        'type' => 'fieldsave',
        'rel_name' => 'products',
    ),
);

$ModuleField['field']['panel_5'] = array(
    'setup' => array(
        'css' => 'width:33%;',
        'lablewith' => '35',
    ),
    'sum_amount' => array(
        'type' => 'price',
        'name' => __('Total cost'),
        'listview' => array(
            'order' => '1',
            'with' => '15',
            'css' => 'width:15%;text-align: right;',
            'sort' => '1',
        ),
    ),
);


//============ *** RELATIONSHIP *** =============//
//====== LINE ENTRY =======//
$ModuleField['relationship']['line_entry']['name'] = __('Line entry');

//Line entry Details
$ModuleField['relationship']['line_entry']['block']['products'] = array(
    'title' => __('Details'),
    'type' => 'listview_box',
    'css' => 'width:100%;margin-top:0;',
    'height' => '282',
    'add' => __('Add line'),
    'custom_box_bottom' => '1',
    'custom_box_top' => '1',
    'link' => array('w' => '1', 'cls' => 'products'),
    'reltb' => 'tb_purchaseorder@products', //tb@option
    'delete' => '1',
    'field' => array(
        'code' => array(
            'name' => __('Code'),
            'type' => 'link',
            'module_rel' => 'products',
            'popup_title' => 'Specify Purchase items',
            'popup_key' => 'change',
            'width' => '4',
            'align' => 'center',
            'indata' => '0',
            'edit' => '1',
            'width' => '3',
            'para' => 'get_where_com_po()',
        ),
		'sku' => array(
            'name' => __('SKU'),
            'width' => '3',
        ),
        'products_name' => array(
            'name' => __('Name'),
            'width' => '9',
            'edit' => '1',
            'default' => 'Click for edit',
        ),
        'products_id' => array(
            'name' => __('Products ID'),
            'type' => 'hidden',
        ),
        /* 'option' => array(
          'name' =>  __('Option'),
          'width'=>'5',
          'type' => 'text',
          ), */
        'sizew' => array(
            'name' => __('Size-W'),
            'type' => 'price',
            'width' => '3',
            'edit' => '1',
        ),
        'sizew_unit' => array(
            'name' => __(''),
            'type' => 'select',
            'droplist' => 'product_oum_size',
            'default' => 'in',
            'element_input' => 'combobox_blank="1"',
            'not_custom' => '1',
            'edit' => '1',
            'width' => '2',
        ),
        'sizeh' => array(
            'name' => __('Size-H'),
            'type' => 'price',
            'width' => '3',
            'edit' => '1',
        ),
        'sizeh_unit' => array(
            'name' => __(''),
            'type' => 'select',
            'droplist' => 'product_oum_size',
            'default' => 'in',
            'element_input' => 'combobox_blank="1"',
            'not_custom' => '1',
            'edit' => '1',
            'width' => '2',
        ),
        'area' => array(
            'name' => __('Area'),
            'type' => 'hidden',
        ),
        'sell_by' => array(
            'name' => __('Sold by'),
            'type' => 'select',
            'width' => '3',
            'default' => 'area',
            'element_input' => 'combobox_blank="1"',
            'droplist' => 'product_sell_by',
            'edit' => '1',
        ),
        'sell_price' => array(
            'name' => __('Cost price'),
            'type' => 'price',
            'width' => '4',
            'align' => 'right',
            'edit' => '1',
        ),
        'oum' => array(
            'name' => __(''),
            'type' => 'select_dynamic',
            'droplist' => 'product_oum_area',
            'default' => 'Sq.ft.',
            'element_input' => 'combobox_blank="1"',
            'width' => '3',
            'edit' => '1',
        ),
        'unit_price' => array(
            'name' => __('Unit price'),
            'type' => 'price',
            'width' => '4',
            'default' => '0',
            'align' => 'right',
            'isInt' => '1',
            'numformat'=>4,
        ),
        'quantity' => array(
            'name' => __('Qty'),
            'type' => 'price',
            'align' => 'right',
            'width' => '2',
            'edit' => '1',
            'default' => 0,
            'isInt'=>'1',
            'numformat'=>0,
        ),
        // 'quantity_shipped'=>array(
        //     'name'=> __('Shipped'),
        //     'type' => 'text',
        //     'align' => 'right',
        //     'width' => '3',
        //     'edit' => '0',
        //     'default' => '0',
        // ),
        // 'balance_shipped'=>array(
        //     'name'=> __('Balance'),
        //     'type' => 'text',
        //     'align' => 'right',
        //     'width' => '3',
        //     'edit' => '0',
        //     'default' => '0',
        // ),
        'quantity_received'=>array(
            'name'=> __('Received'),
            'type' => 'text',
            'align' => 'right',
            'width' => '4',
            'default' => '0',
            'edit' => '1',
        ),
        'quantity_returned'=>array(
            'name'=> __('Returned'),
            'type' => 'text',
            'align' => 'right',
            'width' => '4',
            'default' => '0',
            'edit' => '1',
        ),
        'balance_received'=>array(
            'name'=> __('Balance'),
            'type' => 'text',
            'align' => 'right',
            'width' => '4',
            'edit' => '0',
            'default' => '0',
        ),
        'sub_total' => array(
            'name' => __('Sub total'),
            'width' => '4',
            'align' => 'right',
            'type' => 'text',
            'default' => '0',
        ),
        'taxper' => array(
            'name' => __('Tax %'),
            'type' => 'price',
            'width' => '3',
            'align' => 'right',
            'default' => '0',
            'edit' => '1',
            'isInt'=>'1',
            'numformat'=>2,
        ),
        'tax' => array(
            'name' => __('Tax'),
            'type'=>'text',
            'width' => '3',
            'align' => 'right',
            'default' => '0',
        ),
        'amount' => array(
            'name' => __('Amount'),
            'width' => '4',
            'align' => 'right',
            'default' => '0',
        ),
    ),
);


//====== TEXT ENTRY =======//
$ModuleField['relationship']['text_entry']['name'] = __('Text entry');

//Text entry Details
$ModuleField['relationship']['text_entry']['block']['products'] = array(
    'title' => __('Details'),
    'type' => 'listview_box',
    'css' => 'width:100%;margin-top:0;',
    'height' => '264',
    'add' => __('Add line'),
    'custom_box_bottom' => '1',
    'custom_box_top' => '1',
    'link' => array('w' => '1', 'cls' => 'products'),
    'reltb' => 'tb_purchaseorder@products', //tb@option
    'delete' => '1',
    'linecss' => 'h_entry',
    'cellcss' => 'h_entryin',
	'full_height' => '1',
    'field' => array(
        'products_name' => array(
            'name' => __('Name / details'),
            'width' => '33',
            'edit' => '1',
            'type' => 'textarea',
            'default' => 'Click for edit',
        ),
        'products_id' => array(
            'name' => __('Products ID'),
            'type' => 'id',
        ),
        /* 'option' => array(
          'name' 		=>  __('Option'),
          'width'=>'7',
          'type' => 'text',
          ), */
        'sizew' => array(
            'name' => __('Size-W'),
            'type' => 'price',
            'width' => '3',
            'edit' => '1',
            'mod' => 'text',
        ),
        'sizew_unit' => array(
            'name' => __(''),
            'type' => 'select',
            'droplist' => 'product_oum_size',
            'default' => 'inch',
            'not_custom' => '1',
            'edit' => '1',
            'width' => '3',
            'mod' => 'text',
        ),
        'sizeh' => array(
            'name' => __('Size-H'),
            'type' => 'price',
            'width' => '3',
            'edit' => '1',
            'mod' => 'text',
        ),
        'sizeh_unit' => array(
            'name' => __(''),
            'type' => 'select',
            'droplist' => 'product_oum_size',
            'default' => 'inch',
            'not_custom' => '1',
            'edit' => '1',
            'width' => '3',
            'mod' => 'text',
        ),
        'sell_by' => array(
            'name' => __('Sold by'),
            'type' => 'select',
            'width' => '3',
            'droplist' => 'product_sell_by',
            'edit' => '1',
            'mod' => 'text',
        ),
        'sell_price' => array(
            'name' => __('Cost price'),
            'type' => 'price',
            'width' => '5',
            'align' => 'right',
            'edit' => '1',
            'mod' => 'text',
        ),
        'oum' => array(
            'name' => __(''),
            'type' => 'select_dynamic',
            'droplist' => 'product_oum_area',
            'width' => '3',
            'edit' => '1',
            'mod' => 'text',
        ),
        'unit_price' => array(
            'name' => __('Unit price'),
            'type' => 'price',
            'width' => '5',
            'align' => 'right',
            'mod' => 'text',
            'isInt'=>1,
            'numformat'=>4,
        ),
        'quantity' => array(
            'name' => __('Quantity'),
            'type' => 'price',
            'align' => 'right',
            'width' => '3',
            'edit' => '1',
            'default' => 0,
            'mod' => 'text',
            'isInt'=>'1',
            'numformat'=>0,
        ),
        'sub_total' => array(
            'name' => __('Sub total'),
            'width' => '5',
            'align' => 'right',
            'type' => 'text',
            'mod' => 'text',
        ),
        'taxper' => array(
            'name' => __('Tax %'),
            'type' => 'hidden',
            'mod' => 'text',
        ),
        'tax' => array(
            'name' => __('Tax'),
            'indata' => '0',
            'width' => '5',
            'align' => 'right',
            'mod' => 'text',
        ),
        'amount' => array(
            'name' => __('Amount'),
            'width' => '7',
            'align' => 'right',
            'mod' => 'text',
        ),
    ),
);

//====== Shipping received =======//

$ModuleField['relationship']['shipping_received']['name'] = __('Shipping / received');
$ModuleField['relationship']['shipping_received']['block']['shipping'] = array(
    'title' => __('Shipping (items shipped / received / returned) related to this purchase order'),
    'type' => 'listview_box',
    'css' => 'width:100%;margin-top:0;',
    'height' => '150',
    'custom_box_top' => '1',
    'link'      => array('w'=>'1', 'cls'=>'shippings'),
    'reltb' => 'tb_purchaseorder@shipping', //tb@option
    'delete' => '0',
    'field' => array(
        'no' => array(
            'name' => __('Ref no'),
            'align' => 'center',
            'edit' => '0',
            'width' => '3',
        ),
        'type' => array(
            'name' => __('Type'),
            'align'=>'center',
            'width' => '3',
            'type' => 'text',
            'cls'=>'shipping_type'

        ),
        'return' => array(
            'name' => __('Return'),
            'align'=>'center',
            'width' => '4',
            'type'=>'checkbox',
            'edit' => '0',

        ),
        'shipping_date' => array(
            'name' => __('Date'),
            'align'=>'center',
            'width' => '5',
            'edit' => '0',

        ),
        'our_rep' => array(
            'name' => __('Our rep'),
            'width' => '6',
            'edit' => '0',
        ),
        'carrier' => array(
            'name' => __('Carrier'),
            'width' => '8',
            'edit' => '0',
        ),
        'tracking_no' => array(
            'name' => __('Tracking no'),
            'width' => '8',
            'edit' => '0',
        ),
        'heading' => array(
            'name' => __('Heading'),
            'width' => '16',
            'edit' => '0',
        ),
        'status' => array(
            'name' => __('Status'),
            'width' => '4',
            'edit' => '0',
        ),
    )
);


//====== Supplier invoice =======//
$ModuleField['relationship']['supplier_invoice']['name'] = __('Supplier Invoice');
$ModuleField['relationship']['supplier_invoice']['block']['invoice'] = array(
    'title' => __('Supplier invoice received linked to this order'),
    'type' => 'listview_box',
    'css' => 'width:100%;margin-top:0;',
    'height' => '150',
    'add' => __('Create supplier invoice'),
   // 'reltb' => 'tb_purchaseorder@invoice', //tb@option
    'delete' => '1',
    'field' => array(
        'no' => array(
            'name' => __('Supplier invoice no'),
            'align' => 'left',
            'edit' => '1',
            'width' => '10',
        ),
        'invoice_day' => array(
            'name' => __('Date'),
            'width' => '5',
            'type' => 'text',
        ),
        'type' => array(
            'name' => __('Type'),
            'width' => '4',
            'edit' => '1',
        ),
        'status' => array(
            'name' => __('Status'),
            'width' => '4',
            'edit' => '1',
            'type' => 'select',
            'droplist' => 'product_oum_size',
        ),
        'terms' => array(
            'name' => __('Terms'),
            'width' => '3',
            'edit' => '1',
        ),
        'due_1' => array(
            'name' => __('Due'),
            'width' => '4',
            'edit' => '1',
        ),
        'day_left' => array(
            'name' => __('Day left'),
            'width' => '4',
            'edit' => '1',
        ),
        'due_2' => array(
            'name' => __('Due'),
            'width' => '2',
            'edit' => '1',
            'align' => 'center',
            'type' => 'checkbox'
        ),
        'note' => array(
            'name' => __('Notes'),
            'width' => '10',
            'edit' => '1',
        ),
        'nc' => array(
            'name' => __('N/C'),
            'width' => '4',
            'edit' => '1',
        ),
        'amount' => array(
            'name' => __('Amount'),
            'width' => '4',
            'edit' => '1',
            'align' => 'right'
        ),
        'tax' => array(
            'name' => __('Tax %'),
            'width' => '3',
            'edit' => '1',
            'align' => 'center'
        ),
        'total_inc_tax' => array(
            'name' => __('Total inc tax'),
            'width' => '6',
            'edit' => '1',
            'align' => 'right'
        ),
        'approved' => array(
            'name' => __('Approved'),
            'width' => '4',
            'edit' => '1',
            'type' => 'checkbox'
        ),
        'approved_by' => array(
            'name' => __('Approved by'),
            'width' => '6',
            'edit' => '1',
        ),
        'paid' => array(
            'name' => __('paid'),
            'width' => '4',
            'edit' => '1',
            'align' => 'right'
        ),
    )
);

//====== DOCUMENTS =======//
$ModuleField['relationship']['tasks']['name'] = __('Tasks');

//====== DOCUMENTS =======//
$ModuleField['relationship']['documents']['name'] = __('Documents');

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
$PurchaseorderField = $ModuleField;
