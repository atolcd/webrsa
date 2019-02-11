<?php
	/**
	 * Code source de la classe Primoanalyses.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Primoanalyses ...
	 *
	 * @package app.Controller
	 */
	class PrimoanalysesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Primoanalyses';

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
			'Primoanalyse',
			'Fichedeliaison',
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

		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'affecter' => 'read',
			'avis' => 'read',
			'delete' => 'delete',
			'proposition' => 'read',
			'validation' => 'update',
			'view' => 'read',
		);

		/**
		 * Action d'affectation d'un gestionnaire à une primoanalyse
		 *
		 * @param integer $primoanalyse_id
		 */
		public function affecter($primoanalyse_id) {
			$this->Primoanalyse->validate['user_id'] = array(NOT_BLANK_RULE_NAME => array('rule' => NOT_BLANK_RULE_NAME));
			$this->_edit($primoanalyse_id);
		}

		/**
		 * Action d'affectation d'un gestionnaire à une primoanalyse
		 *
		 * @param integer $primoanalyse_id
		 */
		public function proposition($primoanalyse_id) {
			$this->_edit($primoanalyse_id);
			$this->Primoanalyse->validate['propositionprimo_id'] = array(NOT_BLANK_RULE_NAME => array('rule' => NOT_BLANK_RULE_NAME));
		}

		/**
		 * Formulaire de modification.
		 *
		 * @param integer $primoanalyse_id
		 */
		protected function _edit($primoanalyse_id) {
			$primoanalyse = $this->Primoanalyse->find('first', $this->Primoanalyse->getEditQuery($primoanalyse_id));
			$fichedeliaison_id = Hash::get($primoanalyse, 'Primoanalyse.fichedeliaison_id');
			$foyer_id = $this->Fichedeliaison->foyerId($fichedeliaison_id);

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu(array('foyer_id' => $foyer_id));
			$dossier_id = $dossierMenu['Dossier']['id'];
			$this->set('dossierMenu', $dossierMenu);
			$this->set('urlMenu', '/Primoanalyses/index/#Foyer_id#');
			$this->set('concerne', $this->Fichedeliaison->FichedeliaisonPersonne->optionsConcerne($foyer_id));
			$this->_setOptions();

			$this->Jetons2->get($dossier_id);

			if (!empty($this->request->data)) {
				if (isset($this->request->data['Cancel'])) {
					$this->Jetons2->release($dossier_id);
					$this->redirect(array('controller' => 'fichedeliaisons', 'action' => 'index', $foyer_id));
				}

				$data = $this->request->data;

				$this->Primoanalyse->begin();
				$this->Primoanalyse->create($data['Primoanalyse']);
				$success = $this->Primoanalyse->save( null, array( 'atomic' => false ) );


				if ($success) {
					// On reconstruit les liens entre Logicielprimo et Primoanalyse
					$this->Primoanalyse->LogicielprimoPrimoanalyse->deleteAllUnbound(array('primoanalyse_id' => $primoanalyse_id));
					foreach ((array)Hash::get($data, 'LogicielprimoPrimoanalyse') as $logiciel) {
						if (Hash::get($logiciel, 'logicielprimo_id')) {
							$logiciel['primoanalyse_id'] = $primoanalyse_id;
							$this->Primoanalyse->LogicielprimoPrimoanalyse->create($logiciel);
							$success = $this->Primoanalyse->LogicielprimoPrimoanalyse->save( null, array( 'atomic' => false ) ) && $success;
						}
					}
				}

				if ($success) {
					$this->Fichedeliaison->commit();
					$this->Primoanalyse->updatePositionsById($primoanalyse_id);
					$this->Fichedeliaison->updatePositionsById($fichedeliaison_id); // Passe en traité si primo analyse > traité
					$this->Jetons2->release($dossier_id);
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('controller' => 'fichedeliaisons', 'action' => 'index', $foyer_id));
				}
				else {
					$this->Fichedeliaison->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			// Restitution des valeurs pour LogicielprimoPrimoanalyse
			foreach ((array)Hash::extract($primoanalyse, 'Logicielprimo.{n}') as $value) {
				$primoanalyse['LogicielprimoPrimoanalyse'][$value['id']] = array(
					'logicielprimo_id' => Hash::get($value, 'LogicielprimoPrimoanalyse.logicielprimo_id'),
					'consultation' => Hash::get($value, 'LogicielprimoPrimoanalyse.consultation'),
					'commentaire' => Hash::get($value, 'LogicielprimoPrimoanalyse.commentaire'),
				);
			}

			$this->request->data = $primoanalyse;
			$this->request->data['FichedeliaisonPersonne']['personne_id'] =
				Hash::extract(
					$this->Fichedeliaison->FichedeliaisonPersonne->find('all',
						array('conditions' => array('fichedeliaison_id' => $fichedeliaison_id))
					),
					'{n}.FichedeliaisonPersonne.personne_id'
				)
			;
		}

		/**
		 * Formulaire d'avis technique
		 *
		 * @param integer $primoanalyse_id
		 */
		public function avis($primoanalyse_id) {
			$this->_avis($primoanalyse_id);
		}

		/**
		 * Formulaire de validation
		 *
		 * @param integer $primoanalyse_id
		 */
		public function validation($primoanalyse_id) {
			$this->_avis($primoanalyse_id);
		}

		/**
		 * Fonction générique pour l'avis technique et la validation
		 *
		 * @param integer $primoanalyse_id
		 * @param string $etape
		 */
		protected function _avis($primoanalyse_id) {
			$primoanalyse = $this->Primoanalyse->find('first', $this->Primoanalyse->getEditQuery($primoanalyse_id));
			$primoanalyse += array('Avistechniqueprimo' => array(), 'Validationprimo' => array()); // Utile pour la validation javascript

			$fichedeliaison_id = Hash::get($primoanalyse, 'Primoanalyse.fichedeliaison_id');
			$foyer_id = $this->Fichedeliaison->foyerId($fichedeliaison_id);
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu(array('foyer_id' => $foyer_id));
			$dossier_id = $dossierMenu['Dossier']['id'];
			$this->set('dossierMenu', $dossierMenu);
			$this->set('urlMenu', '/Primoanalyses/index/#Foyer_id#');
			$this->set('concerne', $this->Fichedeliaison->FichedeliaisonPersonne->optionsConcerne($foyer_id));
			$this->_setOptions();
			$saveAlias = $this->action === 'avis' ? 'Avistechniqueprimo' : 'Validationprimo';

			$this->Jetons2->get($dossier_id);
			if (!empty($this->request->data)) {
				if (isset($this->request->data['Cancel'])) {
					$this->Jetons2->release($dossier_id);
					$this->redirect(array('controller' => 'fichedeliaisons', 'action' => 'index', $foyer_id));
				}

				$data = $this->request->data;
				$data[$saveAlias]['user_id'] = $this->Session->read('Auth.User.id');
				$data[$saveAlias]['primoanalyse_id'] = $primoanalyse_id;
				$data[$saveAlias]['etape'] = $this->action;

				$this->Primoanalyse->begin();
				$this->Primoanalyse->Avistechniqueprimo->create($data[$saveAlias]);

				$success = $this->Primoanalyse->Avistechniqueprimo->save( null, array( 'atomic' => false ) );

				$etat = current($this->Primoanalyse->updatePositionsById($primoanalyse_id));
				$this->Fichedeliaison->updatePositionsById($fichedeliaison_id); // Passe en traité si primo analyse > traité

				$primoanalyse = Hash::merge($primoanalyse, $data);

				if ($etat === 'decisionnonvalid') {
					$this->_createNewPrimoanalyse($primoanalyse);

				} elseif ($etat === 'traite'
					&& Hash::get($primoanalyse, 'Primoanalyse.createdossierpcg')
					&& !Hash::get($primoanalyse, 'Primoanalyse.dossierpcg66_id')
				) {
					$this->_createDossierpcg($primoanalyse);
				}

				if ($success) {
					$this->Primoanalyse->commit();
					$this->Jetons2->release($dossier_id);
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('controller' => 'fichedeliaisons', 'action' => 'index', $foyer_id));
				}
				else {
					$this->Fichedeliaison->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}

			$this->request->data = Hash::merge(
				$primoanalyse,
				$this->request->data
			);
		}

		/**
		 * Suppression et redirection vers l'index.
		 *
		 * @param integer $primoanalyse_id
		 */
		public function delete($primoanalyse_id) {
			$primoanalyse = $this->Primoanalyse->find('first', array('conditions' => array('id' => $primoanalyse_id)));
			$fichedeliaison_id = Hash::get($primoanalyse, 'Primoanalyse.fichedeliaison_id');
			$foyer_id = $this->Fichedeliaison->foyerId($fichedeliaison_id);

			$this->Primoanalyse->Avistechniqueprimo->deleteAllUnBound(array('primoanalyse_id' => $primoanalyse_id));
			$this->Primoanalyse->LogicielprimoPrimoanalyse->deleteAllUnBound(array('primoanalyse_id' => $primoanalyse_id));
			$this->Primoanalyse->deleteAllUnBound(array('id' => $primoanalyse_id));

			$this->Flash->success( __( 'Delete->success' ) );
			$this->redirect(array('controller' => 'fichedeliaisons', 'action' => 'index', $foyer_id));
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

			$options['Logicielprimo']['name'] = $this->Primoanalyse->LogicielprimoPrimoanalyse->Logicielprimo->find('list');

			$options = Hash::merge(
				$options,
				$this->Fichedeliaison->enums(),
				$this->Fichedeliaison->Avistechniquefiche->enums(),
				$this->Fichedeliaison->Validationfiche->enums(),
				$this->Primoanalyse->enums(),
				$this->Primoanalyse->Avistechniqueprimo->enums(),
				$this->Primoanalyse->Validationprimo->enums()
			);

			$this->set('options', $options);
			return $options;
		}

		/**
		 * Créer une nouvelle primo analyse
		 *
		 * @param array $primoanalyse
		 */
		protected function _createNewPrimoanalyse($primoanalyse) {
			// On vérifi qu'il n'y ai pas de primo analyse plus récente
			$exists = $this->Primoanalyse->find('first',
				array(
					'fields' => 'Primoanalyse.id',
					'conditions' => array(
						'Primoanalyse.fichedeliaison_id' => Hash::get($primoanalyse, 'Primoanalyse.fichedeliaison_id'),
						'Primoanalyse.id !=' => Hash::get($primoanalyse, 'Primoanalyse.id'),
						'Primoanalyse.created >' => Hash::get($primoanalyse, 'Primoanalyse.created'),
					)
				)
			);

			if (empty($exists)) {
				$data = array(
					'fichedeliaison_id' => Hash::get($primoanalyse, 'Primoanalyse.fichedeliaison_id'),
					'user_id' => Hash::get($primoanalyse, 'Primoanalyse.user_id'),
					'dateaffectation' => Hash::get($primoanalyse, 'Primoanalyse.dateaffectation'),
				);
				$this->Primoanalyse->create($data);
				$this->Primoanalyse->save( null, array( 'atomic' => false ) );
				$this->Primoanalyse->updatePositionsById($this->Primoanalyse->id);
			}
		}

		/**
		 * Créer un dossier pcg
		 *
		 * @param array $primoanalyse
		 */
		protected function _createDossierpcg($primoanalyse) {
			$this->assert(valid_int(Hash::get($primoanalyse, 'Primoanalyse.id')));

			$dossierpcg = array(
				'foyer_id' => Hash::get($primoanalyse, 'Fichedeliaison.foyer_id'),
				'typepdo_id' => Configure::read('Fichedeliaisons.typepdo_id'),
				'datereceptionpdo' => Hash::get($primoanalyse, 'Validationprimo.date'),
				'originepdo_id' => Hash::get($primoanalyse, 'Fichedeliaison.expediteur_id')
			);
			$this->Primoanalyse->Dossierpcg66->create($dossierpcg);
			$this->Primoanalyse->Dossierpcg66->save( null, array( 'atomic' => false ) );

			$this->Primoanalyse->updateAllUnBound(
				array('dossierpcg66_id' => $this->Primoanalyse->Dossierpcg66->id),
				array('id' => Hash::get($primoanalyse, 'Primoanalyse.id'))
			);
		}

		/**
		 * Visualisation
		 *
		 * @param integer $primoanalyse_id
		 */
		public function view($primoanalyse_id) {
			$primoanalyse = $this->Primoanalyse->find('first', $this->Primoanalyse->getEditQuery($primoanalyse_id));
			$fichedeliaison_id = Hash::get($primoanalyse, 'Primoanalyse.fichedeliaison_id');
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

			$this->request->data = Hash::merge($primoanalyse, $this->request->data);

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
		 * Traitement lors des actions tel que "Vu" ou "A faire"
		 *
		 * @param integer $primoanalyse_id
		 */
		protected function _action($primoanalyse_id) {
			$primoanalyse = $this->Primoanalyse->find('first', $this->Primoanalyse->getEditQuery($primoanalyse_id));
			$fichedeliaison_id = Hash::get($primoanalyse, 'Primoanalyse.fichedeliaison_id');
			$foyer_id = $this->Fichedeliaison->foyerId($fichedeliaison_id);
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu(array('foyer_id' => $foyer_id));
			$dossier_id = $dossierMenu['Dossier']['id'];

			$this->Jetons2->get($dossier_id);
			if (!empty($this->request->data)) {
				if (isset($this->request->data['Cancel'])) {
					$this->Jetons2->release($dossier_id);
					$this->redirect(array('controller' => 'fichedeliaisons', 'action' => 'index', $foyer_id));
				}

				$this->Primoanalyse->id = $primoanalyse_id;
				$this->Primoanalyse->begin();
				$success = $this->Primoanalyse->save( $this->request->data, array( 'atomic' => false ) );

				if ($success) {
					$this->Fichedeliaison->commit();
					$this->Primoanalyse->updatePositionsById($primoanalyse_id);
					$this->Fichedeliaison->updatePositionsById($fichedeliaison_id); // Passe en traité si primo analyse > traité
					$this->Jetons2->release($dossier_id);
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect(array('controller' => 'fichedeliaisons', 'action' => 'index', $foyer_id));
				}
				else {
					$this->Fichedeliaison->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			} else {
				$this->request->data = $this->Primoanalyse->find('first', $this->Primoanalyse->getEditQuery($primoanalyse_id));
			}

			$this->set('dossierMenu', $dossierMenu);
			$this->set('urlMenu', '/Fichedeliaisons/index/#Foyer_id#');
			$this->_setOptions();
		}

		/**
		 * Permet de marquer la primoanalyse comme "vu"
		 * Influe sur l'etat de la primoanalyse
		 *
		 * @param integer $primoanalyse_id
		 */
		public function vu($primoanalyse_id) {
			return $this->_action($primoanalyse_id);
		}

		/**
		 * Permet de marquer la primoanalyse comme "A faire"
		 * Influe sur l'etat de la primoanalyse
		 *
		 * @param integer $primoanalyse_id
		 */
		public function afaire($primoanalyse_id) {
			return $this->_action($primoanalyse_id);
		}
	}
?>
