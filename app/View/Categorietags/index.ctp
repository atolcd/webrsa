<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'categorietag', "Categorietags::{$this->action}" )
	);

	// occurences renvoi false si il n'y a pas de modèle liés autrement que en belongsTo, on s'assure qu'un false/null soit à 0
	foreach ($categorietags as $key => $value) {
		$categorietags[$key]['Categorietag']['occurences'] = (int)Hash::get($value, 'Categorietag.occurences');
	}
	
	echo $this->Default2->index(
		$categorietags,
		array(
			'Categorietag.name',
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Categorietags::edit',
				'Categorietags::delete' => array( 'disabled' => '\'#Categorietag.occurences#\'!= "0"' )
			),
			'add' => 'Categorietags::add',
			'options' => $options
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'tags',
			'action'     => 'indexparams'
		),
		array(
			'id' => 'Back'
		)
	);
?>