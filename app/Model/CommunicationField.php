<?php

$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Communication'),
	'module_label' 	=> __('Comms'),
	'colection' 	=> 'tb_com',
	'title_field'	=> array('comms_type','contact_name','','comms_date'),
);


//============= *** FIELDS *** =============//
// set default for layout Email. Layout Letter,Fax,Note,Message: custom arr_setting

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
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
							),
			),
	'comms_type'	=>array(
			'name' 		=>  __('Type'),
			'type' 		=> 'select',
			'droplist'	=> 'com_type',
			'default'	=> 'Email',
			'width'		=> '70%',
			'field_class'=>'fieldclass',
			'css'		=> 'padding-left:3.5%;',
			'lock'		=> '1',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
						),
			),
	'contact_title'	=>array(
			'name' 		=>  __('<span class="jt_grey">Contact:</span> Title'),
			'type' 		=> 'select',
			'droplist'	=> 'contacts_title',
			'width'		=> '70%',
			'field_class'=>'fieldclass',
			'css'		=> 'padding-left:3.5%;',
			'lock'		=> '1',
			),
	'amount_sent'	=>array(
			'type' 		=> 'id',
			'name'		=> 'Amount sent',
			'element_input' => ' class="jthidden"',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
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
	'contact_name'	=>array(
			'name' 		=>  __('First name'),
			'type' 		=> 'relationship',
			'cls'		=> 'contacts',
			'id'		=> 'contact_id',
			),
	'contact_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'last_name'	=>array(
			'name' 		=> __('Last name'),
			'type' 		=> 'text',
			),
	'email'	=>array(
			'name' 		=> __('Email'),
			'type' 		=> 'text',
			),
	'comms_date'	=>array(
			'name' 		=> __('Date'),
			'type' 		=> 'date',
			'moreclass' => 'fixbor2',
			'lock'			=>1,
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'6',
							'css'	=>	'width:6%;',
						),
			),
);

// Panel 2
$ModuleField['field']['panel_2'] = array(
	'setup'	=> array(
			'css'	=> 'width:32%;',
			'lablewith' => '38',//%
			'blockcss' => 'width:69%;float:right;',
			),
	'sign_off'	=>array(
			'name' 		=> __('Sign Off'),
			'type' 		=> 'select',
			'droplist'	=> 'com_sign_off',
			'default'	=> 'Regards',
			'moreclass' => 'fixbor',
			'width'		=> '57%',
			),
	'contact_from'	=>array(
			'name' 		=> __('From'),
			'type' 		=> 'text',
			//'droplist'	=> 'comms_type',
			'width'		=> '57%',
			'lock'		=> '1',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'10',
							'css'	=>	'width:10%;',
						),
			),
	'position'	=>array(
			'name' 		=> __('Position'),
			'type' 		=> 'select',
			'droplist'	=> 'contacts_position',
			'width'		=> '57%',
			),
	'salutation'	=>array(
			'name' 		=> __('Salutation'),
			'type' 		=> 'text',
			'width'		=> '57%',
			),
	'name'	=>array(
			'name' 		=> __('Subject'),
			'type' 		=> 'text',
			'width'		=> '57%',
			),
	'include_signature'	=>array(
			'name' 		=> __('Include signature'),
			'type' 		=> 'checkbox',
			'label'		=> '&nbsp;',
			'css'		=> 'width:98%;margin-left:0%;',
			'checkcss'	=> 'margin-left:3%;',
			'default'	=> 0,
			),
	'comms_status'	=>array(
			'name' 		=>  __('Status'),
			//'type' 		=> 'select',
			'type' 		=> 'display',
			'droplist'	=> 'com_status',
			'default'	=> 'Draft',
			'lock'		=> '1',
			'moreclass' => 'fixbor2',
			),
);

