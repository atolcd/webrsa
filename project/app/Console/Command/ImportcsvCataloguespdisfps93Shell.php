<?php
	/**
	 * Code source de la classe ImportcsvCataloguespdisfps93Shell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'CsvAbstractImporterShell', 'Csv.Console/Command/Abstract' );

	/**
	 * La classe ImportcsvCataloguespdisfps93Shell permet d'importer le catalogue PDI
	 * pour le module fiches de rpescriptions du CG 93.
	 *
	 * @package app.Console.Command
	 */
	class ImportcsvCataloguespdisfps93Shell extends CsvAbstractImporterShell
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
		 * Nettoyage et normalisation de la ligne d'en-tête.
		 *
		 * @param array $headers
		 * @return array
		 */
		public function processHeaders( array $headers ) {
			foreach( $headers as $key => $value ) {
				$headers[$key] = preg_replace( '/[\W_ ]+/', ' ', noaccents_upper( trim( $value ) ) );
			}

			return $headers;
		}

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
					$Linked = $Model->{$linkedModelName};
					$valuePath = "{$linkedModelName}.{$Linked->primaryKey}";
				}
				else {
					$valuePath = $path;
				}

				$conditions[$path] = Hash::get( $row, $valuePath );
			}

			return $Model->getInsertedPrimaryKey( $conditions, $complement );
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
				$path = 'Actionfp93.numconvention';
				$data = Hash::insert( $data, $path, strtoupper( Hash::get( $data, $path ) ) );

				// Recherche du numéro de convention
				$query = array(
					'fields' => array( 'Actionfp93.id' ),
					'conditions' => array(
						'Actionfp93.numconvention' => Hash::get( $data, 'Actionfp93.numconvention' )
					),
				);

				$found = $this->Actionfp93->find( 'first', $query );
				if( !empty( $found ) ) {
					$this->rejectRow( $row, $this->Actionfp93, 'N° de convention d\'action déjà présent' );
					$success = false;
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
			// Chargement des configurations liées au model ImportcsvCataloguespdisfps93
			$this->loadModel('Configuration');
			$this->Configuration->setAllConfigurations('ImportcsvCataloguespdisfps93');

			// On supprime pour ne pas avoir de problème plus tard (voir $this->uses de processRow() )
			array_pop($this->uses);

			$this->_defaultHeaders = Configure::read('CSVImport.CataloguePDI.Headers');
			$this->_correspondances = Configure::read('CSVImport.CataloguePDI.Correspondances');
			$this->processModelDetails =  Configure::read('CSVImport.CataloguePDI.ModelDetails');

			parent::startup();

			$this->checkDepartement( 93 );

			$this->XProgressBar->start( $this->_Csv->count() );
		}
	}
?>