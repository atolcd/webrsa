<?php
	$departement = Configure::read( 'Cg.departement' );
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

	/**
	 * ATTENTION
	 * Voir aussi View/Elements/ConfigurableQuery/cohorte.ctp
	 * Les entités des tags sont maintenant définies ici.
	 */
	$options['EntiteTag'] = array(
		'modele' => array (
			__d('tags', 'Cohorte.EntiteTag.personne') => __d('tags', 'Cohorte.EntiteTag.personne'),
			__d('tags', 'Cohorte.EntiteTag.foyer') => __d('tags', 'Cohorte.EntiteTag.foyer'),
		)
	);

	// Bloc beforeSearch
	$this->start( 'before_search_filters' );
	echo '<input name="AffectationDesTagsParCohorte" value="1" type="hidden">';
	echo ('<div class="tag legend">');
	echo $this->Xform->multipleCheckbox( 'Search.Tag.valeurtag_id', $options['filter'], '',  'Search.Tag.text.required');
	echo ('</div>');
	echo ('<hr/>');
	$this->end();

	// Bloc customSearch
	$this->start( 'custom_search_filters' );
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

	if ($departement == 66) {
?>
	<fieldset>
		<legend><?php echo __d( 'dossierspcgs66', 'Dossierpcg66.search' ); ?></legend>
		<?php
			echo $this->Xform->input(
				'Search.Dossierpcg66.has_dossierpcg66',
				array(
					'label' => __d( 'dossierspcgs66', 'Search.Dossierpcg66.has_dossierpcg66' ),
					'type' => 'select',
					'empty' => true,
					'options' => array ('Non', 'Oui')
				)
			);
		?>
	</fieldset>
<?php
	}

	$this->end();

	// Bloc tagCohorteSearch
	$this->start( 'tag_cohorte_search_filters' );
	echo '<fieldset><legend>' . __m( 'Orientstruct.search' ) . '</legend>';
	echo $this->Default3->subform(
		array(
			'Search.Orientstruct.origine' => array('empty' => true),
		),
		array( 'options' => array( 'Search' => $options ) )
	);
	echo $this->Default3->subform(
		array(
			'Search.Orientstruct.typeorient_id' => array('empty' => true, 'required' => false),
		),
		array( 'options' => array( 'Search' => $options ) )
	);
	echo '</fieldset>';
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
		var duree = parseFloat( $F('CohorteTagCalcullimite'), 10 ),
			now = new Date(),
			jour = now.getUTCDate(),
			mois = now.getUTCMonth() +1,
			annee = now.getUTCFullYear(),
			dateButoir;
		var dureeJour = 0;
		if ( duree == 1.5) {
			dureeJour = 15;
		}

		if ( isNaN(duree*2) ){
			return false;
		}

		dateButoir = new Date(annee, mois + duree - 1, jour + dureeJour -1);

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

	/*
	 *
	 * Affichage des blocs ci-desssus dans la vue ConfigurableQuery/cohorte se trouvant dans le fichier :
	 * View/Elements/ConfigurationQuery/cohorte.ctp
	 *
	 */
	echo $this->element(
		'ConfigurableQuery/cohorte',
		array(
			'beforeSearch' => $this->fetch( 'before_search_filters' ),
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'tagCohorteSearch' => $this->fetch( 'tag_cohorte_search_filters' ),
		)
	);