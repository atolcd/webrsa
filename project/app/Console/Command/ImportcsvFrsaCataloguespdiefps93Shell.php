<?php
	/**
	 * Code source de la classe ImportcsvFrsaCataloguespdiefps93Shell.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 *
	 * Se lance avec : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake  ImportcsvFrsaCataloguespdiefps93 -v -s ';' -app app app/tmp/CATALOGUE_F_yyyy_mm_dd__hh_mm.csv
	 *
	 */
	App::uses( 'CsvAbstractImporterShell', 'Csv.Console/Command/Abstract' );

	/**
	 * La classe ImportcsvFrsaCataloguespdiefps93Shell permet d'importer le catalogue PDIE de FRSA
	 * pour le module fiches de prescriptions du CG 93.
	 *
	 * @package app.Console.Command
	 */
	class ImportcsvFrsaCataloguespdiefps93Shell extends CsvAbstractImporterShell
	{
		/**
		 * Les modèles utilisés par ce shell.
		 *
		 * Il faut que ces modèles soient uniquement les modèles qui servent à
		 * l'enregistrement d'une ligne et qu'ils soient dans le bon ordre.
		 *
		 * @var array
		 */
		public $uses = array( 'Thematiquefp93', 'Categoriefp93', 'Filierefp93', 'Prestatairefp93', 'Adresseprestatairefp93', 'Actionfp93' );

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
				$this->Actionfp93->begin();

				$data = $this->normalizeRow( $row );

				// Formatage des données de la ligne
				$data = Hash::insert( $data, 'Thematiquefp93.type', 'pdi' );
				$path = 'Thematiquefp93.yearthema';
				$data = Hash::insert( $data, $path, date("Y", strtotime(Hash::get( $data, $path )) ) );
				$path = 'Actionfp93.duree';
				$data = Hash::insert( $data, $path, Hash::get($data,$path)." mois" );
				$path = 'Actionfp93.numconvention';
				$data = Hash::insert( $data, $path, strtoupper( Hash::get( $data, $path ) ) );
				$path = 'Actionfp93.annee';
				$data = Hash::insert( $data, $path, date("Y", strtotime(Hash::get( $data, $path )) ) );

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
				// Recherche du numéro de convention
				$query = array(
					'fields' => array( 'Actionfp93.id' ),
					'conditions' => array(
						'Actionfp93.numconvention' => Hash::get( $data, 'Actionfp93.numconvention' )
					),
				);
				$found = $this->Actionfp93->find( 'first', $query );
				//Si le numéro de convetion existe déjà alors il faut editer
				if( !empty( $found ) ) {
					$path = 'Actionfp93.id';
					$action_id = $found['Actionfp93']['id'];
					$data = Hash::insert( $data, $path, $action_id );
				}
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
				if( $success ) {
					$this->Actionfp93->commit();
				}
				else {
					$this->Actionfp93->rollback();
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
				$this->_defaultHeaders = Configure::read('CSVImport.FRSA.CataloguePDIE.Headers');
				$this->_correspondances = Configure::read('CSVImport.FRSA.CataloguePDIE.Correspondances');
				$this->processModelDetails =  Configure::read('CSVImport.FRSA.CataloguePDIE.ModelDetails');
			}

			parent::startup();

			$this->checkDepartement( 93 );

			$this->XProgressBar->start( $this->_Csv->count() );
		}
	}
?>