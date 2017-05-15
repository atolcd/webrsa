<?php
	/**
	 * Code source de la classe WebrsaCohorteDossierpcg66Rsamajore.
	 *
	 * @package app.Model
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Model.php.
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteDossierpcg66Rsamajore ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteDossierpcg66Rsamajore extends AbstractWebrsaCohorte
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteDossierpcg66Rsamajore';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Canton',
			'Dossierpcg66',
			'Tag',
			'WebrsaCohorteTag' // A besoin du module tag
		);
		
		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 * 
		 * @var array
		 */
		public $cohorteFields = array(
			'Personne.id' => array( 'type' => 'hidden' ),
			'Foyer.id' => array( 'type' => 'hidden' ),

			// Selection
			'Dossierpcg66.selection' => array( 'type' => 'checkbox' ),
			'Dossierpcg66.create',

			// Dossierpcg
			'Dossierpcg66.typepdo_id',
			'Dossierpcg66.datereceptionpdo' => array( 'type' => 'date' ),
			'Dossierpcg66.originepdo_id' => array( 'empty' => true ),
			'Dossierpcg66.orgpayeur',
			'Dossierpcg66.serviceinstructeur_id' => array( 'empty' => true ),
			'Dossierpcg66.haspiecejointe' => array( 'type' => 'hidden', 'value' => '0' ),
			'Dossierpcg66.commentairepiecejointe' => array( 'empty' => true, 'type' => 'textarea' ),
			'Dossierpcg66.poledossierpcg66_id' => array( 'empty' => true ),
			'Dossierpcg66.user_id' => array( 'type' => 'hidden' ),
			'Dossierpcg66.dateaffectation' => array( 
				'type' => 'date'
			),

			// Personnepcg
			'Situationpdo.Situationpdo',
			'Statutpdo.Statutpdo' => array( 'empty' => true ),

			// Traitement
			'Traitementpcg66.typetraitement' => array( 'value' => 'courrier' ),
			'Traitementpcg66.typecourrierpcg66_id' => array( 'empty' => true ),
			'Traitementpcg66.affiche_couple' => array( 'type' => 'checkbox' ),

			// Partie AJAX
			'Modeletraitementpcg66.modeletypecourrierpcg66_id' => array( 'type' => 'hidden' ),
			'Piecemodeletypecourrierpcg66.0_Piecemodeletypecourrierpcg66' => array( 'type' => 'hidden' ),
			'Piecemodeletypecourrierpcg66.1_Piecemodeletypecourrierpcg66' => array( 'type' => 'hidden' ),
			'Piecemodeletypecourrierpcg66.2_Piecemodeletypecourrierpcg66' => array( 'type' => 'hidden' ),
			'Piecemodeletypecourrierpcg66.3_Piecemodeletypecourrierpcg66' => array( 'type' => 'hidden' ),
			'Piecemodeletypecourrierpcg66.4_Piecemodeletypecourrierpcg66' => array( 'type' => 'hidden' ),
			'Piecemodeletypecourrierpcg66.5_Piecemodeletypecourrierpcg66' => array( 'type' => 'hidden' ),
			'Modeletraitementpcg66.montantdatedebut' => array( 'type' => 'hidden' ),
			'Modeletraitementpcg66.montantdatefin' => array( 'type' => 'hidden' ),
			'Modeletraitementpcg66.commentaire' => array( 'type' => 'textarea' ),

			'Traitementpcg66.haspiecejointe' => array( 'type' => 'hidden', 'value' => '0' ),
			'Traitementpcg66.serviceinstructeur_id' => array( 'empty' => true ),
			'Traitementpcg66.descriptionpdo_id' => array( 'empty' => true ),
			'Traitementpcg66.datedepart' => array( 'type' => 'date'	),
			'Traitementpcg66.datereception' => array( 'type' => 'date' ),
			'Traitementpcg66.dureeecheance',
			'Traitementpcg66.dateecheance' => array( 'type' => 'date' ),
			'Traitementpcg66.imprimer' => array( 'type' => 'checkbox' ),

			// Tag
			'EntiteTag.modele' => array( 'type' => 'hidden', 'value' => 'Foyer' ),
			'Tag.valeurtag_id',
			'Tag.etat' => array( 'options' => array( 'encours' => 'Non traité', 'traite' => 'Traité' ), 'value' => 'encours' ),
			'Tag.calcullimite' => array( 'empty' => true ),
			'Tag.limite' => array( 'type' => 'date' ),
			'Tag.commentaire' => array( 'type' => 'textarea' ),
		);
		
		/**
		 * Valeurs par défaut pour le préremplissage des champs du formulaire de cohorte
		 * array( 
		 *		'Mymodel' => array( 'Myfield' => 'MyValue' ) )
		 * )
		 * 
		 * @var array
		 */
		public $defaultValues = array();
		
		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			$query = $this->WebrsaCohorteTag->searchConditions($query, $search);
			
			return $query;
		}
		
		/**
		 * Logique de sauvegarde de la cohorte
		 * 
		 * @param type $data
		 * @param type $params
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$success = true;
			
			foreach ($data as $value) {
				// On ne traite que là où les cases sont cochés
				if ( !Hash::get($value, 'Dossierpcg66.selection') ) {
					continue;
				}
				unset($value['Dossierpcg66']['selection']);
				
				// Séparation du data par modèles
				$dataDossierpcg66 = Hash::get($value, 'Dossierpcg66');
				$dataTraitementpcg66 = Hash::get($value, 'Traitementpcg66');
				$dataModeletraitementpcg66 = Hash::get($value, 'Modeletraitementpcg66');
				$dataEntiteTag = Hash::get($value, 'EntiteTag');
				$dataTag = Hash::get($value, 'Tag');
				
				// Définition des foreign keys et renseignement des champs NOT NULL
				$dataDossierpcg66['foyer_id'] = Hash::get($value, 'Foyer.id');
				$dataDossierpcg66['etatdossierpcg'] = 'attinstr';
				$dataPersonnepcg66['personne_id'] = Hash::get($value, 'Personne.id');
				$dataPersonnepcg66['user_id'] = $user_id;
				$dataTraitementpcg66['situationpdo_id'] = Hash::get($value, 'Situationpdo.Situationpdo');
				$dataEntiteTag['fk_value'] = Hash::get($value, 'Foyer.id');
				
				// Sauvegarde Tag
				$this->Dossierpcg66->Foyer->EntiteTag->Tag->create($dataTag);
				$success = $this->Dossierpcg66->Foyer->EntiteTag->Tag->save() && $success;
				$dataEntiteTag['tag_id'] = $this->Dossierpcg66->Foyer->EntiteTag->Tag->id;
				
				$this->Dossierpcg66->Foyer->EntiteTag->create($dataEntiteTag);
				$success = $this->Dossierpcg66->Foyer->EntiteTag->save() && $success;
				
				// Si Dossierpcg66.create est à Non, on s'arrete ici (pas de création du dossier PCG)
				if ( !Hash::get($value, 'Dossierpcg66.create') ) {
					continue;
				}
				
				// Sauvegarde Dossierpcg66
				$this->Dossierpcg66->create($dataDossierpcg66);
				$success = $this->Dossierpcg66->save() && $success;
				$dossierpcg66_id = $this->Dossierpcg66->id;
				
				// Sauvegarde Personnepcg66
				$dataPersonnepcg66['dossierpcg66_id'] = $dossierpcg66_id;
				$this->Dossierpcg66->Personnepcg66->create($dataPersonnepcg66);
				$success = $this->Dossierpcg66->Personnepcg66->save() && $success;
				$personnepcg66_id = $this->Dossierpcg66->Personnepcg66->id;
				
				// Sauvegarde Personnepcg66Situationpdo
				$dataPersonnepcg66Situationpdo = array(
					'personnepcg66_id' => $personnepcg66_id,
					'situationpdo_id' => Hash::get($value, 'Situationpdo.Situationpdo')
				);
				$this->Dossierpcg66->Personnepcg66->Personnepcg66Situationpdo->create($dataPersonnepcg66Situationpdo);
				$success = $this->Dossierpcg66->Personnepcg66->Personnepcg66Situationpdo->save() && $success;
				
				// Sauvegarde Personnepcg66Statutpdo
				$dataPersonnepcg66Statutpdo = array(
					'personnepcg66_id' => $personnepcg66_id,
					'statutpdo_id' => Hash::get($value, 'Statutpdo.Statutpdo')
				);
				$this->Dossierpcg66->Personnepcg66->Personnepcg66Statutpdo->create($dataPersonnepcg66Statutpdo);
				$success = $this->Dossierpcg66->Personnepcg66->Personnepcg66Statutpdo->save() && $success;
				
				// Sauvegarde Traitementpcg66
				if ( Hash::get($value, 'Traitementpcg66.typetraitement') === 'dossierarevoir' ) {
					$dataTraitementpcg66['dossierarevoir'] = $dataModeletraitementpcg66['commentaire'];
					unset($dataModeletraitementpcg66['commentaire']);
					unset($dataTraitementpcg66['imprimer']);
					unset($dataTraitementpcg66['affiche_couple']);
				}
				$dataTraitementpcg66['personnepcg66_id'] = $personnepcg66_id;
				$this->Dossierpcg66->Personnepcg66->Traitementpcg66->create($dataTraitementpcg66);
				$success = $this->Dossierpcg66->Personnepcg66->Traitementpcg66->save() && $success;
				$traitementpcg66_id = $this->Dossierpcg66->Personnepcg66->Traitementpcg66->id;
				
				// Sauvegarde Modeletraitementpcg66
				if ( Hash::get($value, 'Traitementpcg66.typetraitement') === 'courrier' ) {
					$dataModeletraitementpcg66['traitementpcg66_id'] = $traitementpcg66_id;
					$this->Dossierpcg66->Personnepcg66->Traitementpcg66->Modeletraitementpcg66->create($dataModeletraitementpcg66);
					$success = $this->Dossierpcg66->Personnepcg66->Traitementpcg66->Modeletraitementpcg66->save();
					$modeletraitementpcg66_id = $this->Dossierpcg66->Personnepcg66->Traitementpcg66->Modeletraitementpcg66->id;

					// Sauvegarde Mtpcg66Pmtcpcg66 (Table de liaison entre Piecemodeletypecourrierpcg66 et Modeletraitementpcg66)
					foreach (Hash::flatten((array)Hash::get($value, 'Piecemodeletypecourrierpcg66')) as $piecemodeletypecourrierpcg66_id) {
						if (!$piecemodeletypecourrierpcg66_id) {
							continue;
						}

						$dataMtpcg66Pmtcpcg66 = array(
							'piecemodeletypecourrierpcg66_id' => $piecemodeletypecourrierpcg66_id,
							'modeletraitementpcg66_id' => $modeletraitementpcg66_id
						);
						$this->Dossierpcg66->Personnepcg66->Traitementpcg66->Modeletraitementpcg66->Mtpcg66Pmtcpcg66->create( $dataMtpcg66Pmtcpcg66 );
						$success = $this->Dossierpcg66->Personnepcg66->Traitementpcg66->Modeletraitementpcg66->Mtpcg66Pmtcpcg66->save() && $success;
					}
				}
				
				// Mise à jour etat du Dossier PCG
				$this->Dossierpcg66->WebrsaDossierpcg66->updatePositionsPcgsById($dossierpcg66_id);
			}
			
			return $success;
		}
		
		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Personne' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Referent' => 'LEFT OUTER',
				'DspRev' => 'LEFT OUTER',
				
				'Tag' => 'LEFT OUTER',
				'Valeurtag' => 'LEFT OUTER',
				'Categorietag' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->WebrsaCohorteTag->searchQuery($types);
				
				// Gain de perf
				App::uses('WebrsaModelUtility', 'Utility');
				$newOrder = array(
					'Situationdossierrsa', 'Foyer', 'Adressefoyer', 'Adresse'
				);
				$query = WebrsaModelUtility::changeJoinPriority($newOrder, $query);
				Cache::write($cacheKey, $query);
			}
			
			return $query;
		}
	}
?>