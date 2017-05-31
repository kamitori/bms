<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Unit'),
	'module_label' 	=> __('Units'),
	'colection' 	=> 'tb_unit',
	'title_field'	=> array('code','product_name','product_code','name'),
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
								'sort'  => '1',
							),
			),
	'mongo_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'product_code'	=>array(
			'name' 		=>  __('Product: code'),
			'type' 		=> 'text',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort'  => '1',
							),
			),
	'product_name'	=>array(
			'name' 		=>  __('Product name'),
			'type' 		=> 'relationship',
			'cls'		=> 'products',
			'id'		=>  'product_id',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort'=> '1',
							),
			),
	'product_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'serial_no'	=>array(
			'name' 		=>  __('Serial no'),
			'type' 		=> 'text',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort'	=> '1',
							),
			),
	'barcode_no'=>array(
			'name' 		=>  __('Barcode no'),
			'type' 		=> 'text',
			'moreclass' => 'fixbor2',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort' 	=> '1',
							),
			),

);

// Panel 3
$ModuleField['field']['panel_2'] = array(
	'setup'	=> array(
			'css'	=> 'width:33%;',
			'lablewith' => '45',//%
			'blockcss' => 'width:69%;float:right;',
			),

	'usage'	=>array(
			'name' 		=>  __('Usage'),
			'type' 		=> 'select',
			'droplist'  => 'usage_order_type',
			'default'	=> 'Rent/loan',
			'moreclass' => 'fixbor',
			'css'		=> 'padding-left:2%;',
			'width'		=> '50%',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
						),
			),
	'asset' => array(
	        'name'      => __('Asset'),
	        'type'		=> 'checkbox',
	        'label' 	=> '&nbsp;',
	        'css'		=> 'width:50%;margin-left:0%;',
	        'checkcss' 	=> 'margin-left:5%;',
	        'default' 	=> 1,
	        'width' 	=> '38%;',
    ),
    'status' => array(
			'name' 		=>  __('Status'),
			'type' 		=> 'select',
			'default'	=> 'For rent/loan',
			'droplist'  => 'status_order_type',
			'css'		=> 'padding-left:2%;',
			'width'		=> '50%',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
						),
			),
	'inactive'=> array(
	        'name' 		=> __('Inactive'),
	        'type' 		=> 'checkbox',
	        'label' 	=> '&nbsp;',
	        'css' 		=> 'width:70%;margin-left:0%;',
	        'checkcss'	=> 'margin-left:5%;',
	        'default' 	=> 1,
	        'width'		=> '38%;',
    ),
    'reason' => array(
			'name' 		=>  __('Reason'),
			'type' 		=> 'select',
			'droplist'  => 'units_order_type',
			'default'   => 'reason',
			'moreclass' => 'fixbor2',
			'css'		=> 'padding-left:2%;',
			'width'		=> '50%',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
						),
			),


);
// Panel 3
$ModuleField['field']['panel_3'] = array(
	'setup'	=> array(
			'css'	    => 'width:33%;',
			'lablewith' => '45',//%
			'blockcss'  => 'width:69%;float:right;',
			),
	'originally_from'	=>array(
			'name' 		=>  __('Originally from'),
			'type'		=> 'text',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'15',
							'css'	=>	'width:15%;',
							'sort'=> '1',
						),
			),
	'current_location_name'	=>array(
			'name' 		=>  __('Current location'),
			'type'		=> 'relationship',
			'cls'		=> 'locations',
			'id'		=> 'current_location_id'
			),
	'current_location_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'standard_location_name'	=>array(
			'name' 		=>  __('Standard location'),
			'type' 		=> 'relationship',
			'cls'   	=> 'locations',
			'id'		=> 'standard_location_id'
			),
	'standard_location_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'date1'	=>array(
			'name' 		=>  __('Date'),
			'type' 		=> 'date',
			),
	'date2'	=>array(
			'name' 		=>  __('Date'),
			'type' 		=> 'date',
			),
);


