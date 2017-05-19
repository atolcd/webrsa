<?php
	$paramDate = array(
		'domain' => 'entretiens',
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 1
	);
?>
<?php $this->start( 'custom_search_filters' );?>
<fieldset>
	<legend>Filtrer par Entretiens</legend>
	<?php
		echo $this->Default2->subform(
			array(
				'Search.Entretien.arevoirle' => array( 'label' => __d( 'entretien', 'Entretien.arevoirle' ), 'type' => 'date', 'dateFormat' => 'MY', 'empty' => true, 'minYear' => date( 'Y' ) - 2, 'maxYear' => date( 'Y' ) + 2 ),
				'Search.Entretien.structurereferente_id' => array( 'label' => __d( 'entretien', 'Entretien.structurereferente_id' ), 'empty' => true, 'options' => $options['PersonneReferent']['structurereferente_id'] ),
				'Search.Entretien.referent_id' => array( 'label' => __d( 'entretien', 'Entretien.referent_id' ), 'empty' => true, 'options' => $options['PersonneReferent']['referent_id']  )
			),
			array(
				'options' => $options
			)
		);

		echo $this->SearchForm->dateRange( 'Search.Entretien.dateentretien', $paramDate );

		echo $this->Observer->dependantSelect( array(
			'Search.Entretien.structurereferente_id' => 'Search.Entretien.referent_id'
		) );
	?>
</fieldset>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);
?>