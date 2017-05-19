<?php
	$this->pageTitle = 'Permanences';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Permanence', array( 'type' => 'post' ) );
	}
	else {
		echo $this->Form->create( 'Permanence', array( 'type' => 'post' ) );
		echo $this->Form->input( 'Permanence.id', array( 'label' => false, 'type' => 'hidden' ) );
	}
?>

	<fieldset>
		<?php
			echo $this->Form->input( 'Permanence.libpermanence', array( 'label' => required( __( 'Libellé de la permanence' ) ), 'type' => 'text' ) );
			echo $this->Form->input( 'Permanence.structurereferente_id', array( 'label' => required( __( 'Type de structure liée à la permanence' ) ), 'type' => 'select', 'options' => $sr, 'empty' => true ) );
			echo $this->Form->input( 'Permanence.numtel', array( 'label' => required( __( 'N° téléphone de la permanence' ) ), 'type' => 'text', 'maxlength' => 15 ) );
			echo $this->Form->input( 'Permanence.numvoie', array( 'label' =>  __d( 'adresse', 'Adresse.numvoie' ), 'type' => 'text', 'maxlength' => 15 ) );
			echo $this->Form->input( 'Permanence.typevoie', array( 'label' => required( __d( 'adresse', 'Adresse.libtypevoie' ) ), 'type' => 'select', 'options' => $typevoie, 'empty' => true ) );
			echo $this->Form->input( 'Permanence.nomvoie', array( 'label' => required(  __d( 'adresse', 'Adresse.nomvoie' ) ), 'type' => 'text', 'maxlength' => 50 ) );
			echo $this->Form->input( 'Permanence.compladr', array( 'label' =>  __d( 'adresse', 'Adresse.compladr' ), 'type' => 'text', 'maxlength' => 50 ) );
			echo $this->Form->input( 'Permanence.codepos', array( 'label' => required( __d( 'adresse', 'Adresse.codepos' ) ), 'type' => 'text', 'maxlength' => 5 ) );
			echo $this->Form->input( 'Permanence.ville', array( 'label' => required( __( 'ville' ) ), 'type' => 'text' ) );
			echo $this->Form->input( 'Permanence.actif', array( 'label' => required( __( 'actif' ) ), 'type' => 'radio', 'options' => $options['actif'] ) );
		?>
	</fieldset>

	<div class="submit">
		<?php
			echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
			echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>

<?php echo $this->Form->end();?>