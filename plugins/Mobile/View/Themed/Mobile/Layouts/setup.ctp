<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <?php echo $this->Html->charset(); ?>
    <title>JobTraq</title>
    <link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="icon">
    <link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <?php
        echo $this->Html->css('reset');
        echo $this->Html->css('jt_vunguyen');
        echo $this->Html->css('style');

		echo $this->Html->css('jquery-ui-1.10.3.custom.min');

        // jQuery
        echo $this->Html->script('jquery-1.9.1.min'); // resize, cookie, common

		//kendo plugin
        // echo $this->Html->css('kendo/kendo.default.min');
        echo $this->Html->css('kendo/kendo.common.min');
        echo $this->Html->css('kendo/kendo.anvy.min');
		// echo $this->Html->script('kendo/kendo.all.min');
        echo $this->Html->script('jquery.combobox');


        echo $this->Html->script('jquery-ui-1.10.3.custom.min');

		//Scrollbar
		echo $this->Html->css('jquery.mCustomScrollbar');
		echo $this->Html->script('jquery.mCustomScrollbar.concat.min');
        echo $this->Html->script('ckeditor/ckeditor');

    ?>
</head>

<body>
    <div id="wrapper">
		<?php echo $this->fetch('content'); ?>
    </div>
    <?php
        echo $this->Html->script('main.js');
        echo $this->Html->script('kendo/kendo.web.min');
		echo $this->Js->writeBuffer();
		echo $this->Html->script('jquery.mCustomScrollbar.concat.min');
    ?>
</body>
<?php ?>
</html>