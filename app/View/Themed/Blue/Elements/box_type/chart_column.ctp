<?php 
	echo $this->Html->script('highcharts');
	
	$arr = array();
	if(isset($chart_data[$blockname]))
		$arr = $chart_data[$blockname];
		
	//danh sach truc x
	if(isset($arr['cate_x'])){
		$categories = $arr['cate_x'];
	}else{
		$categories = array(1,2,3,4,5,6,7,8,9,10,11,12);
	}
	
	$name = $title = '';
	if (isset($arr['chart_code']) && $arr['chart_code'] != '') {
		if($arr['chart_code'] == '4') {
			$name = $title = 'Doanh thu';
		}elseif ($arr['chart_code']=='2') {
			$name = $title ='Jobs';
		}elseif ($arr['chart_code']=='3') {
			$name = $title ='Loi Nhuan';
		}else {$name = '{series.name}:';}
	}
?>

 <?php //pr($arr);die;?>
 
 		 
<script type="text/javascript">
$(function () {
	  $('<?php echo '#'.$blockname.'_hightchart'; ?>').highcharts({
         chart: {
        	 type: 'column'
            // plotBackgroundColor: 'red',
            // plotBorderWidth: 22,
            // plotShadow: 10
         },
         title: {
				text: '<?php if(isset($arr['title'])) echo $arr['title']; ?>',
				
		},
         subtitle: {
             text: '<?php if(isset($arr['subtitle'])) echo $arr['subtitle']; ?>'
         },
         xAxis: {
        	 <?php if(isset($arr['cate_x']['min'])) echo "min: ".$arr['cate_x']['min'].","; ?>
             <?php if(isset($arr['cate_x']['max'])) echo "min: ".$arr['cate_x']['max'].","; ?>
             categories: [<?php echo join($categories, ',') ?>],
             labels: {
                 //rotation: -45,
                 style: {
                     fontSize: '13px',
                     fontFamily: 'Verdana, sans-serif'
                 }
             }
         },
         yAxis: {
           	<?php if(isset($arr['cate_y']['min'])) echo "min: ".$arr['cate_y']['min'].","; ?>
           	<?php if(isset($arr['cate_y']['max'])) echo "min: ".$arr['cate_y']['max'].","; ?>
             title: {
                 text: '<?php if(isset($arr['cate_y']['title']['text'])) {echo $arr['cate_y']['title']['text']; } else { echo $title; }?>'
               	 
             }
         },
         tooltip: {
             headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
             pointFormat: '<tr><td style="color:{series.color};padding:0"><?php echo $name;?> </td>' +
                 '<td style="padding:0"><b>{point.y:.1f} <?php if(isset($arr['tooltip']['valueSuffix'])) echo $arr['tooltip']['valueSuffix']; ?></b></td></tr>',
             footerFormat: '</table>',
             shared: true,
             useHTML: true
         },
         plotOptions: {
             column: {
            	 dataLabels: {
                     enabled: true,
                    // rotation: -30,
                     color: '#852020',
                     align: 'center',
                 },
                 pointPadding: 0.2,
                 borderWidth: 0
             }
         },
         legend: {
            layout: 'vertical',
             align: 'right',
             verticalAlign: 'middle',
             borderWidth: 0
         },
         series: [
		//doanh so cac nhan vien, user
         <?php if (isset($arr['chart_code']) && ($arr['chart_code'] == 1 || $arr['chart_code'] == 5)) {?>         
         <?php $i=0; foreach($arr['series'] as $uid=>$arr_series){?>
         {
        	 <?php if(isset($arr_series['color'])) echo "type: '".$arr_series['line']."',"; ?>
			 <?php if(isset($arr_series['color'])) echo "color: '".$arr_series['color']."',"; ?>
             name: '<?php echo $arr_series['name']; ?>',
             data: [<?php echo join($arr_series['data'], ',') ?>]
 
         }
         <?php if($i<count($arr['series'])-1) echo ','; $i++;} ?>
        //so luong jobs, loi nhuan, doanh thu
         <?php } elseif(isset($arr['chart_code']) && ($arr['chart_code'] == 2 || $arr['chart_code'] == 4 || $arr['chart_code'] == 3)) {?>
         {
         		<?php if(isset($arr['color'])) echo "type: '".$arr['line']."',"; ?>
			 	<?php if(isset($arr['color'])) echo "color: '".$arr['color']."',"; ?>
           		name: '<?php echo $name; ?>',
            	data: [<?php echo join($arr['data'], ',') ?>]
         }
         <?php }?>
         ]
     });
 });
    

</script> 
<div id="<?php echo $blockname.'_hightchart'; ?>" ></div>

 
