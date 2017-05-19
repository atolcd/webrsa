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
			array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Cohortecomiteapre' ).toggle(); return false;" )
		).'</li></ul>';
	}

	echo $this->Xform->create( 'Cohortecomiteapre', array( 'id' => 'Cohortecomiteapre', 'class' => ( !empty( $this->request->data ) ? 'folded' : 'unfolded' ) ) );
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'CohortecomiteapreDatecomite', $( 'CohortecomiteapreDatecomiteFromDay' ).up( 'fieldset' ), false );
	});
</script>

<fieldset class= "noprint">
		<legend>Recherche Comités</legend>
		<?php echo $this->Xform->input( 'Cohortecomiteapre.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>
			<?php echo $this->Xform->input( 'Cohortecomiteapre.id', array( 'label' => 'Intitulé du comité','empty' => true, 'options' => $comitesapre ) );?>
			<?php echo $this->Xform->input( 'Cohortecomiteapre.datecomite', array( 'label' => 'Filtrer par date de comités', 'type' => 'checkbox' ) );?>
			<fieldset>
				<legend>Date de saisie du comité</legend>
				<?php
					$datecomite_from = Set::check( $this->request->data, 'Cohortecomiteapre.datecomite_from' ) ? Set::extract( $this->request->data, 'Cohortecomiteapre.datecomite_from' ) : strtotime( '-1 week' );
					$datecomite_to = Set::check( $this->request->data, 'Cohortecomiteapre.datecomite_to' ) ? Set::extract( $this->request->data, 'Cohortecomiteapre.datecomite_to' ) : strtotime( 'now' );
				?>
				<?php echo $this->Xform->input( 'Cohortecomiteapre.datecomite_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $datecomite_from ) );?>
				<?php echo $this->Xform->input( 'Cohortecomiteapre.datecomite_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $datecomite_to ) );?>
			</fieldset>
	</fieldset>
	<div class="submit noprint">
		<?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Xform->end();?>
