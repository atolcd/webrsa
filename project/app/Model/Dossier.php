<?php
	/**
	 * Code source de la classe Dossier.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe Dossier ...
	 *
	 * @package app.Model
	 */
	class Dossier extends AppModel
	{
		public $name = 'Dossier';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Conditionnable',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		/**
		 * Champ virtuel "statut" qui permet de savoir si on vient d'une demande
		 * de RSA ou de RMI.
		 *
		 * @var array
		 */
		public $virtualFields = array(
			'statut' => array(
				'type'      => 'string',
				'postgres'  => '( CASE WHEN "%s"."dtdemrsa" >= \'2009-06-01 00:00:00\' THEN \'Nouvelle demande\' ELSE \'Diminution des ressources\' END )'
			)
		);

		public $validate = array(
			'numdemrsa' => array(
				'isUnique' => array(
					'rule' => array( 'isUnique' ),
					'message' => 'Cette valeur est déjà utilisée'
				),
				'alphaNumeric' => array(
					'rule' => array( 'alphaNumeric' ),
					'message' => 'Veuillez n\'utiliser que des lettres et des chiffres'
				),
				'between' => array(
					'rule' => array( 'between', 11, 11 ),
					'message' => 'Le n° de demande est composé de 11 caractères'
				),
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'matricule' => array(
				'between' => array(
					'rule' => array( 'between', 15, 15 ),
					'message' => 'Le n° est composé de 15 caractères minimum (ajouter des zéros à la fin)',
					'allowEmpty' => true
				)
			)
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
			'typeinsrmi' => array('A', 'C', 'F', 'S'),
			'typeparte' => array('CG', 'CT', 'CCAS', 'CIAS', 'PE', 'MDPH'),
			'etatdosrsa' => array('NULL', 'Z', '0', '1', '2', '3', '4', '5', '6')
		);

		public $hasOne = array(
			'Avispcgdroitrsa' => array(
				'className' => 'Avispcgdroitrsa',
				'foreignKey' => 'dossier_id',
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
			'Detaildroitrsa' => array(
				'className' => 'Detaildroitrsa',
				'foreignKey' => 'dossier_id',
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
			'Foyer' => array(
				'className' => 'Foyer',
				'foreignKey' => 'dossier_id',
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
			'Jeton' => array(
				'className' => 'Jeton',
				'foreignKey' => 'dossier_id',
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
			'Situationdossierrsa' => array(
				'className' => 'Situationdossierrsa',
				'foreignKey' => 'dossier_id',
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
			'Dossiermodifie' => array(
				'className' => 'Dossiermodifie',
				'foreignKey' => 'dossier_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		public $hasMany = array(
			'Infofinanciere' => array(
				'className' => 'Infofinanciere',
				'foreignKey' => 'dossier_id',
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
			'Suiviinstruction' => array(
				'className' => 'Suiviinstruction',
				'foreignKey' => 'dossier_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		/**
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
			'Personne' => array(
				'className' => 'Personne',
				'joinTable' => 'derniersdossiersallocataires',
				'foreignKey' => 'dossier_id',
				'associationForeignKey' => 'personne_id',
				'unique' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'finderQuery' => null,
				'deleteQuery' => null,
				'insertQuery' => null,
				'with' => 'Dernierdossierallocataire'
			),
		);

		/**
		 * Liste des champs où la valeur du notEmpty/allowEmpty est configurable
		 *
		 * @var array
		 */
		public $configuredAllowEmptyFields = array(
			'dtdemrsa'
		);

		/**
		 * Mise en majuscule du champ numdemrsa.
		 *
		 * @param array $options
		 * @return boolean
		 */
		public function beforeSave( $options = array() ) {
			if( !empty( $this->data['Dossier']['numdemrsa'] ) ) {
				$this->data['Dossier']['numdemrsa'] = strtoupper( $this->data['Dossier']['numdemrsa'] );
			}

			return parent::beforeSave( $options );
		}

		/**
		 * Renvoit un numéro de RSA temporaire (sous la form "TMP00000000", suivant le numéro de la
		 * séquence dossiers_numdemrsatemp_seq) pour l'ajout de dossiers au CG 66.
		 *
		 * @return string
		 */
		public function generationNumdemrsaTemporaire() {
			$numSeq = $this->query( "SELECT nextval('dossiers_numdemrsatemp_seq');" );
			if( $numSeq === false ) {
				return null;
			}

			$numdemrsaTemp = sprintf( "TMP%08s",  $numSeq[0][0]['nextval'] );
			return $numdemrsaTemp;
		}

		/**
		 *
		 * @param array $params Une manière d'identifier le dossier, via la valeur
		 *	de l'une des clés suivantes: id, foyer_id, personne_id.
		 * @param array $qdPartsJetons Les parties de querydata permettant
		 *	d'obtenir des informations sur le jeton éventuel du dossier.
		 * @return array
		 * @throws NotFoundException
		 */
		public function menu( $params, $qdPartsJetons ) {
			$conditions = array();

			if( !empty( $params['id'] ) && is_numeric( $params['id'] ) ) {
				$conditions['Dossier.id'] = $params['id'];
			}
			else if( !empty( $params['foyer_id'] ) && is_numeric( $params['foyer_id'] ) ) {
				$conditions['Foyer.id'] = $params['foyer_id'];
			}
			else if( !empty( $params['personne_id'] ) && is_numeric( $params['personne_id'] ) ) {
				$conditions['Dossier.id'] = $this->Foyer->Personne->dossierId( $params['personne_id'] );
			}

			if( empty( $conditions ) || end($conditions) === null ) {
				throw new NotFoundException();
			}

			// Données du dossier RSA.
			$querydata = array(
				'fields' => array(
					'Dossier.id',
					'Dossier.matricule',
					'Dossier.fonorg',
					'Dossier.numdemrsa',
					'Foyer.id',
					$this->Foyer->sqVirtualField( 'enerreur' ),
					$this->Foyer->sqVirtualField( 'sansprestation' ),
					'Situationdossierrsa.etatdosrsa',
					'Evenement.dtliq',
					'Evenement.fg'
				),
				'joins' => array(
					$this->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->join( 'Situationdossierrsa', array( 'type' => 'LEFT' ) ),
					$this->Foyer->join('Evenement')
				),
				'conditions' => $conditions,
				'contain' => false,
				'order' => array ('Evenement.dtliq DESC')
			);

			$keys = array( 'fields', 'joins' );
			foreach( $keys as $key ) {
				$querydata[$key] = array_merge( $querydata[$key], $qdPartsJetons[$key] );
			}
			$dossier = $this->find( 'first', $querydata );

			if (empty($dossier)) {
				trigger_error("Aucun Foyer n'a été trouvé avec les conditions suivantes : ".var_export($conditions, true));
				exit;
			}

			$adresses = $this->Foyer->Adressefoyer->find(
				'all',
				array(
					'fields' => array(
						'Adressefoyer.rgadr',
						'Adressefoyer.dtemm',
						'"Adresse"."numcom" AS "Adressefoyer__codeinsee"',
					),
					'conditions' => array(
						'Adressefoyer.foyer_id' => $dossier['Foyer']['id']
					),
					'joins' => array(
						$this->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) )
					),
					'contain' => false,
					'order' => array( 'Adressefoyer.rgadr ASC', 'Adressefoyer.dtemm DESC' )
				)
			);
			// Mise en forme des adresses du foyer, ajout des champs virtuels ddemm et dfemm
			$adresses = Hash::combine( $adresses, '{n}.Adressefoyer.rgadr', '{n}.Adressefoyer' );
			$ddemm = null;
			$dfemm = null;
			foreach( $adresses as $rgadr => $adresse ) {
				$dfemm = ( is_null( $ddemm ) ? null : date( 'Y-m-d', strtotime( '-1 day', strtotime( $ddemm ) ) ) );
				$ddemm = $adresse['dtemm'];

				$adresses[$rgadr]['ddemm'] = $ddemm;
				$adresses[$rgadr]['dfemm'] = $dfemm;
			}
			$dossier = Set::merge( $dossier, array( 'Adressefoyer' => $adresses ) );

			// Les personnes du foyer
			$conditionsDerniereOrientstruct = array(
				'Orientstruct.id IN ( '.$this->Foyer->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().' )'
			);

			$query = array(
				'fields' => array(
					'Personne.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Prestation.rolepers',
					'Orientstruct.structurereferente_id',
					'Structurereferente.typestructure',
					$this->Foyer->Personne->Memo->sqNbMemosLies($this, 'Personne.id', 'nb_memos_lies' )
				),
				'conditions' => array(
					'Personne.foyer_id' => Hash::get( $dossier, 'Foyer.id' ),
				),
				'joins' => array(
					$this->Foyer->Personne->join( 'Prestation', array( 'type' => 'LEFT' ) ),
					$this->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'LEFT OUTER', 'conditions' => $conditionsDerniereOrientstruct ) ),
					$this->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
				),
				'contain' => false,
				'order' => array(
					'( CASE WHEN Prestation.rolepers = \'DEM\' THEN 0 WHEN Prestation.rolepers = \'CJT\' THEN 1 WHEN Prestation.rolepers = \'ENF\' THEN 2 ELSE 3 END ) ASC',
					'Personne.nom ASC',
					'Personne.prenom ASC'
				)
			);

			if( Configure::read( 'AncienAllocataire.enabled' ) ) {
				$sqAncienAllocataire = $this->Foyer->Personne->WebrsaPersonne->sqAncienAllocataire();
				$query['fields'][] = "( \"Prestation\".\"id\" IS NULL AND {$sqAncienAllocataire} ) AS \"Personne__ancienallocataire\"";
			}

			$personnes = $this->Foyer->Personne->find( 'all', $query );

			// Reformattage pour la vue
			$dossier['Foyer']['Personne'] = Hash::extract( $personnes, '{n}.Personne' );

			foreach( array( 'Prestation', 'Orientstruct', 'Structurereferente', 'Memo', 'Bilanparcours66' ) as $modelName ) {
				foreach( Hash::extract( $personnes, "{n}.{$modelName}" ) as $i => $datas ) {
					$dossier['Foyer']['Personne'] = Hash::insert( $dossier['Foyer']['Personne'], "{$i}.{$modelName}", $datas );
				}
			}

			if( !empty( $params['personne_id'] ) && is_numeric( $params['personne_id'] ) ) {
				$dossier['personne_id'] = $params['personne_id'];
			}

			// Nombre de dossiers PCGs
			$dossier['PCG'] = ClassRegistry::init( 'Dossierpcg66' )->find(
				'count',
				array (
					'conditions' => array(
						'Dossierpcg66.foyer_id' => $dossier['Foyer']['id'],
					),
				)
			);

			// Récupération de la mise à jour des flux
			$majFlux = ClassRegistry::init( 'Majdossier' )->find(
				'first',
				array (
					'conditions' => array(
						'Majdossier.dossier_id' => $dossier['Dossier']['id'],
					),
				)
			);

			if(!empty($majFlux)) {
				if(isset($majFlux['Majdossier']['dtmajfluxbenef']) && !empty($majFlux['Majdossier']['dtmajfluxbenef'])) {
					$strFluxBenef = date('d/m/Y', strtotime( $majFlux['Majdossier']['dtmajfluxbenef'] ) );
				} else {
					$strFluxBenef = __d('dossiers', 'Dossier::Flux::NoUpdate');
				}

				if(isset($majFlux['Majdossier']['dtmajfluxinst']) && !empty($majFlux['Majdossier']['dtmajfluxinst'])) {
					$strFluxIns = date('d/m/Y', strtotime( $majFlux['Majdossier']['dtmajfluxinst'] ) );
				} else {
					$strFluxIns = __d('dossiers', 'Dossier::Flux::NoUpdate');
				}

				if(isset($majFlux['Majdossier']['dtmajfluxfinanc']) && !empty($majFlux['Majdossier']['dtmajfluxfinanc'])) {
					$strFluxFinanc = date('d/m/Y', strtotime( $majFlux['Majdossier']['dtmajfluxfinanc'] ) );
				} else {
					$strFluxFinanc = __d('dossiers', 'Dossier::Flux::NoUpdate');
				}
			} else {
				$strFluxBenef = __d('dossiers', 'Dossier::Flux::NoUpdate');
				$strFluxIns = __d('dossiers', 'Dossier::Flux::NoUpdate');
				$strFluxFinanc = __d('dossiers', 'Dossier::Flux::NoUpdate');
			}
			$dossier['MAJFlux']['Benef'] = __d('dossiers', 'Dossier::Flux::MAJBenef') . $strFluxBenef . '<br><br>';
			$dossier['MAJFlux']['Inst'] = __d('dossiers', 'Dossier::Flux::MAJInst') . $strFluxIns . '<br><br>';
			$dossier['MAJFlux']['Financ'] = __d('dossiers', 'Dossier::Flux::MAJFinanc') . $strFluxFinanc . '<br><br>';

			return $dossier;
		}

		/**
		 * @return array
		 */
		public function enums() {
			$cacheKey = $this->useDbConfig.'_'.__CLASS__.'_enums_'.$this->alias;

			// Dans le cache "live" ?
			if( false === isset( $this->_appModelCache[$cacheKey] ) ) {
				$this->_appModelCache[$cacheKey] = Cache::read( $cacheKey );

				// Dans le cache CakePHP ?
				if( false === $this->_appModelCache[$cacheKey] ) {
					$this->_appModelCache[$cacheKey] = parent::enums();

					// FIXME: seulement pour le 93 ?
					// @see Tableausuivipdv93
					$this->_appModelCache[$cacheKey][$this->alias]['anciennete_dispositif'] = array(
						'0_0' => 'Moins de 1 an',
						'1_2' => 'De 1 an à moins de 3 ans',
						'3_5' => 'De 3 ans à moins de 6 ans',
						'6_8' => 'De 6 ans à moins de 9 ans',
						'9_999' => 'Plus de 9 ans',
					);

					$this->_cacheOptionsNumorg($cacheKey);

					Cache::write( $cacheKey, $this->_appModelCache[$cacheKey] );
				}
			}

			return $this->_appModelCache[$cacheKey];
		}

		/**
		 * On enregistre la liste des traductions connues, mais on ne met pas en inList,
		 * sinon on aura des soucis à l'enregistrement d'un nouveau numorg
		 */
		protected function _cacheOptionsNumorg($cacheKey) {
			$domain = Inflector::underscore( $this->alias );
			$this->_appModelCache[$cacheKey][$this->alias]['numorg'] = array();
			for ($i = 1 ; $i <= 999 + 9*2 ; $i++) {
				// Cas corse
				if ($i > 999) {
					$departement = $i - 999 > 9 ? '2B' : '2A';
					$numorg = $departement . ($i - 999 > 9 ? $i - 999 - 9 : $i - 999); // 2A[1-9] | 2B[1-9]
				} else {
					$numorg = sprintf('%03d', $i);
				}

				$label = __d( $domain, "ENUM::NUMORG::{$numorg}" );
				if ($label !== "ENUM::NUMORG::{$numorg}") {
					$this->_appModelCache[$cacheKey][$this->alias]['numorg'][(string)$numorg] = $label;
				}
			}
		}

		/**
		 * Enregistrement de la modification de l'état d'un dossier
		 * @param int $dossier_id
		 * @param array $dataToSave
		 * @param string $motif
		 * @return bool
		 */
		public function saveModifEtat($dossier_id, $dataToSave, $motif) {
			$data = array();
			// Préparation à l'enregistrement sur Situationdossierrsa
			$data['Situationdossierrsa'] = $dataToSave['Situationdossierrsa'];
			$data['Situationdossierrsa']['dossier_id'] = $dossier_id;

			// Récupération de l'état actuel du dossier & de l'id
			$etatActuel = $this->Situationdossierrsa->find('first', array(
				'fields' => array('id', 'etatdosrsa'),
				'recursive' => -1,
				'conditions' => array('dossier_id' => $dossier_id)
			));
			$data['Situationdossierrsa']['id'] = $etatActuel['Situationdossierrsa']['id'];

			// Préparation à l'enregistrement sur Historiquedroit
			$dataHisto = array();

			// Initialisation du user à enregistrer
			$User = ClassRegistry::init( 'User' );
			$user = $User->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => AuthComponent::user('id')
					),
					'contain' => false
				)
			);

			// Récupération des personnes
			$foyer = $this->Foyer->find('first', array(
				'fields' => 'id',
				'recursive' => -1,
				'conditions' => array('dossier_id' => $dossier_id)
			));
			$personnes = $this->Foyer->Personne->find('all', array(
				'fields' => 'Personne.id',
				'recursive' => -1,
				'conditions' => array(
					'Personne.foyer_id' => $foyer['Foyer']['id'],
					'Prestation.natprest' => 'RSA',
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
				),
				'joins' => array(
					$this->Foyer->Personne->join( 'Prestation' )
				)
			));

			foreach($personnes as $key => $personne) {
				$dataHisto[$key] = array(
					'personne_id' => $personne['Personne']['id'],
					'etatdosrsa' => $etatActuel['Situationdossierrsa']['etatdosrsa'],
					'nom' => $user['User']['nom'],
					'prenom' => $user['User']['prenom'],
					'motif' => $motif
				);
				// Récupération du toppersdrodevorsa
				$droitrsa = $this->Foyer->Personne->Calculdroitrsa->find('first', array(
					'fields' => 'toppersdrodevorsa',
					'recursive' => -1,
					'conditions' => array('personne_id' => $personne['Personne']['id'])
				));
				$dataHisto[$key]['toppersdrodevorsa'] = $droitrsa['Calculdroitrsa']['toppersdrodevorsa'];
			}

			// Enregistrement dans Situationdossierrsa & dans Historiquedroit
			$Historiquedroit = ClassRegistry::init('Historiquedroit');
			$this->begin();
			if( $this->Situationdossierrsa->save( $data, array( 'validate' => 'first', 'atomic' => true ) ) &&
				$Historiquedroit->saveMany($dataHisto, array( 'validate' => 'first', 'atomic' => true ))
			) {
				$this->commit();
				return true;
			} else {
				$this->rollback();
				return false;
			}
		}
	}
?>