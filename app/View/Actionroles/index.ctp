<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'actionrole', "Actionroles::{$this->action}" )
	);

	// occurences renvoi false si il n'y a pas de modèle liés autrement que en belongsTo, on s'assure qu'un false/null soit à 0
	foreach ($actionroles as $key => $value) {
		$actionroles[$key]['Actionrole']['occurences'] = (int)Hash::get($value, 'Actionrole.occurences');
	}
	
	echo $this->Default2->index(
		$actionroles,
		array(
			'Actionrole.role_id',
			'Actionrole.categorieactionrole_id',
			'Actionrole.name',
			'Actionrole.description',
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Actionroles::edit',
				'Actionroles::delete' => array( 'disabled' => '\'#Actionrole.occurences#\'!= "0"' )
			),
			'add' => 'Actionroles::add',
			'options' => $options
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'dashboards',
			'action'     => 'indexparams'
		),
		array(
			'id' => 'Back'
		)
	);
?>