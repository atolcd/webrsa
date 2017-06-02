<?php
	App::uses('WebrsaAccess', 'Utility');
	if (!empty($dossierMenu)) {
		WebrsaAccess::init($dossierMenu);
	}

	// Datas utilisés par Default3->titleForLayout
	$titleData = isset($titleData) ? $titleData : $this->request->data;
	
	// Params utilisés par Default3->titleForLayout
	$titleParams = isset($titleParams) ? $titleParams : array();
	
	// Lien d'ajout (peut être mis à false pour désactiver le lien)
	$addLink = isset($addLink) && $addLink !== true
		? $addLink
		: '/'.Inflector::camelize($this->request->params['controller']).'/add/'.Hash::get($this->request->params, 'pass.0');
	
	// Utilisé avec Default3->actions
	$ajoutPossible = isset($ajoutPossible) ? $ajoutPossible : true;
	
	echo $this->Default3->titleForLayout($titleData, $titleParams);
	
	if (Configure::read('debug') > 0) {
		echo $this->Html->css(array('all.form'), 'stylesheet', array('media' => 'all', 'inline' => false));
	}

	echo $this->element('ancien_dossier');
	
	if ($addLink) {
		echo $this->Default3->actions(
			WebrsaAccess::actionAdd($addLink, $ajoutPossible)
		);
	}

	// A-t'on des messages à afficher à l'utilisateur ?
	if (!empty($messages)) {
		foreach ($messages as $message => $class) {
			echo $this->Html->tag('p', __m($message), array('class' => "message {$class}"));
		}
	}