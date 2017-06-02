<?php $this->start( 'custom_search_filters' );?>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'modelName' => 'Personne',
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv_validees' )
		)
	);
?>