<?php
	/**
	 * Code source de la classe Infocontactpersonnecaf.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'FrValidation', 'Validation' );

	/**
	 * La classe Infocontactpersonnecaf ...
	 *
	 * @package app.Model
	 */
	class Infocontactpersonnecaf extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Infocontactpersonnecaf';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'infoscontactspersonnecaf';

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable' => array(
				'Validation2.Validation2DefaultFormatter' => array(
					'stripNotAlnum' => '/^(numfixe|numport)$/'
				)
			),
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate',
			'PersonneCSV'
		);

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Champs de validation du modèle.
		 *
		 */
		public $validate = array(
			'fixe' => array(
				'phone' => array(
					'rule' => array( 'phone', null, 'fr' ),
					'allowEmpty' => true
				)
			),
			'mobile' => array(
				'phone' => array(
					'rule' => array( 'phone', null, 'fr' ),
					'allowEmpty' => true
				)
			),
			'email' => array(
				'email' => array(
					'rule' => array( 'email' ),
					'allowEmpty' => true
				)
			),
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Récupère les données de contact caf pour la personne dont l'id est en paramètre
		 * @param int $id Id de la personne
		 */
		public function getContactsPersonne($id){

			return $this->query("
				with contact as(
					select to_char(modified_telephone, 'DD/MM/YY') as date, telephone as tel, null as tel2, null as email
					from infoscontactspersonnecaf i
					where personne_id = {$id}
					union
					select to_char(modified_telephone2, 'DD/MM/YY') as date, null as tel, telephone2 as tel2, null as email
					from infoscontactspersonnecaf i
					where personne_id = {$id}
					union
					select to_char(modified_email, 'DD/MM/YY') as date, null as tel, null as tel2, email as email
					from infoscontactspersonnecaf i
					where personne_id = {$id}
					order by date desc
				)
				select date, max(c.tel) as tel, max(c.tel2) as tel2, max(c.email) as email from contact c group by date order by date desc
			");
		}
	}
?>