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
	$notEmptyRule['notEmpty'] = array(
		'rule' => 'notEmpty',
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

	/**
	 * FILTRES CUSTOM
	 */

	$this->end();
	
	/*
	 * Modifications du search_footer :
	 */

	$explAction = explode('_', $action);
	$exportcsvActionName = isset($explAction[1]) ? 'exportcsv_'.$explAction[1] : 'exportcsv';
	$searchData['Search'] = (array)Hash::get( $this->request->data, 'Search' );
	$buttons = '';
	
	$count = (int)Hash::get( $this->request->params, "paging.Personne.count" );
	$countResults = isset($results) ? count($results) : 0;
	if( $count > 65000 ) {
		$button .= '<p class="noprint" style="border: 1px solid #556; background: #ffe;padding: 0.5em;">'.$this->Xhtml->image( 'icons/error.png' ).'<strong>Attention</strong>, il est possible que votre tableur ne puisse pas vous afficher les résultats au-delà de la 65&nbsp;000ème ligne.</p>';
	}
	
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
				( $this->Permissions->check( $controller, $exportcsvActionName ) && $count > 0 )
			)
			.'</li><li>'.$this->Xhtml->printCohorteLink(
				'Imprimer la cohorte',
				Hash::merge(
					array(
						'controller' => $controller,
						'action'     => $action.'_impressions',
					),
					Hash::flatten( $this->request->data, '__' )
				)
				, ( $this->Permissions->check( $controller, $action.'_impressions' ) && $count > 0 )
				, 'Voulez vous imprimer les '.$countResults.' questionnaires ?'
				, 'popup_impression_cohorte'
			).'</li><li>
				<a href="javascript:location.reload();" class="refresh_page" >Recharger la page</a>
			</li>
		</ul>'
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
</script>