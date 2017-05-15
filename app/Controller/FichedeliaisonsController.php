<?php
	/**
	 * Code source de la classe Fichedeliaisons.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');
	App::uses('CakeEmail', 'Network/Email');
	App::uses('WebrsaEmailConfig', 'Utility');

	/**
	 * La classe Fichedeliaisons ...
	 *
	 * @package app.Controller
	 */
	class FichedeliaisonsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Fichedeliaisons';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'Jetons2',
			'Search.SearchPrg' => array(
				'actions' => array('search')
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Cake1xLegacy.Ajax',
			'Csv',
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Locale',
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Fichedeliaison',
			'Primoanalyse',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajaxfiledelete',
			'ajaxfileupload',
			'download',
			'fileview',
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'create',
			'avis' => 'read',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'filelink' => 'read',
			'fileview' => 'read',
			'index' => 'read',
			'indexparams' => 'read',
			'validation' => 'update',
			'view' => 'read',
		);
		
		/**
		 * Nom de l'array contenant la config pour l'envoi d'e-mails
		 * @see app/Config/email.php
		 * @var String
		 */
		public $configEmail = 'mail_fichedeliaison';
		
		/**
		 * Pagination sur la table.
		 * 
		 * @param integer $foyer_id
		 */
		public function index($foyer_id) {
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('foyer_id' => $foyer_id)));
			$this->set('fichedeliaisons', $this->Fichedeliaison->find('all', $this->Fichedeliaison->getIndexQuery($foyer_id)));
			$this->set('primoanalyses', $this->Primoanalyse->find('all', $this->Primoanalyse->getIndexQuery($foyer_id)));
			$this->set('foyer_id', $foyer_id);
			$this->_setOptions();
		}

		/**
		 * Formulaire d'ajout.
		 * 
		 * @param integer $foyer_id
		 */
		public function add($foyer_id) {
			$this->_edit($foyer_id);
			$this->view = 'edit';
		}
		
		/**
		 * Méthode générique pour add et edit
		 * 
		 * @param integer $foyer_id
		 */
		protected function _edit($foyer_id) {
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu(array('foyer_id' => $foyer_id));
			$dossier_id = $dossierMenu['Dossier']['id'];
			$this->set('dossierMenu', $dossierMenu);
			$this->set('urlMenu', '/Fichedeliaisons/index/#Foyer_id#');
			$this->set('concerne', $this->Fichedeliaison->FichedeliaisonPersonne->optionsConcerne($foyer_id));
			$this->_setOptions();
			
			$this->Jetons2->get($dossier_id);
			
			if (!empty($this->request->data)) {
				if (isset($this->request->data['Cancel'])) {
					$this->Jetons2->release($dossier_id);
					$this->redirect(array('action' => 'index', $foyer_id));
				}
				
				$data = $this->request->data;
				$data['Fichedeliaison']['user_id'] = $this->Session->read('Auth.User.id');
				$data['Fichedeliaison']['foyer_id'] = $foyer_id;
				
				if ($data['Fichedeliaison']['direction'] === 'interne_vers_externe') {
					$data['Fichedeliaison']['expediteur_id'] = $data['Fichedeliaison']['expediteurinterne_id'];
					$data['Fichedeliaison']['destinataire_id'] = $data['Fichedeliaison']['destinataireexterne_id'];
				} else {
					$data['Fichedeliaison']['expediteur_id'] = $data['Fichedeliaison']['expediteurexterne_id'];
					$data['Fichedeliaison']['destinataire_id'] = $data['Fichedeliaison']['destinataireinterne_id'];
				}
				
				$this->Fichedeliaison->begin();
				$this->Fichedeliaison->create($data['Fichedeliaison']);
				$success = $this->Fichedeliaison->save();
				
				$fichedeliaison_id = $this->Fichedeliaison->id;
				
				if ($success) {
					// On reconstruit les liens entre Fichedeliaison et Personne
					$this->Fichedeliaison->FichedeliaisonPersonne->deleteAllUnbound(array('fichedeliaison_id' => $fichedeliaison_id));
					foreach ((array)Hash::get($data, 'FichedeliaisonPersonne.personne_id') as $personne_id) {
						$insert = array(
							'personne_id' => $personne_id,
							'fichedeliaison_id' => $fichedeliaison_id,
						);
						$this->Fichedeliaison->FichedeliaisonPersonne->create($insert);
						$success = $this->Fichedeliaison->FichedeliaisonPersonne->save() && $success;
					}	
				}
				
				$this->Fichedeliaison->Destinataireemail->deleteAllUnbound(array('fichedeliaison_id' => $fichedeliaison_id));
				if ($success && Hash::get($this->request->data, 'Fichedeliaison.envoiemail') 
					&& !empty($this->request->data['Destinataireemail'])
				) {
					foreach ((array)$this->request->data['Destinataireemail']['a'] as $destinataire) {
						preg_match('/[\d]+_(.*)/', $destinataire, $match); // equivalent de suffix() mais compatible avec un email
						
						$this->Fichedeliaison->Destinataireemail->create(
							array(
								'fichedeliaison_id' => $fichedeliaison_id,
								'name' => $match[1],
								'type' => 'A',
							)
						);
						$success = $this->Fichedeliaison->Destinataireemail->save() && $success;
					}
					
					$cc = Hash::get($this->request->data, 'Destinataireemail.cc');
					if (empty($cc)) {
						$cc = array();
					}
					
					foreach ($cc as $destinataire) {
						preg_match('/[\d]+_(.*)/', $destinataire, $match); // equivalent de suffix() mais compatible avec un email
						
						$this->Fichedeliaison->Destinataireemail->create(
							array(
								'fichedeliaison_id' => $fichedeliaison_id,
								'name' => $match[1],
								'type' => 'CC',
							)
						);
						$success = $this->Fichedeliaison->Destinataireemail->save() && $success;
					}
				}
				
				if ($success) {
					$this->Fichedeliaison->commit();
					$this->Fichedeliaison->updatePositionsById($fichedeliaison_id);
					$this->Jetons2->release($dossier_id);
					$this->Session->setFlash('Enregistrement effectué', 'flash/success');
					$this->redirect(array('action' => 'index', $foyer_id));
				}
				else {
					$this->Fichedeliaison->rollback();
					$this->Session->setFlash('Erreur lors de l\'enregistrement', 'flash/error');
				}
			}
		}

		/**
		 * Formulaire de modification.
		 * 
		 * @param integer $fichedeliaison_id
		 */
		public function edit($fichedeliaison_id) {
			$foyer_id = $this->Fichedeliaison->foyerId($fichedeliaison_id);
			$this->_edit($foyer_id);
			$this->request->data = $this->Fichedeliaison->find('first', array('conditions' => array('id' => $fichedeliaison_id)));
			
			/**
			 * Expediteur/destinataire
			 */
			if (hash::get($this->request->data, 'Fichedeliaison.direction') === 'externe_vers_interne') {
				$this->request->data['Fichedeliaison']['expediteurexterne_id'] = 
					$this->request->data['Fichedeliaison']['expediteur_id'];
				$this->request->data['Fichedeliaison']['destinataireinterne_id'] = 
					$this->request->data['Fichedeliaison']['destinataire_id'];
			} else {
				$this->request->data['Fichedeliaison']['expediteurinterne_id'] = 
					$this->request->data['Fichedeliaison']['expediteur_id'];
				$this->request->data['Fichedeliaison']['destinataireexterne_id'] = 
					$this->request->data['Fichedeliaison']['destinataire_id'];
			}
			
			/**
			 * Concerne
			 */
			$this->request->data['FichedeliaisonPersonne']['personne_id'] = 
				Hash::extract(
					$this->Fichedeliaison->FichedeliaisonPersonne->find('all', 
						array('conditions' => array('fichedeliaison_id' => $fichedeliaison_id))
					), 
					'{n}.FichedeliaisonPersonne.personne_id'
				)
			;
			
			/**
			 * Destinataires e-mail
			 */
			$a = Hash::extract(
				$this->Fichedeliaison->Destinataireemail->find('all', 
					array('conditions' => array('fichedeliaison_id' => $fichedeliaison_id, 'type' => 'A'))
				), 
				'{n}.Destinataireemail.name'
			);
			$this->request->data['Destinataireemail']['a'] = array();
			foreach ($a as $email) {
				$destinataire_id = Hash::get($this->request->data, 'Fichedeliaison.destinataire_id');
				$this->request->data['Destinataireemail']['a'][] = $destinataire_id.'_'.h($email);
			}
			
			$cc = Hash::extract(
				$this->Fichedeliaison->Destinataireemail->find('all', 
					array('conditions' => array('fichedeliaison_id' => $fichedeliaison_id, 'type' => 'CC'))
				), 
				'{n}.Destinataireemail.name'
			);
			$this->request->data['Destinataireemail']['cc'] = array();
			foreach ($cc as $email) {
				$destinataire_id = Hash::get($this->request->data, 'Fichedeliaison.destinataire_id');
				$this->request->data['Destinataireemail']['cc'][] = $destinataire_id.'_'.h($email);
			}
		}

		/**
		 * Visualisation
		 * 
		 * @param integer $fichedeliaison_id
		 */
		public function view($fichedeliaison_id) {
			$foyer_id = $this->Fichedeliaison->foyerId($fichedeliaison_id);
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu(array('foyer_id' => $foyer_id));
			
			$this->set('dossierMenu', $dossierMenu);
			$this->set('urlMenu', '/Fichedeliaisons/index/#Foyer_id#');
			$this->set('foyer_id', $foyer_id);
			$this->set('concerne', $this->Fichedeliaison->FichedeliaisonPersonne->optionsConcerne($foyer_id));
			$this->_setOptions();
			
			$this->request->data = $this->Fichedeliaison->Avistechniquefiche->prepareFormDataAvis($fichedeliaison_id);
			$this->request->data['FichedeliaisonPersonne']['personne_id'] = 
				Hash::extract(
					$this->Fichedeliaison->FichedeliaisonPersonne->find('all', 
						array('conditions' => array('fichedeliaison_id' => $fichedeliaison_id))
					), 
					'{n}.FichedeliaisonPersonne.personne_id'
				)
			;
			
			/**
			 * Destinataires e-mail
			 */
			$a = Hash::extract(
				$this->Fichedeliaison->Destinataireemail->find('all', 
					array('conditions' => array('fichedeliaison_id' => $fichedeliaison_id, 'type' => 'A'))
				), 
				'{n}.Destinataireemail.name'
			);
			$this->request->data['Destinataireemail']['a'] = array();
			foreach ($a as $email) {
				$destinataire_id = Hash::get($this->request->data, 'Fichedeliaison.destinataire_id');
				$this->request->data['Destinataireemail']['a'][] = $destinataire_id.'_'.h($email);
			}
			
			$cc = Hash::extract(
				$this->Fichedeliaison->Destinataireemail->find('all', 
					array('conditions' => array('fichedeliaison_id' => $fichedeliaison_id, 'type' => 'CC'))
				), 
				'{n}.Destinataireemail.name'
			);
			$this->request->data['Destinataireemail']['cc'] = array();
			foreach ($cc as $email) {
				$destinataire_id = Hash::get($this->request->data, 'Fichedeliaison.destinataire_id');
				$this->request->data['Destinataireemail']['cc'][] = $destinataire_id.'_'.h($email);
			}
		}
		
		/**
		 * Formulaire d'avis technique
		 * 
		 * @param integer $fichedeliaison_id
		 */
		public function avis($fichedeliaison_id) {
			$this->_avis($fichedeliaison_id);
		}
		
		/**
		 * Formulaire de validation
		 * 
		 * @param integer $fichedeliaison_id
		 */
		public function validation($fichedeliaison_id) {
			$this->_avis($fichedeliaison_id);
		}
		
		/**
		 * Fonction générique pour l'avis technique et la validation
		 * 
		 * @param integer $fichedeliaison_id
		 */
		protected function _avis($fichedeliaison_id) {
			$foyer_id = $this->Fichedeliaison->foyerId($fichedeliaison_id);
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu(array('foyer_id' => $foyer_id));
			$dossier_id = $dossierMenu['Dossier']['id'];
			$this->set('dossierMenu', $dossierMenu);
			$this->set('urlMenu', '/Fichedeliaisons/index/#Foyer_id#');
			$this->set('concerne', $this->Fichedeliaison->FichedeliaisonPersonne->optionsConcerne($foyer_id));
			$this->_setOptions();
			$saveAlias = $this->action === 'avis' ? 'Avistechniquefiche' : 'Validationfiche';
			
			$this->Jetons2->get($dossier_id);
			if (!empty($this->request->data)) {
				if (isset($this->request->data['Cancel'])) {
					$this->Jetons2->release($dossier_id);
					$this->redirect(array('action' => 'index', $foyer_id));
				}
				
				$data = $this->request->data;
				$data[$saveAlias]['user_id'] = $this->Session->read('Auth.User.id');
				$data[$saveAlias]['fichedeliaison_id'] = $fichedeliaison_id;
				$data[$saveAlias]['etape'] = $this->action;
				
				$this->Fichedeliaison->begin();
				$this->Fichedeliaison->Avistechniquefiche->create($data[$saveAlias]);
				
				$success = $this->Fichedeliaison->Avistechniquefiche->save();
				
				if ($success) {
					$this->Fichedeliaison->commit();
					$etat = current($this->Fichedeliaison->updatePositionsById($fichedeliaison_id));
					
					if ($etat === 'decisionvalid' && $data['Fichedeliaison']['envoiemail'] 
						&& empty($data['Fichedeliaison']['dateenvoiemail'])
					) {
						$this->_sendmail($fichedeliaison_id);
					}
					
					if ($etat === 'decisionvalid') {
						$this->_createPrimoanalyse($fichedeliaison_id);
					}
					
					$this->Jetons2->release($dossier_id);
					$this->Session->setFlash('Enregistrement effectué', 'flash/success');
					$this->redirect(array('action' => 'index', $foyer_id));
				}
				else {
					$this->Fichedeliaison->rollback();
					$this->Session->setFlash('Erreur lors de l\'enregistrement', 'flash/error');
				}
			}
			
			$this->request->data = Hash::merge(
				$this->Fichedeliaison->Avistechniquefiche->prepareFormDataAvis($fichedeliaison_id),
				$this->request->data
			);
		}

		/**
		 * Suppression et redirection vers l'index.
		 *
		 * @param integer $fichedeliaison_id
		 */
		public function delete($fichedeliaison_id) {
			$foyer_id = $this->Fichedeliaison->foyerId($fichedeliaison_id);
			
			$this->Fichedeliaison->Avistechniquefiche->deleteAllUnBound(array('fichedeliaison_id' => $fichedeliaison_id));
			$this->Fichedeliaison->FichedeliaisonPersonne->deleteAllUnBound(array('fichedeliaison_id' => $fichedeliaison_id));
			$this->Fichedeliaison->deleteAllUnBound(array('id' => $fichedeliaison_id));
			
			$this->Session->setFlash('Suppression effectué', 'flash/success');
			$this->redirect(array('action' => 'index', $foyer_id));
		}
		
		/**
		 * Envoi d'un fichier temporaire depuis le formualaire.
		 */
		public function ajaxfileupload() {
			$this->Fileuploader->ajaxfileupload();
		}

		/**
		 * Suppression d'un fichier temporaire.
		 */
		public function ajaxfiledelete() {
			$this->Fileuploader->ajaxfiledelete();
		}

		/**
		 * Visualisation d'un fichier temporaire.
		 *
		 * @param integer $id
		 */
		public function fileview($id) {
			$this->Fileuploader->fileview($id);
		}

		/**
		 * Visualisation d'un fichier stocké.
		 *
		 * @param integer $id
		 */
		public function download($id) {
			$this->Fileuploader->download($id);
		}

		/**
		 * Liste des fichiers liés à une orientation.
		 *
		 * @param integer $fichedeliaison_id
		 */
		public function filelink($fichedeliaison_id) {
			$foyer_id = $this->Fichedeliaison->foyerId($fichedeliaison_id);
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('foyer_id' => $foyer_id)));
			
			$this->Fileuploader->filelink($fichedeliaison_id, array('action' => 'index', $foyer_id));
			$this->set('urlmenu', "/fichedeliaisons/index/{$foyer_id}");
			$this->set('options', array());
		}
		
		/**
		 * Options
		 */
		protected function _setOptions() {
			$options = array();
			$actif = array('conditions' => array('actif' => 1));
			
			$options['Fichedeliaison'] = array(
				'motiffichedeliaison_id' => $this->Fichedeliaison->Motiffichedeliaison->find('list'),
				'actif_motiffichedeliaison_id' => $this->Fichedeliaison->Motiffichedeliaison->find('list', $actif),
				'expediteur_id' => $this->Fichedeliaison->Expediteur->find('list'),
			);
			$options['Fichedeliaison']['destinataire_id'] = $options['Fichedeliaison']['expediteur_id'];
			
			$gestionnaires = $this->User->find(
                'all',
                array(
                    'fields' => array(
                        'User.nom_complet',
                        'User.id',
                  ),
                    'conditions' => array(
                        'User.isgestionnaire' => 'O'
                  ),
                    'joins' => array(
                        $this->User->join('Poledossierpcg66', array('type' => 'INNER')),
                  ),
                    'order' => array('User.nom ASC', 'User.prenom ASC'),
                    'contain' => false
              )
			);
			
            $options['Primoanalyse'] = array(
				'user_id' => Hash::combine($gestionnaires, '{n}.User.id', '{n}.User.nom_complet'),
				'propositionprimo_id' => $this->Primoanalyse->Propositionprimo->find('list'),
			);
			
			$emails = $this->Fichedeliaison->Expediteur->find('all',
				array(
					'fields' => array(
						'Expediteur.id',
						'User.email',
						'User.nom',
						'User.prenom',
					),
					'joins' => array(
						$this->Fichedeliaison->Expediteur->join('User')
					),
					'conditions' => array(
						'Expediteur.actif' => 1,
						'User.email IS NOT NULL',
						'User.email !=' => ''
					),
					'order' => array('User.email' => 'ASC')
				)
			);
			$emailsServices = array();
			foreach ($emails as $email) {
				$emailsServices[$email['Expediteur']['id'].'_'.h($email['User']['email'])] 
					= $email['User']['nom'].' '.$email['User']['prenom'].' ('.$email['User']['email'].')';
			}
			
			$servicesInterne = $this->Fichedeliaison->Expediteur->find('list',
				array('conditions' => array('Expediteur.actif' => 1, 'Expediteur.interne' => 1))
			);
			$servicesExterne = $this->Fichedeliaison->Expediteur->find('list',
				array('conditions' => array('Expediteur.actif' => 1, 'Expediteur.interne' => 0))
			);
			$this->set(compact('emailsServices', 'servicesInterne', 'servicesExterne'));
			
			$options = Hash::merge(
				$options,
				$this->Fichedeliaison->enums(),
				$this->Fichedeliaison->Avistechniquefiche->enums(),
				$this->Fichedeliaison->Validationfiche->enums(),
				$this->Primoanalyse->enums()
			);
			
			$this->set('options', $options);
			return $options;
		}
		
		/**
		 * Parametrages liés
		 */
		public function indexparams(){
			
		}
		
		/**
		 * Permet la création de la primoanalyse d'une fiche de liaison
		 * 
		 * @param integer $fichedeliaison_id
		 */
		protected function _createPrimoanalyse($fichedeliaison_id) {
			$havePrimoanalyse = $this->Primoanalyse->find('first', 
				array('conditions' => array('fichedeliaison_id' => $fichedeliaison_id))
			);
			
			if (empty($havePrimoanalyse)) {
				$this->Primoanalyse->create(array('fichedeliaison_id' => $fichedeliaison_id, 'etat' => 'attaffect'));
				return $this->Primoanalyse->save();
			}
		}
		
		/**
		 * Permet d'envoyer un email aux personnes dans Destinataireemail
		 * 
		 * @param integer $fichedeliaison_id
		 */
		protected function _sendmail($fichedeliaison_id) {
			$query = array(
				'fields' => array(
					// NOTE : ordre important pour la traduction
					'Expediteur.name',
					'Destinataire.name',
					'Dossier.matricule',
					'Motiffichedeliaison.name',
				),
				'contain' => false,
				'joins' => array(
					$this->Fichedeliaison->join('Expediteur'),
					$this->Fichedeliaison->join('Destinataire'),
					$this->Fichedeliaison->join('Foyer'),
					$this->Fichedeliaison->Foyer->join('Dossier'),
					$this->Fichedeliaison->join('Motiffichedeliaison'),
				),
				'conditions' => array(
					'Fichedeliaison.id' => $fichedeliaison_id
				)
			);
			
			$result = $this->Fichedeliaison->find('first', $query);
			
			$params = array_merge(
				array('Notification::email'),
				Hash::flatten($result)
			);
			$message = call_user_func_array('__m', $params);
			
			$Email = new CakeEmail($this->configEmail);
			
			$Email->subject(__m('Notification::subject'));
			
			if (WebrsaEmailConfig::isTestEnvironment()){
				$Email->to(WebrsaEmailConfig::getValue($this->configEmail, 'to', $Email->to()));
			} else {
				$Email->to(
					Hash::extract(
						$this->Fichedeliaison->Destinataireemail->find('all', 
							array(
								'fields' => 'name',
								'conditions' => array(
									'Destinataireemail.fichedeliaison_id' => $fichedeliaison_id,
									'Destinataireemail.type' => 'A'
								)
							)
						),
						'{n}.Destinataireemail.name'
					)
				);
				$Email->cc(
					Hash::extract(
						$this->Fichedeliaison->Destinataireemail->find('all', 
							array(
								'fields' => 'name',
								'conditions' => array(
									'Destinataireemail.fichedeliaison_id' => $fichedeliaison_id,
									'Destinataireemail.type' => 'CC'
								)
							)
						),
						'{n}.Destinataireemail.name'
					)
				);
			}
			
			$this->Fichedeliaison->id = $fichedeliaison_id;
			return $Email->send($message) && $this->Fichedeliaison->save(array("dateenvoiemail" => date("Y-m-d")));
		}
	}
?>
