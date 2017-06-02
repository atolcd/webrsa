<?php
	/**
	 * Code source de la classe Typerdv.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Typerdv ...
	 *
	 * @package app.Model
	 */
	class Typerdv extends AppModel
	{
		public $name = 'Typerdv';

		public $displayField = 'libelle';

		public $order = 'Typerdv.id ASC';

		public $validate = array(
			'libelle' => array(
				'isUnique' => array(
					'rule' => 'isUnique',
					'message' => 'Cette valeur est déjà utilisée'
				),
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'modelenotifrdv' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'motifpassageep' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'nbabsencesavpassageep', false, array( 0 ) ),
					'message' => 'Champ obligatoire',
				)
			)
		);

		public $hasMany = array(
			'Entretien' => array(
				'className' => 'Entretien',
				'foreignKey' => 'typerdv_id',
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
				'foreignKey' => 'typerdv_id',
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
			'Thematiquerdv' => array(
				'className' => 'Thematiquerdv',
				'foreignKey' => 'typerdv_id',
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
			'Statutrdv' => array(
				'className' => 'Statutrdv',
				'joinTable' => 'statutsrdvs_typesrdv',
				'foreignKey' => 'typerdv_id',
				'associationForeignKey' => 'statutrdv_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'StatutrdvTyperdv'
			)
		);

		/**
		 * Retourne la liste des modèles odt paramétrés pour le impressions de
		 * cette classe.
		 *
		 * @return array
		 */
		public function modelesOdt() {
			$prefix = 'RDV'.DS;

			$typesrdv = $this->find(
				'all',
				array(
					'fields' => array(
						'( \''.$prefix.'\' || "'.$this->alias.'"."modelenotifrdv" || \'.odt\' ) AS "'.$this->alias.'__modele"',
					),
					'recursive' => -1
				)
			);
			return Set::extract( $typesrdv, '/'.$this->alias.'/modele' );
		}
	}
?>