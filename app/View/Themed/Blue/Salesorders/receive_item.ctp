<?php echo $this->element('../' . $name . '/tab_option'); ?>
<form method="POST" enctype="multipart/form-data" id="form_shipped" name="form_shipped" >
<?php
		foreach($arr_settings['relationship']['part_invoice']['block'] as $key => $arr_val){
			echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));

		}
?>
