<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'entretien', "Entretiens::{$this->action}" )
	);
?>
<?php
	echo $this->Default->view(
		$entretien,
		array(
			'Entretien.dateentretien' => array( 'type' => 'date' ),
			'Structurereferente.lib_struc' => array( 'type' => 'text' ),
			'Referent.nom_complet' => array( 'type' => 'text' ),
			'Entretien.typeentretien' => array( 'type' => 'text' ),
			'Entretien.typerdv_id' => array( 'type' => 'text' ),
			'Entretien.commentaireentretien' => array( 'type' => 'text' )
		),
		array(
			'options' => $options
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'entretiens',
			'action'     => 'index',
			$personne_id
		),
		array(
			'id' => 'Back'
		)
	);
?>