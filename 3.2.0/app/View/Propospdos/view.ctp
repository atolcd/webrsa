<?php
	echo $this->Default3->titleForLayout();

	echo $this->Default3->actions(
		array(
			"/Propospdos/edit/{$pdo['Propopdo']['id']}" => array(
				'title' => false
			)
		)
	);

	echo $this->Default3->view(
		$pdo,
		$this->Translator->normalize(
			array(
				'Structurereferente.lib_struc',
				'Typepdo.libelle',
				'Propopdo.datereceptionpdo',
				'Originepdo.libelle',
				'Decisionpropopdo.0.Decisionpdo.libelle',
				'Propopdo.motifpdo',
				'Decisionpropopdo.0.datedecisionpdo',
				'Decisionpropopdo.0.commentairepdo',
				'Propopdo.iscomplet',
			)
		),
		array(
			'th' => true,
			'options' => $options
		)
	);

	echo "<br/><h2>Pièces jointes</h2>";
	echo $this->Fileuploader->results( (array)Hash::get( $pdo, 'Fichiermodule' ) );

	echo $this->Default3->actions(
		array(
			"/Propospdos/index/{$personne_id}" => array(
				'class' => 'back',
				'title' => false
			)
		)
	);
?>