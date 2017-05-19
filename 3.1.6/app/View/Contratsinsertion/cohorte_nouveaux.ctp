<?php $this->start( 'custom_search_filters' );?>
<fieldset>
	<legend>Recherche de CER</legend>
	<?php
		$paramDate = array(
			'domain' => null,
			'minYear_from' => '2009',
			'maxYear_from' => date( 'Y' ) + 1,
			'minYear_to' => '2009',
			'maxYear_to' => date( 'Y' ) + 4
		);
		echo $this->Allocataires->SearchForm->dateRange( 'Search.Contratinsertion.created', $paramDate );

		echo $this->Allocataires->communautesr( 'Contratinsertion', array( 'options' => array( 'Search' => $options ), 'hide' => false ) );
		echo $this->Default3->subform(
			array(
				'Search.Contratinsertion.structurereferente_id' => array( 'empty' => true, 'required' => false ),
				'Search.Contratinsertion.referent_id' => array( 'empty' => true ),
				'Search.Contratinsertion.forme_ci' => array( 'type' => 'radio', 'required' => false, 'legend' => __m( 'Search.Contratinsertion.forme_ci' ) )
			),
			array( 'options' => array( 'Search' => $options ) )
		);
	?>
</fieldset>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/cohorte',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => false
		)
	);

	echo $this->Observer->dependantSelect(
		array(
			'Search.Contratinsertion.structurereferente_id' => 'Search.Contratinsertion.referent_id'
		)
	);
?>
<?php if( isset( $results ) ): ?>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		<?php foreach( array_keys( $results ) as $index ):?>
		observeDisableFieldsOnValue(
			'Cohorte<?php echo $index;?>ContratinsertionDecisionCi',
			[
				'Cohorte<?php echo $index;?>ContratinsertionDatevalidationCiDay',
				'Cohorte<?php echo $index;?>ContratinsertionDatevalidationCiMonth',
				'Cohorte<?php echo $index;?>ContratinsertionDatevalidationCiYear'
			],
			'V',
			false
		);

		observeDisableFieldsOnValue(
			'Cohorte<?php echo $index;?>ContratinsertionDecisionCi',
			[ 'Cohorte<?php echo $index;?>ContratinsertionObservCi' ],
			'E',
			true
		);
		<?php endforeach;?>
	} );
</script>
<?php endif;?>