<?php
	function multipleCheckbox( $View, $path, $options, $class = '' ) {
		$name = model_field($path);
		return $View->Xform->input($path, array(
			'label' => __m($path), 
			'type' => 'select', 
			'multiple' => 'checkbox', 
			'options' => $options[$name[0]][$name[1]],
			'class' => $class
		));
	}

	echo $this->Default3->titleForLayout();

	// @param 1 Validation javascript, verification seulement sur date, 
	// @param 2 allowEmpty
	// @param 3 Verifications additionnelles
	// @param 4 ne regarde pas dans $this->request->data mais dans $this->request->data['Search']
	echo $this->FormValidator->checkOnly( 'date', true, null, 'Search' )->generateJavascript();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

	echo $this->Default3->actions(
		array(
			'/Criterescuis/search/#toggleform' => array(
				'onclick' => '$(\'CriterescuisSearchSearchForm\').toggle(); return false;',
				'class' => 'searchForm'
			),
		)
	);

	// 1. Moteur de recherche
	echo $this->Xform->create( null, 
		array( 
			'id' => 'CriterescuisSearchSearchForm', 
			'class' => ( ( isset( $results ) ) ? 'folded' : 'unfolded' ), 
			'url' => Router::url( array( 'controller' => 'criterescuis', 'action' => 'search' ), true )
		)
	);

	echo $this->Allocataires->blocDossier(
		array(
			'options' => $options,
			'prefix' => 'Search',
		)
	);

	echo $this->Allocataires->blocAdresse(
		array(
			'options' => $options,
			'prefix' => 'Search'
		)
	);

	echo $this->Allocataires->blocAllocataire(
		array(
			'options' => $options,
			'prefix' => 'Search'
		)
	);
	
	echo $this->Allocataires->blocReferentparcours(
		array(
			'options' => $options,
			'prefix' => 'Search'
		)
	);
	
	$paramDate = array( 
		'domain' => 'criterescuis', 
		'minYear_from' => '2009', 
		'maxYear_from' => date( 'Y' ) + 1, 
		'minYear_to' => '2009', 
		'maxYear_to' => date( 'Y' ) + 4
	);
	
	// Spécifique CG 66
	if ( Configure::read( 'Cg.departement' ) == 66 ){
		foreach( $options['Cui66']['etatdossiercui66'] as $key => $value ){
			$options['Cui66']['etatdossiercui66'][$key] = sprintf( $value, '(Date)' );
		}
		
		echo '<fieldset><legend id="Cui66Choixformulaire">' . __d('cuis66', 'Cui66.positions') . '</legend>'
			. multipleCheckbox( $this, 'Search.Cui66.etatdossiercui66', $options, 'divideInto2Columns' )
			. $this->SearchForm->dateRange( 'Search.Historiquepositioncui66.created', $paramDate )	
			. '</fieldset>'
		;
		
		echo '<fieldset><legend id="Cui66Choixformulaire">' . __d('cuis66', 'Cui66.choixformulaire') . '</legend>'
			. $this->Default3->subform(
				array(
					'Search.Cui66.typeformulaire' => array( 'empty' => true, 'options' => $options['Cui66']['typeformulaire'], 'label' => __d( 'cuis66', 'Cui66.typeformulaire' ) )
				)
			) . '</fieldset>'
		;
		
		echo '<fieldset id="CuiSecteur"><legend>' . __d('cuis66', 'Cui.secteur') . '</legend>'
			. $this->Default3->subform(
				array(
					'Search.Cui.secteurmarchand' => array( 'empty' => true, 'label' => __d( 'cuis', 'Cui.secteurmarchand' ) ),
					'Search.Cui66.typecontrat' => array( 'empty' => true, 'label' => __d( 'cuis66', 'Cui66.typecontrat' ), 'options' => $options['Cui66']['typecontrat_actif'] ),	
				),
				array( 'options' => array( 'Search' => $options ) )
			) . '</fieldset>'
		;
		
		echo '<fieldset><legend>' . __d('cuis66', 'Cui66.dossier') . '</legend>'
			. $this->Default3->subform(
				array(
					'Search.Cui66.dossiereligible' => array( 'empty' => true, 'label' => __d( 'cuis66', 'Cui66.dossiereligible' ) ),
					'Search.Cui66.dossierrecu' => array( 'empty' => true, 'label' => __d( 'cuis66', 'Cui66.dossierrecu' ) ),
					'Search.Cui66.dossiercomplet' => array( 'empty' => true, 'label' => __d( 'cuis66', 'Cui66.dossiercomplet' ) ),
				),
				array( 'options' => array( 'Search' => $options ) )
			)
			. $this->SearchForm->dateRange( 'Search.Cui66.dateeligibilite', $paramDate )
			. $this->SearchForm->dateRange( 'Search.Cui66.datereception', $paramDate )
			. $this->SearchForm->dateRange( 'Search.Cui66.datecomplet', $paramDate )
			. '</fieldset>'
		;
		
		echo '<fieldset><legend>' . __d('criterescuis', 'Cui66.email') . '</legend>'
			. $this->Default3->subform(
				array(
					'Search.Emailcui.textmailcui66_id' => array( 'empty' => true, 'label' => __d( 'cuis66', 'Emailcui.textmailcui66_id' ) ),
				),
				array( 'options' => array( 'Search' => $options ) )
			)
			. $this->SearchForm->dateRange( 'Search.Emailcui.insertiondate', $paramDate )
			. $this->SearchForm->dateRange( 'Search.Emailcui.created', $paramDate )
			. $this->SearchForm->dateRange( 'Search.Emailcui.dateenvoi', $paramDate )
			. '</fieldset>'
		;
		
		echo '<fieldset><legend>' . __d('criterescuis', 'Cui66.decision') . '</legend>'
			. $this->Default3->subform(
				array(
					'Search.Decisioncui66.decision' => array( 'empty' => true, 'label' => __d( 'cuis66', 'Decisioncui66.decision' ) ),
				),
				array( 'options' => array( 'Search' => $options ) )
			)
			. $this->SearchForm->dateRange( 'Search.Decisioncui66.datedecision', $paramDate )
			. '</fieldset>'
		;
	}
	
	echo '<fieldset id="CuiSituationsalarie"><legend>' . __d('cuis', 'Cui.situationsalarie') . '</legend>'
		. $this->Default3->subform(
			array(
				'Search.Cui.niveauformation' => array( 'empty' => true, 'type' => 'select', 'label' => __d( 'cuis', 'Cui.niveauformation' ) ),
				'Search.Cui.inscritpoleemploi' => array( 'empty' => true, 'label' => __d( 'cuis', 'Cui.inscritpoleemploi' ) ),
				'Search.Cui.sansemploi' => array( 'empty' => true, 'label' => __d( 'cuis', 'Cui.sansemploi' ) ),
				'Search.Cui.beneficiairede' => array( 'empty' => true, 'label' => __d( 'cuis', 'Cui.beneficiairede' ) ),
				'Search.Cui.majorationrsa' => array( 'empty' => true, 'label' => __d( 'cuis', 'Cui.majorationrsa' ) ),
				'Search.Cui.rsadepuis' => array( 'empty' => true, 'label' => __d( 'cuis', 'Cui.rsadepuis' ) ),
				'Search.Cui.travailleurhandicape' => array( 'empty' => true, 'label' => __d( 'cuis', 'Cui.travailleurhandicape' ) ),
			),
			array( 'options' => array( 'Search' => $options ) )
		) . '</fieldset>'
	;

	echo '<fieldset id="CuiContrattravail"><legend>' . __d('cuis', 'Cui.contrattravail') . '</legend>'
		. $this->Default3->subform(
			array(
				'Search.Cui.typecontrat' => array( 'empty' => true, 'label' => __d( 'cuis66', 'Cui.typecontrat' ) ),
				'Search.Cui.partenaire_id' => array( 'empty' => true, 'label' => __d( 'cuis66', 'Cui.partenaire_id' ) ),
				'Search.Adressecui.commune' => array( 'empty' => true, 'label' => __d( 'cuis66', 'Adressecui.commune' ) ),
				'Search.Adressecui.canton' => array( 'empty' => true, 'label' => __d( 'cuis66', 'Adressecui.canton' ) ),
			),
			array( 'options' => array( 'Search' => $options ) )
		)
		. $this->SearchForm->dateRange( 'Search.Cui.dateembauche', $paramDate )
		. $this->SearchForm->dateRange( 'Search.Cui.findecontrat', $paramDate )
		. $this->Romev3->fieldset( 'Entreeromev3', array( 'options' => $options, 'prefix' => 'Search' ) )
		. '</fieldset>'
	;
	
	echo '<fieldset id="CuiPrise_en_charge"><legend>' . __d('cuis', 'Cui.prise_en_charge') . '</legend>'
		. $this->SearchForm->dateRange( 'Search.Cui.effetpriseencharge', $paramDate )
		. $this->SearchForm->dateRange( 'Search.Cui.finpriseencharge', $paramDate )
		. $this->SearchForm->dateRange( 'Search.Cui.decisionpriseencharge', $paramDate )
		. '</fieldset>'
	;
	
	echo '<fieldset id="CuiPrise_en_charge"><legend>' . __d('cuis', 'Cui.date') . '</legend>'
		. $this->SearchForm->dateRange( 'Search.Cui.faitle', $paramDate )
		. '</fieldset>'
	;
	
	echo $this->Allocataires->blocPagination(
		array(
			'options' => $options,
			'prefix' => 'Search'
		)
	);

	echo $this->Xform->end( 'Search' );

	echo $this->Observer->disableFormOnSubmit( 'CriterescuisSearchSearchForm' );

	// 2. Formulaire de traitement des résultats de la recherche
	if( isset( $results ) ) {
		echo '<h2 class="noprint">Résultats de la recherche</h2>';
		
		if( !empty( $results ) ) {
			echo $this->Default3->DefaultForm->create( null, array( 'id' => 'CriterescuisSearchForm' ) );
		}

		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);
		
		// TODO: à factoriser avec Dsps::search() + les exportcsv
		$fields = Hash::normalize( (array)Configure::read( 'Criterescuis.search.fields' ) );

		// On recherche le type de chacun des champs
		foreach( $fields as $fieldName => $params ) {
			$params = (array)$params;
			if( !isset( $params['type'] ) ) {
				$fields[$fieldName]['type'] = $this->Default3->DefaultTable->DefaultTableCell->DefaultData->type( $fieldName );
			}
		}

		if( Configure::read( 'Cg.departement' ) == 66 ) {
			$linkView = '/cuis/index/#Cui.personne_id#';
			$linkEdit = '/Cuis66/edit/#Cui.id#';
		}
		else {
			$linkView = '/Cuis/view/#Cui.id#';
			$linkEdit = '/Cuis/edit/#Cui.id#';
		}

		$fields = array_merge(
			$fields,
			array(
				$linkView => array(
					'class' => 'view external',
					'title' => __d( 'criterescuis', '/Cuis/view' )
				),
			)
		);

		echo $this->Default3->index(
			$results,
			$fields,
			array(
				'format' => SearchProgressivePagination::format( !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' ) ),
				'options' => $options
			)
		);
	}
?>
<?php if( isset( $results ) ):?>
<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
			( $this->Permissions->check( $this->request->params['controller'], 'exportcsv' ) && count( $results ) > 0 )
		);
	?></li>
</ul>
<?php endif;?>