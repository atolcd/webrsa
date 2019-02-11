<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<?php  echo $this->Form->create( 'Dossiersimplifie',array('novalidate' => true) ); ?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		// Masquage des champs select si Statut = non orienté
		observeDisableFieldsOnValue( 'Orientstruct0StatutOrient', [ 'Orientstruct0TypeorientId', 'Orientstruct0StructurereferenteId','Orientstruct0ReferentorientantId', 'Orientstruct0StructureorientanteId'  ], 'Non orienté', true );
		observeDisableFieldsOnValue(
			'CalculdroitrsaToppersdrodevorsa',
			[
				'Orientstruct0StatutOrient',
				'Orientstruct0TypeorientId',
				'Orientstruct0StructurereferenteId',
				'Orientstruct0ReferentorientantId',
				'Orientstruct0StructureorientanteId',
				'ButtonSubmit'
			],
			['',1],
			false
		);

	});
</script>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		dependantSelect( 'Orientstruct0ReferentorientantId', 'Orientstruct0StructureorientanteId' );
		dependantSelect( 'Orientstruct0StructurereferenteId', 'Orientstruct0TypeorientId' );
	});
</script>

<h1><?php echo $this->pageTitle = 'Edition d\'une préconisation d\'orientation'; ?></h1>
<fieldset>
	<h2>Dossier RSA</h2>
	<p><?php echo "Numéro de demande RSA : $numdossierrsa";?></p>
	<p><?php
		echo $this->Form->input(
			'Dossier.dtdemrsa',
			array( 'label' => ValidateAllowEmptyUtility::label( 'Dossier.dtdemrsa', 'dossiers' ),
				'dateFormat' => 'DMY',
				'maxYear' => date( 'Y' )+1,
				'minYear' => 2009,
				'empty' => true
			)
		);
	?></p>
	<p><?php echo "N° CAF : $matricule";?></p>
</fieldset>
<fieldset>
	<h2>Personne orientée</h2>
	<div><?php echo $this->Form->input( 'Prestation.id', array( 'label' => false, 'type' => 'hidden') );?></div>
	<div><?php echo $this->Form->input( 'Prestation.personne_id', array( 'label' => false, 'type' => 'hidden') );?></div>
	<div><?php echo $this->Form->input( 'Prestation.natprest', array( 'label' => false, 'value' => 'RSA', 'type' => 'hidden') );?></div>

	<?php echo $this->Form->input( 'Prestation.rolepers', array( 'label' => required( __d( 'prestation', 'Prestation.rolepers' ) ), 'type' => 'select', 'options' => $rolepers, 'empty' => true ) );?>


	<div><?php echo $this->Form->input( 'Personne.id', array( 'label' => required( __( 'id' ) ), 'value' => $personne_id , 'type' => 'hidden') );?></div>
	<?php echo $this->Form->input( 'Personne.qual', array( 'label' => required( __d( 'personne', 'Personne.qual' ) ), 'type' => 'select', 'options' => $qual, 'empty' => true ) );?>
	<?php echo $this->Form->input( 'Personne.nom', array( 'label' => required( __d( 'personne', 'Personne.nom' ) ) ) );?>
	<?php echo $this->Form->input( 'Personne.nomnai', array( 'label' => __d( 'personne', 'Personne.nomnai' ) ) );?>
	<?php echo $this->Form->input( 'Personne.prenom', array( 'label' => required( __d( 'personne', 'Personne.prenom' ) ) ) );?>
	<?php echo $this->Form->input( 'Personne.nir', array( 'label' =>  __d( 'personne', 'Personne.nir' ) ) );?>
	<?php echo $this->Form->input( 'Personne.dtnai', array( 'label' => required( __d( 'personne', 'Personne.dtnai' ) ), 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => ( date( 'Y' ) - 100 ), 'empty' => true ) );?>
	<div><?php echo $this->Form->input( 'Calculdroitrsa.id', array( 'label' => false, 'type' => 'hidden') );?></div>
	<?php echo $this->Form->input( 'Calculdroitrsa.toppersdrodevorsa', array(  'label' =>  required( __d( 'calculdroitrsa', 'Calculdroitrsa.toppersdrodevorsa' ) ), 'options' => $toppersdrodevorsa, 'type' => 'select', 'empty' => 'Non défini'  ) );?>
</fieldset>
<fieldset>
	<h3>Orientation</h3>
	<div><?php echo $this->Form->input( 'Orientstruct.0.personne_id', array( 'label' => false, 'type' => 'hidden') );?></div>
	<div><?php echo $this->Form->input( 'Orientstruct.0.origine', array( 'label' => false, 'type' => 'hidden', 'value' => 'manuelle' ) );?></div>
	<?php
		if( Configure::read( 'Cg.departement' ) == 66 ){
			echo '<fieldset><legend>Orienté par</legend>';
				$this->request->data['Orientstruct'][0]['referentorientant_id'] = Set::classicExtract( $this->request->data, 'Orientstruct.0.structureorientante_id' ).'_'.Set::classicExtract( $this->request->data, 'Orientstruct.0.referentorientant_id' );

				echo $this->Form->input( 'Orientstruct.0.structureorientante_id', array( 'label' =>  'Structure', 'type' => 'select', 'selected' => $structureorientante_id, 'options' => $structuresorientantes, 'empty' => true ) );
				echo $this->Form->input( 'Orientstruct.0.referentorientant_id', array( 'label' =>  'Nom du professionnel', 'type' => 'select', 'selected' => $this->request->data['Orientstruct'][0]['referentorientant_id'], 'options' => $refsorientants, 'empty' => true ) );
			echo '</fieldset>';
		}
	?>
	<?php echo $this->Form->input( 'Orientstruct.0.statut_orient', array( 'label' => "Statut de l'orientation", 'type' => 'select' , 'options' => $statut_orient, 'empty' => true ) );?>
	<?php echo $this->Form->input( 'Orientstruct.0.typeorient_id', array( 'label' => "Type d'orientation / Type de structure",'type' => 'select', 'selected'=> $orient_id, 'options' => $typesOrient, 'empty'=>true));?>
	<?php $this->request->data['Orientstruct'][0]['structurereferente_id'] = Set::classicExtract( $this->request->data, 'Orientstruct.0.typeorient_id' ).'_'.Set::classicExtract( $this->request->data, 'Orientstruct.0.structurereferente_id' ); ?>
	<?php echo $this->Form->input( 'Orientstruct.0.structurereferente_id', array( 'label' => __d( 'structurereferente', 'Structurereferente.structure_referente_'.Configure::read( 'nom_form_ci_cg' ) ), 'type' => 'select', 'selected' => $this->request->data['Orientstruct'][0]['structurereferente_id'], 'options' => $structures, 'empty' => true ) );?>
</fieldset>

<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false, 'id' => 'ButtonSubmit' ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>
<?php
	echo $this->Observer->disableFieldsOnValue(
		'Personne.qual',
		'Personne.nomnai',
		'MME',
		false
	);
?>