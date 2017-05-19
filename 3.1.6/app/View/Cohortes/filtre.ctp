<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'FiltreDtdemrsa', $( 'FiltreDtdemrsaFromDay' ).up( 'fieldset' ), false );
		observeDisableFieldsetOnCheckbox( 'FiltreDatePrint', $( 'FiltreDateImpressionFromDay' ).up( 'fieldset' ), false );
		observeDisableFieldsetOnCheckbox( 'FiltreDateValid', $( 'FiltreDateValidFromDay' ).up( 'fieldset' ), false );
	});
</script>

<?php
	$oridemrsaCochees = Set::extract( $this->request->data, 'Filtre.oridemrsa' );
	if( empty( $oridemrsaCochees ) ) {
		$oridemrsaCochees = array_keys( $oridemrsa );
	}

	$formSent = ( isset( $this->request->data['Filtre']['actif'] ) && $this->request->data['Filtre']['actif'] );
?>
<?php echo $this->Form->create( 'Filtre', array( 'id' => 'Filtre', 'class' => ( $formSent ? 'folded' : 'unfolded' ) ) );?>
	<div><?php echo $this->Form->input( 'Filtre.actif', array( 'type' => 'hidden', 'value' => true ) );?></div>
	<fieldset>
		<legend>Recherche par personne</legend>
		<?php echo $this->Form->input( 'Filtre.nom', array( 'label' => 'Nom ', 'type' => 'text' ) );?>
		<?php echo $this->Form->input( 'Filtre.prenom', array( 'label' => 'Prénom ', 'type' => 'text' ) );?>
		<?php
// 			if( isset( $toppersdrodevorsa ) ) {
				echo $this->Form->input( 'Filtre.toppersdrodevorsa', array( 'label' => 'Soumis à Droit et Devoir', 'type' => 'select', 'options' => $toppersdrodevorsa, 'selected' => ( !empty( $this->request->data ) ? @$this->request->data['Filtre']['toppersdrodevorsa'] : null ), 'empty' => true ) );
