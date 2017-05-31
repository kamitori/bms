<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>JobTraq</title>
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('reset');
		echo $this->Html->css('style');
		echo $this->Html->css('jt_screen');
	?>
</head>

<body >
	<?php echo $this->fetch('content'); ?>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
