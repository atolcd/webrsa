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
		structuresreferentesParCodeInsee = <?php echo json_encode( Hash::get( $options, 'Structurereferente.listeParCodeInseeFormat' ) );?>,
		zonesgeographiques = <?php echo json_encode( Hash::get( $options, 'Structurereferente.listeParCodeInsee' ) );?>,
		typesorients = <?php echo json_encode( Hash::get( $options, 'Orientstruct.typeorient_id' ) );?>,
		allStructures = <?php echo json_encode( Hash::get( $options, 'Structurereferente.all' ) );?>,
		liveCache = {},
		structurespartypeorientetcodeinsee = {};

	//--------------------------------------------------------------------------

	for( var keyzg in zonesgeographiques ) {
		if( zonesgeographiques.hasOwnProperty( keyzg ) ) {
			structurespartypeorientetcodeinsee[keyzg] = {};
			for( var keytype in typesorients ) {
				if( typesorients.hasOwnProperty( keytype ) ) {
					var typeorient_id = Object.keys(typesorients[keytype])[0];
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
		var	codeinseeHasTypeorient = 'undefined' !== typeof structurespartypeorientetcodeinsee[numcom]
				&& 'undefined' !== typeof structurespartypeorientetcodeinsee[numcom][typeorient_id]
				&& structurespartypeorientetcodeinsee[numcom][typeorient_id].length > 0,
			structuresLigne = {}, options = '', label, i, length, id, sorted = [];

		// Si on n'a pas encore de live cache pour le code INSEE et le type d'orientation
		if( 'undefined' === typeof liveCache[numcom] || 'undefined' === typeof liveCache[numcom][typeorient_id] ) {
			options = '<option value=""> </option>';
			// Si on possède des structures référentes pour le code insee
			if('undefined' !== typeof structuresreferentesParCodeInsee[numcom]) {
				structuresLigne = Object.clone( structuresreferentesParCodeInsee[numcom] );
			} else {
				structuresLigne = Object.clone( allStructures );
			}

				// Remplissage de la liste d'options
				for(var group in structuresLigne) {
					options += '<optgroup label="' + group + '">';
					for(var struct in structuresLigne[group]) {
						label = structuresLigne[group][struct].escapeHTML();
						options += '<option value="' + struct + '" title="' + label.replace('"', '&quot;') + '">' + label + '</option>';
					}
					options += '</optgroup>';
				}

				// Population du cache
				if( 'undefined' === typeof liveCache[numcom] ) {
					liveCache[numcom] = {};
				}
				liveCache[numcom][typeorient_id] = options;

		}

		if( 'undefined' !== typeof liveCache[numcom] && 'undefined' !== typeof liveCache[numcom][typeorient_id] ) {
			$( 'Cohorte' + index + 'Transfertpdv93StructurereferenteDstId' ).innerHTML = liveCache[numcom][typeorient_id];
		}

		// Pré-sélection en fonction du type d'orientation
		if( '' === selected ) {
			// Si le code INSEE a au moins une structure référente pour l'orientation
			if( codeinseeHasTypeorient ) {
				selected = structurespartypeorientetcodeinsee[numcom][typeorient_id][0];
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
					'<?php echo $results[$index]['Structurereferente']['typeorient_id'];?>',
					'<?php echo Hash::get($this->request->data, "Cohorte.{$index}.Transfertpdv93.structurereferente_dst_id");?>'
				);
			} catch( exception ) {
				console.error( exception );
			}
		<?php endforeach;?>
	} );
</script>
<?php endif;?>