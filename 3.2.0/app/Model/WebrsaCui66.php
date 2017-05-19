<?php
	/**
	 * Code source de la classe WebrsaCui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaCui', 'Model');
	App::uses('WebrsaModelUtility', 'Utility');

	/**
	 * La classe WebrsaCui66 possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaCui66 extends WebrsaCui
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCui66';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cui',
			'Cui66',
		);

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$query = parent::completeVirtualFieldsForAccess($query);

			if (!isset($query['joins'])) {
				$query['joins'] = array();
			}

			$modelName = Inflector::camelize(Inflector::singularize(Inflector::underscore($params['controller'])));
			if ($modelName !== 'Cui66') {
				$query = WebrsaModelUtility::addJoins($this->Cui->Cui66, $modelName, $query);
			}

			return $query;
		}

		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 *
		 * @param integer $cui_id
		 * @param array $params
		 * @return boolean
		 */
		public function ajoutPossible($cui_id, array $params = array()) {
			$ajoutPossible = true;
			if ($ajoutPossible && in_array('isModuleDecision', $params)) {
				$query = array(
					'fields' => 'Decisioncui66.id',
					'joins' => array(
						$this->Cui->join('Cui66', array('type' => 'INNER')),
						$this->Cui->Cui66->join('Decisioncui66', array('type' => 'INNER'))
					),
					'conditions' => array('Cui.id' => $cui_id)
				);
				$exist = $this->Cui->find('first', $query);
				$ajoutPossible = empty($exist);
			}
			if ($ajoutPossible && in_array('isModuleRupture', $params)) {
				$query = array(
					'fields' => 'Rupturecui66.id',
					'joins' => array(
						$this->Cui->join('Cui66', array('type' => 'INNER')),
						$this->Cui->Cui66->join('Rupturecui66', array('type' => 'INNER'))
					),
					'conditions' => array('Cui.id' => $cui_id)
				);
				$exist = $this->Cui->find('first', $query);
				$ajoutPossible = empty($exist);
			}
			return $ajoutPossible;
		}

		/**
		 * Permet de savoir si un ajout est possible à partir des messages
		 * renvoyés par la méthode messages.
		 *
		 * @param array $messages
		 * @return boolean
		 */
		public function addEnabled( array $messages ) {
			return !in_array( 'error', $messages );
		}

		/**
		 * Récupère les donnés par defaut dans le cas d'un ajout, ou récupère les données stocké en base dans le cas d'une modification
		 *
		 * @param integer $personne_id
		 * @param integer $id
		 * @return array
		 */
		public function prepareFormDataAddEdit( $personne_id, $id = null ) {
			// Ajout
			if( empty( $id ) ) {
				$sqDernierTitresejour = $this->Cui66->Cui->Personne->Titresejour->sqDernier();
				$sqNbEnfants = $this->Cui66->Cui->Personne->Foyer->vfNbEnfants();
				$sqNbBeneficiaires = $this->Cui66->Cui->Personne->Foyer->sqNombreBeneficiaires();
				$sqLastCodepartenaire = $this->Cui66->Cui->Partenaire->sqGetLastCodePartenaire();

				$query = $this->Cui66->Cui->Personne->PersonneReferent->completeSearchQueryReferentParcours( array(
					'fields' => array(
						'Titresejour.dftitsej',
						"( {$sqNbEnfants} ) AS \"Foyer__nb_enfants\"",
						"( {$sqNbBeneficiaires} ) AS \"Foyer__nb_beneficiaires\"",
						"( {$sqLastCodepartenaire} ) AS \"Partenairecui__codepartenaire\"",

						// Pour table personnescuis (pour impression uniquement)
						'Personne.qual',
						'Personne.nom',
						'Personne.prenom',
						'Personne.nomnai',
						'Personne.prenom2',
						'Personne.prenom3',
						'Personne.nomcomnai',
						'Personne.dtnai',
						'Personne.nir',
						'Personne.nati',
						'Dossier.matricule',
						'Dossier.fonorg',

						// Pour table personnescuis66 (persistance des données affichés)
						'Dossier.dtdemrsa',
						'Adresse.numvoie',
						'Adresse.libtypevoie',
						'Adresse.nomvoie',
						'Adresse.complideadr',
						'Adresse.compladr',
						'Adresse.lieudist',
						'Adresse.codepos',
						'Adresse.nomcom',
						'Adresse.canton',
						'Departement.name',
						'Detaildroitrsa.mttotdrorsa',
					),
					'recursive' => -1,
					'conditions' => array(
						'Personne.id' => $personne_id
					),
					'joins' => array(
						$this->Cui66->Cui->Personne->join( 'Titresejour',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => "Titresejour.id IN ( {$sqDernierTitresejour} )"
							)
						),
						$this->Cui66->Cui->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Cui66->Cui->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Cui66->Cui->Personne->Foyer->Dossier->join( 'Detaildroitrsa', array( 'type' => 'LEFT OUTER' ) ),
						$this->Cui66->Cui->Personne->Foyer->join( 'Adressefoyer', array(
							'type' => 'LEFT OUTER', 'conditions' => array('Adressefoyer.rgadr' => '01')
						)),
						$this->Cui66->Cui->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						array(
							'table' => 'departements',
							'alias' => 'Departement',
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'SUBSTRING( Adresse.codepos FROM 1 FOR 2 ) = Departement.numdep'
							)
						)
					)
				));

				$Adresse =& $this->Cui66->Cui->Personne->Foyer->Adressefoyer->Adresse;
				if (Configure::read( 'CG.cantons' )) {
					$query['fields'][] = 'Canton.canton';

					if (Configure::read('Canton.useAdresseCanton')) {
						$query['joins'][] = $Adresse->join('AdresseCanton', array('type' => 'LEFT OUTER'));
						$query['joins'][] = $Adresse->AdresseCanton->join('Canton', array('type' => 'LEFT OUTER'));
					} else {
						$query['joins'][] = $Adresse->AdresseCanton->Canton->joinAdresse();
					}
				}

				$record = $this->Cui66->Cui->Personne->find( 'first', $query );

				$record['Partenairecui']['codepartenaire'] = str_pad( ($record['Partenairecui']['codepartenaire'] +1), 3, '0', STR_PAD_LEFT );
				$adr =& $record['Adresse'];
				$adr['complete'] = $adr['numvoie'] . ' ' . $adr['libtypevoie'] . ' ' . $adr['nomvoie'] . '<br>';
				$adr['complete'] .= $adr['complideadr'] !== '' ? $adr['complideadr'] . '<br>' : '';
				$adr['complete'] .= $adr['compladr'] !== '' ? $adr['compladr'] . '<br>' : '';
				$adr['complete'] .= $adr['lieudist'] !== '' ? $adr['lieudist'] . '<br>' : '';
				$adr['complete'] .= $adr['codepos'] . ' ' . $adr['nomcom'];

				/**
				 * INFO: si one ne met pas le modèle Adressecui dans le $this->Cui66->request->data, il n'est
				 * pas instancié dans la vue, donc pas d'astérisque ni validation javascript...
				 */
				$result = array(
					'Cui' => array(
						'personne_id' => $personne_id,
						'numconventionobjectif' => Configure::read( 'Cui.Numeroconvention' ),
						'decision_cui' => 'E',
					),
					'Cui66' => array(
						'encouple' => $record['Foyer']['nb_beneficiaires'] >= 2 ? 1 : 0,
						'avecenfant' => $record['Foyer']['nb_enfants'] >= 1 ? 1 : 0,
						'demandeenregistree' => date_format(new DateTime(), 'Y-m-d'),
						'datefinsejour' => Hash::get( $record, 'Titresejour.dftitsej' ),
						'etatdossiercui66' => 'attentemail',
						'notifie' => 0,
					),
					'Partenairecui66' => array(
						'nbcontratsaidescg' => '0',
						'codepartenaire' => $record['Partenairecui']['codepartenaire']
					),
					'Personnecui' => array(
						'civilite' => Hash::get($record, 'Personne.qual'),
						'nomusage' => Hash::get($record, 'Personne.nom'),
						'prenom1' => Hash::get($record, 'Personne.prenom'),
						'nomfamille' => Hash::get($record, 'Personne.nomnai'),
						'prenom2' => Hash::get($record, 'Personne.prenom2'),
						'prenom3' => Hash::get($record, 'Personne.prenom3'),
						'villenaissance' => Hash::get($record, 'Personne.nomcomnai'),
						'datenaissance' => Hash::get($record, 'Personne.dtnai'),
						'nir' => Hash::get($record, 'Personne.nir'),
						'numallocataire' => Hash::get($record, 'Dossier.matricule'),
						'nationalite' => Hash::get($record, 'Personne.nati'),
						'organismepayeur' => Hash::get($record, 'Dossier.fonorg'),
					),
					'Personnecui66' => array(
						'adressecomplete' => Hash::get($record, 'Adresse.complete'),
						'canton' => Hash::get($record, 'Adresse.canton'),
						'departement' => Hash::get($record, 'Departement.name'),
						'referent' => Hash::get($record, 'Referentparcours.nom_complet'),
						'nbpersacharge' => Hash::get($record, 'Foyer.nb_enfants'),
						'dtdemrsa' => Hash::get($record, 'Dossier.dtdemrsa'),
						'montantrsa' => Hash::get( $record, 'Detaildroitrsa.mttotdrorsa' )
					),
					'Adressecui' => array()
				);

				// Remplacement par le canton calculé
				if( Configure::read( 'CG.cantons' ) ) {
					$result['Personnecui66']['canton'] = Hash::get($record, 'Canton.canton');
				}
			}
			// Mise à jour
			else {
				$query = $this->Cui66->Cui->WebrsaCui->queryView($id);
				$result = $this->Cui66->Cui->find( 'first', $query );

				$result = $this->Cui66->Cui->Entreeromev3->prepareFormDataAddEdit( $result );

				// Ajoute un champ virtuel
				foreach ($this->Cui66->Cui->beneficiairede as $key => $value){
					$keyName = 'beneficiaire_' . strtolower($value);
					if ( $result['Cui'][$keyName] === 1 ){
						$result['Cui']['beneficiairede'][] = $key;
					}
				}
			}

			return $result;
		}

		/**
		 * Change la valeur de Cui66.etatdossiercui66 en annule
		 *
		 * @param array $data
		 * @return boolean
		 */
		public function annule( $data ){
			$data['Cui66']['etatdossiercui66'] = 'annule';
			return $this->Cui66->save( $data , array( 'atomic' => false ) );
		}

		/**
		 * Revoi la requete pour récuperer toutes les données pour l'affichage d'un CUI (Hors modules)
		 *
		 * @param integer $id
		 * @return array
		 */
		public function queryView( $id = null ){
			$query = array(
				'fields' => array_merge(
					$this->Cui66->fields(),
					$this->Cui66->Personnecui66->fields(),
					$this->Cui66->Cui->Personnecui->fields(),
					$this->Cui66->Cui->Partenairecui->fields(),
					$this->Cui66->Cui->Entreeromev3->fields(),
					$this->Cui66->Cui->Partenairecui->Adressecui->fields(),
					$this->Cui66->Cui->Partenairecui->Partenairecui66->fields(),
					$this->Cui66->Cui->fields()
				),
				'recursive' => -1,
				'conditions' => array(),
				'joins' => array(
					$this->Cui66->Cui->join( 'Cui66', array( 'type' => 'INNER' ) ),
					$this->Cui66->join( 'Personnecui66' ),
					$this->Cui66->Cui->join( 'Personnecui' ),
					$this->Cui66->Cui->join( 'Partenairecui' ),
					$this->Cui66->Cui->join( 'Entreeromev3' ),
					$this->Cui66->Cui->Partenairecui->join( 'Partenairecui66' ),
					$this->Cui66->Cui->Partenairecui->join( 'Adressecui' ),
				)
			);

			if( $id !== null ) {
				$query['conditions']['Cui.id'] = $id;
			}

			return $query;
		}

		/**
		 * Revoi la requete pour récuperer toutes les données pour l'affichage de l'index du CUI
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function queryIndex($personne_id){
			// Utile pour l'affichage des dates de relance par email
			$sqRelanceQuery = array(
				'alias' => 'emailscuis',
				'fields' => 'emailscuis.id',
				'conditions' => array(
					'emailscuis.dateenvoi IS NOT NULL',
					'UPPER(textsmailscuis66.name) LIKE \'%RELANCE%\'',
					'emailscuis.cui_id = Cui66.cui_id'
				),
				'joins' => array(
					array_words_replace(
						$this->Cui66->Cui->Emailcui->join( 'Textmailcui66', array( 'type' => 'INNER' ) ),
						array( 'Emailcui' => 'emailscuis', 'Textmailcui66' => 'textsmailscuis66' )
					)
				),
				'order' => 'emailscuis.dateenvoi DESC',
				'limit' => 1
			);
			$sqRelanceMail = $this->Cui66->Cui->Emailcui->sq( $sqRelanceQuery );

			// Utile pour l'affichage des changements de positions du CUI
			$sqDateChangementQuery = array(
				'alias' => 'historiquepositionscuis66',
				'fields' => 'historiquepositionscuis66.id',
				'conditions' => array(
					'historiquepositionscuis66.cui66_id = Cui66.id'
				),
				'order' => 'historiquepositionscuis66.created DESC',
				'limit' => 1
			);
			$sqDateChangementPosition = $this->Cui66->Historiquepositioncui66->sq( $sqDateChangementQuery );

			$sqDerniereSuspension = $this->Cui66->Suspensioncui66->sq(
				array(
					'alias' => 'suspensionscuis66',
					'fields' => 'suspensionscuis66.id',
					'conditions' => array(
						'suspensionscuis66.cui66_id = Cui66.id',
						'suspensionscuis66.datedebut <= NOW()',
						'suspensionscuis66.datefin >= NOW()',
					),
					'order' => array(
						'suspensionscuis66.datefin' => 'DESC',
						'suspensionscuis66.created' => 'DESC',
					),
					'limit' => 1
				)
			);

			$query = array(
				'fields' => array_merge(
					$this->Cui66->Cui->fields(),
					array('Cui.dureecontrat', 'Emailcui.dateenvoi', 'Historiquepositioncui66.created'),
					$this->Cui66->fields(),
					$this->Cui66->Cui->Partenairecui->fields(),
					$this->Cui66->Decisioncui66->fields(),
					$this->Cui66->Suspensioncui66->fields(),
					$this->Cui66->Rupturecui66->fields(),
					array(
						$this->Cui66->Cui->Fichiermodule->sqNbFichiersLies( $this->Cui66->Cui, 'nombre' ),
					)
				),
				'conditions' => array(
					'Cui.personne_id' => $personne_id
				),
				'joins' => array(
					$this->Cui66->Cui->join( 'Cui66', array( 'type' => 'INNER' ) ),
					$this->Cui66->Cui->join( 'Partenairecui' ),
					$this->Cui66->Cui->join( 'Emailcui', array( 'conditions' => array("Emailcui.id IN ({$sqRelanceMail})")) ),
					$this->Cui66->join( 'Decisioncui66' ),
					$this->Cui66->join( 'Suspensioncui66', array( 'conditions' => array("Suspensioncui66.id IN ({$sqDerniereSuspension})")) ),
					$this->Cui66->join( 'Rupturecui66' ),
					$this->Cui66->join( 'Historiquepositioncui66', array( 'conditions' => "Historiquepositioncui66.id IN ({$sqDateChangementPosition})") ),
				),
				'order' => array( 'Cui.created DESC' )
			);

			return $query;
		}

		/**
		 * Requète d'impression
		 *
		 * @param type $cui_id
		 * @return type
		 */
		public function queryImpression( $cui_id = null ){
			$queryView = $this->Cui66->Cui->WebrsaCui->queryView( $cui_id );
			$queryPersonne = $this->Cui66->Cui->WebrsaCui->queryPersonne( 'Cui.personne_id' );

			$query['fields'] = array_merge(
				$queryView['fields'],
				$queryPersonne['fields'],
				$this->Cui66->Cui->Entreeromev3->Familleromev3->fields(),
				$this->Cui66->Cui->Entreeromev3->Domaineromev3->fields(),
				$this->Cui66->Cui->Entreeromev3->Metierromev3->fields(),
				$this->Cui66->Cui->Entreeromev3->Appellationromev3->fields()
			);
			$query['joins'] = array_merge(
				$queryView['joins'],
				array(
					$this->Cui66->Cui->join( 'Personne' ),
					$this->Cui66->Cui->Entreeromev3->join( 'Familleromev3' ),
					$this->Cui66->Cui->Entreeromev3->join( 'Domaineromev3' ),
					$this->Cui66->Cui->Entreeromev3->join( 'Metierromev3' ),
					$this->Cui66->Cui->Entreeromev3->join( 'Appellationromev3' ),
				),
				$queryPersonne['joins'] );
			$query['conditions'] = $queryView['conditions'];

			return $query;
		}

		/**
		 * Sauvegarde d'un CUI
		 *
		 * @param array $data
		 * @return boolean
		 */
		public function saveAddEdit( array $data, $user_id = null ) {
			// INFO: champ non obligatoire
			unset( $this->Cui66->Cui->Entreeromev3->validate['familleromev3_id'][NOT_BLANK_RULE_NAME] );
			$success = true;

			// Transforme le champ virtuel beneficiairede type hasAndBelongsToMany en 4 champs de type smallint (boolean)
			foreach ($this->Cui66->Cui->beneficiairede as $value){
				$keyName = 'beneficiaire_' . strtolower($value);
				$data['Cui'][$keyName] = 0;
			}
			if ( is_array($data['Cui']['beneficiairede']) ){
				foreach( $data['Cui']['beneficiairede'] as $value ){
					if ( isset($this->Cui66->Cui->beneficiairede[$value]) ){
						$keyName = 'beneficiaire_' . strtolower($this->Cui66->Cui->beneficiairede[$value]);
						$data['Cui'][$keyName] = 1;
					}
				}
				unset($data['Cui']['beneficiairede']);
			}

			$data['Cui']['user_id'] = $user_id;

			// Si un code famille (rome v3) est vide, on ne sauvegarde pas le code rome
			if ( !isset($data['Entreeromev3']['familleromev3_id']) || $data['Entreeromev3']['familleromev3_id'] === '' ){
				$data['Cui']['entreeromev3_id'] = null;

				// Si le code rome avait un id, on supprime l'entreeromev3 correspondant
				if ( isset($data['Entreeromev3']['id']) && $data['Entreeromev3']['id'] !== '' ){
					$this->Cui66->Cui->Entreeromev3->id = $data['Entreeromev3']['id'];
					$success = $this->Cui66->Cui->Entreeromev3->delete() && $success;
				}
			}
			// Dans le cas contraire, on enregistre le tout
			else{
				$this->Cui66->Cui->Entreeromev3->create($data);
				$success = $this->Cui66->Cui->Entreeromev3->save( null, array( 'atomic' => false ) ) && $success;
				$data['Cui']['entreeromev3_id'] = $this->Cui66->Cui->Entreeromev3->id;
			}

			// Si le contrat est un CDI, on s'assure que la date de fin soit nulle
			if ( $data['Cui']['typecontrat'] === 'CDI' ){
				$data['Cui']['findecontrat'] = null;
			}

			// Partenairecui possède une Adressecui, on commence par cette dernière
			$this->Cui66->Cui->Partenairecui->Adressecui->create($data);
			$success = $this->Cui66->Cui->Partenairecui->Adressecui->save( null, array( 'atomic' => false ) ) && $success;
			$data['Partenairecui']['adressecui_id'] = $this->Cui66->Cui->Partenairecui->Adressecui->id;

			// Cui et Partenairecui66 possèdent un Partenairecui, il nous faut son id
			$this->Cui66->Cui->Partenairecui->create($data);
			$success = $this->Cui66->Cui->Partenairecui->save( null, array( 'atomic' => false ) ) && $success;
			$data['Cui']['partenairecui_id'] = $this->Cui66->Cui->Partenairecui->id;
			$data['Partenairecui66']['partenairecui_id'] = $this->Cui66->Cui->Partenairecui->id;

			// On peut ensuite enregistrer Partenairecui66
			$this->Cui66->Cui->Partenairecui->Partenairecui66->create($data);
			$success = $this->Cui66->Cui->Partenairecui->Partenairecui66->save( null, array( 'atomic' => false ) ) && $success;

			// Dans le cas d'un ajout, on met à jour les parametrages des partenaires
			if ( $success && empty($data['Cui']['id']) ){
				$data = $this->Cui66->Cui->Partenairecui->Partenairecui66->addPartenaireData( $data );
				$this->Cui66->Cui->Partenaire->create($data['Partenaire']);
				$success = $this->Cui66->Cui->Partenaire->save( null, array( 'atomic' => false ) ) && $success;
				$data['Cui']['partenaire_id'] = $this->Cui66->Cui->Partenaire->id;
			}

			if ( empty($data['Cui']['tauxfixeregion']) && empty($data['Cui']['priseenchargeeffectif']) && empty($data['Cui']['tauxcg']) ){
				$Tauxcgcui66 = ClassRegistry::init( 'Tauxcgcui66' );
				$query = array(
					'conditions' => array(
						'Tauxcgcui66.typeformulaire' => $data['Cui66']['typeformulaire'],
						'Tauxcgcui66.secteurmarchand' => $data['Cui']['secteurmarchand'],
						'Tauxcgcui66.typecontrat' => $data['Cui66']['typecontrat'],
					),
					'order' => array(
						'created' => 'DESC'
					)
				);
				$result = $Tauxcgcui66->find( 'first', $query );

				if ( !empty($result) ){
					$data['Cui']['tauxfixeregion'] = $result['Tauxcgcui66']['tauxfixeregion'];
					$data['Cui']['priseenchargeeffectif'] = $result['Tauxcgcui66']['priseenchargeeffectif'];
					$data['Cui']['tauxcg'] = $result['Tauxcgcui66']['tauxcg'];
				}
			}

			// Cui possède un Personnecui
			$this->Cui66->Cui->Personnecui->create($data);
			$success = $this->Cui66->Cui->Personnecui->save( null, array( 'atomic' => false ) ) && $success;
			$data['Cui']['personnecui_id'] = $this->Cui66->Cui->Personnecui->id;

			// Cui66 possède un Cui
			$this->Cui66->Cui->create($data);
			$success = $this->Cui66->Cui->save( null, array( 'atomic' => false ) ) && $success;
			$data['Cui66']['cui_id'] = $this->Cui66->Cui->id;

			// Cui66 possède un Personnecui66
			$this->Cui66->Personnecui66->create($data);
			$success = $this->Cui66->Personnecui66->save( null, array( 'atomic' => false ) ) && $success;
			$data['Cui66']['personnecui66_id'] = $this->Cui66->Personnecui66->id;

			// On termine par le Cui66
			$this->Cui66->create($data);
			$success = $this->Cui66->save( null, array( 'atomic' => false ) ) && $success;

			return $success;
		}

		/**
		 * Retourne les options nécessaires au formulaire de recherche, au formulaire,
		 * aux impressions, ...
		 *
		 * @return array
		 */
		public function options($user_id = null) {
			$Typecontratcui66 = ClassRegistry::init( 'Typecontratcui66' );

			$options = Hash::merge(
				array(
					'Cui66' => array(
						'datebutoir_select' => array(
							1 => __d( 'cui66', 'ENUM::DATEBUTOIR_SELECT::1' ),
							2 => __d( 'cui66', 'ENUM::DATEBUTOIR_SELECT::2' ),
							3 => __d( 'cui66', 'ENUM::DATEBUTOIR_SELECT::3' ),
						),
						'typecontrat' => $Typecontratcui66->find( 'list', array( 'order' => 'name' ) ),
						'typecontrat_actif' => $Typecontratcui66->find( 'list',
							array( 'conditions' => array( 'actif' => true ), 'order' => 'name' )
						)
					),
					'Partenairecui' => array(
						'naf' => $this->Cui66->Cui->Personne->Dsp->Libsecactderact66Secteur->find(
							'list',	array( 'contain' => false, 'order' => array( 'Libsecactderact66Secteur.code' ) )
						),
					),
					'Partenairecui66' => array(
						'subventioncg' => array(
							'Non',
							'Oui'
						),
					)
				),
				$this->Cui66->enums(),
				$this->Cui66->Decisioncui66->enums(),
				$this->Cui66->Personnecui66->enums(),
				$this->Cui66->Cui->enums(),
				$this->Cui66->Cui->Partenairecui->enums(),
				$this->Cui66->Cui->Personnecui->enums(),
				$this->Cui66->Cui->Entreeromev3->options()
			);

			foreach( $this->Cui66->Cui->beneficiairede as $value ){
				$options['Cui']['beneficiairede'][] = $value;
			}

			if ($user_id !== null) {
				// Récupération de la liste des actions avec une fiche de candidature (pour Cui.organismedesuivi)
				$qd_user = array(
					'conditions' => array(
						'User.id' => $this->Cui66->Session->read( 'Auth.User.id' )
					),
					'fields' => null,
					'order' => null,
					'contain' => array(
						'Serviceinstructeur'
					)
				);
				$user = $this->Cui66->User->find( 'first', $qd_user );

				$codeinseeUser = Set::classicExtract( $user, 'Serviceinstructeur.code_insee' );

				// On affiche les actions inactives en édition mais pas en ajout,
				// afin de pouvoir gérer les actions n'étant plus prises en compte mais toujours en cours
				$isactive = 'O';
				if( $this->Cui66->action == 'edit' ){
					$isactive = array( 'O', 'N' );
				}

				$actions = array();
				foreach(ClassRegistry::init('Actioncandidat')->listePourFicheCandidature( $codeinseeUser, $isactive, '1' ) as $action) {
					$actions[$action] = $action;
				}
				$options['Cui']['organismedesuivi'] = $actions;
			}

			// Ajout de la liste des partenaires
			$options['Cui']['partenaire_id'] = $this->Cui66->Cui->Partenaire->find( 'list', array( 'order' => array( 'Partenaire.libstruc' ) ) );

			// Liste des cantons pour l'adresse du partenaire
			App::uses( 'GestionzonesgeosComponent', 'Controller/Component' );
			$Gestionzonesgeos = new GestionzonesgeosComponent(new ComponentCollection());
			$options['Adressecui']['canton'] = $Gestionzonesgeos->listeCantons();
			$options['Adressecui']['canton2'] =& $options['Adressecui']['canton'];

			return $options;
		}

		/**************************************************************************************************************/

		/**
		 * Retourne les positions et les conditions CakePHP/SQL dans l'ordre dans
		 * lequel elles doivent être traitées pour récupérer la position actuelle.
		 *
		 * @return array
		 */
		protected function _getConditionsPositionsCuis() {
			// Le CUI possède une décision favorable
			$decisionFavorable = 'EXISTS(
				SELECT decisioncui66_sq3.id
				FROM decisionscuis66 AS decisioncui66_sq3
				WHERE decisioncui66_sq3.cui66_id = Cui66.id
				AND decisioncui66_sq3.decision IS NOT NULL
				AND decisioncui66_sq3.decision = \'accord\'
				LIMIT 1
			)';

			// Le CUI possède une décision ajourné
			$decisionAjourne = 'EXISTS(
				SELECT decisioncui66_sq3.id
				FROM decisionscuis66 AS decisioncui66_sq3
				WHERE decisioncui66_sq3.cui66_id = Cui66.id
				AND decisioncui66_sq3.decision IS NOT NULL
				AND decisioncui66_sq3.decision = \'ajourne\'
				LIMIT 1
			)';

			// Le CUI possède une décision de refus
			$decisionRefus = 'EXISTS(
				SELECT decisioncui66_sq3.id
				FROM decisionscuis66 AS decisioncui66_sq3
				WHERE decisioncui66_sq3.cui66_id = Cui66.id
				AND decisioncui66_sq3.decision IS NOT NULL
				AND (decisioncui66_sq3.decision = \'refus\'
				OR decisioncui66_sq3.decision = \'sanssuite\')
				LIMIT 1
			)';

			// Le CUI possède une décision
			$decision = 'EXISTS(
				SELECT decisioncui66_sq3.id
				FROM decisionscuis66 AS decisioncui66_sq3
				WHERE decisioncui66_sq3.cui66_id = Cui66.id
				AND decisioncui66_sq3.decision IS NOT NULL
				LIMIT 1
			)';

			// Le CUI possède une rupture
			$rupture = 'EXISTS(
				SELECT rupurecui66_sq.id
				FROM rupturescuis66 AS rupurecui66_sq
				WHERE rupurecui66_sq.cui66_id = Cui66.id
				LIMIT 1
			)';

			// Le CUI possède une suspension avec une datedefin < NOW() > datedebut
			$suspensionEnCours = 'EXISTS(
				SELECT Cui66.id
				FROM suspensionscuis66 AS suspensioncui66_sq
				WHERE suspensioncui66_sq.cui66_id = Cui66.id
				AND suspensioncui66_sq.datedebut IS NOT NULL
				AND suspensioncui66_sq.datefin IS NOT NULL
				AND NOW()::date BETWEEN suspensioncui66_sq.datedebut AND suspensioncui66_sq.datefin
				LIMIT 1
			)';

			// Le CUI possède un avis technique PRE
			$avisTechniquePre = 'EXISTS(
				SELECT propositioncui66_sq.id
				FROM propositionscuis66 AS propositioncui66_sq
				WHERE propositioncui66_sq.cui66_id = Cui66.id
				AND propositioncui66_sq.donneuravis = \'PRE\'
				AND propositioncui66_sq.avis != \'attentedecision\'
				LIMIT 1
			)';

			// Les cases importantes ont toute été rempli
			$formulaireRempli = 'Cui.dateembauche IS NOT NULL';

			// Le CUI possède un e-mail avec une dateenvoi not null
			$emailInitial = 'EXISTS(
				SELECT emailcui_sq.id
				FROM emailscuis AS emailcui_sq
				WHERE emailcui_sq.dateenvoi IS NOT NULL
				AND emailcui_sq.cui_id = Cui66.cui_id
				LIMIT 1
			)';

			// /!\ Attention, le mot relance doit être placé dans le textsmailscuis66.name pour être pris en compte
			$emailRelance = 'EXISTS(
				SELECT emailcui_sq.id
				FROM emailscuis AS emailcui_sq
				INNER JOIN textsmailscuis66 ON (textsmailscuis66.id = emailcui_sq.textmailcui66_id)
				WHERE emailcui_sq.dateenvoi IS NOT NULL
				AND UPPER(textsmailscuis66.name) LIKE \'%RELANCE%\'
				AND emailcui_sq.cui_id = Cui66.cui_id
				LIMIT 1
			)';

			$return = array(
				// 1. Annulé
				'annule' => array(
					array(
						$this->Cui66->alias.'.etatdossiercui66' => 'annule',
					)
				),

				// 2. Traité (Décision sans suite)
				'decisionsanssuite' => array(
					'OR' => array(
						array(
							$this->Cui66->alias.'.notifie' => 1,
							$decisionRefus
						),
						array(
							$this->Cui66->alias.'.dossierrecu IS NOT NULL',
							$this->Cui66->alias.'.dossierrecu' => 0
						),
						array(
							$this->Cui66->alias.'.etatdossiercui66' => array(
								'attentemail',
								'attentepiece',
								'dossiereligible',
								'dossierrecu',
								'formulairecomplet',
								'attenteavis',
								'attentedecision'
							),
							$this->Cui66->alias.'.datebutoir IS NOT NULL',
							$this->Cui66->alias.'.datebutoir <= NOW()::DATE'
						)
					)
				),

				// 3. Traité (Dossier non éligible)
				'nonvalide' => array(
					array(
						$this->Cui66->alias.'.dossiereligible IS NOT NULL',
						$this->Cui66->alias.'.dossiereligible' => 0
					)
				),

				// 4. Rupture du CUI depuis le
				'rupturecontrat' => array(
					array(
						$rupture
					)
				),

				// 5. Suspendu jusqu'au
				'contratsuspendu' => array(
					array(
						$suspensionEnCours
					)
				),

				// 6. Fin de contrat
				'perime' => array(
					array(
						'Cui.findecontrat IS NOT NULL',
						'Cui.findecontrat <= NOW()::DATE'
					)
				),

				// 7. En cours
				'encours' => array(
					array(
						$this->Cui66->alias.'.notifie' => 1,
						$decisionFavorable,
						'Cui.dateembauche IS NOT NULL',
						'Cui.dateembauche <= NOW()::DATE'
					)
				),

				// Notifié
				'notifie' => array(
					array(
						$this->Cui66->alias.'.notifie' => 1,
						$decisionFavorable
					)
				),

				// 8. En attente de notification
				'attentenotification' => array(
					array(
						$decision
					)
				),

				// 9. En attente de décision
				'attentedecision' => array(
					'OR' => array(
						$avisTechniquePre,
						$decisionAjourne
					)
				),

				// 12. En attente d'avis techniques
				'attenteavis' => array(
					array(
						$emailInitial,
						$formulaireRempli
					)
				),

				// X. Relance le %s (Dossier non reçu)
				'dossierrelance' => array(
					array(
						$emailRelance,
						$this->Cui66->alias.'.dossiercomplet IS NOT NULL',
						$this->Cui66->alias.'.dossiercomplet' => 0
					)
				),

				// X. En attente de relance (Dossier non reçu)
				'dossiernonrecu' => array(
					array(
						$emailInitial,
						$this->Cui66->alias.'.dossiercomplet IS NOT NULL',
						$this->Cui66->alias.'.dossiercomplet' => 0
					)
				),

				// 11. En attente d'informations complémentaires
				'formulairecomplet' => array(
					array(
						$emailInitial,
						$this->Cui66->alias.'.dossiercomplet IS NOT NULL',
						$this->Cui66->alias.'.dossiercomplet' => 1
					)
				),

				// 14. En attente de pièces (Verification éligibilité)
				'dossierrecu' => array(
					array(
						$emailInitial,
						$this->Cui66->alias.'.dossierrecu IS NOT NULL',
						$this->Cui66->alias.'.dossierrecu' => 1
					)
				),

				// 13. En attente de pièces
				'dossiereligible' => array(
					array(
						$emailInitial,
						$this->Cui66->alias.'.dossiereligible IS NOT NULL',
						$this->Cui66->alias.'.dossiereligible' => 1
					)
				),

				'attentepiece' => array(
					array(
						$emailInitial,
					)
				),
			);

			return $return;
		}

		/**
		 * Retourne les conditions permettant de cibler les CUI qui devraient être
		 * dans une cuitaine position.
		 *
		 * @param string $etatdossiercui66
		 * @return array
		 */
		public function getConditionsPositioncui( $etatdossiercui66 ) {
			$conditions = array();

			foreach( $this->_getConditionsPositionsCuis() as $keyPosition => $conditionsPosition ) {
				if ( $keyPosition === $etatdossiercui66 ) {
					$conditions[] = array( $conditionsPosition );
					break;
				}
			}

			return $conditions;
		}

		/**
		 * Retourne une CASE (PostgreSQL) pemettant de connaître la position que
		 * devrait avoir un CUI (au CG 66).
		 *
		 * A utiliser par exemple en tant que chmap virtuel, à partir du moment
		 * où le modèle Contratinsertion (ou un alias) est présent dans la requête
		 * de base.
		 *
		 * @return string
		 */
		public function getCasePositionCui() {
			$return = '';
			$Dbo = $this->Cui66->getDataSource();

			foreach( array_keys( $this->_getConditionsPositionsCuis() ) as $etatdossiercui66 ) {
				$conditions = $this->getConditionsPositioncui( $etatdossiercui66 );
				$conditions = $Dbo->conditions( $conditions, true, false, $this );
				$return .= "WHEN {$conditions} THEN '{$etatdossiercui66}' ";
			}

			// Position par defaut : En attente d'envoi de l'e-mail pour l'employeur
			$return = "( CASE {$return} ELSE 'attentemail' END )";

			return $return;
		}

		/**
		 * Mise à jour des positions des CUI suivant des conditions données.
		 *
		 * @param array $conditions
		 * @return boolean
		 */
		public function updatePositionsCuisByConditions( array $conditions ) {
			$query = array(
				'fields' => array( "{$this->Cui66->alias}.{$this->Cui66->primaryKey}", "{$this->Cui66->alias}.etatdossiercui66" ),
				'conditions' => $conditions,
				'joins' => array( $this->Cui66->join( 'Cui' ) )
			);
			$datas = $this->Cui66->find( 'all', $query );

			if ( empty( $datas ) ){
				return true;
			}

			$Dbo = $this->Cui66->getDataSource();
			$DboCui = $this->Cui66->Cui->getDataSource();

			$tableName = $Dbo->fullTableName( $this->Cui66, true, true );
			$tableNameCui = $DboCui->fullTableName( $this->Cui66->Cui, true, true );
			$case = $this->getCasePositionCui();

			$sq = $Dbo->startQuote;
			$eq = $Dbo->endQuote;
			$fullAlias = $sq.$this->Cui66->alias.$eq;
			$fullCuiAlias = $sq.$this->Cui66->Cui->alias.$eq;

			$conditionsSql = $Dbo->conditions( $conditions, true, true, $this );

			$sql = "UPDATE {$tableName} AS {$fullAlias} SET {$sq}etatdossiercui66{$eq} = {$case} FROM {$tableNameCui} AS {$fullCuiAlias} {$conditionsSql} AND {$fullCuiAlias}.{$sq}id{$eq} = {$fullAlias}.{$sq}cui_id{$eq};";

			$result = $Dbo->query( $sql ) !== false;

			// On regarde si des valeurs ont changés
			$query2 = array(
				'fields' => array( "{$this->Cui66->alias}.{$this->Cui66->primaryKey}", "{$this->Cui66->alias}.etatdossiercui66" ),
				'conditions' => $conditions,
				'joins' => array( $this->Cui66->join( 'Cui' ) )
			);
			$datas2 = $this->Cui66->find( 'all', $query2 );

			// On génère une requete si il y a eu changement
			$different = false;
			$updateValues = array();
			foreach( $datas as $data ){
				foreach( $datas2 as $data2 ){ // Logiquement, ne tournera qu'une seule fois
					if ( $data['Cui66']['id'] === $data2['Cui66']['id'] && $data['Cui66']['etatdossiercui66'] !== $data2['Cui66']['etatdossiercui66'] ){
						$different = true;
						$updateValues[] = array( 'cui66_id' => $data2['Cui66']['id'], 'etatdossiercui66' => $data2['Cui66']['etatdossiercui66'] );
					}
				}
			}
			if ( $different ){
				$result = $result && $this->Cui66->Historiquepositioncui66->saveMany( $updateValues );
				$this->updateDecisionCui($conditions);
			}

			return $result;
		}

		/**
		 * Mise à jour des positions des CUI qui devraient se trouver dans une
		 * position donnée.
		 *
		 * @param integer $etatdossiercui66
		 * @return boolean
		 */
		public function updatePositionsCuisByPosition( $etatdossiercui66 ) {
			$conditions = $this->getConditionsPositioncui( $etatdossiercui66 );

			$query = array(
				'fields' => array( "{$this->Cui66->alias}.{$this->Cui66->primaryKey}" ),
				'conditions' => $conditions,
				'joins' => array( $this->Cui66->join( 'Cui' ) )
			);
			$sample = $this->Cui66->find( 'first', $query );

			return (
				empty( $sample )
				|| $this->Cui66->updateAllUnBound(
					array( "{$this->Cui66->alias}.etatdossiercui66" => "'{$etatdossiercui66}'" ),
					$conditions
				)
			);
		}

		/**
		 * Permet de mettre à jour les positions des CUI d'un allocataire retrouvé
		 * grâce à la clé primaire d'un CUI en particulier.
		 *
		 * @param integer $id La clé primaire d'un CUI.
		 * @return boolean
		 */
		public function updatePositionsCuisById( $id ) {
			$return = $this->updatePositionsCuisByConditions(
				array( "Cui.id" => $id )
			);

			return $return;
		}

		/**
		 * Ajoute les données des modules liés au CUI pour l'impression
		 *
		 * @param array $data
		 * @return type
		 */
		public function completeDataImpression( array $data ) {
			$cui66_id = Hash::get( $data, 'Cui66.id' );
			$data = array( $data );

			$modeles = array(
				'Propositioncui66',
				'Decisioncui66',
				'Accompagnementcui66',
				'Suspensioncui66',
				'Rupturecui66'
			);

			foreach( $modeles as $modele ) {
				$query = $this->Cui66->{$modele}->getCompleteDataImpressionQuery( $cui66_id );
				$data[$modele] = $this->Cui66->{$modele}->find( 'all', $query );
			}

			return $data;
		}

		/**
		 * Met à jour les decision_cui
		 *
		 * @param array|string $conditions
		 * @return boolean
		 */
		public function updateDecisionCui($conditions = array()) {
			$results = $this->Cui66->find('all',
				array(
					'fields' => array(
						'Cui66.cui_id',
						'Cui66.etatdossiercui66',
					),
					'joins' => array(
						$this->Cui66->join('Cui')
					),
					'conditions' => $conditions
				)
			);

			if (empty($results)) {
				return false;
			}

			foreach ($this->Cui66->correspondance_decision_ci as $decision_cui => $etatdossiercui66) {
				foreach ($results as $value) {
					if (in_array(Hash::get($value, 'Cui66.etatdossiercui66'), $etatdossiercui66)) {
						$this->Cui66->Cui->updateAllUnBound(
							array('Cui.decision_cui' => "'$decision_cui'"),
							array(
								'"Cui"."id"' => Hash::get($value, 'Cui66.cui_id'),
							)
						);
					}
				}
			}

			return true;
		}
	}