<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	$searchFormId = Inflector::camelize( Inflector::underscore( Inflector::classify( $this->request->params['controller'] ) )."_{$this->request->params['action']}_form" );
	$type = $this->Session->read( 'Auth.User.type' );
	$domain = 'tableauxsuivispdvs93';

	// TODO: dans le contrôleur
	$tableau = null;
	$tableaux = array_keys( (array)$options['Tableausuivipdv93']['name'] );
	if( in_array( $this->request->params['action'], $tableaux ) ) {
		$tableau = $this->request->params['action'];
	}
	else if( $this->request->params['action'] === 'view' ) {
		$tableau = $tableausuivipdv93['Tableausuivipdv93']['name'];
	}
	else {
		$pass = Hash::get( $this->request->params, 'pass.0' );
		if( in_array( $this->request->params['action'], $tableaux ) ) {
			$pass = $this->request->params['action'];
		}
	}

	if( isset( $tableausuivipdv93 ) ) {
		echo $this->Default3->titleForLayout(
			Hash::merge(
				$tableausuivipdv93,
				array(
					'Tableausuivipdv93' => array(
						'name' => value( $options['Tableausuivipdv93']['name'], $tableausuivipdv93['Tableausuivipdv93']['name'] )
					)
				)
			)
		);
	}
	else {
		echo $this->Default3->titleForLayout();
	}

	$actions['/'.Inflector::camelize( $this->request->params['controller'] ).'/'.$this->request->params['action'].'/#toggleform'] =  array(
		'title' => 'Visibilité formulaire',
		'text' => 'Formulaire',
		'class' => 'search',
		'onclick' => "$( '{$searchFormId}' ).toggle(); return false;"
	);
	echo $this->Default3->actions( $actions );

	// 1. Formulaire de recherche, CG
	echo $this->Default3->DefaultForm->create( null, array( 'url' => array( 'controller' => $this->request->params['controller'], 'action' => $this->request->params['action'] ), 'novalidate' => 'novalidate', 'id' => $searchFormId, 'class' => ( isset( $results ) ? 'folded' : 'unfolded' ) ) );

	echo $this->Default3->subform(
		array(
			'Search.annee' => array( 'empty' => ( empty( $tableau ) ? true : false ) )
		),
		array(
			'options' => $options
		)
	);

	if( empty( $tableau ) ) {
		echo $this->Default3->subform(
			array(
				'Search.type' => array( 'empty' => true, 'type' => 'select' )
			),
			array(
				'options' => $options
			)
		);
	}

	if( in_array( $type, array( 'cg' ) ) ) {
		echo '<fieldset class="invisible" id="SearchStructurereferenteFieldsetPdv">';
		if( $hasCommunautessrs ) {
			echo $this->Default3->subform(
				array(
					'Search.communautesr_id' => array( 'empty' => true, 'type' => 'select' )
				),
				array(
					'options' => $options
				)
			);
		}
	}

	if( in_array( $type, array( 'cg', 'externe_cpdvcom' ) ) ) {
		echo $this->Default3->subform(
			array(
				'Search.structurereferente_id' => array( 'empty' => true, 'type' => 'select' )
			),
			array(
				'options' => $options
			)
		);
		echo '</fieldset>';
	}

	if( in_array( $type, array( 'cg', 'externe_cpdvcom', 'externe_cpdv' ) ) ) {
		echo $this->Default3->subform(
			array(
				'Search.referent_id' => array( 'empty' => true, 'type' => 'select' )
			),
			array(
				'options' => $options
			)
		);
		echo '</fieldset>';
	}

	if( !empty( $tableau ) && in_array( $type, array( 'cg', 'externe_cpdvcom' ) ) ) {
		echo '<fieldset class="invisible" id="SearchStructurereferenteIdMacro">';
		echo $this->SearchForm->dependantCheckboxes(
			'Search.structurereferente_id',
			array(
				'options' => $options['Search']['structurereferente_id'],
				'class' => 'divideInto3Columns',
				'buttons' => true,
				'autoCheck' => true,
				'domain' => $domain,
				'hide' => true,
				'hiddenField' => false
			)
		);
		echo '</fieldset>';
	}

	// Formulaire de recherche seulement
	if( empty( $tableau ) ) {
		echo $this->Default3->subform(
			array(
				'Search.user_id' => array( 'empty' => true, 'type' => 'select' ),
				'Search.tableau' => array( 'empty' => true, 'type' => 'select' )
			),
			array(
				'options' => $options
			)
		);
	}
	else {
		if( in_array( $tableau, array( 'tableaud1', 'tableaud2' ) ) ) {
			echo $this->Default3->subform(
				array(
					'Search.soumis_dd_dans_annee' => array( 'type' => 'checkbox' )
				)
			);
		}
		else if( in_array( $tableau, array( 'tableau1b3' ) ) ) {
			echo $this->Default3->subform(
				array(
					'Search.dsps_maj_dans_annee' => array( 'type' => 'checkbox' )
				)
			);
		}
		else if( in_array( $tableau, array( 'tableau1b4', 'tableau1b5' ) ) ) {
			echo $this->Default3->subform(
				array(
					'Search.typethematiquefp93_id' => array( 'type' => 'select', 'empty' => true ),
					'Search.rdv_structurereferente' => array( 'type' => 'checkbox' )
				),
				array(
					'options' => $options
				)
			);
		}
		else if( in_array( $tableau, array( 'tableau1b6' ) ) ) {
			echo $this->Default3->subform(
				array(
					'Search.rdv_structurereferente' => array( 'type' => 'checkbox' )
				),
				array(
					'options' => $options
				)
			);
		}
	}

	if( !in_array( $this->request->params['action'], array( 'view', 'historiser' ) ) ) {
		echo $this->Default3->DefaultForm->buttons( array( 'Search' ) );
	}

	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->dependantSelect(
		array(
			'Search.structurereferente_id' => 'Search.referent_id'
		)
	);

	echo $this->Observer->disableFormOnSubmit( $searchFormId );

	echo $this->Observer->disableFieldsOnCheckbox(
		'Search.structurereferente_id_choice',
		array(
			'Search.communautesr_id',
			'Search.structurereferente_id',
			'Search.referent_id',
		),
		true,
		true
	);

	echo $this->Observer->disableFieldsOnValue(
		'Search.communautesr_id',
		'Search.structurereferente_id',
		'',
		false,
		true
	);
	echo $this->Observer->disableFieldsOnValue(
		'Search.communautesr_id',
		'Search.referent_id',
		'',
		false,
		true
	);
