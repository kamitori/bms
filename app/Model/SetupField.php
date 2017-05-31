<?php
$ModuleField = array();
$ModuleField = array(
	'module_name' 	=> __('Setup'),
	'module_label' 	=> __('Setups'),
	'colection' 	=> 'tb_setup',
);


//============= *** FIELDS *** =============//
$ModuleField['field'] = array();

//============ *** RELATIONSHIP *** =============//

$ModuleField['relationship'] =  array();




//====== General =======//
$ModuleField['relationship']['general']['name'] = 'General';
$ModuleField['relationship']['general']['block']['general'] = array(
	'title'	=>__('Specify values for lists used in system'),
	'type'	=>'listview_box',
	'css'	=>'width:98%;',
	'height' => '480',
	'add'	=> __('Add more'),
	'delete' => '6',
	'field'=> array(),	
);


//====== Droplist =======//
$ModuleField['relationship']['list_and_menu']['name'] = 'Lists & Menus';
$ModuleField['relationship']['list_and_menu']['block']['list_and_menu'] = array(
);



//====== Mesages =======//
$ModuleField['relationship']['list_message']['name'] = 'System Message';
$ModuleField['relationship']['list_message']['block']['system_message'] = array(
);


//====== Auto Process =======//
$ModuleField['relationship']['auto_process']['name'] = 'Auto Process';
$ModuleField['relationship']['auto_process']['block']['auto_process'] = array(
);


//====== Assets =======//
$ModuleField['relationship']['equipments']['name'] = 'Assets';
$ModuleField['relationship']['equipments']['block']['equipments'] = array(
);


//====== Province =======//
$ModuleField['relationship']['list_country']['name'] = 'Province';
$ModuleField['relationship']['list_country']['block']['provinces'] = array(
);


//====== Permissions =======//
$ModuleField['relationship']['privileges']['name'] = 'Permissions';
$ModuleField['relationship']['privileges']['block']['privileges'] = array(
);



//====== Roles =======//
$ModuleField['relationship']['roles']['name'] = 'Roles';
$ModuleField['relationship']['roles']['block']['roles'] = array(
);


//====== User Roles =======//
$ModuleField['relationship']['user_roles']['name'] = 'User Roles';
$ModuleField['relationship']['user_roles']['block']['user_roles'] = array(
);


//====== System Email =======//
$ModuleField['relationship']['system_email']['name'] = 'System Email';
$ModuleField['relationship']['system_email']['block']['system_email'] = array(
);



//====== Hook Setting =======//
$ModuleField['relationship']['hook_setting']['name'] = 'Hook Setting';
$ModuleField['relationship']['hook_setting']['block']['hook_setting'] = array(
);



//====== Language =======//
$ModuleField['relationship']['language']['name'] = 'Language';
$ModuleField['relationship']['language']['block']['language'] = array(
);


//====== Support =======//
$ModuleField['relationship']['support']['name'] = 'Support';
$ModuleField['relationship']['support']['block']['support'] = array(
);



//====== Module studio =======//
$ModuleField['relationship']['studio']['name'] = 'Module studio';
$ModuleField['relationship']['studio']['block']['studio'] = array(
);


//====== Administrator =======//
$ModuleField['relationship']['administrator']['name'] = 'Administrator';
$ModuleField['relationship']['administrator']['block']['administrator'] = array(
);




$SetupField = $ModuleField;