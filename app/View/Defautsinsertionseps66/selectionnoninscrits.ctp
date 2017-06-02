<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<h1><?php echo $this->pageTitle = __d( 'defautinsertionep66', "{$this->name}::{$this->action}" );?></h1>

<ul class="actionMenu">
	<?php
		echo '<li>'.$this->Xhtml->link(
			$this->Xhtml->image(
				'icons/application_form_magnify.png',
				array( 'alt' => '' )
			).' Formulaire',
			'#',
			array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
		).'</li>';
	?>
</ul>

<?php echo $this->Form->create( 'Defautinsertionep66', array( 'type' => 'post', 'action' => $this->action, 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>
	<?php echo $this->Form->input( 'Defautinsertionep66.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>

	<?php
		echo $this->Search->blocAllocataire();
		echo $this->Search->blocAdresse( $mesCodesInsee, $cantons );
	?>
	<fieldset>
		<legend>Recherche par dossier</legend>
		<?php
			echo $this->Form->input( 'Dossier.numdemrsa', array( 'label' => 'Numéro de demande RSA' ) );
			echo $this->Form->input( 'Dossier.matricule', array( 'label' => __d( 'dossier', 'Dossier.matricule' ) ) );

			$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
			echo $this->Form->input( 'Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
			echo $this->Search->etatdosrsa($etatdosrsa);
		?>
	</fieldset>

	<fieldset>
		<legend>Recherche par parcours allocataire</legend>
		<?php if( $this->action == 'selectionradies' ):?>
			<?php echo $this->Form->input( 'Historiqueetatpe.identifiantpe', array( 'label' => 'Identifiant Pôle Emploi', 'maxlength' => 15 ) );?>
		<?php endif;?>
		<?php echo $this->Form->input( 'Orientstruct.date_valid', array( 'label' => 'Mois d\'orientation', 'type' => 'date', 'dateFormat' => 'MY', 'minYear' => date( 'Y' ) - 5, 'maxYear' => date( 'Y' ) + 1, 'empty' => true ) );?>

	</fieldset>

	<?php
		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours );
		echo $this->Search->paginationNombretotal();
		echo $this->Search->observeDisableFormOnSubmit( 'Search' );
	?>

	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>

<?php if( isset( $personnes ) ):?>
<h2 class="noprint">Résultats de la recherche</h2>
<?php
	if ( is_array( $personnes ) && count( $personnes ) > 0 ) {
		echo $this->Default2->index(
			$personnes,
			array(
				'Personne.nom',
				'Personne.prenom',
				'Personne.dtnai',
				'Orientstruct.date_valid',
				'Foyer.enerreur' => array( 'type' => 'string', 'class' => 'foyer_enerreur' ),
				'Situationdossierrsa.etatdosrsa'
			),
			array(
				'cohorte' => false,
				'paginate' => 'Personne',
				'actions' => array(
					'Orientsstructs::index' => array( 'label' => 'Voir', 'url' => array( 'controller' => 'bilansparcours66', 'action' => 'add', '#Personne.id#', 'Bilanparcours66__examenauditionpe:'.$actionbp ) )
				),
				'options' => array('Situationdossierrsa'=>array('etatdosrsa'=> $etatdosrsa)),
				'tooltip' => array(
					'Structurereferenteparcours.lib_struc' => array( 'type' => 'text', 'domain' => 'search_plugin' ),
					'Referentparcours.nom_complet' => array( 'type' => 'text', 'domain' => 'search_plugin' )
				)
			)
		);
	}
	else{
		echo $this->Xhtml->tag( 'p', 'Aucun résultat ne correspond aux critères choisis.', array( 'class' => 'notice' ) );
	}
?>
<?php endif;?>