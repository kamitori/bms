<?php

$ModuleField = array();
$ModuleField = array(
    'module_name' => __('Product'),
    'module_label' => __('Product'),
    'colection' => 'tb_product',
    'title_field' => array('code', 'name', 'product_type', 'company'),
);


//============= *** FIELDS *** =============//
// Panel 1
$ModuleField['field']['panel_1'] = array(
    'setup' => array(
        'css' => 'width:100%;',
        'lablewith' => '30',
        'blockcss' => 'width:30%;float:left;',
    ),
    'code' => array(
        'name' => __('Code'),
        'type' => 'text',
        'moreclass' => 'fixbor',
        'css' => 'padding-left:3.5%;width: 93.5%;',
        'lock' => '1',
        'listview' => array(
            'order' => '1',
            'align' => 'center',
            'css' => 'width:3%;',
            'sort' => '1',
            'popup_width' => '7%',
        ),
    ),
	'sku' => array(
        'name' => __('SKU'),
        'type' => 'text',
        'css' => 'padding-left:3.5%;width: 93.5%;',
		'listview' => array(
            'order' => '2',
            'align' => 'left',
            'css' => 'width:5%;',
            'sort' => '1',
            'popup_width' => '7%',
        ),
    ),
    'serial' => array(
        'name' => __('Serial'),
        'type' => 'hidden',
    ),
    'name' => array(
        'name' => __('Name'),
        'type' => 'text',
        'css' => 'padding-left:3.5%;width: 93.5%;',
        'listview' => array(
            'order' => '3',
            'align' => 'left',
            'css' => 'width:22%;',
            'sort' => '1',
            'popup_width' => '30%',
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
        'type' => 'text',
        'css' => 'padding-left:3.5%;width: 93.5%;',
    ),
    'group_type' => array(
        'name' => __('Parrent type'),
        'type' => 'hidden',
        'droplist' => 'product_group_type',
		'element_input' => 'combobox_blank="1"',
        'default' => 'SELL',
        'field_class' => 'fieldclass',
		'lock' => '1',
    ),

	'product_type' => array(
        'name' => __('Type'),
        'type' => 'select',
        'droplist' => 'product_type',
        'default' => 'Product',
        'field_class' => 'fieldclass',
        'element_input' => ' readonly="readonly" combobox_blank="1"',
        'listview' => array(
            'order' => '4',
            'align' => 'left',
            'css' => 'width:6%;',
            'popup_width' => '15%',
            'sort' => '1',
        ),
    ),
	'company_name'	=>array(
		'name' 		=>  __('Current supplier'),
		'type' 		=> 'relationship',
		'cls'		=> 'companies',
		'id'		=> 'company_id',
		'css' => 'padding-left:3.5%;width: 93.5%;',
		'lock'		=> '0',
		'para' 		=> ",'?is_supplier=1'",
        'list_syncname' => 'company_name',
        'listview' => array(
            'order' => '5',
            'align' => 'left',
            'css' => 'width:12%;',
            'popup_width' => '15%',
            'sort' => '1',
            ),
	),
	'company_id'	=>array(
		'type' 		=> 'id',
		'element_input' => ' class="jthidden"',
	),
    'prefer_customer'  =>array(
        'name'      =>  __('Prefer Customer'),
        'type'      => 'relationship',
        'cls'       => 'companies',
        'id'        => 'prefer_customer_id',
        'css' => 'padding-left:3.5%;width: 93.5%;',
        'lock'      => '0',
        'list_syncname' => 'prefer_customer',
    ),
    'prefer_customer_id'    =>array(
        'type'      => 'id',
        'element_input' => ' class="jthidden"',
    ),
     'facebook_app' => array(
        'name' => __('Facebook app'),
        'type' => 'checkbox',
        'label' => '&nbsp;',
        'css' => 'width:96%;margin-left:0%;border:none;',
        'checkcss' => 'margin-left:5%;',
        'default' => 0,
        'width' => '38%;border:none;',
        'moreclass' => 'fixbor2',
    )
);


// Panel 2
$ModuleField['field']['panel_2'] = array(
    'setup' => array(
        'css' => 'width:32%;',
        'lablewith' => '38', //%
        'blockcss' => 'width:69%;float:right;',
    ),
    'color' => array(
        'name' => __('Color'),
        'type' => 'select',
        'droplist' => 'product_color',
        'default' => '',
        'moreclass' => 'fixbor',
        'width' => '57%;',
        'not_custom' => '1',
        'css' => 'padding-left:3%;width: 101%;',
    ),
    'thickness' => array(
        'name' => __('Thickness'),
        'type' => 'price',
        'width' => '25%',
        'css' => 'width:95%;padding:0 5.5%;text-align:right;',
        'after_field' => 'thickness_unit',
    ),
    'thickness_unit' => array(
        'name' => __('Unit'),
        'type' => 'select',
        'droplist' => 'product_oum_size',
        'other_type' => 'after_other',
        'classselect' => 'jt_after_field',
        'default' => 'in',
        'not_custom' => '1',
        'width' => '32%;" id="field_after_type" alt="',
    ),
    'sizew' => array(
        'name' => __('Size-W'),
        'type' => 'price',
        'width' => '25%',
        'css' => 'width:95%;padding:0 5.5%;text-align:right;',
        'after_field' => 'sizew_unit',
    ),
    'sizew_unit' => array(
        'name' => __('Unit'),
        'type' => 'select',
        'droplist' => 'product_oum_size',
        'other_type' => 'after_other',
        'classselect' => 'jt_after_field',
        'default' => 'in',
        'not_custom' => '1',
        'width' => '32%;" id="field_after_type" alt="',
    ),
    'sizeh' => array(
        'name' => __('Size-H'),
        'type' => 'price',
        'width' => '25%',
        'css' => 'width:95%;padding:0 5.5%;text-align:right;',
        'after_field' => 'sizeh_unit',
    ),
    'sizeh_unit' => array(
        'name' => __('Unit'),
        'type' => 'select',
        'droplist' => 'product_oum_size',
        'other_type' => 'after_other',
        'classselect' => 'jt_after_field',
        'default' => 'in',
        'not_custom' => '1',
        'width' => '32%;" id="field_after_type" alt="',
    ),
    'is_custom_size' => array(
        'name' => __('Custom-Size'),
        'type' => 'checkbox',
        'label' => '&nbsp;not allowed',
        'css' => 'width:96%;margin-left:0%;border:none;',
        'checkcss' => 'margin-left:5%;',
        'default' => 0,
        'width' => '38%;border:none;',
    ),
	'none11'	=>array(
			'type' 		=> 'not_in_data',
	),
    'none02'    =>array(
            'type'      => 'not_in_data',
    ),
     'none03'    =>array(
            'type'      => 'not_in_data',
     ),
     'none04'    =>array(
            'type'      => 'not_in_data',
    ),
    'none'    =>array(
            'type'      => 'not_in_data',
    ),
	'none12'	=>array(
			'type' 		=> 'not_in_data',
			'moreclass' => 'fixbor2',
	),
);


// Panel 3
$ModuleField['field']['panel_3'] = array(
    'setup' => array(
        'css' => 'width:38%;',
        'lablewith' => '32',
    ),
    'markup' => array(
        'type' => 'id',
        'element_input' => ' class="jthidden"',
    ),
    'profit' => array(
        'type' => 'id',
        'element_input' => ' class="jthidden"',
    ),
    'sell_by' => array(
        'name' => __('Sold by'),
        'type' => 'select',
        'droplist' => 'product_sell_by',
        'default' => 'unit',
        'not_custom' => '1',
        'element_input' => 'combobox_blank="1"',
        'listview' => array(
            'order' => '8',
            'align' => 'left',
            'css' => 'width:3%;',
            'popup_width' => '7%',
        ),
    ),

    'sell_price' => array(
        'name' => __('Sell price<span id="sell_price_category"></span>'),
        'type' => 'price',
        'css' => 'padding-right:4%; width:97%',
        'moreclass' => '',
        'listview' => array(
            'order' => '10',
            'align' => 'right',
            'css' => 'text-align:right; margin-right:1%;width:5%;',
            'popup_width' => '8%',
			'sort' => '1',
        ),
    ),
    'discount' => array(
        'name' => __('Discount'),
        'type' => 'hidden',
        'css' => 'padding-right:4%; width:97%',
        'numformat' => 3,
    ),
    'oum' => array(
        'name' => __('<span title="Unit of measurement">UOM</span>'),
        'type' => 'select',
        'droplist' => 'product_oum_unit',
        'default' => 'unit',
        'width' => '64%" id="field_oum" alt="',
        'element_input' => 'combobox_blank="1"',
		'css' => 'text-align:left;padding-right:3%; width:97%;',
        'listview' => array(
            'order' => '9',
            'align' => 'left',
            'css' => 'width:3%;',
            'popup_width' => '5%',
        ),
    ),
	'cost_price' => array(
		'name' => __('Cost price'),
        'type' => 'hidden',
        'css' => 'padding-right:4%; width:97%',
    ),
	'unit_price' => array(
        'name' => __('Unit price'),
        'type' => 'hidden',
        'css' => 'padding-left:3%!important; width:97%;text-align:left;',
		'numformat' => 3,
		'lock'=>'1',
    ),
	'oum_depend' => array(
        'name' => __('<span title="UOM for Unit price - depend on parent product">UOM</span>'),
        'type' => 'hidden',
        'droplist' => 'product_oum_area',
        'default' => '',
        'width' => '64%" id="field_oum_2" alt="',
        'element_input' => 'combobox_blank="1"',
    ),
    'gst_tax' => array(
        'name' => __('GST tax %'),
        'type' => 'select',
        'droplist' => 'product_gst_tax',
        'not_custom' => '1',
    ),
    'pst_tax' => array(
        'name' => __('PST tax %'),
        'type' => 'select',
        'droplist' => 'product_pst_tax',
        'not_custom' => '1',
    ),
	'combo_sales'	=>array(
		'name' => __('Combo sales %'),
        'type' => 'price',
        'css' => 'padding-right:4%; width:97%',
	),
    'combo_sales'   =>array(
        'name' => __('Combo sales %'),
        'type' => 'price',
        'css' => 'padding-right:4%; width:97%',
    ),
    'use_group_order' => array(
        'name' => __('Order by group'),
        'type' => 'checkbox',
        'label' => '&nbsp;',
        'css' => 'width:96%;margin-left:0%;border:none;',
        'checkcss' => 'margin-left:5%;',
        'default' => 0,
        'width' => '38%;border:none;',
    ),
    'off_website' => array(
        'name' => __('Off in website'),
        'type' => 'checkbox',
        'label' => '&nbsp;',
        'css' => 'width:96%;margin-left:0%;border:none;',
        'checkcss' => 'margin-left:5%;',
        'default' => 0,
        'width' => '38%;border:none;',
        'moreclass' => 'fixbor3',
    )
);


// Panel 4
$ModuleField['field']['panel_4'] = array(
    'setup' => array(
        'css' => 'width:30%;',
        'lablewith' => '38',
    ),
	'status' => array(
        'name' => __('Status'),
        'type' => 'select',
        'droplist' => 'product_statuses',
        'width' => '57%',
        'default' => '1',
        'element_input' => ' readonly="readonly" combobox_blank="1"',
    ),
    'category' => array(
        'name' => __('Category'),
        'type' => 'select',
        'droplist' => 'product_category',
        'width' => '57%',
        'not_custom' => '1',
        'listview' => array(
            'order' => '6',
            'align' => 'left',
            'css' => 'width:5%;',
            'popup_width' => '15%',
            'sort' => '1',
        ),
    ),
    'product_base' => array(
        'name' => __('Product Base'),
        'type' => 'select',
        'droplist' => 'product_base',
        'width' => '57%',
        'not_custom' => '1',
    ),
    'special_order' => array(
        'name' => __('Special Order'),
        'type' => 'select',
        'droplist' => 'product_yesno',
        'width' => '57%',
		'listview' => array(
            'order' => '12',
            'align' => 'left',
            'css' => 'width:7%;',
			'sort' => '1',
        ),
    ),
    'maximum_option' => array(
        'name' => __('Maximum Option'),
        'type' => 'text',
        // 'css' => 'padding-left:3.5%;width: 93.5%;',
    ),    
    'assemply_item' => array(
        'name' => __('QQ item'),
        'type' => 'checkbox',
        'label' => '&nbsp;',
        'css' => 'width:96%;margin-left:0%;border:none;',
        'checkcss' => 'margin-left:5%;',
        'default' => 0,
        'width' => '38%;border:none;',
    ),
	'approved' => array(
        'name' => __('Approved'),
        'type' => 'checkbox',
        'label' => '&nbsp;',
        'css' => 'width:96%;margin-left:0%;border:none;',
        'checkcss' => 'margin-left:5%;',
        'default' => 0,
        'width' => '38%;border:none;',
		'listview' => array(
            'order' => '11',
            'align' => 'left',
            'css' => 'width:6%;',
			'sort' => '1',
        ),
    ),
    'is_rfq' => array(
        'name' => __('RFQ'),
        'type' => 'checkbox',
        'label' => '&nbsp;',
        'css' => 'width:96%;margin-left:0%;border:none;',
        'checkcss' => 'margin-left:5%;',
        'default' => 0,
        'width' => '38%;border:none;',
    ),
    'wholesale' => array(
        'name' => __('Whole Sale'),
        'type' => 'checkbox',
        'label' => '&nbsp;',
        'css' => 'width:96%;margin-left:0%;border:none;',
        'checkcss' => 'margin-left:5%;',
        'default' => 0,
        'width' => '38%;border:none;',
    ),
     'none02'    =>array(
            'type'      => 'not_in_data',
    ),
	'parent_product_name'	=>array(
			'name' 		=>  __('Back to product'),
			'type' 		=> 'hidden',
			'cls'		=> 'products',
			'id'		=> 'parent_product_id',
			'not_custom'=> '1',
			'lock'		=> '1',
			),
	'parent_product_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'parent_product_code'	=>array(
			'name' 		=> __(':Code'),
			'type' 		=> 'hidden',
            'moreclass' => 'fixbor3',
			),
    'madeup' => array(
        'type' => 'fieldsave',
        'rel_name' => 'madeup',
    ),
);


