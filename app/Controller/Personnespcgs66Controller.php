<?php
	/**
	 * Code source de la classe Personnespcgs66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Personnespcgs66Controller (CG 66).
	 *
	 * @package app.Controller
	 */
	class Personnespcgs66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Personnespcgs66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
			'Fileuploader',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default2',
			'Fileuploader',
			'Locale',
			'Romev3',
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Personnepcg66',
			'Dossierpcg66',
			'Option',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Personnespcgs66:edit',
			'view' => 'Personnespcgs66:index',
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'delete' => 'delete',
			'edit' => 'update',
			'view' => 'read',
		);
		
		/**
		 *
		 */
		protected function _setOptions() {
			$options = array( );

			$this->set( 'statutlist', $this->Dossierpcg66->Personnepcg66->Statutpdo->find( 'list', array( 'order' => 'Statutpdo.libelle ASC', 'conditions' => array( 'Statutpdo.isactif' => '1' ) ) ) );
			$this->set( 'situationlist', $this->Dossierpcg66->Personnepcg66->Situationpdo->find( 'list', array( 'order' => 'Situationpdo.libelle ASC', 'conditions' => array( 'Situationpdo.isactif' => '1' ) ) ) );
			$this->set( compact( 'options' ) );

			$this->set( 'gestionnaire', $this->User->find(
							'list', array(
						'fields' => array(
							'User.nom_complet'
						),
						'conditions' => array(
							'User.isgestionnaire' => 'O'
						)
							)
					)
			);

			// Récupération des codes ROM stockés en paramétrage
			$options['Coderomesecteurdsp66'] = ClassRegistry::init( 'Libsecactderact66Secteur' )->find(
					'list', array(
				'contain' => false,
				'order' => array( 'Libsecactderact66Secteur.code' )
					)
			);
			$codesromemetiersdsps66 = ClassRegistry::init( 'Libderact66Metier' )->find(
					'all', array(
				'contain' => false,
				'order' => array( 'Libderact66Metier.code' )
					)
			);
			foreach( $codesromemetiersdsps66 as $coderomemetierdsp66 ) {
				$options['Coderomemetierdsp66'][$coderomemetierdsp66['Libderact66Metier']['coderomesecteurdsp66_id'].'_'.$coderomemetierdsp66['Libderact66Metier']['id']] = $coderomemetierdsp66['Libderact66Metier']['code'].'. '.$coderomemetierdsp66['Libderact66Metier']['name'];
			}
			$this->set( compact( 'options' ) );
		}

		/**
		 *
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 */
		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 */
		protected function _add_edit( $id = null ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			if( $this->action == 'add' ) {
				$dossier_id = $this->Dossierpcg66->dossierId( $id );
			}
			else {
				$dossier_id = $this->Dossierpcg66->Personnepcg66->dossierId( $id );
			}
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $dossier_id ) );
			$this->set( compact( 'dossierMenu' ) );

			$personnes = array( );
			$premierAjout = false;

            // Retour à la liste en cas d'annulation
            if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
                if( $this->action == 'edit' ) {
                    $id = $this->Personnepcg66->field( 'dossierpcg66_id', array( 'id' => $id ) );
                }
                $this->Jetons2->release( $dossier_id );
                $this->redirect( array( 'controller' => 'dossierspcgs66', 'action' => 'edit', $id ) );
            }
			// Récupération des id afférents
			if( $this->action == 'add' ) {
				$dossierpcg66_id = $id;

				//$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $this->Dossierpcg66->dossierId( $id ) ) ) );

				$dossierpcg66 = $this->Dossierpcg66->find(
						'first', array(
					'conditions' => array(
						'Dossierpcg66.id' => $id
					),
					'contain' => array(
						'Personnepcg66' => array(
							'Statutpdo',
							'Situationpdo'
						)
					)
						)
				);
				$this->set( 'dossierpcg66', $dossierpcg66 );

				if( !empty( $dossierpcg66['Dossierpcg66']['bilanparcours66_id'] ) ) {
					$personne = $this->Personnepcg66->Dossierpcg66->Bilanparcours66->Orientstruct->Personne->find(
                        'first',
                        array(
                            'fields' => array(
                                'Personne.id',
                                'Bilanparcours66.examenaudition',
                                'Bilanparcours66.examenauditionpe'
                            ),
                            'conditions' => array(
                                'Bilanparcours66.id' => $dossierpcg66['Dossierpcg66']['bilanparcours66_id']
                            ),
                            'joins' => array(
                                $this->Personnepcg66->Dossierpcg66->Bilanparcours66->Orientstruct->Personne->join( 'Bilanparcours66' ),
                                $this->Personnepcg66->Dossierpcg66->Bilanparcours66->Orientstruct->Personne->join( 'Orientstruct', array( 'LEFT OUTER') )
                            ),
                            'contain' => false
                        )
					);

					if( empty( $dossierpcg66['Personnepcg66'] ) ) {
						$premierAjout = true;
					}
					else {
						$dejaAjoute = false;
						foreach( $dossierpcg66['Personnepcg66'] as $personnepcg66 ) {
							if( $personnepcg66['id'] == $personne['Personne']['id'] ) {
								$dejaAjoute = true;
							}
						}
						if( !$dejaAjoute ) {
							$premierAjout = true;
						}
					}

					if( $premierAjout ) {
						$situationspdos = array( );
						if( $personne['Bilanparcours66']['examenaudition'] == 'DOD' || $personne['Bilanparcours66']['examenauditionpe'] == 'noninscriptionpe' ) {
							$situationspdos = $this->Personnepcg66->Situationpdo->find(
									'all', array(
								'conditions' => array(
									'Situationpdo.nc' => '1'
								),
								'contain' => false
									)
							);
						}
						elseif( $personne['Bilanparcours66']['examenaudition'] == 'DRD' || $personne['Bilanparcours66']['examenauditionpe'] == 'radiationpe' ) {
							$situationspdos = $this->Personnepcg66->Situationpdo->find(
									'all', array(
								'conditions' => array(
									'Situationpdo.nr' => '1'
								),
								'contain' => false
									)
							);
						}

						$motif = array( );
						foreach( $situationspdos as $situationpdo ) {
							$motif['Situationpdo']['Situationpdo'][] = $situationpdo['Situationpdo']['id'];
						}

						$personnepcg66 = array_merge(
								array(
							'Personnepcg66' => array(
								'personne_id' => $personne['Personne']['id']
							)
								), $motif
						);
					}
				}

				$foyer_id = Set::classicExtract( $dossierpcg66, 'Dossierpcg66.foyer_id' );
				$dossier_id = $this->Dossierpcg66->Foyer->dossierId( $foyer_id );

				$this->Jetons2->get( $dossier_id );


				//Liste des personnes appartenant au foyer dont le dossier fait question
				$personnes = $this->Personnepcg66->Personne->find(
                    'list', array(
					'fields' => array( 'nom_complet' ),
					'conditions' => array(
						'Personne.foyer_id' => $foyer_id,
						'Personne.id IN (
								'.$this->Personnepcg66->Personne->Prestation->sq(
								array(
									'alias' => 'prestations',
									'fields' => array( 'prestations.personne_id' ),
									'conditions' => array(
										'prestations.natprest = \'RSA\'',
//										'prestations.rolepers' => array( 'DEM', 'CJT' )
									),
									'contain' => false
								)
						).
						' )',
						'Personne.id NOT IN (
								'.$this->Personnepcg66->sq(
								array(
									'alias' => 'personnespcgs66',
									'fields' => array( 'personnespcgs66.personne_id' ),
									'conditions' => array(
										'personnespcgs66.dossierpcg66_id' => $id
									),
									'contain' => false
								)
						).
						' )',
					),
						)
				);
			}
			else if( $this->action == 'edit' ) {
				$personnepcg66_id = $id;
				$personnepcg66 = $this->Personnepcg66->find(
					'first',
					array(
						'conditions' => array(
							'Personnepcg66.id' => $personnepcg66_id
						),
						'contain' => array(
							'Categorieromev3',
							'Statutpdo',
							'Situationpdo'
						)
					)
				);
				$this->assert( !empty( $personnepcg66 ), 'invalidParameter' );
				$dossierpcg66_id = Set::classicExtract( $personnepcg66, 'Personnepcg66.dossierpcg66_id' );

				//$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'id' => $this->Dossierpcg66->dossierId( $dossierpcg66_id ) ) ) );

				$qd_dossierpcg66 = array(
					'conditions' => array(
						'Dossierpcg66.id' => $dossierpcg66_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$dossierpcg66 = $this->Dossierpcg66->find( 'first', $qd_dossierpcg66 );


				$foyer_id = Set::classicExtract( $dossierpcg66, 'Dossierpcg66.foyer_id' );
				$dossier_id = $this->Dossierpcg66->Foyer->dossierId( $foyer_id );

				//Liste des personnes appartenant au foyer dont le dossier fait question
				$personnes = $this->Personnepcg66->Personne->find(
						'list', array(
					'fields' => array( 'nom_complet' ),
					'conditions' => array(
						'Personne.foyer_id' => $foyer_id,
						'Personne.id IN (
								'.$this->Personnepcg66->Personne->Prestation->sq(
								array(
									'alias' => 'prestations',
									'fields' => array( 'prestations.personne_id' ),
									'conditions' => array(
										'prestations.natprest = \'RSA\'',
//										'prestations.rolepers' => array( 'DEM', 'CJT' )
									),
									'contain' => false
								)
						).' )',
						'Personne.id NOT IN (
								'.$this->Personnepcg66->sq(
								array(
									'alias' => 'personnespcgs66',
									'fields' => array( 'personnespcgs66.personne_id' ),
									'conditions' => array(
										'personnespcgs66.dossierpcg66_id' => $dossierpcg66_id,
										'personnespcgs66.id NOT' => $personnepcg66_id
									),
									'contain' => false
								)
						).' )',
					),
						)
				);
			}
			$this->set( compact( 'personnes', 'dossierpcg66' ) );

			// On récupère l'utilisateur connecté et qui exécute l'action
			$userConnected = $this->Session->read( 'Auth.User.id' );
			$this->set( compact( 'userConnected' ) );

			//Gestion des jetons
			$dossier_id = $this->Personnepcg66->Dossierpcg66->Foyer->dossierId( $foyer_id );
			$this->Jetons2->get( $dossier_id );

			if( !empty( $this->request->data ) ) {
				$this->Personnepcg66->begin();
				$success = true;

				$categorieromev3 = array( 'Categorieromev3' => $this->request->data['Categorieromev3'] );
				$personnepcg66 = $this->request->data['Personnepcg66'];
				$situationspdos = $this->request->data['Situationpdo'];
				$statutspdos = $this->request->data['Statutpdo'];

				// Catégorie ROME V3
				$clean = Hash::filter( Hash::flatten( $categorieromev3 ) );
				if( !empty( $clean ) ) {
					// Si on n'a que l'id, alors on supprime l'enregistrement
					// FIXME: il faudrait mettre à NULL avant pour ne pas avoir de problème de clé étrangère ?
					// @see /home/cbuffin/Bureau/WebRSA/2014/2.8.0/En cours/CG 66 - Codes ROME v3/20141212/personnespcgs66_romev3.txt
					if( count( $clean ) === 1 && isset( $clean['Categorieromev3.id'] ) ) {
						$success = $this->Personnepcg66->Categorieromev3->delete( $clean['Categorieromev3.id'] ) && $success;
						$personnepcg66['categorieromev3_id'] = null;
					}
					// Sinon, on a un enregistrement "normal"
					else {
						$empty = Hash::normalize( Hash::extract( (array)$this->Personnepcg66->Categorieromev3->belongsTo, '{s}.foreignKey' ) );

						$categorieromev3['Categorieromev3'] = $categorieromev3['Categorieromev3']
								+ $empty;

						$this->Personnepcg66->Categorieromev3->create( $categorieromev3 );
						$success = $this->Personnepcg66->Categorieromev3->save() && $success;
						$personnepcg66['categorieromev3_id'] = $this->Personnepcg66->Categorieromev3->id;
					}
				}
				else {
					$personnepcg66['categorieromev3_id'] = null;
				}

				$this->Personnepcg66->create( $personnepcg66 );
				$success = $this->Personnepcg66->save() && $success;
				
				$success = $this->_checkValidation() && $success;

				if( $success ) {
					foreach( array( 'situationspdos', 'statutspdos' ) as $tableliee ) {
						$modelelie = Inflector::classify( $tableliee );
						$modeleliaison = Inflector::classify( "personnespcgs66_{$tableliee}" );
						$foreignkey = Inflector::singularize( $tableliee ).'_id';
						$records = $this->Personnepcg66->{$modeleliaison}->find(
								'list', array(
							'fields' => array( "{$modeleliaison}.id", "{$modeleliaison}.{$foreignkey}" ),
							'conditions' => array(
								"{$modeleliaison}.personnepcg66_id" => $this->Personnepcg66->id
							)
								)
						);

						$oldrecordsids = array_values( $records );
						$nouveauxids = Hash::filter( (array)Set::extract( "/{$modelelie}", $$tableliee ) );

						if( empty( $nouveauxids ) ) {
							$this->Personnepcg66->{$modelelie}->invalidate( $modelelie, 'Merci de cocher au moins une case' );
							$success = false;
						}
						else {
							// En moins -> Supprimer
							$idsenmoins = array_diff( $oldrecordsids, $nouveauxids );
							if( !empty( $idsenmoins ) ) {
								$success = $this->Personnepcg66->{$modeleliaison}->deleteAll(
												array(
													"{$modeleliaison}.personnepcg66_id" => $this->Personnepcg66->id,
													"{$modeleliaison}.{$foreignkey}" => $idsenmoins
												)
										) && $success;
							}

							// En plus -> Ajouter
							$idsenplus = array_diff( $nouveauxids, $oldrecordsids );
							if( !empty( $idsenplus ) ) {
								foreach( $idsenplus as $idenplus ) {
									$record = array(
										$modeleliaison => array(
											"personnepcg66_id" => $this->Personnepcg66->id,
											"{$foreignkey}" => $idenplus
										)
									);

									$this->Personnepcg66->{$modeleliaison}->create( $record );
									$success = $this->Personnepcg66->{$modeleliaison}->save() && $success;
								}
							}
						}
					}

					if( $success ) {
						 $this->Personnepcg66->commit();
						$this->Jetons2->release( $dossier_id );
						$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
						$this->redirect( array( 'controller' => 'dossierspcgs66', 'action' => 'edit', $dossierpcg66_id ) );
					}
					else {
						$this->Personnepcg66->rollback();
						$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
					}
				}
			}
			else {
				if( $this->action == 'edit' || $premierAjout ) {
					$personnepcg66 = $this->Personnepcg66->Categorieromev3->prepareFormDataAddEdit( $personnepcg66 );
					$this->request->data = $personnepcg66;
				}
			}

            // Récupération d ela dernière personne enregistrée pour le dernier dossier PCG présent dans le foyer
            $dossierpcg66Pcd = $this->Personnepcg66->Dossierpcg66->find(
                'first',
                array(
                    'conditions' => array(
                        'Dossierpcg66.foyer_id' => $foyer_id,
                        'Dossierpcg66.id <>' => $dossierpcg66_id
                    ),
//                    'recursive' => -1,
                    'contain' => array(
                        'Personnepcg66' => array(
                            'Statutpdo',
                            'Situationpdo',
                            'Categorieromev3',
                            'order' => array( 'Personnepcg66.created DESC' )
                        )
                    ),
                    'order' => array( 'Dossierpcg66.created DESC'),
                    'limit' => 1
                )
            );

           if( !empty( $dossierpcg66Pcd ) && $this->action == 'add' ) {
                // Données permettant de pré-remplir le formulaire d'ajout d'e la DERNIERE personne'une personne
                // suivant la DERNIERE perosnne saisie préalablement
                if( !empty( $dossierpcg66Pcd['Personnepcg66'] ) ){
                    $this->request->data['Personnepcg66']['personne_id'] = $dossierpcg66Pcd['Personnepcg66'][0]['personne_id'];
					
					//Préremplissage des rome v3
					$romev3 = (array)Hash::get($dossierpcg66Pcd, 'Personnepcg66.0.Categorieromev3');
					$depend['familleromev3_id'] = Hash::get($romev3, 'familleromev3_id');
					$depend['domaineromev3_id'] = Hash::get($romev3, 'familleromev3_id').'_'.Hash::get($romev3, 'domaineromev3_id');
					$depend['metierromev3_id'] = Hash::get($romev3, 'domaineromev3_id').'_'.Hash::get($romev3, 'metierromev3_id');
					$depend['appellationromev3_id'] = Hash::get($romev3, 'metierromev3_id').'_'.Hash::get($romev3, 'appellationromev3_id');
                    $this->request->data['Categorieromev3'] = $depend;

                    foreach( array( 'Situationpdo', 'Statutpdo' ) as $value ) {
                        foreach( $dossierpcg66Pcd['Personnepcg66'] as $i => $info ) {
                            $this->request->data[$value][$value] = Hash::extract( $info, "$value.{n}.id" );
                        }
                    }
                }
            }

			$this->_setOptions();

			$this->set( compact( 'foyer_id', 'dossier_id', 'dossierpcg66_id', 'personnepcg66_id' ) );
			$this->set( 'urlmenu', '/dossierspcgs66/index/'.$foyer_id );

			// Options
			$options = Hash::get( $this->viewVars, 'options' );
			$Catalogueromev3 = ClassRegistry::init( 'Catalogueromev3' );
			$selects = $Catalogueromev3->dependantSelects();
			$options = Hash::merge(
				$options,
				array( 'Categorieromev3' => (array)Hash::get( $selects, 'Catalogueromev3' ) )
			);
			$this->set( compact( 'options' ) );

			$this->render( 'add_edit' );
		}

		/**
		 *
		 */
		public function view( $id = null ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $this->Personnepcg66->personneId( $id ) ) ) );

			$personnepcg66 = $this->Personnepcg66->find(
					'first', array(
				'conditions' => array(
					'Personnepcg66.id' => $id
				),
				'contain' => array(
					'Statutpdo',
					'Situationpdo',
					'Personne' => array(
						'fields' => array(
							'Personne.qual',
							'Personne.nom',
							'Personne.prenom',
						)
					),
				)
					)
			);
			$this->assert( !empty( $personnepcg66 ), 'invalidParameter' );

			$dossierpcg66_id = Set::classicExtract( $personnepcg66, 'Personnepcg66.dossierpcg66_id' );
			$qd_dossierpcg66 = array(
				'conditions' => array(
					'Dossierpcg66.id' => $dossierpcg66_id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$dossierpcg66 = $this->Dossierpcg66->find( 'first', $qd_dossierpcg66 );


			$foyer_id = Set::classicExtract( $dossierpcg66, 'Dossierpcg66.foyer_id' );

			// Retour à l'entretien en cas de retour
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'dossierspcgs66', 'action' => 'edit', $dossierpcg66_id ) );
			}

			$this->_setOptions();
			$this->set( compact( 'personnepcg66', 'foyer_id' ) );

			$this->set( 'urlmenu', '/dossierspcgs66/index/'.$foyer_id );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $this->Personnepcg66->personneId( $id ) ) );

			$this->Default->delete( $id );
		}

		protected function _checkValidation() {
			$success = true;
			
			if( empty( $this->request->data['Situationpdo']['Situationpdo'] ) ) {
				$success = false;
				$this->Personnepcg66->invalidate( 'Situationpdo.Situationpdo', 'Il est obligatoire de saisir au moins un motif de décision pour la personne.' );
			}
			
			if( empty( $this->request->data['Statutpdo']['Statutpdo'] ) ) {
				$success = false;
				$this->Personnepcg66->invalidate( 'Statutpdo.Statutpdo', 'Il est obligatoire de saisir au moins un statut pour la personne.' );
			}
			
			if (Hash::get($this->request->data, 'Personnepcg66.id')) {
				$query = array(
					'fields' => array(
						'Traitementpcg66.situationpdo_id',
						'Situationpdo.libelle',
					),
					'contain' => false,
					'joins' => array(
						$this->Personnepcg66->Traitementpcg66->join('Situationpdo', array( 'type' => 'INNER' ))
					),
					'conditions' => array(
						'Traitementpcg66.personnepcg66_id' => Hash::get($this->request->data, 'Personnepcg66.id')
					)
				);
				$results = (array)$this->Personnepcg66->Traitementpcg66->find('all', $query);
				$errors = array();
				
				foreach ($results as $value) {
					$situationpdo_id = Hash::get($value, 'Traitementpcg66.situationpdo_id');
					$libelle = Hash::get($value, 'Situationpdo.libelle');
					if (!in_array($situationpdo_id, (array)Hash::get($this->request->data, 'Situationpdo.Situationpdo'))) {
						$success = false;
						$errors[] = "Un traitement existe portant un motif non choisi ({$libelle}).";
					}
				}
				
				foreach (array_unique($errors) as $error) {
					$this->Personnepcg66->invalidate('Situationpdo.Situationpdo', $error);
				}
			}
			
			return $success;
		}
	}
?>