<?php
	$paramDate = array(
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 5
	);
?>
<?php $this->start( 'custom_search_filters' );?>
<fieldset>
	<legend><?php echo __m( 'Search.Propopdo' );?></legend>
	<?php
		echo $this->SearchForm->dateRange( 'Search.Propopdo.datereceptionpdo', $paramDate + array( 'legend' => __m( 'Search.Propopdo.datereceptionpdo' ) ) );
		echo $this->SearchForm->dateRange( 'Search.Decisionpropopdo.datedecisionpdo', $paramDate + array( 'legend' => __m( 'Search.Decisionpropopdo.datedecision' ) ) );

		echo $this->Xform->input( 'Search.Propopdo.traitementencours', array( 'label' => 'Uniquement les PDOs possédant un traitement avec une date d\'échéance', 'type' => 'checkbox' ) );

		echo $this->Default3->subform(
			array(
				'Search.Propopdo.originepdo_id' => array( 'empty' => true ),
				'Search.Propopdo.etatdossierpdo' => array( 'empty' => true ),
				'Search.Decisionpropopdo.decisionpdo_id' => array( 'empty' => true, 'label' => __d( 'propospdos_search', 'Search.Decisionpropopdo.decisionpdo_id' ) ),
				'Search.Propopdo.user_id' => array( 'empty' => true ),
				'Search.Propopdo.motifpdo' => array( 'empty' => true )
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
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);
?>