// Custom field for listview
$ModuleField['field']['panel_5'] = array(
    'setup' => array(),
    'on_po' => array(
        'name' => __("On PO's"),
        'type' => 'display',
        // 'listview' => array(
        //     'order' => '6',
        //     'align' => 'left',
        //     'css' => 'width:4%;display',
        // ),
    ),
    'on_so' => array(
        'name' => __("On SO's"),
        'type' => 'display',
        // 'listview' => array(
        //     'order' => '7',
        //     'align' => 'left',
        //     'css' => 'width:4%;',
        // ),
    ),
    'qty_in_stock' => array(
        'name' => __("In stock"),
        'type' => 'hidden',
        'listview' => array(
            'order' => '7',
            'align' => 'left',
            'css' => 'width:5%;',
			'align'=>'right',
			'sort'=>'1',
        ),
    ),
);


//============ *** RELATIONSHIP *** =============//
//====== GENERAL =======//
$ModuleField['relationship']['general']['name'] = __('General');

//Image
$ModuleField['relationship']['general']['block']['image'] = array(
    'title' => __('Image'),
    'css' => 'width:25%;margin-top:0;float:left;',
    'upload' => 'Import image',
    'cls' => 'company',
    'custom_box_top' => '1',
    'type' => 'upload_box',
    'field' => array(
        'files' => array(
            'colection' => 'tb_document',
            'name' => __('Click the "+" button to insert an image'),
            'css' => 'height:312px;padding: 5px 1%;',
        ),
    ),
);

//Options for this item
$ModuleField['relationship']['general']['block']['productoptions'] = array(
    'title' => __('Options for this item '),
    'moreclass' => 'full_width',
    'height' => '310',
	'css' => 'width:74%;margin-top:0;margin-left:1%;float:left;',
    'add' => 'Add option',
	'custom_box_top' => '1',
    'foottext' => array(
        'label' => __(''),
    ),
    //'total' => 'Total cost',
    //'total_css' => 'margin-right:3%;',
    'type' => 'listview_box',
    'link' => array('w' => '1', 'cls' => 'products','field'=>'product_id'),
    'reltb' => 'tb_product@options', //tb@option
    'delete' => '2',
    'field' => array(
        'code' => array(
            'name' => __('Code'),
            'type' => 'link_code',
            'module_rel' => 'products',
            'popup_title' => 'Change Material',
            'popup_key' => 'change',
            'width' => '3',
            'align' => 'center',
            'indata' => '0',
            'edit' => '1',
        ),
		'sku' => array(
            'name' => __('SKU'),
            'width' => '5',
			'type' => 'text',
        ),
        'product_name' => array(
            'name' => __('Name'),
            'width' => '11',
        ),
		'product_id' => array(
            'name' => __('ID'),
			'type'=>'id',
		),
		'group_type' => array(
            'name' => __('Type'),
			'width' => '4',
            'type'=>'select',
			'droplist' => 'product_group',
			'edit' => '1',
			'not_custom'=>'1',
        ),
		'option_group' => array(
            'name' => __('Group'),
			'width' => '6',
            'type'=>'select',
			'droplist' => 'product_group',
			'edit' => '1',
        ),
        'group_order' => array(
            'name' => __('G.O.'),
            'title' => 'Group Order',
            'width' => '3',
            'type'=>'text',
            'edit' => '1',
        ),
		'require' => array(
            'name' => __('Req'),
			'width' => '3',
            'type'=>'checkbox',
			'edit' => '1',
        ),
		'same_parent' => array(
            'name' => __('<span title="Same info as parent product">S.P.</span>'),
			'width' => '3',
            'type'=>'checkbox',
			'edit' => '1',
        ),
        'default' => array(
            'name' => __('Default'),
            'width' => '4',
            'type'=>'checkbox',
            'edit' => '1',
            'default' => 0
        ),
		'product_type' => array(
            'name' => __('Type'),
			'type'=>'hidden',
			'width' => '8',
			'droplist' => 'product_type',
			'edit' => '1',
		),
        'level' => array(
            'name' => __('Level'),
            'width' => '4',
            'type'=>'select',
            'droplist' => 'product_option_level',
            'edit' => '0',
            'not_custom'=>'1',
        ),
        'category' => array(
            'name' => __('Category'),
            'width' => '0',
            'type' => 'hidden',
        	'droplist' => 'product_category',
        ),
		'company_id' => array(
            'type' => 'hidden',
			'width' => '0',
        ),
        'company_name' => array(
            'name' => __('Supplier'),
            'type' => 'hidden',
            'align' => 'left',
            'width' => '10',
			'title' => 'Specify Current supplier',
            'para' => ",'?is_supplier=1'",
			'indata' => '0',
        ),
        'unit_price' => array(
            'name' => __('Unit cost'),
            'width' => '5',
            'type' => 'price',
            'align' => 'right',
			'numformat'=>3,
			'edit' => '1',
        ),
		'oum' => array(
            'name' => __('UOM'),
            'width' => '4',
            'type' 		=> 'text',
			'droplist'	=> 'product_oum_area',
			'align' => 'center',
        ),
		'discount' => array(
            'name' => __('%Discount'),
            'width' => '6',
            'type' => 'hidden',
            'align' => 'right',
            'edit' => '1',
            'default' => '0',
        ),
        'adjustment' => array(
            'name' => __('Adjustment'),
            'width' => '6',
            'type' => 'price',
            'align' => 'right',
            'edit' => '1',
            'default' => '0',
        ),
		'markup' => array(
            'name' => __('%Markup'),
            'width' => '5',
            'type' => 'hidden',
            'align' => 'right',
            'edit' => '1',
            'default' => '0',
        ),
		'margin' => array(
            'name' => __('%Margin'),
            'width' => '5',
            'type' => 'hidden',
            'align' => 'right',
            'edit' => '1',
            'default' => '0',
        ),
        'finish' => array(
            'name' => __('Finish'),
            'width' => '4',
            'type'=>'select_dynamic',
            'edit' => '0',
            'not_custom'=>'1',
        ),
        'quantity' => array(
            'name' => __('Qty'),
            'width' => '3',
            'type' => 'text',
            'align' => 'right',
            'edit' => '1',
            'default' => '1',
        ),
        'sub_total' => array(
            'name' => __('Sub total'),
            'width' => '6',
            'align' => 'right',
            'type' => 'price',
        ),
        'hidden' => array(
            'name' => __('Hidden'),
            'title' => __('Hidden from PDFs'),
            'edit' => 1,
            'width' => '2',
            'align' => 'center',
            'type' => 'checkbox',
        )
    ),
);


