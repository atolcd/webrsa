<?php
	/**
	 * Code source de la classe Avisfichedeliaison.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Avisfichedeliaison ...
	 *
	 * @package app.Model
	 */
	class Avisfichedeliaison extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Avisfichedeliaison';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2Formattable',
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
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Avisfichedeliaison' => array(
				'className' => 'Fichedeliaison',
				'foreignKey' => 'fichedeliaison_id',
				'conditions' => array('Avisfichedeliaison.etape' => 'avis'),
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Validationfiche' => array(
				'className' => 'Fichedeliaison',
				'foreignKey' => 'fichedeliaison_id',
				'conditions' => array('Validationfiche.etape' => 'validation'),
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Prépare le formulaire d'avis ou de validation
		 *
		 * @param integer $fichedeliaison_id
		 * @return array
		 */
		public function prepareFormDataAvis($fichedeliaison_id) {
			$results = $this->Fichedeliaison->find('first',
				array(
					'fields' => array_merge(
						$this->Fichedeliaison->fields(),
						$this->Fichedeliaison->Avistechniquefiche->fields(),
						$this->Fichedeliaison->Validationfiche->fields(),
						array(

						)
					),
					'joins' => array(
						$this->Fichedeliaison->join('Avistechniquefiche'),
						$this->Fichedeliaison->join('Validationfiche'),
					),
					'conditions' => array(
						'Fichedeliaison.id' => $fichedeliaison_id
					),
				)
			);
			$results['FichedeliaisonPersonne']['personne_id'] =
				Hash::extract(
					$this->Fichedeliaison->FichedeliaisonPersonne->find('all',
						array('conditions' => array('fichedeliaison_id' => $fichedeliaison_id))
					),
					'{n}.FichedeliaisonPersonne.personne_id'
				)
			;

			return $results;
		}
	}
?>