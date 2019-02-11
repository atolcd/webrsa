<h1>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->pageTitle = 'Ajout d\'un participant à l\'EP';
?>
</h1>

<?php
	if ( !empty( $listeParticipants ) ) {
		echo $this->Default->form(
			array(
				'EpMembreep.ep_id' => array( 'type' => 'hidden', 'value' => $ep_id ),
				'EpMembreep.membreep_id' => array( 'required' => true, 'type' => 'select', 'options' => $listeParticipants )
			)
		);
	}
	else {
		echo $this->Xhtml->tag(
			'p',
			'Aucun participant pour cette fonction ne peut être ajouté.',
			array( 'class' => 'notice' )
		);
	}

	if ( $ep_id == 0 ) {
		echo $this->Default->button(
			'back',
			array(
				'controller' => 'eps',
				'action'     => 'add'
			),
			array(
				'id' => 'Back'
			)
		);
	}
	else {
		echo $this->Default->button(
			'back',
			array(
				'controller' => 'eps',
				'action'     => 'edit',
				$ep_id
			),
			array(
				'id' => 'Back'
			)
		);
	}

?>
