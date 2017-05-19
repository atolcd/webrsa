<?php
	// Donne le domain du plus haut niveau de précision (prefix, action puis controller)
	$domain = current(WebrsaTranslator::domains());
	$defaultParams = compact('options', 'domain');

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));
	echo $this->FormValidator->generateJavascript();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
	
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate', 'id' => 'FichedeliaisonAddForm' ) );

/***********************************************************************************
 * FORMULAIRE
/***********************************************************************************/
	
	/**
	 * Logiciels et/ou sites consultés
	 */
	echo '<fieldset>'
		. $this->Default3->subform(
			array(
				'Primoanalyse.id' => array('type' => 'hidden'),
				'Primoanalyse.fichedeliaison_id' => array('type' => 'hidden'),
				'Primoanalyse.actionafaire' => array('type' => 'radio'),
				'Primoanalyse.dateafaire' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Primoanalyse.commentaireafaire' => array('type' => 'textarea'),
			),
			$defaultParams
		) . '</fieldset>'
	;
	
/***********************************************************************************
 * FIN DU FORMULAIRE
/***********************************************************************************/
	
	echo $this->Default3->DefaultForm->buttons(array('Save', 'Cancel'));
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit('FichedeliaisonAddForm');
?>
<script type="text/javascript">
	observeDisableElementsOnValues(
		[
			$('PrimoanalyseDateafaireDay').up('div'),
			'PrimoanalyseCommentaireafaire'
		],
		[
			{
				element: 'PrimoanalyseActionafaire1',
				value: '1',
				operator: '!='
			},
			{
				element: 'PrimoanalyseActionafaire0',
				value: '1',
				operator: '!='
			}
		],
		true,
		false
	);
</script>