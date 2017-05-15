<?php
	/**
	 * Code source de la classe Referent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	define( 'CHAMP_FACULTATIF_REFERENT', Configure::read( 'Cg.departement' ) == 58 );

	/**
	 * La classe Referent s'occupe de la gestion des référents.
	 *
	 * @package app.Model
	 */
	class Referent extends AppModel
	{
		public $name = 'Referent';

		public $displayField = 'nom_complet';

		public $actsAs = array(
			'Autovalidate2',
			'Enumerable' => array(
				'fields' => array(
					'actif' => array( 'type' => 'no', 'domain' => 'default' )
				)
			),
			'Formattable' => array(
				'phone' => array( 'numero_poste' )
			),
			'ValidateTranslate',
			'Validation.ExtraValidationRules',
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Option', 'WebrsaReferent'
		);

		public $order = array( 'Referent.nom ASC', 'Referent.prenom ASC' );

		public $virtualFields = array(
			'nom_complet' => array(
				'type'      => 'string',
				'postgres'  => '( "%s"."qual" || \' \' || "%s"."nom" || \' \' || "%s"."prenom" )'
			),
			'nom_complet_court' => array(
				'type'		=> 'string',
				'postgres'	=> '( "%s"."nom" || \' \' || "%s"."prenom" )'
			)
		);

		public $validate = array(
			'numero_poste' => array(
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				)
			),
			'qual' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire',
					'allowEmpty' => CHAMP_FACULTATIF_REFERENT
				)
			),
			'nom' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'prenom' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'fonction' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'email' => array(
				'email' => array(
					'rule' => 'email',
					'message' => 'Veuillez entrer une adresse email valide',
					'allowEmpty' => true
				)
			),
			'structurereferente_id' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
		);

		public $belongsTo = array(
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasOne = array(
			'Dernierreferent' => array(
				'className' => 'Dernierreferent',
				'foreignKey' => 'referent_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		public $hasMany = array(
			'Actioncandidat' => array(
				'className' => 'Actioncandidat',
				'foreignKey' => 'referent_id',
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
			'ActioncandidatPersonne' => array(
				'className' => 'ActioncandidatPersonne',
				'foreignKey' => 'referent_id',
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
			'Bilanparcours66' => array(
				'className' => 'Bilanparcours66',
				'foreignKey' => 'referent_id',
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
			'Entretien' => array(
				'className' => 'Entretien',
				'foreignKey' => 'referent_id',
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
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'referent_id',
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
			'Apre' => array(
				'className' => 'Apre',
				'foreignKey' => 'referent_id',
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
			'Propopdo' => array(
				'className' => 'Propopdo',
				'foreignKey' => 'referent_id',
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
			'Rendezvous' => array(
				'className' => 'Rendezvous',
				'foreignKey' => 'referent_id',
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
			'Orientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'referent_id',
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
			'PersonneReferent' => array(
				'className' => 'PersonneReferent',
				'foreignKey' => 'referent_id',
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
			'Decisiondefautinsertionep66' => array(
				'className' => 'Decisiondefautinsertionep66',
				'foreignKey' => 'referent_id',
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
			'Decisionsaisinebilanparcoursep66' => array(
				'className' => 'Decisionsaisinebilanparcoursep66',
				'foreignKey' => 'referent_id',
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
			'Decisionpropoorientsocialecov58' => array(
				'className' => 'Decisionpropoorientsocialecov58',
				'foreignKey' => 'referent_id',
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
			'Ficheprescription93' => array(
				'className' => 'Ficheprescription93',
				'foreignKey' => 'referent_id',
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
			'Decisionnonorientationprocov58' => array(
				'className' => 'Decisionnonorientationprocov58',
				'foreignKey' => 'referent_id',
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
			'Regressionorientationcov58' => array(
				'className' => 'Regressionorientationcov58',
				'foreignKey' => 'referent_id',
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
			'Decisionregressionorientationcov58' => array(
				'className' => 'Decisionregressionorientationcov58',
				'foreignKey' => 'referent_id',
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
			'Tableausuivipdv93' => array(
				'className' => 'Tableausuivipdv93',
				'foreignKey' => 'referent_id',
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
		);

		public $hasAndBelongsToMany = array(
			'Personne' => array(
				'className' => 'Personne',
				'joinTable' => 'personnes_referents',
				'foreignKey' => 'referent_id',
				'associationForeignKey' => 'personne_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'PersonneReferent'
			)
		);

		/**
		 * Après la sauvegarde, on met à jour la table Dernierreferent
		 *
		 * @param boolean $created True if this save created a new record
		 */
		public function afterSave($created) {
			parent::afterSave($created);

			if ((integer)Configure::read('Cg.departement') === 66) {
				$id = Hash::get($this->data, 'Referent.id');
				$prevreferent_id = Hash::get($this->data, 'Dernierreferent.prevreferent_id');
				$prevdatas = $this->Dernierreferent->find('first',
					array('conditions' => array('Dernierreferent.referent_id' => $prevreferent_id))
				);
				$dernierreferent_id = Hash::get($prevdatas, 'Dernierreferent.dernierreferent_id');

				$toSave = Hash::get($this->data, 'Dernierreferent');
				$toSave['referent_id'] = $id;
				$toSave['dernierreferent_id'] = $created || $dernierreferent_id === null ? $id : $dernierreferent_id;

				if (!$created) {
					// Si un lien n'existait pas dans l'edition, on traite comme un nouvel enregistrement
					$datas = $this->Dernierreferent->find('first',
						array(
							'fields' => '(referent_id != dernierreferent_id) AS "Dernierreferent__is_diff"',
							'conditions' => array('Dernierreferent.referent_id' => $id)
						)
					);
					$create = empty($datas) || Hash::get($datas, 'Dernierreferent.is_diff') === false;
				}

				if ($created || $create) {
					$this->Dernierreferent->create($toSave);
					$this->Dernierreferent->save();
				}

				if ($toSave['dernierreferent_id'] !== $dernierreferent_id && $dernierreferent_id !== null) {
					$this->Dernierreferent->updateAllUnbound(
						array('Dernierreferent.dernierreferent_id' => $toSave['dernierreferent_id']),
						array('Dernierreferent.dernierreferent_id' => $dernierreferent_id)
					);
				}
			}
		}

		/**
		 * Surcharge de la méthode enums pour ajouter la qualité.
		 *
		 * @return array
		 */
		public function enums() {
			$results = parent::enums();

			$results[$this->alias]['qual'] = $this->Option->qual();

			return $results;
		}
	}
?>