<?php
	$data = array();
	foreach( $results as $key => $value){
		$data[] = array( 'value' => $key, 'label' => $value );
} ?>
<?php echo json_encode($data); ?>