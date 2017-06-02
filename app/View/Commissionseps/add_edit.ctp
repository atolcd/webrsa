<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1>
<?php
	if( $this->action == 'add' ) {
		echo $this->pageTitle = 'Ajout d\'une commission d\'EP';
	}
	else {
		echo $this->pageTitle = 'Modification d\'une commission d\'EP';
	}
?>
</h1>

<?php
	echo $this->Form->create( 'Commissionep', array( 'type' => 'post' ) );

	echo $this->Default->subform(
		array(
			'Commissionep.id' => array('type'=>'hidden'),
			'Commissionep.etatcommissionep' => array('type'=>'hidden'),
			'Commissionep.ep_id' => array( 'type' => 'select' ),
			'Commissionep.name',
			'Commissionep.dateseance' => array( 'dateFormat' => __( 'Locale->dateFormat', true ), 'maxYear' => date('Y')+1, 'minYear' => date('Y')-1,  'timeFormat' => __( 'Locale->timeFormat' ), 'interval'=>15 ), // TODO: à mettre par défaut dans Default2Helper
			'Commissionep.lieuseance',
			'Commissionep.adresseseance',
			'Commissionep.codepostalseance',
			'Commissionep.villeseance'
		),
		array(
			'options' => $options
		)
	);
	if( Configure::read( 'Cg.departement' ) == 93 ){
		echo $this->Default->subform(
			array(
				'Commissionep.chargesuivi',
				'Commissionep.gestionnairebat',
				'Commissionep.gestionnairebada',
			),
			array(
				'options' => $options
			)
		);
	}

	echo $this->Default->subform(
		array(
			'Commissionep.salle',
			'Commissionep.observations' => array('type'=>'textarea')
		),
		array(
			'options' => $options
		)
	);

	echo $this->Form->end( 'Enregistrer' );

	if( $this->action == 'edit')  {
		echo $this->Default->button(
			'back',
			array(
				'controller' => 'commissionseps',
				'action'     => 'view',
				Set::classicExtract( $this->request->data, 'Commissionep.id' )
			),
			array(
				'id' => 'Back'
			)
		);
	}
?>