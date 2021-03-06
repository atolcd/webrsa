<?php
	$activateFica = (boolean)Configure::read('Module.Creances.FICA.enabled');

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
	$validationCohorte = array(
		'Creance' => array(
			'created' => $dateRule,
		)
	);
	echo $this->FormValidator->generateJavascript($validationCohorte, false);

	$this->start( 'custom_search_filters' );
?>
<fieldset>
	<legend><?php echo __m( 'Search.Titrescreanciers' ); ?></legend>
	<?php
		echo $this->Xform->input(
			'Search.Creance.orgcre',
			array(
				'label' => __d('creances','Creance::search::orgcre'),
				'type' => 'select',
				'empty' => true,
				'options' => $options['Creance']['orgcre']
			)
		);
		echo $this->Xform->input(
			'Search.Creance.motiindu',
			array(
				'label' => __d('creances','Creance::search::motiindu'),
				'type' => 'select',
				'empty' => true,
				'options' => $options['Creance']['motiindu']
			)
		);
		echo "<fieldset><legend> ".__d('creances','Creance::search::dtimplcre')."</legend>";
		echo $this->Xform->input(
			'Search.Creance.dtimplcre_from',
			array(
				'label' => __d('creances','Search.Creance.dtimplcre_from'),
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+1,
				'minYear'=> '2009',
				'empty' => true
			)
		) ;
		echo $this->Xform->input(
			'Search.Creance.dtimplcre_to',
			array(
				'label' => __d('creances','Search.Creance.dtimplcre_to'),
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+2,
				'minYear'=> '2009' ,
				'empty' => true
			)
		) ;
		echo "</fieldset>";
		echo "<fieldset><legend> ".__d('creances','Creance::search::moismoucompta')."</legend>";
		echo $this->Xform->input(
			'Search.Creance.moismoucompta_from',
			array(
				'label' => __d('creances','Search.Creance.moismoucompta_from'),
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+1,
				'minYear'=> '2009',
				'empty' => true
			)
		) ;
		echo $this->Xform->input(
			'Search.Creance.moismoucompta_to',
			array(
				'label' => __d('creances','Search.Creance.moismoucompta_to'),
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+2,
				'minYear'=> '2009' ,
				'empty' => true
			)
		) ;
		echo "</fieldset>";
		echo $this->Xform->input(
			'Search.Titrecreancier.etat',
			array(
				'label' => __m('Titrecreancier::search::etat'),
				'type' => 'hidden',
				'value' => 'ATTENVOICOMPTA',
				'options' => $options['Titrecreancier']['etat']
			)
		);
	?>
</fieldset>
<?php

	$this->end();

	/*
	 * Modifications du search_footer :
	 */
	$explAction = explode('_', $action);
	$exportcsvActionName = isset($explAction[1]) ? 'exportcsv_'.$explAction[1] : 'exportcsv';

	$searchData['Search'] = (array)Hash::get( $this->request->data, 'Search' );
	$buttons = '';

	$buttons .= '<ul class="actionMenu">'
			.'<li>'
				.$this->Xhtml->printLinkJs(
					__m('Titrecreancier::cohortetransmission::impression:title'),
					array( 'onclick' => 'printit(); return false;', 'class' => 'noprint' )
				)
			.'</li><li>'
			. $this->Xhtml->exportLink(
				__m('Titrecreancier::cohortetransmission::exportcsv:title'),
				array( 'controller' => $controller, 'action' => $exportcsvActionName ) + Hash::flatten( $searchData + array( 'prevAction' => $this->action ), '__' ),
				( $this->Permissions->check( $controller, $exportcsvActionName )  )
			)
			.'</li>';
		if ( $activateFica ){
			$buttons .= '<li>'.$this->Xhtml->printCohorteLink(
				__m('Titrecreancier::cohortetransmission::exportfica:title'),
				Hash::merge(
					array(
						'controller' => $controller,
						'action'     => $action.'_exportfica',
					),
					Hash::flatten( $searchData, '__' )
				),
				($activateFica && $this->Permissions->check( $controller, $action.'_exportfica' )),
				__m('Titrecreancier::cohortetransmission::exportfica:popuptext')
				, 'popup_impression_cohorte'
			).'</li>';
			$buttons .= '<li>'.$this->Xhtml->printCohorteLink(
				__m('Titrecreancier::cohortetransmission::exportzip:title'),
				Hash::merge(
					array(
						'controller' => $controller,
						'action'     => $action.'_exportzip',
					),
					Hash::flatten( $searchData, '__' )
				),
				($activateFica && $this->Permissions->check( $controller, $action.'_exportzip' )),
				__m('Titrecreancier::cohortetransmission::exportzip:popuptext')
				, 'popup_impression_cohorte'
			).'</li>';
		}
	$buttons .= '</ul>
			<li>
				<a href="javascript:location.reload();" class="refresh_page" >Recharger la page</a>
			</li>'
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
