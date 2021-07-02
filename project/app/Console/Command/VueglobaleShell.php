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
App::uses('XShell', 'Console/Command');
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
	 * Attribut date permettant de savoir si le bénéficiaire a été actif sur les 24 derniers mois
	*/
	private $passe24mois;

	/**
	 * Liste des états de dossier RSA permettant l'ouverture d'une aide
	*/
	private $droitOuvert = array('2');

	/**
	 * Liste des états de dossier RSA cloturant une aide
	 */
	private $droitClos = array('5', '6');

	/**
	 * Modèles utilisés par ce shell
	 *
	 * @var array
	 */
	public $uses = array(
		'Allocataire',
		'Personne',
		'Historiquedroit',
		'Structurereferente'
	);

	/**
	 * Affiche l'en-tête du shell
	 */
	public function _welcome() {
		$this->out();
		$this->out('Shell de création XML contenant la liste des bénéficiaire pour le projet Vue Globale');
		$this->out();
		$this->hr();
	}

	/**
	 * Initialisation: lecture des paramètres, on s'assure d'avoir une connexion
	 * valide
	 */
	public function startup() {
		parent::startup();
		// Initialisation de la date de -24 mois
		$this->passe24mois = new DateTime('today -24 months');
		try {
			$this->connection = ConnectionManager::getDataSource($this->params['connection']);
		} catch (Exception $e) {
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
		if (preg_match('/^[0-9]{6}(A|B|[0-9])[0-9]{6}$/', $nir) === 0) {
			return null;
		}

		if (preg_match('/^.{6}(A|B)/', $nir) == 1) {
			if (preg_match('/^.{6}A/', $nir) == 1) {
				$correction = 1000000;
			} else {
				$correction = 2000000;
			}
			$nir = preg_replace('/(A|B)/', '0', $nir);
		}
		$cle = str_pad(strval(97 - ((intval($nir) - $correction) % 97)), 2, '0');
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
					Dossier.numdemrsa,
					Dossier.matricule,
					Dossier.id AS DossierID,
					Adresse.numvoie,
					Adresse.libtypevoie,
					Adresse.nomvoie,
					Adresse.compladr,
					Adresse.codepos,
					Adresse.nomcom,
					Personne.email,
					Prestation.rolepers
				FROM
					personnes AS Personne
					INNER JOIN foyers AS Foyer ON (Personne.foyer_id = Foyer.id)
					INNER JOIN dossiers AS Dossier ON (Foyer.dossier_id = Dossier.id)
					INNER JOIN calculsdroitsrsa AS Calculdroitrsa ON (Calculdroitrsa.personne_id = Personne.id)
					INNER JOIN situationsdossiersrsa Situationdossierrsa ON (Situationdossierrsa.dossier_id = Dossier.id)
					INNER JOIN prestations AS Prestation ON (Prestation.personne_id = Personne.id AND Prestation.natprest = 'RSA')
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
	 * Supprime les doublons dans tableaux multidimensionnels
	 * Voir https://www.php.net/manual/fr/function.array-unique.php pour plus d'informations
	 * @param array
	 * @param string
	 *
	 * @return array
	 */
	function unique_multidim_array($array, $key) {
		$temp_array = array();
		$i = 0;
		$key_array = array();

		foreach($array as $val) {
			if (!in_array($val[$key], $key_array)) {
				$key_array[$i] = $val[$key];
				$temp_array[$i] = $val;
			}
			$i++;
		}
		return $temp_array;
	}

	/**
	 * Traite les résultats de la première requête pour avoir en clé l'ID de la personne
	 * @return array
	 */
	private function _getResultSQL() {
		$query = $this->_query();
		$personnes = $this->Personne->query($query);
		$results = array();

		foreach ($personnes as $personne) {
			if (!isset($results[$personne[0]['id']])) {
				$results[$personne[0]['id']] = $personne[0];
			}

			if (isset($personne[0]['personne_id'])) {
				$results[$personne[0]['id']]['RDVs'][] = array(
					'typeRdv' => $personne[0]['typerdv_lib'],
					'dateRdv' => $personne[0]['daterdv'],
					'heureRdv' => $personne[0]['heurerdv'],
					'etatRdv' => $personne[0]['statutrdv_lib'],
					'lieuRdv' => $personne[0]['libpermanence'],
					'nomIntervenant' => $personne[0]['referentrdvnom'],
					'prenomIntervenant' => $personne[0]['referentrdvprenom'],
					'fonctionIntervenant' => $personne[0]['referentrdvfonction']
				);
			}
		}

		return $results;
	}

	/**
	 * Récupère toutes les personnes en lien avec l'ID passé en paramètre
	 * @param int
	 * @param array
	 * @return array
	 */
	function _getPersonneLien($idPersonne, $infoPersonne) {
		// Récupération du foyer
		$foyer = $this->Personne->find('first', array(
			'fields' => array(
				'Personne.foyer_id'
			),
			'recursive' => -1,
			'conditions' => array('Personne.id' => $idPersonne)
			)
		);
		$foyer_id = $foyer['Personne']['foyer_id'];

		// Récupération des personnes vivant sous le même foyer
		$query = array(
			'fields' => array(
				'Personne.id',
				'Personne.qual',
				'Personne.nomnai',
				'Personne.nom',
				'Personne.prenom',
				'Personne.dtnai',
				'Personne.nir',
				'Personne.numfixe',
				'Personne.numport',
				'Dossier.matricule',
				'Dossier.id',
				'Adresse.numvoie',
				'Adresse.libtypevoie',
				'Adresse.nomvoie',
				'Adresse.compladr',
				'Adresse.codepos',
				'Adresse.nomcom',
				'Personne.email',
				'Prestation.rolepers'
			),
			'recursive' => -1,
			'joins' => array(
				$this->Personne->join('Prestation'),
				$this->Personne->join('Foyer'),
				$this->Personne->Foyer->join('Dossier'),
				$this->Personne->Foyer->join('Adressefoyer'),
				$this->Personne->Foyer->Adressefoyer->join('Adresse'),
			),
			'conditions' => array(
				'Personne.foyer_id' => $foyer_id,
				'Personne.id !=' => $idPersonne
			)
		);
		$personnesLiens = $this->Personne->find('all', $query);
		if(!empty($personnesLiens)) {
			$results = array();
			foreach($personnesLiens as $personne) {
				if(	$personne['Personne']['prenom'] != $infoPersonne['prenom'] &&
					$personne['Personne']['dtnai'] != $infoPersonne['dateNaissance']
				) {
					$typeLien = '';
					if($personne['Prestation']['rolepers'] != '') {
						$typeLien = __d('prestation', 'ENUM::ROLEPERS::' . $personne['Prestation']['rolepers']);
					}
					$results[] = array(
						'id' => $personne['Personne']['id'],
						'qual' => $personne['Personne']['qual'],
						'nomnai' => $personne['Personne']['nomnai'],
						'nom' => $personne['Personne']['nom'],
						'prenom' => $personne['Personne']['prenom'],
						'dtnai' => $personne['Personne']['dtnai'],
						'nir' => $personne['Personne']['nir'],
						'numfixe' => $personne['Personne']['numfixe'],
						'numport' => $personne['Personne']['numport'],
						'email' => $personne['Personne']['email'],
						'matricule' => $personne['Dossier']['matricule'],
						'numvoie' => $personne['Adresse']['numvoie'],
						'libtypevoie' => $personne['Adresse']['libtypevoie'],
						'compladr' => $personne['Adresse']['compladr'],
						'codepos' => $personne['Adresse']['codepos'],
						'nomcom' => $personne['Adresse']['nomcom'],
						'typeLien' => $typeLien
					);
				}
			}
			// Suppression des doublons
			$results = $this->unique_multidim_array($results, 'dtnai');
			return $results;
		}
		return array();
	}

	/**
	 * Récupère la dernière structure d'orientation d'une personne
	 * @param int
	 * @return string
	 */
	private function _getLastMSP($idPersonne) {
		$query = array(
			'recursive' => -1,
			'conditions' => array(
				'Orientstruct.personne_id' => $idPersonne,
			),
			'joins' => array(
				$this->Structurereferente->join('Orientstruct')
			),
			'order' => array('Orientstruct.date_valid DESC'),
		);
		$results = $this->Structurereferente->find('first', $query);
		if(isset($results) && !empty($results)) {
			return $results['Structurereferente']['lib_struc'];
		} else {
			return '';
		}
	}

	/**
	 * Récupère le dernier historique d'une personne
	 * @param int
	 * @return array
	 */
	private function _getHisto($idPersonne) {
		$query = array(
			'fields' => array(
				'Historiquedroit.etatdosrsa',
				'Historiquedroit.created',
				'Historiquedroit.modified'
			),
			'recursive' => -1,
			'conditions' => array(
				'Historiquedroit.personne_id' => $idPersonne,
			),
			'order' => array('Historiquedroit.created DESC'),
		);
		return $this->Historiquedroit->find('all', $query);
	}

	/**
	 * Regarde si la personne a été active lors des derniers 24 mois
	 * @param array
	 * @param int
	 * @return int
	 */
	private function _isActif($data, $idPersonne) {
		// Test sur les rendez vous
		if (isset($data['RDVs']) && !empty($data['RDVs'])) {
			foreach ($data['RDVs'] as $rdv) {
				if (new DateTime($rdv['dateRdv']) > $this->passe24mois) {
					return 1;
				}
			}
		}

		// Test sur les aides
		if (isset($data['Aides']) && !empty($data['Aides'])) {
			foreach ($data['Aides'] as $aide) {
				if (
					new DateTime($aide['datePremiereAttribution']) > $this->passe24mois ||
					new DateTime($aide['dateFinDroits']) > $this->passe24mois
				) {
					return 1;
				}
			}
		}

		// Test sur l'historique
		$histos = $this->_getHisto($idPersonne);
		if (isset($histos) && !empty($histos)) {
			foreach ($histos as $histo) {
				if (
					(new DateTime($histo['Historiquedroit']['created']) > $this->passe24mois ||
						new DateTime($histo['Historiquedroit']['modified']) > $this->passe24mois)
					&&
					in_array($histo['Historiquedroit']['etatdosrsa'], array('2', '3', '4'))
				) {
					return 1;
				}
			}
		}
		return 0;
	}

	/**
	 * Récupère tous les id lié à une personne selon son nom / prénom / date de naissance
	 * puis sur le NIR
	 * @param array
	 * @return array
	 */
	private function _getDoublon($data) {
		$conditions = array();
		if (isset($data['nir']) && !empty($data['nir'])) {
			if (strlen(trim($data['nir'])) == 13) {
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
		foreach ($personnes as $id => $pers) {
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
		foreach ($results as $result) {
			$result = $result[0];
			if (empty($result['nom']) && isset($referents[$result['id']]) && !empty($referents[$result['id']])) {
				continue;
			} else if (!isset($referents[$result['id']]) && empty($result['nom'])) {
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
	 * Traite les aides par personnes grâce à l'historique des droits
	 * @param array
	 * @param array
	 * @return array
	 *
	 */
	private function _traitementAides($result, $listeId) {
		// Récupération de tous les historiques de chaque personneId
		$query = array(
			'fields' => array(
				'Historiquedroit.personne_id',
				'Historiquedroit.etatdosrsa',
				'Historiquedroit.created',
				'Historiquedroit.modified',
				'Dossier.id',
				'Dossier.dtdemrsa'
			),
			'recursive' => -1,
			'joins' => array(
				$this->Historiquedroit->join('Personne'),
				$this->Historiquedroit->Personne->join('Foyer'),
				$this->Historiquedroit->Personne->Foyer->join('Dossier')
			),
			'conditions' => array(
				'Historiquedroit.personne_id IN' => $listeId,
			),
			'order' => array('Historiquedroit.created ASC'),
		);
		$results = $this->Historiquedroit->find('all', $query);

		// S'il n'y pas d'infos on retourne un tableau vide
		if (empty($results)) {
			return array();
		}

		// Récupération des référents
		$referents = $this->_getReferents($listeId);

		$aides = array();
		$nbAides = 0;
		foreach ($results as $result) {
			$histo = $result['Historiquedroit'];
			$dossierId = $result['Dossier']['id'];
			// Si c'est un nouveau droit ouvert
			if (in_array($histo['etatdosrsa'], $this->droitOuvert) && !isset($aides[$nbAides])) {
				// Gestion du référent
				if (isset($referents[$dossierId]) && !empty($referents[$dossierId])) {
					$referent = array(
						'nomReferent' => $referents[$dossierId]['nom'],
						'prenomReferent' => $referents[$dossierId]['prenom'],
						'fonctionReferent' => $referents[$dossierId]['fonction'],
					);
				} else {
					$referent = array();
				}

				// Création de l'aide
				$dateCreated = new DateTime($result['Dossier']['dtdemrsa']);
				$created = $dateCreated->format("Y-m-d");
				$aides[$nbAides] = array_merge(
					array(
						'nature' => 'RSA',
						'datePremiereAttribution' => $created,
						'dateFinDroits' => ''
					),
					$referent
				);
			}

			// Si le droit est déjà ouvert et que c'est un droit clos
			if (in_array($histo['etatdosrsa'], $this->droitClos) && isset($aides[$nbAides])) {
				$dateCreated = new DateTime($histo['created']);
				$created = $dateCreated->format("Y-m-d");
				$aides[$nbAides]['dateFinDroits'] = $created;
				$nbAides++;
			}
		}

		// Récupération de la traduction de l'état actuel du droit
		if( isset($aides) && !empty($aides) ) {
			$nbDerAide = count($aides)-1;
			if($aides[$nbDerAide]['dateFinDroits'] == '') {
				$aides[$nbDerAide]['etatDroitActuel'] = __d('historiquedroit', "ENUM::ETATDOSRSA::" . $histo['etatdosrsa']);
			}
		}
		return $aides;
	}

	/**
	 * Traite les aides par personnes grâce à leur indemnités
	 * @param array
	 * @param array
	 * @return array
	 */
	private function _traitementAidesIndemnite($data, $listeId) {
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
		if (empty($infos)) {
			return array();
		}

		// Gestion du référent
		$referents = $this->_getReferents($listeId);

		// Initialisation des variables de base
		$aides = array();
		$datedebutdroit = '';

		foreach ($infos as $info) {
			$info = $info['Infofinanciere'];
			// Initialisation de la date début de doit et calcul du mois suivant
			if ($datedebutdroit == '') {
				$datedebutdroit = $info['moismoucompta'];
				$datedebut1mois = new DateTime($info['moismoucompta']);
				$datedebut1mois->add(new DateInterval('P1M'));
				if (isset($referents[$info['dossier_id']]) && !empty($referents[$info['dossier_id']])) {
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
			} else if ($dtencours != new DateTime($info['moismoucompta'])) {
				// S'il y a une regul
				if (isset($info['ddregu']) && !empty($info['ddregu'])) {
					$isRegul = true;
				} else {
					$isRegul = false;
				}
				// S'il y a une date de régulation inférieur à la date en cours
				if ($isRegul && $dtencours < new DateTime($info['ddregu'])) {
					$dtencours = new DateTime($info['ddregu']);
					$twoMonths = new DateTime($info['ddregu']);
					$twoMonths = $twoMonths->sub(new DateInterval('P2M1D'));
				}
				// Si la date du mois comptable est la même que la date en cours
				// ou qu'il y a une date de régul inférieur ou égal à la date en cours on passe au suivant
				else if (new DateTime($info['moismoucompta']) == $dtencours || ($isRegul && new DateTime($info['ddregu']) <= $dtencours)) {
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
				if ($interval->days > $twoMonthsInterval->days) {
					$tmpaide['dateFinDroits'] = $datedebut1mois->format('Y-m-d');
					$aides[] = $tmpaide;

					if ($isRegul && new DateTime($info['moismoucompta']) > new DateTime($info['ddregu'])) {
						$datedebutdroit = $info['ddregu'];
						$datedebut1mois = new DateTime($info['ddregu']);
					} else {
						$datedebutdroit = $info['moismoucompta'];
						$datedebut1mois = new DateTime($info['moismoucompta']);
					}

					// Récupération du référent de la personne
					if (isset($referents[$info['dossier_id']]) && !empty($referents[$info['dossier_id']])) {
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
					if (
						$isRegul
						&& new DateTime($info['ddregu']) > $datedebut1mois
					) {
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
		if ($interval->days > $twoMonthsInterval->days) {
			$tmpaide['dateFinDroits'] = $datedebut1mois->format("Y-m-d");
		}
		$aides[] = $tmpaide;

		return $aides;
	}

	private function _traitementBeneficiaire($resultLastId, $lastId, $currentResult) {
		// Modification du nom avec dernier nom connu si différent (cas mariage)
		if (isset($resultLastId['nom']) && !empty($resultLastId['nom']) && ($currentResult['nom'] != $resultLastId['nom'])) {
			$currentResult['nom'] = $resultLastId['nom'];
		}

		// Balise nom naissance
		if (!isset($currentResult['nomnai'])) {
			$currentResult['nomnai'] = $currentResult['nom'];
		}

		// Balise adresse
		$adressecomplete = '';
		if (isset($resultLastId['numvoie']) && !empty($resultLastId['numvoie'])) {
			$adressecomplete .= $resultLastId['numvoie'];
		}

		if (isset($resultLastId['libtypevoie']) && !empty($resultLastId['libtypevoie'])) {
			$adressecomplete .= ' ' . $resultLastId['libtypevoie'];
		}

		if (isset($resultLastId['nomvoie']) && !empty($resultLastId['nomvoie'])) {
			$adressecomplete .= ' ' . $resultLastId['nomvoie'];
		}

		if (isset($resultLastId['compladr']) && !empty($resultLastId['compladr'])) {
			$adressecomplete .= ' ' . $resultLastId['compladr'];
		}

		// Balise téléphone
		$tel = array();
		if (isset($currentResult['numfixe']) && !empty($currentResult['numfixe'])) {
			$tel[] = preg_replace('/[^\d](?=\d)/', '', $currentResult['numfixe']);
		}
		if (isset($currentResult['numport']) && !empty($currentResult['numport'])) {
			$tel[] = preg_replace('/[^\d](?=\d)/', '', $currentResult['numport']);
		}

		// NIR
		if (strlen(trim($currentResult['nir'])) == 13) {
			$cle = $this->_calcul_cle_nir(trim($currentResult['nir']));
			if ($cle != null) {
				$nir = trim($currentResult['nir']) . $cle;
			} else {
				$nir = '';
			}
		} else if (strlen(trim($currentResult['nir'])) == 15) {
			$nir = trim($currentResult['nir']);
		} else {
			$nir = '';
		}

		// Matricule
		if (isset($currentResult['matricule'])) {
			$currentResult['matricule'] = trim($currentResult['matricule']);
		}

		// Email
		if (isset($currentResult['email']) && !empty($currentResult['email'])) {
			$email = $currentResult['email'];
		} else {
			$email = '';
		}

		// MSP
		$msp = $this->_getLastMSP($lastId);

		// Référérence du dossier : numéro de demande RSA
		if (isset($currentResult['numdemrsa']) && !empty($currentResult['numdemrsa'])) {
			$numdemrsa = $currentResult['numdemrsa'];
		} else {
			$numdemrsa = '';
		}

		return array(
			'flagSortie' => '0',
			'genre' => $currentResult['qual'],
			'nomNaissance' => $currentResult['nomnai'],
			'nomUsage' => $currentResult['nom'],
			'prenom' => $currentResult['prenom'],
			'dateNaissance' => $currentResult['dtnai'],
			'codeNir' => $nir,
			'codeCaf' => $currentResult['matricule'],
			'adresseComplete' => $adressecomplete,
			'codePostal' => $currentResult['codepos'],
			'ville' => $currentResult['nomcom'],
			'mspRattachement' => $msp,
			'refDossier' => $numdemrsa,
			'telephone' => $tel,
			'mail' => $email
		);
	}

	/**
	 * Récupère et prépare le tableau pour la création du XML
	 * @return array
	 */
	private function _traitementSql() {
		$results = $this->_getResultSQL();
		$arrayXml = array();

		foreach ($results as $result) {
			/* -- Partie Bénificiaire -- */

			// Gestion des doublons sur Nom / Prenom / Date de naissance
			$listeIDPersonne = $this->_getDoublon($result);
			sort($listeIDPersonne);
			$lastId = end($listeIDPersonne);
			if(isset($results[$lastId])) {
				$tmpResult = $results[$lastId];
			} else {
				$tmpResult = $result;
			}
			$arrayXml[$lastId]['Beneficiaire'] = $this->_traitementBeneficiaire($tmpResult, $lastId, $result);

			/* -- Fin partie Bénificiaire -- */

			if($result['rolepers'] == 'DEM') {
				// Aides
				if (in_array('ind', $this->args)) {
					$arrayXml[$lastId]['Aides'] = $this->_traitementAidesIndemnite($result, $listeIDPersonne);
				} else {
					$arrayXml[$lastId]['Aides'] = $this->_traitementAides($result, $listeIDPersonne);
				}

				// RDV
				if (
					isset($arrayXml[$result['personne_id']])
					&& !empty($arrayXml[$result['personne_id']])
				) {
					if (!isset($arrayXml[$lastId]['RDVs'])) {
						$arrayXml[$lastId]['RDVs'] = array();
					}
					$arrayXml[$lastId]['RDVs'] += $result['RDVs'];
				}

				// Balise actif
				$arrayXml[$lastId]['Beneficiaire']['actif'] = $this->_isActif($arrayXml[$lastId], $lastId);
			} else {
				$arrayXml[$lastId]['Beneficiaire']['actif'] = 0;
			}

			// Personne en lien
			$arrayXml[$lastId]['lien'] = $this->_getPersonneLien($lastId, $arrayXml[$lastId]['Beneficiaire']);
			foreach($arrayXml[$lastId]['lien'] as $lien) {
				if($lien['typeLien'] == '' || $lien['typeLien'] == 'Enfant') {
					$arrayXml[$lien['id']]['Beneficiaire'] = $this->_traitementBeneficiaire($lien, $lien['id'], $lien);
					$arrayXml[$lien['id']]['Beneficiaire']['actif'] = 0;
					$arrayXml[$lien['id']]['lien'] = $this->_getPersonneLien($lien['id'], $arrayXml[$lien['id']]['Beneficiaire']);
				}
			}
		}
		debug($arrayXml);
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
		$xml = new XMLWriter();
		$success = $xml->openUri($file);
		if (!$success) {
			return $success;
		}
		$xml->startDocument('1.0', 'ISO-8859-15');
		$xml->startElement('VueGlobale');
		$xml->writeAttribute('application', 'WebRSA');
		$xml->writeAttribute('date_extraction', $now->format('Y-m-d'));
		foreach ($listePersonnes as $personne) {
			$xml->startElement('Dossier');
			// Début Beneficiaire
			$xml->startElement('Beneficiaire');
			$xml->writeElement('actif', $personne['Beneficiaire']['actif']);
			$xml->writeElement('flagSortie', $personne['Beneficiaire']['flagSortie']);
			$xml->writeElement('genre', $personne['Beneficiaire']['genre']);
			$xml->writeElement('nomNaissance', $personne['Beneficiaire']['nomNaissance']);
			$xml->writeElement('nomUsage', $personne['Beneficiaire']['nomUsage']);
			$xml->writeElement('prenom', $personne['Beneficiaire']['prenom']);
			$xml->writeElement('dateNaissance', $personne['Beneficiaire']['dateNaissance']);
			$xml->writeElement('codeNir', $personne['Beneficiaire']['codeNir']);
			$xml->writeElement('codeCaf', $personne['Beneficiaire']['codeCaf']);

			// Début Adresse
			$xml->startElement('adresses');
			$xml->startElement('adresse');
			$xml->writeElement('adresseComplete', $personne['Beneficiaire']['adresseComplete']);
			$xml->writeElement('codePostal', $personne['Beneficiaire']['codePostal']);
			$xml->writeElement('ville', $personne['Beneficiaire']['ville']);
			$xml->endElement();
			$xml->endElement();
			// Fin Adresse

			// Structure référente (msp)
			$xml->writeElement('mspRattachement', $personne['Beneficiaire']['mspRattachement']);

			// Reférence de dossier (numdemrsa)
			$xml->writeElement('refDossier', $personne['Beneficiaire']['refDossier']);

			// Téléphones
			if (isset($personne['Beneficiaire']['telephone']) && !empty($personne['Beneficiaire']['telephone'])) {
				$xml->startElement('Telephones');
				foreach ($personne['Beneficiaire']['telephone'] as $tel) {
					$xml->startElement('Telephone');
					$xml->writeElement('NumTel', $tel);
					$xml->endElement();
				}
				$xml->endElement();
			}
			// Fin Téléphones

			// Mail
			if ($personne['Beneficiaire']['mail'] != '') {
				$xml->startElement('Mails');
				$xml->startElement('Mail');
				$xml->writeElement('AdresseMail', $personne['Beneficiaire']['mail']);
				$xml->endElement();
				$xml->endElement();
			}
			// Fin Mail

			$xml->endElement();
			// Fin Bénificiaire

			// Début Aides
			if (isset($personne['Aides']) && !empty($personne['Aides'])) {
				$xml->startElement('Aides');
				foreach ($personne['Aides'] as $aide) {
					$xml->startElement('Aide');
					$xml->writeElement('nature', $aide['nature']);
					$xml->writeElement('datePremiereAttribution', $aide['datePremiereAttribution']);
					if ($aide['dateFinDroits'] != '') {
						$xml->writeElement('dateFinDroits', $aide['dateFinDroits']);
					} else {
						// S'il n'y a pas de date de fin de droit, il a un état actuel
						$xml->writeElement('commentEtatDroit', $aide['etatDroitActuel']);
					}
					// Noeud Référent
					if (isset($aide['nomReferent']) && !empty($aide['nomReferent'])) {
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
			if (isset($personne['RDVs']) && !empty($personne['RDVs'])) {
				$xml->startElement('RDVs');
				foreach ($personne['RDVs'] as $rdv) {
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
			// Personnes en lien
			if(isset($personne['lien']) && !empty($personne['lien'])) {
				$xml->startElement('personnesLien');
				foreach($personne['lien'] as $lien) {
					$xml->startElement('personneLien');
					$xml->writeElement('nom', $lien['nom']);
					$xml->writeElement('prenom', $lien['prenom']);
					$xml->writeElement('dateNaissance', $lien['dtnai']);
					$xml->writeElement('typeLien', $lien['typeLien']);
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
		if ($octets == 0) {
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
		if (!isset($this->args[0])) {
			$this->out(__d('shells', 'Shells:Vueglobale:error::path'));
			exit();
		} else if (substr($this->args[0], -1) !== '/') {
			$path = $this->args[0] . '/';
		} else {
			$path = $this->args[0];
		}

		// Vérification que l'écriture est possible dans le dossier spécifié
		if (file_put_contents($path . 'test', '') === false) {
			$this->out(__d('shells', 'Shells:Vueglobale:error::rightPath'));
			exit();
		} else {
			unlink($path . 'test');
		}

		// Traitement SQL
		$this->out(__d('shells', 'Shells:Vueglobale:comment::debutSQLPersonne'));
		$timestart = microtime(true);
		$listeDonnee = $this->_traitementSql();
		$this->out(sprintf(__d('shells', 'Shells:Vueglobale:comment::finTraitement'), number_format(microtime(true) - $timestart, 3)));

		// Suppression d'un ancien XML si la commande a été exécuté deux fois
		$this->out();
		$this->out(__d('shells', 'Shells:Vueglobale:comment::suppressionXML'));
		$timestart = microtime(true);

		// Récupération de la date du jour pour nom fichier & attribut date_extraction
		$now = new DateTime();
		$file = $path . 'xml_webrsa_' . $now->format('Y-m-d') . '.xml';
		if (file_exists($file)) {
			unlink($file);
		}
		$this->out(sprintf(__d('shells', 'Shells:Vueglobale:comment::finTraitement'), number_format(microtime(true) - $timestart, 3)));

		// Ecriture du fichier XML
		$this->out();
		$this->out(__d('shells', 'Shells:Vueglobale:comment::debutXML'));
		$timestart = microtime(true);
		$success = $this->_ecritureXml($listeDonnee, $file . '.tmp');
		if ($success) {
			$nombrePersonne = count($listeDonnee);
			$this->out(sprintf(
				__d('shells', 'Shells:Vueglobale:comment::finXML'),
				number_format(microtime(true) - $timestart, 3),
				$nombrePersonne
			));
		} else {
			$this->out(__d('shells', 'Shells:Vueglobale:error::errorXML'));
			exit();
		}

		// Formatage du XML pour l'avoir indenté
		$this->out();
		$this->out(__d('shells', 'Shells:Vueglobale:comment::debutFormatXML'));
		$timestart = microtime(true);
		shell_exec('xmllint -format -recover ' . $file . '.tmp > ' . $file);
		unlink($file . '.tmp');
		$sz = 'BKMGTP'; // correspond à Bytes, Kilobytes, etc.
		$bytes = filesize($file);
		$factor = floor((strlen($bytes) - 1) / 3);
		$this->out(sprintf(
			__d('shells', 'Shells:Vueglobale:comment::finFormatXMLPremPartie') . @$sz[$factor] . __d('shells', 'Shells:Vueglobale:comment::finFormatXMLDeuxPartie'),
			number_format(microtime(true) - $timestart, 3),
			$bytes / pow(1024, $factor)
		));

		// Test et récupération du XML
		if (in_array('log', $this->args)) {
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
		foreach ($all->Dossier as $dossier) {
			$strFile = $dossier->Beneficiaire->genre . ' ' . $dossier->Beneficiaire->nomUsage . ' ' . $dossier->Beneficiaire->prenom . ' ' . $dossier->Beneficiaire->codeNir . PHP_EOL;
			fwrite($file, $strFile);
		}
		fclose($file);
	}

	/**
	 *
	 */
	public function help() {
		$this->out("Usage pour CentOS: sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake Vueglobale -app app [Filepath]");
		$this->out("Arguments supplémentaires : ");
		$this->out("log : créé un log comprenant toutes les personnes prises en compte dans le XML");
		$this->out("ind : calcul le droit des aides grâce aux indemnitées payées");

		$this->_stop(0);
	}
}
