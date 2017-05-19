<?php
	$this->pageTitle = __d( 'cui66', "Cuis66::{$this->action}" );
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
	
	echo $this->Default3->titleForLayout(array(), array('msgid' => 'DEPRECATED'));

	echo $this->element( 'ancien_dossier' );

	echo $this->Default3->actions(
		array(
			"/Cuis66/add/{$personne_id}" => array(
				'disabled' => !$this->Permissions->checkDossier( 'Cuis66', 'add', $dossierMenu )
			),
		)
	);

	// A-t'on des messages à afficher à l'utilisateur ?
	if( !empty( $messages ) ) {
		foreach( $messages as $message => $class ) {
			echo $this->Html->tag( 'p', __d( $this->request->params['controller'], $message ), array( 'class' => "message {$class}" ) );
		}
	}
	
	// Liste des permissions liés aux actions. 
	// Dans le cas d'un autre controller que Cuis66, on le renseigne avec Controller.action
	$perms = array(
		'view',
		'edit',
		'impression_fichedeliaison',
		'impression',
		'email',
		'Propositionscuis66.index',
		'Decisionscuis66.index',
		'notification',
		'Accompagnementscuis66.index',
		'Suspensionscuis66.index',
		'Rupturescuis66.index',
		'annule',
		'delete',
	);
	
	// Attribu à $perm[$nomDeLaction] la valeur 'true' ou 'false' (string)
	// Utile pour defaut3 avec son eval() 
	// ex: '( in_array( \'#Cui66.etatdossiercui66#\', array( \'annule\' ) ) ) || ' . $perm['edit']
	foreach( $perms as $permission ){
		$controllerName = 'Cuis66';
		$actionName = $permission;
		
		if ( strpos($permission, '.') !== false ){
			$divide = explode( '.', $permission );
			$controllerName = $divide[0];
			$actionName = $divide[1];
		}
		
		$perm[$permission] = !$this->Permissions->checkDossier( $controllerName, $actionName, $dossierMenu ) ? 'true' : 'false';
	}
	
	// Ajout des dates sur certaines positions du CUI
	foreach( $results as $key => $value ){
		$etat = $value['Cui66']['etatdossiercui66'];
		$insert = '';
		if ( in_array( $etat, array( 'contratsuspendu', 'rupturecontrat', 'dossierrelance' ) ) ){
			switch ( $etat ){
				case 'contratsuspendu': $insert = new DateTime($value['Suspensioncui66']['datefin']); break;
				case 'rupturecontrat': $insert = new DateTime($value['Rupturecui66']['daterupture']); break;
				case 'dossierrelance': $insert = new DateTime($value['Emailcui']['dateenvoi']); break;
				default: $insert = '';
			}
			$insert = date_format($insert, 'd/m/Y');
		}
		$results[$key]['Cui66']['positioncui66'] = sprintf( __d('cui66', 'ENUM::ETATDOSSIERCUI66::' . $etat  ), $insert );
	}
	
	echo $this->Default3->index(
		$results,
		array(
			'Cui.faitle',
			'Cui66.positioncui66',
			'Historiquepositioncui66.created',
			'Cui66.positioncui66',
			'Cui.secteurmarchand' => array( 'type' => 'select' ),
			'Partenairecui.raisonsociale',
			'Cui.effetpriseencharge',
			'Cui.finpriseencharge',
			'Decisioncui66.decision',
			'Decisioncui66.datedecision',
			'Cui66.notifie' => array( 'type' => 'select' ),
			'Cui66.raisonannulation',
			'/Cuis66/view/#Cui.id#' => array(
				'title' => __d('cuis66', '/Cuis66/view'),
				'disabled' => $perm['view']
			),
			'/Cuis66/edit/#Cui.id#' => array(
				'title' => __d('cuis66', '/Cuis66/edit'),
				'disabled' => '( in_array( \'#Cui66.etatdossiercui66#\', array( \'annule\' ) ) ) || ' . $perm['edit']
			),
			'/Cuis66/impression_fichedeliaison/#Cui.id#' => array(
				'title' => __d('cuis66', '/Cuis66/impression_fichedeliaison'),
				'disabled' => $perm['impression_fichedeliaison'],
				'class' => 'impression'
			),
			'/Cuis66/impression/#Cui.id#' => array(
				'title' => __d('cuis66', '/Cuis66/impression'),
				'disabled' => $perm['impression'],
			),
			'/Cuis66/email/#Cui.personne_id#/#Cui.id#' => array(
				'title' => __d('cuis66', '/Cuis66/email'),
				'disabled' => $perm['email']
			),
			'/Propositionscuis66/index/#Cui.id#' => array(
				'title' => __d('cuis66', '/Propositionscuis66/index'),
				'class' => 'proposition',
				'disabled' => $perm['Propositionscuis66.index']
			),
			'/Decisionscuis66/index/#Cui.id#' => array(
				'title' => __d('cuis66', '/Decisionscuis66/index'),
				'class' => 'valider',
				'disabled' => $perm['Decisionscuis66.index']
			),
			'/Cuis66/notification/#Cui66.id#' => array(
				'title' => __d('cuis66', '/Cuis66/notification'),
				'class' => 'alert',
				'disabled' => '( #Cui66.notifie# === 1 || !in_array( \'#Cui66.etatdossiercui66#\', array( \'attentenotification\' ) ) ) || ' . $perm['notification']
			),
			'/Accompagnementscuis66/index/#Cui.id#' => array(
				'title' => __d('cuis66', '/Accompagnementscuis66/index'),
				'class' => 'accompagnement',
				'disabled' => $perm['Accompagnementscuis66.index']
			),
			'/Suspensionscuis66/index/#Cui.id#' => array(
				'title' => __d('cuis66', '/Suspensionscuis66/index'),
				'class' => 'suspension',
				'disabled' => '( in_array( \'#Cui66.etatdossiercui66#\', array( \'attentepiece\', \'dossierrecu\', \'dossiereligible\', \'attentemail\', \'formulairecomplet\', \'attenteavis\' ) ) ) || ' . $perm['Suspensionscuis66.index']
			),
			'/Rupturescuis66/index/#Cui.id#' => array(
				'title' => __d('cuis66', '/Rupturescuis66/index'),
				'class' => 'rupture',
				'disabled' => '( in_array( \'#Cui66.etatdossiercui66#\', array( \'attentepiece\', \'dossierrecu\', \'dossiereligible\', \'attentemail\', \'formulairecomplet\', \'attenteavis\' ) ) ) || ' . $perm['Rupturescuis66.index']
			),
			'/Cuis66/annule/#Cui66.id#' => array(
				'title' => __d('cuis66', '/Cuis66/annule'),
				'class' => 'delete',
				'disabled' => '( in_array( \'#Cui66.etatdossiercui66#\', array( \'annule\' ) ) ) || ' . $perm['annule']
			),
			'/Cuis66/delete/#Cui.id#' => array(
				'title' => __d('cuis66', '/Cuis66/delete'),
				'disabled' => $perm['delete']
			),
			'/Cuis66/filelink/#Cui.id#' => array(
				'title' => __d('cuis66', '/Cuis66/filelink'),
				'disabled' => !$this->Permissions->checkDossier( 'Cuis66', 'filelink', $dossierMenu )
			),
			'Fichiermodule.nombre' => array( 'type' => 'integer', 'class' => 'number' ),
		),
		array(
			'options' => $options,
			'paginate' => false
		)
	);
