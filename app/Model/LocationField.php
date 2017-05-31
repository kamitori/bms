<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Location'),
	'module_label' 	=> __('Location'),
	'colection' 	=> 'tb_location',
	'title_field'	=> array('code','name','location_type','contact_name'),
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
			'lock'		=> '1',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort'=> '1',
							),
			),
	'name'	=>array(
			'name' 		=>  __('Location name'),
			'type' 		=> 'text',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort'=> '1',
							),
			),
	'mongo_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'location_type'	=>array(
			'name' 		=>  __('Type'),
			'type' 		=> 'select',
			'droplist'	=> 'location_type',
			),
	'stock_usage'	=>array(
			'name' 		=>  __('Stock usage'),
			'type' 		=> 'select',
			'droplist'	=> 'location_stock_usage',
			'default'	=> 'Sell',
			),
	'inactive' => array(
        'name' => __('Inactive'),
        'type' => 'checkbox',
        'label' => '&nbsp;',
        'css' => 'width:96%;margin-left:0%;border:none;',
        'checkcss' => 'margin-left:5%;',
        'default' => 0,
        'width' => '38%;border:none;',
    ),
	'bookable' => array(
        'name' => __('Bookable'),
        'type' => 'checkbox',
        'label' => '&nbsp;',
        'css' => 'width:96%;margin-left:0%;border:none;',
        'checkcss' => 'margin-left:5%;',
        'default' => 0,
        'width' => '38%;border:none;',
    ),

	'stockuse'	=>array(
		'name' 		=>  __('Stock use'),
        'type' => 'checkbox',
        'label' => '&nbsp;',
        'css' => 'width:96%;margin-left:0%;border:none;',
        'checkcss' => 'margin-left:5%;',
        'default' => 1,
        'width' => '38%;border:none;',
    ),

	'none10' => array(
			'type' 		=> 'not_in_data',
			'moreclass'=>'fixbor2',
			),
);

