<?php
	$activateTitreCreancier = Configure::read( 'Creances.titrescreanciers' );
	if ( !empty($activateTitreCreancier)	&& $activateTitreCreancier == true ){
		$activateTitreCreancier = true;
	}else{
		$activateTitreCreancier = false;
	}

	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	echo $this->element('default_index');
	//Visualisation des Créances
if( empty( $creances ) ) {
	echo '<p class="notice">'.__m('Creances::index::emptyCreance').'</p>';
}else{
	echo $this->Default3->index(
		$creances,
		$this->Translator->normalize(
			array(
				'Creance.dtimplcre',
				'Creance.orgcre',
				'Creance.natcre',
				'Creance.mtsolreelcretrans',
				'Creance.mention',
				'Creance.rgcre',
			)+ WebrsaAccess::links(
				array(
					'/Creances/view/#Creance.id#'
						=> array(
							'class' => 'view',
						),
					'/Creances/edit/#Creance.id#',
					'/Creances/nonemission/#Creance.id#'
						=> array(
							'class' => 'edit',
						),
					'/Creances/validation/#Creance.id#',
					'/Titrescreanciers/index/#Creance.id#'
						=> array(
							'class' => 'view',
							'condition' => $activateTitreCreancier,
						),
					'/Creances/delete/#Creance.id#',
					'/Creances/copycreance/#Creance.id#' => array(
							'class' => 'edit'
					),
					'/Creances/filelink/#Creance.id#',
				)
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Creances::index::emptyLabel'),
		)
	);

	if( isset($histoDeleted) && !empty($histoDeleted)) {
		echo '<br><br> <h2>' . __m('Creance::index::historyDeleted') .  '</h2>';
		echo $this->Default3->index(
			$histoDeleted,
			$this->Translator->normalize(
				array(
					'Historiqueetat.created' => array('type' => 'date', 'dateFormat' => 'DMY'),
					'Historiqueetat.nom',
					'Historiqueetat.prenom' ,
					'Historiqueetat.modele'
				)
				),
				array('paginate' => false)
		);
	}
}
?>