<?php
	/**
	 * Code source de la classe Adresseprestatairefp93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractElementCataloguefp93', 'Model/Abstractclass' );

	/**
	 * La classe Adresseprestatairefp93 ...
	 *
	 * @package app.Model
	 */
	class Adresseprestatairefp93 extends AbstractElementCataloguefp93
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Adresseprestatairefp93';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		public $displayField = 'name';

		/**
		 * Champs virtuels impressionfin1 et impressionfin2 permettant de savoir
		 * si un courrier de "fin de sanction 1" ou "fin de sanction 2" peut être
		 * imprimé.
		 *
		 * @var array
		 */
		public $virtualFields = array(
			'name' => array(
				'type'      => 'string',
				'postgres'  => '( "%s"."adresse" || \', \' || "%s"."codepos" || \' \' || "%s"."localite" )'
			)
		);

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Cataloguepdifp93',
			'Formattable' => array(
				'phone' => array( 'tel', 'fax' )
			),
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
		);

		/**
		 * Règles de validation.
		 *
		 * @var array
		 */
		public $validate = array(
			'tel' => array(
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				)
			),
			'fax' => array(
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				)
			),
			'email' => array(
				'email' => array(
					'rule' => array( 'email' ),
					'allowEmpty' => true,
				)
			),
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Prestatairefp93' => array(
				'className' => 'Prestatairefp93',
				'foreignKey' => 'prestatairefp93_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Actionfp93' => array(
				'className' => 'Actionfp93',
				'foreignKey' => 'adresseprestatairefp93_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
			'Ficheprescription93' => array(
				'className' => 'Ficheprescription93',
				'foreignKey' => 'adresseprestatairefp93_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
		);

		/**
		 * Retourne les options à utiliser dans le formulaire d'ajout / de
		 * modification de la partie paramétrage.
		 *
		 * @param boolean Permet de s'assurer que l'on possède au moins un
		 *	enregistrement au niveau inférieur.
		 * @return array
		 */
		public function getParametrageOptions( $hasDescendant = false ) {
			$options = parent::getParametrageOptions( $hasDescendant );

			// Liste des prestataires
			$query = array( 'conditions' => array() );

			// ... et qui possède au moins un descendant ?
			if( $hasDescendant ) {
				$this->Prestatairefp93->Behaviors->attach( 'LinkedRecords' );
				$query['conditions'][] = $this->Prestatairefp93->linkedRecordVirtualField( $this->alias );
			}
			$options[$this->alias]['prestatairefp93_id'] = $this->Prestatairefp93->find( 'list', $query );

			return $options;
		}
	}
?>