// Panel 3
$ModuleField['field']['panel_2'] = array(
	'setup'	=> array(
			'css'	=> 'width:33%;',
			'lablewith' => '45',//%
			'blockcss' => 'width:69%;float:right;',
			),

	'company_name'	=>array(
			'name' 		=>  __('Company'),
			'type' 		=> 'relationship',
			'cls'		=> 'companies',
			'id'		=> 'company_id',
			'moreclass' => 'fixbor',
			'css'		=> 'padding-left:2%;',
			'lock'		=> '1',
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
			'id'		=> 'contact_id',
			'css'		=> 'padding-left:2%;',
			'para'		=> ',get_para_employee()',
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
	'fax'	=>array(
			'name' 		=> __('Fax'),
			'type' 		=> 'phone',
			'css'		=> 'padding-left:2%;',
			),
	'email'	=>array(
			'name' 		=> __('Email'),
			'type' 		=> 'email',
			'css'		=> 'padding-left:2%;',
			),
	'none21'	=>array(
		'type' 		=> 'not_in_data',
		),
	'none22'	=>array(
		'type' 		=> 'not_in_data',
		),
	'none23'	=>array(
		'type' 		=> 'not_in_data',
		'moreclass'=>'fixbor2'
	),
);

// Panel 3
$ModuleField['field']['panel_3'] = array(
	'setup'	=> array(
			'css'	=> 'width:35%;',
			'lablewith' => '35',//%
			'blockcss' => 'width:35%;float:right;',
			'blocktype'=> 'address',
			),
	'shipping_address' =>array(
			'name' 		=> __('Address'),
			'type' 		=> 'text',
			),
);


// Panel 4
$ModuleField['field']['panel_4'] = array(
	'setup'	=> array(
			'css'	=> 'width:33%;',
			'lablewith' => '35',
			),
	'description'	=>array(
			'name' 		=>  __('Description'),
			'type' 		=> 'text',
			),
	'none40'	=>array(
		'type' 		=> 'not_in_data',

	),
	'none41'	=>array(
		'type' 		=> 'not_in_data',
	),
	'none42'	=>array(
		'type' 		=> 'not_in_data',

	),
	'none43'	=>array(
		'type' 		=> 'not_in_data',
	),
	'none44'	=>array(
		'type' 		=> 'not_in_data',
	),
	'none45'	=>array(
		'type' 		=> 'not_in_data',

	),
	'none46'	=>array(
		'type' 		=> 'not_in_data',
		'moreclass' => 'fixbor3',
	),
	'products' => array(
			'type'	=>'fieldsave',
			'rel_name'	=>'products',
			),
);



//============ *** RELATIONSHIP *** =============//

//====== GENERAL =======//
$ModuleField['relationship']['general']['name'] = __('General');
$ModuleField['relationship']['general']['block']['stockcurrent'] = array(
    'title' => 'Stock currently at this location',
    'css' => 'width:100%;margin-bottom:1%;',
    'height' => '200',
    'custom_box_bottom' => '1',
	'custom_box_top' => '1',
    'link' => array('w' => '3', 'cls' => 'products','field'=>'_id'),
    'reltb' => 'tb_location@products', //tb@option
    'type' => 'listview_box',
	'full_height' =>'1',
    'field' => array(
    	'_id' => array(
            'name' => 'Invoice ID',
            'type' => 'hidden',
        ),
        'code' => array(
            'name' => __('Code'),
            'width' => '2',
            'align' => 'center',
            'type' => 'text',
        ),
		'sku' => array(
            'name' => __('SKU'),
            'width' => '5',
            'align' => 'left',
            'type' => 'text',
        ),
        'name' => array(
            'name' => __('Name'),
            'width' => '22',
            // 'type' => 'text',
            'relid' => 'product_id',
          'type' => 'idlink',
          'cls' => 'products'
        ),
        'product_id' => array(
            'type' => 'id',
        ),
        'product_type' => array(
			'name' => __('Type'),
			'type' => 'hidden',
			'droplist' => 'product_type',
			'default' => 'Product',
			'width' => '0',
		),
		'category' => array(
			'name' => __('Category'),
			'type' => 'select',
			'droplist' => 'product_category',
			'width' => '8',
		),
		'sizew' => array(
			'name' => __('W'),
			'type' => 'hidden',
			'align' => 'right',
			'width' => '3',
		),
		'sizew_unit' => array(
			'name' => __('&nbsp;'),
			'type' => 'hidden',
			'droplist' => 'product_oum_size',
			'width' => '1',
		),
		'sizeh' => array(
			'name' => __('H'),
			'type' => 'hidden',
			'align' => 'right',
			'width' => '3',
		),
		'sizeh_unit' => array(
			'name' => __('&nbsp;'),
			'type' => 'hidden',
			'droplist' => 'product_oum_size',
			'width' => '1',
		),
		'cost_price' => array(
            'name' => __('Cost value'),
            'type' => 'hidden',
			'width' => '0',
			'align' => 'right',
        ),
		'sell_price' => array(
            'name' => __('Cost price'),
            'type' => 'price',
			'width' => '5',
			'align' => 'right',
        ),
		'oum' => array(
            'name' => __('  OUM'),
            'type' => 'select',
			'droplist' => 'product_oum_unit',
			'width' => '3',
			'align' => 'left',
        ),
		'unit_price' => array(
            'name' => __('Unit price'),
            'type' => 'price',
			'width' => '5',
			'align' => 'right',
        ),
		'oum_depend' => array(
            'name' => __('OUM'),
            'type' => 'select',
			'droplist' => 'product_oum_area',
			'width' => '2',
			'align' => 'left',
        ),
		'total_stock' => array(
            'name' => __('Total stock'),
            'type' => 'price',
            'numformat'=>0,
            'align' => 'right',
			'width' => '5',
        ),
		'profit' => array(
            'name' => __('Profit value'),
            'type' => 'hidden',
			'width' => '0',
			'align' => 'right',
        ),
		'on_so' => array(
            'name' => __("On SO's"),
            'type' => 'text',
			'width' => '5',
			'align' => 'right',
        ),
		'in_use' => array(
            'name' => __("Use"),
            'type' => 'text',
			'width' => '2',
			'align' => 'right',
        ),
        'in_assembly' => array(
            'name' => __('Assembly'),
			'type' => 'text',
			'width' => '4',
			'align' => 'right',
        ),
		'avalible' => array(
            'name' => __('Avalible'),
			'type' => 'price',
			'width' => '4',
			'align' => 'right',
			'numformat'=>0,
        ),
		'min_stock' => array(
            'name' => __('Min stock'),
			'type' => 'price',
			'width' => '5',
			'align' => 'right',
			'numformat'=>0,
        ),
		'low' => array(
            'name' => __('Low'),
			'type' => 'checkbox',
			'width' => '2',
			'align' => 'center',
        ),
        /*'max_stock' => array(
            'name' => __('Max stock'),
			'type' => 'price',
			'width' => '5',
			'align' => 'right',
        ),*/
    ),
);
//====== BOOKINGS =======//
$ModuleField['relationship']['bookings']['name'] = __('Bookings');
$ModuleField['relationship']['bookings']['block']['bookings1'] = array(
    'title' => 'Rental settings',
    'css' => 'width:20%;margin-top:0;',
    'height' => '300',
    'reltb' => 'tb_location',
    'type' => 'editview_box',
	//'custom_box_top' => '1',
    'field' => array(
		'bookable' => array(
			'name' => __('Bookable'),
			'type' => 'checkbox',
			'label'=> ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
			'default'	=> 0,
		),
		'rate_per_hour' => array(
			'name' => __('Rate per hour'),
			'type' => 'text',
			'css'=>'text-align:left;',
			'noformat'=>'',
		),
		'rate_per_day' => array(
			'name' => __('Rate per day'),
			'type' => 'price',
			'css'=>'text-align:left;',
			'noformat'=>'',
		),
		'rate_per_booking' => array(
			'name' => __('Rate per booking'),
			'type' => 'text',
			'css'=>'text-align:left;',
			'noformat'=>'',
		),
		'none' => array(
			'name' => __('Budget related'),
			'type' => 'header',
		),
		'cost_per_hour' => array(
			'name' => __('Cost per hour'),
			'type' => 'text',
			'css'  =>'text-align: left',
		),
		'cost_per_day' => array(
			'name'  => __('Cost per day'),
			'type' 		=> 'text',
			'css' 		=> 'text-align: left',
		),
    ),
);
$ModuleField['relationship']['bookings']['block']['bookings2'] = array(
	'title' => 'Resource bookings for this location',
	    'css' => 'width:75%;margin-bottom:1%; float: left; margin-left: 2%',
	    'height' => '325',
		'add'	=> __('Add line'),
	    'link' => array('w' => '1', 'cls' => 'units'),
	    'type' => 'listview_box',
	    'field' => array(
	        'job_no' => array(
	            'name' => __('Job no'),
	            'width' => '5',
	            'align' => 'center',
	            'type' => 'text',
	        ),
	        'task_ref' => array(
	            'name' => __('Task ref'),
	            'width' => '5',
	            'type' => 'text',
	        ),
	        'purpose' => array(
				'name' => __('Purpose'),
				'type' => 'text',
				'width'=> '5',
			),
			'date_from' => array(
				'name' => __('Date from'),
				'type' => 'text',
				'width'=> '5',
			),
			'date_to' => array(
				'name' => __('Date to'),
				'type' => 'text',
				'width'=> '5',
			),
			'date_from' => array(
				'name' => __('Date from'),
				'type' => 'text',
				'width'=> '5',
			),
			'start' => array(
				'name' => __('Start'),
				'type' => 'text',
				'width'=> '5',
			),
			'finish' => array(
				'name' => __('Finish'),
				'type' => 'text',
				'width'=> '5',
			),
			'clash' => array(
				'name' => __('Clash'),
				'type' => 'text',
				'width'=> '5',
			),
			'status' => array(
				'name' => __('Status'),
				'type' => 'text',
				'width'=> '5',
			),
			'details' => array(
				'name' => __('Details'),
				'type' => 'text',
				'width'=> '40',
			),
	    ),
);
//====== GENERAL =======//
//$ModuleField['relationship']['other']['name'] = __('Other');
$ModuleField['relationship']['other']['name'] = __('Other');
$ModuleField['relationship']['other']['block']['other1'] = array(
    'title' => 'Notes / communications',
    'css' => 'width:49.5%;margin-bottom:1%; float: left',
    'height' => '200',
    'add'	=> __('Add line'),
    'link' => array('w' => '1', 'cls' => ''),
    'type' => 'listview_box',
    'field' => array(
        'fax' => array(
            'name' => __('Fax'),
            'width' => '10',
            'align' => 'center',
            'type' => 'text',
        ),
        'date' => array(
            'name' => __('Date'),
            'width' => '10',
            'type' => 'text',
            'align' => 'center',
        ),
        'from' => array(
			'name' => __('From'),
			'type' => 'text',
			'width'=> '20',
			'align' => 'left',
		),
		'details' => array(
			'name' => __('Details'),
			'type' => 'text',
			'width'=> '50',
			'align' => 'left',
		),
    ),
);
//$ModuleField['relationship']['other']['name'] = __('Other');
$ModuleField['relationship']['other']['block']['otherdetails'] = array(
	'title'	=>__('Other details'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '240',
	'add'	=> __('Add line'),
	'reltb'	=> 'tb_location@otherdetails',//tb@option
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


$LocationField = $ModuleField;