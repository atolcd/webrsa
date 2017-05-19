<?php
	$this->pageTitle = 'Personne chargée du suivi des Aides APREs';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>

<?php
	echo $this->Xform->create( 'Suiviaideapre', array( 'type' => 'post' ) );

	if( $this->action == 'edit' ) {
		echo '<div>';
		echo $this->Xform->input( 'Suiviaideapre.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
?>

<fieldset>
	<?php echo $this->Xform->input( 'Suiviaideapre.qual', array( 'label' => required( __d( 'personne', 'Personne.qual' ) ), 'type' => 'select', 'options' => $qual, 'empty' => true ) );?>
	<?php echo $this->Xform->input( 'Suiviaideapre.nom', array( 'label' => required( __d( 'personne', 'Personne.nom' ) ), 'type' => 'text' ) );?>
	<?php echo $this->Xform->input( 'Suiviaideapre.prenom', array( 'label' => required( __d( 'personne', 'Personne.prenom' ) ), 'type' => 'text' ) );?>
	<?php echo $this->Xform->input( 'Suiviaideapre.numtel', array( 'label' =>  __( 'numtel' ), 'type' => 'text', 'maxlength' => 10 ) );?>
</fieldset>

<div class="submit">
	<?php
		echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
		echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
	?>
</div>
<?php echo $this->Xform->end();?>

<div class="clearer"><hr /></div>