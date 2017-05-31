<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <?php echo $this->Html->charset(); ?>
    <title>JobTraq</title>
    <link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="icon">
    <link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <?php
        echo $this->Minify->css(array('reset','style','jt_vunguyen','jobtraq_chat','jquery-ui-1.10.3.custom.min','kendo/kendo.common.min','kendo/kendo.anvy.min','jquery.mCustomScrollbar'));
        echo $this->Minify->script(array('jquery-1.11.1.min','jquery-ui-1.10.3.custom.min','jquery.combobox','jquery.mousewheel.min','jquery.mCustomScrollbar.concat.min','ckeditor/ckeditor'));
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
    ?>
</body>
<?php ?>
</html>