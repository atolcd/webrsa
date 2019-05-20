<?php
	/**
	 * Code source de la classe ImportcsvFrsaPositionnements93Shell.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 *
	 * Se lance avec :  sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake ImportcsvFrsaPositionnements93Shell -v -s ';' -app app app/tmp/BENEF_F_2019_03_12__13_39.csv 
	 *
	 */
	App::uses( 'CsvAbstractImporterShell', 'Csv.Console/Command/Abstract' );

	/**
	 * La classe ImportcsvFrsaPositionnements93Shell permet d'importer le catalogue PDIE de FRSA
	 * pour le module fiches de prescriptions du CG 93.
	 *
	 * @package app.Console.Command
	 */
	class ImportcsvFrsaPositionnements93Shell extends CsvAbstractImporterShell
	{
		/**
		 * Les modèles utilisés par ce shell.
		 *
		 * Il faut que ces modèles soient uniquement les modèles qui servent à
		 * l'enregistrement d'une ligne et qu'ils soient dans le bon ordre.
		 *
		 * @var array
		 */
		public $uses = array( 'Ficheprescription93');

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
				$valuePath = $path;
				$conditions[$path] = Hash::get( $row, $valuePath );
			}
			return $Model->csvUpdate( $conditions, $complement );
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
				$this->Ficheprescription93->begin();

				$data = $this->normalizeRow( $row );

				//Correction des données
				if (
					!empty(Hash::get( $data,'Ficheprescription93.id'))
					&& !is_numeric(Hash::get( $data,'Ficheprescription93.id'))
				){
					$data['Ficheprescription93']['id'] = null;
				}

				// Recherche de la fiche prescription
				if (!empty(Hash::get( $data, 'Ficheprescription93.id' ) ) && !empty(Hash::get( $data, 'Ficheprescription93.frsa_id' )) ) {
					// Recherche de l'existance du positionnement dans le deux systèmes
					$query = array(
						'fields' => array(
							'Ficheprescription93.id'
						),
						'recursive' => 0,
						'conditions' => array(
							'Ficheprescription93.id' => Hash::get( $data, 'Ficheprescription93.id' ),
							'Ficheprescription93.frsa_id' => Hash::get( $data, 'Ficheprescription93.frsa_id' )
						),
					);
					$found = $this->Ficheprescription93->find( 'all', $query );
				}elseif ( !empty(Hash::get( $data, 'Ficheprescription93.frsa_id' )) ) {
					// Recherche de l'existance du positionnement avec un FRSA ID
					$query = array(
						'fields' => array(
							'Ficheprescription93.id'
						),
						'recursive' => 0,
						'conditions' => array(
							'Ficheprescription93.frsa_id' => Hash::get( $data, 'Ficheprescription93.frsa_id' )
						),
					);
					$query['conditions'][] = 'Ficheprescription93.frsa_id IS NULL';
					$found = $this->Ficheprescription93->find( 'all', $query );
				}elseif (!empty(Hash::get( $data, 'Ficheprescription93.id' ) )){
					// Recherche de l'existance du positionnement uniquement dans webrsa sans import précédents
					$query = array(
						'fields' => array(
							'Ficheprescription93.id'
						),
						'recursive' => 0,
						'conditions' => array(
							'Ficheprescription93.id' => Hash::get( $data, 'Ficheprescription93.id' )
						),
					);
					$query['conditions'][] = 'Ficheprescription93.frsa_id IS NULL';
					$found = $this->Ficheprescription93->find( 'all', $query );
				}
				if( !empty( $found ) && count($found)<2 ) {

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

					//Traitement par model
					foreach( $this->uses as $modelName ) {
						if(  $success ) {
							$primaryKey = $this->processModel( $this->{$modelName}, $data );
							if( $primaryKey === null ) {
								$this->rejectRow( $row, $this->{$modelName} );
								$success = false;
							}
						}
					}
				}else{
					$this->rejectRow( $row, $this->Ficheprescription93, 'Id de Ficheprescription93 non trouvée / Corrompue' );
					$success = false;
				}
				if( $success ) {
					foreach( $this->uses as $modelName ) {
						$this->{$modelName}->commit();
					}
				}
				else {
					foreach( $this->uses as $modelName ) {
						$this->{$modelName}->rollback();
					}
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
				$this->_defaultHeaders = Configure::read('CSVImport.FRSA.Positionnement.Headers');
				$this->_correspondances = Configure::read('CSVImport.FRSA.Positionnement.Correspondances');
				$this->processModelDetails =  Configure::read('CSVImport.FRSA.Positionnement.ModelDetails');
			}

			parent::startup();

			$this->checkDepartement( 93 );

			$this->XProgressBar->start( $this->_Csv->count() );
		}
	}
?>