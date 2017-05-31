<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Batche'),
	'module_label' 	=> __('Batches'),
	'colection' 	=> 'tb_batche',
	'title_field'	=> array('code','batch_name','',''),
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
			'width'		=> '30%;text-align:right;',
			'css'		=> 'width:50%; padding-left:1%;',
			'after_field'=>'batch_no',
			'lock'		=> '1',
			'moreinline'=> 'Batche no',
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
	'batch_no'	=>array(
			'name' 		=>  __('Batche no'),
			'type' 		=> 'text',
			'other_type' => 'after_other',
			'classselect' =>'jt_after_field',
			'width'		=> '20%;" id="field_after_quotetype" alt="',
			'element_input' => 'combobox_blank="1"',
			'css'		=> ' width:200%;',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
							'sort'=> '1',
						),
			),
	'batch_name' =>array(
			'name' 			=>  __('Batche name'),
			'type' 			=> 'text',
			'listview'		=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort'=> '1',
							),
			),
	'description'	=>array(
			'name' 			=>  __('Description'),
			'type' 			=> 'text',
			'listview'		=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort'	=> '1',
							),
			),
	'product'=>array(
			'name' 		=> __('Product'),
			'type' 		=> 'text',
			'moreclass' => 'fixbor2',
			'width'		=> '30%;text-align:right;',
			'css'		=> 'width:90%; padding-left:1%;',
			'after_field'=>'product_name',
			'lock'		=> '1',
			//'moreinline'=> 'Batche-no',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
								'sort'=> '1',
							),
			),
	'product_name'	=>array(
			'name' 		=>  __(''),
			'type' 		=> 'relationship',
			'cls'		=> 'products',
			'id'		=> 'product_id',
			'other_type' => 'after_other',
			'classselect' =>'jt_after_field',
			'width'		=> '38%;" id="field_after_quotetype" alt="',
			'element_input' => 'combobox_blank="1"',
			'css'		=> ' width:100%;',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
							'sort'=> '1',
						),
			),
	'product_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
);

// Panel 3
$ModuleField['field']['panel_2'] = array(
	'setup'	=> array(
			'css'	=> 'width:33%;',
			'lablewith' => '45',//%
			'blockcss' => 'width:69%;float:right;',
			),
	
	'batch_created'	=>array(
			'name' 		=>  __('Batch created'),
			'type' 		=> 'date',
			'moreclass' => 'fixbor',
			'css'		=> 'padding-left:2%;',
			'width'		=> '50%',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
						),
			),
	'created_from' => array(
	        'name'      => __('Created from'),
	        'type'		=> 'text',
	        'label' 	=> '&nbsp;',
	        'css'		=> 'width:100%;margin-left:0%;',
	        'checkcss' 	=> 'margin-left:5%;',
	        'default' 	=> 'Stock amendment',
	        'width' 	=> '47%;',
    ), 
    'none40'	=>array(
		'type' 		=> 'not_in_data',
		'css'		=> 'width:100%;margin-left:0%;',

	),
	'none41'	=>array(
		'type' 		=> 'not_in_data',
		'moreclass' => 'fixbor2',


	),

);
// Panel 3
$ModuleField['field']['panel_3'] = array(
	'setup'	=> array(
			'css'	    => 'width:33%;',
			'lablewith' => '45',//%
			'blockcss'  => 'width:69%;float:right;',
			),
	'original_quantity'	=>array(
			'name' 		=>  __('Original quantity'),
			'type'		=> 'text',
			'default'	=> '21.256',
			),
	'qty_used_sold'	=>array(
			'name' 		=>  __('Qty used/sold'),
			'type' 		=> 'text',
			),
	'balance' =>array(
			'name' 		=>  __('Balance'),
			'type' 		=> 'text',
			'default'   => '21.256',
			),
	'none40'	=>array(
		'type' 		=> 'not_in_data',

	),

);


