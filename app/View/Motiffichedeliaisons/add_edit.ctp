<?php
	$controller = $this->params->controller;
	$action = $this->action;
	$modelName = Inflector::singularize(Inflector::camelize($controller));

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));
	echo $this->FormValidator->generateJavascript();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
	
	echo $this->Default3->DefaultForm->create(null, array('novalidate' => 'novalidate', 'id' => ucfirst($controller).ucfirst($action).'Form'));

/***********************************************************************************
 * FORMULAIRE
/***********************************************************************************/
	
	echo '<fieldset>'
		. $this->Default3->subform(
			$this->Translator->normalize(
				array(
					$modelName.'.id' => array('type' => 'hidden'),
					$modelName.'.name',
					$modelName.'.actif' => array('type' => 'checkbox'),
				)
			)
		) . '</fieldset>'
	;
	
/***********************************************************************************
 * FIN DU FORMULAIRE
/***********************************************************************************/
	
	echo $this->Default3->DefaultForm->buttons(array('Save', 'Cancel'));
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit(ucfirst($controller).ucfirst($action).'Form');