<?php
	/**
	 * Code source de la classe AdresseCantonShell.
	 *
	 * @package app.Console.Command
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Shell.php.
	 */
	 App::uses( 'XShell', 'Console/Command' );
	 App::uses( 'ConnectionManager', 'Model' );
	 App::uses( 'Csv', 'Helper' );
	 App::uses( 'View', 'View' );
	 
	/**
	 * La classe AdresseCantonShell permet d'obtenir les correspondances entre les personne_id 
	 * de différents dossiers selon le nom/prenom/dtnai/nir
	 *
	 * @package app.Console.Command
	 */
	class AdresseCantonShell extends XShell
	{
		/**
		 * La constante à utiliser dans la méthode _stop() en cas de succès.
		 */
		const SUCCESS = 0;

		/**
		 * La constante à utiliser dans la méthode _stop() en cas d'erreur.
		 */
		const ERROR = 1;

		/**
		 * Initialisation: lecture des paramètres, on s'assure d'avoir une connexion
		 * valide
		 */
		public function startup() {
			parent::startup();
			try {
				$this->connection = ConnectionManager::getDataSource( $this->params['connection'] );
			}
			catch( Exception $e ) {

			}
		}

		/**
		 * Lignes de bienvenue.
		 */
		protected function _welcome() {
			parent::_welcome();
		}
		
		/**
		 * Méthode principale.
		 */
		public function main() {
			$Adresse = ClassRegistry::init( 'Adresse' );
			$Canton = ClassRegistry::init( 'Canton' );
			$departement = Configure::read('Cg.departement');

			$this->out();
			$this->out('Recherche des correspondances entre adresses et cantons...');
			$timestart = microtime(true);
			$query = array(
				'fields' => array(
					'Adresse.id',
					'Adresse.complete',
					'Canton.id',
				),
				'joins' => array(
					$Canton->joinAdresse()
				),
				'conditions' => array(
					"Adresse.codepos LIKE '{$departement}%'"
				),
				'contain' => false
			);
			$results = $Adresse->find('all', $query);
			$this->out(sprintf('Terminé en %s secondes.', number_format(microtime(true)-$timestart, 3)));
			
			$Adresse->begin();
			$Dbo = $Adresse->AdresseCanton->getDataSource();
			
			$this->out();
			$this->out('Supression du contenu de la table de liaison...');
			$timestart = microtime(true);

			$success = $Adresse->AdresseCanton->query( sprintf( "DELETE FROM %s", $Dbo->fullTableName( $Adresse->AdresseCanton ) ) ) !== false;
			$this->out(sprintf('Terminé en %s secondes.', number_format(microtime(true)-$timestart, 3)));

			// On extrait les Adresse.id lorsque le canton n'a pas été trouvé et on prépare la sauvegarde
			$noCanton = array( array( 'Adresse.id', 'Adresse.complete' ) );
			$data = array();
			foreach ( $results as $key => $value ) {
				if ( !Hash::get($value, 'Canton.id') ) {
					$noCanton[] = array(
						'adresse_id' => Hash::get($value, 'Adresse.id'),
						'complete' => trim( preg_replace('/[\s]+/', ' ', Hash::get($value, 'Adresse.complete')) ),
					);
				}
				else {
					$data[] = array(
						'adresse_id' => Hash::get($value, 'Adresse.id'),
						'canton_id' => Hash::get($value, 'Canton.id'),
					);
				}
			}
			
			if ( !empty($data) && $success ) {
				$this->out();
				$this->out('Création du contenu de la table de liaison...');
				$timestart = microtime(true);
				$success = $success && $Adresse->AdresseCanton->saveMany($data);
				$this->out(sprintf('Terminé en %s secondes.', number_format(microtime(true)-$timestart, 3)));
			}
			
			if ( $success ) {
				$Adresse->commit();
				
				$dirPath = APP.'tmp'.DS.'logs'.DS;
				$fileName = 'adresses_sans_cantons_'.date('Y-m-d_H:i:s').'.csv';
				
				$this->out();
				$this->out('Création du fichier de rapport CSV...');
				$timestart = microtime(true);
				$this->_createCsv( $noCanton, $dirPath, $fileName );
				$this->out(sprintf('Terminé en %s secondes.', number_format(microtime(true)-$timestart, 3)));

				$this->out();

				$this->out(sprintf('%s Cantons ont été trouvés et sauvegardés.', count($data)));
				$this->out(sprintf('%s Cantons n\'ont pas été trouvés, la liste se situe à %s.', count($noCanton), $dirPath.$fileName));

				$this->out();
			}
			else {
				$Adresse->rollback();
				$this->out('Un erreur s\'est produite !');
			}
		}
		
		/**
		 * Converti un array en document CSV
		 * 
		 * @param array $data
		 * @param string $path
		 * @param string $fileName
		 */
		protected function _createCsv( $data, $path = null, $fileName = null ) {
			if ( empty($data) ) {
				return false;
			}
			if ( $path === null ) {
				$path = APP.'tmp'.DS.'logs'.DS;
			}
			if ( $fileName === null ) {
				$fileName = 'adresses_sans_cantons_'.date('Y-m-d_H:i:s').'.csv';
			}
			if ( !is_dir($path) ) {
				mkdir($path, 0777, true);
			}
			
			App::import('Helper', 'Csv');
			App::import('View', 'View');
			$Csv = new CsvHelper( new View() );
			$Csv->addGrid( $data, false );
			$fileData = $Csv->render(false);
			
			$file = fopen($path.$fileName, "w");
			chmod($path.$fileName, 0777);
			fwrite($file, $fileData);
			fclose($file);
		}
		
		/**
		 * Paramétrages et aides du shell.
		 */
		public function getOptionParser() {
			$Parser = parent::getOptionParser();
			return $Parser;
		}
	}
?>