<?php
	/**
	 * Code source de la classe ImportCsvFRSAAutoPosfps93Shell.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 *
	 * Se lance avec : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake ImportcsvFrsaAutoposfps93 -v -s ';' -app app app/tmp/AUTOPOS_F_2019_03_12__13_39.csv  
	 *
	 */
	App::uses( 'CsvAbstractImporterShell', 'Csv.Console/Command/Abstract' );

	/**
	 * La classe ImportCsvFRSAAutoPosfps93Shell permet d'importer le catalogue PDI
	 * pour le module fiches de rpescriptions du CG 93.
	 *
	 * @package app.Console.Command
	 */
	class ImportcsvFrsaAutoposfps93Shell extends CsvAbstractImporterShell
	{
		/**
		 * Les modèles utilisés par ce shell.
		 *
		 * Il faut que ces modèles soient uniquement les modèles qui servent à
		 * l'enregistrement d'une ligne et qu'ils soient dans le bon ordre.
		 *
		 * @var array
		 */
		public $uses = array(
			'Thematiquefp93', 'Categoriefp93', 'Filierefp93', 'Prestatairefp93', 'Adresseprestatairefp93', 'Actionfp93',
			'Personne','Infocontactpersonne', 'Personnelangue' , 'Personnefrsadiplomexper',
			'Ficheprescription93'
		);

		/**
		 * Les tâches utilisées par ce shell.
		 *
		 * @var array
		 */
		public $tasks = array( 'XProgressBar' );

		/**
		 * Les en-têtes par défaut tels qu'ils sont attendus.
		 *
		 * @var array
		 */
		protected $_defaultHeaders = array();

		/**
		 * Tableau de correspondances entre les en-têtes et des chemins de
		 * modèles CakePHP.
		 *
		 * @var array
		 */
		protected $_correspondances = array();

		/**
		 * Les chemins de données et données complémentaires pour chacun des
		 * modèles nécessaires à la méthode processModel().
		 *
		 * @var array
		 */
		public $processModelDetails = array();

		/**
		 * Nettoyage des valeurs des champs (suppression des espaces excédentaires)
		 * et transformation des clés via les correspondances.
		 *
		 * @param array $row
		 * @return array
		 */
		public function normalizeRow( array $row ) {
			$new = array();

			foreach( $row as $key => $value ) {
				if( isset( $this->_correspondances[$key] ) ) {
					$new = Hash::insert(
						$new,
						$this->_correspondances[$key],
						trim( preg_replace( '/[ ]+/', ' ', $value ) )
					);
				}
			}

			return $new;
		}

		/**
		 * Traitement d'une ligne du fichier CSV pour un modèle donné.
		 *
		 * @param Model $Model
		 * @param array $row
		 * @return type
		 */
		public function processModel( Model $Model, array $row ) {
			$paths = (array)Hash::get( $this->processModelDetails, "{$Model->alias}.paths" );
			$complement = (array)Hash::get( $this->processModelDetails, "{$Model->alias}.complement" );
			$conditions = array();

			foreach( $paths as $path ) {
				list( , $fieldName ) = model_field( $path );

				// Si c'est une clé étrangère, le chemin sera celui de la clé primaire du modèle associé.
				if( preg_match( '/_id$/', $fieldName ) ) {
					$linkedModelName = Inflector::classify( preg_replace( '/_id$/', '', $fieldName ) );
					if ( $linkedModelName != 'Frsa' ) {
						$Linked = $Model->{$linkedModelName};
						$valuePath = "{$linkedModelName}.{$Linked->primaryKey}";
					} else {
						$valuePath = $path;
					}
				} else {
					$valuePath = $path;
				}

				$conditions[$path] = Hash::get( $row, $valuePath );
			}

			$path = 'Actionfp93.id';
			if ( $Model->alias == 'Actionfp93' && !empty(Hash::get( $row, $path )) ){
				$conditions[$path] = Hash::get( $row, $path );
			}
			return $Model->getInsertedUpdatedPrimaryKey( $conditions, $complement );
		}

		/**
		 * Traitement d'une ligne de données du fichier CSV.
		 *
		 * @param array $row
		 * @return boolean
		 */
		public function processRow( array $row ) {
			$success = true;

			if( empty( $row ) ) {
				$this->empty[] = $row;
			}
			else {
				//Préparation des Modeles
				$this->Actionfp93->begin();
				$this->Personne->begin();
				$this->Ficheprescription93->begin();

				//Récupération des données de la Ligne
				$data = $this->normalizeRow( $row );

				//Correction des données
				if (
					!empty(Hash::get( $data,'Ficheprescription93.id'))
					&& !is_numeric(Hash::get( $data,'Ficheprescription93.id'))
				){
					$data['Ficheprescription93']['id'] = null;
				}

				// I. Verification de l'existance ou Abscence de valeur obligatoires
				// I.1 Recherche de l'abscence de l'ID du positionnement F-RSA dans Webrsa.
				$query = array(
					'fields' => array('Ficheprescription93.id'),
					'recursive' => 0,
					'conditions' => array(
						'Ficheprescription93.frsa_id' => Hash::get( $data, 'Ficheprescription93.frsa_id' )
					),
				);
				$foundFP93 = $this->Ficheprescription93->find( 'all', $query );

				// I.2 Recherche de l'ID de la personne
				$query = array(
					'fields' => array('Personne.id'),
					'recursive' => 0,
					'conditions' => array(
						'Personne.id' => Hash::get( $data, 'Personne.id' )
					),
				);
				$foundPers = $this->Personne->find( 'all', $query );

				//Si la personne n'existe pas ou n'est pas unique et/ou que la FP n'existe pas
				//on refuse la ligne, SINON GO
				if(empty($foundFP93) && !empty( $foundPers ) && count($foundPers)<2) {

					//II Formatage des données de la ligne
					//II.1)Section Personne
					//II.1.1 Gestions des Booleans
					foreach ( $data as  $table => $contenu ) {
						foreach ( $contenu as  $key => $value ) {
								if ($value=='') {
									$data[$table][$key]=NULL;
								}elseif($value=='true'){
									$data[$table][$key]=1;
								}elseif($value=='false'){
									$data[$table][$key]=0;
								}
							}
					}
					//II.1.2 Copie des données d'informations pour historisation et des identifiants de personne
					$path = 'Personnelangue.personne_id';
					$data = Hash::insert( $data, $path,Hash::get( $data, 'Personne.id' ) );
					$path = 'Personnefrsadiplomexper.personne_id';
					$data = Hash::insert( $data, $path,Hash::get( $data, 'Personne.id' ) );
					$path = 'Infocontactpersonne.personne_id';
					$data = Hash::insert( $data, $path,Hash::get( $data, 'Personne.id' ) );
					$path = 'Infocontactpersonne.fixe';
					$data = Hash::insert( $data, $path,Hash::get( $data, 'Personne.numfixe' ) );
					$path = 'Infocontactpersonne.mobile';
					$data = Hash::insert( $data, $path,Hash::get( $data, 'Personne.numport' ) );
					$path = 'Infocontactpersonne.email';
					$data = Hash::insert( $data, $path,Hash::get( $data, 'Personne.email' ) );

					//2) Section Catalogue
					//II.2.1 Verification des valeurs
					$data = Hash::insert( $data, 'Thematiquefp93.type', 'pdi' );
					$path = 'Thematiquefp93.yearthema';
					$data = Hash::insert( $data, $path, date("Y", strtotime(Hash::get( $data, $path )) ) );
					$path = 'Actionfp93.duree';
					$data = Hash::insert( $data, $path, Hash::get($data,$path)." mois" );
					$path = 'Actionfp93.numconvention';
					$data = Hash::insert( $data, $path, strtoupper( Hash::get( $data, $path ) ) );
					$path = 'Actionfp93.annee';
					$data = Hash::insert( $data, $path, date("Y", strtotime(Hash::get( $data, $path )) ) );
					//II.2.2 Verification de l'encodage UTF-8
					$arraypath = array (
						'Actionfp93.name',
						'Prestatairefp93.name',
						'Filierefp93.name',
						'Categoriefp93.name',
						'Thematiquefp93.name',
						'Adresseprestatairefp93.adresse',
						'Adresseprestatairefp93.localite'
					);
					foreach ($arraypath AS $path ) {
						$str = Hash::get( $data, $path );
						$encoding = mb_detect_encoding($str, 'UTF-8', true);
						if ($encoding != 'UTF-8' ) {
							// Check string encode
							$str = mb_convert_encoding ($str, 'UTF-8');
							$data = Hash::insert( $data, $path, $str );
						}
					}
					//II.2.3 Recherche de l'existance du numéro de convention
					$query = array(
						'fields' => array( 'Actionfp93.id' ),
						'conditions' => array(
							'Actionfp93.numconvention' => Hash::get( $data, 'Actionfp93.numconvention' )
						),
					);
					$found = $this->Actionfp93->find( 'first', $query );
					//II.2.4 Si le numéro de convetion existe déjà alors il faut editer
					if( !empty( $found ) ) {
						$path = 'Actionfp93.id';
						$action_id = $found['Actionfp93']['id'];
						$data = Hash::insert( $data, $path, $action_id );
					}

					//3) Section fiche prescription
					//Rien pour l'instant

					//III Traitement des models
					foreach( $this->uses as $modelName ) {
						if(  $success ) {
							$primaryKey = $this->processModel( $this->{$modelName}, $data );
							if( $primaryKey === null ) {
								$this->rejectRow( $row, $this->{$modelName} );
								$success = false;
							}
							else {
								$data = Hash::insert( $data, "{$modelName}.id", $primaryKey );
							}
						}
					}
				}else{
					$this->rejectRow( $row, $this->Personne, 'Id de personne non trouvée / Corrompue' );
					$success = false;
				}

				if( $success ) {
					$this->Actionfp93->commit();
					$this->Personne->commit();
					$this->Ficheprescription93->commit();
				}
				else {
					$this->Actionfp93->rollback();
					$this->Personne->rollback();
					$this->Ficheprescription93->rollback();
				}
			}

			$this->XProgressBar->next();
			return $success;
		}

		/**
		 * Surcharge de la méthode startup pour vérifier que le département soit
		 * uniquement le 93 et démarrage de la barre de progression.
		 */
		public function startup() {
			// Chargement du fichier de configuration lié, s'il existe
			$department=Configure::read('Cg.departement');
			$path = APP.'Config'.DS.'Cg'.$department.DS.'ImportCSVFRSA.php';
			if( file_exists( $path ) ) {
				include_once $path;
				$this->_defaultHeaders = Configure::read('CSVImport.FRSA.AutoPositionnement.Headers');
				$this->_correspondances = Configure::read('CSVImport.FRSA.AutoPositionnement.Correspondances');
				$this->processModelDetails =  Configure::read('CSVImport.FRSA.AutoPositionnement.ModelDetails');	
			}

			parent::startup();

			$this->checkDepartement( 93 );

			$this->XProgressBar->start( $this->_Csv->count() );
		}
	}
?>