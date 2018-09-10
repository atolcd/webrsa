<?php
	/**
	 * Code source de la classe WebrsaDossierpcg66.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractLogic', 'Model' );

	/**
	 * La classe WebrsaDossierpcg66 ...
	 *
	 * @package app.Model
	 */
	class WebrsaDossierpcg66 extends WebrsaAbstractLogic
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaDossierpcg66';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Dossierpcg66' );


		/**
		 * Permet d'obtenir un data pour affichage d'un index de dossierpcg
		 *
		 * @param integer $foyer_id
		 * @return array
		 */
		public function getIndexData( $foyer_id ) {
			$query = $this->queryIndexByConditions( array( 'Dossierpcg66.foyer_id' => $foyer_id ) );
			$results = $this->Dossierpcg66->Foyer->find('all', $query);

			foreach ( $results as $key => $result ) {
				$results[$key]['Poledossierpcg66']['classname'] = Inflector::underscore(
					str_replace( ' ', '_', replace_accents( $results[$key]['Poledossierpcg66']['name'] ) )
				);

				$results[$key]['Dossierpcg66']['etatdossierpcg_full'] = __d(
					'dossierpcg66',
					'ENUM::ETATDOSSIERPCG::'.Hash::get($result, 'Dossierpcg66.etatdossierpcg')
				);

				if ( Hash::get($result, 'Decisiondossierpcg66.orgtransmis_list_name') ) {
					$results[$key]['Dossierpcg66']['etatdossierpcg_full'] .= ' à '
						. Hash::get($result, 'Decisiondossierpcg66.orgtransmis_list_name')
					;
				}

				if ( Hash::get($result, 'Decisiondossierpcg66.datetransmissionop') ) {
					$results[$key]['Dossierpcg66']['etatdossierpcg_full'] .= ' le '
						. date_format(date_create(Hash::get($result, 'Decisiondossierpcg66.datetransmissionop')), 'd/m/Y')
					;
				}

				if ( Hash::get($result, 'Personnepcg66.situationpdo_list_libelle') ) {
					$results[$key]['Personnepcg66']['situationpdo_list_libelle_ulli'] =
						'<ul><li>'
						. implode( '</li><li>', explode( '__', Hash::get($result, 'Personnepcg66.situationpdo_list_libelle') ) )
						. '</li></ul>'
					;
				}

				if ( Hash::get($result, 'Bilanparcours66.personne_nom_complet') ) {
					$results[$key]['Dossierpcg66']['bilan_de'] = 'Bilan de parcours de&nbsp;: '
						. Hash::get($result, 'Bilanparcours66.personne_nom_complet')
					;
				}
			}

			return $results;
		}

		/**
		 * Permet d'obtenir un query d'index des dossiers PCGs
		 *
		 * @param mixed $conditions
		 * @return array
		 */
		public function queryIndexByConditions( $conditions = array() ) {
			$sqLastDecision = $this->Dossierpcg66->Decisiondossierpcg66->sq(
				array(
					'alias' => 'decisionsdossierspcgs66',
					'fields' => 'decisionsdossierspcgs66.id',
					'conditions' => array(
						'decisionsdossierspcgs66.dossierpcg66_id = Dossierpcg66.id'
					),
					'contain' => false,
					'order' => array(
						'decisionsdossierspcgs66.created' => 'DESC'
					),
					'limit' => 1
				)
			);

			$joinDecision = $this->Dossierpcg66->join('Decisiondossierpcg66');
			$joinDecision['conditions'] = "Decisiondossierpcg66.id IN ( {$sqLastDecision} )";

			$sqTransmisOp = $this->Dossierpcg66->Decisiondossierpcg66->Decdospcg66Orgdospcg66->sq(
				array(
					'fields' => 'Orgtransmisdossierpcg66.name',
					'joins' => array(
						$this->Dossierpcg66->Decisiondossierpcg66->Decdospcg66Orgdospcg66->join('Orgtransmisdossierpcg66', array('type' => 'INNER'))
					),
					'conditions' => array(
						'Decdospcg66Orgdospcg66.decisiondossierpcg66_id = Decisiondossierpcg66.id',
					),
					'order' => 'Orgtransmisdossierpcg66.name'
				)
			);

			$sqMotifPersonne = $this->Dossierpcg66->Personnepcg66->Personnepcg66Situationpdo->sq(
				array(
					'fields' => "Situationpdo.libelle",
					'joins' => array(
						$this->Dossierpcg66->Personnepcg66->Personnepcg66Situationpdo->join('Situationpdo', array('type' => 'INNER'))
					),
					'conditions' => array(
						'Personnepcg66Situationpdo.personnepcg66_id = Personnepcg66.id'
					)
				)
			);

			$sqBilanPersonne = $this->Dossierpcg66->Bilanparcours66->Personne->sq(
				array(
					'alias' => 'personnes',
					'fields' => array(
						"personnes.qual || ' ' || personnes.nom || ' ' || personnes.prenom AS \"personnes__nom_complet\""
					),
					'joins' => array(
						array(
							'alias' => 'Bilanparcours66',
							'table' => 'bilansparcours66',
							'type' => 'INNER',
							'conditions' => array(
								'Bilanparcours66.personne_id = personnes.id',
								'Bilanparcours66.id = Dossierpcg66.bilanparcours66_id'
							)
						)
					),
					'limit' => 1
				)
			);

			$sqTraitement = words_replace(
				$this->Dossierpcg66->Personnepcg66->Traitementpcg66->sq(
					array(
						'fields' => 'id',
						'conditions' => array(
							'Traitementpcg66.personnepcg66_id = Personnepcg66.id',
							'Traitementpcg66.typetraitement' => 'documentarrive'
						),
						'contain' => false,
						'order' => array(
							'Traitementpcg66.datereception' => 'DESC',
							'Traitementpcg66.id' => 'DESC'
						),
						'limit' => 1
					)
				),
				array( 'Traitementpcg66' => 'traitementspcgs66' )
			);

			$query = array(
				'fields' => array(
					'Dossierpcg66.id',
					'Dossierpcg66.motifannulation',
					'Typepdo.libelle',
					'Dossierpcg66.datereceptionpdo',
					'Dossierpcg66.etatdossierpcg',
					'Decisiondossierpcg66.datetransmissionop',
					'Decisionpdo.libelle',
					'Poledossierpcg66.name',
					'Traitementpcg66.datereception',
					'User.nom',
					'User.prenom',
					'( COALESCE( "Poledossierpcg66"."name", \'\' ) || \' / \' || COALESCE( "User"."nom", \'\' ) || \' \' || COALESCE( "User"."prenom", \'\' ) ) AS "Pole__user"',
					"( {$sqBilanPersonne} ) AS \"Bilanparcours66__personne_nom_complet\"",
					"( ARRAY_TO_STRING(ARRAY(({$sqTransmisOp})), ', ') ) AS \"Decisiondossierpcg66__orgtransmis_list_name\"",
					"( ARRAY_TO_STRING(ARRAY(({$sqMotifPersonne})), '__') ) AS \"Personnepcg66__situationpdo_list_libelle\"",
				),
				'joins' => array(
					$this->Dossierpcg66->Foyer->join('Dossierpcg66', array('type' => 'INNER')),
					$this->Dossierpcg66->Foyer->join('Personne', array('type' => 'INNER')),
					$this->Dossierpcg66->Foyer->Personne->join('Prestation',
						array(
							'type' => 'INNER',
							'conditions' => array(
								'Prestation.rolepers' => 'DEM'
							)
						)
					),
					$this->Dossierpcg66->join('Personnepcg66',
						array(
							'conditions' => array(
								'Personnepcg66.personne_id = Personne.id'
							)
						)
					),
					$this->Dossierpcg66->Personnepcg66->join('Traitementpcg66',
						array(
							'conditions' => array(
								"Traitementpcg66.id IN ({$sqTraitement})"
							)
						)
					),
					$this->Dossierpcg66->join('Typepdo'),
					$this->Dossierpcg66->join('User'),
					$this->Dossierpcg66->join('Poledossierpcg66'),
					$joinDecision,
					$this->Dossierpcg66->Decisiondossierpcg66->join('Decisionpdo'),
				),
				'conditions' => $conditions,
				'contain' => false,
				'order' => array(
					'Dossierpcg66.datereceptionpdo' => 'DESC NULLS LAST',
					'Decisiondossierpcg66.datetransmissionop' => 'DESC NULLS FIRST',
					'Decisiondossierpcg66.datevalidation' => 'DESC NULLS FIRST',
					'Dossierpcg66.id' => 'DESC',
				)
			);

			return $query;
		}

		/**
		 * Permet d'obtenir les informations sur le demandeur du RSA pour affichage dans le dossier pcg
		 *
		 * @param integer $foyer_id
		 * @return array
		 */
		public function findPersonneDem( $foyer_id ) {
			return $this->Dossierpcg66->Foyer->Personne->find(
				'first',
				array(
					'fields' => array(
						'Personne.id',
						'Prestation.rolepers',
						$this->Dossierpcg66->Foyer->Personne->sqVirtualField('nom_complet')
					),
					'conditions' => array(
						'Personne.foyer_id' => $foyer_id,
						'Prestation.rolepers' => array( 'DEM' )
					),
					'joins' => array(
						$this->Dossierpcg66->Foyer->Personne->join( 'Prestation' )
					),
					'contain' => false
				)
			);
		}

		/**
		 * Permet d'obtenir les informations nécéssaire à l'edition d'un dossier pcg
		 *
		 * @param integer $id
		 * @return array
		 */
		public function findDossierpcg( $id ) {
			return $this->Dossierpcg66->find(
				'first',
				array(
					'conditions' => array(
						'Dossierpcg66.id' => $id
					),
					'contain' => array(
						'Personnepcg66' => array(
							'Personne',
							'Statutpdo',
							'Situationpdo'
						),
						'Decisiondefautinsertionep66' => array(
							'Passagecommissionep'
						),
						'Decisiondossierpcg66' => array(
                            'order' => array( 'Decisiondossierpcg66.created DESC' ),
                            'Notificationdecisiondossierpcg66',
							'Useravistechnique' => array(
								'fields' => 'nom_complet'
							),
							'Userproposition' => array(
								'fields' => 'nom_complet'
							),
						),
						'Fichiermodule',
						'Typepdo',
						'Originepdo',
						'Serviceinstructeur',
						'User',
						'Poledossierpcg66',
					)
				)
			);
		}

		/**
		 * Permet d'obtenir la liste des personnes liés à un dossier pcg
		 *
		 * @param integer $dossierpcg66_id
		 * @return array
		 */
		public function findPersonnepcg( $dossierpcg66_id ) {
			return $this->Dossierpcg66->Personnepcg66->find(
				'all',
				array(
					'conditions' => array(
						'Personnepcg66.dossierpcg66_id' => $dossierpcg66_id
					),
					'contain' => array(
						'Statutpdo',
						'Situationpdo',
						'Personne',
						'Traitementpcg66'
					)
				)
			);
		}

		/**
		 * Permet d'obtenir la liste des propositions d'un dossier pcg
		 *
		 * @param integer $dossierpcg66_id
		 * @return array
		 * @deprecated since version 3.1
		 */
		public function findDecisiondossierpcg( $dossierpcg66_id ) {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			//Gestion des décisions pour le dossier au niveau foyer
			$joins = array(
				array(
					'table'      => 'pdfs',
					'alias'      => 'Pdf',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'Pdf.modele' => 'Decisiondossierpcg66',
						'Pdf.fk_value = Decisiondossierpcg66.id'
					)
				),
				array(
					'table'      => 'decisionspdos',
					'alias'      => 'Decisionpdo',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'Decisionpdo.id = Decisiondossierpcg66.decisionpdo_id'
					)
				),
			);

			return $this->Dossierpcg66->Decisiondossierpcg66->find(
				'all',
				array(
					'fields' => array(
						'Decisiondossierpcg66.id',
						'Decisiondossierpcg66.dossierpcg66_id',
						'Decisiondossierpcg66.decisionpdo_id',
						'Decisiondossierpcg66.datepropositiontechnicien',
						'Decisiondossierpcg66.commentairetechnicien',
						'Decisiondossierpcg66.commentaire',
						'Decisiondossierpcg66.avistechnique',
						'Decisiondossierpcg66.commentaireavistechnique',
						'Decisiondossierpcg66.dateavistechnique',
						'Decisiondossierpcg66.etatdossierpcg',
						'Decisiondossierpcg66.validationproposition',
						'Decisiondossierpcg66.motifannulation',
						'Decisiondossierpcg66.commentairevalidation',
						'Decisiondossierpcg66.datevalidation',
						'Decisionpdo.libelle',
						'Pdf.fk_value',
						$this->Dossierpcg66->Decisiondossierpcg66->Fichiermodule->sqNbFichiersLies(
							$this->Dossierpcg66->Decisiondossierpcg66,
							'nb_fichiers_lies'
						)
					),
					'conditions' => array(
						'dossierpcg66_id' => $dossierpcg66_id
					),
					'joins' => $joins,
					'order' => array(
						'Decisiondossierpcg66.modified DESC'
					),
					'recursive' => -1
				)
			);
		}

		/**
		 * Permet d'obtenir la liste des fichiers liés à un dossier pcg
		 *
		 * @param integer $dossierpcg66_id
		 * @return array
		 */
		public function findFichiers( $dossierpcg66_id ) {
			return $this->Dossierpcg66->Fichiermodule->find(
				'all',
				array(
					'fields' => array(
						'Fichiermodule.id',
						'Fichiermodule.name',
						'Fichiermodule.fk_value',
						'Fichiermodule.modele',
						'Fichiermodule.cmspath',
						'Fichiermodule.mime',
						'Fichiermodule.created',
						'Fichiermodule.modified',
					),
					'conditions' => array(
						'Fichiermodule.modele' => 'Dossierpcg66',
						'Fichiermodule.fk_value' => $dossierpcg66_id,
					),
					'contain' => false
				)
			);
		}

		/**
		 * 	Liste des courriers envoyés aux personnes PCG liés au dossier sur lequel on travaille
		 * 	@params	integer
		 * 	@return array
		 *
		 */
		public function listeCourriersEnvoyes($personneId = 'Personne.id', $data = array()) {
			$traitementsNonClos = array();

			$personnespcgs66 = $this->Dossierpcg66->Personnepcg66->find(
					'all', array(
				'fields' => array(
					'Personnepcg66.id',
					'Personnepcg66.dossierpcg66_id',
				),
				'conditions' => array(
					'Personnepcg66.personne_id' => $personneId,
					'Personnepcg66.dossierpcg66_id' => $data['Dossierpcg66']['id']
				),
				'contain' => false
					)
			);
			$infosDossierpcg66 = (array) Set::extract($personnespcgs66, '{n}.Personnepcg66.dossierpcg66_id');
			$listPersonnespcgs66 = (array) Set::extract($personnespcgs66, '{n}.Personnepcg66.id');

			$traitementspcgs66 = array();
			if (!empty($infosDossierpcg66)) {
				$conditions = array(
					'Traitementpcg66.personnepcg66_id' => $listPersonnespcgs66,
					'Traitementpcg66.dateenvoicourrier IS NOT NULL'
				);

				$traitementspcgs66 = $this->Dossierpcg66->Personnepcg66->Traitementpcg66->find(
						'all', array(
					'fields' => array(
						'Traitementpcg66.id',
						'Traitementpcg66.datedepart',
						'Traitementpcg66.dateenvoicourrier',
						'Personnepcg66.dossierpcg66_id',
						'Personnepcg66.id',
						'Personnepcg66.personne_id',
						$this->Dossierpcg66->Personnepcg66->Personne->sqVirtualField('nom_complet'),
						'Descriptionpdo.name',
						'Situationpdo.libelle',
						'Dossierpcg66.datereceptionpdo',
						'Typepdo.libelle',
						$this->Dossierpcg66->User->sqVirtualField('nom_complet')
					),
					'conditions' => $conditions,
					'joins' => array(
						$this->Dossierpcg66->Personnepcg66->Traitementpcg66->join('Personnepcg66', array('type' => 'INNER')),
						$this->Dossierpcg66->Personnepcg66->Traitementpcg66->Personnepcg66->join('Dossierpcg66', array('type' => 'INNER')),
						$this->Dossierpcg66->Personnepcg66->Traitementpcg66->Personnepcg66->Dossierpcg66->join('Typepdo', array('type' => 'INNER')),
						$this->Dossierpcg66->Personnepcg66->Traitementpcg66->Personnepcg66->Dossierpcg66->join('User', array('type' => 'INNER')),
						$this->Dossierpcg66->Personnepcg66->join('Personne', array('type' => 'INNER')),
						$this->Dossierpcg66->Personnepcg66->Traitementpcg66->join('Descriptionpdo', array('type' => 'INNER')),
						$this->Dossierpcg66->Personnepcg66->Traitementpcg66->join('Situationpdo', array('type' => 'LEFT OUTER'))
					),
					'contain' => false
						)
				);
			}

			return $traitementspcgs66;
		}

		/*
		 * Mise à jour de l'état du passage en comission EP du dossier EP pour
		 * un défaut d'insertion (issu d'une EP Audition)
		 *
		 * @param integer $decisiondefautinsertionep66_id
		 * @return array
		 */
		public function updateEtatPassagecommissionep($decisiondefautinsertionep66_id) {
			if (empty($decisiondefautinsertionep66_id)) {
				return false;
			}

			$decisiondefautinsertionep66 = $this->Dossierpcg66->Decisiondefautinsertionep66->find(
				'first',
				array(
					'fields' => array_merge(
						$this->Dossierpcg66->Decisiondefautinsertionep66->fields(),
						$this->Dossierpcg66->Decisiondefautinsertionep66->Passagecommissionep->fields()
					),
					'conditions' => array(
						'Decisiondefautinsertionep66.id' => $decisiondefautinsertionep66_id,
						'Passagecommissionep.id IN ('.$this->Dossierpcg66->Decisiondefautinsertionep66->Passagecommissionep->sqDernier().' )'
					),
					'joins' => array(
						$this->Dossierpcg66->Decisiondefautinsertionep66->join('Passagecommissionep', array('type' => 'INNER')),
						$this->Dossierpcg66->Decisiondefautinsertionep66->Passagecommissionep->join('Dossierep', array('type' => 'INNER'))
					),
					'contain' => false
				)
			);

			$dec = Hash::get($decisiondefautinsertionep66, 'Passagecommissionep');
			if ( Hash::get($dec, 'etatdossierep') !== 'traite' ) {
				return $this->Dossierpcg66->Decisiondefautinsertionep66->Passagecommissionep->updateAllUnBound(
					array(
						'Passagecommissionep.etatdossierep' => '\'traite\''
					),
					array(
						'"Passagecommissionep"."dossierep_id"' => Hash::get($dec, 'dossierep_id'),
						'"Passagecommissionep"."id"' => Hash::get($dec, 'id')
					)
				);
			}

			return true;
		}

		/**
		 * Fonction permettant la génération automatique d'un dossier PCG dès lors
		 * que le dossier PCG initial a été transmis à un organisme pour lequel il
		 * est possible de générer automatiquement un dossier PCG et que le pôle
		 * lié est différent du pôle actuel du dossier PCG et qu'il n'existe pas
		 * encore de dossier PCG généré automatiquement à partir du dossier PCG
		 * initial.
		 *
		 * @param integer $dossierpcg66_id L'id du dossier PCG initial
		 * @return boolean
		 */
		public function generateDossierPCG66Transmis( $dossierpcg66_id ) {
			if( true === empty( $dossierpcg66_id ) ) {
				return false;
			}

			$success = true;

			$query = array(
				'fields' => array_merge(
					$this->Dossierpcg66->fields(),
					$this->Dossierpcg66->Decisiondossierpcg66->fields(),
					$this->Dossierpcg66->Decisiondossierpcg66->Orgtransmisdossierpcg66->fields(),
					$this->Dossierpcg66->Poledossierpcg66->fields(),
					alias(
						$this->Dossierpcg66->Decisiondossierpcg66->Orgtransmisdossierpcg66->Poledossierpcg66->fields(),
						array( 'Poledossierpcg66' => 'Nouveaupoledossierpcg66' )
					)
				),
				'joins' => array(
					$this->Dossierpcg66->join(
						'Decisiondossierpcg66',
						array(
							'type' => 'INNER',
							'conditions' => array(
								'Decisiondossierpcg66.id IN ('.
									$this->Dossierpcg66->Decisiondossierpcg66->sq(
										array(
											'alias' => 'decisionsdossierspcgs66',
											'fields' => array( 'decisionsdossierspcgs66.id' ),
											'conditions' => array(
												'decisionsdossierspcgs66.dossierpcg66_id = Decisiondossierpcg66.dossierpcg66_id'
											),
											'contain' => false,
											'order' => array(
												'decisionsdossierspcgs66.created DESC',
												'decisionsdossierspcgs66.id DESC'
											),
											'limit' => 1
										)
									)
								.')'
							)
						)
					),
					$this->Dossierpcg66->join('Poledossierpcg66', array('type' => 'INNER')),
					$this->Dossierpcg66->Decisiondossierpcg66->join( 'Orgtransmisdossierpcg66', array( 'type' => 'LEFT OUTER' ) ),
					alias(
						$this->Dossierpcg66->Decisiondossierpcg66->Orgtransmisdossierpcg66->join( 'Poledossierpcg66', array( 'type' => 'LEFT OUTER' ) ),
						array( 'Poledossierpcg66' => 'Nouveaupoledossierpcg66' )
					)
				),
				'contain' => false,
				'conditions' => array(
					'Dossierpcg66.id' => $dossierpcg66_id
				)
			);
			$dossierpcg66EnCours = $this->Dossierpcg66->find( 'first', $query );

			// Par défaut, on ne génère pas de dossier PCG automatiquement
			$generationAuto = false;

			// On ne peut générer qu'un seul dossier PCG auto à partir d'un certain dossier PCG
			$query = array(
				'fields' => array( 'Dossierpcg66.id' ),
				'conditions' => array( 'Dossierpcg66.dossierpcg66pcd_id' => $dossierpcg66_id ),
				'contain' => false
			);
			$exists = $this->Dossierpcg66->find( 'first', $query );

			// Pôle chargé de traiter le dossier PCG en cours
			$poledossierpcg66EncoursId = $dossierpcg66EnCours['Dossierpcg66']['poledossierpcg66_id'];

			// Pôle lié à l'organisme auquel on a transmis l'information
			$poledossierpcg66TransmisId = $dossierpcg66EnCours['Orgtransmisdossierpcg66']['poledossierpcg66_id'];

			// On génère un dossier SSI il n'existe pas encore de dossier auto généré à partir de ce dossier PCG,...
			$generationAuto = true === empty( $exists )
				//  et si "Information transmise à" de la décision n'est pas vide, ...
				&& false === empty( $poledossierpcg66TransmisId )
				//  et s'il est prévu la création automatique lors du transfert du dossier à l'organisme
				&& '1' == Hash::get( $dossierpcg66EnCours, 'Orgtransmisdossierpcg66.generation_auto' )
				//  et si le pôle auquel on transmet est différent du pôle auquel le dossier PCG appartient
				&& $poledossierpcg66EncoursId != $poledossierpcg66TransmisId;

			if( true === $generationAuto ) {
				$nouveauDossierpcg66 = array(
					'Dossierpcg66' => array(
						'foyer_id' => $dossierpcg66EnCours['Dossierpcg66']['foyer_id'],
						'originepdo_id' => $dossierpcg66EnCours['Poledossierpcg66']['originepdo_id'],
						'typepdo_id' => $dossierpcg66EnCours['Nouveaupoledossierpcg66']['typepdo_id'],
						'orgpayeur' => $dossierpcg66EnCours['Dossierpcg66']['orgpayeur'],
						'datereceptionpdo' => $dossierpcg66EnCours['Decisiondossierpcg66']['datevalidation'],
						'commentairepiecejointe' => $dossierpcg66EnCours['Decisiondossierpcg66']['infotransmise'],
						'haspiecejointe' => 0,
						'poledossierpcg66_id' => $poledossierpcg66TransmisId,
						'etatdossierpcg' => 'attaffect',
						'dossierpcg66pcd_id' => $dossierpcg66EnCours['Dossierpcg66']['id']
					)
				);

				$this->Dossierpcg66->create( $nouveauDossierpcg66 );
				$success = $this->Dossierpcg66->save( null, array( 'atomic' => false ) ) && $success;
			}

			return $success;
		}

		/**
		 * Retourne les positions et les conditions CakePHP/SQL dans l'ordre dans
		 * lequel elles doivent être traitées pour récupérer la position actuelle.
		 *
		 * @return array
		 */
		protected function _getConditionsPositionsPcgs() {
			$sqArevoir = 'EXISTS(
				SELECT traitementspcgs66.id
				FROM traitementspcgs66
				INNER JOIN personnespcgs66 ON (personnespcgs66.id = traitementspcgs66.personnepcg66_id)
				WHERE personnespcgs66.dossierpcg66_id = "Dossierpcg66"."id"
				AND traitementspcgs66.typetraitement IS NOT NULL
				AND traitementspcgs66.typetraitement = \'dossierarevoir\'
				LIMIT 1
			)';

			$sqAttinstrdocarrive = 'EXISTS(
				SELECT traitementspcgs66.id
				FROM traitementspcgs66
				INNER JOIN personnespcgs66 ON (personnespcgs66.id = traitementspcgs66.personnepcg66_id)
				WHERE personnespcgs66.dossierpcg66_id = "Dossierpcg66"."id"
				AND traitementspcgs66.typetraitement IS NOT NULL
				AND traitementspcgs66.typetraitement = \'documentarrive\'
				AND NOT EXISTS(
					SELECT traitementspcgs66_sq.id
					FROM traitementspcgs66 AS traitementspcgs66_sq
					WHERE personnespcgs66.id = traitementspcgs66_sq.personnepcg66_id
					AND traitementspcgs66.descriptionpdo_id IS NOT NULL
					AND traitementspcgs66.descriptionpdo_id IN (' . implode(', ', (array) Configure::read('Corbeillepcg.descriptionpdoId') ) . ')
					AND traitementspcgs66_sq.created > traitementspcgs66.created
					LIMIT 1
				)
				AND traitementspcgs66.id IN (
					SELECT traitementspcgs66_sq2.id
					FROM traitementspcgs66 AS traitementspcgs66_sq2
					WHERE personnespcgs66.id = traitementspcgs66_sq2.personnepcg66_id
						AND personnespcgs66.dossierpcg66_id = "Dossierpcg66"."id"
						AND traitementspcgs66_sq2.typetraitement IS NOT NULL
					ORDER BY traitementspcgs66_sq2.id DESC
					LIMIT 1
				)
				ORDER BY traitementspcgs66.id DESC
				LIMIT 1
			)';
