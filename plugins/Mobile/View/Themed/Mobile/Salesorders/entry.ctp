


<?php
	pr($name);
?>
<?php if(!$request->is('ajax')) echo $this->element('../'.$name.'/common');?>