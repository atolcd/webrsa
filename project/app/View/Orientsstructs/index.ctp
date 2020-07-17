<?php
	$personne_id = Hash::get( $dossierMenu, 'personne_id' );
	$personne = Hash::get( (array)Hash::extract( $dossierMenu, "Foyer.Personne.{n}[id={$personne_id}]" ), 0 );
	
	App::uses('WebrsaAccess', 'Utility');
	WebrsaAccess::init($dossierMenu);

	$departement = Configure::read( 'Cg.departement' );

	echo $this->Default3->titleForLayout( $personne );

	echo $this->element( 'ancien_dossier' );

	// Messages explicatifs
	if ( empty( $orientsstructs ) ) {
		echo '<p class="notice">Cette personne ne possède pas encore d\'orientation.</p>';
	}

	if( !empty( $reorientationseps ) ) {
		echo '<p class="notice">Impossible d\'ajouter une nouvelle orientation à ce dossier (passage en EP).</p>';
	}
	else if( !empty( $reorientationscovs ) ) {
		echo '<p class="notice">Impossible d\'ajouter une nouvelle orientation à ce dossier (passage en COV).</p>';
	}
	else if( !$ajoutPossible ) {
		echo '<p class="notice">Impossible d\'ajouter une nouvelle orientation à ce dossier (dossier ne pouvant être orienté).</p>';
	}

	if( !empty( $en_procedure_relance ) ) {
		echo '<p class="notice">Cette personne est en cours de procédure de relance.</p>';
	}

	echo $this->Default3->actions( $actions );

	if( !empty( $nonrespectppae ) ) {
		echo $this->Html->tag( 'h2', __m('Orientation.nonrespectppae') );
		echo $this->Default3->index(
			$nonrespectppae,
			array(
				'Orientstruct.date_valid' => array( 'type' => 'date' ),
				'Sanctionep58.created',
				'Passagecommissionep.etatdossierep',
				'/Sanctionseps58/deleteNonrespectppae/#Sanctionep58.id#' => array(
					'class' => 'delete',
					'confirm' => __m('/Sanctionseps58/deleteNonrespectppae/#Sanctionep58.id# ?')
				),
			),
			array(
				'paginate' => false,
				'options' => $options,
			)
		);
		echo '<br>';
	}

	if( !empty( $reorientationseps ) ) {
		echo $this->Html->tag( 'h2', 'Orientations en cours de passage en EP' );
		echo $this->Default3->index(
			$reorientationseps,
			array(
				'Dossierep.created' => array( 'type' => 'date' ),
				'Dossierep.themeep',
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc' => array( 'label' => ( $departement == 93 ? 'Type de structure' : null ) ),
				'Orientstruct.rgorient' => array( 'type' => 'integer', 'class' => 'number' ),
				'Passagecommissionep.etatdossierep',
				'Commissionep.dateseance',
				'Commissionep.etatcommissionep',
				'/#Actions.view_url#' => array(
					'msgid' => 'Voir',
					'title' => false,
					'class' => 'view',
					'disabled' => '"#Actions.view_enabled#" != "1"'
				),
				'/#Actions.edit_url#' => array(
					'msgid' => 'Modifier',
					'title' => false,
					'class' => 'edit',
					'disabled' => '"#Actions.edit_enabled#" != "1"'
				),
				'/#Actions.delete_url#' => array(
					'msgid' => 'Supprimer',
					'title' => false,
					'class' => 'delete',
					'disabled' => '"#Actions.delete_enabled#" != "1"',
					'confirm' => 'Confirmer la suppression du dossier d\'EP ?'
				),
			),
			array(
				'paginate' => false,
				'options' => $options,
				'id' => 'TableReorientationsepsIndex'
			)
		);
	}

	if( !empty( $dossierseps ) ) {
		echo $this->Html->tag( 'h2', 'Dossiers en cours de passage en EP (hors réorientation)' );
		echo $this->Default3->index(
			$dossierseps,
			array(
				'Dossierep.created' => array( 'type' => 'date' ),
				'Dossierep.themeep',
				'Passagecommissionep.etatdossierep',
				'Commissionep.dateseance',
				'Commissionep.etatcommissionep',
				'/#Actions.view_url#' => array(
					'msgid' => 'Voir',
					'title' => false,
					'class' => 'view',
					'disabled' => '"#Actions.view_enabled#" != "1"'
				)
			),
			array(
				'paginate' => false,
				'options' => $options,
				'id' => 'TableDossiersepsIndex'
			)
		);
		echo '<br>';
	}

	if( !empty( $reorientationscovs ) ) {
		echo $this->Html->tag( 'h2', 'Orientations en cours de passage en COV' );
		echo $this->Default3->index(
			$reorientationscovs,
			array(
				'Dossiercov58.created' => array( 'type' => 'date' ),
				'Dossiercov58.themecov58',
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Orientstruct.rgorient' => array( 'type' => 'integer', 'class' => 'number' ),
				'Passagecov58.etatdossiercov',
				'Cov58.datecommission',
				'Cov58.etatcov',
				'/#Actions.view_url#' => array(
					'msgid' => 'Voir',
					'title' => false,
					'class' => 'view',
					'disabled' => '"#Actions.view_enabled#" != "1"'
				),
				'/#Actions.edit_url#' => array(
					'msgid' => 'Modifier',
					'title' => false,
					'class' => 'edit',
					'disabled' => '"#Actions.edit_enabled#" != "1"'
				),
				'/#Actions.delete_url#' => array(
					'msgid' => 'Supprimer',
					'title' => false,
					'class' => 'delete',
					'disabled' => '"#Actions.delete_enabled#" != "1"',
					'confirm' => 'Confirmer la suppression du dossier de COV ?'
				),
			),
			array(
				'paginate' => false,
				'options' => $options,
				'id' => 'TableReorientationscovsIndex'
			)
		);
	}

	if (!empty ($orientsstructs)) {
		echo $this->Html->tag( 'h2', 'Orientations effectives' );

		if( $departement == 93 ) {
			if( $this->Session->read( 'Auth.User.type' ) === 'cg' ) {
				$fields = array(
					'Orientstruct.date_propo' => array( 'label' => 'Date de préOrientation' ),
					'Orientstruct.date_valid',
					'Orientstruct.propo_algo' => array( 'type' => 'text' ),
					'Orientstruct.origine',
					'Typeorient.lib_type_orient' => array( 'label' => 'Orientation' ),
					'Structurereferente.lib_struc',
					'Orientstruct.rgorient' => array( 'type' => 'text' ),
					'Fichiermodule.nombre' => array( 'type' => 'integer', 'class' => 'number' ),
				);
			}
			else  {
				$fields = array(
					'Orientstruct.date_valid',
					'Orientstruct.origine',
					'Typeorient.lib_type_orient' => array( 'label' => 'Orientation' ),
					'Structurereferente.lib_struc',
					'Orientstruct.rgorient' => array( 'type' => 'text' ),
					'Fichiermodule.nombre' => array( 'type' => 'integer', 'class' => 'number' ),
				);
			}
		}
		else if( $departement == 66 ) {
			$fields = array(
				'Orientstruct.date_propo',
				'Orientstruct.date_valid',
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Orientstruct.rgorient' => array( 'type' => 'text' ),
				'Fichiermodule.nombre' => array( 'type' => 'integer', 'class' => 'number' ),
			);
		}
		else if( $departement == 58 ) {
			$fields = array(
				'Orientstruct.date_propo',
				'Orientstruct.date_valid',
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Orientstruct.rgorient' => array( 'type' => 'text' ),
				'Sitecov58.name',
				'Cov58.datecommission',
				'Cov58.observation',
				'Fichiermodule.nombre' => array( 'type' => 'integer', 'class' => 'number' ),
			);
		}
		else {
			$fields = array(
				'Orientstruct.date_propo',
				'Orientstruct.statut_orient',
				'Orientstruct.date_valid',
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Orientstruct.rgorient' => array( 'type' => 'text' ),
				'Fichiermodule.nombre' => array( 'type' => 'integer', 'class' => 'number' ),
			);
		}

		if( $departement == 66 ) {
			$links = WebrsaAccess::links(
				array(
					'/Orientsstructs/edit/#Orientstruct.id#',
					'/Orientsstructs/impression/#Orientstruct.id#',
					'/Orientsstructs/impression_changement_referent/#Orientstruct.id#',
					'/Orientsstructs/nonrespectppae/#Orientstruct.id#' => array(
						'confirm' => __m('/Orientsstructs/nonrespectppae/#Orientstruct.id# ?'),
					),
					'/Orientsstructs/delete/#Orientstruct.id#' => array(
						'confirm' => true
					),
					'/Orientsstructs/filelink/#Orientstruct.id#'
				)
			);
		}
		else {
			$links = WebrsaAccess::links(
				array(
					'/Orientsstructs/edit/#Orientstruct.id#',
					'/Orientsstructs/impression/#Orientstruct.id#',
					'/Orientsstructs/delete/#Orientstruct.id#' => array(
						'confirm' => true,
					),
					'/Orientsstructs/nonrespectppae/#Orientstruct.id#' => array(
						'confirm' => __m('/Orientsstructs/nonrespectppae/#Orientstruct.id# ?'),
					),
					'/Orientsstructs/filelink/#Orientstruct.id#'
				)
			);
		}

		// Rendu du tableau
		echo $this->Default3->index(
			$orientsstructs,
			$fields + $links,
			array(
				'paginate' => false,
				'options' => $options
			)
		);
	}