//Stock tracking
$ModuleField['relationship']['general']['block']['stocktracking'] = array(
    'title' => 'Stock tracking',
    'css' => 'width:25%;margin-top:1%;margin-left:0;float:left;',
    'height' => '150',
    'reltb' => 'tb_product@stocktracking',
    'type' => 'editview_box',
    'custom_box_top' => '1',
    'field' => array(
        'qty_in_stock' => array(
            'name' => __('Qty in stock (sell)'),
            'type' => 'price',
            'css'=>'text-align:left;',
            'lock'=>'1',
            'noformat'=>'',
        ),
        'qty_on_so' => array(
            'name' => __('Qty on sales orders'),
            'type' => 'price',
            'css'=>'text-align:left;',
            'noformat'=>'',
            'lock'=>'1',
        ),
        'qty_balance' => array(
            'name' => __('Qty balance available'),
            'type' => 'price',
            'css'=>'text-align:left;',
            'noformat'=>'',
            'lock'=>'1',
        ),
        'none' => array(
            'name' => __('Tracking units'),
            'type' => 'header',
        ),
        'tracking_units_individually' => array(
            'name' => __('Tracking units individually'),
            'type' => 'checkbox',
            'label'=> ' using serial numbers &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
            'default'   => 0,
        ),
        'generate_serials' => array(
            'name'  => __('Generate serials'),
            'type'      => 'select',
            'droplist'  => 'generate_serials_droplist',
            'default'   => '',
            'not_custom'=>'1',
            'edit'      =>'1',
        ),
        'serial_length' => array(
            'name'  => __('Serial length'),
            'type' => 'text',
            'type' => 'price',
            'css'=>'text-align:left;',
            'element_input'=>'maxlength="2"',
            'noformat'=>'',
        ),
        'next_serial_ex' => array(
            'name'  => __('Next serial example'),
            'type' => 'text',
            'element_input'=>'maxlength="20"',
        ),
        'check_stock_stracking'=>array(
            'type' => 'hidden',
        ),
        'none2' => array(
            'name' => __('Manufacturer details'),
            'type' => 'header',
        ),
        'manufacturer' => array(
            'name'  => __('Manufacturer'),
            'type' => 'select',
            'droplist' => 'manufacturer_droplist',
            'default'   => '',
        ),
        'manufacturer_pn' => array(
            'name'  => __('Manufacturer P/N'),
            'type' => 'text',
        ),
        'model_no' => array(
            'name'  => __('Model no'),
            'type' => 'text',
        ),

    ),
);


//Pricing Method
$ModuleField['relationship']['general']['block']['pricing_method'] = array(
    'title' => __('Pricing Method'),
    'css' => 'width:20%;margin-top:1%;margin-left:1%;float:left;',
    'height' => '150',
    'type' => 'editview_box',
    'field' => array(
        'pricing_method_name' => array(
            'type' => 'text',
            'name' => __('Pricing method note'),
            'css' => 'padding-left:3%!important; width:96%;!important',
            'default' => '',
        ),
        'pricing_method_id' => array(
            'type' => 'hidden',
        ),
        'pricing_rule_unit' => array(
            'type' => 'text',
            'name' => __('Unit Price'),
            'css' => 'padding-left:3%!important; width:96%;!important',
            'default' => '',
        ),
        'rule_name' => array(//cau hinh chuan cho rel field
            'type' => 'relationship',
            'name' => __('Pricing rule'),
            'cls' => 'rules',
            'relmodule' => 'rule',
            'relid' => 'rule_id',
        ),
        'rule_id' => array(
            'type' => 'hidden',
        ),
        'rule_formula' => array(
            'type' => 'display',
            'name' => __('Pricing rule formula'),
        ),
        'rule_description' => array(
            'type' => 'display',
            'name' => __('Pricing rule description'),
        ),
        'remove_rules' => array(
            'type' => 'button',
            'value' => __(' Remove '),
            'name' => __('Remove Rules'),
            'css' => 'margin:2px; padding:2px; cursor:pointer;',
        ),
    ),
);

//Same Category
$ModuleField['relationship']['general']['block']['same_category'] = array(
    'title' => __('Same category products'),
    'css' => 'width:74%;margin-top:1%;margin-left:1%;float:left;',
    'height' => '312',
    'type' => 'listview_box',
    'link' => array('w' => '2', 'cls' => 'products'),
    'field' => array(
        'code' => array(
            'name' => __('Code'),
            'width' => '7',
            'align' => 'center',
        ),
        'name' => array(
            'name' => __('Name'),
            'width' => '40',
        ),
        'product_type' => array(
            'name' => __('Type'),
            'width' => '20',
        ),
        'sell_price' => array(
            'name' => __('Sell Price'),
            'type' => 'price',
            'width' => '20',
            'align' => 'right',
        ),
    ),
);



//Avaliable Option
$ModuleField['relationship']['general']['block']['avaliable_option'] = array(
    'title' => __('Avaliable Options &nbsp;( No change in Pricing )'),
    'css' => 'width:100%;',
    'height' => '310',
    'type' => 'listview_box',
    //'link'	=> array('w'=>'6', 'cls'=>'productoptions'),
    'field' => array(
        'none' => array(
            'width' => '3',
        ),
        'productoptions_name' => array(
            'name' => __('Option type'),
            'width' => '40',
            'align' => 'left',
        ),
        'productoptions_id' => array(
            'type' => 'hidden',
        ),
        'drop_list' => array(
            'name' => __('Default value'),
            'width' => '50',
            'type' => 'select_value',
            'edit' => '1',
        ),
    ),
);



//====== COSTING =======//
$ModuleField['relationship']['costings']['name'] = __('Costings');

//Made up
$ModuleField['relationship']['costings']['block']['madeup'] = array(
    'title' => __('This item is made up of the following costs / items'),
    'moreclass' => 'full_width',
    'height' => '150',
	'css' => 'margin-bottom:1%',
    'add' => 'Add cost / item',
    'foottext' => array(
        'label' => __('Note: Stock items automatically get deducted from stock when the  main item is used on a job assembly.'),
    ),
    'total' => 'Total cost',
    'total_css' => 'margin-right:3%;',
    'type' => 'listview_box',
    'link' => array('w' => '1', 'cls' => 'products','field'=>'product_id'),
    'reltb' => 'tb_product@madeup', //tb@option
    'delete' => '2',
    'field' => array(
        'code' => array(
            'name' => __('Code'),
            'type' => 'link_code',
            'module_rel' => 'products',
            'popup_title' => 'Change Material',
            'popup_key' => 'change',
            'width' => '3',
            'align' => 'center',
            'indata' => '0',
            'edit' => '1',
        ),
		'sku' => array(
            'name' => __('SKU'),
            'width' => '6',
			'type' => 'text',
        ),
        'product_name' => array(
            'name' => __('Name'),
            'width' => '20',
        ),
		'product_id' => array(
            'name' => __('ID'),
			'type'=>'id',
		),
		'product_type' => array(
            'name' => __('Type'),
			'type'=>'select',
			'width' => '8',
			'droplist' => 'product_type',
		),

        'category' => array(
            'name' => __('Category'),
            'width' => '0',
            'type' => 'hidden',
        	'droplist' => 'product_category',
        ),
		'company_id' => array(
            'type' => 'id',
			'width' => '0',
        ),
        'company_name' => array(
            'name' => __('Supplier'),
            'type' => 'text',
            'align' => 'left',
            'width' => '10',
			'title' => 'Specify Current supplier',
            'para' => ",'?is_supplier=1'",
			'indata' => '0',
        ),
        'unit_price' => array(
            'name' => __('Unit cost'),
            'width' => '5',
            'type' => 'price',
            'align' => 'right',
			'numformat'=>3,
        ),
		'oum' => array(
            'name' => __('UOM'),
            'width' => '3',
            'type' 		=> 'select',
			'droplist'	=> 'product_oum_area',
			'align' => 'center',
        ),
		'markup' => array(
            'name' => __('%Markup'),
            'width' => '5',
            'type' => 'price',
            'align' => 'right',
            'edit' => '1',
            'default' => '0',
        ),
		'margin' => array(
            'name' => __('%Margin'),
            'width' => '5',
            'type' => 'price',
            'align' => 'right',
            'edit' => '1',
            'default' => '0',
        ),
        'quantity' => array(
            'name' => __('Quantity'),
            'width' => '5',
            'type' => 'text',
            'align' => 'right',
            'edit' => '1',
            'default' => '1',
        ),
        'sub_total' => array(
            'name' => __('Sub total'),
            'width' => '7',
            'align' => 'right',
            'type' => 'price',
            'indata' => '0',
        ),
        // 'view_in_detail' => array(
        //    'name' => __('<span title="List this costing in Text entry">Detail List</span>'),
        //    'width' => '4',
        //    'align' => 'center',
        //    'type' => 'checkbox',
        //    'edit'  => '1',
        // ),
    ),
);


