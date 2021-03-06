<?php
	$departement = Configure::read( 'Cg.departement' );
	$controller = $this->params->controller;
	$action = $this->action;
	$formId = ucfirst($controller) . ucfirst($action) . 'Form';
	$availableDomains = WebrsaTranslator::domains();
	$domain = isset( $availableDomains[0] ) ? $availableDomains[0] : $controller;
	$paramDate = array(
		'domain' => null,
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 4
	);
	$notEmptyRule[NOT_BLANK_RULE_NAME] = array(
		'rule' => NOT_BLANK_RULE_NAME,
		'message' => 'Champ obligatoire'
	);
	$dateRule['date'] = array(
		'rule' => array('date'),
		'message' => null,
		'required' => null,
		'allowEmpty' => true,
		'on' => null
	);
	$validationCohorte = array();
	echo $this->FormValidator->generateJavascript($validationCohorte, false);

	$this->start( 'custom_search_filters' );

	/*if( Configure::read( 'CG.cantons' ) ) {
		echo $this->Xform->multipleCheckbox( 'Search.Zonegeographique.id', $options, 'divideInto2Columns' );
	}*/

	//echo $this->Xform->multipleCheckbox( 'Search.Prestation.rolepers', $options, 'divideInto2Columns' );
	//echo $this->Xform->multipleCheckbox( 'Search.Foyer.composition', $options, 'divideInto2Columns' );

	/**
	 * FILTRES CUSTOM
	 */
	echo "<fieldset><legend>" . __m( 'Search.Orientstruct' ) . "</legend>";
	echo $this->SearchForm->dateRange( 'Search.Orientstruct.date_valid', array(
		'domain' => 'planpauvreteorientations',
		'minYear_from' => 2009,
		'minYear_to' => 2009,
		'maxYear_from' => date( 'Y' ) + 1,
		'maxYear_to' => date( 'Y' ) + 1,
	) );
	echo '</fieldset>';
	$this->end();


	/*
	 * Modifications du search_footer :
	 */
	$explAction = substr($action, (strpos($action, '_')+1));
	$exportcsvActionName = isset($explAction) ? 'exportcsv_'.$explAction : 'exportcsv';
	$searchData['Search'] = array_merge (
		(array)Hash::get( $this->request->data, 'Search' ),
		array ('limit' => Hash::get( $this->request->data, 'limit' ))
	);
	$buttons = '';

	$countResults = isset($results) ? count($results) : 0;

	$buttons .= '<ul class="actionMenu">'
			.'<li>'
				.$this->Xhtml->printLinkJs(
					'Imprimer le tableau',
					array( 'onclick' => 'printit(); return false;', 'class' => 'noprint' )
				)
			.'</li><li>'
			. $this->Xhtml->exportLink(
				'Télécharger le tableau',
				array( 'controller' => $controller, 'action' => $exportcsvActionName ) + Hash::flatten( $searchData + array( 'prevAction' => $this->action ), '__' ),
				( $this->Permissions->check( $controller, $exportcsvActionName ) && $countResults > 0 )
			)
			.'</li><li>'.$this->Xhtml->printCohorteLink(
				'Imprimer la cohorte',
				Hash::merge(
					array(
						'controller' => $controller,
						'action'     => $action.'_impressions',
					),
					Hash::flatten( $searchData + array( 'prevAction' => $this->action ), '__' )
				)
				, ( $this->Permissions->check( $controller, $action.'_impressions' ) && $countResults > 0 )
				, 'Voulez vous imprimer les '.$countResults.' courrier d orientation ?'
				, 'popup_impression_cohorte'
			).'</li><li>
				<a href="javascript:location.reload();" class="refresh_page" >Recharger la page</a>
			</li>'
			. (Configure::read('Module.Savesearch.enabled') && WebrsaPermissions::check('savesearchs', 'save') 
			? '<li><a href="#" onclick="$(\'savesearch_popup\').show(); return false;">'
				.$this->Xhtml->image('icons/disk.png').' Sauvegarder recherche</a></li>'
			: '')
		. '</ul>'
	;

	/*
	 * Fin de la modification du search_footer (inclu dans afterResult avec exportcsv => false)
	 */

	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => false,
			'afterResults' => $buttons
		)
	);
?>
<script>
	$$('td.action>a.imprimer').each(function(div){
		Event.observe(div, 'click', function(){
			div.addClassName( 'visited' );
		});
	});
	document.observe("dom:loaded", function() {
		dependantSelect( 'SearchRendezvousPermanenceId', 'SearchRendezvousStructurereferenteId' );
	});
</script>