<?php
	/**
	 * Code source de la classe WebrsaApre66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractLogic', 'Model');
	App::uses('WebrsaLogicAccessInterface', 'Model/Interface');
	App::uses('WebrsaModelUtility', 'Utility');

	/**
	 * La classe WebrsaApre66 possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaApre66 extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaApre66';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Apre66');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$fields = array(
				'Apre66.etatdossierapre'
			);
			return Hash::merge($query, array('fields' => array_values($fields)));
		}

		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$query = array(
				'fields' => array(
					'Apre66.id',
					'Apre66.personne_id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Apre66->join('Personne')
				),
				'contain' => false,
				'order' => array(
					'Apre66.id' => 'DESC',
				)
			);

			$results = $this->Apre66->find('all', $this->completeVirtualFieldsForAccess($query));
			return $results;
		}

		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 *
		 * @see WebrsaAccess::getParamsList
		 * @param integer $personne_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($personne_id, array $params = array()) {
			$results = array();

			if (in_array('ajoutPossible', $params)) {
				$results['ajoutPossible'] = $this->ajoutPossible($personne_id);
			}

			return $results;
		}

		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 *
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function ajoutPossible($personne_id) {
			return true;
		}

		/**
		 *
		 * @param integer $apre_id
		 * @return string
		 */
		public function getNotificationAprePdf( $apre_id, $update = true ) {
			$apre = $this->Apre66->find(
				'first',
				array(
					'fields' => array_merge(
						$this->Apre66->fields(),
						$this->Apre66->Aideapre66->Themeapre66->fields(),
						$this->Apre66->Aideapre66->Typeaideapre66->fields(),
						$this->Apre66->Personne->fields(),
						$this->Apre66->Structurereferente->fields(),
						$this->Apre66->Referent->fields(),
						$this->Apre66->Aideapre66->fields(),
						$this->Apre66->Personne->Foyer->Adressefoyer->Adresse->fields(),
						$this->Apre66->Personne->Foyer->fields(),
						$this->Apre66->Personne->Foyer->Dossier->fields(),
						array(
							'( '.$this->Apre66->Aideapre66->Pieceaide66->vfListePieces().' ) AS "Aideapre66__piecesaides66"',
							'( '.$this->Apre66->Aideapre66->Typeaideapre66->Piececomptable66->vfListePieces().' ) AS "Aideapre66__piecescomptables66"',
							$this->Apre66->Personne->Foyer->Adressefoyer->Adresse->sqVirtualField( 'localite' )
						)
					),
					'joins' => array(
						$this->Apre66->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Apre66->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre66->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre66->join( 'Aideapre66', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre66->Aideapre66->join( 'Themeapre66', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre66->Aideapre66->join( 'Typeaideapre66', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre66->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Apre66->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre66->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Apre66->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) )
					),
					'conditions' => array(
						"Apre66.id" => $apre_id,
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ( '.$this->Apre66->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
						)
					),
					'contain' => false
				)
			);

			if( empty( $apre ) ) {
				return false;
			}

			// Options pour les traductions
			$Option = ClassRegistry::init( 'Option' );
			$options = array(
				'Personne' => array(
					'qual' => $Option->qual()
				),
				'Referent' => array(
					'qual' => $Option->qual()
				)
			);

			// On sauvagarde la date de notification si ce n'est pas déjà fait.
			if ($update) {
				$recursive = $this->Apre66->recursive;
				$this->Apre66->recursive = -1;
				$this->Apre66->updateAllUnBound(
					array( 'Apre66.datenotifapre' => date( "'Y-m-d'" ) ),
					array(
						'"Apre66"."id"' => $apre_id,
						'"Apre66"."datenotifapre" IS NULL'
					)
				);
				$this->Apre66->recursive = $recursive;
			}

			// Construction du champ virtuel Structurereferente.adresse
			$apre['Structurereferente']['adresse'] = implode(
					' ', array(
				Set::classicExtract( $apre, 'Structurereferente.num_voie' ),
				Set::classicExtract( $apre, 'Structurereferente.type_voie' ),
				Set::classicExtract( $apre, 'Structurereferente.nom_voie' ),
				Set::classicExtract( $apre, 'Structurereferente.code_postal' ),
				Set::classicExtract( $apre, 'Structurereferente.ville' )
					)
			);

			// Choix du modèle de document
			if( $apre['Aideapre66']['decisionapre'] == 'ACC' ) {
				$modeleodt = 'APRE/accordaide.odt';
			}
			else {
				$modeleodt = 'APRE/refusaide.odt';
			}

			// Génération du PDF
			return $this->Apre66->ged( $apre, $modeleodt, false, $options );
		}

		/**
		 * Retourne le chemin vers le modèle odt utilisé pour l'APRE 66
		 *
		 * @param array $data
		 * @return string
		 */
		public function modeleOdt( $data ) {
			return 'APRE/apre66.odt';
		}

		/**
		 * Retourne les données nécessaires à l'impression d'une APRE pour le CG 66.
		 * Les données contiennent l'APRE à l'index 0 et une section "oldapres".
		 *
		 * @param integer $id
		 * @param integer $user_id
		 * @return array
		 */
		public function getDataForPdf( $id, $user_id ) {
			$apre = $this->Apre66->find(
				'first',
				array(
					'fields' => array_merge(
						$this->Apre66->fields(),
						$this->Apre66->Personne->fields(),
						$this->Apre66->Structurereferente->fields(),
						$this->Apre66->Referent->fields(),
						$this->Apre66->Aideapre66->fields(),
						$this->Apre66->Aideapre66->Fraisdeplacement66->fields(),
						$this->Apre66->Aideapre66->Themeapre66->fields(),
						$this->Apre66->Aideapre66->Themeapre66->Typeaideapre66->fields(),
						$this->Apre66->Personne->Foyer->Adressefoyer->Adresse->fields(),
						$this->Apre66->Personne->Foyer->fields(),
						$this->Apre66->Personne->Foyer->Dossier->fields(),
						array(
							'( '.$this->Apre66->Aideapre66->Pieceaide66->vfListePieces().' ) AS "Aideapre66__piecesaides66"',
							'( '.$this->Apre66->Aideapre66->Typeaideapre66->Piececomptable66->vfListePieces().' ) AS "Aideapre66__piecescomptables66"',
							$this->Apre66->Personne->Foyer->sqVirtualField( 'enerreur' ),
							$this->Apre66->Personne->Foyer->sqVirtualField( 'sansprestation' ),
							$this->Apre66->Personne->Foyer->Adressefoyer->Adresse->sqVirtualField( 'localite' )
						)
					),
					'joins' => array(
						$this->Apre66->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Apre66->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre66->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre66->join( 'Aideapre66', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre66->Aideapre66->join( 'Fraisdeplacement66', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre66->Aideapre66->join( 'Themeapre66', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre66->Aideapre66->join( 'Typeaideapre66', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre66->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Apre66->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Apre66->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Apre66->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					),
					'conditions' => array(
						"Apre66.id" => $id,
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ( '.$this->Apre66->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
						)
					),
					'contain' => false
				)
			);

			if( empty( $apre ) ) {
				return $apre;
			}

			// Récupération de l'utilisateur connecté
			$user = $this->Apre66->Personne->Contratinsertion->User->find(
					'first', array(
				'conditions' => array(
					'User.id' => $user_id
				),
				'contain' => false
					)
			);
			$apre = Set::merge( $apre, $user );

			// Construction du champ virtuel Structurereferente.adresse
			$apre['Structurereferente']['adresse'] = implode(
					' ', array(
				Set::classicExtract( $apre, 'Structurereferente.num_voie' ),
				Set::classicExtract( $apre, 'Structurereferente.type_voie' ),
				Set::classicExtract( $apre, 'Structurereferente.nom_voie' ),
				Set::classicExtract( $apre, 'Structurereferente.code_postal' ),
				Set::classicExtract( $apre, 'Structurereferente.ville' )
					)
			);

			// Le lieu de résidence correspond à l'adresse normale s'il n'est pas explicitement renseigné
			if( !empty( $apre['Fraisdeplacement66']['id'] ) && empty( $apre['Fraisdeplacement66']['lieuresidence'] ) ) {
				$apre['Fraisdeplacement66']['lieuresidence'] = implode(
						' ', array(
					Set::extract( $apre, 'Adresse.numvoie' ),
					Set::extract( $apre, 'Adresse.libtypevoie' ),
					Set::extract( $apre, 'Adresse.nomvoie' ),
					Set::extract( $apre, 'Adresse.codepos' ),
					Set::extract( $apre, 'Adresse.nomcom' )
						)
				);
			}

			// Le passif des demandes d'APRE attribuées
			$listeApres = $this->Apre66->find(
				'all',
				array(
					'fields' => array(
						'Apre66.id',
						'Aideapre66.datedemande',
						'Aideapre66.montantaccorde',
						'Typeaideapre66.name',
						'Themeapre66.name'
					),
					'conditions' => array(
						"Apre66.personne_id" => $apre['Personne']['id'],
						"Apre66.id <>" => $id,
						'Aideapre66.id IS NOT NULL',
						'Aideapre66.decisionapre' => 'ACC',
						'Apre66.datenotifapre IS NOT NULL',
						'Aideapre66.datedemande <=' => $apre['Aideapre66']['datedemande'],
					),
					'joins' => array(
						$this->Apre66->join( 'Aideapre66' ),
						$this->Apre66->Aideapre66->join( 'Typeaideapre66'),
						$this->Apre66->Aideapre66->join( 'Themeapre66' )
					),
					'order' => array( 'Aideapre66.datedemande DESC' ),
					'recursive' => -1
				)
			);

			/// INFO: pour éviter d'écraser les valeurs de la partie principale avec la valeur de la dernière itération lorsque la section précède l'affichage de la valeur principale.
			foreach( $listeApres as $i => $oldapre ) {
				$listeApres[$i] = array( 'oldapre' => $oldapre );
			}

			return array( $apre, 'oldapres' => $listeApres );
		}

		/**
		 * Retourne le PDF par défaut généré par les appels aux méthodes getDataForPdf, modeleOdt et
		 * à la méthode ged du behavior Gedooo
		 *
		 * @param type $id Id de l'APRE
		 * @param type $user_id Id de l'utilisateur connecté
		 * @return string
		 */
		public function getDefaultPdf( $id, $user_id ) {
			$Option = ClassRegistry::init( 'Option' );

			$options = array(
				'Personne' => array(
					'qual' => $Option->qual()
				),
				'Referent' => array(
					'qual' => $Option->qual()
				)
			);

			$apre = $this->getDataForPdf( $id, $user_id );

			if( empty( $apre ) ) {
				$this->Apre66->cakeError( 'error404' );
			}

			return $this->Apre66->ged(
				$apre,
				$this->modeleOdt( $apre ),
				true,
				$options
			);
		}

		/**
		 * Utilise Correspondancepersonne pour trouver le montant total d'apre pris dans l'année.
		 * @param integer $personne_id
		 * @param boolean $anomalie
		 * @return integer
		 */
		public function getMontantApreEnCours( $personne_id, $anomalie = null ){
			$dateDebut = date( 'Y' ).'-01-01';
			$dateFin = (date( 'Y' ) + Configure::read( 'Apre.periodeMontantMaxComplementaires' ) - 1).'-12-31';

			return $this->getMontantAprePeriode($dateDebut, $dateFin, $personne_id, $anomalie);
		}

		/**
		 * Utilise Correspondancepersonne pour trouver le montant total d'apre pour une période donnée.
		 * @param string $dateDebut au format SQL
		 * @param string $dateFin au format SQL
		 * @param integer $personne_id
		 * @param boolean $anomalie
		 * @return integer
		 */
		public function getMontantAprePeriode( $dateDebut, $dateFin, $personne_id, $anomalie = null ){
			$queryCorrespondances = array(
				'fields' => 'Correspondancepersonne.personne2_id',
				'conditions' => array(
					'Correspondancepersonne.personne1_id' => $personne_id,
				),
			);

			if ( $anomalie !== null ) {
				$queryCorrespondances['conditions']['Correspondancepersonne.anomalie'] = $anomalie;
			}

			$personne_idSearch = $this->Apre66->Personne->Correspondancepersonne->find( 'all', $queryCorrespondances );

			$personne_idList = array();
			foreach ($personne_idSearch as $value) {
				$personne_idList[] = $value['Correspondancepersonne']['personne2_id'];
			}

			$query = array(
				'fields' => array(
					'SUM(Aideapre66.montantaccorde) AS "Aideapre66__montantaccorde"',
				),
				'joins' => array(
					$this->Apre66->join( 'Aideapre66', array( 'type' => 'INNER' ) )
				),
				'contain' => false,
				'conditions' => array(
					"{$this->Apre66->alias}.personne_id" => array_merge(
						array($personne_id),
						$personne_idList
					),
					"{$this->Apre66->alias}.statutapre" => 'C',
					"Aideapre66.decisionapre" => 'ACC',
					"Aideapre66.datemontantpropose BETWEEN '{$dateDebut}' AND '{$dateFin}'",
					"{$this->Apre66->alias}.etatdossierapre <>" => 'ANN',
					'Aideapre66.montantaccorde IS NOT NULL'
				),
			);
			$results = $this->Apre66->find( 'all', $query );

			$montantaccorde = Hash::get($results, '0.Aideapre66.montantaccorde');

			return $montantaccorde;
		}
	}