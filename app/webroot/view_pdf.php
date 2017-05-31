<html>
<head>	
	<script src="js/opensave/opensave.js" type="text/javascript"></script>	
</head>
<body>

<div style="width:100%; height:100%;">
<embed src="/upload/<?php echo $_GET['pdf']; ?>" width="100%" height="100%" alt="pdf" pluginspage="http://www.adobe.com/products/acrobat/readstep2.html">	
</div>
<div style="position:absolute; top:5%; right:10%;"><button>	
	<div id="savePDFButton"> Save PDF place holder</div>
</button></div>

<script language="javascript" id="thisScript">

opensave.make({ 				
				width: 		88,
				height: 	111,
				image_up: 	"img/floppy.png",
				image_down: "img/floppy.png",
				image_over: "img/floppy.png",
				filename: 	"<?php echo $_GET['pdf']; ?>", 
				url: 		"/upload/<?php echo $_GET['pdf']; ?>",
				buttonDiv: 	"savePDFButton"
				}
			);


</script>

</body>
</html>