// Panel 3
$ModuleField['field']['panel_3'] = array(
	'setup'	=> array(
			'css'	=> 'width:38%;',
			'lablewith' => '32',
			),
	'job_name'	=>array(
			'name' 		=>  __('Job'),
			'type' 		=> 'relationship',
			'cls'		=> 'jobs',
			'id'		=> 'job_id',
			'before_field'	=> 'job_number',
			'width'		=>	'38.5%',
			'css'		=> 'float:left;',
			),
	'job_number'	=>array(
			'name' 		=> __(''),
			'type' 		=> 'text',
			'other_type'=> '1',
			'width'		=> '24%',
			'css'		=> 'width:90%;float:left;padding-left:5%;padding-right: 2%;',
			),
	'job_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'company_name'	=>array(
			'name' 		=>  __('Company'),
			'type' 		=> 'relationship',
			'cls'		=> 'companies',
			'id'		=> 'company_id',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'15',
							'css'	=>	'width:15%;',
						),
			),
	'company_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'toother'	=>array(
			'name' 		=> __('To other'),
			'type' 		=> 'text',
			),
	'email_cc'	=>array(
			'name' 		=> __('CC'),
			'type' 		=> 'select',
			'droplist'	=> 'comms_position',
			),
	'email_bcc'	=>array(
			'name' 		=> __('BCC'),
			'type' 		=> 'select',
			'droplist'	=> 'comms_position',
			),
	'identity'	=>array(
			'name' 		=> __('Identity'),
			'type' 		=> 'select',
			'droplist'	=> 'comms_position',
			),
	'module'			=>array(
			'name' 		=>  __('Module'),
			'id'		=> 'module_id',
			'type'		=> 'relationship',
			'moreclass' => 'fixbor2',
			'lock'		=> '1',
		),
	'module_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),

);


// Panel 4
$ModuleField['field']['panel_4'] = array(
	'setup'	=> array(
			'css'	=> 'width:30%;',
			'lablewith' => '38',
			),
	'none1'	=>array(
			'type' 		=> 'not_in_data',
			),
	'none2'	=>array(
			'type' 		=> 'not_in_data',
			),
	'none3'	=>array(
			'type' 		=> 'not_in_data',
			),
	'none4'	=>array(
			'type' 		=> 'not_in_data',
			),
	'none5'	=>array(
			'type' 		=> 'not_in_data',
			),	'none'	=>array(
			'type' 		=> 'not_in_data',
			),
	'none6'	=>array(
			'type' 		=> 'not_in_data',
			'moreclass' => 'fixbor3',
			),
	'internal_notes' =>array(
			'name' 		=>  __('Detail'),
			'type' 		=> 'hidden',
			),
	'contact_address' =>array(
			'name' 		=> __('Address'),
			'type' 		=> 'hidden',
			),


);

//============ *** RELATIONSHIP *** =============//


//============ *** CUSTOM LAYOUT *** =============//
$ModuleField['custom'] = true;
// Panel custom 1
$ModuleField['field_custom']['letter'] = $ModuleField['field_custom']['fax'] = array(
	'panel_1'		=>'split',
	'code'			=>array(),
	'comms_type'	=>array(),
	'company_name'	=>array(),
	'company_id'	=>array(),
	'contact_title'	=>array(),
	'amount_sent'	=>array(),
	'mongo_id'		=>array(),
	'date_modified'	=>array(),
	'created_by'	=>array(),
	'modified_by'	=>array(),
	'contact_name'	=>array(),
	'contact_id'	=>array(),
	'last_name'		=>array(),
	'comms_date'	=>array(),

	'panel_2'		=>'split',
	'sign_off'      => array('default' => 'Regards'),
	'contact_from'	=>array(),
	'position'		=>array(),
	'salutation'	=>array(),
	'name'			=>array('name'=>__('Reference'),),
	'email_cc'		=>array('type'=>'text',),
	'include_signature'	=>array('moreclass' => 'fixbor2',),

	'panel_3'		=>'split',
	'contact_address'=>array('type' => 'text',),
	'setup_panel_3'	=> array('blocktype'=> 'address',),

	'panel_4'		=>'split',
	'phone' 		=>array('name'=> __('Phone'),'type'=>'phone',),
	'fax' 			=>array('name'=> __('Fax'),'type'=>'text',),
	'email'			=>array(),
	'job_name'		=>array('width'=>'32%','css'=> 'float:left;',),
	'job_number'	=>array(
			'width'		=> '24%',
			'css'		=> 'width:90%;float:left;padding-left:5%;padding-right: 2%;',
			),
	'job_id'		=>array(),
	'pages' 		=>array('name'=> __('Pages'),'type'=>'text',),
	'identity'		=>array(),
	'none6'			=>array(),
	'setup_panel_4'	=> array(
						'css'	=> 'width:34%;',
						'lablewith' => '38',
					),
);

