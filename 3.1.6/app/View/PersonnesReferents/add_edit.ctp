<?php
	$departement = Configure::read( 'Cg.departement' );
	$this->pageTitle = $departement == 93 ? 'Personne chargée du suivi' : 'Référents liés à la personne';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		dependantSelect( 'PersonneReferentReferentId', 'PersonneReferentStructurereferenteId' );
	} );
</script>

	<h1><?php echo $this->pageTitle;?></h1>
<?php
	echo $this->Xform->create( 'PersonneReferent', array( 'type' => 'post', 'id' => 'PersonneReferentForm' ) );

	if( $this->action == 'edit' ) {
		echo '<div>'.$this->Xform->input( 'PersonneReferent.id', array( 'type' => 'hidden' ) ).'</div>';
	}
?>

	<fieldset>
		<legend><?php echo $departement == 93 ? 'Structure de suivi' : 'Structures référentes';?></legend>
		<?php
			echo $this->Xform->input( 'PersonneReferent.personne_id', array( 'type' => 'hidden', 'value' => $personne_id ) );

			echo $this->Xform->input( 'PersonneReferent.structurereferente_id', array( 'label' => required( $departement == 93 ? 'Structure de suivi' : 'Structure référente' ), 'type' => 'select' , 'options' => $options['PersonneReferent']['structurereferente_id'], 'empty' => true ) );
			echo $this->Xform->input( 'PersonneReferent.referent_id', array( 'label' => required( $departement == 93 ? 'Personne chargée du suivi' : 'Référents' ), 'type' => 'select' , 'options' => $options['PersonneReferent']['referent_id'], 'empty' => true ) );

			echo $this->Xform->input( 'PersonneReferent.dddesignation', array( 'label' => required( 'Début de désignation' ), 'type' => 'date' , 'dateFormat' => 'DMY' ) );

		?>
	</fieldset>

	<div class="submit">
		<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
		<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
	</div>
<?php
	echo $this->Xform->end();
	echo $this->Observer->disableFormOnSubmit( 'PersonneReferentForm' );
?>