<?php
	$modelClassName = 'Decisiondossierpcg66';
	$domain = "decisiondossierpcg66";

	$this->pageTitle = __d( $domain, "Decisionsdossierspcgs66::{$this->action}" );

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
			"{$modelClassName}.dossierpcg66_id" => array( 'type' => 'hidden' ),
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