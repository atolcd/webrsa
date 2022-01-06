<?php
	/**
	 * Code source de la classe Fichesprescriptions93Controller.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Fichesprescriptions93Controller ...
	 *
	 * @todo exportcsv
	 *
	 * @package app.Controller
	 */
	class Fichesprescriptions93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Fichesprescriptions93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'DossiersMenus',
			'Gedooo.Gedooo',
			'InsertionsBeneficiaires',
			'Jetons2',
			'Search.SearchFiltresdefaut' => array(
				'search'
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'search' => array('filter' => 'Search')
				),
			),
			'WebrsaAccesses',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Ajax2' => array(
				'className' => 'Prototype.PrototypeAjax',
				'useBuffer' => false
			),
			'Allocataires',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Search.SearchForm',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Ficheprescription93',
			'Personne',
			'Infocontactpersonne',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'ajax_action' => 'Fichesprescriptions93:index',
			'ajax_duree_pdi' => 'Fichesprescriptions93:index',
			'ajax_prescripteur' => 'Fichesprescriptions93:index',
			'ajax_prestataire' => 'Fichesprescriptions93:index',
			'ajax_prestataire_horspdi' => 'Fichesprescriptions93:index'
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(

		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajax_action' => 'read',
			'ajax_duree_pdi' => 'read',
			'ajax_prescripteur' => 'read',
			'ajax_prestataire' => 'read',
			'ajax_prestataire_horspdi' => 'read',
			'cancel' => 'update',
			'edit' => 'update',
			'exportcsv' => 'read',
			'impression' => 'read',
			'index' => 'read',
			'search' => 'read',
		);

		/**
		 * Le json "vide" utilisé dans la méthode ajax_prestataire_horspdi().
		 *
		 * @var array
		 */
		public $jsonAjaxPrestataireHorspdi = array(
			'success' => true,
			'fields' => array(
				'Prestatairehorspdifp93.name' => array(
					'id' => 'Prestatairehorspdifp93Name',
					'value' => null,
					'type' => 'text',
					'prefix' => null,
					'options' => array()
				),
				'Ficheprescription93.selection_adresse_prestataire' => array(
					'id' => 'Ficheprescription93SelectionAdressePrestataire',
					'value' => null,
					'type' => 'select',
					'prefix' => null,
					'options' => array()
				),
				'Prestatairehorspdifp93.adresse' => array(
					'id' => 'Prestatairehorspdifp93Adresse',
					'value' => null,
					'type' => 'text',
					'prefix' => null
				),
				'Prestatairehorspdifp93.codepos' => array(
					'id' => 'Prestatairehorspdifp93Codepos',
					'value' => null,
					'type' => 'text',
					'prefix' => null
				),
				'Prestatairehorspdifp93.localite' => array(
					'id' => 'Prestatairehorspdifp93Localite',
					'value' => null,
					'type' => 'text',
					'prefix' => null
				),
				'Prestatairehorspdifp93.tel' => array(
					'id' => 'Prestatairehorspdifp93Tel',
					'value' => null,
					'type' => 'text',
					'prefix' => null
				),
				'Prestatairehorspdifp93.fax' => array(
					'id' => 'Prestatairehorspdifp93Fax',
					'value' => null,
					'type' => 'text',
					'prefix' => null
				),
				'Prestatairehorspdifp93.email' => array(
					'id' => 'Prestatairehorspdifp93Email',
					'value' => null,
					'type' => 'text',
					'prefix' => null
				),
			)
		);

		/**
		 * Ajax permettant de récupérer les coordonnées du prescripteur ou de sa
		 * structure.
		 */
		public function ajax_prescripteur() {
			$structurereferente_id = Hash::get( $this->request->data, 'Ficheprescription93.structurereferente_id' );
			$referent_id = suffix( Hash::get( $this->request->data, 'Ficheprescription93.referent_id' ) );

			$result = array();
			if( !empty( $structurereferente_id ) ) {
				$query = array(
					'fields' => array(
						'Structurereferente.num_voie',
						'Structurereferente.type_voie',
						'Structurereferente.nom_voie',
						'Structurereferente.code_postal',
						'Structurereferente.ville',
						'Structurereferente.numtel',
						'Structurereferente.numfax',
					),
					'contain' => false,
					'joins' => array(),
					'conditions' => array(
						'Structurereferente.id' => $structurereferente_id,
					)
				);

				if( !empty( $referent_id ) ) {
					$query['fields'][] = 'Referent.email';
					$query['fields'][] = 'Referent.fonction';
					$query['joins'][] = $this->Ficheprescription93->Referent->Structurereferente->join( 'Referent', array( 'type' => 'INNER' ) );
					$query['conditions']['Referent.id'] = $referent_id;
				}

				$result = $this->Ficheprescription93->Referent->Structurereferente->find( 'first', $query );
			}

			$this->set( compact( 'result' ) );
			$this->layout = 'ajax';
		}

		/**
		 * Ajax permettant de récupérer les coordonnées du prestataire PDI.
		 */
		public function ajax_prestataire() {
			$adresseprestatairefp93_id = suffix( Hash::get( $this->request->data, 'Ficheprescription93.adresseprestatairefp93_id' ) );

			$result = array();
			if( !empty( $adresseprestatairefp93_id ) ) {
				$query = array(
					'fields' => $this->Ficheprescription93->Actionfp93->Adresseprestatairefp93->fields(),
					'contain' => false,
					'joins' => array(),
					'conditions' => array(
						'Adresseprestatairefp93.id' => $adresseprestatairefp93_id,
					)
				);

				$result = $this->Ficheprescription93->Actionfp93->Adresseprestatairefp93->find( 'first', $query );
			}

			$this->set( compact( 'result' ) );
			$this->layout = 'ajax';
		}

		/**
		 * Ajax permettant de récupérer la durée d'une action PDI.
		 */
		public function ajax_duree_pdi() {
			$typethematiquefp93_id = Hash::get( $this->request->data, 'Ficheprescription93.typethematiquefp93_id' );
			$actionfp93_id = Hash::get( $this->request->data, 'Ficheprescription93.actionfp93_id' );

			$result = array();
			if( !empty( $typethematiquefp93_id ) ) {
				if( ( $typethematiquefp93_id === 'pdi' ) && !empty( $actionfp93_id ) ) {
					$query = array(
						'fields' => array( 'Actionfp93.duree' ),
						'conditions' => array(
							'Actionfp93.id' => $actionfp93_id,
						)
					);

					$result = $this->Ficheprescription93->Actionfp93->find( 'first', $query );
					$result = array( 'Ficheprescription93' => array( 'duree_action' => Hash::get( $result, 'Actionfp93.duree' ) ) );
				}
				else if( $typethematiquefp93_id === 'horspdi' ) {
					$result = false;
				}
			}

			$this->request->data = $result;

			$this->layout = 'ajax';
		}

		/**
		 * Retourne la liste des options du select permettant de choisir l'adresse
		 * d'un prestataire hors PDI.
		 *
		 * @param integer $prestatairefp93_id
		 * @return array
		 */
		protected function _ajax_options_adresses_prestataire_horspdi( $prestatairefp93_id ) {
			$query = array(
				'conditions' => array(
					'Adresseprestatairefp93.prestatairefp93_id' => $prestatairefp93_id
				)
			);
			$results = $this->Ficheprescription93->Adresseprestatairefp93->find( 'all', $query );

			$adresses = array();
			foreach( (array)Hash::extract( $results, '{n}.Adresseprestatairefp93' ) as $adresse ) {
				$title = array();
				$details = array(
					'tel' => 'Tél.: %s',
					'fax' => 'Fax.: %s',
					'email' => 'Email: %s',
				);
				foreach( $details as $fieldName => $label ) {
					if( !empty( $adresse[$fieldName] ) ) {
						$title[] = sprintf( $label, $adresse[$fieldName] );
					}
				}

				$adresses[] = array(
					'id' => $adresse['id'],
					'name' => "{$adresse['adresse']}, {$adresse['codepos']} {$adresse['localite']}",
					'title' => implode( ', ', $title )
				);
			}

			return $adresses;
		}

		/**
		 *
	     */
		public function ajax_prestataire_horspdi() {
			$json = $this->jsonAjaxPrestataireHorspdi;

			$adressePaths = array( 'adresse', 'codepos', 'localite', 'tel', 'fax', 'email'	);

			$event = Hash::get( $this->request->data, 'Event.type' );

			if( $event === 'dataavailable' ) {
				// On remet les valeurs qui ont été renvoyées par le formulaire
				foreach( array_keys( $json['fields'] ) as $path ) {
					$json['fields'][$path]['value'] = Hash::get( $this->request->data, $path );
				}

				// Si on avait une adresse / un prestataire sélectionné -> on re-remplit la liste
				$prestatairefp93_id = null;
				if( !empty( $json['fields']['Ficheprescription93.selection_adresse_prestataire']['value'] ) ) {
					$query = array(
						'fields' => array( 'Adresseprestatairefp93.prestatairefp93_id' ),
						'conditions' => array(
							'Adresseprestatairefp93.id' => $json['fields']['Ficheprescription93.selection_adresse_prestataire']['value']
						)
					);
					$result = $this->Ficheprescription93->Adresseprestatairefp93->find( 'first', $query );
					$prestatairefp93_id = Hash::get( $result, 'Adresseprestatairefp93.prestatairefp93_id' );
				}
				else if( !empty( $json['fields']['Prestatairehorspdifp93.name']['value'] ) ) {
					$query = array(
						'fields' => array( 'Prestatairefp93.id' ),
						'conditions' => array(
							'NOACCENTS_UPPER( Prestatairefp93.name )' => noaccents_upper( $json['fields']['Prestatairehorspdifp93.name']['value'] )
						)
					);
					$result = $this->Ficheprescription93->Adresseprestatairefp93->Prestatairefp93->find( 'first', $query );
					$prestatairefp93_id = Hash::get( $result, 'Prestatairefp93.id' );
				}

				if( !empty( $prestatairefp93_id ) ) {
					$adresses = $this->_ajax_options_adresses_prestataire_horspdi( $prestatairefp93_id );
					$json['fields']['Ficheprescription93.selection_adresse_prestataire']['options'] = $adresses;
				}
			}
			// On a commencé à taper le nom du prestataire hors PDI
			else if( $event === 'keyup' ) {
				$prestatairehorspdifp93Name = Hash::get( $this->request->data, 'Prestatairehorspdifp93.name' );
				if( !empty( $prestatairehorspdifp93Name ) ) {
					$query = array(
						'fields' => array(
							'Prestatairefp93.id',
							'Prestatairefp93.name'
						),
						'conditions' => array(
							'NOACCENTS_UPPER( Prestatairefp93.name ) LIKE' => noaccents_upper( $prestatairehorspdifp93Name ).'%'
						)
					);
					$results = $this->Ficheprescription93->Adresseprestatairefp93->Prestatairefp93->find( 'all', $query );
					$json = array(
						'success' => true,
						'fields' => array(
							'Prestatairehorspdifp93.name' => array(
								'id' => 'Prestatairehorspdifp93Name',
								// INFO: On n'envoie pas la valeur pour ne pas perturber la saisie
								// 'value' => $prestatairehorspdifp93Name,
								'type' => 'ajax_select',
								'prefix' => null,
								'options' => Hash::extract( $results, '{n}.Prestatairefp93' )
							)
						)
					);
				}
			}
			// On a sélectionné un prestataire hors PDI dans la liste Ajax
			else if( $event === 'click' ) {
				$field = Hash::get( $this->request->data, 'name' );
				$field = str_replace( '][', '.', preg_replace( '/^data\[(.*)\]$/', '\1', $field ) );

				if( $field === 'Prestatairehorspdifp93.name' ) {
					$prestatairefp93_id = Hash::get( $this->request->data, 'value' );
					$query = array(
						'conditions' => array(
							'Prestatairefp93.id' => $prestatairefp93_id
						),
						'contain' => array(
							'Adresseprestatairefp93'
						)
					);
					$result = $this->Ficheprescription93->Adresseprestatairefp93->Prestatairefp93->find( 'first', $query );
					$json['fields']['Prestatairehorspdifp93.name']['value'] = Hash::get( $result, 'Prestatairefp93.name' );

					$adresses = $this->_ajax_options_adresses_prestataire_horspdi( $prestatairefp93_id );
					$json['fields']['Ficheprescription93.selection_adresse_prestataire']['options'] = $adresses;

					// Si on n'a qu'une seule adresse, on préremplit tout
					if( count( $adresses ) === 1 ) {
						$json['fields']['Ficheprescription93.selection_adresse_prestataire']['value'] = $result['Adresseprestatairefp93'][0]['id'];

						foreach( $adressePaths as $adressePath ) {
							$fromPath = "Adresseprestatairefp93.0.{$adressePath}";
							$toPath = "Prestatairehorspdifp93.{$adressePath}";
							$json['fields'][$toPath]['value'] = Hash::get( $result, $fromPath );
						}
					}
				}
			}
			// On a sélectionné une adresse de prestataire dans la liste
			else if( $event === 'change' ) {
				$field = Hash::get( $this->request->data, 'Target.name' );
				$field = str_replace( '][', '.', preg_replace( '/^data\[(.*)\]$/', '\1', $field ) );

				if( $field === 'Ficheprescription93.selection_adresse_prestataire' ) {
					unset( $json['fields']['Prestatairehorspdifp93.name'] );

					$adresseprestatairefp93_id = Hash::get( $this->request->data, 'Ficheprescription93.selection_adresse_prestataire' );

					if( !empty( $adresseprestatairefp93_id ) ) {
						$query = array(
							'conditions' => array(
								'Adresseprestatairefp93.id' => $adresseprestatairefp93_id
							)
						);
						$result = $this->Ficheprescription93->Adresseprestatairefp93->find( 'first', $query );
						$json['fields']['Ficheprescription93.selection_adresse_prestataire']['options'] = $this->_ajax_options_adresses_prestataire_horspdi( Hash::get( $result, 'Adresseprestatairefp93.prestatairefp93_id' ) );
						$json['fields']['Ficheprescription93.selection_adresse_prestataire']['value'] = $adresseprestatairefp93_id;
					}
					else {
						$result = array();
						unset( $json['fields']['Ficheprescription93.selection_adresse_prestataire'] );
					}

					foreach( $adressePaths as $adressePath ) {
						$fromPath = "Adresseprestatairefp93.{$adressePath}";
						$toPath = "Prestatairehorspdifp93.{$adressePath}";
						$json['fields'][$toPath]['value'] = Hash::get( $result, $fromPath );
					}
				}
			}

			$this->set( compact( 'json' ) );
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}

		/**
		 * Ajax permettant le pré-remplissage des champs liés à l'action, en
		 * cascade.
		 */
		public function ajax_action() {
			$json = $this->Components->load( 'AjaxFichesprescriptions93' )->ajaxAction( $this->request->data );

			$this->set( compact( 'json' ) );
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}

		/**
		 * Moteur de recherche par fiche de prescription.
		 */
		public function search() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesFichesprescriptions93' );
			$Recherches->search();
		}

		/**
		 * Export CSV des résultats du moteur de recherche par fiche de prescription.
		 */
		public function exportcsv() {
			$Recherches = $this->Components->load( 'WebrsaRecherchesFichesprescriptions93' );
			$Recherches->exportcsv();
		}

		/**
		 * Liste des fiches de presciptions d'un allocataire.
		 *
		 * @param integer $personne_id
		 */
		public function index( $personne_id ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$this->_setEntriesAncienDossier( $personne_id, 'Ficheprescription93' );

			$messages = $this->Ficheprescription93->messages( $personne_id );
			$addEnabled = $this->Ficheprescription93->addEnabled( $messages );

			$query = array(
				'fields' => array(
					'Ficheprescription93.id',
					'Ficheprescription93.created',
					'Ficheprescription93.modified',
					'Thematiquefp93.type',
					'Thematiquefp93.name',
					'Thematiquefp93.yearthema',
					'Categoriefp93.name',
					'Prestatairehorspdifp93.name',
					'Prestatairefp93.name',
					'Actionfp93.name',
					'Ficheprescription93.actionfp93',
					'Ficheprescription93.dd_action',
					'Ficheprescription93.df_action',
					'Ficheprescription93.statut',
				),
				'conditions' => array(
					'Ficheprescription93.personne_id' => $personne_id
				),
				'contain' => false,
				'joins' => array(
					$this->Ficheprescription93->join( 'Actionfp93', array( 'type' => 'LEFT OUTER' ) ),
					$this->Ficheprescription93->Actionfp93->join( 'Adresseprestatairefp93', array( 'type' => 'LEFT OUTER' ) ),
					$this->Ficheprescription93->Actionfp93->Adresseprestatairefp93->join( 'Prestatairefp93', array( 'type' => 'LEFT OUTER' ) ),
					$this->Ficheprescription93->join( 'Filierefp93' ),
					$this->Ficheprescription93->join( 'Prestatairehorspdifp93', array( 'type' => 'LEFT OUTER' ) ),
					$this->Ficheprescription93->Filierefp93->join( 'Categoriefp93' ),
					$this->Ficheprescription93->Filierefp93->Categoriefp93->join( 'Thematiquefp93' )
				),
				'order' => array(
					'Ficheprescription93.created DESC'
				)
			);

			$results = $this->WebrsaAccesses->getIndexRecords( $personne_id,  $query );

			foreach ($results AS $key => $result )  {
				if ($result['Thematiquefp93']['type'] == 'pdi') {
					//'Prestatairefp93.name',
					$results[$key]['Ficheprescription93']['prestaname'] = $result['Prestatairefp93']['name'];
					//'Actionfp93.name',
					$results[$key]['Ficheprescription93']['actionname'] = $result['Actionfp93']['name'];
				} elseif ($result['Thematiquefp93']['type'] == 'horspdi') {
					//'Prestatairehorspdifp93.name',
					$results[$key]['Ficheprescription93']['prestaname'] = $result['Prestatairehorspdifp93']['name'];
					//'Ficheprescription93.actionfp93',
					$results[$key]['Ficheprescription93']['actionname'] = $result['Ficheprescription93']['actionfp93'] ;
				}	else {
					$results[$key]['Ficheprescription93']['prestaname'] = null;
					$results[$key]['Ficheprescription93']['actionname'] = null;
				}
			}

			$options = $this->Ficheprescription93->options();

			$this->set( compact( 'results', 'options', 'personne_id', 'messages', 'addEnabled' ) );
		}

		/**
		 * Formulaire d'ajout de fiche de prescription.
		 *
		 * @param integer $personne_id L'id de la Personne à laquelle on veut ajouter une fiche
		 */
		public function add( $personne_id ) {
			$this->WebrsaAccesses->check( null, $personne_id );

			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Formulaire de modification de fiche de prescription.
		 *
		 * @param integer $id L'id de la fiche que l'on veut modifier
		 */
		public function edit( $id ) {
			$this->WebrsaAccesses->check( $id );

			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Méthode générique d'ajout et de modification de fiche de prescription.
		 *
		 * @param integer $id L'id de la personne (add) ou de la fiche (edit)
		 */
		protected function _add_edit( $id = null ) {
			if( $this->action == 'add' ) {
				$personne_id = $id;
				$id = null;
			}
			else {
				$personne_id = $this->Ficheprescription93->personneId( $id );
			}

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$this->Jetons2->get( $dossierMenu['Dossier']['id'] );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Ficheprescription93->begin();
				if( $this->Ficheprescription93->saveAddEdit( $this->request->data ) ) {
					$this->Ficheprescription93->commit();
					if($this->request->data['Ficheprescription93']['action']=='add'){
						//Lors d'une création on historise le contact
						$personnedata['Personne']['id'] = $this->request->data['Ficheprescription93']['personne_id'];
						$personnedata['Personne']['numfixe'] = $this->request->data['Instantanedonneesfp93']['benef_tel_fixe'];
						$personnedata['Personne']['numport'] = $this->request->data['Instantanedonneesfp93']['benef_tel_port'];
						$personnedata['Personne']['email'] = $this->request->data['Instantanedonneesfp93']['benef_email'];
						$infocontactdata['Infocontactpersonne']['personne_id'] = $this->request->data['Ficheprescription93']['personne_id'];
						$infocontactdata['Infocontactpersonne']['fixe'] = $this->request->data['Instantanedonneesfp93']['benef_tel_fixe'];
						$infocontactdata['Infocontactpersonne']['mobile'] = $this->request->data['Instantanedonneesfp93']['benef_tel_port'];
						$infocontactdata['Infocontactpersonne']['email'] = $this->request->data['Instantanedonneesfp93']['benef_email'];
						$queryData =
							array(
								'fields' => array(
									'Personne.id',
									'Personne.numfixe',
									'Personne.numport',
									'Personne.email'
								),
								'recursive' => 0,
								'conditions' => array( 'Personne.id' => $this->request->data['Ficheprescription93']['personne_id'] ),
							);
						$personne = $this->Personne->find('first', $queryData);
						if (
							$personne['Personne']['numfixe'] != $infocontactdata['Infocontactpersonne']['fixe']
							|| $personne['Personne']['numport'] != $infocontactdata['Infocontactpersonne']['mobile']
							|| $personne['Personne']['email'] != $infocontactdata['Infocontactpersonne']['email']
						) {
							//Sauvegarder les informations de contact sur la personne
							$this->Personne->begin();
							$this->Personne->create($personnedata);
							if ($this->Personne->save( null, array( 'atomic' => false ) )) {
								$this->Personne->commit();
							}
							//Historiser la nouvelle information contact
							$this->Infocontactpersonne->begin();
							$this->Infocontactpersonne->create( $infocontactdata );
							if ($this->Infocontactpersonne->save( null, array( 'atomic' => false ) )) {
								$this->Infocontactpersonne->commit();
							}
						}
					}
					$this->Flash->success( __( 'Save->success' ) );
					$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
					$this->redirect( array( 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Ficheprescription93->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				$this->request->data = $this->Ficheprescription93->prepareFormDataAddEdit( $personne_id, $id );
			}

			$options = $this->Ficheprescription93->options( array( 'allocataire' => true, 'find' => true, 'autre' => true ) );

			$options['Ficheprescription93']['structurereferente_id'] = $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ), true );
			$options['Ficheprescription93']['referent_id'] = $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) );

			// INFO: si la structure ou le référent enregistrés ne se trouvent pas dans les options, on les ajoute
			if( !empty( $this->request->data ) ) {
				$options['Ficheprescription93'] = $this->InsertionsBeneficiaires->completeOptions(
					$options['Ficheprescription93'],
					$this->request->data['Ficheprescription93'],
					array(
						'structuresreferentes' => array(
							'type' => 'optgroup',
							'prefix' => false
						),
						'referents' => array(
							'type' => 'list',
							'prefix' => true
						)
					)
				);
			}

			// On complète les options de l'adresse du prestataire PDI s'il y a lieu
			if( $this->action === 'edit' ) {
				$adresseprestatairefp93_id = Hash::get( $this->request->data, 'Ficheprescription93.adresseprestatairefp93_id' );
				if( !empty( $adresseprestatairefp93_id ) ) {
					$query = array(
						'conditions' => array(
							'Adresseprestatairefp93.prestatairefp93_id' => Hash::get( $this->request->data, 'Ficheprescription93.prestatairefp93_id' )
						)
					);
					$options['Ficheprescription93']['adresseprestatairefp93_id'] = $this->Ficheprescription93->Adresseprestatairefp93->find( 'list', $query );
				}
			}

			$urlmenu = "/fichesprescriptions93/index/{$personne_id}";

			$this->set( compact( 'options', 'personne_id', 'dossierMenu', 'urlmenu' ) );
			$this->render( 'add_edit' );
		}

		/**
		 * Imprime une fiche de prescription.
		 *
		 * @param integer $ficheprescription93_id
		 * @return void
		 */
		public function impression( $ficheprescription93_id = null ) {
			$this->WebrsaAccesses->check( $ficheprescription93_id );

			$personne_id = $this->Ficheprescription93->personneId( $ficheprescription93_id );
			$this->DossiersMenus->checkDossierMenu( array( 'personne_id' => $personne_id ) );

			$pdf = $this->Ficheprescription93->getDefaultPdf( $ficheprescription93_id, $this->Session->read( 'Auth.User.id' ) );

			if( !empty( $pdf ) ) {
				$this->Gedooo->sendPdfContentToClient( $pdf, "fichesprescriptions93_{$ficheprescription93_id}.pdf" );
			}
			else {
				$this->Flash->error( 'Impossible de générer la fiche de prescription.' );
				$this->redirect( $this->referer() );
			}
		}

		/**
		 * Formulaire d'annulation d'un fiche de prescription.
		 *
		 * @param integer $id
		 */
		public function cancel( $id = null ) {
			$this->WebrsaAccesses->check( $id );

			$query = array(
				'conditions' => array(
					'Ficheprescription93.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$ficheprescription93 = $this->Ficheprescription93->find( 'first', $query );

			$personne_id = Hash::get( $ficheprescription93, 'Ficheprescription93.personne_id' );
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			// On transforme les champs date_annulation et motif_annulation en champs obligatoires
			$notEmpty = array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'allowEmpty' => false,
					'required' => true
				)
			);
			foreach( array( 'date_annulation', 'motif_annulation' ) as $field ) {
				$this->Ficheprescription93->validate[$field] = Hash::merge(
					$notEmpty,
					$this->Ficheprescription93->validate[$field]
				);
			}

			// Suppression du contrôle sur les champs non présents
			unset( $this->Ficheprescription93->validate['prestatairehorspdifp93_id'] );

			// Traitement du formulaire
			if( !empty( $this->request->data ) ) {
				$this->Ficheprescription93->begin();

				$ficheprescription93['Ficheprescription93']['statut'] = '99annulee';
				$ficheprescription93['Ficheprescription93']['date_annulation'] = Hash::get( $this->request->data, 'Ficheprescription93.date_annulation' );
				$ficheprescription93['Ficheprescription93']['motif_annulation'] = Hash::get( $this->request->data, 'Ficheprescription93.motif_annulation' );

				unset( $ficheprescription93['Ficheprescription93']['created'], $ficheprescription93['Ficheprescription93']['modified'] );

				$this->Ficheprescription93->create( $ficheprescription93 );

				if( $this->Ficheprescription93->save( null, array( 'atomic' => false ) ) ) {
					$this->Ficheprescription93->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', $personne_id ) );
				}
				else {
					debug( $this->Ficheprescription93->validationErrors );
					$this->Ficheprescription93->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				$this->request->data = $ficheprescription93;
			}
			$this->set( 'urlmenu', '/fichesprescriptions93/index/'.$personne_id );
		}
	}
?>
