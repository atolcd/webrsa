<?php
	/**
	 * Code source de la classe Contactpartenaire.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Contactpartenaire ...
	 *
	 * @package app.Model
	 */
	class Contactpartenaire extends AppModel
	{
		public $name = 'Contactpartenaire';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'nom_candidat';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Partenaire' => array(
				'className' => 'Partenaire',
				'foreignKey' => 'partenaire_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Actioncandidat' => array(
				'className' => 'Actioncandidat',
				'foreignKey' => 'contactpartenaire_id',
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

		public $virtualFields = array(
			'nom_candidat' => array(
				'type'      => 'string',
				'postgres'  => '( "%s"."qual" || \' \' || "%s"."nom" || \' \' || "%s"."prenom" )'
			)
		);


        /**
		*	Recherche des contacts de partenaires dans le paramétrage de l'application
		*
		*/
		public function search( $criteres ) {
			/// Conditions de base
			$conditions = array();

			// Critères sur une personne du foyer - nom, prénom, nom de naissance -> FIXME: seulement demandeur pour l'instant
			$filtersContactspartenaires = array();
			foreach( array( 'nom', 'prenom' ) as $critereContactpartenaire ) {
				if( isset( $criteres['Contactpartenaire'][$critereContactpartenaire] ) && !empty( $criteres['Contactpartenaire'][$critereContactpartenaire] ) ) {
					$conditions[] = 'Contactpartenaire.'.$critereContactpartenaire.' ILIKE \''.$this->wildcard( $criteres['Contactpartenaire'][$critereContactpartenaire] ).'\'';
				}
			}

            if( !empty( $criteres['Contactpartenaire']['partenaire_id'] ) ) {
                $conditions[] = array( 'Partenaire.id' => $criteres['Contactpartenaire']['partenaire_id'] );
            }

			$query = array(
				'fields' => array_merge(
					$this->fields(),
					$this->Partenaire->fields()
				),
				'order' => array( 'Contactpartenaire.nom ASC', 'Contactpartenaire.prenom ASC' ),
				'joins' => array(
					$this->join( 'Partenaire', array( 'type' => 'LEFT OUTER' ) )
				),
				'recursive' => -1,
				'conditions' => $conditions
			);

			return $query;
		}
	}
?>