<?php
	App::uses('WebrsaAccess', 'Utility');
	$controller = $this->params->controller;
	$action = $this->action;
	$modelName = Inflector::singularize(Inflector::camelize($controller));

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));

	echo $this->Default3->actions(WebrsaAccess::actionAdd("/".ucfirst($controller)."/add"));
	
	echo $this->Default3->index(
		$datas,
		$this->Translator->normalize(
			array(
				$modelName.'.name',
				$modelName.'.actif' => array('type' => 'boolean'),
			) + WebrsaAccess::links(
				array(
					'/'.ucfirst($controller).'/edit/#'.$modelName.'.id#',
					'/'.ucfirst($controller).'/delete/#'.$modelName.'.id#' => array(
						'disabled' => "('#$modelName.occurences#' == true)"
					)
				),
				array('regles_metier' => false)
			)
		)
	);
	
	echo $this->Xhtml->link(
		'Retour',
		array('controller' => 'fichedeliaisons', 'action' => 'indexparams')
	);