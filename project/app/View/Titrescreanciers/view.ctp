<?php

	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	// Datas utilisés par Default3->titleForLayout
	$titleData = isset($titleData) ? $titleData : $this->request->data;
	// Params utilisés par Default3->titleForLayout
	$titleParams = isset($titleParams) ? $titleParams : array();
	echo $this->Default3->titleForLayout($titleData, $titleParams);


	// A-t'on des messages à afficher à l'utilisateur ?
	if (!empty($messages)) {
		foreach ($messages as $message => $class) {
			echo $this->Html->tag('p', __m($message), array('class' => "message {$class}"));
		}
	}

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
					'Creance.mtinicre',
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
			echo '<p class="notice">Cet Indus Transféré ne possède pas de Titre de recettes liée</p>';
		}else{
			echo $this->Default3->index(
				$titresCreanciers,
				$this->Translator->normalize(
					array(
						'Titrecreancier.dtemissiontitre',
						'Titrecreancier.numtitr',
						'Titrecreancier.mnttitr',
						'Titrecreancier.type'=> array( 'type' => 'select' ),
						'Titrecreancier.dtvalidation',
						'Titrecreancier.etat',
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
		echo $this->Xhtml->link(
			'Retour',
			array('controller' => 'creances', 'action' => 'index', $foyer_id)
		);

	}
?>