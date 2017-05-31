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
?>

 <?php //pr($arr);die;?>
 
 		 
<script type="text/javascript">
  
$(function () {
	$('<?php echo '#'.$blockname.'_hightchart'; ?>').highcharts({
        chart: {
          // plotBackgroundColor: 'red',
          // plotBorderWidth: 22,
           // plotShadow: 10
        },
        subtitle: {
        	text: '<?php if(isset($arr['subtitle'])) echo $arr['subtitle']; ?>',
        },
        title: {
        	text: '<?php if(isset($arr['title'])) echo $arr['title']; ?>',
        },
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 1000
        },
        series: [{
            type: 'pie',
            name: 'Test',
            data: [
					<?php if(isset($arr['series'])) {?>
						<?php if($arr['code'] == 1 || $arr['code'] == 5){ ?>
		 					<?php $m=0;
							foreach($arr['series'] as $key=>$value){?>
								<?php if(isset($arr['datacheck']) && $arr['datacheck']==$key){?>
									{
					                    name: '<?php if(isset($value['name'])){echo $value['name'];} ?>',
					                    y: <?php   
					                    	foreach ($value['data'] as $k => $v){ 
					                    		$data_pie = 0;  $data_pie+=$v;
					                    	} 
					                    	echo $data_pie;
					                    ?>,
					                    sliced: true,
					                    selected: true
					                }
								<?php }else{?>
								
									['<?php echo $value['name'];?>',  <?php foreach ($value['data'] as $k => $v){ 
					                    		$data_pie = 0;  $data_pie+=$v;
					                    	} echo $data_pie;
					                ?>]
								<?php }?>
							<?php if($m<count($arr['series'])-1) echo ','; $m++;}?>
						<?php }elseif($arr['code'] == 2 || $arr['code'] == 4 || $arr['code'] == 3) {?>
							<?php	$m=0; foreach($arr['series'] as $key=>$value){   if ($key == 'user_name') continue;           ?>
	               			['<?php echo $key;?>',  <?php
	            				echo $value;
	        				?>]
	               			<?php if($m < count($arr['series']) - 1) echo ','; $m++;}?>
						<?php }?>
               		<?php }else {?>
               			<?php	$m=0; foreach($arr as $key=>$value){   if ($key == 'user_name') continue;           ?>
               			['<?php echo $key;?>',  <?php
            				echo $value;
        				?>]
               			<?php if($m < count($arr) - 1) echo ','; $m++;}?>
               		<?php }?>
                
                
            ]
        }]
    });
});
    

</script> 
<div id="<?php echo $blockname.'_hightchart'; ?>" ></div>

 
