<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Api'),
	'module_label' 	=> __('Apis'),
	'colection' 	=> 'tb_api',
	'title_field'	=> array('company','api_key','name','site'),
);


//============= *** FIELDS *** =============//

// Panel 1
$ModuleField['field']['panel_1'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '30',
			'blockcss' => 'width:30%;float:left;',
			),
	'api_key'	=>array(
			'name' 		=> __('Api key'),
			'type' 		=> 'text',
			'moreclass' => 'fixbor',
			'listview'	=>	array(
							'order'	=>	'2',
							'css'	=>	'width:5%;',
						),
			),
	'mongo_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'name'=>array(
			'name' 		=>  __('Name'),
			'type' 		=> 'text',
			'listview'	=>	array(
							'order'	=>	'6',
							'css'	=>	'width:55%;',
						),
			),
	'site'=>array(
			'name' 		=>  __('Site'),
			'type' 		=> 'text',
			'listview'	=>	array(
							'order'	=>	'6',
							'css'	=>	'width:55%;',
						),
			),
	'company'	=>array(
			'name' 		=>  __('Company'),
			'type' 		=> 'relationship',
			'cls'		=> 'companies',
			'id'		=> 'company_id',
        	'list_syncname' => 'company',
			'css'		=> 'padding-left:2%;',
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
	'none'	=>array(
			'type' 		=> 'not_in_data',
			'moreclass' => 'fixbor2',
			),
);



//============ *** RELATIONSHIP *** =============//


$ApiField = $ModuleField;