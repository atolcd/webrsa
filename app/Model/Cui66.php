<?php
	/**
	 * Fichier source de la classe Cui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Cui66 est la classe contenant les informations additionnelles du CUI pour le CG 66.
	 *
	 * @package app.Model
	 */
	class Cui66 extends AppModel
	{
		/**
		 * Alias de la table et du model
		 * @var string
		 */
		public $name = 'Cui66';

		/**
		 * Recurcivité du model 
		 * @var integer
		 */
		public $recursive = -1;
		
		/**
		 * Possède des clefs étrangères vers d'autres models
		 * @var array
		 */
        public $belongsTo = array(
			'Cui' => array(
				'className' => 'Cui',
				'foreignKey' => 'cui_id',
				'dependent' => true,
			),
			'Partenaire' => array(
				'className' => 'Partenaire',
				'foreignKey' => 'partenaire_id',
				'dependent' => true,
			),
			'Personnecui66' => array(
				'className' => 'Personnecui66',
				'foreignKey' => 'personnecui66_id',
				'dependent' => true,
			),
        );
		
		/**
		 * Ces models possèdent une clef étrangère vers ce model
		 * @var array
		 */
		public $hasMany = array(
			'Propositioncui66' => array(
				'className' => 'Propositioncui66',
				'foreignKey' => 'cui66_id',
				'dependent' => true,
			),
			'Decisioncui66' => array(
				'className' => 'Decisioncui66',
				'foreignKey' => 'cui66_id',
				'dependent' => true,
			),
			'Accompagnementcui66' => array(
				'className' => 'Accompagnementcui66',
				'foreignKey' => 'cui66_id',
				'dependent' => true,
			),
			'Suspensioncui66' => array(
				'className' => 'Suspensioncui66',
				'foreignKey' => 'cui66_id',
				'dependent' => true,
			),
			'Rupturecui66' => array(
				'className' => 'Rupturecui66',
				'foreignKey' => 'cui66_id',
				'dependent' => true,
			),
			'Historiquepositioncui66' => array(
				'className' => 'Historiquepositioncui66',
				'foreignKey' => 'cui66_id',
				'dependent' => true,
			),
		);
				
		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Formattable',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
		);
		
		/**
		 * Chemin relatif pour les modèles de documents .odt utilisés lors des
		 * impressions. Utiliser %s pour remplacer par l'alias.
		 * @var array
		 */
		public $modelesOdt = array(
			'ficheLiaison' => 'CUI/synthesecui66.odt',
			'default' => 'CUI/impression.odt',
		);
		
		/**
		 * Permet de faire le lien entre Cui66.etatdossiercui66 et Cui.decision_ci
		 * @var array
		 */
		public $correspondance_decision_ci = array(
			'A' => array(
				'annule',
				'rupturecontrat',
			),
			'E' => array(
				'attentepiece',
				'dossierrecu',
				'dossiereligible',
				'attentemail',
				'formulairecomplet',
				'attenteavis',
				'attentedecision',
				'attentenotification',
				'contratsuspendu',
				'dossiernonrecu',
				'dossierrelance',
			),
			'V' => array(
				'notifie',
				'encours',
				'perime',
			),
			'R' => array(
				'decisionsanssuite',
				'nonvalide',
			),
		);
		
		/**
		 * Modèles utilisés par ce modèle.
		 * 
		 * @var array
		 */
		public $uses = array(
			'WebrsaCui66'
		);
		
		/**
		 * Permet de savoir si un ajout est possible à partir des messages
		 * renvoyés par la méthode messages.
		 *
		 * @param array $messages
		 * @return boolean
		 */
		public function addEnabled( array $messages ) {
			return $this->WebrsaCui66->addEnabled($messages);
		}
		
		/**
		 * Retourne les options nécessaires au formulaire de recherche, au formulaire,
		 * aux impressions, ...
		 *
		 * @return array
		 */
		public function options($user_id = null) {
			return $this->WebrsaCui66->options($user_id);
		}
	}
?>