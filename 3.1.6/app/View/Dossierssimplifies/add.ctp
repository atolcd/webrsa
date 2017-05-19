<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<?php  echo $this->Form->create( 'Dossiersimplifie',array() ); ?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		// Masquage des champs select si Statut = non orienté
		observeDisableFieldsOnValue( 'Orientstruct0StatutOrient', [ 'Orientstruct0TypeorientId', 'Orientstruct0StructurereferenteId', 'Orientstruct0StructureorientanteId', 'Orientstruct0ReferentorientantId' ], 'Non orienté', true );
		observeDisableFieldsOnValue( 'Orientstruct1StatutOrient', [ 'Orientstruct1TypeorientId', 'Orientstruct1StructurereferenteId', 'Orientstruct1StructureorientanteId', 'Orientstruct1ReferentorientantId'  ], 'Non orienté', true );
		// Masquage des champs select si non droit et devoir
		observeDisableFieldsOnValue( 'Calculdroitrsa0Toppersdrodevorsa', [ 'Orientstruct0TypeorientId', 'Orientstruct0StructurereferenteId', 'Orientstruct0StatutOrient', 'Orientstruct0StructureorientanteId', 'Orientstruct0ReferentorientantId' ], ['',1], false );
		observeDisableFieldsOnValue( 'Calculdroitrsa1Toppersdrodevorsa', [ 'Orientstruct1TypeorientId', 'Orientstruct1StructurereferenteId', 'Orientstruct1StatutOrient', 'Orientstruct1StructureorientanteId', 'Orientstruct1ReferentorientantId'  ], ['',1], false );
	});
</script>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		dependantSelect( 'Orientstruct0ReferentorientantId', 'Orientstruct0StructureorientanteId' );
		dependantSelect( 'Orientstruct1ReferentorientantId', 'Orientstruct1StructureorientanteId' );
		dependantSelect( 'Orientstruct0StructurereferenteId', 'Orientstruct0TypeorientId' );
		dependantSelect( 'Orientstruct1StructurereferenteId', 'Orientstruct1TypeorientId' );
	});
</script>

