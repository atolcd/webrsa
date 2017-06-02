<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php $this->pageTitle = 'Orientations';?>

<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Orientation';
	}
	else {
		$this->pageTitle = 'Édition de l\'orientation';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		dependantSelect( 'OrientstructStructurereferenteId', 'OrientstructTypeorientId' );
		try { $( 'OrientstructStructurereferenteId' ).onchange(); } catch(id) { }

		dependantSelect( 'OrientstructReferentId', 'OrientstructStructurereferenteId' );
		try { $( 'OrientstructReferentId' ).onchange(); } catch(id) { }
	});
</script>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Orientstruct', array(  'type' => 'post'  ));
		echo '<div>';
		echo $this->Form->input( 'Orientstruct.id', array( 'type' => 'hidden', 'value' => '' ) );
		echo '</div>';
	}
	else {
		echo $this->Form->create( 'Orientstruct', array( 'type' => 'post'  ));
		echo '<div>';
		echo $this->Form->input( 'Orientstruct.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}

	// On s'assure de ne pas perdre l'origine si on est en modification
	echo '<div>';
	$origine = Hash::get( $this->request->data, 'Orientstruct.origine' );
	$origine = ( empty( $origine ) ? 'manuelle' : $origine );
	echo $this->Form->input( 'Orientstruct.origine', array( 'type' => 'hidden', 'value' => $origine ) );
	echo '</div>';

	$typeorient_id = null;
	if( !empty( $this->request->data['Structurereferente']['Typeorient']['id'] ) ) {
		$typeorient_id = $this->request->data['Structurereferente']['Typeorient']['id'];
	}
	$domain = 'orientstruct';
?>

<?php if( Configure::read( 'nom_form_ci_cg' ) == 'cg66' ):?>
<fieldset><legend>Orienté par</legend>
	<script type="text/javascript">
		document.observe("dom:loaded", function() {
			dependantSelect( 'OrientstructReferentorientantId', 'OrientstructStructureorientanteId' );
			try { $( 'OrientstructReferentorientantId' ).onchange(); } catch(id) { }
		});
	</script>

	<?php
		$selected = null;
		if( $this->action == 'edit' ){
			$selected = preg_replace( '/^[^_]+_/', '', $this->request->data['Orientstruct']['structureorientante_id'] ).'_'.$this->request->data['Orientstruct']['referentorientant_id'];
		}

		echo $this->Default2->subform(
			array(
				'Orientstruct.structureorientante_id' => array( 'type' => 'select', 'options' => $structsorientantes, 'required' => true ),
				'Orientstruct.referentorientant_id' => array(  'type' => 'select', 'options' => $refsorientants, 'selected' => $selected, 'required' => true )
			),
			array(
				'options' => $options
			)
		);
	?>
</fieldset>
<?php endif;?>
<fieldset>
	<legend>Ajout d'une orientation</legend>
	<?php
		echo $this->Form->input( 'Orientstruct.typeorient_id', array( 'label' =>  required( __d( 'structurereferente', 'Structurereferente.lib_type_orient' ) ), 'type' => 'select', 'options' => $typesorients, 'empty' => true, 'value' => $typeorient_id ) );

		$selectedtype = Set::classicExtract( $this->request->data, 'Orientstruct.typeorient_id' );
		$selectedstruct = Set::classicExtract( $this->request->data, 'Orientstruct.structurereferente_id' );
		$selectedref = Set::classicExtract( $this->request->data, 'Orientstruct.referent_id' );

		if( !empty( $selectedtype ) && !empty( $selectedstruct ) && ( strpos( $selectedstruct, '_' ) === false ) ) {
			if( !empty( $selectedref ) && ( strpos( $selectedref, '_' ) === false ) ) {
					$selectedref = "{$selectedstruct}_{$selectedref}";
			}
			$selectedstruct = "{$selectedtype}_{$selectedstruct}";
		}

		if( isset( $this->request->data['Calculdroitrsa']['id'] ) ) {
			echo $this->Form->input( 'Calculdroitrsa.id', array(  'label' =>  false, 'type' => 'hidden' ) );
		}
		echo $this->Form->input( 'Orientstruct.statut_orient', array(  'label' =>  false, 'type' => 'hidden', 'value' => 'Orienté' ) );
		echo $this->Form->input( 'Orientstruct.structurereferente_id', array( 'label' => required(__d( 'structurereferente', 'Structurereferente.lib_struc' )), 'type' => 'select', 'options' => $structs, 'empty' => true, 'selected' => $selectedstruct ) );
		echo $this->Form->input( 'Orientstruct.referent_id', array(  'label' => __d( 'structurereferente', 'Structurereferente.nom_referent' ), 'type' => 'select', 'options' => $referents, 'empty' => true, 'selected' => $selectedref ) );
		echo $this->Form->input( 'Calculdroitrsa.toppersdrodevorsa', array(  'label' =>  required( __d( 'calculdroitrsa', 'Calculdroitrsa.toppersdrodevorsa' ) ), 'options' => $toppersdrodevorsa, 'type' => 'select', 'empty' => 'Non défini'  ) );

		$selectedDateDemande = $dossier['Dossier']['dtdemrsa'];
		if( $this->action == 'edit' ){
			$selectedDateDemande = $this->request->data['Orientstruct']['date_propo'];
		}
		echo $this->Form->input( 'Orientstruct.date_propo', array(  'label' =>  required( __d( 'contratinsertion', 'Contratinsertion.date_propo' ) ), 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 3, 'minYear' => ( date( 'Y' ) - 10 ), 'empty' => true, 'selected' => $selectedDateDemande ) );

		echo $this->Form->input( 'Orientstruct.date_valid', array(  'label' =>  required( __d( 'contratinsertion', 'Contratinsertion.date_valid' ) ), 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 3, 'minYear' => ( date( 'Y' ) - 10 ) ) );

		if( Configure::read( 'Cg.departement' ) == 66 ) {
			echo $this->Form->input( 'Orientstruct.typenotification', array(  'label' =>  'Type de notification', 'options' => $options['Orientstruct']['typenotification'], 'type' => 'select', 'empty' => false  ) );
		}
	?>
</fieldset>

	<div class="submit">
		<?php
			echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
			echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>
<?php echo $this->Form->end();?>