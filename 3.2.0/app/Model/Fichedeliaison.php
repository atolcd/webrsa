<?php
	/**
	 * Code source de la classe Fichedeliaison.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Fichedeliaison ...
	 *
	 * @package app.Model
	 */
	class Fichedeliaison extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Fichedeliaison';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Has one".
		 *
		 * @var array
		 */
		public $hasOne = array(
			'Avistechniquefiche' => array(
				'className' => 'Avisfichedeliaison',
				'foreignKey' => 'fichedeliaison_id',
				'conditions' => array('Avistechniquefiche.etape' => 'avis'),
				'fields' => null,
				'order' => null,
				'dependent' => true
			),
			'Validationfiche' => array(
				'className' => 'Avisfichedeliaison',
				'foreignKey' => 'fichedeliaison_id',
				'conditions' => array('Validationfiche.etape' => 'validation'),
				'fields' => null,
				'order' => null,
				'dependent' => true
			),
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Foyer' => array(
				'className' => 'Foyer',
				'foreignKey' => 'foyer_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Motiffichedeliaison' => array(
				'className' => 'Motiffichedeliaison',
				'foreignKey' => 'motiffichedeliaison_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Expediteur' => array(
				'className' => 'Service66',
				'foreignKey' => 'expediteur_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Destinataire' => array(
				'className' => 'Service66',
				'foreignKey' => 'destinataire_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Primoanalyse' => array(
				'className' => 'Primoanalyse',
				'foreignKey' => 'fichedeliaison_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
			'Destinataireemail' => array(
				'className' => 'Destinataireemail',
				'foreignKey' => 'fichedeliaison_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => 'fk_value',
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele' => 'Fichedeliaison'
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
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
			'Personne' => array(
				'className' => 'Personne',
				'joinTable' => 'fichedeliaisons_personnes',
				'foreignKey' => 'fichedeliaison_id',
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
				'with' => 'FichedeliaisonPersonne'
			),
		);

		/**
		 * Règles de validation.
		 *
		 * @var array
		 */
		public $validate = array(
			'direction' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array(NOT_BLANK_RULE_NAME)
				)
			)
		);

		/**
		 * Permet d'obtenir la requête nécéssaire pour l'index
		 *
		 * @param integer $foyer_id
		 */
		public function getIndexQuery($foyer_id) {
			$query = array(
				'fields' => array_merge(
					$this->fields(),
					array(
						'Motiffichedeliaison.name',
						'Expediteur.name',
						'Destinataire.name',
						'Primoanalyse.etat',
						$this->Fichiermodule->sqNbFichiersLies($this, 'nombre'),
					)
				),
				'joins' => array(
					$this->join('Motiffichedeliaison'),
					$this->join('Expediteur'),
					$this->join('Destinataire'),
					$this->join('Primoanalyse', array(
						'conditions' => array(
							'Primoanalyse.id IN ('
							. 'SELECT a.id '
							. 'FROM primoanalyses AS a '
							. 'WHERE a.fichedeliaison_id = "Fichedeliaison"."id" '
							. 'ORDER BY a.created DESC '
							. 'LIMIT 1)'
						)
					)),
				),
				'contain' => array(
					'Personne'
				),
				'conditions' => array(
					'Fichedeliaison.foyer_id' => $foyer_id
				),
				'order' => array(
					'Fichedeliaison.created' => 'DESC'
				)
			);

			return $query;
		}

		/**
		 * Retourne les positions et les conditions CakePHP/SQL dans l'ordre dans
		 * lequel elles doivent être traitées pour récupérer la position actuelle.
		 *
		 * @return array
		 */
		protected function _getConditionsPositions() {
			$return = array(
				'traite' => array(
					array(
						'Primoanalyse.etat' => 'traite',
					)
				),
				'decisionvalid' => array(
					array(
						'Validationfichedeliaison.choix' => 1,
					)
				),
				'decisionnonvalid' => array(
					array(
						'Validationfichedeliaison.choix' => 0,
					)
				),
				'attval' => array(
					array(
						'Avisfichedeliaison.etape' => 'avis',
					)
				),
			);

			return $return;
		}

		/**
		 * Retourne les conditions permettant de cibler selon une cetaine position.
		 *
		 * @param string $etat
		 * @return array
		 */
		public function getConditionsEtat($etat) {
			$conditions = array();

			foreach( $this->_getConditionsPositions() as $keyPosition => $conditionsPosition ) {
				if ( $keyPosition === $etat ) {
					$conditions[] = array( $conditionsPosition );
					break;
				}
			}

			return $conditions;
		}

		/**
		 * Retourne une CASE (PostgreSQL) pemettant de connaître la position
		 *
		 * @return string
		 */
		public function getCasePositionFichedeliaison() {
			$return = '';
			$Dbo = $this->getDataSource();

			foreach( array_keys( $this->_getConditionsPositions() ) as $etat ) {
				$conditions = $this->getConditionsEtat( $etat );
				$conditions = $Dbo->conditions( $conditions, true, false, $this );
				$return .= "WHEN {$conditions} THEN '{$etat}' ";
			}

			// Position par defaut : En attente d'envoi de l'e-mail pour l'employeur
			$return = "( CASE {$return} ELSE 'attavistech' END )";

			return $return;
		}

		/**
		 * Mise à jour des positions suivant des conditions données.
		 *
		 * @param array $conditions
		 * @return boolean
		 */
		public function updatePositionsByConditions(array $conditions) {
			// Vérification existance
			$occurences = $this->find('all', array(
				'fields' => 'Fichedeliaison.id',
				'conditions' => $conditions,
			));

			$case = $this->getCasePositionFichedeliaison();
			$Dbo = $this->getDataSource();
			$etats = array();

			foreach ((array)Hash::extract($occurences, '{n}.Fichedeliaison.id') as $fichedeliaison_id) {
				$sql = '
					UPDATE fichedeliaisons AS "Fichedeliaison"
					SET "etat" = '.$case.'

					FROM fichedeliaisons AS a

					LEFT JOIN primoanalyses AS "Primoanalyse" ON (
						"Primoanalyse"."fichedeliaison_id" = a.id
						AND "Primoanalyse"."id" IN (
							SELECT b.id FROM primoanalyses AS b
							WHERE b.fichedeliaison_id = a.id
							ORDER BY b.created DESC
							LIMIT 1
						)
					)

					LEFT JOIN avisfichedeliaisons AS "Avisfichedeliaison" ON (
						"Avisfichedeliaison"."fichedeliaison_id" = a.id
						AND "Avisfichedeliaison"."etape" = \'avis\'
					)

					LEFT JOIN avisfichedeliaisons AS "Validationfichedeliaison" ON (
						"Validationfichedeliaison"."fichedeliaison_id" = a.id
						AND "Validationfichedeliaison"."etape" = \'validation\'
					)

					WHERE "Fichedeliaison"."id" = '.$fichedeliaison_id.'
					AND a.id = "Fichedeliaison"."id"
					RETURNING "Fichedeliaison"."etat";'
				;

				$etats[$fichedeliaison_id] = Hash::get($Dbo->query($sql), '0.0.etat');

				if ($etats[$fichedeliaison_id] === false) {
					 return false;
				}
			}

			return $etats;
		}

		/**
		 * Mise à jour des positions qui devraient se trouver dans une position donnée.
		 *
		 * @param string $etat
		 * @return boolean
		 */
		public function updatePositionsByPosition( $etat ) {
			$conditions = $this->getConditionsEtat( $etat );

			$query = array(
				'fields' => array( "{$this->alias}.{$this->primaryKey}" ),
				'conditions' => $conditions,
				'joins' => array( $this->join( 'Fichedeliaison' ) )
			);
			$sample = $this->find( 'first', $query );

			return (
				empty( $sample )
				|| $this->updateAllUnBound(
					array( "{$this->alias}.etat" => "'{$etat}'" ),
					$conditions
				)
			);
		}

		/**
		 * Permet de mettre à jour les positions grâce à la clé primaire.
		 *
		 * @param integer $id
		 * @return boolean
		 */
		public function updatePositionsById( $id ) {
			$return = $this->updatePositionsByConditions(
				array( "Fichedeliaison.id" => $id )
			);

			return $return;
		}

		/**
		 * Renvoi le foyer_id d'un enregistrement
		 *
		 * @param integer $fichedeliaison_id
		 * @return integer
		 */
		public function foyerId($fichedeliaison_id) {
			$query = array(
				'fields' => 'Fichedeliaison.foyer_id',
				'conditions' => array('Fichedeliaison.id' => $fichedeliaison_id)
			);

			return Hash::get($this->find('first', $query), 'Fichedeliaison.foyer_id');
		}
	}
?>