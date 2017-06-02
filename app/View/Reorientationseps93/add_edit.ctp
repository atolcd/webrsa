<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'une réorientation';
	}
	else {
		$this->pageTitle = 'Modification d\'une réorientation';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<h1> <?php echo $this->pageTitle; ?> </h1>

<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		dependantSelect( 'Reorientationep93StructurereferenteId', 'Reorientationep93TypeorientId' );
		try { $( 'Reorientationep93StructurereferenteId' ).onchange(); } catch(id) { }

		dependantSelect( 'Reorientationep93ReferentId', 'Reorientationep93StructurereferenteId' );
		try { $( 'Reorientationep93ReferentId' ).onchange(); } catch(id) { }

		observeDisableFieldsOnValue(
			'Reorientationep93Accordaccueil',
			[ 'Reorientationep93Desaccordaccueil' ],
			'0',
			false
		);
	} );
</script>

<fieldset>
	<legend><?php if( $this->action == 'add' ):?>Ajout d'une réorientation<?php elseif( $this->action == 'edit' ):?>Modification d'une réorientation<?php endif;?></legend>
	<?php
		echo $this->Xform->create();

		echo $this->Default->subform(
			array(
				'Reorientationep93.id' => array( 'type' => 'hidden' ),
				'Reorientationep93.orientstruct_id' => array( 'type' => 'hidden' ),
				'Reorientationep93.typeorient_id' => array( 'label' => 'Type d\'orientation', 'required' => true, 'type' => 'select' ),
				'Reorientationep93.structurereferente_id' => array( 'label' => 'Type de structure', 'type' => 'select' ),
				'Reorientationep93.referent_id' => array( 'label' => 'Nom du référent', 'type' => 'select' ),
				'Reorientationep93.motifreorientep93_id' => array( 'type' => 'select' ),
				'Reorientationep93.commentaire',
				'Reorientationep93.accordaccueil',
				'Reorientationep93.desaccordaccueil',
				'Reorientationep93.accordallocataire',
				'Reorientationep93.urgent',
			),
			array(
				'options' => $options
			)
		);

		echo '<div class="input select"><span class="label">Personne soumise à droits et devoirs ?</span><span class="input">'.( $toppersdrodevorsa ? 'Oui' : 'Non' ).'</span></div>';

		echo $this->Default->subform( array( 'Reorientationep93.datedemande' => array( 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 1 ) ) );

		echo '<div class="input select"><span class="label">Rang d\'orientation</span><span class="input">'.( $nb_orientations + 1 ).'</span></div>';

		echo '<div class="submit">';
		echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
		echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
		echo '</div>';
		echo $this->Form->end();
	?>
</fieldset>