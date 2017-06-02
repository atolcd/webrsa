<?php
	/**
	 * Code source de la classe Primoanalyse.
	 *
	 * @package app.Model
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Model.php.
	 */

	/**
	 * La classe Primoanalyse ...
	 *
	 * @package app.Model
	 */
	class Primoanalyse extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Primoanalyse';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
		);

		/**
		 * Associations "Has one".
		 *
		 * @var array
		 */
		public $hasOne = array(
			'Avistechniqueprimo' => array(
				'className' => 'Avisprimoanalyse',
				'foreignKey' => 'primoanalyse_id',
				'conditions' => array('Avistechniqueprimo.etape' => 'avis'),
				'fields' => null,
				'order' => null,
				'dependent' => true
			),
			'Validationprimo' => array(
				'className' => 'Avisprimoanalyse',
				'foreignKey' => 'primoanalyse_id',
				'conditions' => array('Validationprimo.etape' => 'validation'),
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
			'Fichedeliaison' => array(
				'className' => 'Fichedeliaison',
				'foreignKey' => 'fichedeliaison_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Propositionprimo' => array(
				'className' => 'Propositionprimo',
				'foreignKey' => 'propositionprimo_id',
				'conditions' => null,
				'type' => 'LEFT',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Gestionnaire' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => array('Gestionnaire.isgestionnaire' => 'O'),
				'type' => 'LEFT',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Dossierpcg66' => array(
				'className' => 'Dossierpcg66',
				'foreignKey' => 'dossierpcg66_id',
				'conditions' => null,
				'type' => 'LEFT',
				'fields' => null,
				'order' => null,
				'counterCache' => null,
				'dependent' => false
			),
		);

		/**
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
			'Logicielprimo' => array(
				'className' => 'Logicielprimo',
				'joinTable' => 'logicielprimos_primoanalyses',
				'foreignKey' => 'primoanalyse_id',
				'associationForeignKey' => 'logicielprimo_id',
				'unique' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'finderQuery' => null,
				'deleteQuery' => null,
				'insertQuery' => null,
				'with' => 'LogicielprimoPrimoanalyse'
			),
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
					$this->Fichedeliaison->fields(),
					array(
						'Motiffichedeliaison.name',
						'Expediteur.name',
						'Destinataire.name',
					)
				),
				'joins' => array(
					$this->join('Fichedeliaison'),
					$this->Fichedeliaison->join('Motiffichedeliaison'),
					$this->Fichedeliaison->join('Expediteur'),
					$this->Fichedeliaison->join('Destinataire'),
				),
				'conditions' => array(
					'Fichedeliaison.foyer_id' => $foyer_id
				),
				'order' => array(
					'Primoanalyse.created' => 'DESC'
				)
			);
			
			return $query;
		}
		
		/**
		 * Permet d'obtenir toutes les informations sur une Fiche de liaison et sa primoanalyse
		 * 
		 * @param integer $primoanalyse_id
		 * @return array
		 */
		public function getEditQuery($primoanalyse_id) {
			$query = array(
				'fields' => array_merge(
					$this->fields(),
					$this->Avistechniqueprimo->fields(),
					$this->Validationprimo->fields(),
					$this->Fichedeliaison->fields(),
					$this->Fichedeliaison->Avistechniquefiche->fields(),
					$this->Fichedeliaison->Validationfiche->fields(),
					array(
						'Motiffichedeliaison.name',
						'Expediteur.name',
						'Destinataire.name',
					)
				),
				'joins' => array(
					$this->join('Avistechniqueprimo'),
					$this->join('Validationprimo'),
					$this->join('Fichedeliaison'),
					$this->Fichedeliaison->join('Motiffichedeliaison'),
					$this->Fichedeliaison->join('Expediteur'),
					$this->Fichedeliaison->join('Destinataire'),
					$this->Fichedeliaison->join('Avistechniquefiche'),
					$this->Fichedeliaison->join('Validationfiche'),
				),
				'contain' => array(
					'Logicielprimo'
				),
				'conditions' => array(
					'Primoanalyse.id' => $primoanalyse_id
				),
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
					'OR' => array(
						'Validationprimo.choix' => 1,
						'Primoanalyse.actionafaire' => 1,
					)
				),
				'decisionnonvalid' => array(
					array(
						'Validationprimo.choix' => 0,
					)
				),
				'vu' => array(
					array(
						'Primoanalyse.actionvu' => 1,
					)
				),
				'attval' => array(
					array(
						'Avistechniqueprimo.etape' => 'avis',
					)
				),
				'attavistech' => array(
					array(
						'Primoanalyse.propositionprimo_id IS NOT NULL',
					)
				),
				'attinstr' => array(
					array(
						'Primoanalyse.user_id IS NOT NULL',
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
		public function getCasePositionPrimoanalyse() {
			$return = '';
			$Dbo = $this->getDataSource();

			foreach( array_keys( $this->_getConditionsPositions() ) as $etat ) {
				$conditions = $this->getConditionsEtat( $etat );
				$conditions = $Dbo->conditions( $conditions, true, false, $this );
				$return .= "WHEN {$conditions} THEN '{$etat}' ";
			}

			// Position par defaut : En attente d'envoi de l'e-mail pour l'employeur
			$return = "( CASE {$return} ELSE 'attaffect' END )";

			return $return;
		}

		/**
		 * Mise à jour des positions suivant des conditions données.
		 *
		 * @param array $conditions
		 * @return array|boolean
		 */
		public function updatePositionsByConditions(array $conditions) {
			// Vérification existance
			$occurences = $this->find('all', array(
				'fields' => 'Primoanalyse.id',
				'conditions' => $conditions,
			));
			
			$case = $this->getCasePositionPrimoanalyse();
			$Dbo = $this->getDataSource();
			$etats = array();
			
			foreach ((array)Hash::extract($occurences, '{n}.Primoanalyse.id') as $primoanalyse_id) {
				$sql = '
					UPDATE primoanalyses AS "Primoanalyse"
					SET "etat" = '.$case.'

					FROM primoanalyses AS a

					LEFT JOIN avisprimoanalyses AS "Avistechniqueprimo" ON (
						"Avistechniqueprimo"."primoanalyse_id" = a.id
						AND "Avistechniqueprimo"."etape" = \'avis\'
					)

					LEFT JOIN avisprimoanalyses AS "Validationprimo" ON (
						"Validationprimo"."primoanalyse_id" = a.id
						AND "Validationprimo"."etape" = \'validation\'
					)

					WHERE "Primoanalyse"."id" = '.$primoanalyse_id.'
					AND a.id = "Primoanalyse"."id"
					RETURNING "Primoanalyse"."etat";'
				;
				
				$etats[$primoanalyse_id] = Hash::get($Dbo->query($sql), '0.0.etat');
				
				if ($etats[$primoanalyse_id] === false) {
					 return false;
				}
			}
			
			return $etats;
		}

		/**
		 * Mise à jour des positions qui devraient se trouver dans une
		 * position donnée.
		 *
		 * @param string $etat
		 * @return boolean
		 */
		public function updatePositionsByPosition( $etat ) {
			$conditions = $this->getConditionsEtat( $etat );

			$query = array( 
				'fields' => array( "{$this->alias}.{$this->primaryKey}" ), 
				'conditions' => $conditions,
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
		 * @return array|boolean
		 */
		public function updatePositionsById( $id ) {
			$return = $this->updatePositionsByConditions(
				array( "Primoanalyse.id" => $id )
			);

			return $return;
		}
	}
?>