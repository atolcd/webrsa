<?php
	/**
	 * Code source de la classe Statutrdv.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Statutrdv ...
	 *
	 * @package app.Model
	 */
	class Statutrdv extends AppModel
	{
		public $name = 'Statutrdv';

		public $displayField = 'libelle';

		public $order = 'Statutrdv.id ASC';

		public $validate = array(
			'libelle' => array(
				array(
					'rule' => 'isUnique',
					'message' => 'Cette valeur est déjà utilisée'
				),
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			)
		);

		public $hasMany = array(
			'Rendezvous' => array(
				'className' => 'Rendezvous',
				'foreignKey' => 'statutrdv_id',
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
				'foreignKey' => 'statutrdv_id',
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
			'Typerdv' => array(
				'className' => 'Typerdv',
				'joinTable' => 'statutsrdvs_typesrdv',
				'foreignKey' => 'statutrdv_id',
				'associationForeignKey' => 'typerdv_id',
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
		 * Retourne un booléen suivant si le statut du rdv passé en paramètre
		 * peut ou non provoquer un passage en EP
		 *
		 * @param integer $statutrdv_id
		 * @return boolean
		 */
		public function provoquePassageCommission( $statutrdv_id ) {
			$statutrdv = $this->find(
				'first',
				array(
					'conditions' => array(
						'Statutrdv.id' => $statutrdv_id
					),
					'contain' => false
				)
			);

			return Hash::get( (array)$statutrdv, 'Statutrdv.provoquepassagecommission' );
		}
	}
?>
