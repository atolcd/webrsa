<?php

	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	echo $this->element('default_index');

if( empty( $creances ) ) {
		echo '<p class="notice">Aucun creance trouvée.</p>';
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
				'Creance.mtinicre'
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Creances::index::emptyLabel'),
		)
	);

	//Visualisation des Créances
	if( empty( $titresCreanciers ) ) {
		echo '<p class="notice">Cette creance ne possède pas de Titres creanciers liée</p>';
	}else{
		//debug($creances);
		echo $this->Default3->index(
			$titresCreanciers,
			$this->Translator->normalize(
				array(
					'Titrecreancier.dtemissiontitre',
					'Titrecreancier.numtitr',
					'Titrecreancier.mnttitr',
					'Titrecreancier.type'=> array( 'type' => 'select' ),
					'Titrecreancier.dtvalidation',
					'Titrecreancier.etat'=> array( 'type' => 'select' ),
					'Titrecreancier.mention',
					'Titrecreancier.qual',
					'Titrecreancier.nom',
					'Titrecreancier.nir',
					'Titrecreancier.iban',
					'Titrecreancier.bic',
					'Titrecreancier.titulairecompte',
					'Titrecreancier.numtel',
				)+ WebrsaAccess::links(
					array(
						'/Titrescreanciers/edit/#Titrecreancier.id#',
						'/Titrescreanciers/valider/#Titrecreancier.id#',
						'/Titrescreanciers/filelink/#Titrecreancier.id#',
					)
				)
			),
			array(
				'paginate' => false,
				'options' => $options,
				'empty_label' => __m('Titrecreancier::index::emptyLabel'),
			)
		);
	}
}
?>