<?php
	/**
	 * Fichier source du modèle Transfertpdv93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Classe Transfertpdv93.
	 *
	 * @package app.Model
	 */
	class Transfertpdv93 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Transfertpdv93';

		public $actsAs = array(
			'ModelesodtConditionnables' => array(
				93 => array(
					'Transfertpdv93/mutation_emploi.odt',
					'Transfertpdv93/mutation_social.odt'
				)
			),
			'Gedooo.Gedooo',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'NvOrientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'nv_orientstruct_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'VxOrientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'vx_orientstruct_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'NvAdressefoyer' => array(
				'className' => 'Adressefoyer',
				'foreignKey' => 'nv_adressefoyer_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'VxAdressefoyer' => array(
				'className' => 'Adressefoyer',
				'foreignKey' => 'vx_adressefoyer_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Retourne le chemin relatif du modèle de document à utiliser pour
		 * l'enregistrement du PDF.
		 *
		 * @param array $data
		 * @return string
		 */
		public function modeleOdt( $data ) {
			$nv_typeorient_id = $data['NvOrientstruct']['typeorient_id'];
			$modeleodt = 'Transfertpdv93/mutation_social.odt';
			if( in_array( $nv_typeorient_id, Configure::read( 'Orientstruct.typeorientprincipale.Emploi' ) ) ) {
				$modeleodt = 'Transfertpdv93/mutation_emploi.odt';
			}
			return $modeleodt;
		}

		/**
		 * Récupère les données pour le PDF.
		 *
		 * @param integer $nvorientstruct_id
		 * @param integer $user_id (ATTENTION: pas utilisé)
		 * @return array
		 */
		public function getDataForPdf( $nvorientstruct_id, $user_id ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read($cacheKey);

			if( false === $query ) {
				$query = array(
					'fields' => array_merge(
						$this->NvOrientstruct->fields(),
						array_words_replace(
							$this->NvOrientstruct->Structurereferente->fields(),
							array(
								'Structurereferente' => 'NvStructurereferente'
							)
						),
						array_words_replace(
							$this->NvOrientstruct->NvTransfertpdv93->fields(),
							array(
								'NvTransfertpdv93' => 'Transfertpdv93'
							)
						),
						$this->NvOrientstruct->NvTransfertpdv93->User->fields(),
						$this->NvOrientstruct->NvTransfertpdv93->VxOrientstruct->fields(),
						array_words_replace(
							$this->NvOrientstruct->NvTransfertpdv93->VxOrientstruct->Structurereferente->fields(),
							array(
								'Structurereferente' => 'VxStructurereferente'
							)
						),
						$this->NvOrientstruct->Personne->fields(),
						$this->NvOrientstruct->Personne->Foyer->fields(),
						$this->NvOrientstruct->Personne->Prestation->fields(),
						$this->NvOrientstruct->Personne->Foyer->Adressefoyer->fields(),
						$this->NvOrientstruct->Personne->Foyer->Adressefoyer->Adresse->fields(),
						$this->NvOrientstruct->Personne->Foyer->Dossier->fields()
					),
					'joins' => array(
						array_words_replace(
							$this->NvOrientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
							array(
								'Structurereferente' => 'NvStructurereferente'
							)
						),
						array_words_replace(
							$this->NvOrientstruct->join( 'NvTransfertpdv93', array( 'type' => 'LEFT OUTER' ) ),
							array(
								'NvTransfertpdv93' => 'Transfertpdv93'
							)
						),
						array_words_replace(
							$this->NvOrientstruct->NvTransfertpdv93->join( 'User', array( 'type' => 'LEFT OUTER' ) ),
							array(
								'NvTransfertpdv93' => 'Transfertpdv93'
							)
						),
						array_words_replace(
							$this->NvOrientstruct->NvTransfertpdv93->join( 'VxOrientstruct', array( 'type' => 'LEFT OUTER' ) ),
							array(
								'NvTransfertpdv93' => 'Transfertpdv93'
							)
						),
						array_words_replace(
							$this->NvOrientstruct->NvTransfertpdv93->VxOrientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
							array(
								'Structurereferente' => 'VxStructurereferente'
							)
						),
						$this->NvOrientstruct->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->NvOrientstruct->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->NvOrientstruct->Personne->join( 'Prestation', array( 'type' => 'LEFT OUTER'  ) ),
						$this->NvOrientstruct->Personne->Foyer->join(
							'Adressefoyer',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Adressefoyer.id IN ( '.$this->NvOrientstruct->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
								)
							)
						),
						$this->NvOrientstruct->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$this->NvOrientstruct->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) )
					),
					'conditions' => array(
						'NvOrientstruct.origine' => 'demenagement'
					),
					'contain' => false
				);
			}

			$query['conditions']['NvOrientstruct.id'] = $nvorientstruct_id;

			return $this->NvOrientstruct->find( 'first', $query );
		}

		/**
		 * Retourne le PDF par défaut, stocké, ou généré par les appels aux méthodes getDataForPdf, modeleOdt et
		 * à la méthode ged du behavior Gedooo et le stocke,
		 *
		 * @param integer $id Id du Trasnfert réalisé
		 * @param integer $user_id Id de l'utilisateur connecté
		 * @return string
		 */
		public function getDefaultPdf( $nvorientstruct_id, $user_id ) {
			$data = $this->getDataForPdf( $nvorientstruct_id, $user_id );
			$modeleodt = $this->modeleOdt( $data );

			$options =  $this->getPdfOptions();

			return $this->ged( $data, $modeleodt, false, $options );
		}

		/**
		 * Retourne les options pour les traductions du PDF.
		 *
		 * @return array
		 */
		public function getPdfOptions() {
			$Option = ClassRegistry::init( 'Option' );

			$qual = $Option->qual();

			$options =  array(
				'Personne' => array(
					'qual' => $qual
				)
			);

			return $options;
		}

		/**
		 * Retourne un champ virtuel permettant de savoir si la date de transfert
		 * est postérieure à l'autre date (les deux dates n'étant pas nulles).
		 *
		 * @param string $dateTransfertPdvField
		 * @param string $champAutreDateField
		 * @param string $champValueField
		 * @return string
		 */
		public function vfDateAnterieureTransfert( $dateTransfertPdvField, $champAutreDateField, $champValueField ) {
			$dateTransfertPdvField = '"'.implode( '"."', explode( '.', $dateTransfertPdvField ) ).'"';
			$champAutreDateField = '"'.implode( '"."', explode( '.', $champAutreDateField ) ).'"';
			$champValueField = implode( '__', explode( '.', $champValueField ) );

			return "( ( {$dateTransfertPdvField} IS NOT NULL ) AND ( {$champAutreDateField} IS NOT NULL ) AND ( DATE_TRUNC( 'day', {$dateTransfertPdvField} ) >= DATE_TRUNC( 'day', {$champAutreDateField} ) ) ) AS \"{$champValueField}\"";
		}

		/**
		 * Ajoute un champ virtuel à un jeu de résultats permettant de savoir si
		 * la date de transfert est postérieure à l'autre date (les deux dates
		 * n'étant pas nulles).
		 *
		 * @param array $data
		 * @param string $dateTransfertPdvPath
		 * @param string $champAutreDatePath
		 * @param string $champValuePath
		 * @return array
		 */
		public function calculVfdateAnterieureTransfert( $data, $dateTransfertPdvPath, $champAutreDatePath, $champValuePath ) {
			$value = null;

			$dateTransfertPdv = Hash::get( $data, $dateTransfertPdvPath );
			$champAutreDate = Hash::get( $data, $champAutreDatePath );

			if( !is_null( $dateTransfertPdv ) && !is_null( $champAutreDate ) ) {
				$dateTransfertPdv = date( 'Y-m-d', strtotime( $dateTransfertPdv ) );
				$champAutreDate = date( 'Y-m-d', strtotime( $champAutreDate ) );
				$value = ( strtotime( $champAutreDate ) <= strtotime( $dateTransfertPdv ) );
			}

			$data = Hash::insert( $data, $champValuePath, $value );

			return $data;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les fonctions vides.
		 */
		public function prechargement() {
			$result = false !== parent::prechargement();

			try {
				$this->getDataForPdf( 0, 0 );
			} catch( Exception $e ) {
				$result = false;
			}

			return $result;
		}
	}
?>