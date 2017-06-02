<?php $this->start( 'custom_search_filters' );?>
<fieldset>
	<legend>Recherche PDO</legend>
	<?php
		echo $this->Default3->subform(
			array(
				'Search.Propopdo.typepdo_id' => array( 'empty' => true, 'required' => false ),
				'Search.Decisionpropopdo.decisionpdo_id' => array( 'empty' => true, 'required' => false ),
				'Search.Propopdo.motifpdo' => array( 'empty' => true, 'required' => false ),
				'Search.Propopdo.user_id' => array( 'empty' => true, 'required' => false ),
			),
			array( 'options' => array( 'Search' => $options ) )
		);
		$paramDate = array(
			'domain' => null,
			'minYear_from' => '2009',
			'maxYear_from' => date( 'Y' ) + 1,
			'minYear_to' => '2009',
			'maxYear_to' => date( 'Y' ) + 4
		);
		echo $this->Allocataires->SearchForm->dateRange( 'Search.Propopdo.datedecisionpdo', $paramDate );
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
?>