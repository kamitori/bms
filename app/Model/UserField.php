<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('User'),
	'module_label' 	=> __('User'),
	'colection' 	=> 'tb_user',
	'title_field'	=> array('no','user_name'),
);


//============= *** FIELDS *** =============//

// Panel 1
$ModuleField['field']['panel_1'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '25',
			'blockcss' => 'width:30%;float:left;',
			),
	'no'	=>array(
			'name' 		=> __('User no'),
			'type' 		=> 'text',
			'moreclass' => 'fixbor',
			'width'		=> '20%',
			'after'		=> '',
			'lock'		=> '1',
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
	'user_name'	=>array(
			'name' 		=>  __('User  name'),
			'type' 		=> 'text',
			'css'		=> 'padding-left:2%;width:95%;',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'15',
							'css'	=>	'width:15%;',
							'sort'=> '1',
						),
			),
	'deleted'	=>array(
			'name' 		=> __('Status'),
			'type' 		=> 'select',
        	'not_custom' => '1',
			'droplist'	=> 'user_status',
			'default'	=> '',
			'css'		=> 'padding-left:2%;',
			'moreclass' => 'fixbor2',
			)
);

$UserField = $ModuleField;