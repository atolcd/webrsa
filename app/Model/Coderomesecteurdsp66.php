<?php
	/**
	 * Code source de la classe Coderomesecteurdsp66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Coderomesecteurdsp66 ...
	 *
	 * @package app.Model
	 */
	class Coderomesecteurdsp66 extends AppModel
	{
		public $name = 'Coderomesecteurdsp66';

		public $displayField = 'intitule';

		public $actsAs = array(
			'Autovalidate2'
		);

		public $hasMany = array(
			'Coderomemetierdsp66' => array(
				'className' => 'Coderomemetierdsp66',
				'foreignKey' => 'coderomesecteurdsp66_id',
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
			'Libsecactderact66SecteurDsp' => array(
				'className' => 'Dsp',
				'foreignKey' => 'libsecactderact66_secteur_id',
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
			'Libsecactderact66SecteurDspRev' => array(
				'className' => 'DspRev',
				'foreignKey' => 'libsecactderact66_secteur_id',
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
			'Libsecactdomi66SecteurDsp' => array(
				'className' => 'Dsp',
				'foreignKey' => 'libsecactdomi66_secteur_id',
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
			'Libsecactdomi66SecteurDspRev' => array(
				'className' => 'DspRev',
				'foreignKey' => 'libsecactdomi66_secteur_id',
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
			'Libsecactrech66SecteurDsp' => array(
				'className' => 'Dsp',
				'foreignKey' => 'libsecactrech66_secteur_id',
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
			'Libsecactrech66SecteurDspRev' => array(
				'className' => 'DspRev',
				'foreignKey' => 'libsecactrech66_secteur_id',
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
            'Partenaire' => array(
				'className' => 'Partenaire',
				'foreignKey' => 'secteuractivitepartenaire_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
            'Personnepcg66' => array(
				'className' => 'Personnepcg66',
				'foreignKey' => 'categoriegeneral',
				'dependent' => false,
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

		public $virtualFields = array(
			'intitule' => array(
				'type'      => 'string',
				'postgres'  => '( "%s"."code" || \'. \' || "%s"."name" )'
			),
		);
	}
?>