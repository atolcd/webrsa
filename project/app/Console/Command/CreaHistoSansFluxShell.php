<?php
	/**
	 * Code source de la classe CreaHistoSansFluxShell.
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 App::uses( 'XShell', 'Console/Command' );
	 App::uses( 'ConnectionManager', 'Model' );
	 App::uses( 'View', 'View' );

	/**
	 * La classe CreaHistoSansFluxShell permet de créer l'historique des droits des personnes
	 * sans passé par les flux mais en utilisat les informations fiancière en base.
	 * Il est préférable d'utiliser ce script suite à l'utilisation du shell CreationHistoriquesdroitsShell
	 * La commande se fait par fichier
	 *
	 * sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake CreaHistoSansFlux -app app
	 *
	 * @package app.Console.Command
	 */
	class CreaHistoSansFluxShell extends XShell
	{
		public $uses = array(
			'Personne',
			'Historiquedroit',
			'Infofinanciere'
		);

		/**
		 * Renvoie la liste des personnes qui n'ont pas d'historique des droits de créer en base
		 * @return array
		 */
		public function listePersonneSansHistorique() {
			$querydataBase = array(
				'fields' => array(
					'Personne.id',
					'Calculdroitrsa.toppersdrodevorsa',
					'Situationdossierrsa.etatdosrsa',
					'Dossier.id',
					'Dossier.dtdemrsa',
				),
				'contain' => false,
				'joins' => array(
					$this->Historiquedroit->Personne->join( 'Calculdroitrsa', array( 'type' => 'LEFT OUTER' ) ),
					$this->Historiquedroit->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Historiquedroit->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$this->Historiquedroit->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$this->Historiquedroit->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'LEFT OUTER' ) ),
				),
				'conditions' => array(
					'Prestation.rolepers' => array( 'DEM', 'CJT' )
				),
				'order' => array('Personne.id'),
			);
			$querydata = $querydataBase;
			$querydataSq = array(
				'alias' => 'historiquesdroits',
				'fields' => array( 'historiquesdroits.personne_id' ),
				'contain' => false,
				'conditions' => array(
					'historiquesdroits.personne_id = Personne.id'
				)
			);
			$sq = $this->Historiquedroit->sq( $querydataSq );
			$querydata['conditions'][] = array( "Personne.id NOT IN ( {$sq} )" );
			$nbPersonnes = $this->Personne->find('count', $querydata);

			$this->out(sprintf(__d("shells", "Shells:CreaHistoSansFlux:comment::nbPersonne"), $nbPersonnes));

			return $this->Personne->find('all', $querydata);
		}

		/**
		 * Prépare les données à insérer dans la table historiquedroit
		 * @param array liste des personnes
		 * @return array tableau d'insertion
		 */
		public function traitementPersonnes($liste) {
			$datas = array();
			$key = 0;
			$now = new DateTime('NOW');
			foreach($liste as $personne) {
				$hasInfoFinanciere = false;
				$dtdemrsa = new DateTime($personne['Dossier']['dtdemrsa']);
				// On créé la personne avec son état de droit actuel et une date de création à sa date de 1ere demande RSA
				$datas[$key] = array(
					'personne_id' => $personne['Personne']['id'],
					'toppersdrodevorsa' => $personne['Calculdroitrsa']['toppersdrodevorsa'],
					'etatdosrsa' => $personne['Situationdossierrsa']['etatdosrsa'],
					'created' => $dtdemrsa->format('Y-m-d H:i:s'),
					'modified' => $now->format('Y-m-d H:i:s')
				);
				// On vérifie que la personne a des informations financières liées ou non
				$queryInfoFinanciere = array(
					'fields' => array(
						'Infofinanciere.ddregu',
						'Infofinanciere.moismoucompta'
					),
					'recursive' => -1,
					'conditions' => array(
						'Infofinanciere.dossier_id' => $personne['Dossier']['id'],
						'Infofinanciere.type_allocation' => 'AllocationsComptabilisees'
					),
					'order' => array('Infofinanciere.id')
				);
				$infosfinancieres = $this->Infofinanciere->find('all', $queryInfoFinanciere);
				if(isset($infosfinancieres) && !empty($infosfinancieres)) {
					$hasInfoFinanciere = true;
					$nouvelInfo = true;
					foreach($infosfinancieres as $info) {
						// Vérification de la date d'info financière à récupérer
						if($info['Infofinanciere']['ddregu']) {
							$dateInfo = $info['Infofinanciere']['ddregu'];
						} else {
							$dateInfo = $info['Infofinanciere']['moismoucompta'];
						}
						if($nouvelInfo) {
							$nouvelInfo = false;
							$date = new DateTime($dateInfo);
							$datas[$key]['modified'] = $date->format('Y-m-d H:i:s');
							$key++;
							$datas[$key] = array(
								'personne_id' => $personne['Personne']['id'],
								'toppersdrodevorsa' => '1',
								'etatdosrsa' => '2',
								'created' => $date->format('Y-m-d H:i:s'),
								'modified' => $date->format('Y-m-d H:i:s')
							);
							$twoMonths = new DateTime($dateInfo);
							$twoMonths = $twoMonths->add(new DateInterval('P2M1D'));
						} else {
							$dtencours = new DateTime($dateInfo);
							// Si dateInfo est supérieur à dateCreated + 2 mois
							// On crée une nouvelle data
							// Sinon on met à jour le modified
							if( $dtencours > $twoMonths ) {
								$ancienneDate = $datas[$key]['modified'];
								$key++;
								$date = new DateTime($dateInfo);
								$datas[$key] = array(
									'personne_id' => $personne['Personne']['id'],
									'toppersdrodevorsa' => '1',
									'etatdosrsa' => '4',
									'created' => $ancienneDate,
									'modified' => $date->format('Y-m-d H:i:s')
								);
								$key++;
								$datas[$key] = array(
									'personne_id' => $personne['Personne']['id'],
									'toppersdrodevorsa' => '1',
									'etatdosrsa' => '2',
									'created' => $date->format('Y-m-d H:i:s'),
									'modified' => $date->format('Y-m-d H:i:s')
								);
							} else {
								$date = new DateTime($dateInfo);
								$datas[$key]['modified'] = $date->format('Y-m-d H:i:s');
							}
							$twoMonths = new DateTime($dateInfo);
							$twoMonths = $twoMonths->add(new DateInterval('P2M1D'));
						}
					}
				}
				// Si on a eu des info financiere on cree un nouvelle data avec creation à la derniere date + modified now
				if($hasInfoFinanciere) {
					$key++;
					$date = new DateTime($dateInfo);
					$datas[$key] = array(
						'personne_id' => $personne['Personne']['id'],
						'toppersdrodevorsa' => $personne['Calculdroitrsa']['toppersdrodevorsa'],
						'etatdosrsa' => $personne['Situationdossierrsa']['etatdosrsa'],
						'created' => $date->format('Y-m-d H:i:s'),
						'modified' => $now->format('Y-m-d H:i:s')
					);
				}
				$key++;
			}
			return $datas;
		}

		public function main() {
			$timestart = microtime(true);
			$this->out(__d("shells", "Shells:CreaHistoSansFlux:comment::debutRecup"));
			$listePersonne = $this->listePersonneSansHistorique();
			$this->out(sprintf(__d("shells", "Shells:CreaHistoSansFlux:comment::finRecup"), number_format(microtime(true) - $timestart, 3)));

			$this->out(__d("shells","Shells:CreaHistoSansFlux:comment::debutTraitement") );
			$timestart = microtime(true);
			$dataToInsert = $this->traitementPersonnes($listePersonne);
			$this->out(sprintf(__d("shells", "Shells:CreaHistoSansFlux:comment::FinTraitement"), number_format(microtime(true) - $timestart, 3)));

			$this->out(__d("shells", "Shells:CreaHistoSansFlux:comment::debutInsert"));
			$timestart = microtime(true);
			if($this->Historiquedroit->saveAll($dataToInsert)) {
				$this->out(sprintf(__d("shells", "Shells:CreaHistoSansFlux:comment::insertOK"), number_format(microtime(true) - $timestart, 3) ) );
			} else {
				$this->out(__d("shells", "Shells:CreaHistoSansFlux:comment::insertNOK"));
				$errors = $this->Historiquedroit->validationErrors;
				foreach($errors as $key => $error) {
					debug($error);
					debug($dataToInsert[$key]);
				}
			}
		}
	}
