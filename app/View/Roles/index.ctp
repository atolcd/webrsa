<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'role', "Roles::{$this->action}" )
	);

	// occurences renvoi false si il n'y a pas de modèle liés autrement que en belongsTo, on s'assure qu'un false/null soit à 0
	foreach ($roles as $key => $value) {
		$roles[$key]['Role']['occurences'] = (int)Hash::get($value, 'Role.occurences');
	}
	
	echo $this->Default2->index(
		$roles,
		array(
			'Role.name',
			'Role.actif' => array( 'type' => 'boolean' )
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Roles::edit',
				'Roles::delete' => array( 'disabled' => '\'#Role.occurences#\'!= "0"' )
			),
			'add' => 'Roles::add',
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