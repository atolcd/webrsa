<?php
	echo $this->Default3->titleForLayout( $this->request->data );

	echo $this->Default3->DefaultForm->create();

	echo $this->Default3->subform(
		$this->Translator->normalize(
			[
				'Criterealgorithmeorientation.libelle',
				'Criterealgorithmeorientation.type_orient_enfant_id' => array( 'type' => 'select', 'options' => $options['typesorients'], 'empty' => false ),
				'Criterealgorithmeorientation.valeurtag_id' => array( 'type' => 'select', 'options' => $options['tags'],  'empty' => true ),
			]
		)
	);
	if($this->request->data['Criterealgorithmeorientation']['age_min'] != 'false'){
		echo $this->Default3->subform(
			$this->Translator->normalize(
				[
					'Criterealgorithmeorientation.age_min' => array( 'type' => 'select', 'options' => $options['agemin'], 'empty' => false ),
				]
			)
		);
	}
	if($this->request->data['Criterealgorithmeorientation']['age_max'] != 'false'){
		echo $this->Default3->subform(
			$this->Translator->normalize(
				[
					'Criterealgorithmeorientation.age_max' => array( 'type' => 'select', 'options' => $options['agemax'], 'empty' => false ),
				]
			)
		);
	}
	if($this->request->data['Criterealgorithmeorientation']['nb_enfants'] != 'false'){
		echo $this->Default3->subform(
			$this->Translator->normalize(
				[
					'Criterealgorithmeorientation.nb_enfants' => array( 'type' => 'select', 'options' => $options['nbenfants'], 'empty' => false ),
				]
			)
		);
	}
	if($this->request->data['Criterealgorithmeorientation']['nb_mois'] != 'false'){
		echo $this->Default3->subform(
			$this->Translator->normalize(
				[
					'Criterealgorithmeorientation.nb_mois' => array( 'type' => 'select', 'options' => $options['nbmois'], 'empty' => false ),
				]
			)
		);
	}
	echo $this->Default3->subform(
		$this->Translator->normalize(
			[
				'Criterealgorithmeorientation.actif'
			]
		)
	);
	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();