?>
<script type="text/javascript">
	//<![CDATA[
	document.observe( 'dom:loaded', function() {
		// TODO: en faire une fonction ?
		var removeLinks = {
			'TableReorientationsepsIndex': {
				// CG 58
				'nonorientationsproseps58': {
					'edit': false,
					'delete': false
				},
				'regressionorientationep58': {
					'edit': false,
					'delete': true
				},
				// CG 66
				'saisinesbilansparcourseps66': {
					'edit': false,
					'delete': false
				},
				// CG 93
				'reorientationseps93': {
					'edit': true,
					'delete': true
				},
				'nonorientationsproseps93': {
					'edit': false,
					'delete': false
				}
			},
			'TableReorientationscovsIndex': {
				// CG 58
				'proposorientationscovs58': {
					'edit': true,
					'delete': true
				},
				'proposorientssocialescovs58': {
					'edit': false,
					'delete': false
				},
				'proposnonorientationsproscovs58': {
					'edit': false,
					'delete': false
				}
			}
		};
		/**
		 * Permet de masquer les liens inactifs en fonction du type de dossier
		 * et de la thématique pour etre cohérent par-rapport à ce qui existait
		 * avant.
		 */
		for( var tableId in removeLinks ) {
			if( removeLinks.hasOwnProperty( tableId ) ) {
				var table = $( tableId );

				if( table ) {
					var removed = 0;

					for( var thematique in removeLinks[tableId] ) {
						if( removeLinks[tableId].hasOwnProperty( thematique ) ) {
							for( var linkType in removeLinks[tableId][thematique] ) {
								if( removeLinks[tableId][thematique].hasOwnProperty( linkType ) ) {
									if( !removeLinks[tableId][thematique][linkType] ) {
										var link = $(table).down( 'tbody' ).down( 'span.link.disabled.' + thematique + '.' + linkType );
										if( link ) {
											$(link).up( 'td' ).remove();
											removed++;
										}
									}
								}
							}
						}
					}

					if( removed > 0 ) {
						var thActions = $(table).down( 'thead th.actions' );
						if( thActions ) {
							$(thActions).writeAttribute( 'colspan', $(thActions).readAttribute( 'colspan' ) - removed );
						}
					}
				}
			}
		}
	} );
	//]]>
</script>