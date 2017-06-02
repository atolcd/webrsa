<?php
	/**
	 * Fichier source du modèle Dernierdossierallocataire.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Classe Dernierdossierallocataire.
	 *
	 * @package app.Model
	 */
	class Dernierdossierallocataire extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Dernierdossierallocataire';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Dossier' => array(
				'className' => 'Dossier',
				'foreignKey' => 'dossier_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
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
		 * Retourne une erreur lorsque la configuration de Optimisations.useTableDernierdossierallocataire
		 * est à true et qu'il n'existe aucune donnée dans cette table.
		 *
		 * @return array
		 */
		public function storedDataErrors() {
			$entry = array();

			if( Configure::read( 'Optimisations.useTableDernierdossierallocataire' ) === true ) {
				$record = $this->find( 'all', array( 'limit' => 1 ) );
				if( empty( $record ) ) {
					$message = 'La configuration de <code>Optimisations.useTableDernierdossierallocataire</code> dans le webrsa.inc est à <code>true</code>, mais aucune donnée n\'est présente dans la table <code>derniersdossiersallocataires</code>.<br/>Veuillez lancer le shell Derniersdossiersallocataires: <code>lib/Cake/Console/cake Derniersdossiersallocataires</code> et pensez à mettre cette commande en tâche planifiée.';
					$entry = array( array( $this->alias => array( 'error' => $message ) ) );
				}
			}

			return $entry;
		}
	}
?>