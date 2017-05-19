<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Xhtml->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
			$this->Xhtml->image(
					'icons/application_form_magnify.png', array( 'alt' => '' )
			).' Formulaire', '#', array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
	).'</li></ul>';
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox('SearchCommissionepDateseance', $('SearchCommissionepDateseanceFromDay').up('fieldset'), false);
		observeDisableFieldsetOnCheckbox('SearchDossierDtdemrsa', $('SearchDossierDtdemrsaFromDay').up('fieldset'), false);
	});
</script>

<?php echo $this->Xform->create( 'Gestionsanctionep58', array( 'type' => 'post', 'action' => $this->request->action, 'id' => 'Search', 'class' => ( ( isset( $this->request->data['Search']['active'] ) && !empty( $this->request->data['Search']['active'] ) ) ? 'folded' : 'unfolded' ) ) ); ?>
	<?php echo $this->Xform->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) ); ?>
    <?php echo $this->Xform->input( 'Search.action', array( 'type' => 'hidden', 'value' => $this->request->action ) ); ?>
	<fieldset>
		<legend>Filtrer par Equipe Pluridisciplinaire</legend>
		<?php
			echo $this->Default2->subform(
				array(
					'Search.Ep.regroupementep_id' => array( 'type' => 'select', 'label' => __d( 'ep', 'Ep.regroupementep_id', true ) ),
					'Search.Commissionep.name' => array( 'label' => __d( 'commissionep', 'Commissionep.name', true ) ),
					'Search.Commissionep.identifiant' => array( 'label' => __d( 'commissionep', 'Commissionep.identifiant', true ) ),
					'Search.Structurereferente.ville' => array( 'label' => __d( 'structurereferente', 'Structurereferente.ville', true ) ),
					'Search.Dossierep.themeep' => array( 'label' => __d( 'dossierep', 'Dossierep.themeep', true ), 'type' => 'select' )
				),
				array(
					'options' => $options
				)
			);
			echo $this->Xform->input( 'Search.Commissionep.dateseance', array( 'label' => 'Filtrer par date de Commission', 'type' => 'checkbox' ) );
		?>
		<fieldset>
			<legend>Filtrer par période</legend>
			<?php
				$dateseance_from = Set::check( $this->request->data, 'Search.Commissionep.dateseance_from' ) ? Set::extract( $this->request->data, 'Search.Commissionep.datecomite_from' ) : strtotime( '-1 week' );
				$dateseance_to = Set::check( $this->request->data, 'Search.Commissionep.dateseance_to' ) ? Set::extract( $this->request->data, 'Search.Commissionep.datecomite_to' ) : strtotime( 'now' );
			?>
			<?php echo $this->Xform->input( 'Search.Commissionep.dateseance_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $dateseance_from ) ); ?>
			<?php echo $this->Xform->input( 'Search.Commissionep.dateseance_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $dateseance_to ) ); ?>
		</fieldset>
	</fieldset>
	<?php if( $this->request->action == 'traitement' ): ?>
		<fieldset>
			<legend>Filtrer par décisions de sanction</legend>
			<?php echo $this->Form->input( 'Search.Decision.sanction', array( 'label' => 'Suivi de la sanction', 'type' => 'select', 'options' => array( 'N' => 'Non', 'O' => 'Oui' ), 'empty' => true ) ); ?>
		</fieldset>
	<?php endif; ?>
	<fieldset>
		<legend>Filtrer par Dossier</legend>
		<?php echo $this->Xform->input( 'Search.Dossier.dtdemrsa', array( 'label' => 'Filtrer par date de demande', 'type' => 'checkbox' ) ); ?>
		<fieldset>
			<legend>Date de demande RSA</legend>
			<?php
				$dtdemrsaFromSelected = $dtdemrsaToSelected = array( );
				if( !dateComplete( $this->request->data, 'Search.Dossier.dtdemrsa_from' ) ) {
					$dtdemrsaFromSelected = array( 'selected' => strtotime( '-1 week' ) );
				}
				if( !dateComplete( $this->request->data, 'Search.Dossier.dtdemrsa_to' ) ) {
					$dtdemrsaToSelected = array( 'selected' => strtotime( 'today' ) );
				}

				echo $this->Xform->input( 'Search.Dossier.dtdemrsa_from', Set::merge( array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 20 ), $dtdemrsaFromSelected ) );

				echo $this->Xform->input( 'Search.Dossier.dtdemrsa_to', Set::merge( array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 5, 'minYear' => date( 'Y' ) - 20 ), $dtdemrsaToSelected ) );
			?>
		</fieldset>
		<fieldset>
			<?php
				$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
				echo $this->Xform->input( 'Search.Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
			?>
		</fieldset>
		<?php
			if( !is_null( $etatdosrsa ) ) {
				echo $this->Search->etatdosrsa( $etatdosrsa, 'Search.Situationdossierrsa.etatdosrsa' );
			}

			echo $this->Default2->subform(
				array(
					'Search.Personne.nom' => array( 'label' => __d( 'personne', 'Personne.nom', true ), 'type' => 'text' ),
					'Search.Personne.prenom' => array( 'label' => __d( 'personne', 'Personne.prenom', true ), 'type' => 'text' ),
					'Search.Personne.nomnai' => array( 'label' => __d( 'personne', 'Personne.nomnai', true ), 'type' => 'text' ),
					'Search.Personne.nir' => array( 'label' => __d( 'personne', 'Personne.nir', true ), 'type' => 'text', 'maxlength' => 15 ),
					'Search.Dossier.matricule' => array( 'label' => __d( 'dossier', 'Dossier.matricule', true ), 'type' => 'text', 'maxlength' => 15 ),
					'Search.Dossier.numdemrsa' => array( 'label' => __d( 'dossier', 'Dossier.numdemrsa', true ), 'type' => 'text', 'maxlength' => 15 ),
					'Search.Adresse.nomcom' => array( 'label' => __d( 'adresse', 'Adresse.nomcom', true ), 'type' => 'text' ),
					'Search.Adresse.numcom' => array( 'label' => __d( 'adresse', 'Adresse.numcom', true ), 'type' => 'select', 'options' => $mesCodesInsee, 'empty' => true )
				),
				array(
					'options' => $options
				)
			);

			if( Configure::read( 'CG.cantons' ) ) {
				echo $this->Xform->input( 'Search.Canton.canton', array( 'label' => 'Canton', 'type' => 'select', 'options' => $cantons, 'empty' => true ) );
			}
		?>
	</fieldset>
	<?php
		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );
		echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );
		echo $this->Search->observeDisableFormOnSubmit( 'Search' );
	?>
	<div class="submit noprint">
		<?php
			echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );
			echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );
		?>
	</div>
<?php echo $this->Xform->end(); ?>