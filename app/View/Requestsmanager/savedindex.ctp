<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'requestmanager', "Requestsmanager::{$this->action}" )
	);

	// occurences renvoi false si il n'y a pas de modèle liés autrement que en belongsTo, on s'assure qu'un false/null soit à 0
	foreach ($requestlist as $key => $value) {
		$requestlist[$key]['Requestmanager']['occurences'] = (int)Hash::get($value, 'Requestmanager.occurences');
	}

	echo $this->Default3->actions(
		array(
			"/Requestsmanager/index" => array(
				'disabled' => !$this->Permissions->check( 'Requestsmanager', 'index' ),
				'class' => 'add'
			),
		)
	);

	echo $this->Default2->index(
		$requestlist,
		array(
			'Requestmanager.name',
			'Requestmanager.requestgroup_id',
			'Requestmanager.actif' => array( 'type' => 'boolean' )
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Requestsmanager::edit',
				'Requestsmanager::delete' => array( 'disabled' => '\'#Requestmanager.occurences#\'!= "0"' )
			),
			'options' => $options
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'parametrages',
			'action'     => 'index',
			'#'     => 'requestsmanager'
		),
		array(
			'id' => 'Back'
		)
	);
?>