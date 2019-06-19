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

}
?>