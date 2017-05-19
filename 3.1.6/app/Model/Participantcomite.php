<?php
	/**
	 * Code source de la classe Participantcomite.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Participantcomite ...
	 *
	 * @package app.Model
	 */
	class Participantcomite extends AppModel
	{
		public $name = 'Participantcomite';

		public $order = 'Participantcomite.id ASC';

		public $actsAs = array(
			'Formattable' => array(
				'phone' => array( 'numtel' )
			),
			'ValidateTranslate',
			'Validation.ExtraValidationRules',
		);

		public $hasAndBelongsToMany = array(
			'Comiteapre' => array(
				'className' => 'Comiteapre',
				'joinTable' => 'comitesapres_participantscomites',
				'foreignKey' => 'participantcomite_id',
				'associationForeignKey' => 'comiteapre_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'ComiteapreParticipantcomite'
			)
		);

		public $validate = array(
			'nom' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'qual' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'prenom' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'organisme' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'fonction' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'numtel' => array(
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				),
			),
			'mail' => array(
				'email' => array(
					'rule' => 'email',
					'allowEmpty' => true,
					'message' => 'Le mail n\'est pas valide'
				)
			)
		);
	}
?>
