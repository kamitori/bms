<?php
	//for bug
	//pr($blockname);
	//pr($arr_subsetting);
	//pr($subdatas);
	//$tracking_no = '212121';

  //  echo $subdatas['tracking_detail']['tracking_no_detail'];die;
//echo $tracking_url;die;
$no = isset($subdatas['tracking_detail']['tracking_no'])?$subdatas['tracking_detail']['tracking_no']:'';
$no1= isset($tracking_url_iframe)?$tracking_url_iframe:'';
$url =  str_replace("NUMTRACKING",$no,$no1);



?>




<iframe style="width:100%;height:100%;" src="<?php

if(isset($url)) echo $url;

 ?>"></iframe>