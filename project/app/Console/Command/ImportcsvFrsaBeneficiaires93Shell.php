<?php
	/**
	 * Code source de la classe ImportcsvFrsaBeneficiaires93Shell.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 *
	 * Se lance avec :  sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake  ImportcsvFrsaBeneficiaires93 -v -s ';' -app app app/tmp/BENEF_F_2019_03_12__13_39.csv 
	 *
	 */
	App::uses( 'CsvAbstractImporterShell', 'Csv.Console/Command/Abstract' );

	/**
	 * La classe ImportcsvFrsaCataloguespdiefps93Shell permet d'importer le catalogue PDIE de FRSA
	 * pour le module fiches de prescriptions du CG 93.
	 *
	 * @package app.Console.Command
	 */
	class ImportcsvFrsaBeneficiaires93Shell extends CsvAbstractImporterShell
	{
		/**
		 * Les modèles utilisés par ce shell.
		 *
		 * Il faut que ces modèles soient uniquement les modèles qui servent à
		 * l'enregistrement d'une ligne et qu'ils soient dans le bon ordre.
		 *
		 * @var array
		 */
		public $uses = array( 'Personne','Infocontactpersonne', 'Personnelangue' , 'Personnefrsadiplomexper');

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
					$Linked = $Model->{$linkedModelName};
					$valuePath = "{$linkedModelName}.{$Linked->primaryKey}";
				}
				else {
					$valuePath = $path;
				}
				$conditions[$path] = Hash::get( $row, $valuePath );
			}
			return $Model->csvInsertUpdate($conditions, $complement );
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
				$this->Personne->begin();

				$data = $this->normalizeRow( $row );

				//Correction des données
				if (
					!empty(Hash::get( $data,'Ficheprescription93.id'))
					&& !is_numeric(Hash::get( $data,'Ficheprescription93.id'))
				){
					$data['Ficheprescription93']['id'] = null;
				}

				// Recherche de l'ID de la personne
				$query = array(
					'fields' => array(
						'Personne.id'
					),
					'recursive' => 0,
					'conditions' => array(
						'Personne.id' => Hash::get( $data, 'Personne.id' )
					),
				);

				$found = $this->Personne->find( 'all', $query );
				if( !empty( $found ) && count($found)<2) {
					/*Gestions spécifiques*/
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
					//Mise en place des id et copie des données
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
					$this->rejectRow( $row, $this->Personne, 'Id de personne non trouvée / Corrompue' );
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
				$this->_defaultHeaders = Configure::read('CSVImport.FRSA.Beneficiaire.Headers');
				$this->_correspondances = Configure::read('CSVImport.FRSA.Beneficiaire.Correspondances');
				$this->processModelDetails =  Configure::read('CSVImport.FRSA.Beneficiaire.ModelDetails');
			}

			parent::startup();

			$this->checkDepartement( 93 );

			$this->XProgressBar->start( $this->_Csv->count() );
		}
	}
?>