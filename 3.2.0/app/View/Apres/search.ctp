<?php
	$departement = (int)Configure::read( 'Cg.departement' );
	$paramDate = array(
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 4
	);
	$paramAllocataire = array(
		'options' => $options,
		'prefix' => 'Search',
	);
	$dateRule = array(
		'date' => array(
			'rule' => array('date'),
			'message' => null,
			'required' => null,
			'allowEmpty' => true,
			'on' => null
		)
	);

	$dates = array(
		'Dossier' => array('dtdemrsa' => $dateRule),
		'Personne' => array('dtnai' => $dateRule),
		'Apre' => array('datedemandeapre' => $dateRule)
	);
	echo $this->FormValidator->generateJavascript($dates, false);
?>
<?php $this->start( 'custom_search_filters' );?>
<?php
	echo '<fieldset><legend>' . __m( 'Apre.search' ) . '</legend>'
		. (
			$departement === 93 && $this->request->action === 'search'
			? $this->Default3->subform(
				array(
					'Search.Apre.statutapre',
					'Search.Tiersprestataireapre.id' => array('empty' => true)
				),
				array( 'options' => array( 'Search' => $options ) )
			)
			: ''
		)
	;
	
	$datedemandeParams = array('legend' => __m('Search.Apre.datedemandeapre'));
	if ($departement === 66) {
		echo $this->SearchForm->dateRange('Search.Aideapre66.datedemande', $paramDate + $datedemandeParams);
	} else {
		echo $this->SearchForm->dateRange('Search.Apre.datedemandeapre', $paramDate + $datedemandeParams);
	}

	if ( $departement === 66 ) {
			echo $this->Default3->subform(
				array(
					'Search.Apre.isapre' => array('empty' => true),
					'Search.Apre.structurereferente_id' => array('empty' => true),
					'Search.Apre.referent_id' => array('empty' => true),
					'Search.Apre.activitebeneficiaire' => array('empty' => true),
				),
				array( 'options' => array( 'Search' => $options ) )
			)
			. $this->Default3->subform(
				array(
					'Search.Aideapre66.themeapre66_id' => array('empty' => true),
					'Search.Aideapre66.typeaideapre66_id' => array('empty' => true),
				),
				array( 'options' => array( 'Search' => $options ) )
			)
			. $this->Default3->subform(
				array(
					'Search.Apre.etatdossierapre' => array('empty' => true),
					'Search.Apre.isdecision' => array('type' => 'radio', 'class' => 'uncheckable', 'legend' => __m('Search.Apre.isdecision')),
				),
				array( 'options' => array( 'Search' => $options ) )
			)
		;
	}
	else if ( $departement === 93 ) { // FIXME: en fait, tout le monde (?), mais pas dans la même vue
		echo $this->Default3->subform(
			array_merge(
				(
					$this->request->action === 'search_eligibilite'
						? array(
							'Search.Apre.eligibiliteapre' => array( 'empty' => true ),
						)
						: array()
				),
				array(
					'Search.Apre.typedemandeapre' => array( 'empty' => true),
					'Search.Apre.activitebeneficiaire' => array('empty' => true),
					'Search.Apre.natureaide' => array('empty' => true),
				)
			),
			array( 'options' => array( 'Search' => $options ) )
		);
		
		echo '</fieldset>';

		echo '<fieldset><legend>' . __m( 'Search.Relanceapre' ) . '</legend>'
			. $this->SearchForm->dateRange( 'Search.Relanceapre.daterelance', $paramDate + array( 'legend' => __m( 'Search.Relanceapre.daterelance' ) ) )
			. $this->Default3->subform(
				array(
					'Search.Apre.etatdossierapre' => array('empty' => true)
				),
				array( 'options' => array( 'Search' => $options ) )
			)
		;
	}
	
	echo '</fieldset>';
?>
<?php $this->end();?>

<?php
	$beforeResults = '';
	if( isset( $count_apres_statut ) ) {
		$msgid = 'Nombre total d\'APREs: %d, dont %d en attente de décision et %d en attente de traitement';
		$total = (int)Hash::get( $count_apres_statut, 'autre' ) + (int)Hash::get( $count_apres_statut, 'decision' ) + (int)Hash::get( $count_apres_statut, 'traitement' );
		$beforeResults = $this->Html->tag( 'p', sprintf( $msgid, $total, (int)Hash::get( $count_apres_statut, 'decision' ), (int)Hash::get( $count_apres_statut, 'traitement' ) ) );
	}

	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'beforeResults' => $beforeResults,
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);

	$dependantSelects = array( 'Search.Apre.structurereferente_id' => 'Search.Apre.referent_id' );
	if( $departement === 66 ) {
		$dependantSelects['Search.Aideapre66.themeapre66_id'] = 'Search.Aideapre66.typeaideapre66_id';
	}
	echo $this->Observer->dependantSelect( $dependantSelects );
?>