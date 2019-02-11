<?php
	/**
	 * Code source de la classe Decisionpropopdo.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Decisionpropopdo ...
	 *
	 * @package app.Model
	 */
	class Decisionpropopdo extends AppModel
	{
		public $name = 'Decisionpropopdo';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Allocatairelie' => array(
				'joins' => array( 'Propopdo' )
			),
			'Gedooo.Gedooo',
			'StorablePdf' => array(
				'active' => 66
			),
			'ModelesodtConditionnables' => array(
				66 => array(
					'PDO/propositiondecision.odt',
				)
			),
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'decisionpdo_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'champ obligatoire'
				)
			)
		);


		public $belongsTo = array(
			'Propopdo' => array(
				'className' => 'Propopdo',
				'foreignKey' => 'propopdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Decisionpdo' => array(
				'className' => 'Decisionpdo',
				'foreignKey' => 'decisionpdo_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);



		public function beforeSave( $options = array() ) {
			$return = parent::beforeSave( $options );

			if( Configure::read( 'nom_form_pdo_cg' ) == 'cg66' ) {
				$decisionpdo_id = Set::extract( $this->data, 'Decisionpropopdo.decisionpdo_id' );
				$validationdecision = Set::extract( $this->data, 'Decisionpropopdo.validationdecision' );

				$etat = null;

				$decisionpdo = $this->Decisionpdo->find(
					'first',
					array(
						'conditions' => array(
							'Decisionpdo.id' => $decisionpdo_id
						),
						'contain' => false
					)
				);

				if ( isset( $decisionpdo['Decisionpdo']['clos'] ) ) {
					if ( !empty( $decisionpdo_id ) && !is_numeric( $validationdecision ) )
						$etat = 'attval';
					elseif ( !empty( $decisionpdo_id ) && is_numeric( $validationdecision ) && $validationdecision == '1' && $decisionpdo['Decisionpdo']['clos'] == 'O' )
						$etat = 'dossiertraite';
					elseif ( !empty( $decisionpdo_id ) && is_numeric( $validationdecision ) && ( $validationdecision == '0' || $decisionpdo['Decisionpdo']['clos'] == 'N' ) )
						$etat = 'instrencours';

					$this->data['Decisionpropopdo']['etatdossierpdo'] = $etat;
				}
				else {
					$return = false;
				}
			}

			return $return;
		}

		/**
		* Récupère les données pour le PDf
		*/

		public function getDataForPdf( $id ) {
			// TODO: error404/error500 si on ne trouve pas les données
			$optionModel = ClassRegistry::init( 'Option' );
			$qual = $optionModel->qual();
			$services = $this->Propopdo->Serviceinstructeur->find( 'list' );
			$typestraitements = $this->Propopdo->Traitementpdo->Traitementtypepdo->find( 'list' );
			$descriptionspdos = $this->Propopdo->Traitementpdo->Descriptionpdo->find( 'list' );
			$conditions = array( 'Decisionpropopdo.id' => $id );

			$joins = array(
				array(
					'table'      => 'propospdos',
					'alias'      => 'Propopdo',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Propopdo.id = Decisionpropopdo.propopdo_id' )
				),
				array(
					'table'      => 'decisionspdos',
					'alias'      => 'Decisionpdo',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => array( 'Decisionpdo.id = Decisionpropopdo.decisionpdo_id' )
				),
				array(
					'table'      => 'traitementspdos',
					'alias'      => 'Traitementpdo',
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'Propopdo.id = Traitementpdo.propopdo_id',
						'Traitementpdo.id IN(
							'.$this->Propopdo->Traitementpdo->sq(
								array(
									'alias' => 'traitementspdos',
									'fields' => array( 'traitementspdos.id' ),
									'conditions' => array(
										'traitementspdos.propopdo_id = Propopdo.id'
									),
									'order' => array( 'traitementspdos.id DESC' ),
									'limit' => 1
								)
							).'
						)'
					)
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
						'Pdf.fk_value = Decisionpropopdo.id'
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
					'Propopdo.referent_id',
					'Propopdo.orgpayeur',
					'Propopdo.datereceptionpdo',
					'Propopdo.serviceinstructeur_id',
					'Decisionpropopdo.datedecisionpdo',
					'Decisionpdo.libelle',
					'Decisionpropopdo.commentairepdo',
					'Decisionpropopdo.avistechnique',
					'Decisionpropopdo.dateavistechnique',
					'Decisionpropopdo.commentaireavistechnique',
					'Decisionpropopdo.validationdecision',
					'Decisionpropopdo.datevalidationdecision',
					'Decisionpropopdo.commentairedecision',
					'Traitementpdo.traitementtypepdo_id',
					'Traitementpdo.datereception',
					'Traitementpdo.id',
					'Traitementpdo.datedepart',
					'Traitementpdo.descriptionpdo_id',
					'Traitementpdo.clos'
				),
				'joins' => $joins,
				'conditions' => $conditions,
				'contain' => false
			);

			$data = $this->find( 'first', $queryData );

			$data['Personne']['qual'] = Set::enum( $data['Personne']['qual'], $qual );
			$data['Propopdo']['serviceinstructeur_id'] = Set::enum( $data['Propopdo']['serviceinstructeur_id'], $services );
			$data['Decisionpropopdo']['validationdecision'] = $data['Decisionpropopdo']['validationdecision'] ? 'Oui' : 'Non';

			return $data;
		}

		/**
		* Retourne le chemin relatif du modèle de document à utiliser pour l'enregistrement du PDF.
		*/

		public function modeleOdt( $data ) {
			return "PDO/propositiondecision.odt";
		}
	}
?>