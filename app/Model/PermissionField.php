<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Permission'),
	'module_label' 	=> __('Permissions'),
	'colection' 	=> 'tb_permission',
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
	'name'	=>array(
			'name' 		=> __('Module name'),
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
	'date_modified'=>array(
			'type' 		=> 'hidden',
			),
	'created_by'=>array(
			'type' 		=> 'hidden',
			),
	'modified_by'=>array(
			'type' 		=> 'hidden',
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
	'none'	=>array(
			'type' 		=> 'not_in_data',
			'moreclass' => 'fixbor2',
			),
	'option_list' => array(
			'type'	=>'fieldsave',
			),
	'premission_list' => array(
			'type'	=>'fieldsave',
			),
);



//============ *** RELATIONSHIP *** =============//

//====== Option =======//
$ModuleField['relationship']['option_list']['name'] =  __('Option');

//Option list data
$ModuleField['relationship']['option_list']['block']['optiondata'] = array(
	'title'	=>__('Option list'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '150',
	'add'	=> __('Add option'),
	'custom_box_top'=>'1',
	'reltb'		=> 'tb_permission@option_list',//tb@option
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
$ModuleField['relationship']['premission_list']['name'] =  __('Premission');
//Premission list data
$ModuleField['relationship']['premission_list']['block']['premissiondata'] = array(
	'title'	=>__('Premission list'),
	'type'	=>'listview_box',
	'css'	=>'width:100%;margin-top:0;',
	'height' => '150',
	'add'	=> __('Add premission'),
	'reltb'		=> 'tb_permission@premission_list',//tb@option
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


$PermissionField = $ModuleField;