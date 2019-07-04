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
				'label' => __m('Creance::search::orgcre'),
				'type' => 'select',
				'empty' => true,
				'options' => $options['Creance']['orgcre']
			)
		);
		echo $this->Xform->input(
			'Search.Creance.motiindu',
			array(
				'label' => __m('Creance::search::motiindu'),
				'type' => 'select',
				'empty' => true,
				'options' => $options['Creance']['motiindu']
			)
		);
		echo "<fieldset><legend> ".__m('Creance::search::dtimplcre')."</legend>";
		echo $this->Xform->input(
			'Search.Creance.dtimplcre_from',
			array(
				'label' => ' From',
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
				'label' => 'To ',
				'type' => 'date',
				'dateFormat'=>'DMY',
				'maxYear'=>date('Y')+2,
				'minYear'=> '2009' ,
				'empty' => true
			)
		) ;
		echo "</fieldset>";
		echo "<fieldset><legend> ".__m('Creance::search::moismoucompta')."</legend>";
		echo $this->Xform->input(
			'Search.Creance.moismoucompta_from',
			array(
				'label' => ' From',
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
				'label' => 'To ',
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
					'Imprimer le tableau',
					array( 'onclick' => 'printit(); return false;', 'class' => 'noprint' )
				)
			.'</li><li>'
			. $this->Xhtml->exportLink(
				'Télécharger le tableau',
				array( 'controller' => $controller, 'action' => $exportcsvActionName ) + Hash::flatten( $searchData + array( 'prevAction' => $this->action ), '__' ),
				( $this->Permissions->check( $controller, $exportcsvActionName )  )
			)
			.'</li><li>'.$this->Xhtml->printCohorteLink(
				__m('Titrecreancier::cohortetransmission::exporttitle'),
				Hash::merge(
					array(
						'controller' => $controller,
						'action'     => $action.'_exportfica',
					),
					Hash::flatten( $this->request->data, '__' )
				),
				( $this->Permissions->check( $controller, $action.'_exportfica' )),
				__m('Titrecreancier::cohortetransmission::exportpopuptext')
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
