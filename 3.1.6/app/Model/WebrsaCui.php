<?php
	/**
	 * Code source de la classe WebrsaCui.
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
	 * La classe WebrsaCui possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaCui extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCui';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Cui');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 * 
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$departement = (integer)Configure::read('Cg.departement');
			$modelDepartement = 'Cui'.$departement;
			$fields = array(
				
			);
			
			if (isset($this->Cui->{$modelDepartement})) {
				if (!isset($query['joins'])) {
					$query['joins'] = array();
				}
				if (WebrsaModelUtility::findJoinKey($modelDepartement, $query) === false) {
					$query['joins'][] = $this->Cui->join($modelDepartement);
				}
				
				$fields[] = $modelDepartement.'.cui_id';
				
				if ($departement === 66) {
					$fields[] = 'Cui66.etatdossiercui66';
				}
			}
			
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
					'Cui.id',
					'Cui.personne_id',
					'Personne.id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Cui->join('Personne')
				),
				'contain' => false,
				'order' => array(
					'Cui.created' => 'DESC',
					'Cui.id' => 'DESC',
				)
			);
			
			$results = $this->Cui->find('all', $this->completeVirtualFieldsForAccess($query, $params));
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
				$results['ajoutPossible'] = $this->ajoutPossible($personne_id, $params);
			}
			if (in_array('isModuleEmail', $params)) {
				$results['isModuleEmail'] = true;
			}
			
			return $results;
		}
		
		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 * 
		 * @param integer $personne_id
		 * @param array $params
		 * @return boolean
		 */
		public function ajoutPossible($personne_id, array $params = array()) {
			return true;
		}
		
		/**
		 * Recherche des données CAF liées à l'allocataire dans le cadre du CUI.
		 *
		 * @param integer $personne_id
		 * @return array
		 * @throws NotFoundException
		 * @throws InternalErrorException
		 */
		public function dataCafAllocataire( $personne_id ) {
			$Informationpe = ClassRegistry::init( 'Informationpe' );
            $sqDernierReferent = $this->Cui->Personne->PersonneReferent->sqDerniere( 'Personne.id', false );

			$querydataCaf = array(
				'fields' => array_merge(
					$this->Cui->Personne->fields(),
					$this->Cui->Personne->Prestation->fields(),
					$this->Cui->Personne->Foyer->fields(),
					$this->Cui->Personne->Foyer->Adressefoyer->Adresse->fields(),
					$this->Cui->Personne->Foyer->Dossier->fields(),
                    $this->Cui->Personne->PersonneReferent->Referent->fields(),
					array(
						'Historiqueetatpe.identifiantpe',
						'Historiqueetatpe.etat',
                        '( '.$this->Cui->Personne->Foyer->vfNbEnfants().' ) AS "Foyer__nbenfants"',
                        'Titresejour.dftitsej'
					)
				),
				'joins' => array(
					$Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', 'LEFT OUTER' ),
					$Informationpe->join( 'Historiqueetatpe', array( 'type' => 'LEFT OUTER' ) ),
					$this->Cui->Personne->join( 'Foyer', array( 'type' => 'INNER' )),
					$this->Cui->Personne->join( 'Prestation', array( 'type' => 'LEFT OUTER'  )),
					$this->Cui->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Cui->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$this->Cui->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
                    $this->Cui->Personne->join(
                        'PersonneReferent',
                        array(
                            'type' => 'LEFT OUTER',
                            'conditions' => array(
                                "PersonneReferent.id IN ( {$sqDernierReferent} )"
                            )
                        )
                    ),
                    $this->Cui->Personne->PersonneReferent->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
                    $this->Cui->Personne->join( 'Titresejour', array( 'type' => 'LEFT OUTER' ) )
				),
				'conditions' => array(
					'Personne.id' => $personne_id,
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ( '.$this->Cui->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
						)
					),
					array(
						'OR' => array(
							'Informationpe.id IS NULL',
							'Informationpe.id IN( '.$Informationpe->sqDerniere( 'Personne' ).' )'
						)
					),
					array(
						'OR' => array(
							'Historiqueetatpe.id IS NULL',
							'Historiqueetatpe.id IN( '.$Informationpe->Historiqueetatpe->sqDernier( 'Informationpe' ).' )'
						)
					),
				),
				'contain' => false
			);
			$dataCaf = $this->Cui->Personne->find( 'first', $querydataCaf );


			// On s'assure d'avoir trouvé l'allocataire
			if( empty( $dataCaf ) ) {
				throw new NotFoundException();
			}

			// Et que celui-ci soit bien demandeur ou conjoint
			if( !in_array( $dataCaf['Prestation']['rolepers'], array( 'DEM', 'CJT' ) ) ) {
				throw new InternalErrorException( "L'allocataire \"{$personne_id}\" doit être demandeur ou conjont" );
			}

			return $dataCaf;
		}

		/**
		 * Permet de savoir si une personne lié au CUI possède un RSA Socle
		 * 
		 * @param numeric $personne_id
		 * @return boolean
		 */
		public function isRsaSocle( $personne_id ){
			$vfRsaSocle = $this->Cui->Personne->Foyer->Dossier->Detaildroitrsa->vfRsaSocle();
			$result = $this->Cui->Personne->find(
				'first',
				array(
					'fields' => array(
						"( {$vfRsaSocle} ) AS \"Dossier__rsasocle\""
					),
					'joins' => array(
						$this->Cui->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Cui->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Cui->Personne->Foyer->Dossier->join( 'Detaildroitrsa' )
					),
					'conditions' => array(
						'Personne.id' => $personne_id
					),
					'recursive' => -1
				)
			);			
			$isRsaSocle = isset($result['Dossier']['rsasocle']) && $result['Dossier']['rsasocle'] === true ? true : false;
			return $isRsaSocle;
		}
		
		/** 	 
		 * Sous-requête permettant de récupérer le dernier CUI d'un allocataire. 	 
		 * 	 
		 * @param string $personneIdField Le champ où trouver l'id de la personne. 	 
		 * @return string 	 
		 */ 	 
		public function sqDernierContrat( $personneIdField = 'Personne.id' ) {
			return $this->Cui->sq( 	 
				array( 	 
					'fields' => array( 	 
						'cuis.id' 	 
					),
					'alias' => 'cuis', 	 
					'conditions' => array( 	 
						"cuis.personne_id = {$personneIdField}" 	 
					),
					'order' => array( 'cuis.faitle DESC', 'cuis.created DESC' ), 	 
					'limit' => 1 	 
				) 	 
			); 	 
		}
		
		/**
		 * Revoi la requete pour récuperer toutes les données pour l'affichage de l'index du CUI
		 * 
		 * @param integer $personne_id
		 * @return array
		 */
		public function queryIndex($personne_id){
			switch ((int)Configure::read('Cg.departement')) {
				case 66: $query = $this->Cui->Cui66->WebrsaCui66->queryIndex($personne_id); break;
				default: $query = array(
					'fields' => array_merge(
						$this->Cui->fields(),
						$this->Cui->Partenairecui->fields(),
						array(
							$this->Cui->Fichiermodule->sqNbFichiersLies( $this->Cui, 'nombre' ),
						)
					),
					'conditions' => array(
						'Cui.personne_id' => $personne_id
					),
					'joins' => array(
						$this->Cui->join( 'Partenairecui' ),
					),
					'order' => array( 'Cui.created DESC' )
				);
			}
			
			return $query;
		}
		
		/**
		 * Affiche des messages dans index
		 * 
		 * @param integer $personne_id
		 * @return array
		 */
		public function messages( $personne_id ) {
			$messages = array();
			$isRsaSocle = $this->isRsaSocle( $personne_id );
			
			if ( !$isRsaSocle ){
				$messages['Personne.rsasocle'] = 'error';
			}

			return $messages;
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
		 * Retourne les options nécessaires au formulaire de recherche, au formulaire,
		 * aux impressions, ...
		 *
		 * @param integer $user_id
		 * @return array
		 */
		public function options($user_id = null) {
			switch ((int)Configure::read('Cg.departement')) {
				case 66: $options = $this->Cui->Cui66->WebrsaCui66->options(); break;
				default: 
					$options = Hash::merge(
						$this->Cui->enums(),
						$this->Cui->Partenairecui->enums(),
						$this->Cui->Personnecui->enums()
					);
					
					foreach( $this->Cui->beneficiairede as $value ){
						$options['Cui']['beneficiairede'][] = $value;
					}
					
					if ($user_id !== null) {
						// Récupération de la liste des actions avec une fiche de candidature (pour Cui.organismedesuivi)
						$qd_user = array(
							'conditions' => array(
								'User.id' => $user_id
							),
							'fields' => null,
							'order' => null,
							'contain' => array(
								'Serviceinstructeur'
							)
						);
						$user = $this->Cui->User->find( 'first', $qd_user );

						$codeinseeUser = Set::classicExtract( $user, 'Serviceinstructeur.code_insee' );

						// On affiche les actions inactives en édition mais pas en ajout,
						// afin de pouvoir gérer les actions n'étant plus prises en compte mais toujours en cours
						$isactive = 'O';
						if( $this->Cui->action == 'edit' ){
							$isactive = array( 'O', 'N' );
						}

						$actions = array();
						foreach(ClassRegistry::init('Actioncandidat')->listePourFicheCandidature( $codeinseeUser, $isactive, '1' ) as $action) {
							$actions[$action] = $action;
						}
						$options['Cui']['organismedesuivi'] = $actions;
					}

					// Ajout de la liste des partenaires
					$options['Cui']['partenaire_id'] = $this->Cui->Partenaire->find( 'list', array( 'order' => array( 'Partenaire.libstruc' ) ) );

					// Liste des cantons pour l'adresse du partenaire
					App::import('Component','Gestionzonesgeos');
					$Gestionzonesgeos = new GestionzonesgeosComponent(new ComponentCollection());
					$options['Adressecui']['canton'] = $Gestionzonesgeos->listeCantons();
					$options['Adressecui']['canton2'] =& $options['Adressecui']['canton'];
			}

			return $options;
		}
		
		/**
		 * Sauvegarde d'un CUI
		 * 
		 * @param array $data
		 * @return boolean
		 */
		public function saveAddEdit( array $data, $user_id = null ) {
			switch ((int)Configure::read('Cg.departement')) {
				case 66: $success = $this->Cui->Cui66->WebrsaCui66->saveAddEdit($data, $user_id); break;
				default: 
					$success = true;
					$data['Cui']['user_id'] = $user_id;
					
					// Si un code famille (rome v3) est vide, on ne sauvegarde pas le code rome
					if ( !isset($data['Entreeromev3']['familleromev3_id']) || $data['Entreeromev3']['familleromev3_id'] === '' ){ 
						$data['Cui']['entreeromev3_id'] = null;

						// Si le code rome avait un id, on supprime l'entreeromev3 correspondant
						if ( isset($data['Entreeromev3']['id']) && $data['Entreeromev3']['id'] !== '' ){
							$this->Cui->Entreeromev3->id = $data['Entreeromev3']['id'];
							$success = $success && $this->Cui->Entreeromev3->delete();
						}
					}
					// Dans le cas contraire, on enregistre le tout
					else{
						$this->Cui->Entreeromev3->create($data);
						$success = $success && $this->Cui->Entreeromev3->save();
						$data['Cui']['entreeromev3_id'] = $this->Cui->Entreeromev3->id;
					}

					// Si le contrat est un CDI, on s'assure que la date de fin soit nulle
					if ( $data['Cui']['typecontrat'] === 'CDI' ){
						$data['Cui']['findecontrat'] = null;
					}
					
					// Partenairecui possède une Adressecui, on commence par cette dernière
					$this->Cui->Partenairecui->Adressecui->create($data);
					$success = $success && $this->Cui->Partenairecui->Adressecui->save();
					$data['Partenairecui']['adressecui_id'] = $this->Cui->Partenairecui->Adressecui->id;
					
					// Cui possède un Partenairecui, il nous faut son id
					$this->Cui->Partenairecui->create($data);
					$success = $success && $this->Cui->Partenairecui->save();
					$data['Cui']['partenairecui_id'] = $this->Cui->Partenairecui->id;
					
					// Cui possède un Personnecui
					$this->Cui->Personnecui->create($data);
					$success = $success && $this->Cui->Personnecui->save();
					$data['Cui']['personnecui_id'] = $this->Cui->Personnecui->id;

					// On termine par le Cui
					$this->Cui->create($data);
					$success = $success && $this->Cui->save();
			}

			return $success;
		}
		
		/**
		 * Mise à jour des positions des CUI suivant des conditions données.
		 *
		 * @param array $conditions
		 * @return boolean
		 */
		public function updatePositionsCuisByConditions( array $conditions ) {
			switch ((int)Configure::read('Cg.departement')) {
				case 66: $success = $this->Cui->Cui66->WebrsaCui66->updatePositionsCuisByConditions($conditions); break;
				default: $success = true;
			}
			
			return $success;
		}

		/**
		 * Mise à jour des positions des CUI qui devraient se trouver dans une
		 * position donnée.
		 *
		 * @param integer $position
		 * @return boolean
		 */
		public function updatePositionsCuisByPosition( $position ) {
			switch ((int)Configure::read('Cg.departement')) {
				case 66: $success = $this->Cui->Cui66->WebrsaCui66->updatePositionsCuisByPosition($position); break;
				default: $success = true;
			}
			
			return $success;
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
		 * Récupère les donnés par defaut dans le cas d'un ajout, ou récupère les données stocké en base dans le cas d'une modification
		 * 
		 * @param integer $personne_id
		 * @param integer $id
		 * @return array
		 */
		public function prepareFormDataAddEdit( $personne_id, $id = null ) {
			switch ((int)Configure::read('Cg.departement')) {
				case 66: $result = $this->Cui->Cui66->WebrsaCui66->prepareFormDataAddEdit($personne_id, $id); break;
				default:
					// Ajout
					if( empty( $id ) ) {
						$query = $this->Cui->Personne->PersonneReferent->completeSearchQueryReferentParcours( array(
							'fields' => array(
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
							),
							'recursive' => -1,
							'conditions' => array(
								'Personne.id' => $personne_id
							),
							'joins' => array(
								$this->Cui->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
								$this->Cui->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
							)
						));

						$record = $this->Cui->Personne->find( 'first', $query );

						/** 
						 * INFO: si one ne met pas le modèle Adressecui dans le $this->Cui->request->data, il n'est
						 * pas instancié dans la vue, donc pas d'astérisque ni validation javascript...
						 */
						$result = array(
							'Cui' => array(
								'personne_id' => $personne_id,
								'numconventionobjectif' => Configure::read( 'Cui.Numeroconvention' ),
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
							'Adressecui' => array()
						);
					}
					// Mise à jour
					else {
						$query = $this->queryView($id);
						$result = $this->Cui->find( 'first', $query );

						$result = $this->Cui->Entreeromev3->prepareFormDataAddEdit( $result );
					}
			}
			
			return $result;
		}
		
		/**
		 * Permet d'obtenir les informations lié à un Allocataire d'un Cui
		 * 
		 * @param integer $personne_id
		 * @return array
		 */
		public function queryPersonne( $personne_id ){
			$query = ClassRegistry::init( 'Allocataire' )->searchQuery();

			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Titresejour.dftitsej',
					'Departement.name',
					'( '.$this->Cui->Personne->Foyer->vfNbEnfants().' ) AS "Foyer__nb_enfants"'
				)
			);

			$query['joins'][] = $this->Cui->Personne->Foyer->Adressefoyer->Adresse->join( 'Departement', array( 'type' => 'LEFT OUTER' ) );
			$query['joins'][] = $this->Cui->Personne->join( 'Titresejour', array( 'type' => 'LEFT OUTER' ) );
			
			$query['conditions']['Personne.id'] = $personne_id;
			
			return $query;
		}
		
		/**
		 * Revoi la requete pour récuperer toutes les données pour l'affichage d'un CUI (Hors modules)
		 * 
		 * @param integer $id
		 * @return array
		 */
		public function queryView( $id = null ){
			switch ((int)Configure::read('Cg.departement')) {
				case 66: $query = $this->Cui->Cui66->WebrsaCui66->queryView($id); break;
				default:
					$query = array(
						'fields' => array_merge(
							$this->Cui->fields(),
							$this->Cui->Personnecui->fields(),
							$this->Cui->Partenairecui->fields(),
							$this->Cui->Entreeromev3->fields(),
							$this->Cui->Partenairecui->Adressecui->fields()
						),
						'recursive' => -1,
						'conditions' => array(),
						'joins' => array(
							$this->Cui->join( 'Personnecui' ),
							$this->Cui->join( 'Partenairecui' ),
							$this->Cui->join( 'Entreeromev3' ),
							$this->Cui->Partenairecui->join( 'Adressecui' ),						
						)
					);

					if( $id !== null ) {
						$query['conditions']['Cui.id'] = $id;
					}
			}
			
			return $query;
		}
	}