<h1><?php echo $this->pageTitle = 'Ajout d\'une préconisation d\'orientation'; ?></h1>

		<fieldset>
			<h2>Dossier RSA</h2>
			<?php echo $this->Form->input( 'Dossier.numdemrsatemp', array( 'label' => 'Génération automatique d\'un N° de demande RSA temporaire', 'type' 	=> 'checkbox' ) );?>
			<?php echo $this->Form->input( 'Dossier.numdemrsa', array( 'label' => required( 'Numéro de demande RSA' ) ) );?>
			<?php echo $this->Form->input( 'Dossier.dtdemrsa', array( 'empty' => ( Configure::read( 'Cg.departement') == 66 ), 'label' => required( 'Date de demande' ), 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 1 ) );?>
			<?php echo $this->Form->input( 'Dossier.matricule', array( 'label' => __d( 'dossier', 'Dossier.matricule' ) ) );?>
			<?php echo $this->Form->input( 'Dossier.fonorg', array( 'label' => required( 'Organisme gérant le dossier' ), 'type' => 'select', 'options' => $fonorg ) );?>
			<div><?php echo $this->Form->input( 'Foyer.id', array( 'label' => required( __( 'id' ) ), 'type' => 'hidden') );?></div>
		</fieldset>
		<fieldset>
			<h2>Personne à orienter</h2>
			<div><?php echo $this->Form->input( 'Prestation.0.natprest', array( 'label' => false, 'value' => 'RSA', 'type' => 'hidden') );?></div>

			<?php echo $this->Form->input( 'Prestation.0.rolepers', array( 'label' => required( __d( 'prestation', 'Prestation.rolepers' ) ), 'type' => 'select', 'options' => $rolepers, 'empty' => true ) );?>

			<div><?php echo $this->Form->input( 'Personne.0.id', array( 'label' => required( __( 'id' ) ),  'type' => 'hidden') );?></div>
			<?php echo $this->Form->input( 'Personne.0.qual', array( 'label' => required( __d( 'personne', 'Personne.qual' ) ), 'type' => 'select', 'options' => $qual, 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Personne.0.nom', array( 'label' => required( __d( 'personne', 'Personne.nom' ) ) ) );?>
			<?php echo $this->Form->input( 'Personne.0.nomnai', array( 'label' => __d( 'personne', 'Personne.nomnai' ) ) );?>
			<?php echo $this->Form->input( 'Personne.0.prenom', array( 'label' => required( __d( 'personne', 'Personne.prenom' ) ) ) );?>
			<?php echo $this->Form->input( 'Personne.0.nir', array( 'label' => ( __d( 'personne', 'Personne.nir' ) ) ) );?>
			<?php echo $this->Form->input( 'Personne.0.dtnai', array( 'label' => required( __d( 'personne', 'Personne.dtnai' ) ), 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => ( date( 'Y' ) - 100 ), 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Calculdroitrsa.0.toppersdrodevorsa', array(  'label' =>  required( __d( 'calculdroitrsa', 'Calculdroitrsa.toppersdrodevorsa' ) ), 'options' => $toppersdrodevorsa, 'type' => 'select', 'empty' => 'Non défini'  ) );?>
		</fieldset>
		<fieldset>
			<h3>Orientation</h3>
			<div><?php echo $this->Form->input( 'Orientstruct.0.origine', array( 'label' => false, 'type' => 'hidden', 'value' => 'manuelle' ) );?></div>
			<?php
				if( Configure::read( 'Cg.departement' ) == 66 ){
					echo '<fieldset><legend>Orienté par</legend>';
					echo $this->Form->input( 'Orientstruct.0.structureorientante_id', array( 'label' =>  'Structure', 'type' => 'select', 'options' => $structsReferentes, 'empty' => true ) );
					echo $this->Form->input( 'Orientstruct.0.referentorientant_id', array( 'label' =>  'Nom du professionnel', 'type' => 'select', 'options' => $refsorientants, 'empty' => true ) );
					echo '</fieldset>';
				}
			?>
			<?php echo $this->Form->input( 'Orientstruct.0.statut_orient', array( 'label' => "Statut de l'orientation", 'type' => 'select' , 'options' => $statut_orient, 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Orientstruct.0.typeorient_id', array( 'label' => "Type d'orientation / Type de structure", 'type' => 'select' , 'options' => $options, 'empty' => true ) );?>
		<!--  <?php echo $this->Form->input( 'Typeorient.0.parent_id', array( 'label' =>  __d( 'structurereferente', 'Structurereferente.lib_type_orient' ), 'type' => 'select', 'options' => $typesOrient, 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Orientstruct.0.typeorient_id', array( 'label' => __d( 'structurereferente', 'Structurereferente.lib_struc' ), 'type' => 'select', 'options' => $typesStruct, 'empty' => true ) );?> -->
			<?php echo $this->Form->input( 'Orientstruct.0.structurereferente_id', array( 'label' =>  __d( 'structurereferente', 'Structurereferente.structure_referente_'.Configure::read( 'nom_form_ci_cg' ) ), 'type' => 'select', 'options' => $structsReferentes, 'empty' => true ) );?>
		</fieldset>
		<fieldset>
			<h2>Autre personne à orienter (le cas échéant)</h2>
			<div><?php echo $this->Form->input( 'Prestation.1.natprest', array( 'label' => false, 'value' => 'RSA', 'type' => 'hidden') );?></div>
			<!-- <div><?php echo $this->Form->input( 'Prestation.1.rolepers', array( 'label' => false, 'value' => 'CJT', 'type' => 'hidden') );?></div> -->

			<?php echo $this->Form->input( 'Prestation.1.rolepers', array( 'label' => __d( 'prestation', 'Prestation.rolepers' ), 'type' => 'select', 'options' => $rolepers , 'empty' => true ) );?>

			<div><?php  echo $this->Form->input( 'Personne.1.id', array( 'label' => required( __( 'id' ) ), 'type' => 'hidden') );?></div>
			<?php echo $this->Form->input( 'Personne.1.qual', array( 'label' =>  __d( 'personne', 'Personne.qual' ) , 'type' => 'select', 'options' => $qual, 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Personne.1.nom', array( 'label' =>  __d( 'personne', 'Personne.nom' )  ) );?>
			<?php echo $this->Form->input( 'Personne.1.nomnai', array( 'label' =>  __d( 'personne', 'Personne.nomnai' )  ) );?>
			<?php echo $this->Form->input( 'Personne.1.prenom', array( 'label' =>  __d( 'personne', 'Personne.prenom' ) ) );?>
			<?php echo $this->Form->input( 'Personne.1.nir', array( 'label' =>  __d( 'personne', 'Personne.nir' ) ) );?>
			<?php echo $this->Form->input( 'Personne.1.dtnai', array( 'label' =>  __d( 'personne', 'Personne.dtnai' ), 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => ( date( 'Y' ) - 100 ), 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Calculdroitrsa.1.toppersdrodevorsa', array(  'label' =>   __d( 'calculdroitrsa', 'Calculdroitrsa.toppersdrodevorsa' ), 'options' => $toppersdrodevorsa, 'type' => 'select', 'empty' => 'Non défini'  ) );?>
		</fieldset>
		<fieldset>
			<h3>Orientation</h3>
			<div><?php echo $this->Form->input( 'Orientstruct.1.origine', array( 'label' => false, 'type' => 'hidden', 'value' => 'manuelle' ) );?></div>
			<?php
				if( Configure::read( 'Cg.departement' ) == 66 ){
					echo '<fieldset><legend>Orienté par</legend>';
					echo $this->Form->input( 'Orientstruct.1.structureorientante_id', array( 'label' =>  'Structure', 'type' => 'select', 'options' => $structsReferentes, 'empty' => true ) );
					echo $this->Form->input( 'Orientstruct.1.referentorientant_id', array( 'label' =>  'Nom du professionnel', 'type' => 'select', 'options' => $refsorientants, 'empty' => true ) );
					echo '</fieldset>';
				}
			?>
			<?php echo $this->Form->input( 'Orientstruct.1.statut_orient', array( 'label' => "Statut de l'orientation", 'type' => 'select' , 'options' => $statut_orient, 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Orientstruct.1.typeorient_id', array( 'label' => "Type d'orientation / Type de structure", 'type' => 'select' , 'options' => $options, 'empty' => true ) );?>
		<!--  <?php echo $this->Form->input( 'Typeorient.1.parent_id', array( 'label' =>  __d( 'structurereferente', 'Structurereferente.lib_type_orient' ), 'type' => 'select', 'options' => $typesOrient, 'empty' => true ) );?>
			<?php echo $this->Form->input( 'Orientstruct.1.typeorient_id', array( 'label' => __d( 'structurereferente', 'Structurereferente.lib_struc' ), 'type' => 'select', 'options' => $typesStruct, 'empty' => true ) );?> -->
			<?php echo $this->Form->input( 'Orientstruct.1.structurereferente_id', array( 'label' =>  __d( 'structurereferente', 'Structurereferente.structure_referente_'.Configure::read( 'nom_form_ci_cg' ) ), 'type' => 'select', 'options' => $structsReferentes, 'empty' => true ) );?>
		</fieldset>

		<?php echo $this->Form->submit( 'Enregistrer' );?>
	<?php echo $this->Form->end();?>
    <script type="text/javascript">
	    observeDisableFieldsOnCheckbox(
			'DossierNumdemrsatemp',
			[
				'DossierNumdemrsa'
			],
			true
	    );
    </script>
	<?php
		echo $this->Observer->disableFieldsOnValue(
			'Personne.0.qual',
			'Personne.0.nomnai',
			'MME',
			false
		);
		echo $this->Observer->disableFieldsOnValue(
			'Personne.1.qual',
			'Personne.1.nomnai',
			'MME',
			false
		);
	?>
<div class="clearer"><hr /></div>