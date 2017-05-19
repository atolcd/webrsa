<?php
	/**
	 * Code source de la classe StatutrdvTyperdv.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe StatutrdvTyperdv ...
	 *
	 * @package app.Model
	 */
	class StatutrdvTyperdv extends AppModel
	{
		public $name = 'StatutrdvTyperdv';

		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'typecommission'
				)
			),
			'ValidateTranslate'
		);

		public $validate = array(
			'statutrdv_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'statutrdv_id', 'typerdv_id' ) ),
					'message' => 'Ce statut est déjà utilisé avec ce type.'
				),
			),
			'typerdv_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'statutrdv_id', 'typerdv_id' ) ),
					'message' => 'Ce statut est déjà utilisé avec ce type.'
				),
			),
			'nbabsenceavantpassagecommission' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'typecommission' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' ),
				),
			),
		);

		public $belongsTo = array(
			'Statutrdv' => array(
				'className' => 'Statutrdv',
				'foreignKey' => 'statutrdv_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typerdv' => array(
				'className' => 'Typerdv',
				'foreignKey' => 'typerdv_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>