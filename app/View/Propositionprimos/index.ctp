<?php
	$controller = $this->params->controller;
	$action = $this->action;
	$modelName = Inflector::singularize(Inflector::camelize($controller));
	$domain = current(WebrsaTranslator::domains());
	$defaultParams = compact('options', 'domain');

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));
	
	if (Configure::read( 'debug' ) > 0) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default3->actions(
		array(
			"/".ucfirst($controller)."/add" => array(
				'disabled' => !WebrsaPermissions::check($controller, 'add')
			),
		)
	);
	
	// Liste des permissions liés aux actions
	$perms = array(
		'edit',
		'delete',
	);
	
	// Attribu à $perm[$nomDeLaction] la valeur 'true' ou 'false' (string)
	foreach( $perms as $permission ){
		$controllerName = $controller;
		$actionName = $permission;
		
		if (strpos($permission, '.') !== false){
			list($controllerName, $actionName) = explode('.', $permission);
		}
		
		$perm[$permission] = !WebrsaPermissions::check($controllerName, $actionName) ? 'true' : 'false';
	}
	
	echo $this->Default3->index(
		$datas,
		array(
			$modelName.'.name',
			$modelName.'.actif' => array('type' => 'boolean'),
			'/'.ucfirst($controller).'/edit/#'.$modelName.'.id#' => array(
				'disabled' => $perm['edit']
			),
			'/'.ucfirst($controller).'/delete/#'.$modelName.'.id#' => array(
				'disabled' => "('#Motiffichedeliaison.occurences#' == true) OR ".$perm['delete']
			),
		),
		$defaultParams
	);
	
	echo $this->Xhtml->link(
		'Retour',
		array('controller' => 'fichedeliaisons', 'action' => 'indexparams')
	);