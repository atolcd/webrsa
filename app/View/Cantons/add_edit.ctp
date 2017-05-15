<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$this->pageTitle = $this->pageTitle = 'Canton';

?>
<h1><?php echo $this->pageTitle;?></h1>

<?php
	echo $this->Form->create( 'Canton', array( 'type' => 'post' ) );
	if( $this->action == 'edit' ) {
		echo $this->Form->input( 'Canton.id', array( 'type' => 'hidden' ) );
	}

	echo $this->Form->input( 'Canton.canton', array( 'label' => required( 'Nom du canton' ) ) );
	echo $this->Form->input( 'Canton.zonegeographique_id', array( 'label' => required( 'Zone géographique associée' ), 'empty' => true ) );
	echo $this->Form->input( 'Canton.libtypevoie', array( 'label' => 'Type de voie', 'type' => 'select', 'options' => $libtypesvoies, 'empty' => '' ) );
	echo $this->Form->input( 'Canton.nomvoie', array( 'label' => 'Nom de voie' ) );
	echo $this->Form->input( 'Canton.nomcom', array( 'label' => required( 'Localité' ) ) );
	echo $this->Form->input( 'Canton.codepos', array( 'label' => 'Code postal' ) );
	echo $this->Form->input( 'Canton.numcom', array( 'label' => required( 'Numéro de commune au sens INSEE' ) ) );

	echo '<div class="submit">';
		echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
		echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
	echo '</div>';

	echo $this->Form->end();
?>
