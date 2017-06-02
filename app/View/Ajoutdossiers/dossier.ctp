<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php $this->pageTitle = __d( 'ajoutdossier', "Ajoutdossiers::{$this->action}" );?>
<?php echo $this->Form->create('Ajoutdossiers',array('id'=>'SignupForm' ) );?>
	<h1>Insertion d'une nouvelle demande de RSA</h1>
	<h2>Étape 4: Dossier RSA</h2>
	<?php echo $this->Form->input( 'Dossier.numdemrsatemp', array( 'label' => 'Génération automatique d\'un N° de demande RSA temporaire', 'type' => 'checkbox' ) );?>
	<?php echo $this->Form->input( 'Dossier.numdemrsa', array( 'label' => required( 'Numéro de dossier' ) ) );?>
	<?php echo $this->Form->input( 'Dossier.matricule', array( 'label' => required( __d( 'dossier', 'Dossier.matricule' ) ) ) );?>
	<?php echo $this->Form->input( 'Dossier.fonorg', array( 'label' => required( 'Organisme gérant le dossier' ), 'type' => 'select', 'options' => $fonorg ) );?>
	<?php echo $this->Form->input( 'Dossier.dtdemrsa', array( 'label' => required( 'Date de demande' ), 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 1 ) );?>

	<?php echo $this->Form->input( 'Detaildroitrsa.oridemrsa', array( 'label' => required( 'Code origine demande Rsa' ), 'type' => 'select', 'options' => $oridemrsa ) );?>

	<?php echo $this->Form->input( 'Ajoutdossier.serviceinstructeur_id', array( 'label' => required( __( 'lib_service' ) ), 'type' => 'select' , 'options' => $typeservice, 'empty' => true ) );?>

	<div class="submit">
		<?php echo $this->Form->submit( '< Précédent', array( 'name' => 'Previous', 'div'=>false ) );?>
		<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
		<?php echo $this->Form->submit( 'Terminer', array( 'div'=>false ) );?>
	</div>
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