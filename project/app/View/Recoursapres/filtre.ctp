<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if( !empty( $this->request->data ) ) {
		echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
			$this->Xhtml->image(
				'icons/application_form_magnify.png',
				array( 'alt' => '' )
			).' Formulaire',
			'#',
			array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Recoursapre' ).toggle(); return false;" )
		).'</li></ul>';
	}
	echo $this->Xform->create( 'Recoursapre', array( 'id' => 'Recoursapre', 'class' => ( !empty( $this->request->data ) ? 'folded' : 'unfolded' ) ) );
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'RecoursapreDatedemandeapre', $( 'RecoursapreDatedemandeapreFromDay' ).up( 'fieldset' ), false );
	});
</script>

	<?php echo $this->Xform->input( 'Recoursapre.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>
	<?php
		echo $this->Search->blocAllocataire();
		echo $this->Search->blocAdresse( $mesCodesInsee, $cantons );
	?>
	<fieldset>
		<legend>Recherche par dossier</legend>
		<?php
			echo $this->Form->input( 'Dossier.numdemrsa', array( 'label' => 'Numéro de demande RSA' ) );
			echo $this->Form->input( 'Dossier.matricule', array( 'label' => __d( 'dossier', 'Dossier.matricule' ), 'maxlength' => 15 ) );

			$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
			echo $this->Form->input( 'Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
			echo $this->Search->etatdosrsa($etatdosrsa);
		?>
	</fieldset>
	<fieldset>
		<legend>Recherche par demande APRE</legend>
		<?php echo $this->Form->input( 'Recoursapre.numeroapre', array( 'label' => 'N° demande APRE ', 'type' => 'text', 'maxlength' => 16 ) );?>
		<?php echo $this->Xform->input( 'Recoursapre.datedemandeapre', array( 'label' => 'Filtrer par date de demande APRE', 'type' => 'checkbox' ) );?>
		<fieldset>
			<legend>Date du demande APRE</legend>
			<?php
				$datedemandeapre_from = Set::check( $this->request->data, 'Recoursapre.datedemandeapre_from' ) ? Set::extract( $this->request->data, 'Recoursapre.datedemandeapre_from' ) : strtotime( '-1 week' );
				$datedemandeapre_to = Set::check( $this->request->data, 'Recoursapre.datedemandeapre_to' ) ? Set::extract( $this->request->data, 'Recoursapre.datedemandeapre_to' ) : strtotime( 'now' );
			?>
			<?php echo $this->Xform->input( 'Recoursapre.datedemandeapre_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $datedemandeapre_from ) );?>
			<?php echo $this->Xform->input( 'Recoursapre.datedemandeapre_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $datedemandeapre_to ) );?>
		</fieldset>
	</fieldset>
	<?php
		$value = Configure::read( 'ResultatsParPage.nombre_par_defaut' );
		if (isset ($this->request->data['Search']['limit'])) {
			$value = $this->request->data['Search']['limit'];
		}

		echo $this->Xform->input(
			"Search.limit",
			array(
				'label' =>  __d( 'search_plugin', "Search.Pagination.resultats_par_page" ),
				'type' => 'radio',
				'options' => Configure::read( 'ResultatsParPage.nombre_de_resultats' ),
				'value' => $value
			)
		);
	?>
	<div class="submit noprint">
		<?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Xform->end();?>