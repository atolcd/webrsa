<?php
	/**
	 * Code source de la classe CourrierpdoTraitementpdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe CourrierpdoTraitementpdo ...
	 *
	 * @package app.Model
	 */
	class CourrierpdoTraitementpdo extends AppModel
	{
		public $name = 'CourrierpdoTraitementpdo';

		public $actsAs = array (
			'Formattable',
			'ValidateTranslate',
			'Autovalidate2',
			'Gedooo.Gedooo',
			'StorablePdf'
		);

		public $belongsTo = array(
			'Traitementpdo' => array(
				'className' => 'Traitementpdo',
				'foreignKey' => 'traitementpdo_id',
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
			$typevoie = $optionModel->typevoie();
			$conditions = array( 'CourrierpdoTraitementpdo.id' => $id );

			$joins = array(
				array(
					'table'      => 'courrierspdos',
					'alias'      => 'Courrierpdo',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'CourrierpdoTraitementpdo.courrierpdo_id = Courrierpdo.id' )
				),
				array(
					'table'      => 'traitementspdos',
					'alias'      => 'Traitementpdo',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'CourrierpdoTraitementpdo.traitementpdo_id = Traitementpdo.id' )
				),
				array(
					'table'      => 'propospdos',
					'alias'      => 'Propopdo',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Propopdo.id = Traitementpdo.propopdo_id' )
				),
				array(
					'table'      => 'personnes',
					'alias'      => 'Personne',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						'Personne.id = Propopdo.personne_id',
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
						'Pdf.fk_value = CourrierpdoTraitementpdo.id'
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
					'Traitementpdo.traitementtypepdo_id',
					'Traitementpdo.datereception',
					'Traitementpdo.id',
					'Traitementpdo.datedepart',
					'Traitementpdo.descriptionpdo_id',
					'Traitementpdo.clos',
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