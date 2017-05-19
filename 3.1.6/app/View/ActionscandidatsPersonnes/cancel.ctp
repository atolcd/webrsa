<?php
	$modelClassName = 'ActioncandidatPersonne';
	$domain = "actioncandidat_personne_".Configure::read( 'ActioncandidatPersonne.suffixe' );
?>
<h1> <?php
		echo $this->Xhtml->tag(
			'h1',
			$this->pageTitle = __d( $domain, "ActionscandidatsPersonnes::{$this->action}" )
		);
	?>
</h1>

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<?php
	echo $this->Xform->create();


	echo $this->Default->subform(
		array(
			"{$modelClassName}.id" => array( 'type' => 'hidden' ),
			"{$modelClassName}.personne_id" => array( 'type' => 'hidden' ),
			"{$modelClassName}.referent_id" => array( 'type' => 'hidden' ),
			"{$modelClassName}.actioncandidat_id" => array( 'type' => 'hidden' ),
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