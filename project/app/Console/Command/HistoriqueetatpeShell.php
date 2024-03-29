<?php
	/**
	 * Code source de la classe HistoriqueetatpeShell.
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 App::uses( 'XShell', 'Console/Command' );
	 App::uses( 'ConnectionManager', 'Model' );
	 App::uses( 'CsvHelper', 'View/Helper' );
	 App::uses( 'View', 'View' );

	/**
	 * La classe HistoriqueetatpeShell permet de créer une entrée dans la table historiqueetatspe pour les personnes
	 * qui ont une entrée dans la table informationspe sans en avoir dans historiqueetatspe
	 *
	 * La commande se fait par fichier
	 *
	 * sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake Historiqueetatpe -app app [path]
	 *
	 * @package app.Console.Command
	 */
	class HistoriqueetatpeShell extends XShell
	{
		/**
		 * Modèles utilisés par ce shell
		 *
		 * @var array
		 */
		public $uses = array(
			'Informationpe',
			'Historiqueetatpe'
		 );

		/**
		 * Paramètres par défaut pour ce shell
		 *
		 * @var array
		 */
		public $defaultParams = array(
			'log' => false,
			'logpath' => LOGS,
			'verbose' => true,
		);

		/**
		 * Affiche l'en-tête du shell
		 */
		public function _welcome() {
			parent::_welcome();
		}

		/**
		 * Converti un array en document CSV
		 *
		 * @param array $data
		 * @param string $path
		 * @param string $fileName
		 */
		protected function _createCsv( $data, $path = null, $fileName ) {
			if ( empty($data) ) {
				return false;
			}

			if ( !is_dir($path) ) {
				mkdir($path, 0777, true);
			}

			// On ajoute le header
			array_unshift($data, array_keys($data[0]));

			$Csv = new CsvHelper( new View() );
			$Csv->addGrid( $data, false );
			$fileData = $Csv->render(false);

			$file = fopen($path.$fileName, "w");
			chmod($path.$fileName, 0777);
			fwrite($file, $fileData);
			fclose($file);
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			// Vérification du path passé en argument
			if (!isset($this->args[0])) {
				$path = APP.'tmp'.DS.'logs'.DS;
			} else if (substr($this->args[0], -1) !== DS) {
				$path = $this->args[0] . DS;
			} else {
				$path = $this->args[0];
			}

			// Récupération des personnes concernées
			$infospeSanshisto = $this->Informationpe->find('all', array(
				'joins' => array(
					$this->Informationpe->join('Historiqueetatpe', array('types' => 'LEFT OUTER'))
				),
				'conditions' => array(
					'Historiqueetatpe.id IS NULL'
				)
			));

			if(count($infospeSanshisto) > 0) {
				$this->out( sprintf( __d('shells', 'Shell::Historiqueetatpe::nbTraitement'), count($infospeSanshisto)) );
				$historiquepe = array();
				$personnesNonTraitees = array();
				$personnesTraitees = array();
				foreach($infospeSanshisto as $info) {
					// Récupération des informations principales
					$tmpInfo = array(
						'Historiqueetatpe' => array(
							'informationpe_id' => $info['Informationpe']['id'],
							'identifiantpe' => $info['Informationpe']['allocataire_identifiant_pe'],
							'ale' => $info['Informationpe']['allocataire_code_pe'],
							'inscription_date_debut_ide' => $info['Informationpe']['inscription_date_debut_ide'],
							'inscription_code_categorie' => $info['Informationpe']['inscription_code_categorie'],
							'inscription_lib_categorie' => $info['Informationpe']['inscription_lib_categorie'],
							'inscription_code_situation' => $info['Informationpe']['inscription_code_situation'],
							'inscription_lib_situation' => $info['Informationpe']['inscription_lib_situation'],
							'inscription_date_cessation_ide' => $info['Informationpe']['inscription_date_cessation_ide'],
							'inscription_motif_cessation_ide' => $info['Informationpe']['inscription_motif_cessation_ide'],
							'inscription_lib_cessation_ide' => $info['Informationpe']['inscription_lib_cessation_ide'],
							'inscription_date_radiation_ide' => $info['Informationpe']['inscription_date_radiation_ide'],
							'inscription_motif_radiation_ide' => $info['Informationpe']['inscription_motif_radiation_ide'],
							'inscription_lib_radiation_ide' => $info['Informationpe']['inscription_lib_radiation_ide'],
							'suivi_structure_principale_nom' => $info['Informationpe']['suivi_structure_principale_nom'],
							'suivi_structure_principale_voie' => $info['Informationpe']['suivi_structure_principale_voie'],
							'suivi_structure_principale_complement' => $info['Informationpe']['suivi_structure_principale_complement'],
							'suivi_structure_principale_code_postal' => $info['Informationpe']['suivi_structure_principale_code_postal'],
							'suivi_structure_principale_cedex' => $info['Informationpe']['suivi_structure_principale_cedex'],
							'suivi_structure_principale_bureau' => $info['Informationpe']['suivi_structure_principale_bureau'],
							'suivi_structure_deleguee_nom' => $info['Informationpe']['suivi_structure_deleguee_nom'],
							'suivi_structure_deleguee_voie' => $info['Informationpe']['suivi_structure_deleguee_voie'],
							'suivi_structure_deleguee_complement' => $info['Informationpe']['suivi_structure_deleguee_complement'],
							'suivi_structure_deleguee_code_postal' => $info['Informationpe']['suivi_structure_deleguee_code_postal'],
							'suivi_structure_deleguee_cedex' => $info['Informationpe']['suivi_structure_deleguee_cedex'],
							'suivi_structure_deleguee_bureau' => $info['Informationpe']['suivi_structure_deleguee_bureau'],
							'formation_code_niveau' => $info['Informationpe']['formation_code_niveau'],
							'formation_lib_niveau' => $info['Informationpe']['formation_lib_niveau'],
							'formation_code_secteur' => $info['Informationpe']['formation_code_secteur'],
							'formation_lib_secteur' => $info['Informationpe']['formation_lib_secteur'],
							'romev3_code_rome' => $info['Informationpe']['romev3_code_rome'],
							'romev3_lib_rome' => $info['Informationpe']['romev3_lib_rome'],
							'ppae_conseiller_pe' => $info['Informationpe']['ppae_conseiller_pe'],
							'ppae_date_signature' => $info['Informationpe']['ppae_date_signature'],
							'ppae_date_notification' => $info['Informationpe']['ppae_date_notification'],
							'ppae_axe_code' => $info['Informationpe']['ppae_axe_code'],
							'ppae_axe_libelle' => $info['Informationpe']['ppae_axe_libelle'],
							'ppae_modalite_code' => $info['Informationpe']['ppae_modalite_code'],
							'ppae_modalite_libelle' => $info['Informationpe']['ppae_modalite_libelle'],
							'ppae_date_dernier_ent' => $info['Informationpe']['ppae_date_dernier_ent'],
						)
					);

					// Ajout des informations supplémentaires
					$statut = '';

					// Infos d'inscription
					if(!empty($info['Informationpe']['inscription_date_debut_ide'])) {
						$statut = 'inscription';
						$code = $info['Informationpe']['inscription_code_categorie'];
						$motif = $info['Informationpe']['inscription_lib_categorie'];
						$date = new DateTime($info['Informationpe']['inscription_date_debut_ide']);
					}

					// Infos de cessation
					if(!empty($info['Informationpe']['inscription_date_cessation_ide'])) {
						$dateCessation = new DateTime($info['Informationpe']['inscription_date_cessation_ide']);
						if( !isset($date) || $dateCessation > $date ) {
							$date = $dateCessation;
							$statut = 'cessation';
							$code = $info['Informationpe']['inscription_motif_cessation_ide'];
							$motif = $info['Informationpe']['inscription_lib_cessation_ide'];
						}
					}

					// Infos de radiation
					if(!empty($info['Informationpe']['inscription_date_radiation_ide'])) {
						$dateRadiation = new DateTime($info['Informationpe']['inscription_date_radiation_ide']);
						if( !isset($date) || $dateRadiation > $date ) {
							$date = $dateRadiation;
							$statut = 'radiation';
							$code = $info['Informationpe']['inscription_motif_radiation_ide'];
							$motif = $info['Informationpe']['inscription_lib_radiation_ide'];
						}
					}

					// Si nous avons une information de statut nous préparons son enregistrement
					if($statut != '') {
						$historiquepe[] = array_merge(
							$tmpInfo['Historiqueetatpe'],
							array(
								'date' =>  $date->format('Y-m-d'),
								'etat' =>  $statut,
								'code' =>  $code,
								'motif' =>  $motif,
							)
						);
						$personnesTraitees[] = array_merge(
							array(
								'nom' => $info['Informationpe']['nom'],
								'prenom' => $info['Informationpe']['prenom'],
								'nir' => $info['Informationpe']['nir'],
								'dtnai' => $info['Informationpe']['dtnai']
							),
							$tmpInfo['Historiqueetatpe'],
							array(
								'date' =>  $date->format('Y-m-d'),
								'etat' =>  $statut,
								'code' =>  $code,
								'motif' =>  $motif,
							)
						);
					} else {
						// Sinon nous ajoutons les informations de la personne dans les personnes non traitées
						$personnesNonTraitees[] =  array(
							'nom' => $info['Informationpe']['nom'],
							'prenom' => $info['Informationpe']['prenom'],
							'nir' => $info['Informationpe']['nir'],
							'dtnai' => $info['Informationpe']['dtnai']
						);
					}
				}

				$doCommit = true;
				if(!empty($historiquepe)) {
					$this->Historiqueetatpe->begin();
					$success = $this->Historiqueetatpe->saveMany($historiquepe, array('atomic' => false));
				} else {
					$doCommit = false;
					$success = !empty($personnesNonTraitees);
				}

				if( $success ) {
					if($doCommit == true) {
						$this->Historiqueetatpe->commit();
						$this->out( sprintf( __d('shells', 'Shell::Historiqueetatpe::nbTraitementOK'), count($historiquepe) ) );
						$fileName = 'historiqueetatpe_personnesTraitees_' . date('Y-m-d_H:i:s') . '.csv';
						$this->_createCsv($personnesTraitees, $path, $fileName);
						$this->out( sprintf( __d('shells', 'Shell::Historiqueetatpe::CSVcree'), $path . $fileName ) );
					}

					// S'il y a des personnes non traitées par le script
					if(count($personnesNonTraitees) > 0) {
						$this->out( sprintf( __d('shells', 'Shell::Historiqueetatpe::persNonTraitee'), count($personnesNonTraitees)) );
						$fileName = 'historiqueetatpe_personnesNonTraitees_' . date('Y-m-d_H:i:s') . '.csv';
						$this->_createCsv($personnesNonTraitees, $path, $fileName);
						$this->out( sprintf( __d('shells', 'Shell::Historiqueetatpe::CSVcree'), $path . $fileName ) );
					} else {
						$this->out( __d('shells', 'Shell::Historiqueetatpe::traitementOK') );
					}
				} else {
					$this->Historiqueetatpe->rollback();
					$this->out( __d('shells', 'Shell::Historiqueetatpe::traitementNOK') );
				}
			} else {
				$this->out( __d('shells', 'Shell::Historiqueetatpe::NoPers') );
			}
		}

		/**
		 *
		 */
		public function help() {
			$this->out();
			$this->out( __d('shells', 'Shell::Historiqueetatpe::help::usage') );
			$this->out();
			$this->out( __d('shells', 'Shell::Historiqueetatpe::help::arguments') );
			$this->out( sprintf( __d('shells', 'Shell::Historiqueetatpe::help::pathInfo'), APP.'tmp'.DS.'logs'.DS ) );
			$this->out();
			$this->_stop( 0 );
		}
	}