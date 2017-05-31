<!DOCTYPE html>
<html lang="en">
<head>
    <head>
    <?php echo $this->Html->charset(); ?>
    <title>JobTraq</title>
    <link href="/favicon.ico" type="image/x-icon" rel="icon">
    <link href="/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <?php
        echo $this->Html->css('reset');
        echo $this->Html->css('style');

        echo $this->Html->css('jt_screen');
        echo $this->Html->css('jt_vunguyen');
    ?>

    <?php
        // echo $this->Html->css('jquery-ui-1.10.3.custom.min');
        echo $this->Html->css('dhtmlxscheduler');

        echo $this->Html->script('jquery-1.9.1.min'); // resize, cookie, common
        echo $this->Html->script('jquery-ui-1.10.3.custom.min');
        echo $this->Html->script('dhtmlxscheduler');
        echo $this->Html->script('dhtmlxscheduler_limit');
        echo $this->Html->script('dhtmlxscheduler_expand');
        echo $this->Html->script('dhtmlxscheduler_active_links');
        echo $this->Html->script('dhtmlxscheduler_minical');
        echo $this->Html->script('dhtmlxscheduler_tooltip');

        //kendo plugin
        // echo $this->Html->css('kendo/kendo.default.min');
        echo $this->Html->css('kendo/kendo.common.min');
        echo $this->Html->css('kendo/kendo.anvy.min');
        // echo $this->Html->script('kendo/kendo.all.min');

    ?>
</head>

</head>
<body>
    <?php echo $this->element('header'); ?>
    <?php echo $this->fetch('content'); ?>
    <?php if(!isset($set_footer))
            $set_footer = 'footer';
          echo $this->element($set_footer);
    ?>
    <?php
        echo $this->element('loading');
        echo $this->element('window');
        echo $this->element('sql_dump');
        echo $this->Html->script('main.js');
        // echo $this->Html->script('jquery.fancybox-1.3.1');

        echo $this->Html->script('kendo/kendo.web');
        echo $this->Js->writeBuffer();
    ?>
</body>
</html>