//Use on
$ModuleField['relationship']['costings']['block']['useon'] = array(
    'title' => __('This item is used on the following assemblies (finished products) and sub assemblies'),
    'moreclass' => 'full_width',
    'css' => 'margin-top:0',
    'height' => '150',
    'foottext' => array(
        'label' => __('Note: Stock items automatically get deducted from stock when the  main item is used on a job assembly.'),
    ),
    'link' => array('w' => '1', 'cls' => 'products'),
    'type' => 'listview_box',
    'field' => array(
        'code' => array(
            'name' => __('Code'),
            'width' => '4',
            'align' => 'center',
        ),
        'name' => array(
            'name' => __('Name'),
            'width' => '46',
        ),
        'product_type' => array(
            'name' => __('Type'),
            'width' => '15',
        ),
        'category' => array(
            'name' => __('Category'),
            'width' => '15',
        ),
        'quantity' => array(
            'name' => __('Quantity'),
            'width' => '12',
            'align' => 'right',
        ),
    ),
);

//Pricing summary
$ModuleField['relationship']['costings']['block']['pricingsummary'] = array(
    'title' => __('Pricing summary'),
    'css' => 'width:100%;float:right',
    'height' => '363',
    'moreclass' => '',
    'type' => 'pricing_summary',
    'field' => array(
        'cost_price' => array(
            'name' => __('Cost'),
        ),
        'sell_price' => array(
            'name' => __('Sell price'),
        ),
        'profit' => array(
            'name' => __('Profit'),
            'moreclass' => 'bor_active',
        ),
        'markup' => array(
            'name' => __('Markup'),
        ),
        'margin' => array(
            'name' => __('Margin'),
        ),
    ),
);



//====== PRICING =======//
$ModuleField['relationship']['pricing']['name'] = __('Pricing');

//Sell prices for this item
$ModuleField['relationship']['pricing']['block']['sellprices'] = array(
    'title' => __('Sell prices for this item'),
    'css' => 'width:25%;',
    'height' => '353',
    'moreclass' => '',
    'add' => 'Create price',
    'footlink' => array(
        'label' => __('Click to edit price breaks on right'),
    ),
    'reltb' => 'tb_product@sellprices', //tb@option
    'delete' => '6',
    'type' => 'listview_box',
    'field' => array(
        'none'=> array(
            'name'  => '',
            'width' => '6'
            ),
        'sell_category' => array(
            'name' => __('&nbsp;&nbsp;Category'),
            'width' => '35',
            'type' => 'select',
            'droplist' => 'products_sell_category',
            'default' => 'Trade',
            'not_custom' => '1',
			'element_input' => 'combobox_blank="1"',
            'edit' => '1',
        ),
        'sell_unit_price' => array(
            'name' => __('Unit price&nbsp;&nbsp;'),
            'width' => '25',
            'align' => 'right',
            'type' => 'price',
            'edit' => '1',
        ),
        'sell_default' => array(
            'name' => __('Default'),
            'width' => '20',
            'align' => 'center',
            'type' => 'checkbox',
            'default' => '0',
        ),
		'cate_key' => array(
            'type' => 'hidden',
        ),
		'category_text' => array(
            'type' => 'hidden',
        ),
    ),
);


//Price breaks
$ModuleField['relationship']['pricing']['block']['pricebreaks'] = array(
    'title' => __('Price breaks <span id="break_sell_price"></span>'),
    'css' => 'width:25%;margin-left:0;',
    'cellcss' => 'padding-left:2%!important;padding-right:2%!important;',
    'height' => '353',
    'moreclass' => '',
    'add' => 'Add level',
    'delete' => '6',
    'reltb' => 'tb_product@pricebreaks', //tb@option
    'foottext' => array(
        'label' => __('Leave \'Range to\' blank on last range if not limit'),
    ),
    'type' => 'listview_box',
    'field' => array(
        'range_from' => array(
            'name' => __('Range from '),
            'width' => '22',
			'type' => 'price',
			'align' => 'right',
            'css' => 'font-weight:bold;',
            'align' => 'right',
            'edit' => '1',
			'numformat'=>0,
        ),
        'range_to' => array(
            'name' => __('Range to'),
            'width' => '22',
            'type' => 'price',
			'align' => 'right',
           	'css' => 'font-weight:bold;',
            'default' => '',
            'not_custom' => '1',
            'edit' => '1',
			'numformat'=>0,
        ),
        'unit_price' => array(
            'name' => __('Unit price'),
            'width' => '35',
            'align' => 'right',
            'type' => 'price',
            'edit' => '1',
        ),
		'sell_category' => array(
            'name' => __('Sell prices'),
            'width' => '0',
            'type' => 'hidden',
        ),

    ),
);


//Other pricing detail
$ModuleField['relationship']['pricing']['block']['otherpricing'] = array(
    'title' => __('Other pricing detail'),
    'css' => 'width:31%;margin-left:1%;',
    'height' => '353',
    'type' => 'double_box',
    'field' => array(
        'update_price_by' => array(
            'name' => __('Pricing last updated by'),
            'width' => '45',
            'type' => 'relationship',
            'cls' => 'contacts',
            'relmodule' => 'contacts',
            'relid' => 'update_price_by_id',
        ),
        'update_price_by_id' => array(
            'type' => 'hidden',
        ),
        'update_price_date' => array(
            'name' => __('Date updated'),
            'type' => 'text',
            'width' => '45',
        ),
        'pricing_method_list' => array(
            'name' => __('Pricing method'),
            'type' => 'text',
            'width' => '45',
        ),
        'price_breaks_type' => array(
            'name' => __('Price breaks type'),
            'type' => 'text',
            'width' => '45',
        ),
        'pricing_bleed_list' => array(
            'name' => __('Pricing bleed'),
            'type' => 'text',
            'width' => '45',
        ),
        'price_note' => array(
            'name' => __('Pricing notes'),
            'type' => 'subbox',
            'width' => '45',
            'note_text' => __('Note: Customers can be a default price category on the "Pricing" tab on the customer screen.If none is specified for the customer then the default category here will be used. Specific prising, including price break, can also be set per customer on their "Pricing" tab.'),
        ),
    ),
);

//Pricing summary
$ModuleField['relationship']['pricing']['block']['pricingsummary'] = array(
    'title' => __('Pricing summary'),
    'css' => 'width:16%;float:right',
    'height' => '353',
    'moreclass' => '',
    'type' => 'pricing_summary',
    'field' => array(
        'cost_price' => array(
            'name' => __('Cost'),
        ),
        'sell_price' => array(
            'name' => __('Sell price'),
        ),
        'profit' => array(
            'name' => __('Profit'),
            'moreclass' => 'bor_active',
        ),
        'markup' => array(
            'name' => __('Markup'),
        ),
        'margin' => array(
            'name' => __('Margin'),
        ),
    ),
);



//====== STOCK =======//
$ModuleField['relationship']['stock']['name'] = __('Stock');

$ModuleField['relationship']['stock']['block']['stock_summary'] = array(

);
//Locations for this item
$ModuleField['relationship']['stock']['block']['locations'] = array(
    'title' => 'Locations for this item',
    'css' => 'margin-left:1%; width: 73.5%;',
    'height' => '175',
    'custom_box_bottom' => '1',
	'custom_box_top' => '1',
    'link' => array('w' => '1.5', 'cls' => 'locations','field' => 'location_id'),
	'delete'=> '1',
	'add'=>'Add location',
    'reltb' => 'tb_product@locations', //tb@option
    'type' => 'listview_box',
    'field' => array(
        'location_name' => array(
            'name' => __('Location'),
            'width' => '18',
            'type' => 'text',
        ),
        'location_id' => array(
            'type' => 'id',
        ),
        'location_type' => array(
			'name' 		=>  __('Type'),
			'type' 		=> 'select',
			'droplist'	=> 'location_type',
			'default'	=> 'Sell',
			'width' => '8',
		),
		'stock_usage'	=>array(
			'name' 		=>  __('Usage'),
			'type' 		=> 'select',
			'droplist'	=> 'location_stock_usage',
			'width' => '6',
			),
        'total_stock' => array(
            'name' => __('Total stock'),
            'type' => 'text',
			'align'=>'right',
			'width' => '7',
        ),
		'on_so' => array(
            'name' => __("On SO's"),
            'type' => 'text',
			'align'=>'right',
			'width' => '6',
        ),
		'in_use' => array(
            'name' => __("In use"),
            'type' => 'text',
			'align'=>'right',
			'width' => '6',
        ),
        'in_assembly' => array(
            'name' => __('In assembly'),
			'type' => 'text',
			'align'=>'right',
			'width' => '9',
        ),
		'avalible' => array(
            'name' => __('Avalible'),
			'type' => 'text',
			'align'=>'right',
			'width' => '6',
        ),
		'min_stock' => array(
            'name' => __('Min stock'),
			'type' => 'price',
			'width' => '7',
			'edit' =>'1',
			'noformat'=>'',
        ),
		'low' => array(
            'name' => __('Low'),
			'type' => 'checkbox',
			'align'=>'right',
			'width' => '3',
			'element_input'=>'readonly="readonly"',
        ),
        'on_po' => array(
            'name' => __("On PO's"),
            'type' => 'text',
			'width' => '7',
			'align'=>'right',
        ),
    ),
);


