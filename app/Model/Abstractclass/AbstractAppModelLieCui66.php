<?php
	/**
	 * Code source de la classe AbstractAppModelLieCui66.
	 *
	 * @package app.Model
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe AbstractAppModelLieCui66 ...
	 *
	 * @package app.Model
	 */
	abstract class AbstractAppModelLieCui66 extends AppModel
	{

		/**
		 * Order des find par défaut
		 * @var type
		 */
		public $order =  array(
			'created' => 'DESC'
		);

		/**
		 * Possède des clefs étrangères vers d'autres models
		 * @var array
		 */
        public $belongsTo = array(
			'Cui66' => array(
				'className' => 'Cui66',
				'foreignKey' => 'cui66_id',
				'dependent' => true,
			),
        );

		/**
		 * Ces models possèdent une clef étrangère vers ce model
		 * @var array
		 */
		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(), // Défini dans le constructeur
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
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Fichiermodulelie',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		/**
		* Chemin relatif pour les modèles de documents .odt utilisés lors des
		* impressions. Utiliser %s pour remplacer par l'alias.
		*/
		public $modelesOdt = array(
			'default' => 'CUI/%s/impression.odt',
		);

		/**
		 * Constructeur
		 * Défini une relation hasMany avec Fichiermodule en fonction de l'alias de l'enfant
		 * Défini un order par défaut avec l'alias de l'enfant
		 *
		 * @param type $id
		 * @param type $table
		 * @param type $ds
		 */
		public function __construct($id = false, $table = null, $ds = null) {
			parent::__construct($id, $table, $ds);

			$this->order = array( $this->alias . '.created' => 'DESC' );

			$this->hasMany['Fichiermodule']['conditions'] = array(
				'Fichiermodule.modele = \'' . $this->alias . '\'',
				'Fichiermodule.fk_value = {$__cakeID__$}'
			);
		}

		/**
		 * Récupère les donnés par defaut dans le cas d'un ajout, ou récupère les données stocké en base dans le cas d'une modification
		 *
		 * @param integer $cui66_id
		 * @param integer $id
		 * @return array
		 */
		public function prepareAddEditFormData( $cui66_id, $id = null ) {
			// Ajout
			if( empty( $id ) ) {
				$result = array(
					$this->alias => array(
						'cui66_id' => $cui66_id,
					)
				);
			}
			// Mise à jour
			else {
				$query = $this->queryView( $id, false );
				$result = $this->find( 'first', $query );
			}

			if ( empty($result) ){
				throw new HttpException(404, "HTTP/1.1 404 Not Found");
			}

			return $result;
		}

		/**
		 * FIXME: doc
		 *
		 * @param type $cui66_id
		 * @return string
		 */
		public function getCompleteDataImpressionQuery( $cui66_id ) {
			$query = array(
				'fields' => $this->fields(),
				'conditions' => array(
					$this->alias . '.cui66_id' => $cui66_id
				)
			);

			return $query;
		}

		/**
		 * Sauvegarde du formulaire
		 *
		 * @param array $data
		 * @return boolean
		 */
		public function saveAddEditFormData( array $data, $user_id = null ) {
			$data[$this->alias]['user_id'] = $user_id;

			$this->create($data);
			$success = $this->save( null, array( 'atomic' => false ) );

			return $success;
		}

		/**
		 * Query utilisé pour la visualisation
		 *
		 * @param integer $id
		 * @return array
		 */
		public function queryView( $id ) {
			$query = array(
				'fields' => array_merge(
					$this->fields()
				),
				'conditions' => array(
					$this->alias . '.id' => $id,
				)
			);

			return $query;
		}
	}
?>