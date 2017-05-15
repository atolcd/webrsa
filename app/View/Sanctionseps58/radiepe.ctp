<h1><?php echo $this->pageTitle = __d( 'sanctionep58', "{$this->name}::{$this->action}" );?></h1>

<!--<?php
    echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
        $this->Xhtml->image(
            'icons/application_form_magnify.png',
            array( 'alt' => '' )
        ).' Formulaire',
        '#',
        array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
    ).'</li></ul>';
?>
<?php echo $this->Xform->create( 'Sanctionseps58', array( 'id' => 'Search', 'class' => 'folded' ) );?>
	<?php echo $this->Search->paginationNombretotal(); ?>

    <div class="submit noprint">
        <?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
    </div>

<?php echo $this->Xform->end();?>-->

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

	echo $this->Default3->actions(
		array(
			'/Sanctionseps58/selectionnoninscrits/#toggleform' => array(
				'onclick' => '$(\'Sanctionseps58SelectionradiesForm\').toggle(); return false;'
			),
		)
	);

	echo $this->Xform->create( 'Search', array( 'id' => 'Sanctionseps58SelectionradiesForm' ) );

	echo $this->Allocataires->blocDossier(
		array(
			'options' => $options,
			'skip' => array(
				'Search.Situationdossierrsa.etatdosrsa',
			)
		)
	);
	echo $this->Allocataires->blocAdresse( array( 'options' => $options ) );
	echo $this->Allocataires->blocAllocataire(
		array(
			'options' => $options,
			'skip' => array(
				'Search.Calculdroitrsa.toppersdrodevorsa',
			)
		)
	);
	echo $this->Allocataires->blocReferentparcours( array( 'options' => $options ) );
	echo $this->Allocataires->blocPagination( array( 'options' => $options ) );
	echo $this->Allocataires->blocScript( array( 'options' => $options ) );

	echo $this->Xform->end( 'Search' );
?>

<?php
	$configureConditions = Configure::read( 'Selectionradies.conditions' );
	if( !empty( $configureConditions ) ) {
		$fields = array(
			'Historiqueetatpe.chosen' => array( 'input' => 'checkbox', 'type' => 'boolean', 'domain' => 'sanctionep58' ),
			'Dossier.matricule',
			'Personne.nom',
			'Personne.prenom',
			'Personne.dtnai',
			'Adresse.nomcom',
			'Historiqueetatpe.etat' => array( 'domain' => 'sanctionep58' ),
			'Historiqueetatpe.code' => array( 'domain' => 'sanctionep58' ),
			'Historiqueetatpe.motif' => array( 'domain' => 'sanctionep58' ),
			'Historiqueetatpe.date',
			'Structureorientante.lib_struc' => array( 'type' => 'text' ),
			'Typeorient.lib_type_orient',
			'Structurereferente.lib_struc'
		);
	}
	else {
		$fields = array(
			'Historiqueetatpe.chosen' => array( 'input' => 'checkbox', 'type' => 'boolean', 'domain' => 'sanctionep58' ),
			'Dossier.matricule',
			'Personne.nom',
			'Personne.prenom',
			'Personne.dtnai',
			'Adresse.nomcom',
			'Historiqueetatpe.motif' => array( 'domain' => 'sanctionep58' ),
			'Historiqueetatpe.date',
			'Structureorientante.lib_struc' => array( 'type' => 'text' ),
			'Typeorient.lib_type_orient',
			'Structurereferente.lib_struc'
		);
	}

	echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

	echo $this->Default2->index(
		$personnes,
		$fields,
		array(
			'cohorte' => true,
			'hidden' => array(
				'Personne.id',
				'Historiqueetatpe.id',
				'Orientstruct.id',
				'Dossierep.id',
			),
			'paginate' => 'Personne',
			'domain' => 'sanctionep58',
			'tooltip' => array(
				'Structurereferenteparcours.lib_struc' => array( 'type' => 'text', 'domain' => 'search_plugin' ),
				'Referentparcours.nom_complet' => array( 'type' => 'text', 'domain' => 'search_plugin' )
			)
		)
	);
?>
<?php if( !empty( $personnes ) ):?>
		<ul class="actionMenu">
			<li><?php
				echo $this->Xhtml->exportLink(
					'Télécharger le tableau',
					array( 'controller' => 'sanctionseps58', 'action' => 'exportcsv', 'qdRadies' ) + Hash::flatten( array( 'Search' => Hash::get( (array)$this->request->data, 'Search' ) ), '__' )
				);
			?></li>
		</ul>
<?php
		echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => 'return toutCocher();' ) );
		echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => 'return toutDecocher();' ) );

endif;?>

<!--<?php echo $this->Search->observeDisableFormOnSubmit( 'Search' ); ?>-->
<?php echo $this->Html->scriptBlock( "document.observe( 'dom:loaded', function() { \$('Sanctionseps58SelectionradiesForm').hide(); } );" );?>