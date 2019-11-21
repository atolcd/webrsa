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
				'value' => 'VALIDAVIS',
				'options' => $options['Titrecreancier']['etat']
			)
		);
	?>
</fieldset>
<?php

	$this->end();

	$explAction = explode('_', $action);
	$exportcsvActionName = isset($explAction[1]) ? 'exportcsv_'.$explAction[1] : 'exportcsv';

	echo $this->element(
		'ConfigurableQuery/cohorte',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => $exportcsvActionName ),
			'modelName' => 'Titrecreancier'
		)
	);

	if( isset( $results ) ) {
		echo $this->Form->button( __m('Titrecreancier::cohorte::coche'), array( 'type' => 'button', 'onclick' => 'return toutCocher();' ) );
		echo ' ';
		echo $this->Form->button( __m('Titrecreancier::cohorte::decoche'), array( 'type' => 'button', 'onclick' => 'return toutDecocher();' ) );
	}else{
		array();
	}
	foreach ($results as $i => $result) {
	?>
		<script type="text/javascript">
			observeDisableElementsOnValues(
				[
				'Cohorte<?php echo $i;?>TitrecreancierValidation',
				'Cohorte<?php echo $i;?>TitrecreancierCommentairevalidateur',
				'Cohorte<?php echo $i;?>TitrecreancierDtvalidationDay',
				'Cohorte<?php echo $i;?>TitrecreancierDtvalidationMonth',
				'Cohorte<?php echo $i;?>TitrecreancierDtvalidationYear'
				],
				[
				{element: 'Cohorte<?php echo $i;?>TitrecreancierSelection', value: null}
				],
				false,
				true
			);
		</script>
	<?php
	}
