<?php
	/**
	 * Code source de la classe Infocontactpersonne.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'FrValidation', 'Validation' );

	/**
	 * La classe Infocontactpersonne ...
	 *
	 * @package app.Model
	 */
	class Infocontactpersonne extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Infocontactpersonne';

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
		 * Récupère les données de contact manuelles pour la personne dont l'id est en paramètre
		 *  @param int $id Id de la personne
		 */
		public function getContactsPersonne($id){

			return $this->query("
				with
				modif as (
					select distinct on (modified) modified
					from infoscontactspersonne i
					where personne_id = {$id}
					order by modified desc
				),
				fixe as (
					select distinct on (modified_fixe) modified_fixe, fixe, id, modified
					from infoscontactspersonne i
					where personne_id = {$id}
					order by modified_fixe desc, modified asc
				),
				mobile as (
					select distinct on (modified_mobile) modified_mobile, mobile, id, modified
					from infoscontactspersonne i
					where personne_id = {$id}
					order by modified_mobile desc, modified asc
				),
				email as (
					select distinct on (modified_email) modified_email, email, id, modified
					from infoscontactspersonne i
					where personne_id = {$id}
					order by modified_email desc, modified asc
				)
				select modif.modified, fixe, modified_fixe, mobile, modified_mobile, email, modified_email
				from modif full join fixe on fixe.modified = modif.modified full join mobile on modif.modified = mobile.modified full join email on email.modified = modif.modified
				order by modif.modified desc
			");

		}

	}
?>