<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Chart'),
	'module_label' 	=> __('Charts Module'),
	'colection' 	=> 'tb_chart',
	'subcolection' 	=> 'tb_chart_user',
	'title_field'	=> array('','name','',''),
);


//============= *** FIELDS *** =============//

// Panel 1
$ModuleField['field']['panel_1'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '15',
			'blockcss' => 'width:50%;float:left;',
			),
	'code'	=>array(
			'name' 		=> __('Code'),
			'type' 		=> 'text',
			'moreclass' => 'fixbor',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:3%;',
						),
			),
	'name'	=>array(
			'name' 		=> __('Name'),
			'type' 		=> 'text',			
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:35%;',
						),
			),	
	'mongo_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'description'=>array(
			'name' 		=>  __('Description'),
			'type' 		=> 'text',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:35%;',
						),
			),
	'none'	=>array(
		'type' 		=> 'not_in_data',
		'moreclass' => 'fixbor2',
		),
	'field_list' => array(
			'type'	=>'fieldsave',
			),
);



// Panel 1
$ModuleField['field']['panel_2'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '15',
			'blockcss' => 'width:48.5%;float:left;margin-left:1%;',
			),
	'type_chart'	=>array(
		'name' => 'Chart type',
		'type' 		=> 'select',
		'droplist'  => 'chart_type',
		'default'=> 'chart_column',
		'moreclass' => 'fixbor',
		),
	'none3'	=>array(
		'type' 		=> 'not_in_data',
		),
	'none4'	=>array(
		'type' 		=> 'not_in_data',
		),
	'none5'	=>array(
		'type' 		=> 'not_in_data',
		'moreclass' => 'fixbor2',
		),
);


$ModuleField['group'] = array(
								'group_1'=>array('panel_1'),
								'group_2'=>array('panel_2'),
						);


//============ *** RELATIONSHIP *** =============//

//====== Option =======//
$ModuleField['relationship']['field_list']['name'] =  __('Field list');

//Option list data
$ModuleField['relationship']['field_list']['block']['field_list'] = array(
	'title'	=>__('Field list'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '150',
	'add'	=> __('Add option'),
	'reltb'		=> 'tb_chart@field_list',//tb@option
	'delete' => '1',
    'full_height' => '1',
	'field'=> array(
				'label' => array(
					'name' 		=>  __('Label'),
					'width'		=> '20',
					'edit'		=> '1',
				),
				'field_name' => array(
					'name' 		=>  __('Field Name'),
					'width'		=> '20',
					'edit'		=> '1',
				),
				'field_type' => array(
					'name' 		=>  __('Field Type'),
					'type'		=> 'select',
					'droplist' => 'field_type_option',
					'element_input' => 'combobox_blank="1"',
					'not_custom'=>'1',
					'width'		=> '10',
					'edit'		=> '1',
				),
				'default' => array(
					'name' 		=>  __('Default'),
					'type'		=> 'text',
					'width'		=> '10',
					'edit'		=> '1',
				),
				'description' => array(
					'name' 		=>  __('Description'),
					'edit'		=> '1',
					'width'		=> '32',
				),
				
			),	
);



//====== Premission =======//
$ModuleField['relationship']['examples']['name'] =  __('Other');
//Premission list data
$ModuleField['relationship']['examples']['block']['examples'] = array(
	'title'	=>__('Other'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '150',
	'add'	=> __('Add new value'),
	'reltb'		=> 'tb_chart@examples',//tb@option
	'delete' => '1',
	'field'=> array(
				'value_1' => array(
					'name' 		=>  __('Value_1'),
					'width'		=> '20',
					'edit'		=> '1',
				),
				'value_2' => array(
					'name' 		=>  __('Value_2'),
					'type'		=> 'select',
					'dropt_list' => 'field_type_option',
					'width'		=> '10',
					'edit'		=> '1',
				),
				'value_3' => array(
					'name' 		=>  __('Value_3'),
					'edit'		=> '1',
					'width'		=> '62',
				),
				
			),		
);


$ChartField = $ModuleField;