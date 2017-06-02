<?php
	$controller = $this->params->controller;
	$availableDomains = WebrsaTranslator::domains();
	$domain = isset( $availableDomains[0] ) ? $availableDomains[0] : $controller;
	$paramDate = array(
		'domain' => null,
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 4
	);

	$this->start( 'custom_search_filters' );

	echo $this->Xform->multipleCheckbox( 'Search.Tag.valeurtag_id', $options['filter'] );
	echo $this->Xform->multipleCheckbox( 'Search.Prestation.rolepers', $options, 'divideInto2Columns' );
	echo $this->Xform->multipleCheckbox( 'Search.Foyer.composition', $options, 'divideInto2Columns' );

	echo '<fieldset><legend>' . __m( 'Tag.cohorte_fieldset' ) . '</legend>'
		. $this->Default3->subform(
			array(
				'Search.Foyer.nb_enfants' => array( 'empty' => true ),
				'Search.Adresse.heberge' => array( 'empty' => true ),
				'Search.Requestmanager.name' => array( 'empty' => true ),
			),
			array(
				'options' => array( 'Search' => $options )
			)
		)
		. '</fieldset>'
	;

	$this->end();

	echo '<fieldset id="CohorteTagPreremplissage" style="display: '.(isset( $results ) ? 'block' : 'none').';"><legend>' . __m( 'Tag.preremplissage_fieldset' ) . '</legend>'
		. $this->Default3->subform(
			array(
				'Cohorte.Tag.selection' => array( 'type' => 'checkbox' ),
				'Cohorte.EntiteTag.modele',
				'Cohorte.Tag.valeurtag_id',
				'Cohorte.Tag.calcullimite' => array( 'empty' => true, 'options' => Configure::read('Tags.cohorte.range_date_butoir') ),
				'Cohorte.Tag.limite' => array( 'dateFormat' => 'DMY', 'minYear' => date('Y'), 'maxYear' => date('Y')+4, 'empty' => true ),
				'Cohorte.Tag.commentaire',
			),
			array(
				'options' => array( 'Cohorte' => $options )
			)
		)
		. '<div class="center"><input type="button" id="preremplissage_cohorte_button" value="Préremplir"/></div>'
		. '</fieldset>'
	;

?>
<script type="text/javascript">
	<!--//--><![CDATA[//><!--
	$('preremplissage_cohorte_button').observe('click', function(){
		$('CohorteTagPreremplissage').select('input[type="checkbox"], select, textarea').each(function(editable){
			var matches = editable.name.match(/^data\[Cohorte\]\[([\w]+)\]\[([\w]+)\](?:\[([\w]+)\]){0,1}$/),
				regex;

			// Cas date
			if ( matches.length === 4 && matches[3] !== undefined ) {
				 regex = new RegExp('^data\\[Cohorte\\]\\[[\\d]+\\]\\['+matches[1]+'\\]\\['+matches[2]+'\\]\\['+matches[3]+'\\]$');
			}
			else {
				 regex = new RegExp('^data\\[Cohorte\\]\\[[\\d]+\\]\\['+matches[1]+'\\]\\['+matches[2]+'\\]$');
			}

			$$('form input[type="checkbox"], select, textarea').each(function(editable_cohorte){
				if ( regex.test(editable_cohorte.name) ) {
					editable_cohorte.setValue(editable.getValue());
				}
			});
		});
	});


	/**
	 * Gestion de la date de cloture automatique en fonction du délai avant cloture automatique
	 * @see View/Cuis66/edit.ctp
	 */
	function setDateCloture(){
		'use strict';
		var duree = parseInt( $F('CohorteTagCalcullimite'), 10 ),
			now = new Date(),
			jour = now.getUTCDate(),
			mois = now.getUTCMonth() +1,
			annee = now.getUTCFullYear(),
			dateButoir;

		if ( isNaN(duree*2) ){
			return false;
		}

		dateButoir = new Date(annee, mois + duree - 1, jour -1);

		$('CohorteTagLimiteDay').select('option').each(function(option){
			option.selected = false;
			if ( parseInt(option.value, 10) === dateButoir.getDate() ){
				option.selected = true;
			}
		});
		$('CohorteTagLimiteMonth').select('option').each(function(option){
			option.selected = false;
			if ( parseInt(option.value, 10) === dateButoir.getMonth() + 1 ){
				option.selected = true;
			}
		});
		$('CohorteTagLimiteYear').select('option').each(function(option){
			option.selected = false;
			if ( parseInt(option.value, 10) === dateButoir.getFullYear() ){
				option.selected = true;
			}
		});
	}
	Event.observe( $('CohorteTagCalcullimite'), 'change', setDateCloture);
	//--><!]]>
</script>
<?php

	echo $this->element(
		'ConfigurableQuery/cohorte',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
		)
	);