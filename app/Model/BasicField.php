<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Basic'),
	'module_label' 	=> __('Basics'),
	'colection' 	=> 'tb_basic',
	'title_field'	=> array('','name','',''),
);


//============= *** FIELDS *** =============//

// Panel 1
$ModuleField['field']['panel_1'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '15',
			'blockcss' => 'width:49%;float:left;',
			),
	'name'	=>array(
			'name' 		=> __('Name'),
			'type' 		=> 'text',
			'moreclass' => 'fixbor',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
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
							'css'	=>	'width:8%;',
						),
			),
	'field01' => array(
			'name' 		=> __('Field 01'),
			'type' 		=> 'text',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'field02' => array(
			'name' 		=> __('Field 02'),
			'type' 		=> 'text',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'none'	=>array(
		'type' 		=> 'not_in_data',
		'moreclass' => 'fixbor2',
		),

	'field03' => array(
			'type'	=>'fieldsave',
			),
);



// Panel 1
$ModuleField['field']['panel_2'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '15',
			'blockcss' => 'width:49%;float:left;margin-left:3%;',
			),
	'name2'	=>array(
			'name' 		=> __('Name2'),
			'type' 		=> 'text',
			'moreclass' => 'fixbor',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'description2'=>array(
			'name' 		=>  __('Description2'),
			'type' 		=> 'text',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'field04' => array(
			'name' 		=> __('Field 04'),
			'type' 		=> 'text',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'field05' => array(
			'name' 		=> __('Field 05'),
			'type' 		=> 'text',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'8',
							'css'	=>	'width:8%;',
						),
			),
	'none'	=>array(
		'type' 		=> 'not_in_data',
		'moreclass' => 'fixbor2',
		),

	'field06' => array(
			'type'	=>'fieldsave',
			),
);



//============ *** RELATIONSHIP *** =============//

//====== Option =======//
$ModuleField['relationship']['general']['name'] =  __('Subtab 01');

//Option list data
$ModuleField['relationship']['general']['block']['subtab01'] = array(
	'title'	=>__('Subtab title 1'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '150',
	'add'	=> __('Add option'),
	'reltb'		=> 'tb_basic@subtab01',//tb@option
	'delete' => '6',
	'field'=> array(
				'name' => array(
					'name' 		=>  __('Name'),
					'width'		=> '25',
					'edit'		=> '1',
					'default'	=> 'Click for edit',
				),
				'codekey' => array(
					'name' 		=>  __('Key for code'),
					'edit'		=> '1',
				),
				'description' => array(
					'name' 		=>  __('Description'),
					'width'		=> '30',
					'edit'		=> '1',
				),
				'date_modified' => array(
					'name' 		=>  __('Date_modified'),
					'type'		=> 'id',
					'width'		=> '1',
				),
				'modified_by' => array(
					'name' 		=>  __('Modified_by'),
					'type'		=> 'id',
					'width'		=> '1',
				),
				
			),	
);



//====== Premission =======//
$ModuleField['relationship']['subtab02']['name'] =  __('Subtab 02');
//Premission list data
$ModuleField['relationship']['subtab02']['block']['subtab02'] = array(
	'title'	=>__('Subtab title 2'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '150',
	'add'	=> __('Add new line'),
	'reltb'		=> 'tb_basic@subtab02',//tb@option
	'delete' => '6',
	'field'=> array(
				'name' => array(
					'name' 		=>  __('Name'),
					'width'		=> '25',
					'edit'		=> '1',
					'default'	=> 'Click for edit',
				),
				'codekey' => array(
					'name' 		=>  __('Key for code'),
					'edit'		=> '1',
				),
				'description' => array(
					'name' 		=>  __('Description'),
					'width'		=> '30',
					'edit'		=> '1',
				),
				'date_modified' => array(
					'name' 		=>  __('Date_modified'),
					'type'		=> 'id',
					'width'		=> '1',
				),
				'modified_by' => array(
					'name' 		=>  __('Modified_by'),
					'type'		=> 'id',
					'width'		=> '1',
				),
				
			),		
);


$BasicField = $ModuleField;