// 			}
				echo $this->Form->input( 'Filtre.hasDsp', array( 'label' => 'Possède des DSPs ?', 'type' => 'select', 'options' => $hasDsp, 'selected' => ( !empty( $this->request->data ) ? @$this->request->data['Filtre']['hasDsp'] : 1 ), 'empty' => true ) );


			if( !is_null($natpf)) {
				echo $this->Search->natpf($natpf);
			}
		?>
	</fieldset>

	<fieldset>
		<legend>Code origine demande Rsa</legend>
		<?php echo $this->Form->input( 'Filtre.oridemrsa', array( 'label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'options' => $oridemrsa, 'empty' => false, 'value' => $oridemrsaCochees ) );?>
	</fieldset>

	<fieldset>
		<legend>Commune de la personne</legend>
		<?php
			if( Configure::read( 'CG.cantons' ) ) {
				echo $this->Form->input( 'Canton.canton', array( 'label' => 'Canton', 'type' => 'select', 'options' => $cantons, 'empty' => true ) );
			}
		?>
		<?php echo $this->Form->input( 'Filtre.nomcom', array( 'label' => __d( 'adresse', 'Adresse.nomcom' ), 'type' => 'text' ) );?>
		<!-- <?php echo $this->Form->input( 'Filtre.numcom', array( 'label' => 'Numéro de commune au sens INSEE' ) );?> -->
		<?php echo $this->Form->input( 'Filtre.numcom', array( 'label' => 'Numéro de commune au sens INSEE', 'type' => 'select', 'options' => $mesCodesInsee, 'empty' => true ) );?>
		<?php echo $this->Form->input( 'Filtre.codepos', array( 'label' => __d( 'adresse', 'Adresse.codepos' ), 'type' => 'text', 'maxlength' => 5 ) );?>
	</fieldset>

	<?php if( $this->action == 'orientees' ):?>
		<?php echo $this->Form->input( 'Filtre.date_valid', array( 'label' => 'Filtrer par date d\'orientation', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Date d'orientation</legend>
			<?php
				$dateValidFromSelected = array();
				if( !dateComplete( $this->request->data, 'Filtre.date_valid_from' ) ) {
					$dateValidFromSelected = array( 'selected' => strtotime( '-1 week' ) );
				}
				echo $this->Form->input( 'Filtre.date_valid_from', Set::merge( array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120 ), $dateValidFromSelected ) );

				echo $this->Form->input( 'Filtre.date_valid_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 5, 'minYear' => date( 'Y' ) - 120 ) );
			?>
		</fieldset>
		<fieldset>
			<legend>Imprimé/Non imprimé</legend>
			<?php echo $this->Form->input( 'Filtre.date_impression', array( 'label' => 'Filtrer par impression', 'type' => 'select', 'options' => $printed, 'empty' => true ) );?>

		<?php echo $this->Form->input( 'Filtre.date_print', array( 'label' => 'Filtrer par date d\'impression', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Date d'impression</legend>
			<?php
				$dateImpressionFromSelected = array();
				if( !dateComplete( $this->request->data, 'Filtre.date_impression_from' ) ) {
					$dateImpressionFromSelected = array( 'selected' => strtotime( '-1 week' ) );
				}
				echo $this->Form->input( 'Filtre.date_impression_from', Set::merge( array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 5 ), $dateImpressionFromSelected ) );

				echo $this->Form->input( 'Filtre.date_impression_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 5, 'minYear' => date( 'Y' ) - 5 ) );
			?>
		</fieldset>
		</fieldset>
	<?php endif;?>

	<?php echo $this->Form->input( 'Filtre.dtdemrsa', array( 'label' => 'Filtrer par date de demande', 'type' => 'checkbox' ) );?>
	<fieldset>
		<legend>Date de demande RSA</legend>
		<?php
			$dtdemrsaFromSelected = array();
			if( !dateComplete( $this->request->data, 'Filtre.dtdemrsa_from' ) ) {
				$dtdemrsaFromSelected = array( 'selected' => strtotime( '-1 week' ) );
			}
			echo $this->Form->input( 'Filtre.dtdemrsa_from', Set::merge( array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120 ), $dtdemrsaFromSelected ) );

			echo $this->Form->input( 'Filtre.dtdemrsa_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 5, 'minYear' => date( 'Y' ) - 120 ) );
		?>
	</fieldset>
	<fieldset>
		<?php
			$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
			echo $this->Form->input( 'Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
		?>
	</fieldset>
	<fieldset>
		<?php
			if( in_array( $this->action, array( 'preconisationscalculables', 'preconisationsnoncalculables' ) ) ) {
				$enattente = array( 'Non orienté', 'En attente' );
				echo $this->Form->input( 'Filtre.enattente', array( 'label' => __( 'Statut de l\'orientation' ), 'type' => 'select', 'options' => array_combine( $enattente, $enattente ), 'empty' => true ) );
			}

			if( $this->action != 'preconisationsnoncalculables' ) {
				if ( Configure::read( 'Cg.departement' ) == 93 && ( in_array( $this->action, array( 'nouvelles', 'enattente', 'preconisationscalculables' ) ) ) ) {
					echo $this->Form->input( 'Filtre.propo_algo', array( 'label' => __( 'Type de préOrientation' ), 'type' => 'select', 'options' => $modeles, 'empty' => true ) );
				}
				else {
					echo $this->Form->input( 'Filtre.typeorient', array( 'label' => __( 'Type d\'orientation' ), 'type' => 'select', 'options' => $modeles, 'empty' => true ) );
					if( Configure::read( 'Cg.departement' ) == 93 ) {
						echo $this->Form->input( 'Filtre.origine', array( 'label' => __d( 'orientstruct', 'Orientstruct.origine' ), 'type' => 'select', 'options' => $options['Orientstruct']['origine'], 'empty' => true ) );
					}
				}
			}
		?>
	</fieldset>
	<?php
		if( !is_null($etatdosrsa)) {
			echo $this->Search->etatdosrsa($etatdosrsa);
		}
	?>

	<?php
		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Filtre' );
		echo $this->Search->paginationNombretotal( 'Filtre.paginationNombreTotal' );
		echo $this->Search->observeDisableFormOnSubmit( 'Filtre' );
	?>

	<div class="submit">
		<?php echo $this->Form->button( 'Filtrer', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>