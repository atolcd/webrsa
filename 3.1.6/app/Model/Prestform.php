<?php
	/**
	 * Code source de la classe Prestform.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Prestform ...
	 *
	 * @package app.Model
	 */
	class Prestform extends AppModel
	{
		public $name = 'Prestform';

		public $validate = array(
			'actioninsertion_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'refpresta_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
		);

		public $belongsTo = array(
			'Actioninsertion' => array(
				'className' => 'Actioninsertion',
				'foreignKey' => 'actioninsertion_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Refpresta' => array(
				'className' => 'Refpresta',
				'foreignKey' => 'refpresta_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Retourne l'id de la personne à laquelle est lié un enregistrement.
		 *
		 * @param integer $id L'id de l'enregistrement
		 * @return integer
		 */
		public function personneId( $id ) {
			$querydata = array(
				'fields' => array( "Contratinsertion.personne_id" ),
				'joins' => array(
					$this->join( 'Actioninsertion', array( 'type' => 'INNER' ) ),
					$this->Actioninsertion->join( 'Contratinsertion', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					"{$this->alias}.id" => $id
				),
				'recursive' => -1
			);

			$result = $this->find( 'first', $querydata );

			if( !empty( $result ) ) {
				return $result['Contratinsertion']['personne_id'];
			}
			else {
				return null;
			}
		}
	}
?>