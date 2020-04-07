<?php

	echo '<fieldset id="creance" class="col6" ><h2>'. __m('Titrecreancier::view::titleCreance').'</h2>';
	/* Section Creancier */
	if( empty( $creance ) ) {
			echo '<p class="notice">Aucun creance trouvée.</p>';
	}else{
		echo $this->Default3->index(
			array($creance),
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
	}
	echo '</fieldset>';

	echo '<fieldset id="titrecreancier" class="col6" ><h2>'. __m('/Titrescreanciers/view/:heading').'</h2>';
	echo '<table class="titrecreancier" ><tr><td>';
	echo '<fieldset  id="titrecreancier" class="col6" ><h2>'. __m('Titrecreancier::view::titleTitrecreancier').'</h2>';
	echo $this->Default3->view(
		$this->request->data,
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
	echo '</td><td>';
	echo '<fieldset  id="titrecreancier_conjoint" class="col6" ><h3>'. __m('Titrecreancier::view::creanceCouple').'</h3>';
	if ($this->request->data['Titrecreancier']['cjtactif'] == 1 ){
		echo $this->Default3->view(
			$this->request->data,
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
	echo '<fieldset  id="titrecreancier_adresse" class="col6" ><h3>'. __m('Titrecreancier::view::titleAdresse').'</h3>';
	echo $this->Default3->view(
		$this->request->data,
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
	echo '</td></tr></table>';
	echo '</fieldset> <br><br>';
