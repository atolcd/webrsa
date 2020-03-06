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

	echo '<fieldset  id="creance" class="col6" ><h2>'. __m('Titrecreancier::view::titleCreance').'</h2>';
	if( empty( $creances ) ) {
		echo '<p class="notice">'.__m('Titrecreancier::index::emptyLabel').'</p>';
	}else{
		echo $this->Default3->view(
			$creances,
			$this->Translator->normalize(
				array(
					'Creance.dtimplcre',
					'Creance.orgcre',
					'Creance.natcre',
					'Creance.rgcre',
					'Creance.etat',
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
				'empty_label' => __m('Titrecreancier::index::emptyLabel'),
			)
		);
	}
	echo '</fieldset>';

	echo '<fieldset  id="titrecreancier" class="col6" ><h2>'. __m('Titrecreancier::view::titleTitrecreancier').'</h2>';
	echo $this->Default3->view(
		$titresCreanciers[0],
		$this->Translator->normalize(
			array(
				'Titrecreancier.dtemissiontitre',
				'Titrecreancier.numtitr',
				'Titrecreancier.mntinit',
				'Titrecreancier.mnttitr',
				'Titrecreancier.typetitrecreancier_id' => array(
					'type' => 'select',
					'options' => $options['Typetitrecreancier']['type']
				),
				'Titrecreancier.etat',
				'Titrecreancier.qual',
				'Titrecreancier.nom',
				'Titrecreancier.nir',
				'Titrecreancier.numtel',
				'Titrecreancier.titulairecompte',
				'Titrecreancier.iban',
				'Titrecreancier.bic',
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Titrecreancier::view::emptyLabel'),
		)
	);
	echo '</fieldset>';

	echo '<fieldset  id="titrecreancier_conjoint" class="col6" ><h3>'. __m('Titrecreancier::view::creanceCouple').'</h3>';
	if ($titresCreanciers[0]['Titrecreancier']['cjtactif'] == 1 ){
		echo $this->Default3->view(
			$titresCreanciers[0],
			$this->Translator->normalize(
				array(
					'Titrecreancier.qualcjt',
					'Titrecreancier.nomcjt',
					'Titrecreancier.nircjt',
				)
			),
			array(
				'paginate' => false,
				'options' => $options,
				'empty_label' => __m('Titrecreancier::view::emptyLabel'),
			)
		);
	}
	echo '</fieldset>';

	echo '<fieldset  id="titrecreancier_mention" class="col6" ><h3>'. __m('Titrecreancier::view::titleMotifEmission').'</h3>';
	echo $this->Default3->view(
		$titresCreanciers[0],
		$this->Translator->normalize(
			array(
				'Titrecreancier.mention',
				'Titrecreancier.motifemissiontitrecreancier_id' => array(
					'options' => $listMotifs
				),
				'Titrecreancier.datemotifemission',
				'Titrecreancier.dtvalidation',
				'Titrecreancier.commentairevalidateur',
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Titrecreancier::view::emptyLabel'),
		)
	);
	echo '</fieldset>';

	echo '<fieldset  id="titrecreancier_adresse" class="col6" ><h3>'. __m('Titrecreancier::view::titleAdresse').'</h3>';
	echo $this->Default3->view(
		$titresCreanciers[0],
		$this->Translator->normalize(
			array(
				'Titrecreancier.dtemm',
				'Titrecreancier.typeadr',
				'Titrecreancier.etatadr',
				'Titrecreancier.complete',
				'Titrecreancier.localite'
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Titrecreancier::view::emptyLabel'),
		)
	);
	echo '</fieldset>';

	echo '<fieldset  id="titrecreancier_bordereau" class="col6" ><h3>'. __m('Titrecreancier::view::titleBordereau').'</h3>';
	echo $this->Default3->view(
		$titresCreanciers[0],
		$this->Translator->normalize(
			array(
				'Titrecreancier.dtbordereau',
				'Titrecreancier.numtitr',
				'Titrecreancier.numbordereau',
				'Titrecreancier.numtier'
			)
		),
		array(
			'paginate' => false,
			'options' => $options,
			'empty_label' => __m('Titrecreancier::view::emptyLabel'),
		)
	);
	echo '</fieldset>';

	if( isset($historique) && !empty($historique)) {
		echo '<br><br> <h1>' . __m('Titrecreancier::view::history') .  '</h1>';
		echo $this->Default3->index(
			$historique,
			$this->Translator->normalize(
				array(
					'Historiqueetat.created' => array('type' => 'date', 'dateFormat' => 'DMY'),
					'Historiqueetat.evenement',
					'Historiqueetat.nom',
					'Historiqueetat.prenom',
					'Historiqueetat.modele',
					'Historiqueetat.etat'
				)
				),
				array('paginate' => false)
		);
	}

	echo $this->Xhtml->link(
		'Retour',
		array('controller' => 'titrescreanciers', 'action' => 'index', $creance_id)
	);

?>