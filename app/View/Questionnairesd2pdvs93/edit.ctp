<?php
	echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );

	echo $this->Default3->titleForLayout( $personne );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$url = array( 'controller' => $this->request->params['controller'], 'action' => $this->request->params['action'], $this->request->params['pass'][0] );

	echo $this->Default3->DefaultForm->create( 'Questionnaired2pdv93', array( 'novalidate' => 'novalidate', 'url' => $url ) );

	echo $this->Default3->subform(
		array(
			'Questionnaired2pdv93.id' => array( 'type' => 'hidden' ),
			'Questionnaired2pdv93.personne_id' => array( 'type' => 'hidden' ),
			'Questionnaired2pdv93.questionnaired1pdv93_id' => array( 'type' => 'hidden' ),
			'Questionnaired2pdv93.isajax' => array( 'type' => 'hidden' ),
			'Questionnaired2pdv93.date_validation' => array( 'type' => 'hidden' ),
			'Questionnaired2pdv93.toujoursenemploi' => array(
				'options' => array ('Non', 'Oui'),
				'required' => true
			),
			'Questionnaired2pdv93.situationaccompagnement' => array(
				'options' => $options['Questionnaired2pdv93']['situationaccompagnement'],
				'empty' => true,
				'required' => true
			),
			'Questionnaired2pdv93.sortieaccompagnementd2pdv93_id' => array(
				'options' => $options['Questionnaired2pdv93']['sortieaccompagnementd2pdv93_id'],
				'empty' => true,
				'required' => true
			),
			'Questionnaired2pdv93.chgmentsituationadmin' => array(
				'options' => $options['Questionnaired2pdv93']['chgmentsituationadmin'],
				'empty' => true,
				'required' => true
			),
		)
	);

	echo $this->Form->input(
		'Questionnaired2pdv93.dureeemploi_id',
		array(
			'options' => $options['Dureeemploi'],
			'empty' => '',
			'label' => __d ('questionnairesb7pdvs93', 'Questionnaireb7pdv93.dureeemploi'),
			'disabled' => true
		)
	);

	// Rome V3
	echo $this->Romev3->fieldset(
		'Emploiromev3',
		array(
			'options' => $options,
			'requiredOnlyFirst' => true,
			'disabled' => true
		)
	);


	if( $isAjax ) {
		$onComplete = 'try {
var json = request.responseText.evalJSON(true);
	if( json.success === true ) {
		var ajaxCohorteUrl = document.URL.replace( /^(https{0,1}:\/\/[^\/]+)\/.*$/gi, \'$1\' ) + cohorteUrl;
		new Ajax.Updater(
			\'Cohortesd2pdv93IndexAjaxContainer\',
			ajaxCohorteUrl,
			{
				evalScripts: true,
				onComplete: function( response ) {
					$( \'Questionnaired2pdv93ModalForm\' ).hide();
				}
			}
		);

		$( \'popup-content1\' ).update(\'\');
	}
}
catch(e) {
	console.log( e );
}';
		/**
		 * Ajout d'un champ caché avec le name et la value du bouton ayant été
		 * activé avant de désactiver les boutons d'envoi de formulaire.
		 *
		 * @see https://prototype.lighthouseapp.com/projects/8886/tickets/672-formserialize-and-multiple-submit-buttons
		 *
		 * @param string $name Le name du bouton ayant été activé
		 * @return string
		 */
		function beforeAjaxSendQuestionnairesd2pdvs93( $name ) {
			return 'try {
			var form = null, hidden = null;
			$$( "input[name='.$name.']" ).each( function( button ) {
				if( null === form ) {
					form = $(button).up( "form" );
					hidden = new Element( "input", { "type": "hidden", "name": $(button).readAttribute( "name" ), "value": $(button).readAttribute( "value" ) } );
				}
			} );

			$(form).insert( { "top" : hidden } );

			$$( "div.submit input" ).each( function( button ) {
				$(button).disable();
			} );
}
catch(e) {
	console.log( e );
}';
		}

		$submit = $this->Ajax->submit(
				__( 'Validate' ),
				array(
					'url'=> $url,
					'update' => 'popup-content1',
					'div' => false,
					'name' => 'Validate',
					'before' => beforeAjaxSendQuestionnairesd2pdvs93( 'Validate' ),
					'complete' => $onComplete,
				)
			)
			.' '
			.$this->Ajax->submit(
				__( 'Cancel' ),
				array(
					'url'=> $url,
					'update' => 'popup-content1',
					'div' => false,
					'name' => 'Cancel',
					'before' => beforeAjaxSendQuestionnairesd2pdvs93( 'Cancel' ),
					'complete' => $onComplete,
				)
		);

		echo $this->Html->tag( 'div', $submit, array( 'class' => 'submit' ) );
	}
	else {
		echo $this->Default3->DefaultForm->buttons( array( 'Validate', 'Cancel' ) );
	}
?>
<script type="text/javascript">
	//<![CDATA[
	observeDisableFieldsOnValue(
		'Questionnaired2pdv93Situationaccompagnement',
		[ 'Questionnaired2pdv93Sortieaccompagnementd2pdv93Id', 'Emploiromev3Romev3', 'Emploiromev3Familleromev3Id', 'Emploiromev3Domaineromev3Id', 'Emploiromev3Metierromev3Id', 'Emploiromev3Appellationromev3Id', 'Questionnaired2pdv93DureeemploiId' ],
		[ 'sortie_obligation' ],
		false,
		false
	);
	observeDisableFieldsOnValue(
		'Questionnaired2pdv93Situationaccompagnement',
		[ 'Questionnaired2pdv93Chgmentsituationadmin' ],
		[ 'changement_situation' ],
		false,
		false
	);
	Event.observe($('Questionnaired2pdv93Situationaccompagnement'), 'change', function(e){
		$('Questionnaired2pdv93Sortieaccompagnementd2pdv93Id').setValue(0);
		$('Questionnaired2pdv93DureeemploiId').disable();
	});
	//]]>
</script>