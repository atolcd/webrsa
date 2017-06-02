<?php
	echo $this->Default3->titleForLayout();

	echo $this->Default3->actions(
		array(
			"/Thematiquesrdvs/add" => array(
				'disabled' => !$this->Permissions->check( 'Thematiquesrdvs', 'add' )
			),
		)
	);

	echo $this->Default3->index(
		$thematiquesrdvs,
		array(
			'Thematiquerdv.name',
			'Typerdv.libelle',
			'Statutrdv.libelle',
			'Thematiquerdv.linkedmodel',
			'/Thematiquesrdvs/edit/#Thematiquerdv.id#' => array(
				'disabled' => !$this->Permissions->check( 'Thematiquesrdvs', 'edit' )
			),
			'/Thematiquesrdvs/delete/#Thematiquerdv.id#' => array(
				'disabled' => !$this->Permissions->check( 'Thematiquesrdvs', 'delete' ),
				'confirm' => true
			),
		),
		array(
			'options'  => $options
		)
	);

	echo $this->Default3->actions(
		array(
			"/Gestionsrdvs/index" => array(
				'text' => 'Retour',
				'class' => 'back',
				'disabled' => !$this->Permissions->check( 'Gestionsrdvs', 'index' )
			),
		)
	);
?>