<?php
$ModuleField = array();
$ModuleField = array(
    'module_name' => __('Log'),
    'module_label' => __('Log Data'),
    'colection' => 'tb_log',
    'title_field' => array('module_name', 'field_name', 'change_from', 'change_to'),
);


//============= *** FIELDS *** =============//
// Panel 1
$ModuleField['field']['panel_1'] = array(
    'setup' => array(
        'css' => 'width:100%;',
        'lablewith' => '25',
        'blockcss' => 'width:30%;float:left;',
    ),
/*	'code' => array(
		'name' 	=>  __('Code'),
        'type' => 'text',
		'moreclass' => 'fixbor',
    ),
*/   'module' => array(
		'name' 	=>  __('Module'),
        'type' => 'text',
		'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
							),
    ),
    'item_id' => array(
        'type' => 'id',
		'name' 	=>  __('Item'),
		'listview'	=>	array(
								'order'	=>	'2',
								'with'	=>	'100',
							),
    ),
    'date_modified' => array(
        'type' => 'hidden',
		'name' 	=>  __('Modified'),
		'listview'	=>	array(
								'order'	=>	'3',
								'with'	=>	'100',
							),
    ),
    'created_by' => array(
        'type' => 'hidden',
    ),
    'modified_by' => array(
        'type' => 'hidden',
		'name' 	=>  __('Modified by'),
		'listview'	=>	array(
								'order'	=>	'5',
								'with'	=>	'100',
							),
    ),
    'change_to' => array(
		'name' 	=>  __('Change to'),
        'type' => 'fieldsave',
    ),
	'none'	=>array(
			'type' 		=> 'not_in_data',
			'moreclass' => 'fixbor2',
	),
);





//============ *** RELATIONSHIP *** =============//

//====== LINE ENTRY =======//
$ModuleField['relationship']['history']['name'] =  __('Line entry');

//Line entry Details
$ModuleField['relationship']['history']['block']['history_detail'] = array(
	'title'	=>__('History Details'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '400',
	'reltb'		=> 'tb_quotation@products',
	'field'=> array(
				'id' => array(
					'name' 	=>  __(''),
					'type'	=> 'text',
					'width'	=> '1',
				),
				'fieldname' => array(
					'name' 	=>  __('Field'),
					'type'	=> 'text',
					'width'	=> '20',
				),
				'change_from' => array(
					'name' 	=>  __('Old data'),
					'type'	=> 'text',
					'width'	=> '25',
				),
				'change_to' => array(
					'name' 	=>  __('New data'),
					'type'	=> 'text',
					'width'	=> '25',
				),
				'created_by' => array(
					'name' 	=>  __('By'),
					'type'	=> 'text',
					'width'	=> '10',
				),
				'created_date' => array(
					'name' 	=>  __('Date'),
					'type' => 'text',
					'width' => '10',
				),
			),
);



$LogField = $ModuleField;