<?php
	echo $this->Html->script( array( 'prototype.event.simulate.js' ), array( 'inline' => false ) );
	echo $this->element( 'required_javascript' );
?>
<?php $this->start( 'custom_search_filters' );?>
<fieldset>
	<legend>Filtrer par orientation</legend>
	<?php
		$paramDate = array(
			'domain' => null,
			'minYear_from' => '2009',
			'maxYear_from' => date( 'Y' ) + 1,
			'minYear_to' => '2009',
			'maxYear_to' => date( 'Y' ) + 4
		);
		echo $this->Allocataires->SearchForm->dateRange( 'Search.Orientstruct.date_valid', $paramDate );

		echo $this->Default3->subform(
			array(
				'Search.Orientstruct.typeorient_id' => array( 'empty' => true, 'required' => false ),
			),
			array( 'options' => array( 'Search' => $options ) )
		);

		// id du formulaire de cohorte
		$cohorteFormId = Inflector::camelize( "{$this->request->params['controller']}_{$this->request->params['action']}_cohorte" );

		// Boutons "Tout cocher"
		$buttons = null;
		if( isset( $results ) ) {
			$buttons = $this->Form->button( 'Tout valider', array( 'type' => 'button', 'onclick' => "return toutChoisir( $( '{$cohorteFormId}' ).getInputs( 'radio' ), '1', true );" ) );
			$buttons .= $this->Form->button( 'Tout mettre en attente', array( 'type' => 'button', 'onclick' => "return toutChoisir( $( '{$cohorteFormId}' ).getInputs( 'radio' ), '0', true );" ) );
		}
	?>
</fieldset>
<?php $this->end();?>
<?php
	echo $this->element(
		'ConfigurableQuery/cohorte',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'modelName' => 'Dossier',
			'exportcsv' => false,
			'afterResults' => $buttons
		)
	);
