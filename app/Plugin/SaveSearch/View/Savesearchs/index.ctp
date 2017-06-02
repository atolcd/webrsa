<?php 
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
	
	echo $this->Default3->titleForLayout(array(), array('msgid' => Configure::read('Module.Savesearch.mon_menu.name') ?: 'Mon menu'));
	
	// Liste des permissions liés aux actions. 
	// Dans le cas d'un autre controller que Savesearchs, on le renseigne avec Controller.action
	$perms = array(
		'edit',
		'delete',
		'edit_group',
		'delete_group',
	);
	
	// Attribu à $perm[$nomDeLaction] la valeur 'true' ou 'false' (string)
	foreach( $perms as $permission ){
		$controllerName = 'Savesearchs';
		$actionName = $permission;
		
		if ( strpos($permission, '.') !== false ){
			$divide = explode( '.', $permission );
			$controllerName = $divide[0];
			$actionName = $divide[1];
		}
		
		$perm[$permission] = !$this->Permissions->check($controllerName, $actionName) ? 'true' : 'false';
	}
	
	// Prise de traduction pour controller/action et on sépare le personnel du groupe
	$user_id = $this->Session->read('Auth.User.id');
	$groupe = array();
	$perso = array();
	foreach ($results as $key => $v) {
		$results[$key]['Savesearch']['moteur_name'] = __d(
			$v['Savesearch']['controller'], 
			'/'.ucfirst($v['Savesearch']['controller']).'/'.$v['Savesearch']['action'].'/:heading'
		);
		
		if ($v['Savesearch']['user_id'] == $user_id) {
			$perso[$key] = $results[$key];
		} else {
			$groupe[$key] = $results[$key];
		}
	}
	
	$defaultParams = array(
		'paginate' => false,
		'options' => $options
	);
	
	$params = array(
		'Savesearch.name',
		'Savesearch.moteur_name',
		'Savesearch.created',
		'Savesearch.isforgroup' => array('type' => 'boolean'),
	);
	
	if (Configure::read('Module.Savesearch.mon_menu.enabled')) {
		$params['Savesearch.isformenu'] = array('type' => 'boolean');
	}
	
	echo '<h3>Sauvegardes personnelles</h3>';
	
	echo $this->Default3->index($perso, $params + array(
		'/Savesearchs/edit/#Savesearch.id#' => array(
			'disabled' => $perm['edit']
		),
		'/Savesearchs/delete/#Savesearch.id#' => array(
			'disabled' => $perm['delete']
		),
	), $defaultParams);
	
	echo '<br/><br/><h3>Sauvegardes de groupe</h3>';
	
	echo $this->Default3->index($groupe, $params + array(
		'User.nom_complet' => array(),
		'/Savesearchs/edit_group/#Savesearch.id#' => array(
			'disabled' => $perm['edit_group'],
			'class' => 'edit'
		),
		'/Savesearchs/delete_group/#Savesearch.id#' => array(
			'disabled' => $perm['delete_group'],
			'class' => 'delete'
		),
	), $defaultParams);