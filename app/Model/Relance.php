<?php
	/**
	 * Code source de la classe Relance.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Relance ...
	 *
	 * @package app.Model
	 */
	class Relance extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Relance';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'relances';

		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 *
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array(
			'relancesupport' => array(
				'SMS',
				'EMAIL'
			),
			'relancetype' => array(
				'RDV',
				'EP'
			),
			'relancemode' => array(
				'ORANGE_CONTACT_EVERYONE',
				'EMAIL'
			),
		);
	}
?>