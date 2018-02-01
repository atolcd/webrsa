<?php
	/**
	 * Code source de la classe Foyer.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Foyer ...
	 *
	 * @package app.Model
	 */
	class Foyer extends AppModel
	{
		public $name = 'Foyer';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Fichiermodulelie',
            'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 *
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array(
			'regagrifam' => array('NSA', 'SA'),
		);

		/**
		 * Valeurs de Foyer.sitfam consideré comme une situation de vie de couple.
		 *
		 * @var array
		 */
		public $sitfam_en_couple = array(
			// TODO: libellés
			'MAR', 'PAC', 'RPA', 'RVC', 'RVM', 'VIM'
		);

		/**
		 * Valeurs de Foyer.sitfam consideré comme une situation d'isolement.
		 *
		 * @var array
		 */
		public $sitfam_isole = array(
			'CEL', // Célibataire
			'DIV', // Divorcé(e)
			'ISO', // Isolement après vie maritale ou PACS
			'SEF', // Séparation de fait
			'SEL', // Séparation légale
			'VEU', // Veuvage
		);

		public $virtualFields = array(
			'enerreur' => array(
				'type' => 'string',
				'postgres' => "(
					CASE WHEN NOT
					(
						(
							SELECT COUNT(personnes.id)
								FROM personnes
									INNER JOIN prestations ON (
										prestations.personne_id = personnes.id
										AND prestations.natprest = 'RSA'
										AND prestations.rolepers = 'DEM'
									)
								WHERE personnes.foyer_id = \"%s\".\"id\"
						) = 1
						AND
						(
							SELECT COUNT(personnes.id)
								FROM personnes
									INNER JOIN prestations ON (
										prestations.personne_id = personnes.id
										AND prestations.natprest = 'RSA'
										AND prestations.rolepers = 'CJT'
									)
								WHERE personnes.foyer_id = \"%s\".\"id\"
						) <= 1
					) THEN (
						'Ce foyer comporte ' ||
						(
							(
								SELECT COUNT(personnes.id)
									FROM personnes
										INNER JOIN prestations ON (
											prestations.personne_id = personnes.id
											AND prestations.natprest = 'RSA'
											AND prestations.rolepers = 'DEM'
										)
									WHERE personnes.foyer_id = \"%s\".\"id\"
							)
							|| ' demandeur(s) et ' ||
							(
								SELECT COUNT(personnes.id)
									FROM personnes
										INNER JOIN prestations ON (
											prestations.personne_id = personnes.id
											AND prestations.natprest = 'RSA'
											AND prestations.rolepers = 'CJT'
										)
									WHERE personnes.foyer_id = \"%s\".\"id\"
							)
						) || ' conjoint(s).'
					) ELSE NULL
					END
				)"
			),
			'sansprestation' => array(
				'type' => 'string',
				'postgres' => "(
					CASE WHEN
					(
						(
							SELECT COUNT(*)
								FROM personnes
								WHERE
									personnes.id NOT IN (
										SELECT
												prestations.personne_id
											FROM prestations
											WHERE prestations.personne_id = personnes.id
									)
									AND personnes.foyer_id = \"%s\".\"id\"
						) > 0
					) THEN (
						'Ce foyer comporte des personnes sans prestation'
					) ELSE NULL
					END
				)",
			)
		);

		public $belongsTo = array(
			'Dossier' => array(
				'className' => 'Dossier',
				'foreignKey' => 'dossier_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Adressefoyer' => array(
				'className' => 'Adressefoyer',
				'foreignKey' => 'foyer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Anomalie' => array(
				'className' => 'Anomalie',
				'foreignKey' => 'foyer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Controleadministratif' => array(
				'className' => 'Controleadministratif',
				'foreignKey' => 'foyer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Creance' => array(
				'className' => 'Creance',
				'foreignKey' => 'foyer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Evenement' => array(
				'className' => 'Evenement',
				'foreignKey' => 'foyer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Fichedeliaison' => array(
				'className' => 'Fichedeliaison',
				'foreignKey' => 'foyer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Foyer\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Modecontact' => array(
				'className' => 'Modecontact',
				'foreignKey' => 'foyer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Paiementfoyer' => array(
				'className' => 'Paiementfoyer',
				'foreignKey' => 'foyer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'foyer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Dossierpcg66' => array(
				'className' => 'Dossierpcg66',
				'foreignKey' => 'foyer_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'EntiteTag' => array(
				'className' => 'EntiteTag',
				'foreignKey' => 'fk_value',
				'dependent' => false,
				'conditions' => array(
					'EntiteTag.modele' => 'Foyer'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		/**
		 * Retourne l'id du dossier à partir de l'id du foyer
		 *
		 * @param integer $foyer_id
		 * @return integer
		 */
		public function dossierId( $foyer_id ) {
			$querydata = array(
				'fields' => array( 'Foyer.dossier_id' ),
				'conditions' => array(
					'Foyer.id' => $foyer_id
				),
				'order' => null,
				'recursive' => -1
			);

			$foyer = $this->find( 'first', $querydata );

			if( !empty( $foyer ) ) {
				return $foyer['Foyer']['dossier_id'];
			}
			else {
				return null;
			}
		}

		/**
		 * Retourne le nombre d'enfants au sein d'un foyer.
		 *
		 * @param integer $foyer_id
		 * @return integer
		 */
		public function nbEnfants( $foyer_id ) {
			$sql = "SELECT COUNT(Prestation.id)
						FROM prestations AS Prestation
							INNER JOIN personnes AS Personne ON Personne.id = Prestation.personne_id
						WHERE Personne.foyer_id = {$foyer_id}
							AND Prestation.natprest = 'RSA'
							AND Prestation.rolepers = 'ENF'";
			$result = $this->Personne->query( $sql );
			return $result[0][0]['count'];
		}

		/**
		 * Retourne un champ virtuel permettant de connaître le nombre d'enfants au sein d'un foyer
		 *
		 * @param type $foyerId
		 * @return type
		 */
		public function vfNbEnfants( $foyerId = 'Foyer.id' ) {
			return $this->Personne->Prestation->sq(
				array(
					'fields' => array(
						'COUNT(prestations.id)'
					),
					'alias' => 'prestations',
					'joins' => array(
						array_words_replace(
								$this->Personne->Prestation->join( 'Personne', array( 'type' => 'INNER' ) ), array(
							'Prestation' => 'prestations',
							'Personne' => 'personnes',
								)
						)
					),
					'conditions' => array(
						"personnes.foyer_id = {$foyerId}",
						'prestations.natprest' => 'RSA',
						'prestations.rolepers' => 'ENF',
					),
				)
			);
		}

		/**
		 *
		 */
		public function refreshRessources( $foyer_id ) {
			$query = array(
				'fields' => array(
					'"Personne"."id'
				),
				'joins' => array(
					array(
						'table' => 'prestations',
						'alias' => 'Prestation',
						'type' => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'Personne.id = Prestation.personne_id',
							'Prestation.natprest = \'RSA\'',
							'Prestation.rolepers' => array( 'DEM', 'CJT' )
						)
					)
				),
				'conditions' => array(
					'Personne.foyer_id' => $foyer_id
				),
				'recursive' => -1
			);

			$personnes = $this->Personne->find( 'all', $query );
			$this->Personne->bindModel( array( 'hasMany' => array( 'Ressource' ) ) );

			$saved = true;
			foreach( $personnes as $personne ) {
				$saved = $this->Personne->Ressource->refresh( $personne['Personne']['id'] ) && $saved;
			}
			return $saved;
		}

		/**
		 *
		 */
		public function refreshSoumisADroitsEtDevoirs( $foyer_id ) {
			$query = array(
				'fields' => array(
					'"Personne"."id"',
					'"Prestation"."id"',
					'"Calculdroitrsa"."id"'
				),
				'joins' => array(
					$this->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$this->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'Personne.foyer_id' => $foyer_id
				),
				'recursive' => -1
			);

			$personnesFoyer = $this->Personne->find( 'all', $query );

			// Doit-on automatiquement ajouter une entrée "Non orienté" dans orientsstructs ?
			$ajoutOrientstruct = Configure::read( 'Foyer.refreshSoumisADroitsEtDevoirs.ajoutOrientstruct' );

			$saved = true;
			foreach( $personnesFoyer as $personne ) {
				$toppersdrodevorsa = $this->Personne->WebrsaPersonne->soumisDroitsEtDevoirs( $personne['Personne']['id'] );
				$personne['Calculdroitrsa']['toppersdrodevorsa'] = ( $toppersdrodevorsa ? '1' : '0' );
				$this->Personne->Calculdroitrsa->create( $personne['Calculdroitrsa'] );
				$saved = $this->Personne->Calculdroitrsa->save( $personne['Calculdroitrsa'] , array( 'atomic' => false ) ) && $saved;

				if( true === $ajoutOrientstruct ) {
					// Ajout dans la table orientsstructs si aucune entrée
					$nbrOrientstruct = $this->Personne->Orientstruct->find( 'count', array( 'conditions' => array( 'Orientstruct.personne_id' => $personne['Personne']['id'] ) ) );
					if( $personne['Calculdroitrsa']['toppersdrodevorsa'] && $nbrOrientstruct == 0 ) {
						$orientstruct = array(
							'Orientstruct' => array(
								'personne_id' => $personne['Personne']['id'],
								'statut_orient' => 'Non orienté'
							)
						);
						$this->Personne->Orientstruct->create( $orientstruct );
						$saved = $this->Personne->Orientstruct->save( null, array( 'atomic' => false ) ) && $saved;
					}
				}
			}
			return $saved;
		}

		/**
		 * 	FIXME: spécifique CG93 ?
		 */
		public function montantForfaitaire( $id ) {
			$F = 454.63;
			$this->Personne->unbindModelAll();
			$this->Personne->bindModel(
					array(
						'hasOne' => array(
							'Calculdroitrsa'
						)
					)
			);

			$personnes = $this->Personne->find(
					'all', array(
				'conditions' => array( 'Personne.foyer_id' => $id )
					)
			);

			// a) Si 1 foyer = 1 personne, montant forfaitaire = F (= 454,63 EUR)
			if( count( $personnes ) == 1 ) {
				$mtpersressmenrsa = Set::extract( $personnes, '/Calculdroitrsa/mtpersressmenrsa' );
				$montant = array_sum( Hash::filter( (array)$mtpersressmenrsa ) );
				return ( $montant < $F );
			}
			// b) Si 1 foyer = 2 personnes, montant forfaitaire = 150% F
			else if( count( $personnes ) == 2 ) {
				$mtpersressmenrsa = Set::extract( $personnes, '/Calculdroitrsa/mtpersressmenrsa' );
				$montant = array_sum( Hash::filter( (array)$mtpersressmenrsa ) );
				return ( $montant < ( $F * 1.5 ) );
			}
			else {
				$X = 0;
				$Y = 0;
				$montant = 0;

				foreach( $personnes as $personne ) {
					list( $year, $month, $day ) = explode( '-', $personne['Personne']['dtnai'] );
					$today = time();
					$age = date( 'Y', $today ) - $year + ( ( ( $month > date( 'm', $today ) ) || ( $month == date( 'm', $today ) && $day > date( 'd', $today ) ) ) ? -1 : 0 );

					if( $age >= 25 ) {
						$X++;
					}
					else {
						$Y++;
					}

					$mtpersressmenrsa = Set::extract( $personnes, '/Calculdroitrsa/mtpersressmenrsa' );
					$montant += array_sum( Hash::filter( (array)$mtpersressmenrsa ) );
				}

				// c) Si 1 foyer = X personnes de plus de 25 ans + Y personnes de moins de 25 ans et X+Y>2 et Y=<2 , montant forfaitaire = 150% F + 30%F(X-2)
				if( $Y <= 2 ) {
					return ( $montant < ( ( 1.5 * $F ) + ( 0.3 * $F * ( $X - 2 ) ) ) );
				}
				// d) Si 1 foyer = X personnes de plus de 25 ans + Y personnes de moins de 25 ans et X+Y>2 et Y>2 , montant forfaitaire = 150% F + 40%F(X-2)
				else if( $Y > 2 ) {
					return ( $montant < ( ( 1.5 * $F ) + ( 0.4 * $F * ( $X - 2 ) ) ) );
				}
			}
		}


        /**
         * Fonction retournant le nombre de dossiers PCGs existants pour un foyer donné
         *
         * @param type $foyerId
         * @return type
         */
        public function vfNbDossierPCG66( $foyerId = 'Foyer.id' ) {
			return $this->Dossierpcg66->sq(
				array(
					'fields' => array(
						'COUNT(dossierspcgs66.id)'
					),
					'alias' => 'dossierspcgs66',
					'conditions' => array(
						"dossierspcgs66.foyer_id = {$foyerId}"
					)
				)
			);
        }

		/**
		 * Permet de savoir s'il existe un CJT au sein du foyer.
		 *
		 * @param string $foyerIdFied
		 * @return string
		 */
		public function sqNombreBeneficiaires( $foyerIdFied = 'Foyer.id' ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $foyerIdFied ) );
			$result = Cache::read( $cacheKey );

			if( $result === false ) {
				$query = array(
					'fields' => array(
						'COUNT(*)',
					),
					'conditions' => array(
						'Prestation.rolepers' => array( 'DEM', 'CJT' )
					),
					'joins' => array(
						$this->join( 'Personne', array( 'type' => 'INNER' )),
						$this->Personne->join( 'Prestation', array( 'type' => 'INNER' ))
					),
					'contain' => false,
					'limit' => 1
				);

				$query = array_words_replace(
					$query,
					array(
						'Foyer' => 'foyers',
						'Personne' => 'personnes',
						'Prestation' => 'prestations'
					)
				);

				$query['alias'] = 'foyers';
				$query['conditions'][] = "personnes.foyer_id = {$foyerIdFied}";
				$result = $this->sq( $query );
				Cache::write( $cacheKey, $result );
			}

			return $result;
		}
	}
?>