?>
<?php if( isset( $results ) ): ?>
<script type="text/javascript">
	var cohorteFormId = '<?php echo $cohorteFormId;?>',
		structuresreferentesParTypeorientId = <?php echo json_encode( Hash::get( $options, 'Structurereferente.listeParTypeorientId' ) );?>,
		zonesgeographiques = <?php echo json_encode( Hash::get( $options, 'Structurereferente.listeParCodeInsee' ) );?>,
		typesorientsprincipales = <?php echo json_encode( (array)Configure::read( 'Orientstruct.typeorientprincipale' ) );?>,
		liveCache = {},
		structurespartypeorientetcodeinsee = {};

	//--------------------------------------------------------------------------

	for( var keyzg in zonesgeographiques ) {
		if( zonesgeographiques.hasOwnProperty( keyzg ) ) {
			structurespartypeorientetcodeinsee[keyzg] = {};
			for( var keytype in typesorientsprincipales ) {
				if( typesorientsprincipales.hasOwnProperty( keytype ) ) {
					var typeorient_id = typesorientsprincipales[keytype];
					structurespartypeorientetcodeinsee[keyzg][typeorient_id] = [];
					structurespartypeorientetcodeinsee[keyzg][keytype] = [];
					var length = zonesgeographiques[keyzg].length;
					for( var i = 0 ; i < length ; i++ ) {
						var structurereferente_id = zonesgeographiques[keyzg][i];
						if( 'undefined' !== typeof structuresreferentesParTypeorientId[typeorient_id] && 'undefined' !== typeof structuresreferentesParTypeorientId[typeorient_id][structurereferente_id] ) {
							structurespartypeorientetcodeinsee[keyzg][typeorient_id].push( structurereferente_id );
							structurespartypeorientetcodeinsee[keyzg][keytype].push( structurereferente_id );
						}
					}
				}
			}
		}
	}

	//--------------------------------------------------------------------------

	function reduceAndSelect(index, numcom, typeorient_id, selected) {
		var isSocioprofessionnelle = 'undefined' !== typeof typesorientsprincipales['Socioprofessionnelle']
			&& in_array(
				typeorient_id,
				typesorientsprincipales['Socioprofessionnelle']
			),
			codeinseeHasTypeorient = 'undefined' !== typeof structurespartypeorientetcodeinsee[numcom]
				&& 'undefined' !== typeof structurespartypeorientetcodeinsee[numcom][typeorient_id]
				&& structurespartypeorientetcodeinsee[numcom][typeorient_id].length > 0,
			structuresLigne = {}, options = '', label, i, length, id, sorted = [];

		// Si on n'a pas encore de live cache pour le code INSEE et le type d'orientation
		if( 'undefined' === typeof liveCache[numcom] || 'undefined' === typeof liveCache[numcom][typeorient_id] ) {
			// Si on possède des structures référentes pour le type d'orientation
			if('undefined' !== typeof structuresreferentesParTypeorientId[typeorient_id]) {
				options = '<option value=""> </option>';

				structuresLigne = Object.clone( structuresreferentesParTypeorientId[typeorient_id] );

				// Amélioration #6259: dans la cohorte des allocataires à transférer, ajout des structures référentes de type "Emploi" dans la liste déroulante s'il n'existe pas de structure Socioprofesionnelle oeuvrant sur le code INSEE de la nouvelle adresse de l'allocataire
				if( true === isSocioprofessionnelle && false === codeinseeHasTypeorient ) {
					length = typesorientsprincipales['Emploi'].length;
					for( i = 0 ; i < length ; i++ ) {
						id = typesorientsprincipales['Emploi'][i];
						if( 'undefined' !== typeof structuresreferentesParTypeorientId[id] ) {
							for( var key in structuresreferentesParTypeorientId[id] ) {
								if( structuresreferentesParTypeorientId[id].hasOwnProperty( key ) ) {
									structuresLigne[key] = structuresreferentesParTypeorientId[id][key];
								}
							}
						}
					}
				}

				// On trie sur l'intitulé pour construire la liste déroulante
				for( var key in structuresLigne ) {
					if( structuresLigne.hasOwnProperty( key ) ) {
						sorted.push( [key, structuresLigne[key]] );
					}
				}
				sorted = sorted.sort(
					function(a,b) {
						return a[1].localeCompare(b[1], 'fr');
					}
				);

				// Remplissage de la liste d'options
				length = sorted.length;
				for( i = 0; i < length; i++ ) {
					label = sorted[i][1].escapeHTML();
					options += '<option value="' + sorted[i][0] + '" title="' + label.replace('"', '&quot;') + '">' + label + '</option>';
				}

				// Population du cache
				if( 'undefined' === typeof liveCache[numcom] ) {
					liveCache[numcom] = {};
				}
				liveCache[numcom][typeorient_id] = options;
			}
		}

		if( 'undefined' !== typeof liveCache[numcom] && 'undefined' !== typeof liveCache[numcom][typeorient_id] ) {
			$( 'Cohorte' + index + 'Transfertpdv93StructurereferenteDstId' ).innerHTML = liveCache[numcom][typeorient_id];
		}

		// Pré-sélection en fonction du code INSEE
		if( '' === selected ) {
			// Si le code INSEE a au moins une structure référente pour l'orientation
			if( codeinseeHasTypeorient ) {
				selected = structurespartypeorientetcodeinsee[numcom][typeorient_id][0];
			}
			// Sinon, si on est en Socioprofessionnelle et que le code INSEE comporte au moins une structure en Emploi
			else if(
				isSocioprofessionnelle
				&& 'undefined' !== typeof structurespartypeorientetcodeinsee[numcom]
				&& 'undefined' !== structurespartypeorientetcodeinsee[numcom]['Emploi']
				&& structurespartypeorientetcodeinsee[numcom]['Emploi'].length > 0
			) {
				selected = structurespartypeorientetcodeinsee[numcom]['Emploi'][0];
			}
		}

		$('Cohorte' + index + 'Transfertpdv93StructurereferenteDstId').setValue(selected);

		observeDisableFieldsOnRadioValue(
			cohorteFormId,
			'data[Cohorte][' + index + '][Transfertpdv93][action]',
			[ 'Cohorte' + index + 'Transfertpdv93StructurereferenteDstId' ],
			1,
			true
		);
	}

	document.observe( "dom:loaded", function() {
		<?php foreach( array_keys( $results ) as $index ):?>
			try {
				reduceAndSelect(
					<?php echo $index;?>,
					'<?php echo $results[$index]['Adresse']['numcom'];?>',
					'<?php echo $results[$index]['Orientstruct']['typeorient_id'];?>',
					'<?php echo Hash::get($this->request->data, "Cohorte.{$index}.Transfertpdv93.structurereferente_dst_id");?>'
				);
			} catch( exception ) {
				console.error( exception );
			}
		<?php endforeach;?>
	} );
</script>
<?php endif;?>