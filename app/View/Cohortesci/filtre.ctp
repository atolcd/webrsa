<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
        echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<?php
	if( Configure::read( 'Cg.departement') == 66 ){
		$complexeparticulier = 'C';
	}
	else{
		$complexeparticulier = 'S';
	}

	?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsOnValue( 'SearchContratinsertionDecisionCi', [ 'SearchContratinsertionDatevalidationCiDay', 'SearchContratinsertionDatevalidationCiMonth', 'SearchContratinsertionDatevalidationCiYear' ], 'V', false );
	});
</script>


<?php
	if( is_array( $this->request->data ) ) {
		echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
			$this->Xhtml->image(
				'icons/application_form_magnify.png',
				array( 'alt' => '' )
			).' Formulaire',
			'#',
			array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Contratinsertion' ).toggle(); return false;" )
		).'</li></ul>';
	}
?>

<?php echo $this->Form->create( 'Search', array( 'id' => 'Contratinsertion', 'class' => ( !empty( $this->request->data ) ? 'folded' : 'unfolded' ) ) );?>
	<?php
		echo $this->Search->blocAllocataire( array(), array(), 'Search' );
		echo $this->Search->blocAdresse( $mesCodesInsee, $cantons, 'Search' );
	?>
	<fieldset>
		<legend>Recherche par dossier</legend>
		<?php
			echo $this->Form->input( 'Search.Dossier.numdemrsa', array( 'label' => 'Numéro de demande RSA' ) );
			echo $this->Form->input( 'Search.Dossier.matricule', array( 'label' => __d( 'dossier', 'Dossier.matricule' ), 'maxlength' => 15 ) );

			$valueDossierDernier = isset( $this->request->data['Search']['Dossier']['dernier'] ) ? $this->request->data['Search']['Dossier']['dernier'] : true;
			echo $this->Form->input( 'Search.Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
			echo $this->Search->etatdosrsa($etatdosrsa, 'Search.Situationdossierrsa.etatdosrsa');
		?>
	</fieldset>
	<fieldset>
		<legend>Recherche de CER</legend>

        <?php
            echo $this->Search->date( 'Search.Contratinsertion.created', 'Date de saisie du contrat' );
        ?>

			<?php echo $this->Form->input( 'Search.Contratinsertion.structurereferente_id', array( 'label' => __d( 'rendezvous', 'Rendezvous.lib_struct' ), 'type' => 'select', 'options' => $struct, 'empty' => true ) ); ?>
			<?php echo $this->Form->input( 'Search.Contratinsertion.referent_id', array( 'label' => __( 'Nom du référent' ), 'type' => 'select', 'options' => $referents, 'empty' => true ) ); ?>
			<?php echo $this->Ajax->observeField( 'SearchContratinsertionStructurereferenteId', array( 'update' => 'SearchContratinsertionReferentId', 'url' => array( 'action' => 'ajaxreferent' ) ) );?>
			<?php
				if( $this->action == 'valides' ) {
					echo $this->Form->input( 'Search.Contratinsertion.decision_ci', array( 'label' => 'Statut du contrat', 'type' => 'select', 'options' => $decision_ci, 'empty' => true ) );
					echo $this->Form->input( 'Search.Contratinsertion.datevalidation_ci', array( 'label' => '', 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+10, 'minYear'=>date('Y')-10 , 'empty' => true)  );
				}
			?>
			<?php
				if( Configure::read( 'Cg.departement' ) != 66 ){
					echo $this->Form->input( 'Search.Contratinsertion.forme_ci', array( 'div' => false, 'type' => 'radio', 'options' => $forme_ci, 'legend' => 'Forme du contrat', 'default' => $complexeparticulier ) );
				}
				else if( $this->action == 'valides' && Configure::read( 'Cg.departement' ) == 66 ){
					echo $this->Form->input( 'Search.Contratinsertion.forme_ci', array( 'div' => false, 'type' => 'radio', 'options' => $forme_ci, 'legend' => 'Forme du contrat' ) );
				}
			?>

	</fieldset>

	<?php
//		echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Contratinsertion' );
        echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );
		echo $this->Search->paginationNombretotal( 'Search.Contratinsertion.nombre_total' );
		echo $this->Search->observeDisableFormOnSubmit( 'Search' );
	?>

	<div class="submit noprint">
		<?php echo $this->Form->button( 'Filtrer', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>