//Amendments/ stocktakes for this item
$ModuleField['relationship']['stock']['block']['stocktakes'] = array(
    'title' => 'Amendments/ stocktakes for this item',
    'css' => 'margin-left:1%; margin-top:1%; width: 73.5%;',
    'height' => '179',
    'custom_box_bottom' => '1',
	'custom_box_top' => '1',
    'link' => array('w' => '1.5', 'cls' => 'locations'),
	'delete'=> '2',
    'reltb' => 'tb_product@stocktakes', //tb@option
    'type' => 'listview_box',
	'add'=>'Add line',
	'total'=>'Total amended',
	'total_css'=>'margin-right:3%;float:right;',
	'total_noprice'=>'',
    'field' => array(
        'stocktakes_date' => array(
            'name' => __('Date'),
            'width' => '10',
            'type' => 'date',
			'fulltime'=>'1',
        ),
		'stocktakes_by' => array(
            'name' => __('By'),
            'width' => '7',
            'type' => 'text',
        ),
		'stocktakes_by_id' => array(
            'name' => __('Stocktakes by id'),
            'type' => 'hidden',
        ),
		'detail' => array(
            'name' => __('Detail'),
            'width' => '28',
            'type' => 'text',
			'edit' =>'1',
        ),
		'location_name' => array(
            'name' => __('Location'),
            'width' => '15',
            'type' => 'text',
        ),
        'location_id' => array(
            'type' => 'id',
        ),
		'location_type' => array(
			'name' 		=>  __('Type'),
			'type' 		=> 'hidden',
			'droplist'	=> 'location_type',
			'default'	=> 'Sell',
			'width' => '8',
		),
		'stock_usage'	=>array(
			'name' 		=>  __('Usage'),
			'type' 		=> 'select',
			'droplist'	=> 'location_stock_usage',
			'width' => '3',
			),
        'qty_in_stock' => array(
            'name' => __('Qty in stock'),
            'type' => 'text',
			'align'=>'right',
			'width' => '7',
        ),
		'qty_counted' => array(
            'name' => __('Qty counted'),
            'type' => 'price',
			'width' => '7',
			'edit' =>'1',
			'noformat'=>''
        ),
		'qty_amended' => array(
            'name' => __('Qty amended'),
            'type' => 'text',
			'align'=>'right',
			'width' => '8',
			'noformat'=>''
        ),

    ),
);





//====== UNITS/SERIALS =======//
// $ModuleField['relationship']['units_serials']['name'] = __('Units/Serials');
// $ModuleField['relationship']['units_serials']['block']['unit_serial'] = array(
//     'title' => 'Units for this batch',
//     'css' => 'width:100%;margin-bottom:1%;',
//     'height' => '200',
//     'add'   => __('Add line'),
//     'link' => array('w' => '1', 'cls' => 'units'),
//     'custom_box_bottom' => '1',
//     'reltb' => 'tb_unit@serial_no',
//     'type' => 'listview_box',
//     'field' => array(
//        'code' => array(
//             'name' => __('Ref no'),
//             'width' => '5',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//        'date_modified' => array(
//             'name' => __('Date added'),
//             'width' => '5',
//             'align' => 'left',
//             'type' => 'date',
//         ),
//         'standard_location_name' => array(
//             'name' => __('Standard location'),
//             'width' => '15',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//         'usage' => array(
//             'name' => __('Usage'),
//             'width' => '3',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//         'serial_no' => array(
//             'name' => __('Serial no'),
//             'width' => '10',
//             'align' => 'center',
//             'type' => 'text',
//         ),
//         'batch_name' => array(
//             'name' => __('Batch'),
//             'width' => '15',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//         'notes' => array(
//             'name' => __('Notes'),
//             'width' => '20',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//         'current_location_name' => array(
//             'name' => __('Current location'),
//             'width' => '8',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//         'status' => array(
//             'name' => __('Status'),
//             'width' => '5',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//     ),
// );


// //====== BATCHES =======//
// //$ModuleField['relationship']['batches']['name'] = __('Batches');
// $ModuleField['relationship']['batches']['name'] = __('Batches');
// $ModuleField['relationship']['batches']['block']['batch'] = array(
//     'title' => 'Units for this batch',
//     'css' => 'width:100%;margin-bottom:1%;',
//      'link' => array('w' => '1', 'cls' => ''),
//     'height' => '200',
//     'type' => 'listview_box',
//     'custom_box_bottom' => '1',
//     //'custom_box_top' => '1',
//     'field' => array(
//        'batch_no' => array(
//             'name' => __('Batch no'),
//             'width' => '5',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//        'batch_name' => array(
//             'name' => __('Batch name'),
//             'width' => '23',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//         'original_quantity' => array(
//             'name' => __('Quantity'),
//             'width' => '5',
//             'align' => 'right',
//             'type' => 'text',
//         ),
//         'qty_used_sold' => array(
//             'name' => __('Used / sold'),
//             'width' => '5',
//             'align' => 'right',
//             'type' => 'text',
//         ),
//         'balance' => array(
//             'name' => __('Balance'),
//             'width' => '5',
//             'align' => 'right',
//             'type' => 'text',
//         ),
//         'created_date' => array(
//             'name' => __('Created date'),
//             'width' => '5',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//         'created_from' => array(
//             'name' => __('Created from'),
//             'width' => '11',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//         'sell_by_date' => array(
//             'name' => __('Sell by date'),
//             'width' => '5',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//         'days1_date' => array(
//             'name' => __('Days'),
//             'width' => '3',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//         'sell_retail' => array(
//             'name' => __('Sell(retail)'),
//             'width' => '5',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//         'days2_date' => array(
//             'name' => __('Days'),
//             'width' => '3',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//         'expiry_date' => array(
//             'name' => __('Expiry date'),
//             'width' => '5',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//         'days3_date' => array(
//             'name' => __('Days'),
//             'width' => '3',
//             'align' => 'left',
//             'type' => 'text',
//         ),
//     ),
// );




//====== Purchase Orders =======//
$ModuleField['relationship']['purchasing']['name'] = __('Purchasing');

// Purchase order of this item
$ModuleField['relationship']['purchasing']['block']['po_supplier'] = array(
    'title' => 'Supplier for this item',
    'add' => 'Add supplier',
    'css' => 'width:100%; float:left; margin-bottom:1%;',
    'height' => '200',
    'reltb' => 'tb_products@supplier', //tb@option
    'type' => 'listview_box',
    'link' => array('w' => '1', 'cls' => 'companies', 'field' => 'company_id'),
    'delete' => '1',
	'custom_box_bottom' => '1',
	'customid' =>'_id',
    'field' => array(
        'company_id' => array(
            'type' => 'hidden',
            'element_input' => 'class="supplier_com_id"'
        ),
        'company_name' => array(
            'name' => __('Supplier'),
            'type' => 'relationship',
            'align' => 'left',
            'width' => '14',
			'title' => 'Specify Current supplier',
            //'cls' => 'companies',
            'edit' => '1',
            'para' => ",'?is_supplier=1'"
        ),
        'sku' => array(
            'name' => __('SKU'),
            'type' => 'relationship',
            'align' => 'left',
            'width' => '11',
			'title' => 'Specify Vendor stock',
			//'cls' => 'products',
			//'para' => ",'supplier_where()'",
			'css'=>'width:80%!important;',
			'edit' => '1',
        ),
		'product_link' => array(
            'name' => __(' '),
            'type' => 'text',
            'width' => '3',
        ),
		'product_id' => array(
            'name' => __(' '),
            'type' => 'id',
        ),
        'name' => array(
            'name' => __('Name'),
            'type' => 'text',
            'align' => 'left',
            'width' => '17',
        ),

        'sizew' => array(
            'name' => __('Size-W'),
            'type' => 'price',
            'width' => '4',
            'edit' => '1',
            'align' => 'center'
        ),
        'sizew_unit' => array(
            'name' => __(''),
            'type' => 'select',
            'droplist' => 'product_oum_size',
            'default' => 'in',
            'element_input' => 'combobox_blank="1"',
            'not_custom' => '1',
            'edit' => '1',
            'outcss' => 'width: 70%; margin-left: 10%;',
            'width' => '3',
        ),
        'sizeh' => array(
            'name' => __('Size-H'),
            'type' => 'price',
            'width' => '4',
            'edit' => '1',
            'align' => 'center'
        ),
        'sizeh_unit' => array(
            'name' => __(''),
            'type' => 'select',
            'droplist' => 'product_oum_size',
            'default' => 'in',
            'element_input' => 'combobox_blank="1"',
            'not_custom' => '1',
            'edit' => '1',
            'width' => '3',
            'outcss' => 'width: 70%; margin-left: 10%;'
        ),
        'sell_by' => array(
            'name' => __('Sold by'),
            'type' => 'hidden',
            'droplist' => 'product_sell_by',
            'default' => 'area',
            'element_input' => 'combobox_blank="1"',
            'not_custom' => '1',
            'edit' => '1',
            'width' => '5',
        ),
		'oum' => array(
            'name' => __('Sold by unit'),
            'type' => 'select',
            'droplist' => 'product_oum_unit',
            'default' => 'unit',
            'element_input' => 'combobox_blank="1"',
            'not_custom' => '1',
            //'edit' => '1',
            'width' => '5',
        ),
        'sell_price' => array(
            'name' => __('Cost price'),
            'type' => 'price',
            'width' => '5',
            'align' => 'center',
            'edit' => '1'
        ),
		'unit_price' => array(
            'name' => __('Unit price'),
            'type' => 'price',
            'width' => '5',
            'align' => 'right',
			'numformat'=>3,
        ),
		'oum_depend' => array(
            'name' => __('UOM'),
            'type' => 'select',
            'droplist' => 'product_oum_area',
            'default' => 'unit',
            'element_input' => 'combobox_blank="1"',
            'not_custom' => '1',
			'edit'=>'1',
            'width' => '3',
        ),
        'current' => array(
            'name' => __('Current'),
            'type' => 'radio',
            'width' => '4',
            'edit' => 1,
            'align' => 'center'
        ),
    )
);

