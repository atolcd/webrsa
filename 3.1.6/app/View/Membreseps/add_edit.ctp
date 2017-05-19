<h1>
<?php
	if( $this->action == 'add' ) {
		echo $this->pageTitle = 'Ajout d\'un membre pour une équipe pluridisciplinaire';
	}
	else {
		echo $this->pageTitle = 'Modification d\'un membre pour une équipe pluridisciplinaire';
	}
?>
</h1>

<?php
	echo $this->Default2->form(
		array(
			'Membreep.fonctionmembreep_id' => array('type'=>'select', 'required' => true),
			'Membreep.qual' => array( 'required' => true ),
			'Membreep.nom' => array( 'required' => true ),
			'Membreep.prenom' => array( 'required' => true ),
			'Membreep.organisme',
			'Membreep.tel',
			'Membreep.mail',
			'Membreep.numvoie',
			'Membreep.typevoie' => array( 'type' => 'select', 'options' => $options['typevoie'] ),
			'Membreep.nomvoie',
			'Membreep.compladr',
			'Membreep.codepostal',
			'Membreep.ville'
		),
		array(
			'id' => 'MembreepAddEditForm',
			'options' => $options
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'membreseps',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>