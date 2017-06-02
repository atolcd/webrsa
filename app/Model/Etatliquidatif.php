<?php
	/**
	 * Code source de la classe Etatliquidatif.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Etatliquidatif ...
	 *
	 * @package app.Model
	 */
	class Etatliquidatif extends AppModel
	{
		public $name = 'Etatliquidatif';

		public $displayField = 'etatliquidatif';

		public $actsAs = array(
			'ValidateTranslate',
			'Frenchfloat' => array(
				'fields' => array(
					'montanttotalapre'
				)
			)
		);

		public $validate = array(
			'budgetapre_id' => array(
				array(
					'rule' => array( 'numeric' ),
				),
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
			'typeapre' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
			),
			'entitefi' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
				),
			),
			'tiers' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
				),
			),
			'codecdr' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
				),
			),
			'libellecdr' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
				),
			),
			'natureanalytique' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
				),
			),
			'programme' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
				),
			),
			'lib_programme' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
				),
			),
			'apreforfait' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
				),
			),
			'natureimput' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
				),
			),
			'operation' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
				),
			),
			'commentaire' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
				),
			),
		);

		public $belongsTo = array(
			'Budgetapre' => array(
				'className' => 'Budgetapre',
				'foreignKey' => 'budgetapre_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasAndBelongsToMany = array(
			'Apre' => array(
				'className' => 'Apre',
				'joinTable' => 'apres_etatsliquidatifs',
				'foreignKey' => 'etatliquidatif_id',
				'associationForeignKey' => 'apre_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'ApreEtatliquidatif'
			)
		);

		public $sousRequeteApreNbpaiementeff = '( SELECT COUNT( apres_etatsliquidatifs.id ) FROM apres_etatsliquidatifs WHERE apres_etatsliquidatifs.apre_id = "Apre"."id" AND apres_etatsliquidatifs.montantattribue IS NOT NULL GROUP BY apres_etatsliquidatifs.apre_id )';

		/**
		* Exécute les différentes méthods du modèle permettant la mise en cache.
		* Utilisé au préchargement de l'application (/prechargements/index).
		*
		* @return boolean true en cas de succès, false sinon.
		*/

		public function prechargement() {
			$success = parent::prechargement();

			$return = $this->qdDonneesApreForfaitaire( NULL );
			$success = !empty( $return ) && $success;

			$return = $this->qdDonneesApreForfaitaireGedooo( NULL );
			$success = !empty( $return ) && $success;

			$return = $this->qdDonneesApreComplementaire( NULL );
			$success = !empty( $return ) && $success;

			$return = $this->qdDonneesApreComplementaireGedooo( NULL );
			$success = !empty( $return ) && $success;

			return $return;
		}

		/**
		*   Récupération de la liste de toutes les APREs selon des conditions
		*   @param array $conditions
		*/

		public function listeApres( $conditions ) {
			$conditions = Set::merge(
				$conditions,
				array(
					'Apre.eligibiliteapre' => 'O'
				)
			);

			$queryData = array(
				'fields' => array(
					'Apre.id',
					'Apre.personne_id',
					'Apre.numeroapre',
					'Apre.statutapre',
					'Apre.datedemandeapre',
					'Apre.mtforfait',
					'Apre.montantaverser',
					'Apre.nbenf12',
					'Apre.nbpaiementsouhait',
					'Apre.montantdejaverse',
					'Personne.nom',
					'Personne.prenom',
					'Personne.qual',
					'Dossier.numdemrsa',
					'Adresse.nomcom',
					'Adresse.numvoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.libtypevoie',
					'Adresse.codepos',
				),
				'joins' => array(
					array(
						'table'      => 'personnes',
						'alias'      => 'Personne',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Apre.personne_id = Personne.id' )
					),
					array(
						'table'      => 'foyers',
						'alias'      => 'Foyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Personne.foyer_id = Foyer.id' )
					),
					array(
						'table'      => 'dossiers',
						'alias'      => 'Dossier',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Foyer.dossier_id = Dossier.id' )
					),
					array(
						'table'      => 'adressesfoyers',
						'alias'      => 'Adressefoyer',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Foyer.id = Adressefoyer.foyer_id', 'Adressefoyer.rgadr = \'01\'',
							'Adressefoyer.id IN (
								'.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01('Adressefoyer.foyer_id').'
							)'
						)
					),
					array(
						'table'      => 'adresses',
						'alias'      => 'Adresse',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Adresse.id = Adressefoyer.adresse_id' )
					),
				),
				'recursive' => -1,
				'conditions' => $conditions
			);

			///Jointure sur les tables des aides liées à l'APRE
			$this->Apre = ClassRegistry::init( 'Apre' );

			$queryData['joins'] = array_merge( $queryData['joins'], $this->Apre->WebrsaApre->joinsAidesLiees() );

			return $queryData;
		}

		/**
		*   Récupération de la liste des APREs selon des conditions pour un état liquidatif donné ( $id )
		*   @param array $conditions
		*   @param int $etatliquidatif_id -> identifiant de l'état liquidatif
		*/

		public function listeApresEtatLiquidatif( $conditions, $etatliquidatif_id ) {
			$conditions = Set::merge(
				$conditions,
				array(
					'Apre.id IN ( SELECT apres_etatsliquidatifs.apre_id FROM apres_etatsliquidatifs INNER JOIN etatsliquidatifs ON apres_etatsliquidatifs.etatliquidatif_id = etatsliquidatifs.id WHERE etatsliquidatifs.datecloture IS NOT NULL AND apres_etatsliquidatifs.etatliquidatif_id = '.Sanitize::clean( $etatliquidatif_id, array( 'encode' => false ) ).' )'
				)
			);

			///
			$queryData['joins'][] = array(
				'table'      => 'apres_etatsliquidatifs',
				'alias'      => 'ApreEtatliquidatif',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array(
					'Apre.id = ApreEtatliquidatif.apre_id',
					'ApreEtatliquidatif.etatliquidatif_id' => $etatliquidatif_id
				)
			);

			foreach( array_keys( $this->ApreEtatliquidatif->schema() ) as $fieldName ) {
				$queryData['fields'][] = "ApreEtatliquidatif.{$fieldName}";
			}

			return $this->listeApres( $conditions );
		}

		/**
		*   Récupération de la liste des APREs selon des conditions pour un état liquidatif donné ( $id )
		*   @param array $conditions
		*   @param int $etatliquidatif_id -> identifiant de l'état liquidatif
		*/

		public function listeApresEtatLiquidatifNonTermine( $conditions, $etatliquidatif_id ) {
			$conditions = Set::merge(
				$conditions,
				array(
					'Apre.id IN ( SELECT apres_etatsliquidatifs.apre_id FROM apres_etatsliquidatifs INNER JOIN etatsliquidatifs ON apres_etatsliquidatifs.etatliquidatif_id = etatsliquidatifs.id WHERE etatsliquidatifs.datecloture IS NULL AND apres_etatsliquidatifs.etatliquidatif_id = '.Sanitize::clean( $etatliquidatif_id, array( 'encode' => false ) ).' )'
				)
			);

			$queryData = $this->listeApres( $conditions );

			/// Création du champ virtuel montant total pour connaître les montants attribués à une APRE complémentaire
			$this->Apre = ClassRegistry::init( 'Apre' );
			$fieldTotal = array();
			foreach( $this->Apre->WebrsaApre->aidesApre as $modelAide ) {
				$fieldTotal[] = "\"{$modelAide}\".\"montantaide\"";
			}
			$queryData['fields'][] = '( COALESCE( '.implode( ', 0 ) + COALESCE( ', $fieldTotal ).', 0 ) ) AS "Apre__montanttotal"';

			return $queryData;
		}

		/**
		*   Récupération de la liste des APREs selon des conditions pour un état liquidatif donné ( $id )
		*   @param array $conditions
		*   @param int $etatliquidatif_id -> identifiant de l'état liquidatif
		*/

		public function listeApresEtatLiquidatifNonTerminePourVersement( $conditions, $etatliquidatif_id ) {
			$conditions = Set::merge(
				$conditions,
				array(
					'Apre.id IN (
						SELECT apres_etatsliquidatifs.apre_id
						FROM apres_etatsliquidatifs
							INNER JOIN etatsliquidatifs ON (
								apres_etatsliquidatifs.etatliquidatif_id = etatsliquidatifs.id
							)
						WHERE
							etatsliquidatifs.datecloture IS NULL
							AND apres_etatsliquidatifs.etatliquidatif_id = '.Sanitize::clean( $etatliquidatif_id, array( 'encode' => false ) ).'
							AND apres_etatsliquidatifs.montantattribue IS NULL
							AND (
								(
									'.$this->sousRequeteApreNbpaiementeff.' <> "Apre"."nbpaiementsouhait"
									OR "Apre"."nbpaiementsouhait" IS NULL
								)
								OR (
									Apre.montantdejaverse <> Apre.montantaverser
									/*'.$this->Apre->sousRequeteMontanttotal().'*/
								)
							)
					)'
				)
			);

			$queryData = $this->listeApres( $conditions );

			$queryData['fields'][] = '( SELECT SUM( apres_etatsliquidatifs.montantattribue ) FROM apres_etatsliquidatifs WHERE apres_etatsliquidatifs.apre_id = "Apre"."id" GROUP BY apres_etatsliquidatifs.apre_id ) AS "Apre__montantattribue"';

			$queryData['fields'][] = $this->sousRequeteApreNbpaiementeff.' AS "Apre__nbpaiementeff"';

			///
			$queryData['joins'][] = array(
				'table'      => 'apres_etatsliquidatifs',
				'alias'      => 'ApreEtatliquidatif',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array(
					'Apre.id = ApreEtatliquidatif.apre_id',
					'ApreEtatliquidatif.etatliquidatif_id' => $etatliquidatif_id
				)
			);

			foreach( array_keys( $this->ApreEtatliquidatif->schema() ) as $fieldName ) {
				$queryData['fields'][] = "ApreEtatliquidatif.{$fieldName}";
			}

			return $queryData;;
		}


		/**
		*   Retourne une requête cakePhp permettant d'obtenir la liste des APREs
		*   non passées dans un état liquidatif donné, selon certaines conditions
		*   @param array $conditions
		*   @return array $queryData -> Requête au format cakePhp
		**/

		public function  listeApresSansEtatLiquidatif( $conditions ) {
			$conditions = Set::merge(
				$conditions,
				array(
					'Apre.id NOT IN ( SELECT apres_etatsliquidatifs.apre_id FROM apres_etatsliquidatifs INNER JOIN etatsliquidatifs ON apres_etatsliquidatifs.etatliquidatif_id = etatsliquidatifs.id WHERE etatsliquidatifs.datecloture IS NOT NULL )'
				)
			);

			$queryData = $this->listeApres( $conditions );
			$queryData['fields'] = array(
				'Apre.id',
				'Apre.personne_id',
				'Apre.numeroapre',
				'Apre.datedemandeapre',
				'Apre.mtforfait',
				'Apre.nbenf12',
				'Apre.quota',
				'Personne.nom',
				'Personne.prenom',
				'Dossier.numdemrsa',
				'Adresse.nomcom',
			);

			/// Création du champ virtuel montant total pour connaître les montants attribués à une APRE complémentaire
			$this->Apre = ClassRegistry::init( 'Apre' );
			$fieldTotal = array();
			foreach( $this->Apre->WebrsaApre->aidesApre as $modelAide ) {
				$fieldTotal[] = "\"{$modelAide}\".\"montantaide\"";
			}
			$queryData['fields'][] = '( COALESCE( '.implode( ', 0 ) + COALESCE( ', $fieldTotal ).', 0 ) ) AS "Apre__montanttotal"';

			return $queryData;
		}

		/**
		*   Retourne une requête cakePhp permettant d'obtenir la liste des APREs
		*   non passées dans un état liquidatif donné, selon certaines conditions
		*   @param array $conditions
		*   @return array $queryData -> Requête au format cakePhp
		**/

		public function listeApresPourEtatLiquidatif( $etatliquidatif_id, $conditions ) {
			$conditions = Set::merge(
				$conditions,
				array(
					'( Apre.id NOT IN ( SELECT apres_etatsliquidatifs.apre_id FROM apres_etatsliquidatifs INNER JOIN etatsliquidatifs ON apres_etatsliquidatifs.etatliquidatif_id = etatsliquidatifs.id WHERE etatsliquidatifs.datecloture IS NOT NULL )
					OR Apre.id IN ( SELECT apres_etatsliquidatifs.apre_id FROM apres_etatsliquidatifs INNER JOIN etatsliquidatifs ON apres_etatsliquidatifs.etatliquidatif_id = etatsliquidatifs.id WHERE ( '.$this->sousRequeteApreNbpaiementeff.' <> "Apre"."nbpaiementsouhait" ) OR ( Apre.montantdejaverse <> Apre.montantaverser/*.$this->Apre->sousRequeteMontanttotal().*/ ) ) )'
				)
			);

			$queryData = $this->listeApres( $conditions );
			$queryData['fields'] = array(
				'Apre.id',
				'Apre.personne_id',
				'Apre.numeroapre',
				'Apre.datedemandeapre',
				'Apre.mtforfait',
				'Apre.montantaverser',
				'Apre.nbenf12',
				'Apre.quota',
				'Personne.nom',
				'Personne.prenom',
				'Dossier.numdemrsa',
				'Adresse.nomcom',
			);

			/**
			*   On ne veut afficher que les APREs complémentaires
			**/
			$jointure = !(
				is_array( $conditions ) &&
				array_key_exists( 'Apre.statutapre', $conditions ) &&
				$conditions['Apre.statutapre'] == 'F'
			);

			/**
			* On ne souhaite afficher QUE les APREs complémentaires passées en comité
			* avec une décision d'ACCORD pour leur dernier passage en comité,
			*/
			if( $jointure == true ) {
				$queryData['joins'][] = array(
					'table'      => 'apres_comitesapres',
					'alias'      => 'ApreComiteapre',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						'Apre.id = ApreComiteapre.apre_id',
						'ApreComiteapre.decisioncomite' => 'ACC',
						'ApreComiteapre.id IN ('
							.$this->Apre->ApreComiteapre->sqDernierComiteApre()
						.')'
					)
				);
			}

			return $queryData;
		}

		/**
		*   Récupération de la liste des APREs pour le fichier HOPEYRA selon un état liquidaif donné
		*   @param int $id --> Id de l'état liquidatif
		*/

		public function hopeyra( $id, $typeapre ) {
			$champAllocation = null;
			if( $typeapre == 'forfaitaire' ) {
				$champAllocation = '"Apre"."mtforfait" AS "Apre__allocation"';
			}
			else if( $typeapre == 'complementaire' ) {
				$champAllocation = '"ApreEtatliquidatif"."montantattribue" AS "Apre__allocation"';
			}
			else {
				$this->cakeError( 'error500' );
			}

			$this->Apre->unbindModelAll();

			$queryData = array(
				'fields' => array(
					$champAllocation,
					'Apre.nbenf12',
					'Apre.statutapre',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Dossier.numdemrsa',
					'Dossier.matricule',
					'Paiementfoyer.titurib',
					'Paiementfoyer.nomprenomtiturib',
					'Paiementfoyer.etaban',
					'Paiementfoyer.guiban',
					'Paiementfoyer.numcomptban',
					'Paiementfoyer.clerib',
					'Domiciliationbancaire.libelledomiciliation',
				),
				'joins' => array(
					array(
						'table'      => 'personnes',
						'alias'      => 'Personne',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Apre.personne_id = Personne.id' )
					),
					array(
						'table'      => 'foyers',
						'alias'      => 'Foyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Personne.foyer_id = Foyer.id' )
					),
					array(
						'table'      => 'paiementsfoyers',
						'alias'      => 'Paiementfoyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'Paiementfoyer.foyer_id = Foyer.id',
							// FIXME: voir ailleurs, quand on utilise paiementsfoyers
							// INFO: C'EST JUSTE LE DERNIER POUR LE FOYER
							// FIXME: à faire dans importcsvapres
							// FIXME: DEM ou CJT
							'Paiementfoyer.id IN ( SELECT MAX(paiementsfoyers.id)
								FROM paiementsfoyers
								WHERE paiementsfoyers.topribconj = (
									CASE WHEN (
										SELECT prestations.rolepers
											FROM prestations
											WHERE prestations.personne_id = "Personne"."id"
												AND prestations.natprest = \'RSA\'
												AND prestations.rolepers IN ( \'DEM\', \'CJT\' )
									)
									= \'DEM\' THEN false ELSE true END
								)
								GROUP BY paiementsfoyers.foyer_id
							)'
						)
					),
					array(
						'table'      => 'dossiers',
						'alias'      => 'Dossier',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Foyer.dossier_id = Dossier.id' )
					),
					array(
						'table'      => 'apres_etatsliquidatifs',
						'alias'      => 'ApreEtatliquidatif',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Apre.id = ApreEtatliquidatif.apre_id' )
					),
					array(
						'table'      => 'domiciliationsbancaires',
						'alias'      => 'Domiciliationbancaire',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'Domiciliationbancaire.codebanque = Paiementfoyer.etaban',
							'Domiciliationbancaire.codeagence = Paiementfoyer.guiban'
						)
					),
				),
				'recursive' => -1,
				'conditions' => array( 'ApreEtatliquidatif.etatliquidatif_id' => Sanitize::clean( $id, array( 'encode' => false ) ) ),
				'order' => array( 'Paiementfoyer.nomprenomtiturib ASC', 'Foyer.id ASC' )
			);

			$this->Apre = ClassRegistry::init( 'Apre' );
			$queryData['joins'] = array_merge( $queryData['joins'], $this->Apre->WebrsaApre->joinsAidesLiees( true ) );

			return $this->Apre->find( 'all', $queryData );
		}


		/**
		* Récupération de la liste des APREs pour le fichier PDF selon un état liquidatif donné ( $id )
		* @param int $id
		*/

		public function pdf( $id, $typeapre, $qdTiersprestataireapreFormations = false ) {
			$champAllocation = null;
			if( $typeapre == 'forfaitaire' ) {
				$champAllocation = '"Apre"."mtforfait" AS "Apre__allocation"';
			}
			else if( $typeapre == 'complementaire' ) {
				$champAllocation = '"ApreEtatliquidatif"."montantattribue" AS "Apre__allocation"';
			}
			else {
				$this->cakeError( 'error500' );
			}

			$PaiementfoyerModel = ClassRegistry::init( 'Paiementfoyer' );
			$sqPaiementfoyerIdPourAllocataire = $PaiementfoyerModel->sqPaiementfoyerIdPourAllocataire( 'Personne.id' );

			$querydata = array(
				'fields' => array(
					'Personne.id',
					'Paiementfoyer.titurib',
					'Paiementfoyer.nomprenomtiturib', // FIXME ?
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.compladr',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Paiementfoyer.etaban',
					'Paiementfoyer.guiban',
					'Paiementfoyer.numcomptban',
					'Paiementfoyer.clerib',
					'Domiciliationbancaire.libelledomiciliation',
					$champAllocation
				),
				'joins' => array(
					array(
						'table'      => 'personnes',
						'alias'      => 'Personne',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Apre.personne_id = Personne.id' )
					),
					array(
						'table'      => 'foyers',
						'alias'      => 'Foyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Personne.foyer_id = Foyer.id' )
					),
					array(
						'table'      => 'adressesfoyers',
						'alias'      => 'Adressefoyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'Foyer.id = Adressefoyer.foyer_id', 'Adressefoyer.rgadr = \'01\'',
							'Adressefoyer.id IN (
								'.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01('Adressefoyer.foyer_id').'
							)'
						)
					),
					array(
						'table'      => 'adresses',
						'alias'      => 'Adresse',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Adresse.id = Adressefoyer.adresse_id' )
					),
					array(
						'table'      => 'paiementsfoyers',
						'alias'      => 'Paiementfoyer',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Paiementfoyer.foyer_id = Foyer.id',
							// FIXME: voir ailleurs, quand on utilise paiementsfoyers
							// INFO: C'EST JUSTE LE DERNIER POUR LE FOYER
							// FIXME: à faire dans importcsvapres
							// FIXME: DEM ou CJT
							"Paiementfoyer.id IN ( {$sqPaiementfoyerIdPourAllocataire} )"
						)
					),
					array(
						'table'      => 'apres_etatsliquidatifs',
						'alias'      => 'ApreEtatliquidatif',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Apre.id = ApreEtatliquidatif.apre_id' )
					),
					array(
						'table'      => 'domiciliationsbancaires',
						'alias'      => 'Domiciliationbancaire',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array(
							'Domiciliationbancaire.codebanque = Paiementfoyer.etaban',
							'Domiciliationbancaire.codeagence = Paiementfoyer.guiban'
						)
					),
				),
				'recursive' => -1,
				'conditions' => array( 'ApreEtatliquidatif.etatliquidatif_id' => Sanitize::clean( $id, array( 'encode' => false ) ) ),
				'order' => array( 'Paiementfoyer.nomprenomtiturib ASC', 'Foyer.id ASC' )
			);

			if( $qdTiersprestataireapreFormations && $typeapre == 'complementaire' ) {
				$qdForm = $this->Apre->qdFormationsPourPdf();
				foreach( $qdForm as $key => $value ) {
					foreach( $value as $v ) {
						$querydata[$key][] = $v;
					}
				}
			}

			$this->Apre->unbindModelAll();

			return $this->Apre->find( 'all', $querydata );
		}

		/**
		*
		*/

		protected function _qdDonneesApreCommun() {
			$querydata = array(
				'fields' => Set::merge(
					$this->Apre->fields(),
					$this->Apre->Personne->fields(),
					$this->ApreEtatliquidatif->fields(),
					$this->fields(),
					$this->Apre->Personne->Foyer->Adressefoyer->Adresse->fields(),
					array(
						$this->Apre->Personne->Foyer->Adressefoyer->Adresse->sqVirtualField( 'localite' )
					),
					$this->Apre->Personne->Foyer->Dossier->fields()
				),
				'contain' => false,
				'joins' => array(
					$this->join( 'ApreEtatliquidatif', array( 'type' => 'INNER' ) ),
					$this->ApreEtatliquidatif->join( 'Apre', array( 'type' => 'INNER' ) ),
					$this->Apre->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Apre->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Apre->Personne->Foyer->join( 'Adressefoyer' ),
					$this->Apre->Personne->Foyer->Adressefoyer->join( 'Adresse' ),
					$this->Apre->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
				),
				'conditions' => array(
					'Adressefoyer.id IN ('
						.$this->Apre->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).
					')',
					'Apre.eligibiliteapre' => 'O',
					'Etatliquidatif.datecloture IS NOT NULL'
				)
			);

			return $querydata;
		}

		/**
		*
		*/

		protected function _qdDonneesApreForfaitaire() {
			$querydata = $this->_qdDonneesApreCommun();

			$querydata['conditions']['Apre.statutapre'] = 'F';

			return $querydata;
		}

		/**
		*
		*/

		protected function _qdDonneesApreComplementaire() {
			$dbo = $this->getDataSource( $this->useDbConfig );

			$querydata = $this->_qdDonneesApreCommun();

			$querydata['fields'] = array_merge(
				$querydata['fields'],
				array_merge(
					$this->Apre->ApreComiteapre->fields(),
					$this->Apre->ApreComiteapre->Comiteapre->fields(),
					$this->Apre->Structurereferente->fields(),
					$this->Apre->Structurereferente->Referent->fields()
				)
			);

			$querydata['joins'] = array_merge(
				$querydata['joins'],
				array(
					$this->Apre->join( 'ApreComiteapre' ),
					$this->Apre->ApreComiteapre->join( 'Comiteapre' ),
					$this->Apre->join( 'Structurereferente' ),
					$this->Apre->join( 'Referent' ),
				)
			);

			$querydata['conditions'] = array_merge(
				$querydata['conditions'],
				array(
					'Apre.statutapre' => 'C',
					'ApreComiteapre.id IN ('
						// FIXME: faire une fonction dans ApreComiteapre: sqDernierPassage( $field = 'Apre.id' )
						.$this->Apre->ApreComiteapre->sq(
							array(
								'fields' => array( 'apres_comitesapres.id' ),
								'alias' => 'apres_comitesapres',
								'joins' => array(
									array(
										'table' => $dbo->fullTableName( $this->Apre->ApreComiteapre->Comiteapre, true, false ),
										'alias' => 'comitesapres',
										'type' => 'INNER',
										'conditions' => array(
											'"apres_comitesapres"."comiteapre_id" = "comitesapres"."id"'
										)
									)
								),
								'conditions' => array(
									'apres_comitesapres.apre_id = Apre.id',
// 									'apres_comitesapres.decisioncomite' => 'ACC'
								),
								'order' => array(
									'comitesapres.datecomite DESC',
									'comitesapres.heurecomite DESC',
								),
								'limit' => 1
							)
						)
					.')',
				)
			);

			return $querydata;
		}

		/**
		*
		*/

		public function qdDonneesApreForfaitaire() {
			$cacheKey = $this->useDbConfig.'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$querydata = Cache::read( $cacheKey );

			if( $querydata === false ) {
				$querydata = $this->_qdDonneesApreForfaitaire();

				// INFO: ce sont les seules infos envoyées à Gedooo dans EtatsliquidatifsController::impressioncohorte
				$querydata['fields'] = array(
					'Apre.id',
					'Apre.personne_id',
					'Apre.numeroapre',
					'Apre.statutapre',
					'Apre.datedemandeapre',
					'Apre.mtforfait',
					'Apre.montantaverser',
					'Apre.nbenf12',
					'Apre.nbpaiementsouhait',
					'Apre.montantdejaverse',
					'NULL AS "Apre__nomaide"',
					'NULL AS "Apre__natureaide"',
					'"Apre"."mtforfait" AS "Apre__allocation"',
					'Personne.nom',
					'Personne.prenom',
					'Personne.qual',
					'Dossier.numdemrsa',
					'Adresse.nomcom',
					'Adresse.numvoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.libtypevoie',
					'Adresse.codepos',
				);

				Cache::write( $cacheKey, $querydata );
			}

			return $querydata;
		}

		/**
		*
		*/

		public function qdDonneesApreForfaitaireGedooo() {
			$cacheKey = $this->useDbConfig.'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$querydata = Cache::read( $cacheKey );

			if( $querydata === false ) {
				$querydata = $this->_qdDonneesApreForfaitaire();

				// INFO: ce sont les seules infos envoyées à Gedooo dans EtatsliquidatifsController::impressioncohorte
				$querydata['fields'] = array(
					'Apre.id',
					'Apre.personne_id',
					'Apre.numeroapre',
					'Apre.statutapre',
					'Apre.datedemandeapre',
					'Apre.mtforfait',
					'Apre.montantaverser',
					'Apre.nbenf12',
					'Apre.nbpaiementsouhait',
					'Apre.montantdejaverse',
					'NULL AS "Apre__nomaide"',
					'NULL AS "Apre__natureaide"',
					'"Apre"."mtforfait" AS "Apre__allocation"',
					'Personne.nom',
					'Personne.prenom',
					'Personne.qual',
					'Dossier.numdemrsa',
					'Adresse.nomcom',
					'Adresse.numvoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.libtypevoie',
					'Adresse.codepos',
				);

				Cache::write( $cacheKey, $querydata );
			}

			return $querydata;
		}

		/**
		*
		*/
		public function qdDonneesApreComplementaire() {
			$cacheKey = $this->useDbConfig.'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$querydata = Cache::read( $cacheKey );

			if( $querydata === false ) {
				$querydata = $this->_qdDonneesApreComplementaire();

				$querydata['fields'] = array(
					'Dossier.numdemrsa',
					'Apre.id',
					'Apre.numeroapre',
					'Apre.datedemandeapre',
					'Apre.mtforfait',
					'Apre.nbenf12',
					$this->Apre->WebrsaApre->sqApreNomaide().' AS "Apre__natureaide"',
					$this->Apre->WebrsaApre->sqApreNomaide().' AS "Apre__nomaide"',
					'Personne.nom',
					'Personne.prenom',
					'Adresse.nomcom',
					'ApreEtatliquidatif.montantattribue',
					'Etatliquidatif.id',
					'Etatliquidatif.typeapre',
				);

				$querydata['conditions']['ApreComiteapre.decisioncomite'] = 'ACC';

				Cache::write( $cacheKey, $querydata );
			}

			return $querydata;
		}

		/**
		*
		*/

		public function qdDonneesApreComplementaireGedooo() {
			$cacheKey = $this->useDbConfig.'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$querydata = Cache::read( $cacheKey );

			if( $querydata === false ) {
				$dbo = $this->getDataSource( $this->useDbConfig );
				$querydata = $this->_qdDonneesApreComplementaire();

				$querydata['fields'] = Set::merge(
					$querydata['fields'],
					array(
						$this->Apre->WebrsaApre->sqApreNomaide().' AS "Apre__nomaide"',
						$this->Apre->WebrsaApre->sqApreNomaide().' AS "Apre__natureaide"',
						'"ApreEtatliquidatif"."montantattribue" AS "Apre__allocation"'
					)
				);

				// Population Modellie
				foreach( array( 'ddform', 'dfform', 'dureeform' ) as $field ) {
					$case = "CASE ";
					if( $field != 'dureeform' ) {
						$models = array( 'Formqualif', 'Formpermfimo', 'Actprof' );
					}
					else {
						$models = array( 'Formqualif', 'Formpermfimo', 'Permisb', 'Actprof' );
					}
					foreach( $models as $aideModel ) {
						$tableName = $dbo->fullTableName( $this->Apre->{$aideModel}, false, false );
						$case .= "WHEN EXISTS( SELECT * FROM {$tableName} AS \"{$aideModel}\" WHERE \"Apre\".\"id\" = \"{$aideModel}\".\"apre_id\" ) THEN \"{$aideModel}\".\"{$field}\" ";
					}
					$case .= 'ELSE NULL END';
					$querydata['fields'][] = "{$case} AS \"Modellie__{$field}\"";
				}

				foreach( $this->Apre->WebrsaApre->aidesApre as $modelAide ) {
					$querydata['joins'][] = $this->Apre->join( $modelAide );
					$querydata['fields'] = Set::merge( $querydata['fields'], $this->Apre->{$modelAide}->fields() );
				}

				// Tiersprestataireapre
				$TiersprestataireapreModel = ClassRegistry::init( 'Tiersprestataireapre' );
				$join = array(
					'table' => $dbo->fullTableName( $TiersprestataireapreModel, true, false ),
					'alias' => 'Tiersprestataireapre',
					'type' => 'LEFT',
					'conditions' => array()
				);

				foreach( $this->Apre->WebrsaApre->modelsFormation as $modelAide ) {
					$join['conditions']['OR'][] = "( {$modelAide}.tiersprestataireapre_id IS NOT NULL AND  Tiersprestataireapre.id = {$modelAide}.tiersprestataireapre_id )";
				}
				$querydata['joins'][] = $join;

				$querydata['fields'] = Set::merge(
					$querydata['fields'],
					$TiersprestataireapreModel->fields()
				);

				// Paiement foyer pour la personne
				$PaiementfoyerModel = ClassRegistry::init( 'Paiementfoyer' );
				$sqPaiementfoyerIdPourAllocataire = $PaiementfoyerModel->sqPaiementfoyerIdPourAllocataire( 'Personne.id' );
				$querydata['joins'][] = array(
					'table'      => $dbo->fullTableName( $PaiementfoyerModel, true, false ),
					'alias'      => 'Paiementfoyer',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'Paiementfoyer.foyer_id = Personne.foyer_id',
						"Paiementfoyer.id IN ( {$sqPaiementfoyerIdPourAllocataire} )"
					)
				);
				$querydata['fields'] = Set::merge(
					$querydata['fields'],
					$PaiementfoyerModel->fields()
				);

				// Données concernant les coordonées bancaires du tiers
				$DomiciliationbancaireModel = ClassRegistry::init( 'Domiciliationbancaire' );
				$querydata['joins'][] = array(
					'table'      => $dbo->fullTableName( $DomiciliationbancaireModel, true, false ),
					'alias'      => 'Domiciliationbancaire',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'OR' => array(
							array(
								'Tiersprestataireapre.id IS NOT NULL',
								'Domiciliationbancaire.codebanque = Tiersprestataireapre.etaban',
								'Domiciliationbancaire.codeagence = Tiersprestataireapre.guiban',
							),
							array(
								'Tiersprestataireapre.id IS NULL',
								'Domiciliationbancaire.codebanque = Paiementfoyer.etaban',
								'Domiciliationbancaire.codeagence = Paiementfoyer.guiban',
							)
						)
					)
				);
				$querydata['fields'] = Set::merge(
					$querydata['fields'],
					$DomiciliationbancaireModel->fields()
				);

				// Suivi
				$SuiviaideapretypeaideModel = ClassRegistry::init( 'Suiviaideapretypeaide' );
				$querydata['joins'][] = array(
					'table'      => $dbo->fullTableName( $SuiviaideapretypeaideModel, true, false ),
					'alias'      => 'Suiviaideapretypeaide',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'Suiviaideapretypeaide.typeaide = ( '.$this->Apre->WebrsaApre->sqApreNomaide().' )'
					)
				);
				$join = $SuiviaideapretypeaideModel->join( 'Suiviaideapre' );
				$join['conditions'] = array( $join['conditions'], 'Suiviaideapre.deleted = \'0\'' );
				$querydata['joins'][] = $join;
				foreach( array_keys( $SuiviaideapretypeaideModel->Suiviaideapre->schema() ) as $field ) {
					$querydata['fields'][] = "\"{$SuiviaideapretypeaideModel->Suiviaideapre->alias}\".\"{$field}\" AS \"Dataperssuivi__{$field}suivi\"";
				}

				// Montants
				$querydata['fields'][] = 'ROUND( ( '.$this->Apre->WebrsaApre->sqApreAllocation().' ) / ( CASE WHEN "Apre"."montantaverser" <> 0 THEN "Apre"."montantaverser" ELSE 1 END ) * 100, 0 ) AS "Apre__pourcentallocation"';
				$querydata['fields'][] = 'ROUND( "Apre"."montantdejaverse" - "Apre"."montantaverser", 2 ) AS "Apre__restantallocation"';


				Cache::write( $cacheKey, $querydata );
			}

			return $querydata;
		}

		/**
		*
		*/

		public function getTypeapre( $id ) {
			$etatliquidatif = $this->find(
				'first',
				array(
					'fields' => array(
						'Etatliquidatif.id',
						'Etatliquidatif.typeapre'
					),
					'conditions' => array(
						'Etatliquidatif.id' => $id,
					),
					'contain' => false
				)
			);

			return Set::classicExtract( $etatliquidatif, 'Etatliquidatif.typeapre' );
		}
	}
?>
