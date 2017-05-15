<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'categorieactionrole', "Categoriesactionroles::{$this->action}" )
	);

	// occurences renvoi false si il n'y a pas de modèle liés autrement que en belongsTo, on s'assure qu'un false/null soit à 0
	foreach ($actionroles as $key => $value) {
		$actionroles[$key]['Categorieactionrole']['occurences'] = (int)Hash::get($value, 'Categorieactionrole.occurences');
	}
	
	echo $this->Default2->index(
		$actionroles,
		array(
			'Categorieactionrole.name',
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Categoriesactionroles::edit',
				'Categoriesactionroles::delete' => array( 'disabled' => '\'#Categorieactionrole.occurences#\'!= "0"' )
			),
			'add' => 'Categoriesactionroles::add',
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