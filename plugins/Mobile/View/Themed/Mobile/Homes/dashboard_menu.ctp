<div class="menu">
	<div class="bg_menu bg_menu_res">
		<ul class="menu_control float_left">
			<li><a href="javascript:void(0)"  onclick="filter_all_by_my_name()"><?php echo translate('Filter all by my name'); ?></a></li>
			<li><a href="javascript:void(0)" onclick="all_update_data_null()"><?php echo translate('Clear all filters'); ?></a></li>
		</ul>
		<ul class="menu_control2 float_right">
	        <li><a href="<?php echo URL; ?>/homes/dashboard/administration" <?php if( $type_auth == 'administration' ){ ?>class="active"<?php } ?>>Admininistration</a></li>
	        <li><a href="<?php echo URL; ?>/homes/dashboard/production" <?php if( $type_auth == 'production' ){ ?>class="active"<?php } ?>>Production</a></li>
	    </ul>
	</div>
</div>
<style type="text/css">
.input-search-listbox{
	margin: 0px 17px 0px 0px;  background: #636363;  border-bottom: solid #636363;  height: 75%;  color: #fff;  line-height: 0%;
}
.combobox{
	position:relative; display:-moz-inline-box; display:inline-block;float: left;
}
.combobox_button{
	cursor:pointer;position:absolute; height:17px; width:17px; top:0; right: -12px;
}
.combobox_arrow{
	border-bottom: #636363;margin-left: -50%;margin-top: 5%;
}
.combobox_selector{
	width: 124px; position: absolute; left: 0px; top: 17px; display: none;
}
.div_updated_data{
	height:186px
}
.div_updated_data li.hg_padd, ul.bg li, ul.bg li label{
	cursor:pointer;
}

.div_updated_data ul:hover{
	background: #cdcdcd;
}

.input_w2 {
	color: #000;
}
</style>