;
			$sqAttinstrattpiece = 'EXISTS(
				SELECT traitementspcgs66.id
				FROM traitementspcgs66
				INNER JOIN personnespcgs66 ON (personnespcgs66.id = traitementspcgs66.personnepcg66_id)
				WHERE personnespcgs66.dossierpcg66_id = "Dossierpcg66"."id"
				AND traitementspcgs66.descriptionpdo_id IN (' . implode(', ', (array) Configure::read('Corbeillepcg.descriptionpdoId') ) . ')
				AND NOT EXISTS(
					SELECT traitementspcgs66_sq.id
					FROM traitementspcgs66 AS traitementspcgs66_sq
					WHERE personnespcgs66.id = traitementspcgs66_sq.personnepcg66_id
					AND traitementspcgs66.typetraitement IS NOT NULL
					AND traitementspcgs66.typetraitement = \'documentarrive\'
					AND traitementspcgs66_sq.created > traitementspcgs66.created
					LIMIT 1
				)
				ORDER BY traitementspcgs66.id DESC
				LIMIT 1
			)';

			$return = array(
				'annule' => array(
					array(
						$this->Dossierpcg66->alias.'.etatdossierpcg' => 'annule',
					)
				),
				'attaffect' => array(
					array(
						$this->Dossierpcg66->alias.'.user_id IS NULL',
					)
				),
				'instrencours' => array(
					array(
						$this->Dossierpcg66->alias.'.user_id IS NOT NULL',
						// Ne doit pas dépendre du type de proposition.
						//$this->Dossierpcg66->Decisiondossierpcg66->alias . '.decisionpdo_id IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours' => '1',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.etatdossierpcg IS NULL',
					),
				),
				'attavistech' => array(
					array(
						$this->Dossierpcg66->alias.'.user_id IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.decisionpdo_id IS NOT NULL',
						'OR' => array(
							$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours IS NULL',
							$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours' => '0',
						),
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.avistechnique IS NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.etatdossierpcg IS NULL',
					),
				),
				'attval' => array(
					array(
						$this->Dossierpcg66->alias.'.user_id IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.decisionpdo_id IS NOT NULL',
						'OR' => array(
							$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours IS NULL',
							$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours' => '0',
						),
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.avistechnique IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.validationproposition IS NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.etatdossierpcg IS NULL',
					),
				),
				'transmisop' => array(
					$this->Dossierpcg66->Decisiondossierpcg66->alias . '.etatop IS NOT NULL',
					$this->Dossierpcg66->Decisiondossierpcg66->alias . '.etatop' => 'transmis',
				),
				'atttransmisop' => array(
					$this->Dossierpcg66->alias.'.etatdossierpcg' => array( 'decisionvalid', 'atttransmisop' ),
					$this->Dossierpcg66->alias.'.dateimpression IS NOT NULL',
					$this->Dossierpcg66->Decisiondossierpcg66->alias . '.validationproposition' => 'O',
					$this->Dossierpcg66->Decisiondossierpcg66->alias . '.datevalidation <= '.$this->Dossierpcg66->alias.'.dateimpression',
					$this->Dossierpcg66->Decisiondossierpcg66->alias . '.etatdossierpcg IS NULL',
					'OR' => array(
						array(
							$this->Dossierpcg66->Decisiondossierpcg66->alias . '.etatop IS NOT NULL',
							$this->Dossierpcg66->Decisiondossierpcg66->alias . '.etatop' => 'atransmettre',
						),
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.etatop IS NULL'
					)
				),
				'decisionnonvalid' => array(
					array(
						$this->Dossierpcg66->alias.'.user_id IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.decisionpdo_id IS NOT NULL',
						array(
							'OR' => array(
								$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours IS NULL',
								$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours' => '0',
							),
						),
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.avistechnique IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.validationproposition IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.validationproposition' => 'N',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.etatdossierpcg IS NULL',
						array(
							'OR' => array(
								$this->Dossierpcg66->Decisiondossierpcg66->alias . '.retouravistechnique' => '0',
								array(
									$this->Dossierpcg66->Decisiondossierpcg66->alias . '.retouravistechnique' => '1',
									$this->Dossierpcg66->Decisiondossierpcg66->alias . '.vuavistechnique' => '1',
								)
							)
						)
					),
				),
				'decisionnonvalidretouravis' => array(
					array(
						$this->Dossierpcg66->alias.'.user_id IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.decisionpdo_id IS NOT NULL',
						array(
							'OR' => array(
								$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours IS NULL',
								$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours' => '0',
							),
						),
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.avistechnique IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.validationproposition IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.validationproposition' => 'N',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.retouravistechnique' => '1',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.vuavistechnique' => '0',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.etatdossierpcg IS NULL',
					),
				),
				'decisionvalidretouravis' => array(
					array(
						$this->Dossierpcg66->alias.'.user_id IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.decisionpdo_id IS NOT NULL',
						array(
							'OR' => array(
								$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours IS NULL',
								$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours' => '0',
							),
						),
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.avistechnique IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.validationproposition IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.validationproposition' => 'O',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.retouravistechnique' => '1',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.vuavistechnique' => '0',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.etatdossierpcg IS NULL',
					),
				),
				'decisionvalid' => array(
					array(
						$this->Dossierpcg66->alias.'.user_id IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.decisionpdo_id IS NOT NULL',
						array(
							'OR' => array(
								$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours IS NULL',
								$this->Dossierpcg66->Decisiondossierpcg66->alias . '.instrencours' => '0',
							),
						),
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.avistechnique IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.validationproposition IS NOT NULL',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.validationproposition' => 'O',
						$this->Dossierpcg66->Decisiondossierpcg66->alias . '.etatdossierpcg IS NULL',
						array(
							'OR' => array(
								array(
									$this->Dossierpcg66->Decisiondossierpcg66->alias . '.retouravistechnique' => '1',
									$this->Dossierpcg66->Decisiondossierpcg66->alias . '.vuavistechnique' => '1',
								),
								array(
									$this->Dossierpcg66->Decisiondossierpcg66->alias . '.retouravistechnique' => '0',
									$this->Dossierpcg66->Decisiondossierpcg66->alias . '.vuavistechnique' => '0',
								),
							),
						),
					),
				),
				'arevoir' => array(
					$sqArevoir
				),
				'attinstrdocarrive' => array(
					$sqAttinstrdocarrive
				),
				'attinstrattpiece' => array(
					$sqAttinstrattpiece
				),
				'attinstr' => array(
					array(
						$this->Dossierpcg66->alias.'.user_id IS NOT NULL',
					)
				),
			);

			return $return;
		}

		/**
		 * Retourne les conditions permettant de cibler les PCG qui devraient être
		 * dans une certaine position.
		 *
		 * @param string $etatdossierpcg66
		 * @return array
		 */
		public function getConditionsPositionpcg( $etatdossierpcg66 ) {
			$conditions = array();

			foreach( $this->_getConditionsPositionsPcgs() as $keyPosition => $conditionsPosition ) {
				if ( $keyPosition === $etatdossierpcg66 ) {
					$conditions[] = array( $conditionsPosition );
					break;
				}
			}

			return $conditions;
		}

		/**
		 * Retourne une CASE (PostgreSQL) pemettant de connaître la position que
		 * devrait avoir un PCG (au CG 66).
		 *
		 * @return string
		 */
		public function getCasePositionPcg() {
			$return = '';
			$Dbo = $this->getDataSource();

			foreach( array_keys( $this->_getConditionsPositionsPcgs() ) as $etatdossierpcg66 ) {
				$conditions = $this->getConditionsPositionpcg( $etatdossierpcg66 );
				$conditions = $Dbo->conditions( $conditions, true, false, $this );
				$return .= "WHEN {$conditions} \nTHEN '{$etatdossierpcg66}' \n";
			}

			$sq = $Dbo->startQuote;
			$eq = $Dbo->endQuote;
			// Position par defaut : En attente d'envoi de l'e-mail pour l'employeur
			$return = "( CASE {$return} ELSE {$sq}{$this->Dossierpcg66->alias}{$eq}.etatdossierpcg END )";

			return $return;
		}

		/**
		 * Mise à jour des positions des PCG suivant des conditions données.
		 *
		 * @param array $conditions
		 * @return boolean
		 */
		public function updatePositionsPcgsByConditions( array $conditions ) {
			// On vérifi qu'au moins un cas existe selon les conditions
			$query = array(
				'fields' => array( "{$this->Dossierpcg66->alias}.{$this->Dossierpcg66->primaryKey}", "{$this->Dossierpcg66->alias}.etatdossierpcg" ),
				'conditions' => $conditions,
			);
			$datas = $this->Dossierpcg66->find( 'first', $query );

			if ( empty( $datas ) ){
				return true;
			}

			$Dbo = $this->Dossierpcg66->getDataSource();
			$DboDecisiondossierpcg66 = $this->Dossierpcg66->Decisiondossierpcg66->getDataSource();
			$DboPersonnepcg66 = $this->Dossierpcg66->Personnepcg66->getDataSource();
			$DboTraitementpcg66 = $this->Dossierpcg66->Personnepcg66->Traitementpcg66->getDataSource();

			$tableName = $Dbo->fullTableName( $this->Dossierpcg66, true, true );
			$tableNameDecisiondossierpcg66 = $DboDecisiondossierpcg66->fullTableName( $this->Dossierpcg66->Decisiondossierpcg66, true, true );
			$tableNamePersonnepcg66 = $DboPersonnepcg66->fullTableName( $this->Dossierpcg66->Personnepcg66, true, true );
			$tableNameTraitementpcg66 = $DboTraitementpcg66->fullTableName( $this->Dossierpcg66->Personnepcg66->Traitementpcg66, true, true );

			$case = $this->getCasePositionPcg();

			$sq = $Dbo->startQuote;
			$eq = $Dbo->endQuote;

			$conditionsSql = $Dbo->conditions( $conditions, true, true, $this );

			$jointureConditionDecision = "
				SELECT decisionsdossierspcgs66.id
				FROM decisionsdossierspcgs66
				WHERE decisionsdossierspcgs66.dossierpcg66_id = {$sq}{$this->Dossierpcg66->alias}_sq{$eq}.{$sq}id{$eq}
				ORDER BY decisionsdossierspcgs66.id DESC
				LIMIT 1
			";

			$query = array(
				'update' => "UPDATE {$tableName} AS {$sq}{$this->Dossierpcg66->alias}{$eq}",
				'set' => "SET {$sq}etatdossierpcg{$eq} = {$case}",
				'from' => "FROM {$tableName} AS {$sq}{$this->Dossierpcg66->alias}_sq{$eq}",
				'join1' => "LEFT JOIN {$tableNameDecisiondossierpcg66} AS {$sq}{$this->Dossierpcg66->Decisiondossierpcg66->alias}{$eq}",
				'condition_join1' => "ON ({$sq}{$this->Dossierpcg66->Decisiondossierpcg66->alias}{$eq}.{$sq}id{$eq} IN ({$jointureConditionDecision}))",
				'condition' => "{$conditionsSql}",
				'condition2' => "AND ({$sq}{$this->Dossierpcg66->alias}_sq{$eq}.{$sq}etatdossierpcg{$eq} IS NULL OR {$sq}{$this->Dossierpcg66->alias}_sq{$eq}.{$sq}etatdossierpcg{$eq} != 'transmisop')",
				'finalisation jointure from' => "AND {$sq}{$this->Dossierpcg66->alias}_sq{$eq}.{$sq}id{$eq} = {$sq}{$this->Dossierpcg66->alias}{$eq}.{$sq}id{$eq}",
				'fin' => "RETURNING {$sq}{$this->Dossierpcg66->alias}{$eq}.{$sq}etatdossierpcg{$eq};"
			);

			$sql = implode( ' ', $query );
			$result = $Dbo->query( $sql );

			return $result !== false;
		}

		/**
		 * Mise à jour des positions des PCG qui devraient se trouver dans une
		 * position donnée.
		 *
		 * @param integer $etatdossierpcg66
		 * @return boolean
		 */
		public function updatePositionsPcgsByPosition( $etatdossierpcg66 ) {
			$conditions = $this->getConditionsPositionpcg( $etatdossierpcg66 );

			$query = array(
				'fields' => array( "{$this->Dossierpcg66->alias}.{$this->Dossierpcg66->primaryKey}" ),
				'conditions' => $conditions,
			);
			$sample = $this->Dossierpcg66->find( 'first', $query );

			return (
				empty( $sample )
				|| $this->Dossierpcg66->updateAllUnBound(
					array( "{$this->Dossierpcg66->alias}.etatdossierpcg66" => "'{$etatdossierpcg66}'" ),
					$conditions
				)
			);
		}

		/**
		 * Permet de mettre à jour les positions des PCG d'un allocataire retrouvé
		 * grâce à la clé primaire d'un PCG en particulier.
		 *
		 * @param integer $id La clé primaire d'un PCG.
		 * @return boolean
		 */
		public function updatePositionsPcgsById( $id ) {
			$return = $this->updatePositionsPcgsByConditions(
				array( $this->Dossierpcg66->alias . ".id" => $id )
			);

			return $return;
		}

		/**
		 * Renvoi la quete de base pour les impressions liés au dossier pcg (pour obtenir les informations nécéssaires)
		 *
		 * @param mixed $dossierpcg66_id
		 * @return array
		 */
		public function getImpressionBaseQuery( $dossierpcg66_id ) {
			return array(
				'fields' => array(
					'Decisiondossierpcg66.id',
					'Dossierpcg66.id',
					'Dossierpcg66.etatdossierpcg',
					'Personne.nom',
					'Personne.prenom',
					'Foyer.dossier_id'
				),
				'joins' => array(
					$this->Dossierpcg66->join( 'Decisiondossierpcg66',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Decisiondossierpcg66.validationproposition' => 'O',
								'Decisiondossierpcg66.id IN ('
								. 'SELECT a.id FROM decisionsdossierspcgs66 AS a '
								. 'WHERE a.dossierpcg66_id = "Dossierpcg66"."id" '
								. 'AND a.etatdossierpcg IS NULL ' // N'est pas annulé
								. "AND Decisiondossierpcg66.validationproposition = 'O'"
								. 'ORDER BY a.datevalidation DESC, a.created DESC '
								. 'LIMIT 1)'
							)
						)
					),
					$this->Dossierpcg66->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Dossierpcg66->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Dossierpcg66->Foyer->Personne->join( 'Prestation',
						array(
							'type' => 'INNER',
							'conditions' => array( 'Prestation.rolepers' => 'DEM' )
						)
					)
				),
				'contain' => false,
				'conditions' => array(
					'Dossierpcg66.id' => $dossierpcg66_id,
				),
				'order' => array(
					'Dossierpcg66.id' => 'DESC'
				)
			);
		}
	}
?>