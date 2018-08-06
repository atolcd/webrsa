<?php
	/**
	 * Code source de la classe AccueilsController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
//	App::uses( 'CakeEmail', 'Network/Email' );

	/**
	 * La classe AccueilsController ...
	 *
	 * @package app.Controller
	 */
	class AccueilsController extends AppController
	{
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'alertmail',
			'index',
			'test',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read',
		);

		/**
		 * Identifiant de l'utilisateur.
		 *
		 * @var array
		 */
		public $idReferent = null;

		/**
		 *
		 */
		public function test () {
			$this->loadModel('User');
			$this->loadModel('Referent');

			$query = array (
				'conditions' => array(
					'User.structurereferente_id IS NOT NULL',
				),
				'recursive' => -1
			);
			$users = $this->User->find ('all', $query);
			$referents = $this->Referent->find ('all', array ('recursive' => -1));

			$ok = array ();
			$ko = array ();
			foreach ($users as $user) {
				$name = strtoupper (iconv ('UTF-8', 'ASCII//TRANSLIT//IGNORE', $user['User']['nom']));
				$firstname = strtoupper (iconv ('UTF-8', 'ASCII//TRANSLIT//IGNORE', $user['User']['prenom']));
				$matched = false;

				foreach ($referents as $referent) {
					$nom = strtoupper (iconv ('UTF-8', 'ASCII//TRANSLIT//IGNORE', $referent['Referent']['nom']));
					$prenom = strtoupper (iconv ('UTF-8', 'ASCII//TRANSLIT//IGNORE', $referent['Referent']['prenom']));

					if ($nom === $name && $prenom === $firstname) {
						$ok[] = $user;
						$matched = true;
						break;
					}
				}

				if (!$matched) {
					$ko[] = $user;
				}
			}

			debug ($ok);
			debug ($ko);

			$this->render(false);
		}

		/**
		 * Page d'accueil
		 */
		public function index() {
			// Paramètres
			$departement = Configure::read('Cg.departement');
			$accueil = Configure::read('page.accueil.profil');
			$profil = $this->Session->read( 'Auth.User.Group.code' );
			$blocs = $accueil['by-default'];
			$this->idReferent = $this->_idReferent ();

			if (isset ($accueil[$profil])) {
				$blocs = $accueil[$profil];
			}

			$results = array ();
			$cers = array ();
			$fiches = array ();

			// Articles
			$articles = $this->_getArticles();

			// Blocs
			if ($this->idReferent !== false && count ($blocs) > 0) {
				foreach ($blocs as $key => $value) {
					if (method_exists($this, '_get'.ucfirst($key))) {
						$results[$key] = $this->{'_get'.ucfirst($key)} ($departement, $value);
					}
				}
			}

			$this->set(compact('departement', 'articles', 'results', 'blocs'));
		}

		/**
		 * Récupération de l'identifiant du référent.
		 *
		 * @return array
		 */
		protected function _idReferent() {
			$user = $this->Session->read( 'Auth.User' );

			switch ($user['accueil_reference_affichage']) {
				// Retourne le referent_id s'il est défini.
				case 'REFER':
					if (is_numeric ($user['accueil_referent_id'])) {
						return $user['accueil_referent_id'];
					}
					else {
						return false;
					}
					break;

				// Retourne la lsite des referent_id s'ils sont définis pour tous les utilisateurs du groupe.
				case 'GROUP':
					$this->loadModel('User');
					$query = array (
						'conditions' => array(
							'User.group_id' => $user['group_id'], 
							'User.accueil_referent_id IS NOT NULL',
						)
					);
					$users = $this->User->find ('all', $query);
					$idReferent = '';
					$separateur = '';
					foreach ($users as $item) {
						if (is_numeric($item['User']['accueil_referent_id'])) {
							$idReferent .= $separateur.$item['User']['accueil_referent_id'];
							$separateur = ',';
						}
					}

					if (!empty ($idReferent)) {
						return $idReferent;
					}
					else {
						return false;
					}
					break;

				// Retourne la liste des referent_id de tous les référents de la structure référente du référent de l'utilisateur s'il est défini
				case 'STRUC':
					if (is_numeric ($user['accueil_referent_id'])) {
						$this->loadModel('Referent');
						$query = array (
							'conditions' => array(
								'Referent.id' => $user['accueil_referent_id'],
							),
							'recursive' => -1
						);
						$referent = $this->Referent->find ('first', $query);

						$query = array (
							'conditions' => array(
								'Referent.structurereferente_id' => $referent['Referent']['structurereferente_id'],
							),
							'recursive' => -1
						);
						$referents = $this->Referent->find ('all', $query);

						$idReferent = '';
						$separateur = '';
						foreach ($referents as $item) {
							if (is_numeric($item['Referent']['id'])) {
								$idReferent .= $separateur.$item['Referent']['id'];
								$separateur = ',';
							}
						}

						if (!empty ($idReferent)) {
							return $idReferent;
						}
						else {
							return false;
						}
					}
					else {
						return false;
					}
					break;

				// Rien sinon
				default:
					return false;
					break;
			}
		}

		/**
		 * Récupération des articles
		 *
		 * @return array
		 */
		protected function _getArticles() {
			$this->loadModel('Accueilarticle');

			$query = array (
				'conditions' => array(
					'actif = 1',
					'publicationto <= NOW()',
					'publicationfrom >= NOW()',
				),
				'order' => array(
					'publicationto DESC',
				),
			);

			$items = $this->Accueilarticle->find (
				'all',
				$query
			);

			return $items;
		}

		/**
		 * Récupération des CER du bon département
		 *
		 * @param $departement
		 * $param $parametres
		 * @return array
		 */
		protected function _getCers ($departement, $parametres = array ()) {
			$cers = array ();

			$aujourdhui = new DateTime ();
			$du = new DateTime ();
			$du->sub (new DateInterval('P'. abs ( ($aujourdhui->format('N') - 1) + (7 * $parametres['du']) ) .'D'));
			$au = new DateTime ();
			$au->add (new DateInterval('P'. abs (7 * $parametres['au'] - $aujourdhui->format('N')) .'D'));

			$this->loadModel('Contratinsertion');
			$query = array (
				'conditions' => array(
					'Contratinsertion.dd_ci IS NOT NULL',
					'DATE( Contratinsertion.dd_ci ) BETWEEN \''.$du->format ('d/m/Y').'\' AND \''.$au->format ('d/m/Y').'\'',
					'Contratinsertion.referent_id IN ('.$this->idReferent.')',
				),
				'order' => array(
					'Contratinsertion.dd_ci ASC',
				),
			);

			$cers = $this->Contratinsertion->find ('all', $query);
			$cers['du'] = $du->format ('d/m/Y');
			$cers['au'] = $au->format ('d/m/Y');

			return $cers;
		}

		/**
		 * Récupération des fiches de prescription du bon département
		 *
		 * @param $departement
		 * @param $parametres
		 * @return array
		 */
		protected function _getFichesprescription($departement, $parametres = array ()) {
			$fiches = array ();

			if (method_exists($this, '_getFichesPrescription'.$departement)) {
				$limite = new DateTime ();
				$limite->sub (new DateInterval('P'. $parametres['limite'] .'M'));
				$fiches = $this->{'_getFichesPrescription'.$departement}($limite->format ('d/m/Y'));
				$fiches['limite'] = $parametres['limite'];
			}

			return $fiches;
		}

		/**
		 * Fiches de prescription du 93
		 *
		 * @param $limite date limite des fiches au format texte
		 * @return array
		 */
		protected function _getFichesPrescription93($limite) {
			$this->loadModel('Ficheprescription93');
			$query = array (
				'conditions' => array(
					'Ficheprescription93.date_signature IS NOT NULL',
					'Ficheprescription93.date_signature >= \''.$limite.'\'',
					'Ficheprescription93.referent_id IN ('.$this->idReferent.')',
					'Ficheprescription93.statut IN (\'03transmise_partenaire\', \'04effectivite_renseignee\')',
				),
				'contain' => array(
					'Personne',
				),
				'order' => array(
					'Ficheprescription93.created ASC',
				),
			);

			return $this->Ficheprescription93->find (
				'all',
				$query
			);
		}

		/**
		 * Récupération des rendez-vous du bon département
		 *
		 * @param $departement
		 * @param $parametres
		 * @return array
		 */
		protected function _getRendezvous ($departement, $parametres = array ()) {
			$fiches = array ();
			$limit = null;

			$limite = new DateTime ();
			$limite->add (new DateInterval('P'. $parametres['limite'] .'D'));

			$this->loadModel('Rendezvous');
			$query = array (
				'contain' => array(
					'Personne',
					'Typerdv',
				),
				'conditions' => array(
					'DATE( Rendezvous.daterdv ) BETWEEN \''.date ('Y-m-d H:i:s').'\' AND \''.$limite->format ('d/m/Y').'\'',
					'Rendezvous.referent_id IN ('.$this->idReferent.')',
				),
				'order' => array(
					'Rendezvous.daterdv ASC',
					'Rendezvous.heurerdv ASC',
				),
			);

			$fiches = $this->Rendezvous->find ('all', $query);
			$fiches['limite'] = $parametres['limite'];;

			return $fiches;
		}

		/**
		 * Alerte par mail les membres des CD concernés
		 *
		 * TODO
		 */
		public function alertmail() {
			// Paramètres
			$departement = Configure::read('Cg.departement');
			$users = array ();

			// CER
			$cers = $this->_getCers($departement);
			foreach ($cers as $i => $cer) {
				if (is_numeric($i)) {
					if (!isset ($users[$cer['Contratinsertion']['referent_id']])) {
						$users[$cer['Contratinsertion']['referent_id']]['CER'] = array ();
					}

					$users[$cer['Contratinsertion']['referent_id']]['CER'][] = $cer;
				}
				break;
			}

			// Mail
			$this->loadModel('User');
			foreach ($users as $user_id => $informations) {
				if (is_numeric($user_id)) {
					$user = $this->User->findById ($user_id);
					$user_email = $user['User']['email'];
					$user_email = 'pla@atolcd.com';

					$sautDeLigne = '<br />';
					$mailBody = '';
					$mailBody .= 'Connectez vous à Webrsa pour gérer les actions suivantes :'.$sautDeLigne;
					$mailBody .= '<a href="'.Configure::read('FULL_BASE_URL').'/accueils/index/">Webrsa</a>';

					$mailBody .= '<h2>CER</h2>';
					foreach ($informations['CER'] as $item) {
						$date = new DateTime ($item['Contratinsertion']['created']);

						$mailBody .= '<b>'.$item['Personne']['nom'].' '.$item['Personne']['prenom'].'</b>'.$sautDeLigne;
						$mailBody .= $date->format('d/m/Y').$sautDeLigne;
						$mailBody .= $sautDeLigne;
					}

					//mail($user_email, '[Webrsa] Alerte', $mailBody, "Content-type: text/html; charset=utf-8");

					/*
					$mail = new CakeEmail('test');
				    $mail->to($user_email);
				    $mail->from(array('p.lavigne@atolcd.com' => 'Webrsa'));
				    $mail->subject('Alerte Webrsa');
				    $mail->emailFormat('html');
				    $mail->send($mailBody);
					 */
				}
			}

			$this->render(false);
		}
	}
?>