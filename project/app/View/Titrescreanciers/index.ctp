<?php
	$activateFica = (boolean)Configure::read('Module.Creances.FICA.enabled');

	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	echo $this->element('default_index');

	//Visualisation des Créances
	if( empty( $titresCreanciers ) ) {
		echo '<p class="notice">Cette creance ne possède pas de Titres de recette lié</p>';
	}else{
		echo $this->Default3->index(
			$titresCreanciers,
			$this->Translator->normalize(
				array(
					'Titrecreancier.dtbordereau',
					'Titrecreancier.numbordereau',
					'Titrecreancier.numtitr',
					'Titrecreancier.mntinit',
					'Titrecreancier.soldetitr',
					'Titrecreancier.etatDepuis',
					'Titrecreancier.acommentaire' => array('type' => 'boolean', 'title' => $titresCreanciers[0]['Titrecreancier']['mention'] ),
				)+ WebrsaAccess::links(
					array(
						'/Titrescreanciers/view/#Titrecreancier.id#'
						=> array(
							'class' => 'view',
						),
						'/Titrescreanciers/edit/#Titrecreancier.id#',
						'/Titrescreanciers/comment/#Titrecreancier.id#'
						=> array(
							'class' => 'edit',
						),
						'/Titressuivis/index/#Titrecreancier.id#'
						=> array(
							'class' => 'edit',
						),
						'/Titrescreanciers/avis/#Titrecreancier.id#'
						=> array(
							'class' => 'edit',
						),
						'/Titrescreanciers/valider/#Titrecreancier.id#',
						'/Titrescreanciers/exportfica/#Titrecreancier.id#'
						=> array(
							'condition' => $activateFica,
							'class' => 'view',
						),
						'/Titrescreanciers/retourcompta/#Titrecreancier.id#'
						=> array(
							'class' => 'edit',
						),
						'/Titrescreanciers/delete/#Titrecreancier.id#',
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

	if( isset($histoDeleted) && !empty($histoDeleted)) {
		echo '<br><br> <h2>' . __m('Titrecreancier::index::historyDeleted') .  '</h2>';
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

	echo $this->Xhtml->link(
		'Retour',
		array('controller' => 'creances', 'action' => 'index', $foyer_id)
	);
?>