// Panel 4
$ModuleField['field']['panel_4'] = array(
	'setup'	=> array(
			'css'		=> 'width:33%;',
			'lablewith' => '35',
			),
	'batch_ref'	=>array(
			'name' 		=>  __('Batch ref'),
			'type' 		=> 'text',
			'lock'		=> '1',
			),
	'batch_name'	=>array(
			'name' 		=>  __('Batch'),
			'type' 		=> 'relationship',
			'cls'		=> 'batches',
			'id'		=> 'batch_id',
			),
	'batch_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
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
);



//============ *** RELATIONSHIP *** =============//

//====== GENERAL =======//
$ModuleField['relationship']['general']['name'] = __('General');
$ModuleField['relationship']['general']['block']['stockcurrent1'] = array(
    'title' => 'History / movements for this item',
    'css' => 'width:49.5%;margin-bottom:1%; float: left',
    'height' => '200',
    'link' => array('w' => '1', 'cls' => 'units'),
    'type' => 'listview_box',
    'field' => array(
        'history_date' => array(
            'name' => __('Date'),
            'width' => '10',
            'align' => 'center',
            'type' => 'text',
        ),
        'type' => array(
            'name' => __('Type'),
            'width' => '20',
            'type' => 'text',
        ),
        'from' => array(
			'name' => __('From'),
			'type' => 'text',
			'width'=> '30',
		),
		'to' => array(
			'name' => __('To'),
			'type' => 'text',
			'width'=> '30',
		),
    ),
);
$ModuleField['relationship']['general']['block']['stockcurrent2'] = array(
    'title' => 'Notes / communications',
    'css' => 'width:49.5%;margin-bottom:1%; float: right',
    'height' => '200',
	'add'	=> __('Add line'),
    'link' => array('w' => '1', 'cls' => 'units'),
    'type' => 'listview_box',
    'field' => array(
        'type' => array(
            'name' => __('Type'),
            'width' => '10',
            'align' => 'center',
            'type' => 'text',
        ),
        'notes_date' => array(
            'name' => __('Date'),
            'width' => '10',
            'type' => 'text',
        ),
        'from' => array(
			'name' => __('From'),
			'type' => 'text',
			'width'=> '20',
		),
		'details' => array(
			'name' => __('Details'),
			'type' => 'text',
			'width'=> '50',
		),
    ),
);

//====== GENERAL =======//
$ModuleField['relationship']['bookings']['name'] = __('Bookings');
$ModuleField['relationship']['bookings']['block']['bookings'] = array(
    'title' => 'Resource bookings for this item',
    'css' => 'width:100%;margin-bottom:1%;',
    'height' => '200',
    'add'	=> __('Add line'),
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
            'align' => 'center',
            'type' => 'text',
        ),
        'purpose' => array(
            'name' => __('Purpose'),
            'width' => '5',
            'align' => 'center',
            'type' => 'text',
        ),
      	'from_date' => array(
            'name' => __('Date from'),
            'width' => '5',
            'align' => 'center',
            'type' => 'date',
        ),
        'to_date' => array(
            'name' => __('Date to'),
            'width' => '5',
            'align' => 'center',
            'type' => 'date',
        ),
        'start' => array(
            'name' => __('Start'),
            'width' => '5',
            'align' => 'center',
            'type' => 'text',
        ),
      	'finish' => array(
            'name' => __('Finish'),
            'width' => '5',
            'align' => 'center',
            'type' => 'text',
        ),
        'clash' => array(
            'name' => __('Clash'),
            'width' => '5',
            'align' => 'center',
            'type' => 'text',
        ),
      	'status' => array(
            'name' => __('Status'),
            'width' => '5',
            'align' => 'center',
            'type' => 'text',
        ),
        'late' => array(
            'name' => __('Late'),
            'width' => '5',
            'align' => 'center',
            'type' => 'text',
        ),
        'details' => array(
            'name' => __('Details'),
            'width' => '35',
            'align' => 'center',
            'type' => 'text',
        ),

    ),
);

//====== GENERAL =======//
//$ModuleField['relationship']['other']['name'] = __('Other');

$ModuleField['group']['group_1'] = array('panel_1'=>'Panel 1');
$ModuleField['group']['group_2'] = array('panel_2'=>'Panel 2','panel_3'=>'Panel 3','panel_4'=>'Panel 4');

$UnitField = $ModuleField;