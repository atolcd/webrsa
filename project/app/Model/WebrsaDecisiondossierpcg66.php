<?php
	/**
	 * Code source de la classe WebrsaDecisiondossierpcg66.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractLogic', 'Model' );
	App::uses('WebrsaLogicAccessInterface', 'Model/Interface');
	App::uses('WebrsaModelUtility', 'Utility');

	/**
	 * La classe WebrsaDecisiondossierpcg66 ...
	 *
	 * @package app.Model
	 */
	class WebrsaDecisiondossierpcg66 extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaDecisiondossierpcg66';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Decisiondossierpcg66');

		/**
		 * Récupère les données pour le PDf
		 */
		public function getPdfDecision($id) {
			// TODO: error404/error500 si on ne trouve pas les données
			$optionModel = ClassRegistry::init('Option');
			$qual = $optionModel->qual();
			$services = $this->Decisiondossierpcg66->Dossierpcg66->Serviceinstructeur->find('list');
			$situationspdos = $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Personnepcg66Situationpdo->Situationpdo->find('list');
			$conditions = array('Decisiondossierpcg66.id' => $id);

			$joins = array(
				array(
					'table' => 'dossierspcgs66',
					'alias' => 'Dossierpcg66',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array('Dossierpcg66.id = Decisiondossierpcg66.dossierpcg66_id')
				),
				array(
					'table' => 'decisionspdos',
					'alias' => 'Decisionpdo',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array('Decisionpdo.id = Decisiondossierpcg66.decisionpdo_id')
				),
				array(
					'table' => 'originespdos',
					'alias' => 'Originepdo',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array('Originepdo.id = Dossierpcg66.originepdo_id')
				),
				array(
					'table' => 'polesdossierspcgs66',
					'alias' => 'Poledossierpcg66',
					'type' => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array('Poledossierpcg66.id = Dossierpcg66.poledossierpcg66_id')
				),
				array(
					'table' => 'personnespcgs66',
					'alias' => 'Personnepcg66',
					'type' => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array('Dossierpcg66.id = Personnepcg66.dossierpcg66_id')
				),
				array(
					'table' => 'traitementspcgs66',
					'alias' => 'Traitementpcg66',
					'type' => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array('Personnepcg66.id = Traitementpcg66.personnepcg66_id')
				),
				array(
					'table' => 'descriptionspdos',
					'alias' => 'Descriptionpdo',
					'type' => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array('Descriptionpdo.id = Traitementpcg66.descriptionpdo_id')
				),
				array(
					'table' => 'personnespcgs66_situationspdos',
					'alias' => 'Personnepcg66Situationpdo',
					'type' => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array('Personnepcg66.id = Personnepcg66Situationpdo.personnepcg66_id')
				),
				array(
					'table' => 'decisionspersonnespcgs66',
					'alias' => 'Decisionpersonnepcg66',
					'type' => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array('Decisionpersonnepcg66.personnepcg66_situationpdo_id = Personnepcg66Situationpdo.id')
				),
				array(
					'table' => 'personnespcgs66_statutspdos',
					'alias' => 'Personnepcg66Statutpdo',
					'type' => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array('Personnepcg66.id = Personnepcg66Statutpdo.personnepcg66_id')
				),
				array(
					'table' => 'statutspdos',
					'alias' => 'Statutpdo',
					'type' => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array('Statutpdo.id = Personnepcg66Statutpdo.statutpdo_id')
				),
				array(
					'table' => 'users',
					'alias' => 'User',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array('User.id = Dossierpcg66.user_id')
				),
				array(
					'table' => 'foyers',
					'alias' => 'Foyer',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array('Foyer.id = Dossierpcg66.foyer_id')
				),
				array(
					'table' => 'personnes',
					'alias' => 'Personne',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array('Personne.foyer_id = Foyer.id')
				),
				array(
					'table' => 'prestations',
					'alias' => 'Prestation',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						'Personne.id = Prestation.personne_id',
						'Prestation.natprest = \'RSA\'',
						'Prestation.rolepers IN ( \'DEM\', \'CJT\')'
					)
				),
				array(
					'table' => 'dossiers',
					'alias' => 'Dossier',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array('Dossier.id = Foyer.dossier_id')
				),
				array(
					'table' => 'adressesfoyers',
					'alias' => 'Adressefoyer',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array(
						'Foyer.id = Adressefoyer.foyer_id',
						'Adressefoyer.rgadr' => '01'
					)
				),
				array(
					'table' => 'adresses',
					'alias' => 'Adresse',
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => array('Adresse.id = Adressefoyer.adresse_id')
				),
				array(
					'table' => 'pdfs',
					'alias' => 'Pdf',
					'type' => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						'Pdf.modele' => $this->Decisiondossierpcg66->alias,
						'Pdf.fk_value = Decisiondossierpcg66.id'
					)
				),
			);

			$queryData = array(
				'fields' => array(
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.lieudist',
					'Adresse.numcom',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Adresse.pays',
					'Dossier.numdemrsa',
					'Dossier.dtdemrsa',
					'Dossier.matricule',
					'Personne.qual',
					'Personne.nom',
					'Personne.prenom',
					'Personne.dtnai',
					'Personne.nir',
					//
					'Dossierpcg66.orgpayeur',
					'Dossierpcg66.id',
					'Dossierpcg66.etatdossierpcg',
					'Dossierpcg66.datereceptionpdo',
					'Dossierpcg66.serviceinstructeur_id',
					'User.nom',
					'User.prenom',
					'User.numtel',
					'Decisiondossierpcg66.id',
					'Decisiondossierpcg66.commentaire',
					'Decisiondossierpcg66.avistechnique',
					'Decisiondossierpcg66.dateavistechnique',
					'Decisiondossierpcg66.commentaireavistechnique',
					'Poledossierpcg66.name',
					'Decisiondossierpcg66.validationproposition',
					'Decisiondossierpcg66.datevalidation',
					'Decisiondossierpcg66.commentairevalidation',
					'Decisiondossierpcg66.commentairetechnicien',
					'Originepdo.libelle',
					'Decisionpdo.libelle'/* ,
				  'Typersapcg66.name' */
				),
				'joins' => $joins,
				'conditions' => $conditions,
				'contain' => false
			);

			$data = $this->Decisiondossierpcg66->find('first', $queryData);


			$data['Personne']['qual'] = Set::enum(Hash::get($data, 'Personne.qual'), $qual);
			$data['Dossierpcg66']['serviceinstructeur_id'] = Set::enum(Hash::get($data, 'Dossierpcg66.serviceinstructeur_id'), $services);

			$sections = array();
			$personnesfoyerpcg = $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->find(
					'all', array(
				'conditions' => array(
					'Personnepcg66.dossierpcg66_id' => $data['Dossierpcg66']['id']
				),
				'contain' => array(
					'Personne' => array(
						'Prestation'
					)
				)
					)
			);

			$data['Presence'] = array();
			$data['Presence']['dem'] = $data['Presence']['cjt'] = $data['Presence']['enf'] = 0;

			foreach ($personnesfoyerpcg as $personnefoyerpcg) {
				$personnefoyerpcg['Prestation'] = $personnefoyerpcg['Personne']['Prestation'];
				unset($personnefoyerpcg['Personne']['Prestation']);

				$data['Presence'][strtolower($personnefoyerpcg['Prestation']['rolepers'])] = 1;

				$data[$personnefoyerpcg['Prestation']['rolepers']] = $personnefoyerpcg;

				$personnespcgs66_situationspdos = $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Personnepcg66Situationpdo->find(
						'all', array(
					'conditions' => array(
						'Personnepcg66Situationpdo.personnepcg66_id' => $personnefoyerpcg['Personnepcg66']['id']
					),
					'joins' => array(
						$this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Personnepcg66Situationpdo->join('Situationpdo')
					),
					'fields' => array_merge(
							$this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Personnepcg66Situationpdo->fields(), $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Personnepcg66Situationpdo->Situationpdo->fields()
					)
						)
				);
				$data[$personnefoyerpcg['Prestation']['rolepers']]['Situationpdo']['libelles'] = implode("\n", Set::extract('/Situationpdo/libelle', $personnespcgs66_situationspdos));

				$personnespcgs66_statutspdos = $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Personnepcg66Statutpdo->find(
						'all', array(
					'conditions' => array(
						'Personnepcg66Statutpdo.personnepcg66_id' => $personnefoyerpcg['Personnepcg66']['id']
					),
					'joins' => array(
						$this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Personnepcg66Statutpdo->join('Statutpdo')
					),
					'fields' => array_merge(
							$this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Personnepcg66Statutpdo->fields(), $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Personnepcg66Statutpdo->Statutpdo->fields()
					)
						)
				);
				$data[$personnefoyerpcg['Prestation']['rolepers']]['Statutpdo']['libelles'] = implode("\n", Set::extract('/Statutpdo/libelle', $personnespcgs66_statutspdos));


				// Calcul des revenus à afficher dans la décision si on décide de répercuter la fiche de calcul dans la décision
				$traitementsAvecFicheCalcul = $this->Decisiondossierpcg66->Dossierpcg66->Personnepcg66->Traitementpcg66->find(
						'all', array(
					'conditions' => array(
						'Traitementpcg66.personnepcg66_id' => $personnefoyerpcg['Personnepcg66']['id'],
						'Traitementpcg66.typetraitement' => 'revenu',
						'Traitementpcg66.reversedo' => '1',
						'Traitementpcg66.annule' => 'N'
					),
					'contain' => false
						)
				);
				$data[$personnefoyerpcg['Prestation']['rolepers']]['Personnepcg66']['fichecalculreversee'] = '';
				foreach ($traitementsAvecFicheCalcul as $i => $traitementFicheCalcul) {
					$data[$personnefoyerpcg['Prestation']['rolepers']]['Personnepcg66']['fichecalculreversee'] += $traitementFicheCalcul['Traitementpcg66']['revenus'];
				}
			}



			// Recherche des pièces nécessaires pour cette aide, et qui ne sont pas présentes
			$querydata = array(
				'joins' => array(
					$this->Decisiondossierpcg66->Typersapcg66->join('Decisiondossierpcg66Typersapcg66')
				),
				'conditions' => array(
					'Decisiondossierpcg66Typersapcg66.decisiondossierpcg66_id' => $id
				),
				'contain' => false
			);


			$data['Decisiondossierpcg66']['Typersapcg66'] = null;

			$typesrsa = $this->Decisiondossierpcg66->Typersapcg66->find('list', $querydata);

			if (!empty($typesrsa)) {
				$data['Decisiondossierpcg66']['Typersapcg66'] .= "\n" . '- ' . implode("\n- ", $typesrsa) . ',';
			}

			$options = array();
			$options = Set::merge(
							$this->Decisiondossierpcg66->enums(), $this->Decisiondossierpcg66->Dossierpcg66->enums()
			);

			// La Personne sans section doit être le demandeur du RSA et non la personne concerné par le dossier PCG
			$query = array(
				'fields' => $this->Decisiondossierpcg66->Dossierpcg66->Foyer->Personne->fields(),
				'joins' => array(
					$this->Decisiondossierpcg66->join( 'Dossierpcg66' ),
					$this->Decisiondossierpcg66->Dossierpcg66->join( 'Foyer' ),
					$this->Decisiondossierpcg66->Dossierpcg66->Foyer->join( 'Personne' ),
					$this->Decisiondossierpcg66->Dossierpcg66->Foyer->Personne->join( 'Prestation' ),
				),
				'conditions' => array(
					'Decisiondossierpcg66.id' => $id,
					'Prestation.rolepers' => 'DEM'
				)
			);
			$personneDem = $this->Decisiondossierpcg66->find('first',$query);
			$data['Personne'] = isset($personneDem['Personne']) ? $personneDem['Personne'] : $data['Personne'];

			return $this->Decisiondossierpcg66->ged(
							$data, $this->modeleOdt($data), false, $options
			);
		}

		/**
		 * Retourne le chemin relatif du modèle de document à utiliser pour l'enregistrement du PDF.
		 */
		public function modeleOdt($data) {
			return "PCG66/propositiondecision.odt";
		}

		/**
		 * Retourne une sous-requête permettant de connaître la dernière décision
		 * pour un dossier PCG donné.
		 *
		 * @param string $field Le champ Dossierpcg66.id sur lequel faire la sous-requête
		 * @return string
		 */
		public function sqDernier($field) {
			$dbo = $this->Decisiondossierpcg66->getDataSource($this->Decisiondossierpcg66->useDbConfig);
			$table = $dbo->fullTableName($this->Decisiondossierpcg66, false, false);
			return "SELECT {$table}.id
						FROM {$table}
						WHERE
							{$table}.dossierpcg66_id = " . $field . "
						ORDER BY {$table}.created DESC
						LIMIT 1";
		}

		/**
		 * Donne la query pour un index
		 * Dans ce cas precis, l'index est l'edit d'un dossier pcg
		 *
		 * @param integer $dossierpcg66_id
		 * @return array
		 */
		public function queryIndex($dossierpcg66_id) {
			return array(
				'fields' => array(
					'Decisionpdo.libelle',
					'Decisiondossierpcg66.id',
					'Decisiondossierpcg66.dossierpcg66_id',
					'Decisiondossierpcg66.avistechnique',
					'Decisiondossierpcg66.dateavistechnique',
					'Decisiondossierpcg66.validationproposition',
					'Decisiondossierpcg66.datevalidation',
					'Decisiondossierpcg66.motifannulation',
					$this->Decisiondossierpcg66->Fichiermodule
						->sqNbFichiersLies($this->Decisiondossierpcg66, 'nb_fichiers_lies'),
				),
				'contain' => false,
				'joins' => array(
					$this->Decisiondossierpcg66->join('Decisionpdo')
				),
				'conditions' => array(
					'Decisiondossierpcg66.dossierpcg66_id' => $dossierpcg66_id
				)
			);
		}

		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 *
		 * @param array $conditions
		 * @param array $params
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$query = array(
				'fields' => array(
					'Decisiondossierpcg66.id',
					'Dossierpcg66.id',
					'Foyer.id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Decisiondossierpcg66->join('Dossierpcg66'),
					$this->Decisiondossierpcg66->Dossierpcg66->join('Foyer')
				),
				'contain' => false,
				'order' => array(
					'Decisiondossierpcg66.created' => 'DESC',
					'Decisiondossierpcg66.id' => 'DESC',
				)
			);

			$results = $this->Decisiondossierpcg66->find('all', $this->completeVirtualFieldsForAccess($query, $params));
			return $results;
		}

		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 *
		 * @see WebrsaAccess::getParamsList
		 * @param integer $dossierpcg66_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($dossierpcg66_id, array $params = array()) {
			$results = array();

			if (in_array('ajoutPossible', $params)) {
				$results['ajoutPossible'] = $this->ajoutPossible($dossierpcg66_id, $params);
			}

			return $results;
		}

		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 *
		 * @param integer $dossierpcg66_id
		 * @param array $params
		 * @return boolean
		 */
		public function ajoutPossible($dossierpcg66_id, array $params = array()) {
			$dossierpcg = $this->Decisiondossierpcg66->Dossierpcg66->find('first', array(
				'fields' => 'Dossierpcg66.id',
				'contain' => false,
				'joins' => array(
					$this->Decisiondossierpcg66->Dossierpcg66->join('Personnepcg66', array('type' => 'INNER')),
					$this->Decisiondossierpcg66->Dossierpcg66->join('Decisiondossierpcg66',
						array(
							'type' => 'LEFT',
							'conditions' => array('Decisiondossierpcg66.etatdossierpcg IS NULL') // Décision non annulé
						)
					),
				),
				'conditions' => array(
					'Dossierpcg66.id' => $dossierpcg66_id,
					'Dossierpcg66.etatdossierpcg' => array(
						'arevoir',
						'attaffect',
						'attinstr',
						'attinstrattpiece',
						'attinstrdocarrive',
						'decisionnonvalid',
						'instr',
						'instrencours',
					),
					'OR' => array(
						'Decisiondossierpcg66.id IS NULL',
						'Decisiondossierpcg66.validationproposition' => 'N'
					)
				),
				'order' => array('Decisiondossierpcg66.created' => 'DESC')
			));

			return !empty($dossierpcg);
		}

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @param array $params
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$query['fields'] = array_merge(
				(array)Hash::get($query, 'fields'),
				array(
					'Decisiondossierpcg66.dernier' => $this->Decisiondossierpcg66->sqVirtualField('dernier'),
					'Decisiondossierpcg66.validationproposition',
					'Decisiondossierpcg66.etatdossierpcg',
					'Decisiondossierpcg66.instrencours',
					'Decisiondossierpcg66.decisionpdo_id',
					'Decisiondossierpcg66.retouravistechnique',
					'Decisiondossierpcg66.vuavistechnique',
					'Dossierpcg66.etatdossierpcg',
					'Dossierpcg66.dateimpression',
				)
			);

			if (WebrsaModelUtility::findJoinKey("Dossierpcg66", $query) === false) {
				$query['joins'][] = $this->Decisiondossierpcg66->join("Dossierpcg66");
			}

			return $query;
		}
	}