// Purchase Orders
$ModuleField['relationship']['purchasing']['block']['po_of_item'] = array(
    'title' => 'Purchase orders for this item',
    'css' => 'width:30%; float: left;',
    'height' => '175',
    'link' => array('w' => '3', 'cls' => 'purchaseorders'),
    'add' => 'Create purchase orders',
    'reltb' => 'tb_purchaseorder@', //tb@option
    'type' => 'listview_box',
    'custom_box_top' => '1',
    'total' => 'Total',
    'total_css' => 'margin-right:3%;',
    'field' => array(
        'code' => array(
            'name' => 'PO #',
            'width' => '10',
            'type' => 'text',
            'align' => 'center',
        ),
        'purchord_date' => array(
            'name' => __('Date'),
            'type' => 'text',
            'align' => 'center',
            'width' => '17',
        ),
        'company_name' => array(
            'name' => __('Supplier'),
            'width' => '36',
            'type' => 'text',
            'align' => 'left',
        ),
        'purchase_orders_status' => array(
            'name' => __('Status'),
            'type' => 'text',
            'align' => 'center',
            'width' => '16',
        ),
        'quantity' => array(
            'name' => __('Qty'),
            'type' => 'text',
            'align' => 'center',
            'width' => '10',
        ),
    ),
);


// Purchase Orders
$ModuleField['relationship']['purchasing']['block']['locations'] = array(
    'title' => 'Locations for this item',
    'css' => 'margin-left:1%; width: 69%;',
    'height' => '175',
    'custom_box_bottom' => '1',
	'custom_box_top' => '1',
    'link' => array('w' => '1.5', 'cls' => 'locations','field' => 'location_id'),
	'delete'=> '1',
	'add'=>'Add location',
    'reltb' => 'tb_product@locations', //tb@option
    'type' => 'listview_box',
    'field' => array(
        'location_name' => array(
            'name' => __('Location'),
            'width' => '18',
            'type' => 'text',
        ),
        'location_id' => array(
            'type' => 'id',
        ),
        'location_type' => array(
			'name' 		=>  __('Type'),
			'type' 		=> 'select',
			'droplist'	=> 'location_type',
			'default'	=> 'Sell',
			'width' => '8',
		),
		'stock_usage'	=>array(
			'name' 		=>  __('Usage'),
			'type' 		=> 'select',
			'droplist'	=> 'location_stock_usage',
			'width' => '6',
			),
        'total_stock' => array(
            'name' => __('Total stock'),
            'type' => 'text',
			'align'=>'right',
			'width' => '7',
        ),
		'on_so' => array(
            'name' => __("On SO's"),
            'type' => 'text',
			'align'=>'right',
			'width' => '6',
        ),
		'in_use' => array(
            'name' => __("In use"),
            'type' => 'text',
			'align'=>'right',
			'width' => '6',
        ),
        'in_assembly' => array(
            'name' => __('In assembly'),
			'type' => 'text',
			'align'=>'right',
			'width' => '9',
        ),
		'avalible' => array(
            'name' => __('Avalible'),
			'type' => 'text',
			'align'=>'right',
			'width' => '6',
        ),
		'min_stock' => array(
            'name' => __('Min stock'),
			'type' => 'price',
			'width' => '7',
			'edit' =>'1',
			'noformat'=>'',
        ),
		'low' => array(
            'name' => __('Low'),
			'type' => 'checkbox',
			'align'=>'right',
			'width' => '3',
			'element_input'=>'readonly="readonly"',
        ),
        'on_po' => array(
            'name' => __("On PO's"),
            'type' => 'text',
			'width' => '7',
			'align'=>'right',
        ),
    ),
);




// Purchase Orders
$ModuleField['relationship']['purchasing']['block']['products_useon'] = array(
    'title' => __('The products use on this item'),
    'moreclass' => 'full_width',
    'css' => 'margin-top:1%',
    'height' => '251',
    'link' => array('w' => '1', 'cls' => 'products'),
    'type' => 'listview_box',
    'field' => array(
        'code' => array(
            'name' => __('Code'),
            'width' => '4',
            'align' => 'center',
        ),
        'name' => array(
            'name' => __('Name'),
            'width' => '46',
        ),
        'product_type' => array(
            'name' => __('Type'),
            'width' => '15',
			'type' => 'select',
        	'droplist' => 'product_type',
        ),
        'category' => array(
            'name' => __('Category'),
            'width' => '15',
			'type' => 'select',
        	'droplist' => 'product_category',
        ),
    ),
);




//====== ORDERS =======//
$ModuleField['relationship']['orders']['name'] = __('Sales Orders');
//Sales orders
$ModuleField['relationship']['orders']['block']['salesorders'] = array(
    'title' => 'Sales orders for this item',
    'css' => 'width:100%;',
    'height' => '350', //350
    'custom_box_bottom' => '1',
    'custom_box_top' => '1',
    'footlink' => array(
        'label' => __('Click to view full details')
    ),
    'link' => array('w' => '1', 'cls' => 'salesorders', 'field' => '_id'),
    'delete' => 1,
    'reltb' => 'tb_product@salesorders', //tb@option
    'type' => 'listview_box',
    'field' => array(
        '_id' => array(
            'name' => __('Order ID'),
            'type'  => 'hidden'
        ),
        'code' => array(
            'name' => __('Ref no'),
            'width' => '5',
            'align' => 'center',
            'type' => 'text',
        ),
        'salesorder_date' => array(
            'name' => __('Date'),
            'width' => '10',
            'type' => 'date',
        ),
        'company_name' => array(
            'name' => __('Company'),
            'width' => '10',
            'type' => 'idlink',
            'relid' => 'company_id',
            'cls' => 'companies',
        ),
        'company_id' => array(
            'name' => __('Company ID'),
            'type' => 'id',
        ),
        'contact_name' => array(
            'name' => __('Contact'),
            'width' => '8',
            'type' => 'idlink',
            'relid' => 'contact_id',
            'cls' => 'contacts',
        ),
        'contact_id' => array(
            'name' => __('Contact ID'),
            'type' => 'id',
        ),
        'our_rep' => array(
            'name' => __('Our rep'),
            'width' => '12',
            'type' => 'idlink',
            'relid' => 'our_rep_id',
            'cls' => 'contacts',
        ),
        'our_rep_id' => array(
            'name' => __('User ID'),
            'type' => 'id',
        ),
        'job_number' => array(
            'name' => __('Job no'),
            'width' => '8',
            'type' => 'idlink',
            'cls' => 'jobs',
            'relid' => 'job_id',
        ),
        'job_id' => array(
            'name' => 'Job ID',
            'type' => 'id',
        ),
        'job_name' => array(
            'name' => __('Job name'),
            'width' => '12',
            'relid' => 'job_id',
            'type' => 'idlink',
            'cls' => 'jobs',
        ),
        'status' => array(
            'name' => __('Status'),
            'width' => '10',
            'type' => 'select',
            'droplist' => 'salesorders_status',
        ),
        'quantity' => array(
            'name' => __('Quantity'),
            'width' => '10',
            'align' => 'right',
        ),
    ),
);


//====== SHIPPING =======//
$ModuleField['relationship']['shipping']['name'] = __('Shipping');
$ModuleField['relationship']['shipping']['block']['ship'] = array(
    'title' => 'Shipping / dispatches for this item',
    'css'   => 'width: 100%; margin-bottom:1%;',
    'height' => '400',
    'footlink' => array(
        'label' => __('Click to view full details'),
        ),
    'custom_box_bottom' => '1',
    'link' => array('w' => '1', 'cls' => 'shippings','field'=>'_id'),
    'reltb' => 'tb_product@shippings',
    'delete' => 1,
    'type' => 'listview_box',
    'field' => array(
        '_id' => array(
            'name' => 'Shipping ID',
            'type' => 'hidden'
        ),
        'code' => array(
            'name' => __('Ref no'),
            'width' => '3',
            'align' => 'left',
            'type' => 'text',
        ),
        'shipping_type' => array(
            'name' => __('Type'),
            'width' => '3',
            'align' => 'left',
            'type' => 'text',
        ),
        'return_status' => array(
            'name' => __('Return'),
            'width' => '3',
            'align' => 'center',
            'type' => 'checkbox',
        ),
        'shipping_date' => array(
            'name' => __('Date'),
            'width' => '6',
            'align' => 'left',
            'type' => 'date',
        ),
        'company_name' => array(
            'name' => __('Company'),
            'width' => '19',
            'align' => 'left',
            'relid' => 'company_id',
            'type' => 'idlink',
            'cls' => 'companies',
        ),
        'company_id' => array(
            'name' => 'Company ID',
            'type' => 'id',
        ),
        'contact_name' => array(
            'name' => __('Contact'),
            'width' => '11',
            'align' => 'left',
            'relid' => 'contact_id',
            'type' => 'idlink',
            'cls' => 'contacts',
        ),
        'contact_id' => array(
            'name' => 'Contact ID',
            'type' => 'id',
        ),
        'our_rep' => array(
            'name' => __('Our rep'),
            'width' => '9',
            'align' => 'left',
            'relid' => 'our_rep_id',
            'type' => 'idlink',
            'cls' => 'contacts',
        ),
        'our_rep_id' => array(
            'name' => 'Our Rep ID',
            'type' => 'id',
        ),
        'job_number' => array(
            'name' => __('Job no'),
            'width' => '5',
            'align' => 'left',
            'relid' => 'job_id',
            'type' => 'idlink',
            'cls' => 'jobs',
        ),
        'job_id' => array(
            'name' => 'Job ID',
            'type' => 'id',
        ),
        'job_name' => array(
            'name' => __('Job name'),
            'width' => '10',
            'align' => 'left',
            'relid' => 'job_id',
            'type' => 'idlink',
            'cls' => 'jobs',
        ),
        'shipping_status' => array(
            'name' => __('Status'),
            'width' => '5',
            'align' => 'left',
            'type' => 'text',
        ),
        'quantity_in' => array(
            'name' => __('Qty in'),
            'width' => '4',
            'align' => 'right',
            'type' => 'text',
        ),
        'quantity_out' => array(
            'name' => __('Qty out'),
            'width' => '4',
            'align' => 'right',
            'type' => 'text',
        ),
    ),
);




