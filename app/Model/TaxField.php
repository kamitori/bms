<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Tax'),
	'module_label' 	=> __('Taxs'),
	'colection' 	=> 'tb_tax',
	'title_field'	=> array('province','province_key','fed_tax','hst_tax'),
);


//============= *** FIELDS *** =============//

// Panel 1
$ModuleField['field']['panel_1'] = array(
	'setup'	=> array(
			'css'	=> 'width:100%;',
			'lablewith' => '30',
			'blockcss' => 'width:30%;float:left;',
			),
	'province'	=>array(
			'name' 		=> __('Province name'),
			'type' 		=> 'select',
			'droplist'	=> 'product_gst_tax',
			'moreclass' => 'fixbor',
			'listview'	=>	array(
							'order'	=>	'1',
							'css'	=>	'width:10%;',
						),
			),
	'province_key'	=>array(
			'name' 		=> __('Province code'),
			'type' 		=> 'text',
			'lock'		=> 1,
			'listview'	=>	array(
							'order'	=>	'2',
							'css'	=>	'width:5%;',
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
							'order'	=>	'6',
							'css'	=>	'width:55%;',
						),
			),
	'fed_tax'	=>array(
			'name' 		=>  __('GST/HST'),
			'type' 		=> 'price',
			'default'	=> 5,
			'listview'	=>	array(
							'order'	=>	'3',
							'css'	=>	'width:5%;',
							'align'	=>  'right',
						),
			),
	'pro_tax'	=>array(
			'name' 		=>  __('PST'),
			'type' 		=> 'price',
			'listview'	=>	array(
							'order'	=>	'4',
							'css'	=>	'width:5%;',
							'align'	=>  'right',
						),
			),
	'hst_tax'	=>array(
			'name' 		=>  __('HST type'),
			'type' 		=> 'select',
			'droplist'	=> 'tax_hst_type',
			'default'	=> 'H',//H or N
			'listview'	=>	array(
							'order'	=>	'5',
							'css'	=>	'width:8%;',
						),
			),
	'none'	=>array(
			'type' 		=> 'not_in_data',
			'moreclass' => 'fixbor2',
			),
);



//============ *** RELATIONSHIP *** =============//


$TaxField = $ModuleField;