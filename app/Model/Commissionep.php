<?php
	/**
	 * Code source de la classe Commissionep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Séance d'équipe pluridisciplinaire.
	 *
	 * @package app.Model
	 */
	class Commissionep extends AppModel
	{
		public $name = 'Commissionep';

		public $displayField = 'dateseance';

		public $recursive = -1;

		public $actsAs = array(
			'Autovalidate2',
			'ValidateTranslate',
			'Formattable',
			'Enumerable' => array(
				'fields' => array(
					'etatcommissionep'
				)
			),
			'Gedooo.Gedooo',
			'ModelesodtConditionnables' => array(
				58 => '%s/ordredujour_participant_58.odt',
				66 => '%s/ordredujour_participant_66.odt',
				93 => array(
					'%s/ordredujour_participant_93.odt',
					'%s/fichesynthese.odt',
				)
			),
            'Conditionnable'
		);

		public $belongsTo = array(
			'Ep' => array(
				'className' => 'Ep',
				'foreignKey' => 'ep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		public $hasMany = array(
			'Passagecommissionep' => array(
				'className' => 'Passagecommissionep',
				'foreignKey' => 'commissionep_id',
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
			'CommissionepMembreep' => array(
				'className' => 'CommissionepMembreep',
				'foreignKey' => 'commissionep_id',
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

		public $hasAndBelongsToMany = array(
			'Membreep' => array(
				'className' => 'Membreep',
				'joinTable' => 'commissionseps_membreseps',
				'foreignKey' => 'commissionep_id',
				'associationForeignKey' => 'membreep_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'CommissionepMembreep'
			),
		);

		public $validate = array(
			'raisonannulation' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Ce champ est obligatoire.',
					'allowEmpty' => false,
					'required' => false
				)
			)
		);
		
		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('WebrsaCommissionep');
		
		/**
		 * Valeurs de etatcommissionep signifiant qu'une commission est "En cours".
		 *
		 * @var array
		 */
		public static $etatsEnCours = array( 'cree', 'associe', 'valide', 'presence', 'decisionep', 'traiteep', 'decisioncg' );
		
		/**
		 * Savoir si la séance est cloturée ou non (suivant le thème l'EP et le CG ce sont prononcés)
		 * @deprecated since version 3.1 - n'est plus utilisé
		 */
		public function clotureSeance($datas) {
			trigger_error("Utilisation d'une méthode dépréciée : ".__CLASS__.'::'.__FUNCTION__, E_USER_DEPRECATED);
			$cloture = true;

			foreach( $this->WebrsaCommissionep->themesTraites( $datas['Commissionep']['id'] ) as $theme => $decision ) {
				$cloture = ($datas['Ep'][$theme]==$datas['Commissionep']['etatcommissionep']) && $cloture;
			}

			return $cloture;
		}
		
		/**
		 * Exporte la liste de dossier sélectionnables pour une commission d'EP donnée.
		 *
		 * @param @integer $commissionep_id L'id de la commission
		 */
		public function exportcsv($commissionep_id) {
			return $this->WebrsaCommissionep->cohorte($commissionep_id);
		}
		
		/**
		 * Retourne une chaîne de 12 caractères formattée comme suit:
		 * CO, année sur 4 chiffres, mois sur 2 chiffres, nombre de commissions.
		 */

		public function identifiant() {
			return 'CO'.date( 'Ym' ).sprintf( "%010s",  $this->find( 'count' ) + 1 );
		}

		/**
		 * Ajout de l'identifiant de la séance lors de la sauvegarde.
		 */

		public function beforeValidate( $options = array() ) {
			$return = parent::beforeValidate( $options );
			$primaryKey = Set::classicExtract( $this->data, "{$this->alias}.{$this->primaryKey}" );
			$identifiant = Set::classicExtract( $this->data, "{$this->alias}.identifiant" );

			if( empty( $primaryKey ) && empty( $identifiant ) && empty( $this->{$this->primaryKey} ) ) {
				$this->data[$this->alias]['identifiant'] = $this->identifiant();
			}

			return $return;
		}
		
		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les fonctions vides.
		 */
		public function prechargement() {
			return $this->WebrsaCommissionep->prechargement();
		}
		
		/**
		 *
		 * @see Commissionep::dossiersParListe()
		 *
		 * @return array
		 */
		public function querydataFragmentsErrors() {
			return $this->WebrsaCommissionep->querydataFragmentsErrors();
		}
	}
?>
