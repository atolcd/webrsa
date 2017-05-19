<?php $this->start( 'custom_search_filters' );?>
<?php
	echo '<fieldset><legend>' . __m( 'Orientstruct.search' ) . '</legend>';
	echo $this->SearchForm->dateRange( 'Search.NvOrientstruct.date_valid', array( 'legend' => __m( 'Search.NvOrientstruct.date_valid' ) ) );
	echo $this->Form->input( 'Search.Orientstruct.typeorient_id', array( 'label' => __m( 'Search.Orientstruct.typeorient_id' ), 'type' => 'select', 'empty' => true, 'options' => $options['Orientstruct']['typeorient_id'] ) );
	echo $this->Allocataires->communautesrSelect( 'NvOrientstruct', array( 'options' => array( 'Search' => $options ) ) );
	echo $this->Form->input( 'Search.NvOrientstruct.structurereferente_id', array( 'label' => __m( 'Search.NvOrientstruct.structurereferente_id' ), 'type' => 'select', 'empty' => true, 'options' => $options['Orientstruct']['structurereferente_id'] ) );
	echo '</fieldset>';
?>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'modelName' => 'Dossier'
		)
	);

	// Si l'utilisateur connecté a accès aux projets de villes communautaires
	if( true === Hash::check($options, 'NvOrientstruct.communautesr_id') ) {
		echo $this->Observer->disableFieldsOnValue(
			'Search.Orientstruct.typeorient_id',
			'Search.NvOrientstruct.communautesr_id',
			array( '', null ),
			false
		);

		echo $this->Observer->disableFieldsOnValue(
			'Search.NvOrientstruct.communautesr_id',
			'Search.Orientstruct.typeorient_id',
			array( '', null ),
			false
		);
	}

	echo $this->Observer->dependantSelect(
		array(
			'Search.Orientstruct.typeorient_id' => 'Search.NvOrientstruct.structurereferente_id'
		)
	);

	echo $this->Allocataires->communautesrScript(
		'NvOrientstruct',
		array(
			'options' => array( 'Search' => $options )
		)
	);
?>