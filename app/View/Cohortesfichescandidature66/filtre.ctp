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
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'SearchActioncandidatPersonneDatesignature', $( 'SearchActioncandidatPersonneDatesignatureFromDay' ).up( 'fieldset' ), false );
	});
</script>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		dependantSelect( 'SearchActioncandidatId', 'SearchPartenaireId' );
	});
</script>

<?php echo $this->Xform->create( 'Cohortefichecandidature66', array( 'type' => 'post', 'action' => $this->action, 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>
	<fieldset>
			<?php echo $this->Xform->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) );?>

			<legend>Filtrer par Fiche de candidature</legend>
			<?php
				echo $this->Default2->subform(
					array(
						'Search.Partenaire.codepartenaire' => array( 'type' => 'text', 'label' => __d( 'partenaire', 'Partenaire.codepartenaire' ) ),
						'Search.Partenaire.id' => array( 'label' => __d( 'partenaire', 'Partenaire.libstruc' ), 'type' => 'select', 'options' => $options['partenaires'] ),
						'Search.Actioncandidat.id' => array( 'label' => __d( 'actioncandidat', 'Actioncandidat.name' ), 'type' => 'select', 'options' => $listeactions ),
						'Search.Personne.nom' => array( 'label' => __d( 'personne', 'Personne.nom' ) ),
						'Search.Personne.prenom' => array( 'label' => __d( 'personne', 'Personne.prenom' ) ),
						'Search.Personne.nomnai' => array( 'label' => __d( 'personne', 'Personne.nomnai' ) ),
						'Search.Personne.nir' => array( 'label' => __d( 'personne', 'Personne.nir' ) ),
						'Search.Dossier.matricule' => array( 'label' => __d( 'dossier', 'Dossier.matricule' ) ),
						'Search.Dossier.numdemrsa' => array( 'label' => __d( 'dossier', 'Dossier.numdemrsa' ) ),
						'Search.ActioncandidatPersonne.referent_id' => array(  'label' => __d( 'actioncandidat_personne', 'ActioncandidatPersonne.referent_id' ), 'type' => 'select', 'options' => $options['referents'] ),
						'Search.ActioncandidatPersonne.positionfiche' => array(  'label' => __d( 'actioncandidat_personne', 'ActioncandidatPersonne.positionfiche' ), 'type' => 'select', 'options' => $options['positionfiche'] ),
					),
					array(
						'options' => $options
					)
				);
			?>
		</fieldset>

			<?php echo $this->Xform->input( 'Search.ActioncandidatPersonne.datesignature', array( 'label' => 'Filtrer par date de Fiche de candidature', 'type' => 'checkbox' ) );?>
			<fieldset>
				<legend>Filtrer par période</legend>
				<?php
					$datesignature_from = Set::check( $this->request->data, 'Search.ActioncandidatPersonne.datesignature_from' ) ? Set::extract( $this->request->data, 'Search.Actioncandidat.datesignature_from' ) : strtotime( '-1 week' );
					$datesignature_to = Set::check( $this->request->data, 'Search.ActioncandidatPersonne.datesignature_to' ) ? Set::extract( $this->request->data, 'Search.Actioncandidat.datesignature_to' ) : strtotime( 'now' );
				?>
				<?php echo $this->Xform->input( 'Search.ActioncandidatPersonne.datesignature_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $datesignature_from ) );?>
				<?php echo $this->Xform->input( 'Search.ActioncandidatPersonne.datesignature_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $datesignature_to ) );?>
			</fieldset>

		<?php
			echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );
			echo $this->Search->paginationNombretotal();
			echo $this->Search->observeDisableFormOnSubmit( 'Search' );
		?>

	<div class="submit noprint">
		<?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Xform->end();?>