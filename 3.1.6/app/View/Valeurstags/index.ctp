<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'valeurtag', "Valeurstags::{$this->action}" )
	);

	// occurences renvoi false si il n'y a pas de modèle liés autrement que en belongsTo, on s'assure qu'un false/null soit à 0
	foreach ($valeurstags as $key => $value) {
		$valeurstags[$key]['Valeurtag']['occurences'] = (int)Hash::get($value, 'Valeurtag.occurences');
	}
	
	echo $this->Default2->index(
		$valeurstags,
		array(
			'Valeurtag.name',
			'Valeurtag.categorietag_id',
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Valeurstags::edit',
				'Valeurstags::delete' => array( 'disabled' => '\'#Valeurtag.occurences#\'!= "0"' )
			),
			'add' => 'Valeurstags::add',
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