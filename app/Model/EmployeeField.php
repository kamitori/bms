<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Employee'),
	'module_label' 	=> __('Employees'),
	'colection' 	=> 'tb_employees',
	'title_field'	=> array('position','employee_name','mobile','email'),
);


//============= *** FIELDS *** =============//

// Panel 1
$ModuleField['field']['panel_1'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '35',
			'blockcss' => 'width:30%;float:left;',
			),
	'mongo_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'code' => array(
		'name' 		=> __('Employee code'),
		'type' 		=> 'text',
		'moreclass' => 'fixbor',
		'width'		=> '41%;float:left',
		'css'		=> 'width:95%; padding-left:5%;float:left;border-right:1px solid #ddd',
		'lock'		=> '1',
	),
	'employee_name'	=>array(
			'name' 		=>  __('Employee name'),
			'type' 		=> 'relationship',
			'cls'		=> 'contacts',
			'id'		=> 'employee_id',
        	'list_syncname' => 'employee_name',
			'css'		=> 'padding-left:2%;',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'15',
							'css'	=>	'width:15%;',
							'sort'=> '1',
			),
			'para' 		=> ",'?is_employee=1'",

	),
	'employee_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'direct_dial'	=>array(
			'name' 		=> __('Direct Dial'),
			'type' 		=> 'phone',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'mobile'	=>array(
			'name' 		=> __('Mobile'),
			'type' 		=> 'phone',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'email'=>array(
			'name' 		=>  __('Email'),
			'type' 		=> 'email',
			),
	'fax'	=>array(
			'name' 		=> __('Fax'),
			'type' 		=> 'email',
			'default' => '',
			),
	'home_phone'	=>array(
			'name' 		=> __('Home phone'),
			'type' 		=> 'phone',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'extension_no'	=>array(
			'name' 		=> __('Extension no'),
			'type' 		=> 'phone',
			),
	'none'	=>array(
		'type' 		=> 'not_in_data',
		'moreclass' => 'fixbor2',
		),

	'field03' => array(
			'type'	=>'fieldsave',
			),
);





$ModuleField['field']['panel_2'] = array(
	'setup'	=> array(
			'css'	=> 'width:33%;',
			'lablewith' => '44',
			'blockcss' => 'width:32%;float:left;',
			),

	'position'	=>array(
			'name' 		=> __('Position'),
			'type' 		=> 'select',
			'droplist' => 'enquirynote_position',
			'moreclass' => 'fixbor',
			),
	'marital_status' => array(
			'name' 		=> __('Marital status'),
			'type' 		=> 'select',
			'droplist' => 'marital_status',
			),
	'date_birth' => array(
			'name' 		=> __('Date of birth'),
			'type' 		=> 'date',
			'morecss' 		=> 'height: 120px;',
			),
	'none'	=>array(
		'type' 		=> 'not_in_data',
		),
	'none2'	=>array(
		'type' 		=> 'not_in_data',
		'moreclass' => 'fixbor2',
		),

	'field06' => array(
			'type'	=>'fieldsave',
			),
);


$ModuleField['field']['panel_3'] = array(
	'setup'	=> array(
			'css'	=> 'width:70%;',
			'lablewith' => '35',//%
			'blockcss' => 'width:69%;float:right;',
			'blocktype'=> 'address',
			),
	'invoice_address' =>array(
			'name' 		=> __('Default address'),
			'type' 		=> 'text',
			),

);



// Panel 4
$ModuleField['field']['panel_4'] = array(
	'setup'	=> array(
			'css'	=> 'width:33%;" id="employee_image"',
			'lablewith' => '44',
			'blockcss' => 'width:49%;float:left;" id="test"',
			),

);


$ModuleField['relationship']['leave']['name'] = __('Leave');
$ModuleField['relationship']['leave']['block']['leave'] = array(
);


$ModuleField['relationship']['workings_holidays']['name'] = __('Working & Holidays');
$ModuleField['relationship']['workings_holidays']['block']['workings_holidays'] = array(
);

$EmployeeField = $ModuleField;




//============ *** RELATIONSHIP *** =============//

