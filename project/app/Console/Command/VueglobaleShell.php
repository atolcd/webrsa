<?php
	/**
	 * Code source de la classe VueglobaleShell.
	 *
	 * PHP 7.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 *
	 * Se lance avec :  sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake Vueglobale -app app [Filepath]
	 *
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::uses('CakeEmail', 'Network/Email');

	/**
	 * La classe VueglobaleShell permet de créer un XML contenant
	 * la liste des bénéficiaire pour le projet Vue Globale du 66
	 *
	 * @package app.Console.Command
	 */
	class VueglobaleShell extends XShell
	{
		/**
		 * Modèles utilisés par ce shell
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Personne',
			'Historiquedroit'
		);

		/**
		 * Affiche l'en-tête du shell
		 */
		public function _welcome() {
			$this->out();
			$this->out( 'Shell de création XML contenant la liste des bénéficiaire pour le projet Vue Globale' );
			$this->out();
			$this->hr();
		}

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
		 * Calcul de la clé du NIR si besoin
		 *	@param string
		 *	@return string
		*/
		private function _calcul_cle_nir($nir) {
			$correction = 0;
			$cle = '';
			if(preg_match('/^[0-9]{6}(A|B|[0-9])[0-9]{6}$/', $nir) === 0) {
				return null;
			}

			if(preg_match('/^.{6}(A|B)/', $nir) == 1) {
				if(preg_match('/^.{6}A/', $nir) == 1) {
					$correction = 1000000;
				} else {
					$correction = 2000000;
				}
				$nir = preg_replace('/(A|B)/', '0', $nir);
			}
			$cle = str_pad( strval( 97- ( (intval($nir) - $correction) % 97 )), 2, '0' );
			return $cle;
		}

		/**
		 * Renvoi la query de base pour la récupération des personnes
		 * avec leur rendez-vous
		 * @return string
		 */
		private function _query() {
			return "WITH personnesTotal AS (
				SELECT
					DISTINCT ON (Personne.id) Personne.id,
					Personne.qual,
					Personne.nomnai,
					Personne.nom,
					Personne.prenom,
					Personne.dtnai,
					Personne.nir,
					Personne.numfixe,
					Personne.numport,
					Dossier.matricule,
					Dossier.id AS DossierID,
					Adresse.numvoie,
					Adresse.libtypevoie,
					Adresse.nomvoie,
					Adresse.compladr,
					Adresse.codepos,
					Adresse.nomcom,
					Personne.email,
					Structurereferente.lib_struc
				FROM
					personnes AS Personne
					INNER JOIN foyers AS Foyer ON (Personne.foyer_id = Foyer.id)
					INNER JOIN dossiers AS Dossier ON (Foyer.dossier_id = Dossier.id)
					INNER JOIN calculsdroitsrsa AS Calculdroitrsa ON (Calculdroitrsa.personne_id = Personne.id)
					INNER JOIN situationsdossiersrsa Situationdossierrsa ON (Situationdossierrsa.dossier_id = Dossier.id)
					INNER JOIN prestations AS Prestation ON (Prestation.personne_id = Personne.id AND
				 Prestation.natprest = 'RSA' AND Prestation.rolepers IN ('DEM'))
					INNER JOIN adressesfoyers AS Adressefoyer ON (Adressefoyer.foyer_id = Foyer.id AND
				 Adressefoyer.id IN(
						SELECT
							adressesfoyers.id AS adressesfoyers__id
						FROM
							adressesfoyers AS adressesfoyers
						WHERE
							adressesfoyers.foyer_id = Foyer.id
							AND adressesfoyers.rgadr = '01'
						ORDER BY
							adressesfoyers.dtemm DESC
						LIMIT 1
					))
					INNER JOIN adresses AS Adresse ON (Adressefoyer.adresse_id = Adresse.id)
					INNER JOIN orientsstructs AS Orientstruct ON (Personne.id = Orientstruct.personne_id)
					INNER JOIN structuresreferentes AS Structurereferente ON (Orientstruct.structurereferente_id = Structurereferente.id)
					ORDER BY Personne.id
				),
				rendezvousTotal AS (
				SELECT
					Rendezvous.personne_id,
					Rendezvous.daterdv,
					Rendezvous.heurerdv,
					Typerdv.libelle AS typerdv_lib,
					Statutrdv.libelle AS statutrdv_lib,
					Permanence.libpermanence,
					ReferentRDV.nom AS ReferentRDVNom,
					ReferentRDV.prenom AS ReferentRDVPrenom,
					ReferentRDV.fonction AS ReferentRDVFonction
				FROM rendezvous AS Rendezvous
					LEFT OUTER JOIN typesrdv AS Typerdv ON (Rendezvous.typerdv_id = Typerdv.id)
					LEFT OUTER JOIN statutsrdvs AS Statutrdv ON (Rendezvous.statutrdv_id = Statutrdv.id)
					LEFT OUTER JOIN permanences AS Permanence ON (Rendezvous.permanence_id = Permanence.id)
					LEFT OUTER JOIN referents AS ReferentRDV ON (Rendezvous.referent_id = ReferentRDV.id)
				WHERE Rendezvous.daterdv > (current_date - INTERVAL '24 months')
				 ORDER BY Rendezvous.personne_id, Rendezvous.daterdv, Rendezvous.heurerdv
				 )
			SELECT * FROM personnesTotal LEFT OUTER JOIN rendezvousTotal ON (personnesTotal.id = rendezvousTotal.personne_id);";
		}

		/**
		 * Traite les résultats de la première requête pour avoir en clé l'ID de la personne
		 * @return array
		 */
		private function _getResultSQL() {
			$query = $this->_query();
			$personnes = $this->Personne->query($query);
			$results = array();
			foreach($personnes as $personne) {
				$results[$personne[0]['id']] = $personne[0];
			}
			return $results;
		}

		/**
		 * Récupère le dernier historique d'une personne
		 * @param array
		 * @return array
		 */
		private function _getHisto($idPersonnes) {
			$query = array(
				'fields' => array(
					'Historiquedroit.etatdosrsa',
					'Historiquedroit.created',
					'Historiquedroit.modified'
				),
				'recursive' => -1,
				'conditions' => array('Historiquedroit.personne_id IN' => $idPersonnes),
				'order' => array('Historiquedroit.created DESC'),
				'limit' => 1
			);
			$histo = $this->Historiquedroit->find('first', $query);
			return $histo['Historiquedroit'];
		}

		/**
		 * Récupère tous les id lié à une personne selon son nom / prénom / date de naissance
		 * puis sur le NIR
		 * @param array
		 * @return array
		 */
		private function _getDoublon($data) {
			$conditions = array();
			if(isset($data['nir']) && !empty($data['nir'])) {
				if( strlen( trim($data['nir']) ) == 13 ) {
					$cle = $this->_calcul_cle_nir(trim($data['nir']));
					if ($cle != null) {
						$nir = trim($data['nir']) . $cle;
					}
				} else {
					$nir = $data['nir'];
				}
				$conditions = array(
					'OR' => array(
						array(
							'Personne.nom' => $data['nom'],
							'Personne.prenom' => $data['prenom'],
							'Personne.dtnai' => $data['dtnai']
						),
						array(
							'Personne.nir' => substr($nir, 0, 13)
						),
						array(
							'Personne.nir' => $nir
						)
					)
				);
			} else {
				$conditions = array(
					'Personne.nom' => $data['nom'],
					'Personne.prenom' => $data['prenom'],
					'Personne.dtnai' => $data['dtnai']
				);
			}

			$personnes = $this->Personne->find('list', array(
				'conditions' => $conditions
			));

			$liste = array();
			foreach($personnes as $id => $pers) {
				$liste[] = $id;
			}
			return $liste;
		}

		/**
		 * Récupère les référent par personneId
		 * @param array
		 * @return array
		 */
		private function _getReferents($listeId) {
			$results = $this->Personne->query(
				'SELECT
					DISTINCT ON (Personne.id) Personne.id,
					Dossier.id,
					Referentparcours.nom,
					Referentparcours.prenom,
					Referentparcours.fonction
				FROM
					personnes AS Personne
					INNER JOIN foyers AS Foyer ON (Personne.foyer_id = Foyer.id)
					INNER JOIN dossiers AS Dossier ON (Foyer.dossier_id = Dossier.id)
					LEFT OUTER JOIN personnes_referents AS PersonneReferent ON (PersonneReferent.personne_id = Personne.id AND (
						(PersonneReferent.id IS NULL) OR (PersonneReferent.id IN (
							SELECT
								personnes_referents.id
							FROM
								personnes_referents
							WHERE
								personnes_referents.personne_id = Personne.id
								AND personnes_referents.dfdesignation IS NULL
							ORDER BY
								personnes_referents.dddesignation DESC, personnes_referents.id DESC
							LIMIT 1 )
							)
						)
					)
					LEFT OUTER JOIN referents AS Referentparcours ON (PersonneReferent.referent_id = Referentparcours.id)
				WHERE Personne.id IN (' . implode(',', $listeId) . ');'
			);
			$referents = array();
			foreach($results as $result) {
				$result = $result[0];
				if(empty($result['nom']) && isset($referents[$result['id']]) && !empty($referents[$result['id']]) ) {
					continue;
				} else if( !isset($referents[$result['id']]) && empty($result['nom'])) {
					$referents[$result['id']] = array();
				} else {
					$referents[$result['id']] = array(
						'nom' => $result['nom'],
						'prenom' => $result['prenom'],
						'fonction' => $result['fonction']
					);
				}
			}

			return $referents;
		}

		/**
		 * Traite les aides par personnes
		 * @param array
		 * @return array
		 */
		private function _traitementAides($data, $listeId) {
			// Récupération des infos financière de la personne
			$Infofinanciaires = ClassRegistry::init('Infofinanciere');
			$infos = $Infofinanciaires->find('all', array(
				'fields' => array(
					'Infofinanciere.dossier_id',
					'Infofinanciere.moismoucompta',
					'Infofinanciere.ddregu'
				),
				'recursive' => -1,
				'joins' => array(
					$Infofinanciaires->join('Dossier'),
					$Infofinanciaires->Dossier->join('Foyer'),
					$Infofinanciaires->Dossier->Foyer->join('Personne')
				),
				'conditions' => array(
					'Personne.id IN' => $listeId,
					'Infofinanciere.type_allocation' => 'AllocationsComptabilisees',
					'Infofinanciere.natpfcre IN' => array('RSD', 'RSI')
				),
				'order' => array('Infofinanciere.moismoucompta')
			));

			// S'il n'y pas d'infos on retourne un tableau vide
			if(empty($infos)) {
				return array();
			}

			// Gestion du référent
			$referents = $this->_getReferents($listeId);

			// Initialisation des variables de base
			$aides = array();
			$datedebutdroit = '';

			foreach($infos as $info) {
				$info = $info['Infofinanciere'];
				// Initialisation de la date début de doit et calcul du mois suivant
				if($datedebutdroit == '') {
					$datedebutdroit = $info['moismoucompta'];
					$datedebut1mois = new DateTime($info['moismoucompta']);
					$datedebut1mois->add(new DateInterval('P1M'));
					if(isset($referents[$info['dossier_id']]) && !empty($referents[$info['dossier_id']]) ) {
						$referent = array(
							'nomReferent' => $referents[$info['dossier_id']]['nom'],
							'prenomReferent' => $referents[$info['dossier_id']]['prenom'],
							'fonctionReferent' => $referents[$info['dossier_id']]['fonction'],
						);
					} else {
						$referent = array();
					}
					$tmpaide = array_merge(
						array(
						'nature' => 'RSA',
						'datePremiereAttribution' => $datedebutdroit,
						'dateFinDroits' => ''
						),
						$referent
					);
					$dtencours = new DateTime($info['moismoucompta']);
					$twoMonths = new DateTime($info['moismoucompta']);
					$twoMonths = $twoMonths->sub(new DateInterval('P2M1D'));
				} else if($dtencours != new DateTime($info['moismoucompta']) ){
					// S'il y a une regul
					if( isset($info['ddregu']) && !empty($info['ddregu']) ) {
						$isRegul = true;
					} else {
						$isRegul = false;
					}
					// S'il y a une date de régulation inférieur à la date en cours
					if( $isRegul && $dtencours < new DateTime($info['ddregu']) ) {
						$dtencours = new DateTime($info['ddregu']);
						$twoMonths = new DateTime($info['ddregu']);
						$twoMonths = $twoMonths->sub(new DateInterval('P2M1D'));
					}
					// Si la date du mois comptable est la même que la date en cours
					// ou qu'il y a une date de régul inférieur ou égal à la date en cours on passe au suivant
					else if(new DateTime($info['moismoucompta']) == $dtencours || ($isRegul && new DateTime($info['ddregu']) <= $dtencours ) ){
						continue;
					} else {
						$dtencours = new DateTime($info['moismoucompta']);
						$twoMonths = new DateTime($info['moismoucompta']);
						$twoMonths = $twoMonths->sub(new DateInterval('P2M1D'));
					}

					$interval = $dtencours->diff($datedebut1mois);
					$twoMonthsInterval = $dtencours->diff($twoMonths);

					// Si la différence entre la date en cours et la date précédente + 1 mois
					// est supérieur à 2 mois on met une date de fin de droit
					if($interval->days > $twoMonthsInterval->days) {
						$tmpaide['dateFinDroits'] = $datedebut1mois->format('Y-m-d');
						$aides[] = $tmpaide;

						if( $isRegul && new DateTime($info['moismoucompta']) > new DateTime($info['ddregu']) ) {
							$datedebutdroit = $info['ddregu'];
							$datedebut1mois = new DateTime($info['ddregu']);
						} else {
							$datedebutdroit = $info['moismoucompta'];
							$datedebut1mois = new DateTime($info['moismoucompta']);
						}

						// Récupération du référent de la personne
						if(isset($referents[$info['dossier_id']]) && !empty($referents[$info['dossier_id']]) ) {
							$referent = array(
								'nomReferent' => $referents[$info['dossier_id']]['nom'],
								'prenomReferent' => $referents[$info['dossier_id']]['prenom'],
								'fonctionReferent' => $referents[$info['dossier_id']]['fonction'],
							);
						} else {
							$referent = array();
						}

						$datedebut1mois->add(new DateInterval('P1M'));

						// On réinitialise le tableau
						$tmpaide = array_merge(
							array(
							'nature' => 'RSA',
							'datePremiereAttribution' => $datedebutdroit,
							'dateFinDroits' => ''
							),
							$referent
						);
					} else {
						if( $isRegul
						&& new DateTime($info['ddregu']) > $datedebut1mois) {
							$datedebut1mois = new DateTime($info['ddregu']);
						} else {
							$datedebut1mois->add(new DateInterval('P1M'));
						}
					}
				}
			}
			// Vérification de la dernière date
			$keysInfo = array_keys($infos);
			$lastKeyInfo = end($keysInfo);
			$now = new DateTime();
			$interval = $now->diff(new DateTime($infos[$lastKeyInfo]['Infofinanciere']['moismoucompta']));

			$twoMonths = new DateTime();
			$twoMonths = $twoMonths->sub(new DateInterval('P2M1D'));
			$twoMonthsInterval = $now->diff($twoMonths);
			// Si la différence entre aujourd'hui et la dernière prestation est supérieur à 2 mois
			// on met une date de fin
			if( $interval->days > $twoMonthsInterval->days ) {
				$tmpaide['dateFinDroits'] = $datedebut1mois->format("Y-m-d");
			}
			$aides[] = $tmpaide;

			return $aides;
		}

		/**
		 * Récupère et prépare le tableau pour la création du XML
		 * @return array
		 */
		private function _traitementSql() {
			$results = $this->_getResultSQL();
			$arrayXml = array();
			$passe24mois = new DateTime('today -24 months');

			foreach($results as $result) {
				/* -- Partie Bénificiaire -- */

				// Gestion des doublons sur Nom / Prenom / Date de naissance
				$listeIDPersonne = $this->_getDoublon($result);
				sort($listeIDPersonne);
				$lastId = end($listeIDPersonne);

				// Balise actif
				$dernierHisto = $this->_getHisto($listeIDPersonne);

				if(new DateTime($dernierHisto['created']) > $passe24mois && !in_array($dernierHisto['etatdosrsa'], array('5', '6') ) ) {
					$actif = 1;
				} else {
					$actif = 0;
				}

				// Modification du nom avec dernier nom connu si différent (cas mariage)
				if( isset($results[$lastId]['nom']) && !empty($results[$lastId]['nom']) && ($result['nom'] != $results[$lastId]['nom'])) {
					$result['nom'] = $results[$lastId]['nom'];
				}

				// Balise nom naissance
				if(!isset($result['nomnai'])) {
					$result['nomnai'] = $result['nom'];
				}

				// Balise adresse
				$adressecomplete = '';
				if( isset($results[$lastId]['numvoie']) && !empty($results[$lastId]['numvoie']) ){
					$adressecomplete .= $results[$lastId]['numvoie'];
				}

				if( isset($results[$lastId]['libtypevoie']) && !empty($results[$lastId]['libtypevoie']) ){
					$adressecomplete .= ' ' . $results[$lastId]['libtypevoie'];
				}

				if( isset($results[$lastId]['nomvoie']) && !empty($results[$lastId]['nomvoie']) ){
					$adressecomplete .= ' ' . $results[$lastId]['nomvoie'];
				}

				if( isset($results[$lastId]['compladr']) && !empty($results[$lastId]['compladr']) ){
					$adressecomplete .= ' ' . $results[$lastId]['compladr'];
				}

				// Balise téléphone
				$tel = array();
				if( isset($result['numfixe']) && !empty($result['numfixe']) ) {
					$tel[] = preg_replace('/[^\d](?=\d)/', '', $result['numfixe']);
				}
				if( isset($result['numport']) && !empty($result['numport']) ) {
					$tel[] = preg_replace('/[^\d](?=\d)/', '', $result['numport']);
				}

				// NIR
				if( strlen( trim($result['nir']) ) == 13 ) {
					$cle = $this->_calcul_cle_nir(trim($result['nir']));
					if ($cle != null) {
						$nir = trim($result['nir']) . $cle;
					} else {
						$nir = '';
					}
				} else if( strlen( trim($result['nir']) ) == 15 ) {
					$nir = trim($result['nir']);
				} else {
					$nir = '';
				}

				// Matricule
				if(isset($result['matricule'])) {
					$result['matricule'] = trim($result['matricule']);
				}

				// Email
				if(isset($result['email']) && !empty($result['email'])) {
					$email = $result['email'];
				} else {
					$email = '';
				}

				$arrayXml[$lastId]['Beneficiaire'] = array(
					'actif' => $actif,
					'flagSortie' => 'N',
					'genre' => $result['qual'],
					'nomNaissance' => $result['nomnai'],
					'nomUsage' => $result['nom'],
					'prenom' => $result['prenom'],
					'dateNaissance' => $result['dtnai'],
					'codeNir' => $nir,
					'codeCaf' => $result['matricule'],
					'adresseComplete' => $adressecomplete,
					'codePostal' => $result['codepos'],
					'ville' => $result['nomcom'],
					'mspRattachement' => $result['lib_struc'],
					'telephone' => $tel,
					'mail' => $email
				);
				/* -- Fin partie Bénificiaire -- */

				// Aides
				$arrayXml[$lastId]['Aides'] = $this->_traitementAides($result, $listeIDPersonne);

				// RDV
				if( isset($arrayXml[$result['personne_id']])
					&& !empty($arrayXml[$result['personne_id']]) )
				{
					$arrayXml[$lastId]['RDVs'][] = array(
						'typeRdv' => $result['typerdv_lib'],
						'dateRdv' => $result['daterdv'],
						'heureRdv' => $result['heurerdv'],
						'etatRdv' => $result['statutrdv_lib'],
						'lieuRdv' => $result['libpermanence'],
						'nomIntervenant' => $result['referentrdvnom'],
						'prenomIntervenant' => $result['referentrdvprenom'],
						'fonctionIntervenant' => $result['referentrdvfonction']
					);
				}
			}
			return $arrayXml;
		}

		/**
		 * Ecrit la totalité du XML Vue Globale
		 * @param array
		 * @param string
		 * @return bool
		 */
		private function _ecritureXml($listePersonnes, $file) {
			$now = new DateTime();
			// Création  du XML
			$xml= new XMLWriter();
			$success = $xml->openUri($file);
			if(!$success) {
				return $success;
			}
			$xml->startDocument('1.0', 'ISO-8859-15');
			$xml->startElement('VueGlobale');
			$xml->writeAttribute('application', 'WebRSA');
			$xml->writeAttribute('date_extraction', $now->format('Y-m-d'));
			foreach($listePersonnes as $personne) {
				$xml->startElement('Dossier');
				// Début Beneficiaire
				$xml->startElement('Beneficiaire');
				$xml->writeElement('actif',$personne['Beneficiaire']['actif']);
				$xml->writeElement('flagSortie',$personne['Beneficiaire']['flagSortie']);
				$xml->writeElement('genre',$personne['Beneficiaire']['genre']);
				$xml->writeElement('nomNaissance',$personne['Beneficiaire']['nomNaissance']);
				$xml->writeElement('nomUsage',$personne['Beneficiaire']['nomUsage']);
				$xml->writeElement('prenom',$personne['Beneficiaire']['prenom']);
				$xml->writeElement('dateNaissance',$personne['Beneficiaire']['dateNaissance']);
				$xml->writeElement('codeNir',$personne['Beneficiaire']['codeNir']);
				$xml->writeElement('codeCaf',$personne['Beneficiaire']['codeCaf']);

				// Début Adresse
				$xml->startElement('adresses');
				$xml->startElement('adresse');
				$xml->writeElement('adresseComplete',$personne['Beneficiaire']['adresseComplete']);
				$xml->writeElement('codePostal',$personne['Beneficiaire']['codePostal']);
				$xml->writeElement('ville',$personne['Beneficiaire']['ville']);
				$xml->endElement();
				$xml->endElement();
				// Fin Adresse

				// Structure référente (msp)
				$xml->writeElement('mspRattachement',$personne['Beneficiaire']['mspRattachement']);

				// Téléphones
				if(isset($personne['Beneficiaire']['telephone']) && !empty($personne['Beneficiaire']['telephone'])) {
					$xml->startElement('Telephones');
					foreach($personne['Beneficiaire']['telephone'] as $tel) {
						$xml->startElement('Telephone');
						$xml->writeElement('NumTel',$tel);
						$xml->endElement();
					}
					$xml->endElement();
				}
				// Fin Téléphones

				// Mail
				if($personne['Beneficiaire']['mail'] != '') {
					$xml->startElement('Mails');
					$xml->startElement('Mail');
					$xml->writeElement('AdresseMail',$personne['Beneficiaire']['mail']);
					$xml->endElement();
					$xml->endElement();
				}
				// Fin Mail

				$xml->endElement();
				// Fin Bénificiaire

				// Début Aides
				if(isset($personne['Aides']) && !empty($personne['Aides'])) {
					$xml->startElement('Aides');
					foreach($personne['Aides'] as $aide) {
						$xml->startElement('Aide');
						$xml->writeElement('nature', $aide['nature']);
						$xml->writeElement('datePremiereAttribution', $aide['datePremiereAttribution']);
						if($aide['dateFinDroits'] != '') {
							$xml->writeElement('dateFinDroits', $aide['dateFinDroits']);
						}
						// Noeud Référent
						if(isset($aide['nomReferent']) && !empty($aide['nomReferent'])) {
							$xml->startElement('Referents');
							$xml->startElement('Referent');
							$xml->writeElement('nomReferent', $aide['nomReferent']);
							$xml->writeElement('prenomReferent', $aide['prenomReferent']);
							$xml->writeElement('fonctionReferent', $aide['fonctionReferent']);
							$xml->endElement();
							$xml->endElement();
						}
						$xml->endElement();
					}
					$xml->endElement();
				}
				// Fin Aides

				// Début RDV
				if(isset($personne['RDVs']) && !empty($personne['RDVs'])) {
					$xml->startElement('RDVs');
					foreach($personne['RDVs'] as $rdv) {
						$xml->startElement('rdv');
						$xml->writeElement('typeRdv', $rdv['typeRdv']);
						$xml->writeElement('dateRdv', $rdv['dateRdv']);
						$xml->writeElement('heureRdv', $rdv['heureRdv']);
						$xml->writeElement('etatRdv', $rdv['etatRdv']);
						$xml->writeElement('lieuRdv', $rdv['lieuRdv']);

						// Noeud Référent
						$xml->startElement('Intervenants');
						$xml->startElement('Intervenant');
						$xml->writeElement('nomIntervenant', $rdv['nomIntervenant']);
						$xml->writeElement('prenomIntervenant', $rdv['prenomIntervenant']);
						$xml->writeElement('fonctionIntervenant', $rdv['fonctionIntervenant']);
						$xml->endElement();
						$xml->endElement();
						$xml->endElement();
					}
					$xml->endElement();
				}
				// Fin du dossier
				$xml->endElement();
			}
			$xml->endElement();
			$xml->endDocument();
			$octets = $xml->flush();
			if($octets == 0) {
				$success = true;
			} else {
				$success = false;
			}
			return $success;
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			// Vérification du path passé en argument
			if(!isset($this->args[0])) {
				$this->out( __d('shells', 'Shells:Vueglobale:error::path') );
				exit();
			} else if(substr($this->args[0], -1) !== '/') {
				$path = $this->args[0] . '/';
			} else {
				$path = $this->args[0];
			}

			// Vérification que l'écriture est possible dans le dossier spécifié
			if(file_put_contents($path . 'test', '') === false) {
				$this->out( __d('shells', 'Shells:Vueglobale:error::rightPath') );
				exit();
			} else {
				unlink($path . 'test');
			}

			// Traitement SQL
			$this->out( __d('shells', 'Shells:Vueglobale:comment::debutSQLPersonne') );
			$timestart = microtime(true);
			$listeDonnee = $this->_traitementSql();
			$this->out(sprintf( __d('shells', 'Shells:Vueglobale:comment::finTraitement'), number_format(microtime(true)-$timestart, 3)));

			// Suppression d'un ancien XML si la commande a été exécuté deux fois
			$this->out();
			$this->out( __d('shells', 'Shells:Vueglobale:comment::suppressionXML'));
			$timestart = microtime(true);

			// Récupération de la date du jour pour nom fichier & attribut date_extraction
			$now = new DateTime();
			$file = $path . 'xml_webrsa_' . $now->format('Y-m-d') . '.xml';
			if(file_exists($file)) {
				unlink($file);
			}
			$this->out(sprintf( __d('shells', 'Shells:Vueglobale:comment::finTraitement'), number_format(microtime(true)-$timestart, 3)));

			// Ecriture du fichier XML
			$this->out();
			$this->out( __d('shells', 'Shells:Vueglobale:comment::debutXML') );
			$timestart = microtime(true);
			$success = $this->_ecritureXml($listeDonnee, $file . '.tmp');
			if($success) {
				$nombrePersonne = count($listeDonnee);
				$this->out(sprintf( __d('shells', 'Shells:Vueglobale:comment::finXML') ,
					number_format(microtime(true)-$timestart, 3), $nombrePersonne)
				);
			} else {
				$this->out( __d('shells', 'Shells:Vueglobale:error::errorXML') );
				exit();
			}

			// Formatage du XML pour l'avoir indenté
			$this->out();
			$this->out( __d('shells', 'Shells:Vueglobale:comment::debutFormatXML') );
			$timestart = microtime(true);
			shell_exec('xmllint -format -recover ' . $file . '.tmp > ' . $file);
			unlink($file . '.tmp');
			$sz = 'BKMGTP'; // correspond à Bytes, Kilobytes, etc.
			$bytes = filesize($file);
			$factor = floor((strlen($bytes) - 1) / 3);
			$this->out(sprintf( __d('shells', 'Shells:Vueglobale:comment::finFormatXMLPremPartie') . @$sz[$factor] . __d('shells', 'Shells:Vueglobale:comment::finFormatXMLDeuxPartie'),
				number_format(microtime(true)-$timestart, 3),
				$bytes / pow(1024, $factor)
			));

			// Test et récupération du XML
			if(isset($this->args[1]) && $this->args[1] == 'log'){
				$this->out();
				$this->out("Ecriture du log commencé");
				$this->logXML($file);
				$this->out("Ecriture du log terminé");
			}
		}

		/**
		 * Pour récupérer en log les personnes si besoin
		 * @param string
		 */
		public function logXML($xml) {
			$all = simplexml_load_file($xml);
			$file = fopen('/tmp/persVueglobale', 'a');
			foreach($all->Dossier as $dossier) {
				$strFile = $dossier->Beneficiaire->genre . ' ' . $dossier->Beneficiaire->nomUsage . ' ' . $dossier->Beneficiaire->prenom . ' ' . $dossier->Beneficiaire->codeNir . PHP_EOL;
				fwrite($file, $strFile);
			}
			fclose($file);
		}
  }