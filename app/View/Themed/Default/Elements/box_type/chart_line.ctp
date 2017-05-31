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
 <?php //pr($arr);?>
 		 
<script type="text/javascript">
  
	$(function () {
		 
        $('<?php echo '#'.$blockname.'_hightchart'; ?>').highcharts({
		
            title: {
				text: '<?php if(isset($arr['title'])) echo $arr['title']; ?>',
                x: -20 //center
            },
            subtitle: {
            	text: '<?php if(isset($arr['subtitle'])) echo $arr['subtitle']; ?>',
                x: -20
            },
            
            xAxis: {
            	<?php if(isset($arr['cate_x']['min'])) echo "min: ".$arr['cate_x']['min'].","; ?>
                <?php if(isset($arr['cate_x']['max'])) echo "min: ".$arr['cate_x']['max'].","; ?>
                categories: [<?php echo join($categories, ',') ?>],
                labels: {
                   // rotation: -45,
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
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: '<?php if(isset($arr['tooltip']['valueSuffix'])) echo $arr['tooltip']['valueSuffix']; ?>'
            },
            plotOptions: {
                line: {
                	 dataLabels: {
                         enabled: true,
                         color: '#852020',
                         align: 'center',
                     },
                    pointPadding: 0.2,
                    borderWidth: 10
                },
           
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },            
            series: [		
                     	//type: doanh so cac nhan vien, user  
	                    <?php if(isset($arr['chart_code']) && ($arr['chart_code'] == 1) || $arr['chart_code'] == 5){ ?>
               			<?php $i=0; foreach($arr['series'] as $uid=>$arr_series){?>
	                     {
	                    	 <?php if(isset($arr_series['color'])) echo "type: '".$arr_series['line']."',"; ?>
	            			 <?php if(isset($arr_series['color'])) echo "color: '".$arr_series['color']."',"; ?>
	                         name: '<?php echo $arr_series['name']; ?>',
	                         data: [<?php echo join($arr_series['data'], ',') ?>]
	             
	                     }
	                     <?php if($i<count($arr['series'])-1) echo ','; $i++;} ?>

	                    //type: so luong jobs, loi nhuan, doanh thu
	                  	 <?php } elseif(isset($arr['chart_code']) && ($arr['chart_code'] == 2) || $arr['chart_code'] == 4 || $arr['chart_code'] == 3) {?>
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

 
