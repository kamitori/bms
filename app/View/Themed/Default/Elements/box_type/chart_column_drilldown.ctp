<?php 
	//pr($data_chart);
 	echo $this->Html->script('highcharts');
	$arr = array();
	if(isset($chart_data[$blockname]))
		$arr = $chart_data[$blockname];

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
 <?php //pr($arr); die;?>
<script src="http://code.highcharts.com/modules/data.js"></script>
<script src="http://code.highcharts.com/modules/drilldown.js"></script>

<div id="<?php echo $blockname.'_hightchart'; ?>" ></div>
<!-- Data from www.netmarketshare.com. Select Browsers => Desktop share by version. Download as tsv. -->
<pre id="tsv" style="display:none ">
<?php 

	foreach($arr as $key => $value){
		//$name = '';
		$name = end($value);//echo $name;//die;
		$name = str_replace('-', ' ', $name);
		if ($name == '' || empty($name)){
			$name = 'No Name '.$key;
		}		
		foreach ($value as $k => $v) {
			if ($k == 'user_name') continue;
			echo "\n".($name.' '.$k.'	'.$v.'CAD');
		}
	
	}	//die;
?>
</pre>
 <script type="text/javascript">
 $(function () {

	    Highcharts.data({
	        csv: document.getElementById('tsv').innerHTML,
	        //itemDelimiter: '\t',
	        parsed: function (columns) {

	            var brands = {},
	                brandsData = [],
	                versions = {},
	                drilldownSeries = [];
	            
	            // Parse percentage strings
	            columns[1] = $.map(columns[1], function (value) {
	                if (value.indexOf('CAD') === value.length - 3) {
	                    value = parseFloat(value);
	                }
	                
	                return value;
	            });
	            $.each(columns[0], function (i, name) {
 	                var brand,
	                    version;

	                if (i > 0) {
						version = name.substr(name.length-8,name.length);
	                    brand = name.replace(version, '');
	                    // Create the main data
	                    if (!brands[brand]) {
	                        brands[brand] = columns[1][i];
	                    } else {
	                        brands[brand] += columns[1][i];
	                    }
	                    // Create the version data
	                    if (version !== null) {
	                        if (!versions[brand]) {
	                            versions[brand] = [];
	                        }
	                        versions[brand].push([version, columns[1][i]]);
	                    }
	                    //console.log(versions);
	                }
	                
	            });//console.log(versions);

	            $.each(brands, function (name, y) {
	                brandsData.push({ 
	                    name: name, 
	                    y: y,
	                    drilldown: versions[name] ? name : null
	                });
	            });
	            $.each(versions, function (key, value) {
	                drilldownSeries.push({
	                    name: key,
	                    id: key,
	                    data: value
	                });
	            });

	            // Create the chart
				$('<?php echo '#'.$blockname.'_hightchart'; ?>').highcharts({
	                chart: {
	                    type: 'column'
	                },
	                title: {
	                	text: '<?php if(isset($data_chart['title'])) echo $data_chart['title']; ?>',
	                },
	                subtitle: {
	                	text: '<?php if(isset($data_chart['subtitle'])) echo $data_chart['subtitle']; ?>',
	                },
	                xAxis: {
	                    type: 'category'
	                },
	                yAxis: {
	                    title: {
	                        text: '<?php if(isset($data_chart['cate_y']['title']['text'])) {echo $data_chart['cate_y']['title']['text']; } else { echo $title; }?>'
	                    }
	                },
	                legend: {
	                    enabled: false
	                },
	                plotOptions: {
	                    series: {
	                        borderWidth: 0,
	                        dataLabels: {
	                            enabled: true,
	                            format: '{point.y:.1f}'
	                        }
	                    }
	                },

	                tooltip: {
	                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
	                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}CAD</b> of total<br/>'
	                }, 

	                series: [{
	                    name: 'User',
	                    colorByPoint: true,
	                    data: brandsData
	                }],
	                drilldown: {
	                    series: drilldownSeries
	                }
	            })

	        }
	    });
	});
</script>

    