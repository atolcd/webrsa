<?php
	$modelClassName = 'Bilanparcours66';
	$domain = "bilanparcours66";

	$this->pageTitle = __d( $domain, "Bilansparcours66::{$this->action}", true );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<h1><?php echo $this->pageTitle;?></h1>
<?php
	echo $this->Xform->create();

	echo $this->Default->subform(
		array(
			"{$modelClassName}.id" => array( 'type' => 'hidden' ),
			"{$modelClassName}.personne_id" => array( 'type' => 'hidden' ),
			"{$modelClassName}.referent_id" => array( 'type' => 'hidden' ),
			"{$modelClassName}.saisineepparcours" => array( 'type' => 'hidden' ),
			"{$modelClassName}.maintienorientation" => array( 'type' => 'hidden' ),
			"{$modelClassName}.proposition" => array( 'type' => 'hidden' ),
			"{$modelClassName}.maintienorientation" => array( 'type' => 'hidden' ),
			"{$modelClassName}.positionbilan" => array( 'type' => 'hidden', 'value' => 'annule' ),
			"{$modelClassName}.motifannulation" => array( 'type' => 'textarea' ),
		),
		array(
			'domain' => $domain
		)
	);

	echo '<div class="submit">';
	echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
	echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
	echo '</div>';
	echo $this->Xform->end();
?>