<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Quotation</title>
</head>
<body style="margin:0; padding:0; background:#fff;">
	<iframe id="printf" src="<?php echo URL;?>/upload/<?php echo $file_name;?>.pdf" style="width:100%; height:100%;background:#fff; display:table;"></iframe>
    <script type="application/javascript">
		document.getElementById('printf').onload = setTimeout( printWindow, 5500 );
		// implemented in the HTML that is loaded in 'fancy_frame'
		function printWindow()
		{
			window.print();
		}
	</script>
</body>
</html>

