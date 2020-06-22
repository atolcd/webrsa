<?php
	/**
	 * Code source de la classe AdresseCantonShell.
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 App::uses( 'XShell', 'Console/Command' );
	 App::uses( 'ConnectionManager', 'Model' );
	 App::uses( 'CsvHelper', 'View/Helper' );
	 App::uses( 'View', 'View' );

	/**
	 * La classe AdresseCantonShell permet d'obtenir les correspondances entre les personne_id
	 * de différents dossiers selon le nom/prenom/dtnai/nir
	 *
	 * sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake AdresseCanton -app app
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
					'DISTINCT Adresse.id',
					'Adresse.complete',
					'Adresse.nomvoie',
					'Adresse.codepos',
					'Adresse.libtypevoie',
					'Adresse.nomcom',
					'Adresse.numcom',
					'Adresse.numvoie',
					'Canton.id',
				),
				'joins' => array(
					$Canton->joinAdresse(),
					$Adresse->join('Adressefoyer', array('type' => 'LEFT OUTER'))
				),
				'conditions' => array(
					"Adresse.codepos LIKE '{$departement}%'",
					"Adresse.nomcom IS NOT NULL",
					"Adresse.nomcom <> ''",
					"Adressefoyer.rgadr = '01'"
				),
				'contain' => false,
				'order' => array('Adresse.complete ASC')
			);
			$results = $Adresse->find('all', $query);
			$this->out(sprintf('Terminé en %s secondes.', number_format(microtime(true)-$timestart, 3)));

			$Dbo = $Adresse->AdresseCanton->getDataSource();

			$this->out();
			$this->out('Suppression du contenu de la table de liaison...');
			$timestart = microtime(true);
			$success = $Canton->query( "DELETE FROM public.cantons WHERE canton IS NULL;");
			$success = $Adresse->AdresseCanton->query( "TRUNCATE TABLE " . $Dbo->fullTableName( $Adresse->AdresseCanton ) ) && $success;
			$this->out(sprintf('Terminé en %s secondes.', number_format(microtime(true)-$timestart, 3)));

			// On extrait les Adresse.id lorsque le canton n'a pas été trouvé et on prépare la sauvegarde
			$noCanton = array( array( 'Adresse.id', 'Adresse.complete' ) );
			$data = array();
			$valAdresse = '';
			$Adresse->begin();
			foreach ( $results as $key => $value ) {
				if ( !Hash::get($value, 'Canton.id') ) {
					$numcom = Hash::get($value, 'Adresse.numcom');
					$cantonMulti = Configure::read('Canton.multi');
					if( strcmp(Hash::get($value, 'Adresse.complete'),$valAdresse) != 0 && (empty($cantonMulti) ||
					 !empty($cantonMulti) && in_array($numcom, $cantonMulti) ) ) {
						$noCanton[] = array(
							'adresse_id' => Hash::get($value, 'Adresse.id'),
							'complete' => trim( preg_replace('/[\s]+/', ' ', Hash::get($value, 'Adresse.complete')) ),
						);
						$valAdresse = Hash::get($value, 'Adresse.complete');
					}
				}
				else {
					$data[] = array(
						'adresse_id' => Hash::get($value, 'Adresse.id'),
						'canton_id' => Hash::get($value, 'Canton.id'),
					);
				}
			}

			if ( !empty($data) && $success ) {
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

			if( $success && Configure::read('Canton.InsertionAuto.enabled')) {
				$this->out('Ajout des adresses sans canton dans la table adresse...');
				$timestart = microtime(true);
				$nbCanton = $this->_insertionCanton($Canton, $Adresse, $departement, $results);
				$this->out(sprintf('Terminé en %s secondes, %s canton(s) ont été ajoutés.', number_format(microtime(true)-$timestart, 3),$nbCanton));
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

			$Csv = new CsvHelper( new View() );
			$Csv->addGrid( $data, false );
			$fileData = $Csv->render(false);

			$file = fopen($path.$fileName, "w");
			chmod($path.$fileName, 0777);
			fwrite($file, $fileData);
			fclose($file);
		}

		protected function _insertionCanton($canton, $adresse, $departement, $datas) {
			$noCantonData = array();

			$valAdresse = '';
			foreach($datas as $value) {
				if ( !Hash::get($value, 'Canton.id') ) {
					$zoneID = $canton->Zonegeographique->find('first', array(
						'fields' => array('id'),
						'recursive' => -1,
						'conditions' => array(
							'OR' => array(
								'Zonegeographique.codeinsee' => Hash::get($value, "Adresse.numcom"),
								'Zonegeographique.libelle LIKE' => Hash::get($value, 'Adresse.nomcom')
								)
							)
						)
					);

					if(!empty($zoneID)) {
						$zoneID = $zoneID['Zonegeographique']['id'];
						$cantonMulti = Configure::read('Canton.multi');
						if(strcmp(Hash::get($value, 'Adresse.complete'),$valAdresse) != 0 &&
						 !empty($cantonMulti) && in_array(Hash::get($value, "Adresse.numcom"), $cantonMulti) ) {
							$noCantonData[] = array(
								'nomvoie' => Hash::get($value, "Adresse.nomvoie"),
								'codepos' => Hash::get($value, "Adresse.codepos"),
								'canton' => '',
								'zonegeographique_id' => $zoneID,
								'libtypevoie' => Hash::get($value, "Adresse.libtypevoie"),
								'nomcom' => Hash::get($value, "Adresse.nomcom"),
								'numcom' => Hash::get($value, "Adresse.numcom"),
								'numvoie' => Hash::get($value, "Adresse.numvoie")
							);
						} elseif(strcmp(Hash::get($value, 'Adresse.complete'),$valAdresse) != 0 && empty($cantonMulti) ) {
							$noCantonData[] = array(
								'nomvoie' => Hash::get($value, "Adresse.nomvoie"),
								'codepos' => Hash::get($value, "Adresse.codepos"),
								'canton' => '',
								'zonegeographique_id' => $zoneID,
								'libtypevoie' => Hash::get($value, "Adresse.libtypevoie"),
								'nomcom' => Hash::get($value, "Adresse.nomcom"),
								'numcom' => Hash::get($value, "Adresse.numcom"),
								'numvoie' => ''
							);
						}
						$valAdresse = Hash::get($value, 'Adresse.complete');
					}
				}
			}
			$canton->begin();

			// Suppression de la validation du nom de canton exceptionnellement
			unset($canton->validate['canton']);
			$success = $canton->saveMany($noCantonData);

			if(!$success) {
				$this->out($canton->validationErrors);
				$canton->rollback();
				return 0;
			} else {
				$canton->commit();
			}
			return count($noCantonData);
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