$ModuleField['field_custom']['message'] = array(
	'panel_1'		=>'split',
	'setup'		=>array(
			'css'	=> 'width:100%;',
			'lablewith' => '25',
			'blockcss' => 'width: 100%',
		),
	'code'	=>array(
			'name' 		=> __('Ref no'),
			'type' 		=> 'text',
			'moreclass' => 'fixbor',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
							),
			),
	'comms_type'	=>array(
			'name' 		=>  __('Type'),
			'type' 		=> 'select',
			'droplist'	=> 'com_type',
			'width'		=> '70%',
			'css'		=> 'padding-left:3.5%;',
			'lock'		=> '1',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'5',
							'css'	=>	'width:5%;',
						),
			),
	'contact_from'	=>array(
			'name' 		=>  __('From'),
			'width'		=> '70%',
			'css'		=> 'padding-left:3.5%;',
			'lock'		=> '1',
			),
	'contact_to'	=>array(
			'name' 		=>  __('To'),
			'type'		=> 'text',
			'width'		=> '70%',
			'css'		=> 'padding-left:3.5%;',
			'lock'		=> '1',
			),
	'comms_date'	=>array(
			'name' 		=>  __('Date'),
			'css'		=> 'padding-left:3.5%;',
			'type' 		=> 'text',
			'moreclass' => '',
			'lock'		=> '1',
			),
	'message_time'		=>array(
			'name' 		=>  __('Time'),
			'type'		=> 'text',
			'width'		=> '70%',
			'css'		=> 'padding-left:3.5%;',
			'lock'		=> '1',
		),
	'module'			=>array(
			'name' 		=>  __('Module'),
			'id'		=> 'module_id',
			'type'		=> 'relationship',
			'width'		=> '70%',
			'moreclass' => 'fixbor2',
			'css'		=> 'padding-left:3.5%;',
			'lock'		=> '1',
			'morecss'	=> 'height:350px',
		),
	'module_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),

);
$ModuleField['field_custom']['note'] = array(
	'panel_1'		=>'split',
	'setup'		=>array(
			'css'	=> 'width:100%;',
			'lablewith' => '25',
			'blockcss' => 'width: 100%',
		),
	'code'	=>array(
			'name' 		=> __('Ref no'),
			'type' 		=> 'text',
			'moreclass' => 'fixbor',
			'listview'	=>	array(
								'order'	=>	'1',
								'with'	=>	'100',
								'align'	=>	'center',
								'css'	=>	'width:5%;',
							),
			),
	'comms_type'	=>array(
			'name' 		=>  __('Type'),
			'type' 		=> 'select',
			'droplist'	=> 'com_type',
			'width'		=> '70%',
			'css'		=> 'padding-left:3.5%;',
			'lock'		=> '1',
			),
	'contact_from'	=>array(
			'name' 		=>  __('By'),
			'width'		=> '70%',
			'css'		=> 'padding-left:3.5%;',
			'type'		=> 'text',
			'lock'		=> '0',
			),
	'company_name'	=>array(
			'name' 		=>  __('Company'),
			'type' 		=> 'relationship',
			'cls'		=> 'companies',
			'id'		=> 'company_id',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'15',
							'css'	=>	'width:15%;',
						),
			),
	'company_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'contact_name'	=>array(
			'name' 		=>  __('Contact'),
			'type' 		=> 'relationship',
			'cls'		=> 'contacts',
			'id'		=> 'contact_id',
			'listview'	=>	array(
							'order'	=>	'1',
							'with'	=>	'15',
							'css'	=>	'width:15%;',
						),
			),
	'contact_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),
	'comms_date'	=>array(
			'name' 		=>  __('Date'),
			'css'		=> 'padding-left:3.5%;',
			'type' 		=> 'text',
			'moreclass' => '',
			'lock'		=> '1',
			),
	'comms_time'		=>array(
			'name' 		=>  __('Time'),
			'type'		=> 'text',
			'width'		=> '70%',
			'moreclass' => 'fixbor2',
			'css'		=> 'padding-left:3.5%;',
			'lock'		=> '1',
			'morecss'	=> 'height:271px',
		),
	'mongo_id'	=>array(
			'type' 		=> 'id',
			'element_input' => ' class="jthidden"',
			),

);
$CommunicationField = $ModuleField;