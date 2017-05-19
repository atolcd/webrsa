<?php	
	/**
	 * Code source de la classe CandidatureProg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe CandidatureProg66 ...
	 *
	 * @package app.Model
	 */
	class CandidatureProg66 extends AppModel
	{
		public $name = 'CandidatureProg66';

		public $belongsTo = array(
			'ActioncandidatPersonne' => array(
				'className' => 'ActioncandidatPersonne',
				'foreignKey' => 'actioncandidat_personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Progfichecandidature66' => array(
				'className' => 'Progfichecandidature66',
				'foreignKey' => 'progfichecandidature66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>