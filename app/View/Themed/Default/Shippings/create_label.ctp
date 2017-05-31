<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title></title>
<style type="text/css" media="all">
	body{
		width: 100%;
		margin: auto;
		font-size: 12px;
	}
	div.avoid {
		/*page-break-inside: avoid;*/
		display:inline-block;
		page-break-after: auto;
		margin: 5px;
	}
	table {
		width: 98%;
		border-bottom: dashed 1px;
		height: 95px;
	}
	/*table:nth-child(4n) {
		border-bottom: none;
	}*/
	table tbody img.logo {
		width: 110px;
	}
	table tbody td.info {
		font-size: 9px;
		width: 70%;
	}
	table tbody td.other {
		font-size: 10px;
	}
	table tbody td.ship-to {
		padding-left: 15px;
		padding-bottom: 35px;
		font-size: 110%;
		width:30%;
		text-transform: uppercase;
		font-weight: 900;
	}
	table tbody span {
		display: block;
	}
	table tbody span.package {
		font-weight: 900;
	}
</style>
</head>
<body>
	<?php echo $arr_data['html'];  ?>
</body>
</html>