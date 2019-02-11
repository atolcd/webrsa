<?php
	/**
	 * Code source de la classe CourrierpdoTraitementpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe CourrierpdoTraitementpcg66 ...
	 *
	 * @package app.Model
	 */
	class CourrierpdoTraitementpcg66 extends AppModel
	{
		public $name = 'CourrierpdoTraitementpcg66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array (
			'Gedooo.Gedooo',
			'StorablePdf',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Traitementpcg66' => array(
				'className' => 'Traitementpcg66',
				'foreignKey' => 'traitementpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Courrierpdo' => array(
				'className' => 'Courrierpdo',
				'foreignKey' => 'courrierpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Contenutextareacourrierpdo' => array(
				'className' => 'Contenutextareacourrierpdo',
				'foreignKey' => 'textareacourrierpdo_id',
				'dependent' => true,
				'conditions' => '',
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
		* Récupère les données pour le PDf
		*/

		public function getDataForPdf( $id ) {
			// TODO: error404/error500 si on ne trouve pas les données
		$optionModel = ClassRegistry::init( 'Option' );
			$qual = $optionModel->qual();
			$conditions = array( 'CourrierpdoTraitementpcg66.id' => $id );

			$joins = array(
				array(
					'table'      => 'courrierspdos',
					'alias'      => 'Courrierpdo',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'CourrierpdoTraitementpcg66.courrierpdo_id = Courrierpdo.id' )
				),
				array(
					'table'      => 'traitementspcgs66',
					'alias'      => 'Traitementpcg66',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'CourrierpdoTraitementpcg66.traitementpcg66_id = Traitementpcg66.id' )
				),
				array(
					'table'      => 'personnespcgs66',
					'alias'      => 'Personnepcg66',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Personnepcg66.id = Traitementpcg66.personnepcg66_id' )
				),
				array(
					'table'      => 'personnes',
					'alias'      => 'Personne',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						'Personne.id = Personnepcg66.personne_id',
					)
				),
				array(
					'table'      => 'foyers',
					'alias'      => 'Foyer',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Foyer.id = Personne.foyer_id' )
				),
				array(
					'table'      => 'dossiers',
					'alias'      => 'Dossier',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Dossier.id = Foyer.dossier_id' )
				),
				array(
					'table'      => 'adressesfoyers',
					'alias'      => 'Adressefoyer',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						'Foyer.id = Adressefoyer.foyer_id',
						'Adressefoyer.rgadr' => '01'
					)
				),
				array(
					'table'      => 'adresses',
					'alias'      => 'Adresse',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Adresse.id = Adressefoyer.adresse_id' )
				),
				array(
					'table'      => 'pdfs',
					'alias'      => 'Pdf',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'Pdf.modele' => $this->alias,
						'Pdf.fk_value = CourrierpdoTraitementpcg66.id'
					)
				),
			);

			$queryData = array(
				'fields' => array(
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.lieudist',
					'Adresse.numcom',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Adresse.pays',
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Dossier.matricule',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.nir',
					'Traitementpcg66.traitementtypepdo_id',
					'Traitementpcg66.datereception',
					'Traitementpcg66.id',
					'Traitementpcg66.datedepart',
					'Traitementpcg66.descriptionpdo_id',
					'Traitementpcg66.clos',
					'Courrierpdo.name',
					'Courrierpdo.modeleodt',
				),
				'joins' => $joins,
				'conditions' => $conditions,
				'contain' => false
			);

			$data = $this->find( 'first', $queryData );

			$data['Personne']['qual'] = Set::enum( $data['Personne']['qual'], $qual );

			return $data;
		}

		/**
		* Retourne le chemin relatif du modèle de document à utiliser pour l'enregistrement du PDF.
		*/

		public function modeleOdt( $data ) {
			return "PDO/Courrierpdo/{$data['Courrierpdo']['modeleodt']}.odt";
		}
	}
?>