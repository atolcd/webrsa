<?php
	/**
	 * Code source de la classe Action.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Action ...
	 *
	 * @package app.Model
	 */
	class Action extends AppModel
	{
		public $name = 'Action';

		public $displayField = 'libelle';

		public $order = array( 'Action.libelle ASC' );

		public $validate = array(
			'code' => array(
				array(
					'rule' => 'isUnique',
					'message' => 'Cette valeur est déjà utilisée'
				),
				array(
					'rule' => 'alphaNumeric',
					'message' => 'Veuillez n\'utiliser que des lettres et des chiffres'
				),
				array(
					'rule' => array( 'between', 2, 2 ),
					'message' => 'Le code de l\'action est composé de 2 caractères'
				),
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
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
		//The Associations below have been created with all possible keys, those that are not needed can be removed

		public $belongsTo = array(
			'Typeaction' => array(
				'className' => 'Typeaction',
				'foreignKey' => 'typeaction_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array( 'Contratinsertion.engag_object = Action.code' ),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);
		/**
		*
		*/

		public function grouplist( $type = null ) {
			$conditions = array();

			if( $type == 'aide' ) {
				$conditions = array(
					'or' => array(
						'Action.code = \'02\'',
						'Action.code = \'04\'',
						'Action.code = \'05\'',
						'Action.code = \'06\'',
						'Action.code = \'33\'',
						'Action.code = \'07\'',
						'Action.code = \'10\''
					)
				);
			}
			else if( $type == 'prestation' ) {
				$conditions = array(
					'or' => array(
						'Action.code = \'03\'',
						'Action.code = \'1F\'',
						'Action.code = \'1P\'',
						'Action.code = \'21\'',
						'Action.code = \'23\'',
						'Action.code = \'24\'',
						'Action.code = \'26\'',
						'Action.code = \'29\'',
						'Action.code = \'31\'',
						'Action.code = \'22\'',
						'Action.code = \'41\'',
						'Action.code = \'42\'',
						'Action.code = \'43\'',
						'Action.code = \'44\'',
						'Action.code = \'45\'',
						'Action.code = \'46\'',
						'Action.code = \'48\'',
						'Action.code = \'51\'',
						'Action.code = \'71\'',
						'Action.code = \'81\'',
						'Action.code = \'99\''
					)
				);
			}

			$actions = $this->find(
				'list',
				array(
					'fields' => array(
						'Action.code',
						'Action.libelle',
						'Typeaction.libelle'
					),
					'joins' => array(
						array(
							'table' => 'typesactions',
							'alias' => 'Typeaction',
							'type'  => 'left',
							'conditions'=> array( 'Action.typeaction_id = Typeaction.id' )
						)
					),
					'conditions' => $conditions,
					'order' => array(
						'Typeaction.id',
						'Action.id'
					)
				)
			);
			return $actions;
		}
	}
?>
