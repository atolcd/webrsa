<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Xhtml->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

	echo $this->Default3->actions(
		array(
			'/Demenagementshorsdpts/search1/#toggleform' => array(
				'onclick' => '$(\'DemenagementshorsdptsSearch1Form\').toggle(); return false;'
			),
		)
	);

	echo $this->Xform->create( 'Search1', array( 'id' => 'DemenagementshorsdptsSearch1Form' ) );

	echo $this->Allocataires->blocDossier( array( 'options' => $options ) );
	echo $this->Allocataires->blocAdresse( array( 'options' => $options ) );
	echo $this->Allocataires->blocAllocataire( array( 'options' => $options ) );

	// Début spécificités fiche de prescription
	/*echo $this->Xform->input( 'Search.Ficheprescription93.exists', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'Ficheprescription93.exists' ), 'domain' => 'fichesprescriptions93', 'empty' => true ) );
	echo '<fieldset id="specificites_fichesprescriptions93"><legend>'.__d( 'fichesprescriptions93', 'Search.Ficheprescription93' ).'</legend>';
	echo '</fieldset>';*/
	// Fin spécificités fiche de prescription

	echo $this->Allocataires->blocReferentparcours( array( 'options' => $options ) );
	echo $this->Allocataires->blocPagination( array( 'options' => $options ) );
	echo $this->Allocataires->blocScript( array( 'options' => $options ) );

	echo $this->Xform->end( 'Search' );

	if( isset( $results ) ) {
		echo '<h2 class="noprint">Résultats de la recherche</h2>';

		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);

		App::uses( 'SearchProgressivePagination', 'Search.Utility' );

		$index = $this->Default3->index(
			$results,
			array(
				'Dossier.matricule',
				'Personne.nom_complet',
				'Adressefoyer.dtemm',
				'Adresse.localite',
				'Adressefoyer2.dtemm' => array( 'type' => 'date' ),
				'Adresse2.localite',
				'Adressefoyer3.dtemm' => array( 'type' => 'date' ),
				'Adresse3.localite',
				'Dossier.locked' => array( 'type' => 'boolean' ),
				'/Dossiers/view/#Dossier.id#',
			),
			array(
				'options' => $options,
				'format' => __( SearchProgressivePagination::format( !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' ) ) )
			)
		);

		echo str_replace(
			'<thead>',
			'<thead><tr><th colspan="2"></th><th colspan="2">Adresse de rang 01</th><th colspan="2">Adresse de rang 02</th><th colspan="2">Adresse de rang 03</th><th colspan="2"></th></tr>',
			$index
		);
	}
?>
<?php if( isset( $results ) ):?>
<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv1' ) + Hash::flatten( $this->request->data, '__' ),
			( $this->Permissions->check( $this->request->params['controller'], 'exportcsv1' ) && count( $results ) > 0 )
		);
	?></li>
</ul>
<?php endif;?>