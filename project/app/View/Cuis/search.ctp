<?php
	$departement = (integer)Configure::read( 'Cg.departement' );
	$controller = $this->params->controller;
	$action = $this->action;
	$availableDomains = WebrsaTranslator::domains();
	$domain = isset( $availableDomains[0] ) ? $availableDomains[0] : $controller;
	$paramDate = array(
		'domain' => null,
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 4
	);

	$this->start( 'custom_search_filters' );

	// Spécifique CG 66
	if ( $departement === 66 ){
		foreach( $options['Cui66']['etatdossiercui66'] as $key => $value ){
			$options['Cui66']['etatdossiercui66'][$key] = sprintf( $value, '(Date)' );
		}

		echo '<fieldset><legend id="Cui66Positions">' . __m( 'Cui66.positions' ) . '</legend>'
			. $this->Xform->multipleCheckbox( 'Search.Cui66.etatdossiercui66', $options, 'divideInto2Columns' )
			. $this->Allocataires->SearchForm->dateRange( 'Search.Historiquepositioncui66.created', $paramDate )
			. '</fieldset>'
		;

		echo '<fieldset><legend id="Cui66Choixformulaire">' . __m( 'Cui66.choixformulaire' ) . '</legend>'
			. $this->Default3->subform(
				array(
					'Search.Cui66.typeformulaire' => array( 'empty' => true, 'options' => $options['Cui66']['typeformulaire'], 'label' => __m( 'Cui66.typeformulaire' ) )
				)
			) . '</fieldset>'
		;

		echo '<fieldset id="CuiSecteur"><legend>' . __m( 'Cui.secteur' ) . '</legend>'
			. $this->Default3->subform(
				array(
					'Search.Cui.secteurmarchand' => array( 'empty' => true, 'label' => __m( 'Cui.secteurmarchand' ) ),
					'Search.Cui66.typecontrat' => array( 'empty' => true, 'label' => __m( 'Cui66.typecontrat' ), 'options' => $options['Cui66']['typecontrat_actif'] ),
				),
				array( 'options' => array( 'Search' => $options ) )
			) . '</fieldset>'
		;

		echo '<fieldset id="Cui66Dossier"><legend>' . __m( 'Cui66.dossier' ) . '</legend>'
			. $this->Default3->subform(
				array(
					'Search.Cui66.dossiereligible' => array( 'empty' => true, 'label' => __m( 'Cui66.dossiereligible' ) ),
					'Search.Cui66.dossierrecu' => array( 'empty' => true, 'label' => __m( 'Cui66.dossierrecu' ) ),
					'Search.Cui66.dossiercomplet' => array( 'empty' => true, 'label' => __m( 'Cui66.dossiercomplet' ) ),
				),
				array( 'options' => array( 'Search' => $options ) )
			)
			. $this->Allocataires->SearchForm->dateRange( 'Search.Cui66.dateeligibilite', $paramDate )
			. $this->Allocataires->SearchForm->dateRange( 'Search.Cui66.datereception', $paramDate )
			. $this->Allocataires->SearchForm->dateRange( 'Search.Cui66.datecomplet', $paramDate )
			. '</fieldset>'
		;

		echo '<fieldset id="Cui66Email"><legend>' . __m( 'Cui66.email' ) . '</legend>'
			. $this->Default3->subform(
				array(
					'Search.Emailcui.textmailcui66_id' => array( 'empty' => true, 'label' => __m( 'Emailcui.textmailcui66_id' ) ),
				),
				array( 'options' => array( 'Search' => $options ) )
			)
			. $this->Allocataires->SearchForm->dateRange( 'Search.Emailcui.insertiondate', $paramDate )
			. $this->Allocataires->SearchForm->dateRange( 'Search.Emailcui.created', $paramDate )
			. $this->Allocataires->SearchForm->dateRange( 'Search.Emailcui.dateenvoi', $paramDate )
			. '</fieldset>'
		;


		// @fixme: traductions
		echo '<fieldset id="Propositioncui66"><legend>' . __m( 'Cui.Propositioncui66' ) . '</legend>'
			. $this->Default3->subform(
				array(
					'Search.Propositioncui66.avis' => array( 'empty' => true, 'label' => __m( 'Propositioncui66.avis' ), 'options' => $options['Propositioncui66']['avis'], 'required' => false ),
				),
				array( 'options' => array( 'Search' => $options ) )
			) . '</fieldset>'
		;

		echo '<fieldset id="Cui66Decision"><legend>' . __m( 'Cui66.decision' ) . '</legend>'
			. $this->Default3->subform(
				array(
					'Search.Decisioncui66.decision' => array( 'empty' => true, 'label' => __m( 'Decisioncui66.decision' ) ),
				),
				array( 'options' => array( 'Search' => $options ) )
			)
			. $this->Allocataires->SearchForm->dateRange( 'Search.Decisioncui66.datedecision', $paramDate )
			. '</fieldset>'
		;
	}

	echo '<fieldset id="CuiSituationsalarie"><legend>' . __m( 'Cui.situationsalarie' ) . '</legend>'
		. $this->Default3->subform(
			array(
				'Search.Cui.niveauformation' => array( 'empty' => true, 'type' => 'select', 'label' => __m( 'Cui.niveauformation' ) ),
				'Search.Cui.inscritpoleemploi' => array( 'empty' => true, 'label' => __m( 'Cui.inscritpoleemploi' ) ),
				'Search.Cui.sansemploi' => array( 'empty' => true, 'label' => __m( 'Cui.sansemploi' ) ),
				'Search.Cui.beneficiairede' => array( 'empty' => true, 'label' => __m( 'Cui.beneficiairede' ) ),
				'Search.Cui.majorationrsa' => array( 'empty' => true, 'label' => __m( 'Cui.majorationrsa' ) ),
				'Search.Cui.rsadepuis' => array( 'empty' => true, 'label' => __m( 'Cui.rsadepuis' ) ),
				'Search.Cui.travailleurhandicape' => array( 'empty' => true, 'label' => __m( 'Cui.travailleurhandicape' ) ),
			),
			array( 'options' => array( 'Search' => $options ) )
		) . '</fieldset>'
	;

	echo '<fieldset id="CuiContrattravail"><legend>' . __m( 'Cui.contrattravail' ) . '</legend>'
		. $this->Default3->subform(
			array(
				'Search.Cui.typecontrat' => array( 'empty' => true, 'label' => __m( 'Cui.typecontrat' ) ),
				'Search.Cui.partenaire_id' => array( 'empty' => true, 'label' => __d( 'cuis66', 'Cui.partenaire_id' ) ),
				'Search.Adressecui.commune' => array( 'empty' => true, 'label' => __d( 'cuis66', 'Adressecui.commune' ) ),
				'Search.Adressecui.canton' => array( 'empty' => true, 'label' => __d( 'cuis66', 'Adressecui.canton' ) ),
			),
			array( 'options' => array( 'Search' => $options ) )
		)
		. $this->Allocataires->SearchForm->dateRange( 'Search.Cui.dateembauche', $paramDate )
		. $this->Allocataires->SearchForm->dateRange( 'Search.Cui.findecontrat', $paramDate )
		. $this->Romev3->fieldset( 'Entreeromev3', array( 'options' => $options, 'prefix' => 'Search' ) )
		. '</fieldset>'
	;

	echo '<fieldset id="CuiPrise_en_charge"><legend>' . __m( 'Cui.prise_en_charge' ) . '</legend>'
		. $this->Allocataires->SearchForm->dateRange( 'Search.Cui.effetpriseencharge', $paramDate )
		. $this->Allocataires->SearchForm->dateRange( 'Search.Cui.finpriseencharge', $paramDate )
		. $this->Allocataires->SearchForm->dateRange( 'Search.Cui.decisionpriseencharge', $paramDate )
		. '</fieldset>'
	;

	echo '<fieldset id="CuiPrise_en_charge"><legend>' . __m( 'Cui.date' ) . '</legend>'
		. $this->Allocataires->SearchForm->dateRange( 'Search.Cui.faitle', $paramDate )
		. '</fieldset>'
	;

	$this->end();

	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);