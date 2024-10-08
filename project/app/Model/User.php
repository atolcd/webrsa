<?php
	/**
	 * Fichier source de la classe User.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'FrValidation', 'Validation' );

	/**
	 * La classe User ...
	 *
	 * @package app.Model
	 */
	class User extends AppModel
	{
		public $name = 'User';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'username';

		public $actsAs = array(
			'Acl' => array('type' => 'requester'),
			'Validation2.Validation2Formattable' => array(
				'Validation2.Validation2DefaultFormatter' => array(
					'stripNotAlnum' => '/^numtel$/'
				)
			),
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Option', 'WebrsaUser' );

		public $virtualFields = array(
			'nom_complet' => array(
				'type'      => 'string',
				'postgres'  => '( "%s"."nom" || \' \' || "%s"."prenom" )'
			),
		);

		public $validate = array(
			'passwd' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				),
				'passwordStrength' => array(
					'rule' => array( 'passwordStrength' )
				),
			),
			'current_password' => array(
				'checkCurrentPassword' => array(
					'rule' => array( 'checkCurrentPassword' )
				)
			),
			'new_password' => array(
				'passwordStrength' => array(
					'rule' => array( 'passwordStrength' )
				),
				'checkIdenticalValues' => array(
					'rule' => array( 'checkIdenticalValues', 'new_password_confirmation' )
				)
			),
			'new_password_confirmation' => array(
				'passwordStrength' => array(
					'rule' => array( 'passwordStrength' )
				),
				'checkIdenticalValues' => array(
					'rule' => array( 'checkIdenticalValues', 'new_password' )
				),
			),
			'group_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'nom' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'prenom' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'date_deb_hab' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'date_fin_hab' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'numtel' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'isgestionnaire' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'sensibilite' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'communautesr_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'type', true, array( 'externe_cpdvcom' ) ),
					'message' => 'Champ obligatoire'
				)
			),
			'structurereferente_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'type', true, array( 'externe_cpdv', 'externe_secretaire' ) ),
					'message' => 'Champ obligatoire'
				)
			),
			'referent_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'type', true, array( 'externe_ci' ) ),
					'message' => 'Champ obligatoire'
				)
			),
			'email' => array(
				'email' => array(
					'rule' => array( 'email' ),
					'message' => 'Veuillez entrer une adresse mail valide',
					'allowEmpty' => true
				)
			)
		);

		public $belongsTo = array(
			'Group' => array(
				'className' => 'Group',
				'foreignKey' => 'group_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Serviceinstructeur' => array(
				'className' => 'Serviceinstructeur',
				'foreignKey' => 'serviceinstructeur_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Referent' => array(
				'className' => 'Referent',
				'foreignKey' => 'referent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
            'Poledossierpcg66' => array(
				'className' => 'Poledossierpcg66',
				'foreignKey' => 'poledossierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
            'Communautesr' => array(
				'className' => 'Communautesr',
				'foreignKey' => 'communautesr_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
            'Service66' => array(
				'className' => 'Service66',
				'foreignKey' => 'service66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'ReferentAccueil' => array(
				'className' => 'Referent',
				'foreignKey' => 'accueil_referent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Categorieutilisateur' => array(
				'className' => 'Categorieutilisateur',
				'foreignKey' => 'categorieutilisateur_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		public $hasMany = array(
			'Actionroleresultuser' => array(
				'className' => 'Actionroleresultuser',
				'foreignKey' => 'user_id',
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
			'Annulateurcer93' => array(
				'className' => 'Cer93',
				'foreignKey' => 'annulateur_id',
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
			'Connection' => array(
				'className' => 'Connection',
				'foreignKey' => 'user_id',
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
				'foreignKey' => 'user_id',
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
			'Jetonfonction' => array(
				'className' => 'Jetonfonction',
				'foreignKey' => 'user_id',
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
				'foreignKey' => 'user_id',
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
			'Propoorientationcov58' => array(
				'className' => 'Propoorientationcov58',
				'foreignKey' => 'user_id',
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
			'Propoorientsocialecov58' => array(
				'className' => 'Propoorientsocialecov58',
				'foreignKey' => 'user_id',
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
				'foreignKey' => 'user_id',
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
			'Passagecommissionep' => array(
				'className' => 'Passagecommissionep',
				'foreignKey' => 'user_id',
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
			'Relancenonrespectsanctionep93' => array(
				'className' => 'Relancenonrespectsanctionep93',
				'foreignKey' => 'user_id',
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
			'Reorientationep93' => array(
				'className' => 'Reorientationep93',
				'foreignKey' => 'user_id',
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
			'Decisionnonrespectsanctionep93' => array(
				'className' => 'Decisionnonrespectsanctionep93',
				'foreignKey' => 'user_id',
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
			'Decisionreorientationep93' => array(
				'className' => 'Reorientationep93',
				'foreignKey' => 'user_id',
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
			'Cui' => array(
				'className' => 'Cui',
				'foreignKey' => 'user_id',
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
			'Propodecisioncui66' => array(
				'className' => 'Propodecisioncui66',
				'foreignKey' => 'user_id',
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
				'foreignKey' => 'user_id',
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
			'Histochoixcer93' => array(
				'className' => 'Histochoixcer93',
				'foreignKey' => 'user_id',
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
			'Cer93' => array(
				'className' => 'Cer93',
				'foreignKey' => 'user_id',
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
			'Decisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'foreignKey' => 'user_id',
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
			'Avistechniquedecisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'foreignKey' => 'useravistechnique_id',
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
			'Propositiondecisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'foreignKey' => 'userproposition_id',
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
			'Nonorientationprocov58' => array(
				'className' => 'Nonorientationprocov58',
				'foreignKey' => 'user_id',
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
			'Regressionorientationcov58' => array(
				'className' => 'Regressionorientationcov58',
				'foreignKey' => 'user_id',
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
			'Tableausuivipdv93' => array(
				'className' => 'Tableausuivipdv93',
				'foreignKey' => 'user_id',
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
			'Historiqueetat' => array(
				'className' => 'Historiqueetat',
				'foreignKey' => 'user_id',
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
				'foreignKey' => 'user_id',
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
			'Foyerpiecejointe' => array(
				'className' => 'Foyerpiecejointe',
				'foreignKey' => 'user_id',
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
			'Email' => array(
				'className' => 'Email',
				'foreignKey' => 'foyer_id',
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

		public $hasAndBelongsToMany = array(
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'joinTable' => 'contratsinsertion_users',
				'foreignKey' => 'user_id',
				'associationForeignKey' => 'contratinsertion_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'ContratinsertionUser'
			),
			'Ancienpoledossierpcg66' => array(
				'className' => 'Poledossierpcg66',
				'joinTable' => 'polesdossierspcgs66_users',
				'foreignKey' => 'user_id',
				'associationForeignKey' => 'poledossierpcg66_id',
				'unique' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'finderQuery' => null,
				'deleteQuery' => null,
				'insertQuery' => null,
				'with' => 'Poledossierpcg66User'
			),
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'joinTable' => 'users_zonesgeographiques',
				'foreignKey' => 'user_id',
				'associationForeignKey' => 'zonegeographique_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'UserZonegeographique'
			),
			'Referent' => array(
				'className' => 'Referent',
				'joinTable' => 'referents_users',
				'foreignKey' => 'user_id',
				'associationForeignKey' => 'referent_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'ReferentUser'
			)
		);

		/**
		 * Hash du mot de passe.
		 *
		 * @param array $options
		 * @return boolean
		 */
		public function beforeSave( $options = array() ) {
			if( !empty( $this->data['User']['passwd'] ) ) {
				$this->data['User']['password'] = Security::hash( $this->data['User']['passwd'], null, true );
			}
			return parent::beforeSave( $options );
		}

		/**
		 * Vérification du mot de passe courant, avec la clé primaire se trouvant
		 * dans $this->data et le hash par défaut (salted).
		 *
		 * @param mixed $check Les valeurs à vérifier.
		 * @return boolean
		 */
		public function checkCurrentPassword( $check ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$result = true;
			foreach( Set::normalize( $check ) as $value ) {
				$count = $this->find(
					'count',
					array(
						'conditions' => array(
							"{$this->alias}.{$this->primaryKey}" => $this->data[$this->alias][$this->primaryKey],
							"{$this->alias}.password" => Security::hash( $value, null, true ),
						),
						'contain' => false
					)
				);
				$result = ( $count == 1 ) && $result;
			}

			return $result;
		}

		/**
		 * Vérification de la force du mot de passe. Il faut au moins 8
		 * caractères, un caractère spécial et un chiffre.
		 *
		 * @link http://edgeward.co.uk/blog/2010/08/cakephp-password-complexity-validation/ Inspiration regexp
		 *
		 * @param mixed $check Les valeurs à vérifier.
		 * @return boolean
		 */
		public function passwordStrength( $check ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$result = true;
			foreach( Set::normalize( $check ) as $value ) {
				$result = preg_match( '/(?=^.{8,}$)(?=.*\d)(?![.\n])(?=.*\W+).*$/', $value ) && $result;
			}

			return $result;
		}

		/**
		 * Vérification que la valeur du champ soit égale à la valeur de référence.
		 *
		 * @param mixed $check Les valeurs à vérifier.
		 * @param string $referenceField Le nom du champ de référence.
		 * @return boolean
		 */
		public function checkIdenticalValues( $check, $referenceField ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$result = true;
			foreach( Set::normalize( $check ) as $value ) {
				$result = ( $value == $this->data[$this->alias][$referenceField] );
			}

			return $result;
		}

		/**
		 * Vérification et mise à jour du mot de passe.
		 * Les champs attendus sont: id, current_password, new_password et
		 * new_password_confirmation, sous la clé User.
		 *
		 * @param array $data
		 * @return boolean
		 */
		public function changePassword( array $data ) {
			$this->create( $data );
			return $this->validates() && $this->updateAllUnBound(
				array( "{$this->alias}.password" => '\''.Security::hash( $data[$this->alias]['new_password'], null, true ).'\'' ),
				array( "{$this->alias}.{$this->primaryKey}" => $data[$this->alias][$this->primaryKey] )
			);
		}

		/**
		 * Retourne la liste des enums pour le modèle User.
		 *
		 * Lorsque l'on est CG 66, on filtre les valeurs possibles du champ type
		 * et on change une traduction pour les référents des OA.
		 *
		 * @return array
		 */
		public function enums() {
			$enums = parent::enums();

			$enums[$this->alias]['typevoie'] = $this->Option->libtypevoie();
			$enums[$this->alias]['accueil_reference_affichage'] = $this->Option->accueil_reference_affichage();

			if( isset( $enums[$this->alias]['type'] ) && Configure::read( 'Cg.departement' ) == 66 ) {
				unset( $enums[$this->alias]['type']['externe_cpdv'] );
				unset( $enums[$this->alias]['type']['externe_secretaire'] );
				unset( $enums[$this->alias]['type']['externe_cpdvcom'] );

				$enums[$this->alias]['type']['externe_ci'] = 'Référent organisme agrée';
			}

			$this->loadModel('Referent');
			$query = array (
				'order by' => array (
					'nom ASC',
					'prenom ASC'
				),
				'recursive' => -1
			);
			$referents = $this->Referent->find ('all', $query);
			$enums[$this->alias]['accueil_referent_id'] = array ();
			foreach ($referents as $referent) {
				$enums[$this->alias]['accueil_referent_id'][$referent['Referent']['id']] =  $referent['Referent']['nom'].' '.$referent['Referent']['prenom'];
			}

			return $enums;
		}

		/**
		 * Permet d'obtenir le noeud parent pour la mise à jour automatique des aros
		 *
		 * @return array
		 */
		public function parentNode() {
			if (!$this->id && empty($this->data)) {
				return null;
			}
			if (isset($this->data['User']['group_id'])) {
				$groupId = $this->data['User']['group_id'];
			} else {
				$groupId = $this->field('group_id');
			}
			if (!$groupId) {
				return null;
			}
			return array('Group' => array('id' => $groupId));
		}

		/**
		 * Ajoute un alias à l'Aro correspondant dans le cadre d'un ajout
		 *
		 * @param boolean $created
		 */
		public function afterSave( $created, $options = array() ) {
			parent::afterSave( $created, $options );

			if ($created) {
				$aro = $this->Aro->find('first',
					array(
						'conditions' => array('model' => $this->alias, 'foreign_key' => $this->id),
						'recursive' => -1
					)
				);
				$this->Aro->id = Hash::get($aro, $this->Aro->alias.'.id');
				$aro[$this->Aro->alias]['alias'] = Hash::get($this->data, $this->alias.'.username');
				$this->Aro->create(false);
				$this->Aro->save( $aro, array( 'atomic' => false ) );
			}
		}

		/**
		 * Récupère le type d'utilisateur connecté selon l'id passé en paramètre
		 * @param integer user_id
		 * @return string user_type
		 */
		public function getUserType($user_id) {
			$user = $this->find(
				'first',
				array(
					'recursive' => -1,
					'conditions' => array(
						'User.id' => $user_id
					)
				)
			);
			return $user['User']['type'];
		}

		/**
		 * Vérifie si l'utilisateur est de type conseil départemental ('cg') ou non
		 * @param integer $user_id
		 * @return boolean
		 */
		public function isTypeCG($user_id) {
			if( is_numeric($user_id) ) {
				return $this->getUserType($user_id) == 'cg' ? true : false;
			}
			//Si le user_id n'est pas numeric / null
			return false;
		}

		/**
		 * Récupère la liste des référents sectorisation associés à l'utilisateur
		 * sous forme utilisable dans une requête sql
		 * @param integer $user_id
		 * @return string
		 */
		public function getReferentsSectorisation($user_id){
			$sql =
			"select r.id
			from referents_users ru
				join referents r on r.id = ru.referent_id
				join users u on u.id = ru.user_id
				join structuresreferentes s on s.id = r.structurereferente_id
			where u.id = $user_id
				and s.actif_sectorisation = true";

			$results =  $this->query( $sql);

			$referents_user = "";
			foreach ($results as $result){
				$referents_user .= $result[0]['id'].",";
			}
			$referents_user = substr($referents_user, 0, -1);

			return $referents_user;
		}

		/**
		 * Callback de l'authentification LDAP
		 * Permet l'envoi de mail automatique en cas d'authentification réussi sur le LDAP 
		 * mais que l'utilisateur est non trouvé dans WebRSA
		 * @param string $username
		 * @param string $userEmail
		 * @return bool
		 */
		public function ldapAuthCallback($username, $userEmail) {
			$success = true;
			if( Configure::read("Module.Ldap.mail_auto") ) {
				try{
					// Configuration du mail
					$configName = WebrsaEmailConfig::getName( 'ldap_utilisateur_non_trouve' );
					$Email = new CakeEmail( $configName );

					$Email->to( WebrsaEmailConfig::getValue( 'ldap_utilisateur_non_trouve', 'to', $Email->from() ) );

					$Email->subject( WebrsaEmailConfig::getValue( 'ldap_utilisateur_non_trouve', 'subject', $Email->subject() ) );

					// Corps du mail
					$mailBody = Configure::read('Module.Ldap.mail_modele');

					// Remplacement de la balise %user% dans le corps du mail
					$mailBody = str_replace("%user%", $username , $mailBody);

					// Remplacement de la balise %email% dans le corps du mail
					$mailBody = str_replace("%email%", $userEmail , $mailBody);

					$result = $Email->send( $mailBody );

					$success = !empty( $result );
				} catch( Exception $e ) {
					$this->log( $e->getMessage(), LOG_ERROR );
					$success = false;
				}
			}
			return $success;
		}

		public function getUserByALI($ali_id){
			$user = $this->find(
				'first',
				[
					'conditions' => [
						'User.structurereferente_id' => $ali_id,
						'Categorieutilisateur.code' => 'import_donnees_ali',
						'User.email is not NULL'
					]
				]
			);

			return !empty($user) ? $user['User'] : null;
		}
	}
?>