// Panel 4
$ModuleField['field']['panel_4'] = array(
	'setup'	=> array(
			'css'		=> 'width:33%;',
			'lablewith' => '35',
			),
	'sell_by_date'	=>array(
			'name' 		=>  __('Sell by date'),
			'type' 		=> 'date',
			'after_field' => 'left1',
			'width' => '50%;"text-align left"',
			'css'  => 'width:50%; padding-left:0%;',
			'moreinline' => 'Left',
			),
	'left1' => array(
			'name' => __('Left'),
			'type' => 'checkbox',
			'other_type' => 'after_other',
			'classselect' => 'jt_after_field',
			'width'		=> '20%',
			'css' => 'float: left',
			'width'		=> '10%;" id="field_after_quotetype" alt="',
			'element_input' => 'combobox_blank="1"',
			),
	'sell_by_retail'	=>array(
			'name' 		=>  __('Sell by (retail)'),
			'type' 		=> 'date',
			'after_field' => 'left1',
			'width' => '50%;"text-align left"',
			'css'  => 'width:50%; padding-left:0%;',
			'moreinline' => 'Left',
			),
	'left2' => array(
			'name' => __('Left'),
			'type' => 'checkbox',
			'other_type' => 'after_other',
			'classselect' => 'jt_after_field',
			'width'		=> '20%',
			'css' => 'float: left',
			'width'		=> '10%;" id="field_after_quotetype" alt="',
			'element_input' => 'combobox_blank="1"',
			),
	'expiry_date' 	=> array(
			'name' 		=>  __('Expiry date'),
			'type' 		=> 'date',
			'after_field' => 'left1',
			'width' => '50%;"text-align left"',
			'css'  => 'width:50%; padding-left:0%;',
			'moreinline' => 'Left',
			),
	'left3' => array(
			'name' => __('Left'),
			'type' => 'checkbox',
			'other_type' => 'after_other',
			'classselect' => 'jt_after_field',
			'width'		=> '20%',
			'css' => 'float: left',
			'width'		=> '10%;" id="field_after_quotetype" alt="',
			'element_input' => 'combobox_blank="1"',
			),
	'none40'	=>array(
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
    //'custom_box_bottom' => '1',
	//'custom_box_top' => '1',
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
			'width'=> '25',
		),
		'to' => array(
			'name' => __('To'),
			'type' => 'text',
			'width'=> '25',
		),
		'quantity' => array(
			'name' => __('Quantity'),
			'type' => 'text',
			'width'=> '10',
		),
    ),
);
$ModuleField['relationship']['general']['block']['stockcurrent2'] = array(
    'title' => 'Notes / communications',
    'css' => 'width:49.5%;margin-bottom:1%; float: right',
    'height' => '200',
    //'custom_box_bottom' => '1',
	//'custom_box_top' => '1',
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
$ModuleField['relationship']['bookings']['name'] = __('Units / Serials');
$ModuleField['relationship']['bookings']['block']['bookings'] = array(
    'title' => 'Units for this batch',
    'css' => 'width:100%;margin-bottom:1%;',
    'height' => '200',
    'type' => 'listview_box',
    'field' => array(
       'code' => array(
            'name' => __('Ref no'),
            'width' => '5',
            'align' => 'center',
            'type' => 'text',
        ),
       'added_date' => array(
            'name' => __('Date added'),
            'width' => '5',
            'align' => 'left',
            'type' => 'text',
        ),
        'standard_location_name' => array(
            'name' => __('Standard location'),
            'width' => '10',
            'align' => 'left',
            'type' => 'text',
        ),
      	'usage' => array(
            'name' => __('Usage'),
            'width' => '5',
            'align' => 'left',
            'type' => 'text',
        ),
        'serial_no' => array(
            'name' => __('Serial no'),
            'width' => '10',
            'align' => 'left',
            'type' => 'text',
        ),
        'notes' => array(
            'name' => __('Notes'),
            'width' => '40',
            'align' => 'left',
            'type' => 'text',
        ),
      	'current_location_name' => array(
            'name' => __('Current location'),
            'width' => '10',
            'align' => 'left',
            'type' => 'text',
        ),
        'status' => array(
            'name' => __('Status'),
            'width' => '5',
            'align' => 'left',
            'type' => 'text',
        ),
    ),
);


//====== GENERAL =======//
$ModuleField['relationship']['other']['name'] = __('Other');


$BatcheField = $ModuleField;