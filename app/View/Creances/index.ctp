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
	echo '<p class="notice">Cette personne ne possède pas de Créances.</p>';
}else{
	echo $this->Default3->index(
		$creances,
		$this->Translator->normalize(
			array(
				'Creance.dtimplcre',
				'Creance.orgcre',
				'Creance.natcre',
				'Creance.rgcre',
				'Creance.moismoucompta',
				'Creance.motiindu',
				'Creance.oriindu',
				'Creance.respindu',
				'Creance.ddregucre',
				'Creance.dfregucre',
				'Creance.dtdercredcretrans',
				'Creance.mtsolreelcretrans',
				'Creance.mtinicre',
				'Creance.mention'
			)+ WebrsaAccess::links(
				array(
					/*'/Titrescreanciers/index/#Creance.id#'
						=> array(
							'class' => 'view',
							'condition' => $activateTitreCreancier,
						),
					 */
					 /*'/Titrescreanciers/add/#Creance.id#'
						=> array(
							'class' => 'edit',
							'condition' => $activateTitreCreancier,
					),*/
					'/Creances/edit/#Creance.id#',
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