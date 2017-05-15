<?php
	// Donne le domain du plus haut niveau de précision (prefix, action puis controller)
	$domain = current(WebrsaTranslator::domains());

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));
	
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->element( 'ancien_dossier' );

	echo $this->Default3->actions(
		array(
			"/Fichedeliaisons/add/{$foyer_id}" => array(
				'disabled' => !$this->Permissions->checkDossier( 'Fichedeliaisons', 'add', $dossierMenu )
			),
		)
	);

	// A-t'on des messages à afficher à l'utilisateur ?
	if( !empty( $messages ) ) {
		foreach( $messages as $message => $class ) {
			echo $this->Html->tag( 'p', __m($message), array( 'class' => "message {$class}" ) );
		}
	}
	
	// Liste des permissions liés aux actions. 
	// Dans le cas d'un autre controller que Cuis66, on le renseigne avec Controller.action
	$perms = array(
		'view',
		'edit',
		'filelink',
		'avis',
		'validation',
		'delete',
		'Primoanalyses.view',
		'Primoanalyses.affecter',
		'Primoanalyses.proposition',
		'Primoanalyses.avis',
		'Primoanalyses.validation',
		'Primoanalyses.vu',
		'Primoanalyses.afaire',
		'Primoanalyses.delete',
		'Dossierspcgs66.edit',
	);
	
	// Attribu à $perm[$nomDeLaction] la valeur 'true' ou 'false' (string)
	foreach( $perms as $permission ){
		$controllerName = 'Fichedeliaisons';
		$actionName = $permission;
		
		if (strpos($permission, '.') !== false){
			list($controllerName, $actionName) = explode('.', $permission);
		}
		
		$perm[$permission] = !$this->Permissions->checkDossier($controllerName, $actionName, $dossierMenu) ? 'true' : 'false';
	}
	
	echo $this->Default3->index(
		$fichedeliaisons,
		$this->Translator->normalize(
			array(
				'Fichedeliaison.etat',
				'Fichedeliaison.expediteur_id',
				'Fichedeliaison.destinataire_id',
				'Fichedeliaison.datefiche',
				'Fichedeliaison.motiffichedeliaison_id',
				'/Fichedeliaisons/view/#Fichedeliaison.id#' => array(
					'disabled' => $perm['view']
				),
				'/Fichedeliaisons/edit/#Fichedeliaison.id#' => array(
					'disabled' => "('#Fichedeliaison.etat#' != 'attavistech') OR ".$perm['edit']
				),
				'/Fichedeliaisons/avis/#Fichedeliaison.id#' => array(
					'class' => 'avistechnique',
					'disabled' => "(!in_array('#Fichedeliaison.etat#', array('attavistech', 'attval'))) OR ".$perm['avis']
				),
				'/Fichedeliaisons/validation/#Fichedeliaison.id#' => array(
					'disabled' => "(!in_array('#Fichedeliaison.etat#', array('attval')) ) OR ".$perm['validation']
				),
				'/Fichedeliaisons/delete/#Fichedeliaison.id#' => array(
					'disabled' => "('#Primoanalyse.etat#' !== '') OR ".$perm['delete'],
					'confirm' => true
				),
				'/Fichedeliaisons/filelink/#Fichedeliaison.id#' => array(
					'disabled' => $perm['filelink'],
					'msgid' => __m('/Fichedeliaisons/filelink').' (#Fichiermodule.nombre#)'
				),
			)
		),
		array(
			'options' => $options,
			'paginate' => false,
		)
	);
	
	echo '<br/><br/><h2>Primo analyses</h2>';
	
	echo $this->Default3->index(
		$primoanalyses,
		$this->Translator->normalize(
			array(
				'Primoanalyse.etat',
				'Primoanalyse.user_id',
				'Primoanalyse.propositionprimo_id',
				'Primoanalyse.dateprimo',
				'/Primoanalyses/view/#Primoanalyse.id#' => array(
					'disabled' => "(in_array('#Primoanalyse.etat#', array('attaffect'))) OR ".$perm['Primoanalyses.view']
				),
				'/Primoanalyses/affecter/#Primoanalyse.id#' => array(
					'disabled' => "(!in_array('#Primoanalyse.etat#', array('attaffect', 'attinstr'))) OR ".$perm['Primoanalyses.affecter']
				),
				'/Primoanalyses/proposition/#Primoanalyse.id#' => array(
					'disabled' => "(!in_array('#Primoanalyse.etat#', array('attinstr', 'attavistech'))) OR ".$perm['Primoanalyses.proposition']
				),
				'/Primoanalyses/avis/#Primoanalyse.id#' => array(
					'class' => 'avistechnique',
					'disabled' => "(!in_array('#Primoanalyse.etat#', array('attavistech', 'attval'))) OR ".$perm['Primoanalyses.avis']
				),
				'/Primoanalyses/validation/#Primoanalyse.id#' => array(
					'disabled' => "(!in_array('#Primoanalyse.etat#', array('attval'))) OR ".$perm['Primoanalyses.validation']
				),
				'/Dossierspcgs66/edit/#Primoanalyse.dossierpcg66_id#' => array(
					'disabled' => "('#Primoanalyse.dossierpcg66_id#' === '') OR ".$perm['Dossierspcgs66.edit']
				),
				'/Primoanalyses/vu/#Primoanalyse.id#' => array(
					'class' => 'validation',
					'disabled' => $perm['Primoanalyses.vu'],
				),
				'/Primoanalyses/afaire/#Primoanalyse.id#' => array(
					'class' => 'error_icon',
					'disabled' => "('#Fichedeliaison.traitementafaire#' !== '0') OR ".$perm['Primoanalyses.afaire'],
				),
				'/Primoanalyses/delete/#Primoanalyse.id#' => array(
					'disabled' => "('#Primoanalyse.dossierpcg66_id#' !== '') OR ".$perm['Primoanalyses.delete'],
					'confirm' => true
				),
			)
		),
		array(
			'options' => $options,
			'paginate' => false,
		)
	);