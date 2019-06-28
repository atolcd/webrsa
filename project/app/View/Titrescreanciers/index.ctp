<?php

	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	echo $this->element('default_index');

	//Visualisation des Créances
	if( empty( $titresCreanciers ) ) {
		echo '<p class="notice">Cette creance ne possède pas de Titres creanciers liée</p>';
	}else{
		echo $this->Default3->index(
			$titresCreanciers,
			$this->Translator->normalize(
				array(
					'Titrecreancier.numtitr',
					'Titrecreancier.dtemissiontitre',
					'Titrecreancier.mnttitr',
					'Titrecreancier.etat',
					'Titrecreancier.dtvalidation',
					'Titrecreancier.mention',
				)+ WebrsaAccess::links(
					array(
						'/Titrescreanciers/view/#Titrecreancier.id#'
						=> array(
							'class' => 'view',
						),
						'/Titrescreanciers/edit/#Titrecreancier.id#',
						'/Titressuivis/index/#Titrecreancier.id#'
						=> array(
							'class' => 'edit',
						),
						'/Titrescreanciers/avis/#Titrecreancier.id#'
						=> array(
							'class' => 'edit',
						),
						'/Titrescreanciers/valider/#Titrecreancier.id#',
						'/Titrescreanciers/retourcompta/#Titrecreancier.id#'
						=> array(
							'class' => 'edit',
						),
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
	echo $this->Xhtml->link(
		'Retour',
		array('controller' => 'creances', 'action' => 'index', $foyer_id)
	);
?>