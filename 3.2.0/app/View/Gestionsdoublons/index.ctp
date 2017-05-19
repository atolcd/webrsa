<?php
	$this->pageTitle = __d( 'droit', 'controllers/Gestionsdoublons/index' );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );

	echo $this->Default3->actions(
		array(
			'/Gestionsdoublons/index/#toggleform' => array(
				'onclick' => '$(\'GestionsdoublonsIndexForm\').toggle(); return false;'
			),
		)
	);

	if( ( isset( $this->request->data['Search'] ) && !empty( $this->request->params['named'] ) ) ) {
		$out = "document.observe( 'dom:loaded', function() { \$('GestionsdoublonsIndexForm').hide(); } );";
		echo $this->Html->scriptBlock( $out );
	} elseif ($etats2 = Configure::read('Gestiondoublon.Situationdossierrsa2.etatdosrsa')) {
		$this->request->data['Search']["Situationdossierrsa2"]['etatdosrsa'] = (array)$etats2;
		$this->request->data['Search']["Situationdossierrsa2"]['etatdosrsa_choice'] = '1';
	}

	// Moteur de recherche
	echo $this->Xform->create( null, array( 'id' => 'GestionsdoublonsIndexForm' ) );

	// Filtres concernant le dossier
	echo $this->Search->blocDossier( null, 'Search' );

	echo '<fieldset><legend>Volet de gauche</legend>';
	echo $this->Search->etatdosrsa($options['Situationdossierrsa']['etatdosrsa'],  "Search.Situationdossierrsa.etatdosrsa");
	echo '</fieldset>';

	echo '<fieldset><legend>Volet de droite</legend>';
	echo $this->Search->etatdosrsa($options['Situationdossierrsa']['etatdosrsa'],  "Search.Situationdossierrsa2.etatdosrsa");
	echo '</fieldset>';

	echo $this->Search->blocAdresse( $options['mesCodesInsee'], $options['cantons'], 'Search' );

	// Filtres concernant l'allocataire
	echo '<fieldset>';
	echo sprintf( '<legend>%s</legend>', __d( 'search_plugin', 'Search.Personne' ) );
	echo $this->Search->blocAllocataire( array(), array(), 'Search' );
	echo $this->Search->toppersdrodevorsa( $options['Calculdroitrsa']['toppersdrodevorsa'], 'Search.Calculdroitrsa.toppersdrodevorsa' );
	echo '</fieldset>';

	echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );
	echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );

	echo $this->Xform->end( 'Search' );

	echo $this->Search->observeDisableFormOnSubmit( 'GestionsdoublonsIndexForm' );

	// Résultats
	if( isset( $results ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

                $this->Default3->DefaultPaginator->options(
                    array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);

		App::uses( 'SearchProgressivePagination', 'Search.Utility' );

		$nbActions = 3;
		$indexCols = array(
			'Dossier.numdemrsa',
			'Dossier.dtdemrsa',
			'Dossier.matricule',
			'Demandeur.nom',
			'Demandeur.prenom',
			'Situationdossierrsa.etatdosrsa',
			'Adresse.nomcom',
			'Dossier.locked' => array( 'type' => 'boolean' ),
			'Dossier2.numdemrsa',
			'Dossier2.dtdemrsa' => array( 'type' => 'date' ),
			'Dossier2.matricule',
			'Demandeur2.nom',
			'Demandeur2.prenom',
			'Situationdossierrsa2.etatdosrsa',
			'Adresse2.nomcom',
			'Dossier2.locked' => array( 'type' => 'boolean' ),
		);

		if (Configure::read('Gestionsdoublons.index.useTag') && Configure::read('Gestionsdoublons.index.Tag.valeurtag_id')) {
			$indexCols['/Tags/tag_gestionsdoublons_index/#Foyer.id#/#Foyer2.id#'] = array(
				'disabled' => '( !\''.$this->Permissions->check( 'Tags', 'tag_gestionsdoublons_index' ).'\' )',
			);
			$nbActions++;
		}

		$indexCols = array_merge(
			$indexCols,
			array(
				'/Personnes/index/#Foyer.id#' => array(
					'disabled' => '( !\''.$this->Permissions->check( 'Personnes', 'index' ).'\' )',
				),
				'/Personnes/index/#Foyer2.id#' => array(
					'disabled' => '( !\''.$this->Permissions->check( 'Personnes', 'index' ).'\' )',
				),
				'/Gestionsdoublons/fusion/#Foyer.id#/#Foyer2.id#' => array(
					'disabled' => '( \'#Dossier.locked#\' || \'#Dossier2.locked#\' || !\''.$this->Permissions->check( 'Gestionsdoublons', 'fusion' ).'\' )',
				)
			)
		);

		$index = $this->Default3->index(
			$results,
			$indexCols,
			array(
				'options' => $options,
				'format' => $this->element( 'pagination_format' )
			)
		);

		echo str_replace(
			'<thead>',
			'<thead><tr><th colspan="8">Dossier</th><th colspan="8">Dossier temporaire</th><th colspan="'.$nbActions.'"></th></tr>',
			$index
		);
	}
?>