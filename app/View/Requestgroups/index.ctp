<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'requestgroup', "Requestgroups::{$this->action}" )
	);

	// occurences renvoi false si il n'y a pas de modèle liés autrement que en belongsTo, on s'assure qu'un false/null soit à 0
	foreach ($requestgroups as $key => $value) {
		$requestgroups[$key]['Requestgroup']['occurences'] = (int)Hash::get($value, 'Requestgroup.occurences');
	}
	
	echo $this->Default2->index(
		$requestgroups,
		array(
			'Requestgroup.name',
			'Requestgroup.parent_id',
			'Requestgroup.actif' => array( 'type' => 'boolean' )
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Requestgroups::edit',
				'Requestgroups::delete' => array( 'disabled' => '\'#Requestgroup.occurences#\'!= "0"' )
			),
			'add' => 'Requestgroups::add',
			'options' => $options
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'requestsmanager',
			'action'     => 'indexparams'
		),
		array(
			'id' => 'Back'
		)
	);
?>