//====== INVOICE =======//
$ModuleField['relationship']['invoices']['name'] = __('Invoices');
//Sales orders
$ModuleField['relationship']['invoices']['block']['salesinvoices'] = array(
    'title' => 'Sales invoices for this item',
    'css' => 'width:100%;margin-bottom:1%;',
    'height' => '200',
    'footlink' => array(
        'label' => __('Click to view full details'),
    ),
    'link' => array('w' => '1', 'cls' => 'salesinvoices', 'field'=>'_id'),
    'reltb' => 'tb_product@salesinvoices', //tb@option
    'total' => 'Total (not inc. cancelled)',
    'total_css' => 'margin-right:1%;',
    'type' => 'listview_box',
    'delete' => 1,
    'field' => array(
        '_id' => array(
            'name' => 'Invoice ID',
            'type' => 'hidden'
        ),
        'code' => array(
            'name' => __('Ref no'),
            'width' => '4',
            'align' => 'center',
            'type' => 'text',
        ),
        'invoice_type' => array(
            'name' => __('Type'),
            'width' => '5',
            'type' => 'text',
        ),
        'invoice_date' => array(
            'name' => __('Date'),
            'width' => '7',
            'type' => 'date',
        ),
        'company_name' => array(
            'name' => __('Company'),
            'width' => '22',
            'type' => 'idlink',
            'relid' => 'company_id',
            'cls' => 'companies',
        ),
        'company_id' => array(
            'name' => __('Company ID'),
            'type' => 'id',
        ),
        'contact_name' => array(
            'name' => __('Contact'),
            'width' => '10',
            'type' => 'idlink',
            'relid' => 'contact_id',
            'cls' => 'contacts',
        ),
        'contact_id' => array(
            'name' => __('Contact ID'),
            'type' => 'id',
        ),
        'our_rep' => array(
            'name' => __('Our rep'),
            'width' => '12',
            'type' => 'idlink',
            'relid' => 'our_rep_id',
            'cls' => 'contacts',
        ),
        'our_rep_id' => array(
            'name' => __('User ID'),
            'type' => 'id',
        ),
        'job_number' => array(
            'name' => __('Job no'),
            'width' => '3',
            'align' => 'center',
             'relid' => 'job_id',
            'type' => 'idlink',
            'cls' => 'jobs',
        ),
        'job_name' => array(
            'name' => __('Job name'),
            'width' => '11',
            'type' => 'idlink',
            'relid' => 'job_id',
            'cls' => 'jobs',
        ),
        'job_id' => array(
            'name' => __('Job ID'),
            'type' => 'id',
        ),
        'invoice_status' => array(
            'name' => __('Status'),
            'width' => '5',
            'type' => 'select',
            'droplist' => 'salesinvoices_status',
        ),
        'quantity' => array(
            'name' => __('Quantity'),
            'width' => '5',
            'type' => 'text',
            'align' => 'right',
        ),
    ),
);


//Sales orders
$ModuleField['relationship']['invoices']['block']['vendorinvoice'] = array(
    'title' => 'Vendor invoices for this item',
    'css' => 'width:100%;',
    'height' => '200',
    'footlink' => array(
        'label' => __('Click to view full details'),
    ),
    'link' => array('w' => '1', 'cls' => 'salesinvoices'),
    'reltb' => 'tb_salesinvoice@products', //tb@option
    'total' => 'Total (not inc. cancelled)',
    'total_css' => 'margin-right:1%;',
    'type' => 'listview_box',
    'field' => array(
        'code' => array(
            'name' => __('Ref no'),
            'width' => '3',
            'align' => 'center',
            'type' => 'text',
        ),
        'invoice_type' => array(
            'name' => __('Type'),
            'width' => '5',
            'type' => 'text',
        ),
        'date_modified' => array(
            'name' => __('Date'),
            'width' => '8',
            'type' => 'date',
        ),
        'company_name' => array(
            'name' => __('Company'),
            'width' => '22',
            'type' => 'idlink',
            'relid' => 'company_id',
            'cls' => 'companies',
        ),
        'company_id' => array(
            'name' => __('Company ID'),
            'type' => 'id',
        ),
        'contact_name' => array(
            'name' => __('Contact'),
            'width' => '12',
            'type' => 'idlink',
            'relid' => 'contact_id',
            'cls' => 'contacts',
        ),
        'contact_id' => array(
            'name' => __('Contact ID'),
            'type' => 'id',
        ),
        'our_rep' => array(
            'name' => __('Our rep'),
            'width' => '12',
            'type' => 'idlink',
            'relid' => 'our_rep_id',
            'cls' => 'contacts',
        ),
        'our_rep_id' => array(
            'name' => __('User ID'),
            'type' => 'id',
        ),
        'job_number' => array(
            'name' => __('Job no'),
            'width' => '3',
            'align' => 'center',
        ),
        'job_name' => array(
            'name' => __('Job name'),
            'width' => '11',
            'type' => 'idlink',
            'relid' => 'job_id',
            'cls' => 'jobs',
        ),
        'job_id' => array(
            'name' => __('Job ID'),
            'type' => 'id',
        ),
        'invoice_status' => array(
            'name' => __('Status'),
            'width' => '5',
            'type' => 'select',
            'droplist' => 'salesinvoices_status',
        ),
        'quantity' => array(
            'name' => __('Quantity'),
            'width' => '5',
            'type' => 'price',
            'align' => 'right',
        ),
    ),
);


//====== DOCUMENTS =======//
$ModuleField['relationship']['documents']['name'] = __('Documents');





