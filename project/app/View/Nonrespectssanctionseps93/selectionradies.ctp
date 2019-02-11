<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<h1><?php echo $this->pageTitle = __d( 'nonrespectsanctionep93', "{$this->name}::{$this->action}" );?></h1>

<?php
	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "var form = $$( 'form' ); form = form[0]; $( form ).toggle(); return false;" )
	).'</li></ul>';
?>

<?php echo $this->Xform->create( 'Nonrespectsanctionep93', array( 'type' => 'post', 'url' => array( 'action' => $this->action ), 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>
	<fieldset>
			<?php echo $this->Xform->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) );?>

			<legend>Filtrer par Personne</legend>
			<?php
				echo $this->Default2->subform(
					array(
						'Search.Personne.nom' => array( 'label' => __d( 'personne', 'Personne.nom' ) ),
						'Search.Personne.prenom' => array( 'label' => __d( 'personne', 'Personne.prenom' ) ),
						'Search.Personne.nomnai' => array( 'label' => __d( 'personne', 'Personne.nomnai' ) ),
						'Search.Personne.nir' => array( 'label' => __d( 'personne', 'Personne.nir' ), 'maxlength' => 15 ),
						'Search.Dossier.matricule' => array( 'label' => __d( 'dossier', 'Dossier.matricule' ), 'maxlength' => 15 ),
						'Search.Dossier.numdemrsa' => array( 'label' => __d( 'dossier', 'Dossier.numdemrsa' ), 'maxlength' => 15 ),
						'Search.Historiqueetatpe.identifiantpe' => array( 'label' => __d( 'historiqueetatpe', 'Historiqueetatpe.identifiantpe' ), 'maxlength' => 11 ),
						'Search.Adresse.nomcom' => array( 'label' => 'Commune de l\'allocataire ' ),
						'Search.Adresse.numcom' => array( 'label' => 'Numéro de commune au sens INSEE ', 'type' => 'select', 'options' => $mesCodesInsee, 'empty' => true )
					)
				);
			?>
		</fieldset>

		<?php
			echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );
			echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );
			echo $this->Search->observeDisableFormOnSubmit( 'Search' );
		?>

	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>

<?php if( isset( $radiespe ) ):?>
<?php
	echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

	if ( is_array( $radiespe ) && count( $radiespe ) > 0 ) {
		$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

		echo $this->Default2->index(
			$radiespe,
			array(
				'Personne.nom',
				'Personne.prenom',
				'Personne.dtnai',
				'Adresse.nomcom',
				'Orientstruct.date_valid',
				'Typeorient.lib_type_orient',
				'Historiqueetatpe.date',
				'Contratinsertion.present' => array( 'type' => 'boolean' ),
				'Foyer.enerreur' => array( 'type' => 'string', 'class' => 'foyer_enerreur' ),
				'Historiqueetatpe.chosen' => array( 'input' => 'checkbox', 'type' => 'boolean', 'domain' => 'nonrespectsanctionep93', 'sort' => false ),
			),
			array(
				'cohorte' => true,
				'hidden' => array(
					'Personne.id',
					'Historiqueetatpe.id'
				),
				'paginate' => 'Personne',
				'domain' => 'nonrespectsanctionep93',
				'labelcohorte' => 'Enregistrer',
				'tooltip' => array(
					'Structurereferenteparcours.lib_struc' => array( 'type' => 'text', 'domain' => $domain_search_plugin, 'model' => 'Structurereferente' ),
					'Referentparcours.nom_complet' => array( 'type' => 'text', 'domain' => $domain_search_plugin, 'model' => 'Referent' )
				)
			)
		);
	}
	else {
		echo $this->Xhtml->tag( 'p', 'Aucun résultat ne correspond aux critères choisis.', array( 'class' => 'notice' ) );
	}
?>
<?php endif;?>
<?php if( !empty( $radiespe ) ):?>
	<?php echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => 'return toutCocher();' ) );?>
	<?php echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => 'return toutDecocher();' ) );?>
<?php endif;?>