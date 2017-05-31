<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>JobTraq</title>
	<link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="icon">
	<link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="shortcut icon">
	<meta charset="utf-8" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height" />
    <!-- WARNING: for iOS 7, remove the width=device-width and height=device-height attributes. See https://issues.apache.org/jira/browse/CB-4323 -->
	<?php
        #default css
        echo $this->Minify->css(array('reset','main','jquery-ui-1.10.3.custom.min','jquery.mobile-1.4.3.min.css'));
    ?>
    <?php
        #default js
        echo $this->Minify->script(array('jquery-1.10.2.min.js','jquery-ui-1.10.3.custom.min'));
    ?>
    <script type="text/javascript">
        $(document).on("mobileinit", function(){
            $.mobile.changePage.defaults.changeHash = true;
        });
    </script>
    <?php
        echo $this->Minify->script(array('jquery.mobile-1.4.3.min','main'));
        #must be list here to work
    ?>
    <style type="text/css">
        #ui-datepicker-div{
            z-index: 1000 !important;
        }
    </style>
</head>

<body id="no_bg">
    <div class="app reset_app">
        <div id="main_page" data-role="page" class="pages_content reset_top">
            <?php echo $this->element("header"); ?>
            <div class="contain">
                <?php echo $this->fetch('content'); ?>
            </div>
            <div id="main_footer" data-role="footer" class="ui-footer ui-bar-inherit">
                <p>&copy 2009 - 2013 Anvy Digital Imaging, All Rights Reserved.</p>
            </div>
		</div>
	</div>
    <?php
        #View/Themed/Mobile/Elements/js/js.ctp
        echo $this->element("js/js");
        #View/Themed/Mobile/{$controller}/js.ctp
        echo @$this->element('../'.ucfirst($controller).'/js');
        
		echo $this->Js->writeBuffer();
	?>
</body>
</html>