//====== OTHERS =======//
$ModuleField['relationship']['other']['name'] = __('Other');
$ModuleField['relationship']['other']['block']['otherdetails'] = array(
	'title'	=>__('Other details'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '240',
	'add'	=> __('Add line'),
	'reltb'	=> 'tb_product@otherdetails',//tb@option
	'delete' => '6',
	'field'=> array(
				'heading' => array(
					'name' 		=>  __('Heading'),
					'type'	=> 'text',
					'width' => '18',
					'edit'=>'1',
				),
				'details' => array(
					'name' 		=>  __('Details'),
					'width' => '70',
					'type'	=> 'text',
					'edit'	=> '1',
				),
			),
);

$ModuleField['relationship']['other']['block']['production_step'] = array(
	'title'	=>__('Asset tags'),
	'type'	=>'listview_box',
	'css'	=>'width:98%; float:left; margin-left:2%;',
	'height' => '450',
	'add'	=> __('Add line'),
	'reltb'	=> 'tb_product@production_step',//tb@option
	'delete' => '2',
	'customid' => 'id_delete',
	'field'=> array(
                'from'=>array(
                    'name'      =>  __('&nbsp;&nbsp;Code'),
                    'type'  => 'text',
                    'width' => '6',
                    'edit' => '0',
                    ),
				'name' => array(
                    'name'      =>  __('Name'),
                    'type'  => 'text',
                    'width' => '22',
                ),
				'product_id' => array(
                    'name'      =>  __('ID'),
                    'type'  => 'hidden',
                ),
				'product_type' => array(
					'name' => __('Type'),
					'type' => 'select',
					'droplist' => 'product_type',
					'width' => '11',
				),
				'oum' => array(
					'name' => __('Sold by'),
					'type' => 'select',
					'droplist' => 'product_type',
					'width' => '6',
				),
				'tag_key' => array(
					'name' 		=>  __('&nbsp;&nbsp;Tag'),
					'type'	=> 'select',
					'droplist'	=> 'production_department',
					'width' => '18',
					'edit' => '1',
				),
				'factor' => array(
					'name' 		=>  __('Factor'),
					'type'	=> 'price',
					'width' => '6',
					'align'=>'right',
					'edit' => '1',
				),
				'min_of_uom' => array(
					'name' 		=>  __('Min minute/UOM'),
					'type'	=> 'price',
					'width' => '12',
					'align'=>'right',
					'edit' => '1',
				),

                'cost_per_hour' => array(
                    'name'      =>  __('Cost/hr'),
                    'type'  => 'price',
                    'width' => '6',
                    'align'=>'right',
					'edit' => '1',
                ),
			),
);

$ModuleField['relationship']['other']['block']['noteactive'] = array(
	'title'	=>__('Note & activities'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:10px;',
	'height' => '150',
	'add'	=> __('Add line'),
	'reltb'	=> 'tb_product@noteactive',//tb@option
	'delete' => '1',
	'field'=> array(
				'note_type' => array(
					'name' 		=>  __('Type'),
					'type'	=> 'select',
					'droplist'	=> 'com_type',
					'width' => '10',
				),
				'note_dates' => array(
					'name' 		=>  __('Date'),
					'type'	=> 'text',
					'width' => '10',
				),
				'note_by' => array(
					'name' => __('By'),
					'width' => '15',
					'type' => 'idlink',
					'relid' => 'note_by_id',
					'cls' => 'contacts',
				),
				'note_by_id' => array(
					'name' => __('By ID'),
					'type' => 'id',
				),
				'note_details' => array(
					'name' 		=>  __('Details'),
					'width' => '53',
					'type'	=> 'text',
					'edit'	=> '1',
				),
			),
);




$ModuleField['relationship']['description']['name'] = __('Description');



//====== PRITING =======//
// $ModuleField['relationship']['printing']['name'] = __('Printing');

// $ModuleField['relationship']['printing']['block']['printer_setup'] = array(
//     'title' => 'Printer setup',
//     'css' => 'width:25%;margin-top:0;float:left;',
//     'height' => '310',
//     'type' => 'editview_box',
//     'field' => array(
// 		'is_printer' => array(
// 			'name' => __('Is printer'),
// 			'type' => 'checkbox',
// 			'label'=> ' <span id="is_printer_check" style="float:left;cursor:pointer;margin-left:10px;"> this is printer</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
// 			'default'	=> 0,
// 		),
// 		'paper_size' => array(
// 			'name'  => __('Paper size'),
// 			'type' 		=> 'select',
// 			'droplist'	=> 'printing_paper_size',
// 			'default'	=> '',
// 			'not_custom'=>'1',
// 			'edit'		=>'1',
// 		),
// 		'add_paper_size' => array(
// 			'name' => __(''),
// 			'type' => 'button',
// 			'value' => ' Add size ',
// 			'afterinline' => '<div style="float:left;width:70%">W:<input id="paper_size_w" style="width:25%" />&nbsp;&nbsp; H:<input id="paper_size_h" style="width:25%" /> (in)</div>',
// 			'css' => 'float:right;cursor:pointer;',
// 		),
// 		'none' => array(
// 			'name' => __('Sheet yield calculator'),
// 			'type' => 'header',
// 		),
// 		'poster_size_w' => array(
// 			'name' => __('Poster size (W)'),
// 			'type' => 'text',
// 		),
// 		'poster_size_h' => array(
// 			'name' => __('Poster size (H)'),
// 			'type' => 'text',
// 		),
// 		'calculator_button' => array(
// 			'name' => __(''),
// 			'type' => 'button',
// 			'value' => ' Calculator ',
// 			'css' => 'float:right;cursor:pointer;',
// 		),
// 		'none1' => array(
// 			'name' => __('The best results'),
// 			'type' => 'header',
// 		),
// 		'results_type' => array(
// 			'name'  => __('Type'),
// 			'type' => 'text',
// 			'lock' => '1',
// 		),
// 		'results_total_yield' => array(
// 			'name'  => __('Total yield'),
// 			'type' => 'text',
// 			'element_input'=>'maxlength="20"',
// 			'lock' => '1',
// 		),
// 		'results_sheet_utilization' => array(
// 			'name'  => __('Sheet Utilization'),
// 			'type' => 'text',
// 			'element_input'=>'maxlength="20"',
// 			'lock' => '1',
// 		),
// 		'row_col' => array(
// 			'name'  => __('Row-Col'),
// 			'type' => 'text',
// 			'lock' => '1',
// 		),

//     ),
// );

// //Image
// $ModuleField['relationship']['printing']['block']['sheetimage'] = array(
//     'title' => __('Image'),
//     'css' => 'width:48%;margin-top:0;margin-left:1%;float:left;',
//     'upload' => 'Import image',
//     'type' => 'upload_box',
//     'field' => array(
//         'files' => array(
//             'colection' => 'tb_document',
//             'name' => __('<div class="p_paper"><div class="p_hor">&nbsp;A&nbsp;</div><div class="p_ver">&nbsp;B&nbsp;</div></div>'),
//             'css' => 'height:290px;padding: 10px 0px;',
//         ),
//     ),
// );

// $ModuleField['relationship']['printing']['block']['price_calculator'] = array(
//     'title' => 'Price setup',
//     'css' => 'width:25%;margin-top:0;float:right;',
//     'height' => '310',
//     'type' => 'editview_box',
//     'field' => array(
// 		'option_unit_price' => array(
// 			'name' => __('Unit price'),
// 			'type' => 'checkbox',
// 			'label'=> ' Use option in General &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
// 			'default'	=> '0',
// 		),
// 		'inkcolor' => array(
// 			'name'  => __('Ink color (inkcolor)'),
// 			'type' 		=> 'select',
// 			'droplist'	=> 'printing_ink_color',
// 			'default'	=> '0',
// 		),
// 		'rip' => array(
// 			'name'  => __('Rip constant (rip)'),
// 			'type' 		=> 'price',
// 			'lock' => '1',
// 		),
// 		'packing' => array(
// 			'name'  => __('Packing (packing)'),
// 			'type' 		=> 'select',
// 			'droplist'	=> 'printing_packing',
// 			'default'	=> '0',
// 		),
// 		'cutting' => array(
// 			'name'  => __('Cutting (cutting)'),
// 			'type' 		=> 'price',
// 			'lock' => '1',
// 		),

// 		'none' => array(
// 			'name' => __('Price calculator'),
// 			'type' => 'header',
// 		),
// 		'cutting_amount' => array(
// 			'name'  => __('Cutting amount'),
// 			'type' => 'text',
// 		),
// 		'cut_ra' => array(
// 			'name'  => __('Cutting rate'),
// 			'type' 		=> 'price',
// 			'default' => 1.5,
// 		),
// 		'quantity' => array(
// 			'name'  => __('Quantity'),
// 			'type' => 'price',
// 			'default' => 100,
// 		),
// 		'calculator_price' => array(
// 			'name' => __(''),
// 			'type' => 'button',
// 			'value' => ' Calculator ',
// 			'css' => 'float:right;cursor:pointer;',
// 		),
// 		'none2' => array(
// 			'name' => __('Result'),
// 			'type' => 'header',
// 		),
// 		'printing_price' => array(
// 			'name'  => __('Printing price'),
// 			'type' => 'price',
// 			'lock' => '1',
// 		),

//     ),
// );




// //====== PRITING =======//
// $ModuleField['relationship']['cutting']['name'] = __('2D Cutting');

// $ModuleField['relationship']['cutting']['block']['setup_size'] = array(
//     'title' => 'Setup size',
//     'css' => 'width:25%;margin-top:0;float:left;',
//     'height' => '305',
//     'type' => 'editview_box',
//     'field' => array(
//         'product_name'  =>array(
//             'name'      =>  __('Choice material'),
//             'type'      => 'relationship',
//             'cls'       => 'products',
//             'id'        => 'product_id',
//             'css'       => 'padding-left:3.5%;width: 93.5%;',
//             'lock'      => '1',
//             'para'      => ",'?product_type=\"Vendor Stock\"'",
//         ),
//         'product_id'    =>array(
//             'type'      => 'id',
//             'element_input' => ' class="jthidden"',
//         ),
//         'none' => array(
//             'name' => __('Material'),
//             'type' => 'header',
//         ),
//         'poster_size_w' => array(
//             'name' => __('Size-W'),
//             'type' => 'text',
//         ),
//         'poster_size_h' => array(
//             'name' => __('Size-H'),
//             'type' => 'text',
//         ),
//         'none1' => array(
//             'name' => __('The best results'),
//             'type' => 'header',
//         ),
//         'results_type' => array(
//             'name'  => __('Type'),
//             'type' => 'text',
//             'lock' => '1',
//         ),
//         'results_total_sheet' => array(
//             'name'  => __('Total sheet'),
//             'type' => 'text',
//             'element_input'=>'maxlength="20"',
//             'lock' => '1',
//         ),
//     ),
// );

// $ModuleField['relationship']['cutting']['block']['finished_size_list'] = array(
//     'title' =>__('Size list'),
//     'type'  =>'listview_box',
//     'css' => 'width:25%;margin-top:0;margin-left:1%;float:left;',
//     'height' => '310',
//     'add'   => __('Add line'),
//     'reltb' => 'tb_product@otherdetails',//tb@option
//     'delete' => '6',
//     'field'=> array(
//                 'size_w' => array(
//                     'name'      =>  __('Size-W'),
//                     'type'  => 'text',
//                     'width' => '18',
//                     'edit'=>'1',
//                 ),
//                 'size_h' => array(
//                     'name'      =>  __('Size-H'),
//                     'type'  => 'text',
//                     'width' => '18',
//                     'edit'=>'1',
//                 ),
//                 'quantity' => array(
//                     'name'      =>  __('Quantity'),
//                     'width' => '50',
//                     'type'  => 'text',
//                     'edit'  => '1',
//                 ),
//             ),
// );

// //Image
// $ModuleField['relationship']['cutting']['block']['sheetimage'] = array(
//     'title' => __('Image'),
//     'css' => 'width:48%;margin-top:0;float:right;',
//     'upload' => 'Import image',
//     'type' => 'upload_box',
//     'button'=> 'Result',
//     'field' => array(
//         'files' => array(
//             'css' => 'height:290px;padding: 10px 0px;',
//         ),
//     ),
// );

$ProductField = $ModuleField;
