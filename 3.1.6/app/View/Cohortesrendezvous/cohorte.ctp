<?php
	if( false === $this->request->is( 'ajax' ) ) {
		echo $this->element( 'required_javascript' );

		echo $this->Default3->titleForLayout();

		if( Configure::read( 'debug' ) > 0 ) {
			echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
			echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
		}

		echo $this->Default3->actions(
			array(
				'/Cohortesrendezvous/cohorte/#toggleform' => array(
					'onclick' => '$(\'CohortesrendezvousCohorteSearchForm\').toggle(); return false;'
				),
			)
		);

		// 1. Moteur de recherche
		echo $this->Xform->create( null, array( 'id' => 'CohortesrendezvousCohorteSearchForm', 'class' => ( ( isset( $results ) ) ? 'folded' : 'unfolded' ) ) );

		echo $this->Allocataires->blocDossier(
			array(
				'options' => $options,
				'prefix' => 'Search'
			)
		);

		echo $this->Allocataires->blocAdresse(
			array(
				'options' => $options,
				'prefix' => 'Search'
			)
		);

		echo $this->Allocataires->blocAllocataire(
			array(
				'options' => $options,
				'prefix' => 'Search'
			)
		);

		// Thématiques du RDV
		$fieldsThematiquesrdvs = '';
		$observeThematiquesrdvs = '';
		if( isset( $options['RendezvousThematiquerdv']['thematiquerdv_id'] ) && !empty( $options['RendezvousThematiquerdv']['thematiquerdv_id'] ) ) {
			foreach( $options['RendezvousThematiquerdv']['thematiquerdv_id'] as $typerdv_id => $thematiques ) {
				$input = $this->Xform->input(
					'Search.Rendezvous.thematiquerdv_id',
					array(
						'type' => 'select',
						'multiple' => 'checkbox',
						'options' => $thematiques,
						'label' => 'Thématiques'
					)
				);

				$fieldsThematiquesrdvs .= $this->Xhtml->tag(
					'fieldset',
					$input,
					array(
						'id' => "SearchRendezvousThematiquerdvId{$typerdv_id}",
						'class' => 'invisible',
					)
				);

				$observeThematiquesrdvs .= $this->Observer->disableFieldsetOnValue(
					'Search.Rendezvous.typerdv_id',
					"SearchRendezvousThematiquerdvId{$typerdv_id}",
					$typerdv_id,
					false,
					true
				);
			}
		}

		echo $this->Xhtml->tag(
			'fieldset',
			$this->Xhtml->tag( 'legend', __d( 'cohortesrendezvous', 'Search.Rendezvous' ) )
			.$this->Xform->input( 'Search.Rendezvous.statutrdv_id', array( 'type' => 'select', 'multiple' => 'checkbox', 'options' => $options['Rendezvous']['statutrdv_id'], 'domain' => 'cohortesrendezvous' ) )
			.$this->Allocataires->communautesr( 'Rendezvous', array( 'options' => array( 'Search' => $options ), 'hide' => false ) )
			.$this->Xform->input( 'Search.Rendezvous.structurereferente_id', array( 'type' => 'select', 'empty' => true, 'options' => $options['PersonneReferent']['structurereferente_id'], 'domain' => 'cohortesrendezvous' ) )
			.$this->Xform->input( 'Search.Rendezvous.referent_id', array( 'type' => 'select', 'empty' => true, 'options' => $options['PersonneReferent']['referent_id'], 'domain' => 'cohortesrendezvous' ) )
			.$this->Xform->input( 'Search.Rendezvous.typerdv_id', array( 'type' => 'select', 'options' => $options['Rendezvous']['typerdv_id'], 'empty' => true, 'domain' => 'cohortesrendezvous' ) )
			.$fieldsThematiquesrdvs
			.$this->SearchForm->dateRange( 'Search.Rendezvous.daterdv', array( 'domain' => 'cohortesrendezvous' ) )
		);

		// TODO: Permanence liée à la structure

		echo $this->Observer->dependantSelect(
			array( 'Search.Rendezvous.structurereferente_id' => 'Search.Rendezvous.referent_id' )
		);

		echo $this->Allocataires->blocReferentparcours(
			array(
				'options' => $options,
				'prefix' => 'Search'
			)
		);

		echo $this->Allocataires->blocPagination(
			array(
				'options' => $options,
				'prefix' => 'Search'
			)
		);

		echo $this->Xform->end( 'Search' );

		echo $this->Observer->disableFormOnSubmit( 'CohortesrendezvousCohorteSearchForm' );

		echo $observeThematiquesrdvs;
	}

	// 2. Traitement des résultats de la recherche
	if( isset( $results ) ) {
		// TODO: à factoriser avec Dsps::index() + les exportcsv
		$fields = Hash::normalize( (array)Configure::read( 'Cohortesrendezvous.cohorte.fields' ) );

		// On recherche le type de chacun des champs
		foreach( $fields as $fieldName => $params ) {
			$params = (array)$params;
			if( !isset( $params['type'] ) ) {
				$fields[$fieldName]['type'] = $this->Default3->DefaultTable->DefaultTableCell->DefaultData->type( $fieldName );
			}
		}

		$fields = array_merge(
			$fields,
			array(
				'data[Cohorte][Rendezvous][][statutrdv_id]' => array(
					'type' => 'select',
					'empty' => true,
					'label' => false,
					'options' => $options['Rendezvous']['statutrdv_id'],
					'value' => '#Rendezvous.statutrdv_id#'
				),
				// INFO: début champs cachés (en css)!
				'data[Cohorte][Rendezvous][][id]' => array(
					'type' => 'hidden',
					'label' => false,
					'value' => '#Rendezvous.id#'
				),
				'data[Cohorte][Rendezvous][][personne_id]' => array(
					'type' => 'hidden',
					'label' => false,
					'value' => '#Rendezvous.personne_id#'
				),
				'data[Cohorte][Dossier][][id]' => array(
					'type' => 'hidden',
					'label' => false,
					'value' => '#Dossier.id#'
				),
				'/Rendezvous/view/#Rendezvous.id#' => array(
					'class' => 'view external'
				)
			)
		);

		// On prépare les paramètres de pagination à la main en cas de requête Ajax.
		$urlParams = (array)$this->request->data;
		unset( $urlParams['Cohorte'] );
		$urlParams = Hash::flatten( $urlParams, '__' );
		$this->Default3->DefaultPaginator->options(
			array( 'url' => $urlParams )
		);
		$paginationFormat = SearchProgressivePagination::format( !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' ) );

		// 1. On est en ajax
		if( $this->request->is( 'ajax' ) ) {
			$pagination = $this->Default3->pagination( array( 'format' => $paginationFormat ) );

			$json = array(
				'success' => true,
				'pagination' => $pagination,
				'tr' => $this->Default3->DefaultTable->tbody(
					$results,
					$fields,
					array(
						'options' => $options
					)
				)
			);

			echo $this->element( 'json', compact( 'json' ) );
		}
		// 2. On n'est pas en ajax
		else {
			echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

			if( !empty( $results ) ) {
				echo $this->Default3->DefaultForm->create( null, array( 'id' => 'CohortesrendezvousCohorteForm' ) );
			}

			echo $this->Default3->index(
				$results,
				$fields,
				array(
					'format' => $paginationFormat,
					'options' => $options
				)
			);
			// TODO: infobulle

			if( !empty( $results ) ) {
				echo $this->Default3->DefaultForm->end();

				echo $this->Observer->disableFormOnSubmit( 'CohortesrendezvousCohorteForm' );
			}
		}
	}
?>
<?php if( isset( $results ) && !$this->request->is( 'ajax' ) ):?>
<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
			( $this->Permissions->check( $this->request->params['controller'], 'exportcsv' ) && count( $results ) > 0 )
		);
	?></li>
