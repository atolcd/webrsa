<?php
	echo $this->Default3->titleForLayout();

	echo $this->Default3->actions(
		array(
			"/Communautessrs/add" => array(
				'disabled' => !$this->Permissions->check( 'Communautessrs', 'add' )
			)
		)
	);

	echo $this->Default3->index(
		$results,
		array(
			'Communautesr.name',
			'Communautesr.actif',
			'/Communautessrs/edit/#Communautesr.id#' => array(
				'disabled' => "( '#Communautesr.id#' == '' || !'".$this->Permissions->check( 'Communautessrs', 'edit' )."' )",
			),
			'/Communautessrs/delete/#Communautesr.id#' => array(
				'disabled' => "( '#Communautesr.occurences#' != false ) || ( !'".$this->Permissions->check( 'Communautessrs', 'delete' )."' )",
				'confirm' => true
			),
		),
		array(
			'options' => $options
		)
	);

	echo $this->Default3->actions(
		array(
			"/Parametrages/index" => array(
				'text' => 'Retour',
				'class' => 'back',
				'disabled' => !$this->Permissions->check( 'Parametrages', 'index' )
			),
		)
	);
?>