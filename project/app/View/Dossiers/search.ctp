<?php
	$departement = Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );

	$actions = array();
	if( $departement == 66 || $departement == '99X'  ) {
		if( $this->Permissions->check( 'ajoutdossierscomplets', 'add' ) ) {
			$actions['/Ajoutdossierscomplets/add'] = array( 'class' => 'add', 'domain' => 'dossiers' );
		}
	}
	else {
		if( $this->Permissions->check( 'ajoutdossiers', 'wizard' ) ) {
			$actions['/Ajoutdossiers/wizard'] = array( 'class' => 'add', 'domain' => 'dossiers' );
		}
	}

	if( $this->Permissions->check( 'dossierssimplifies', 'add' ) ) {
		if( $departement != 58 ) {
			$actions['/Dossierssimplifies/add'] = array( 'class' => 'add', 'domain' => 'dossiers' );
		}
	}
?>

<?php $this->start( 'custom_search_filters' );?>
<fieldset>
	<legend>Recherche par parcours de l'allocataire</legend>
	<?php
		echo $this->Xform->input( 'Search.Dsp.natlog', array( 'label' => 'Conditions de logement', 'type' => 'select', 'empty' => true, 'options' => $options['Dsp']['natlog'] ) );
		if( $departement == 58 ) {
			echo $this->Xform->input( 'Search.Activite.act', array( 'label' => __d( 'activite', 'Activite.categoriepro' ), 'type' => 'select', 'empty' => true, 'options' => $options['Activite']['act'] ) );
			echo $this->Form->input( 'Search.Propoorientationcov58.referentorientant_id', array( 'label' => 'Travailleur social chargé de l\'évaluation', 'type' => 'select', 'options' => $options['Propoorientationcov58']['referentorientant_id'], 'empty' => true ) );
			echo $this->Form->input( 'Search.Personne.etat_dossier_orientation', array( 'label' => __d( 'personne', 'Personne.etat_dossier_orientation' ), 'type' => 'select', 'options' => $options['Personne']['etat_dossier_orientation'], 'empty' => true ) );
		}
	?>
</fieldset>

<?php
	if ($departement == 66) {
?>
<fieldset>
	<legend><?php echo __d( 'dossierspcgs66', 'Dossierpcg66.search' ); ?></legend>
	<?php
		echo $this->Xform->input(
			'Search.Dossierpcg66.has_dossierpcg66',
			array(
				'label' => __d( 'dossierspcgs66', 'Search.Dossierpcg66.has_dossierpcg66' ),
				'type' => 'select',
				'empty' => true,
				'options' => array ('Non', 'Oui')
			)
		);
	?>
</fieldset>
<?php
	}
?>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'actions' => $actions,
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);
?>