<?php $this->start( 'custom_search_filters' );?>
<fieldset>
	<legend>Recherche de CER</legend>
	<?php
		$paramDate = array(
			'domain' => null,
			'minYear_from' => '2009',
			'maxYear_from' => date( 'Y' ) + 1,
			'minYear_to' => '2009',
			'maxYear_to' => date( 'Y' ) + 4,
			'dateFormat' => 'DMY'
		);
		echo $this->Allocataires->SearchForm->dateRange( 'Search.Contratinsertion.created', $paramDate );

		echo $this->Allocataires->communautesr( 'Contratinsertion', array( 'options' => array( 'Search' => $options ), 'hide' => false ) );
		echo $this->Default3->subform(
			array(
				'Search.Contratinsertion.structurereferente_id' => array( 'empty' => true, 'required' => false ),
				'Search.Contratinsertion.referent_id' => array( 'empty' => true ),
				'Search.Contratinsertion.decision_ci' => array( 'empty' => true, 'required' => false ),
				'Search.Contratinsertion.datevalidation_ci' => $paramDate + array( 'empty' => true, 'required' => false ),
				'Search.Contratinsertion.forme_ci' => array( 'type' => 'radio', 'required' => false, 'legend' => __m( 'Search.Contratinsertion.forme_ci' ) )
			),
			array( 'options' => array( 'Search' => $options ) )
		);
	?>
</fieldset>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'modelName' => 'Contratinsertion',
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv_valides' )
		)
	);

	echo $this->Observer->dependantSelect(
		array(
			'Search.Contratinsertion.structurereferente_id' => 'Search.Contratinsertion.referent_id'
		)
	);
?>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		observeDisableFieldsOnValue(
			'SearchContratinsertionDecisionCi',
			[
				'SearchContratinsertionDatevalidationCiDay',
				'SearchContratinsertionDatevalidationCiMonth',
				'SearchContratinsertionDatevalidationCiYear'
			],
			'V',
			false
		);
	} );
</script>