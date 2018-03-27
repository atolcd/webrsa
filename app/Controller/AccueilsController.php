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
		 * Page d'accueil
		 */
		public function index() {
			// Paramètres
			$departement = Configure::read('Cg.departement');
			$accueil = Configure::read('page.accueil.profil');
			$profil = $this->Session->read( 'Auth.User.Group.code' );
			$blocs = $accueil['by-default'];

			if (isset ($accueil[$profil])) {
				$blocs = $accueil[$profil];
			}

			$results = array ();
			$cers = array ();
			$fiches = array ();

			// Articles
			$articles = $this->_getArticles();

			// Blocs
			if (count ($blocs) > 0) {
				foreach ($blocs as $key => $value) {
					if (method_exists($this, '_get'.ucfirst($key))) {
						$results[$key] = $this->{'_get'.ucfirst($key)} ($departement, $value);
					}
				}
			}

			$this->set(compact('departement', 'articles', 'results', 'blocs'));
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

			if (method_exists($this, '_getCers'.$departement)) {
				$aujourdhui = new DateTime ();
				$du = new DateTime ();
				$du->sub (new DateInterval('P'. abs ( ($aujourdhui->format('N') - 1) + (7 * $parametres['du']) ) .'D'));
				$au = new DateTime ();
				$au->add (new DateInterval('P'. abs (7 * $parametres['au'] - $aujourdhui->format('N')) .'D'));
				$cers = $this->{'_getCers'.$departement}($du->format ('d/m/Y'), $au->format ('d/m/Y'));
				$cers['du'] = $du->format ('d/m/Y');
				$cers['au'] = $au->format ('d/m/Y');
			}

			return $cers;
		}

		/**
		 * CER du 93
		 *
		 * @param $du date de début de semaine au format texte
		 * @param $au date de fin de semaine au format texte
		 * @return array
		 */
		protected function _getCers93($du, $au) {
			$this->loadModel('Cer93');
			$query = array (
				'conditions' => array(
					'Cer93.created IS NOT NULL',
					'DATE( Cer93.created ) BETWEEN \''.$du.'\' AND \''.$au.'\'',
					'Cer93.user_id = '.$this->Session->read( 'Auth.User.id' ),
				),
				'order' => array(
					'Cer93.created ASC',
				),
			);

			return $this->Cer93->find (
				'all',
				$query
			);
		}

		/**
		 * CER du 66
		 *
		 * @param $du date de début de semaine au format texte
		 * @param $au date de fin de semaine au format texte
		 * @return array
		 */
		protected function _getCers66($du, $au) {
			$this->loadModel('Contratinsertion');
			$query = array (
				'conditions' => array(
					'Contratinsertion.created IS NOT NULL',
					'DATE( Contratinsertion.created ) BETWEEN \''.$du.'\' AND \''.$au.'\'',
					'Contratinsertion.referent_id = '.$this->Session->read( 'Auth.User.id' ),
				),
				'order' => array(
					'Contratinsertion.created ASC',
				),
			);

			return $this->Contratinsertion->find (
				'all',
				$query
			);
		}

		/**
		 * CER du 58
		 *
		 * @param $du date de début de semaine au format texte
		 * @param $au date de fin de semaine au format texte
		 * @return array
		 */
		protected function _getCers58($du, $au) {
			return $this->_getCers66($du, $au);
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
					'Ficheprescription93.created IS NOT NULL',
					'Ficheprescription93.created >= \''.$limite.'\'',
					'Ficheprescription93.referent_id = '.$this->Session->read( 'Auth.User.id' ),
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
					'Rendezvous.referent_id = '.$this->Session->read( 'Auth.User.id' ),
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