?>
<script type="text/javascript">
	//<![CDATA[
	var
		empty = function( value ) {
			return value === undefined || value === null || value === false || value === '';
		},
		formElmtValue = function(elmt) {
			var result;

			try {
				result = $F(elmt);
			} catch(e) {
				result = null;
			}

			return result;
		},
		enableFieldset = function(fieldset, toggleVisibility) {
			toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;
			fieldset = $( fieldset );

			fieldset.removeClassName( 'disabled' );
			if( toggleVisibility ) {
				fieldset.show();
			}

			$( fieldset ).getElementsBySelector( 'div.input', 'div.checkbox' ).each( function( elmt ) {
				elmt.removeClassName( 'disabled' );
			} );
			$( fieldset ).getElementsBySelector( 'input', 'select', 'button', 'textarea' ).each( function( elmt ) {
				// INFO: elmt.disable() ne fonctionne pas avec des button
				try{
					elmt.enable();
				} catch( err ) {
					elmt.disabled = false;
				}
			} );
		},
		disableFieldset = function(fieldset, toggleVisibility) {
			toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;
			fieldset = $( fieldset );

			fieldset.addClassName( 'disabled' );
			if( toggleVisibility ) {
				fieldset.hide();
			}

			$( fieldset ).getElementsBySelector( 'div.input', 'div.checkbox' ).each( function( elmt ) {
				elmt.addClassName( 'disabled' );
			} );
			$( fieldset ).getElementsBySelector( 'input', 'select', 'button', 'textarea' ).each( function( elmt ) {
				// INFO: elmt.enable() ne fonctionne pas avec des button
				try{
					elmt.disable();
				} catch( err ) {
					elmt.disabled = true;
				}
			} );
		},
		disableTableauxsuivisFormElmtsOnChange = function() {
			var communautesr_id, choice;

			try {
				communautesr_id = formElmtValue('SearchCommunautesrId'),
				choice = formElmtValue('SearchStructurereferenteIdChoice');

				if(false === empty(communautesr_id)) {
					disableFieldset('SearchStructurereferenteIdFieldset', true);
					disableFieldset('SearchStructurereferenteIdMacro', true);
				} else {
					enableFieldset('SearchStructurereferenteIdMacro', true);

					if(true === empty(choice)) {
						disableFieldset('SearchStructurereferenteIdFieldset', true);
					} else {
						enableFieldset('SearchStructurereferenteIdFieldset', true);
					}
				}
			} catch(e) {
				console.log(e);
			}
		};

	try {
		$('SearchCommunautesrId').observe( 'change', function( event ) {
			disableTableauxsuivisFormElmtsOnChange();
		} );
	} catch(e) {
		console.log(e);
	}

	document.observe( "dom:loaded", function() {
		disableTableauxsuivisFormElmtsOnChange();
	} );
	//]]>
</script>