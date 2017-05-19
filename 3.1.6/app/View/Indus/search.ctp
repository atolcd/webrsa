<?php
	$availableDomains = WebrsaTranslator::domains();
	$domain = isset( $availableDomains[0] ) ? $availableDomains[0] : $controller;
?>
<?php $this->start( 'custom_search_filters' );?>
<?php
	echo '<fieldset><legend>' . __m( 'Infofinanciere.search' ) . '</legend>'
		. $this->Default3->subform(
			array(
				'Search.Infofinanciere.natpfcre' => array( 'empty' => true ),
				'Search.Dossier.typeparte' => array( 'empty' => true ),
				'Search.Infofinanciere.compare' => array( 'empty' => true ),
				'Search.Infofinanciere.mtmoucompta',
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		)
		. '</fieldset>'
	;
?>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'modelName' => 'Dossier',
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);
?>