</ul>
<?php
	echo $this->element(
		'modalbox',
		array(
			'modalid' => "ModalLoadingCog",
			'modalmessage' => 'Enregistrement en cours...',
			'modalcontent' => null
		)
	);
?>
<script type="text/javascript">
	//<![CDATA[
	document.observe( "dom:loaded", function() {
		// TODO: une fonction makeAjaxCohorte() ?
		// Paramètres: modalLoadingId, searchFormId, resultTableId, resultFormId, ... + les autres plus compliqués
		var defaultModalMessage = $( 'ModalLoadingCog' ).select( 'div.message' )[0].innerHTML,
			setModalError = function( msgpart ) {
				var errorText = 'Erreur lors de l\'enregistrement: ' + msgpart;
				errorText += '<br />Vous pouvez fermer ce message d\'erreur et recharger la page avant de réessayer.';
				$( 'ModalLoadingCog' ).select( 'div.message' )[0].update( errorText );
			},
			observeRow = function ( tr ) {
				var select = $(tr).select( 'select' )[0],
					fields = $(tr).select( 'input', 'select' );

				$( select ).observe( 'change', function( event ) {
					var key,
						// 0. Moteur de recherche -> data[Search]
						requestData = $( 'CohortesrendezvousCohorteSearchForm' ).serialize( true ),
						// 1. Ensemble des champs cachés du formulaire -> data[Cohorte][Hidden]
						allHiddenFields = Form.serializeElements( $$( '#TableCohortesrendezvousCohorte tbody tr input[type=hidden]' ), { hash: true, submit: false } ),
						// 2. Ligne qui a changé -> data[Cohorte][Changed]
						allChangedFields = Form.serializeElements( fields, { hash: true, submit: false } ),
						rowOffset = $(event).element().name.replace( /^.*\]\[([0-9]+)\]\[.*$/g, '$1' );

					// On remet la valeur par défaut du message de la boîte modale
					$( 'ModalLoadingCog' ).select( 'div.message' )[0].update( defaultModalMessage );

					// Renommage des clés des champs cachés
					for( key in allHiddenFields ) {
						if( allHiddenFields.hasOwnProperty( key ) ) {
							requestData[key.replace( '[Cohorte]', '[Cohorte][Hidden]' )] = allHiddenFields[key];
						}
					}

					// Renommage des clés de l'enregistrement modifié
					for( key in allChangedFields ) {
						if( allChangedFields.hasOwnProperty( key ) ) {
							requestData[key.replace( '[Cohorte]', '[Cohorte][Changed]' )] = allChangedFields[key];
						}
					}

					// 3. Offset de la ligne qui a changé -> data[Cohorte][Changed][offset]
					requestData['data[Cohorte][Changed][offset]'] = rowOffset;

					var remplaceur = function( match, attribute, value, offset, string ) {
					  return attribute + '="' + value.replace( /0/g, rowOffset ) + '"';
					};

					new Ajax.Request(
						'<?php echo Router::url();?>',
						{
							asynchronous: true,
							evalScripts: true,
							parameters: requestData,
							onCreate: function() {
								$( 'ModalLoadingCog' ).show();
							},
							onSuccess: function( request ) {
								// Soit on reçoit une erreur en JSON
								try {
									var json = request.responseText.evalJSON( true );

									// 1. Suppression de l'erreur précédente éventuelle
									$(tr).select( '.error-message' ).each( function( error ) {
										$(error).remove();
									} );

									// 2. Traitement du retour en erreur
									if( json.success != true ) {
										for( var key in json.errors ) {
											if( json.errors.hasOwnProperty( key ) ) {
												var td = $(select).up( 'div.input.select' ).up( 'td' );
												var messages = json.errors[key]['statutrdv_id'];
												var div = new Element( 'div', { 'class': 'error-message' } );
												if( messages.length === 1 ) {
													(div).update( messages[0] );
												}
												else {
													var ul = new Element( 'ul' );
													$(messages).each( function( message ) {
														var li = new Element( 'li', {} ).update( message );
														$(ul).insert( { 'bottom' : li } );
													} );
													$(div).update( ul );
												}

												$( td ).removeClassName( 'error' );
												$( td ).addClassName( 'error' );
												$( td ).insert( { 'bottom' : div } );
											}
										}
									}
									// ... et aussi en cas de succès
									else {
										// Suppression de la ligne enregistrée
										$(tr).remove();

										// Ajout de la nouvelle rangée, si elle existe
										if( json.tr !== '' && json.tr !== null ) {
											var newTr = json.tr;

											newTr = newTr.replace( '<tbody>', '' ).replace( '</tbody>', '' );
											newTr = newTr.replace( /(name|id)="([^"]+)"/g, remplaceur );

											$( 'TableCohortesrendezvousCohorte' ).down( 'tbody' ).insert( { 'bottom' : newTr } );
											var tbody = $( 'TableCohortesrendezvousCohorte' ).down( 'tbody' ),
												newRows = $(tbody).childElements();
											observeRow( newRows[newRows.length - 1] );
										}

										// S'il reste des enregistrements, mise à jour des classes odd/even des rangées, mise à jour de la pagination
										var index = 1,
											rows = $$( '#TableCohortesrendezvousCohorte tbody tr' );
										if( $(rows).length > 0 ) {
											$(rows).each( function( row ) {
												$(row).removeClassName( 'even' );
												$(row).removeClassName( 'odd' );
												$(row).addClassName( ( index % 2 === 0 ) ? 'even' : 'odd' );
												index++;
											} );
										}
										// Lorsqu'il n'y a plus de résultats, on remplace la pagination et le tableau par un message
										else {
											var actions = $( 'CohortesrendezvousCohorteForm' ).next( 'ul.actionMenu' );
											var emptyMessage = '<?php echo js_escape( $this->Default3->DefaultHtml->tag( 'p', 'Aucun enregistrement', array( 'class' => 'notice' ) ) );?>';
											$( 'CohortesrendezvousCohorteForm' ).replace( emptyMessage );

											if( actions !== undefined ) {
												$(actions).select( 'li a' ).each( function(link) {
													// span à la place du a, on ajoute la classe disabled
													var span = new Element( 'span' );
													$(span).innerHTML = $(link).innerHTML;
													$(span).addClassName( $(link).readAttribute( 'class' ) );
													$(span).addClassName( 'disabled' );
													$(link).replace( span );
												} );
											}
										}

										$$( 'div.pagination' ).each( function( block ) {
											$(block).replace( json.pagination );
										} );
									}

									$( 'ModalLoadingCog' ).hide();
								// Soit on reçoit autre chose, ce qui équivaut à une erreur inattendue
								} catch( e ) {
									setModalError( 'erreur inattendue, ' + e );
								}
							},
							onFailure: function( request ) {
								setModalError( 'erreur ' + request.status + ', ' + request.statusText );
							}
						}
					);
				} );
			};

		$$( '#TableCohortesrendezvousCohorte tbody tr' ).each( function( tr ) {
			observeRow( tr );
		} );
	} );
	//]]>
</script>
<?php endif;?>