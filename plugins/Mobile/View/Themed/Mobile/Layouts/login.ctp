<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>JobTraq</title>
	<meta name="format-detection" content="telephone=no" />
    <!-- WARNING: for iOS 7, remove the width=device-width and height=device-height attributes. See https://issues.apache.org/jira/browse/CB-4323 -->
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
    <title>Jobtraq</title>
    <link rel="stylesheet" type="text/css" href="http://code.jquery.com/mobile/1.4.1/jquery.mobile-1.4.1.min.css" />
	<?php
		echo $this->Html->css('main');
		echo $this->Html->css('jquery-ui-1.10.3.custom.min');
		echo $this->Html->script('jquery-1.10.2.min'); // resize, cookie, common
        echo $this->Html->script('jquery.mobile-1.4.2.min');
	?>
</head>

<body>
	<div class="app">
		<div data-role="page">
			<?php echo $this->fetch('content'); ?>
			<div data-role="footer">
		        <p>&copy 2009 - 2013 Anvy Digital Imaging, All Rights Reserved.</p>
		    </div>
		</div>
</div>
</body>
</html>
