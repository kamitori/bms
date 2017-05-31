<div data-role="header" data-position="fixed" data-id="persistent" id="main_header">
	<!-- insert Minh Hoang -->
	<div class="infor_header">
		<a href="#myPanel" id="openPanel"  class="icon_head list"></a>
		<div class="title" style="font-weight: 700;"><?php echo $controller; ?></div>
		<a href="<?php echo M_URL.'/users/logout/' ?>" data-ajax="false" title="Logout" class="log right_a"></a>
	</div>
	<p class="clear"></p>

	<div data-role="panel" data-display="overlay" id="myPanel" data-theme="b">
	    <div data-role="main" class="ui-content reset_ul">
	        <div class="title_module_left">Jobtraq - Module</div>
	<?php
		foreach($arr_menu as $menu){
	?>
		<h2 class="title_left"><?php echo $menu['name'] ?></h2>
		<ul data-role="listview" data-theme="b" class="list_cus">
	<?php
		unset($menu['name']);
		foreach($menu as $controller_name=>$sub_menu){
			if($sub_menu['inactive']==1) continue;
	?>
			<li class="header_menu_li">
	            <a data-ajax="false" <?php if($sub_menu['name']=='Task') echo 'href="'.M_URL.'/'.$controller_name.'/entry"'; else echo 'href="#"'; ?> ><?php echo $sub_menu['name']; ?></a>
	            <span class="number"><?php echo $sub_menu['number'] ?></span>
	        </li>
	<?php
			}
	?>
		</ul>
	<?php
		}
	?>
	        <br>
	    </div>
	</div>
</div>
