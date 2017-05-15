<?php
	// Donne le domain du plus haut niveau de précision (prefix, action puis controller)
	$domain = current(MultiDomainsTranslator::urlDomains());
	$defaultParams = compact('options', 'domain');
	$formName = ucfirst($this->request->params['controller']).ucfirst($this->action).'Form';

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));
	echo $this->FormValidator->generateJavascript();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
	
	echo $this->Default3->DefaultForm->create(null, array('id' => $formName));

/***********************************************************************************
 * Formulaire
/***********************************************************************************/
	
	$inputs = array(
		'Savesearch.name' => array('type' => 'text', 'required' => true)
	);
	
	if ($this->Permissions->check('savesearchs', 'save_group')) {
		$inputs['Savesearch.isforgroup'] = array('type' => 'radio');
	}

	if (Configure::read('Module.Savesearch.mon_menu.enabled')) {
		$inputs['Savesearch.isformenu'] = array('type' => 'radio');
	}
	
	echo '<fieldset>'.$this->Default->subform($inputs,	$defaultParams).'</fieldset>';
	
/***********************************************************************************
 * FIN DU FORMULAIRE
/***********************************************************************************/
	
	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit($formName);