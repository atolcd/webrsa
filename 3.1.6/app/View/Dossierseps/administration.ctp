<h1><?php echo $this->pageTitle = __d( 'droit', 'Dossierseps:administration' );?></h1>

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
        echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
	).'</li></ul>';

	echo $this->Form->create( 'Dossierep', array( 'id' => 'Search', 'class' => ( !empty( $this->request->data ) ? 'folded' : 'unfolded' ) ) );

	echo $this->Search->blocAllocataire( array(), array(), 'Search' );
	echo $this->Search->blocAdresse( $mesCodesInsee, $cantons, 'Search' );

	echo '<fieldset><legend>'.__d( 'dossierseps', 'Search.Ep' ).'</legend>';
	echo $this->Xform->input( 'Search.Ep.regroupementep_id', array( 'options' => $options['Ep']['regroupementep_id'], 'empty' => true, 'domain' => 'dossierseps' ) );
	echo $this->Xform->input( 'Search.Ep.name', array( 'domain' => 'dossierseps' ) );
	echo $this->Xform->input( 'Search.Ep.identifiant', array( 'domain' => 'dossierseps' ) );
	echo '</fieldset>';

	echo '<fieldset><legend>'.__d( 'dossierseps', 'Search.Commissionep' ).'</legend>';
	echo $this->Xform->input( 'Search.Commissionep.name', array( 'domain' => 'dossierseps' ) );
	echo $this->Xform->input( 'Search.Commissionep.identifiant', array( 'domain' => 'dossierseps' ) );
	echo $this->Search->date( 'Search.Commissionep.dateseance' );
	echo '</fieldset>';

	echo '<fieldset><legend>'.__d( 'dossierseps', 'Search.Dossierep' ).'</legend>';
	echo $this->Xform->input( 'Search.Dossierep.themeep', array( 'options' => $options['Dossierep']['themeep'], 'empty' => true, 'domain' => 'dossierseps' ) );
	echo '</fieldset>';

	echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );
	echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );
	echo $this->Search->observeDisableFormOnSubmit( 'Search' );

	echo '<div class="submit noprint">';
		echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );
		echo ' ';
		echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );
	echo '</div>';
	echo $this->Form->end();

	if( isset( $results ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);

		echo $this->Default3->index(
			$results,
			array(
				'Personne.qual',
				'Personne.nom',
				'Personne.prenom',
				'Dossierep.created',
				'Dossierep.themeep',
				'Decisionthematique.decision' => array( 'type' => 'text' ),
				'Commissionep.dateseance',
				'Commissionep.etatcommissionep',
				'Dossierep.nb_passages_commission' => array( 'type' => 'integer' ),
				'Dossier.locked' => array( 'type' => 'boolean' ),
				'/Historiqueseps/view_passage/#Passagecommissionep.id#' => array(
					'disabled' => '( !\'#Passagecommissionep.id#\' || !\''.$this->Permissions->check( 'Historiqueseps', 'view_passage' ).'\' )',
				),
				'/Commissionseps/view/#Commissionep.id#' => array(
					'disabled' => '( !\'#Passagecommissionep.id#\' || !\''.$this->Permissions->check( 'Commissionseps', 'index' ).'\' )',
				),
				'/Dossierseps/delete/#Dossierep.id#' => array(
					'disabled' => '( \'#Dossier.locked#\' || !\''.$this->Permissions->check( 'Dossierseps', 'delete' ).'\' )',
					'confirm' => true
				),
				'/Dossierseps/deletepassage/#Passagecommissionep.id#' => array(
					'disabled' => '( !\'#Passagecommissionep.id#\' || \'#Dossier.locked#\' || !\''.$this->Permissions->check( 'Dossierseps', 'deletepassage' ).'\' )',
					'confirm' => true
				),
			),
			array(
				'options' => $options,
				'format' => SearchProgressivePagination::format( !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' ) )
			)
		);
	}
?>