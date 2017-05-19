<?php	
	/**
	 * Code source de la classe Integrationfichierapre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Integrationfichierapre ...
	 *
	 * @package app.Model
	 */
	class Integrationfichierapre extends AppModel
	{
		public $name = 'Integrationfichierapre';

		public $validate = array(
			'nbr_atraiter' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'nbr_succes' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'nbr_erreurs' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'fichier_in' => array(
				'notempty' => array(
					'rule' => array('notempty'),
				),
			),
		);
	}
?>