<?php
	/**
	 * Code source de la classe AccueilsController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

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
			'index',
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
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Identifiant de l'utilisateur.
		 *
		 * @var array
		 */
		public $idReferent = null;

		/**
		 * Libellé du mode de référence.
		 *
		 * @var string
		 */
		public $libelleReference = '';

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

			$libelleReference = $this->libelleReference;

			$this->set(compact('departement', 'articles', 'results', 'blocs', 'libelleReference'));
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
						$this->loadModel('Referent');
						$libelle = $this->Referent->find ('first', array ('recursive' => -1, 'conditions' => array ('id' => $user['accueil_referent_id'])));
						$this->libelleReference = __d('accueils', 'Accueil.bloc.libelle.referent').$libelle['Referent']['nom_complet'];
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
						$this->loadModel('Group');
						$libelle = $this->Group->find ('first', array ('recursive' => -1, 'conditions' => array ('id' => $user['group_id'])));
						$this->libelleReference = __d('accueils', 'Accueil.bloc.libelle.group').$libelle['Group']['name'];
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
							$this->loadModel('Structurereferente');
							$libelle = $this->Structurereferente->find ('first', array ('recursive' => -1, 'conditions' => array ('id' => $referent['Referent']['structurereferente_id'])));
							$this->libelleReference = __d('accueils', 'Accueil.bloc.libelle.structurereferente').$libelle['Structurereferente']['lib_struc'];
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
		 * @param $parametres
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
					'DATE( Contratinsertion.dd_ci ) BETWEEN \''.$du->format ('Y-m-d').'\' AND \''.$au->format ('Y-m-d').'\'',
					'Contratinsertion.referent_id IN ('.$this->idReferent.')',
				),
				'order' => array(
					'Contratinsertion.dd_ci ASC',
				),
			);

			// Récupération d'une requête spécifique au département si besoin
			if (method_exists($this, '_getCersQuery'.$departement)) {
				$query = $this->{'_getCersQuery'.$departement}($du, $au);
			}

			$cers = $this->Contratinsertion->find ('all', $query);
			$cers['du'] = $du->format ('d/m/Y');
			$cers['au'] = $au->format ('d/m/Y');

			return $cers;
		}

		/**
		 * Récupération des CER du bon département
		 *
		 * @param $departement
		 * @param $parametres
		 * @return array
		 */
		protected function _getCersQuery66 ($du, $au) {
			$query = array (
				'conditions' => array(
					'Contratinsertion.df_ci IS NOT NULL',
					'DATE( Contratinsertion.df_ci ) BETWEEN \''.$du->format ('Y-m-d').'\' AND \''.$au->format ('Y-m-d').'\'',
					'Contratinsertion.referent_id IN ('.$this->idReferent.')',
					'Contratinsertion.positioncer IN (\'perime\')',
				),
				'order' => array(
					'Contratinsertion.df_ci ASC',
				),
			);

			return $query;
		}

		/**
		 * Récupération des CER du bon département
		 *
		 * @param $departement
		 * @param $parametres
		 * @return array
		 */
		protected function _getCersQuery58 ($du, $au) {
			$query = array (
				'fields' => array_merge(
					$this->Contratinsertion->fields(),
					array(
						"Personne.nom",
						"Personne.prenom"
					)
				),
				'recursive' => -1,
				'joins' => array(
					$this->Contratinsertion->join('Personne'),
					$this->Contratinsertion->Personne->join('Calculdroitrsa'),
					$this->Contratinsertion->Personne->join('Foyer'),
					$this->Contratinsertion->Personne->Foyer->join('Dossier'),
					$this->Contratinsertion->Personne->Foyer->Dossier->join('Situationdossierrsa')
				),
				'conditions' => array(
					'Contratinsertion.df_ci IS NOT NULL',
					'DATE( Contratinsertion.df_ci ) BETWEEN \''.$du->format ('Y-m-d').'\' AND \''.$au->format ('Y-m-d').'\'',
					'Contratinsertion.referent_id IN ('.$this->idReferent.')',
					'Calculdroitrsa.toppersdrodevorsa' => '1',
					'Situationdossierrsa.etatdosrsa' => '2',
					'"Contratinsertion"."id" in (
						select "id" from "contratsinsertion"
						where "personne_id" = "Contratinsertion"."personne_id"
						order by "contratsinsertion"."rg_ci" desc
						limit 1 )'
				),
				'order' => array(
					'Contratinsertion.df_ci ASC',
				),
			);

			return $query;
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
				$fiches = $this->{'_getFichesPrescription'.$departement}($limite->format ('Y-m-d'));
				$fiches['limite'] = $parametres['limite'];
			}

			return $fiches;
		}

		/**
		 * Récupération des fiches de prescription du bon département
		 *
		 * @param $departement
		 * @param $parametres
		 * @return array
		 */
		protected function _getFichesprescriptionresultataction($departement, $parametres = array ()) {
		    $fiches = array ();
		    if (method_exists($this, '_getFichesPrescriptionresultataction'.$departement)) {
		        $limite = new DateTime ();
		        $limite->sub (new DateInterval('P'. $parametres['limite'] .'M'));
		        $fiches = $this->{'_getFichesPrescriptionresultataction'.$departement}($limite->format ('Y-m-d'));
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
         * Fiches de prescription du 93
         *
         * @param $limite date limite des fiches au format texte
         * @return array
         */
        protected function _getFichesprescriptionresultataction93($limite) {
            $this->loadModel('Ficheprescription93');
            $query = array (
                'conditions' => array(
                    'Ficheprescription93.date_retour IS NOT NULL',
                    'Ficheprescription93.date_retour >= \''.$limite.'\'',
                    'Ficheprescription93.referent_id IN ('.$this->idReferent.')',
                    'Ficheprescription93.benef_retour_presente =\'oui\'',
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
         * Fiches de prescription modifier par F-RSA
         *
         * @return array
         */
        protected function _getFichesprescriptionfrsa() {
            $this->loadModel('Ficheprescription93');
            $query = array (
                'conditions' => array(
                    'Ficheprescription93.frsa_datetransmi::date >= Ficheprescription93.modified::date',
                    'Ficheprescription93.frsa_datetransmi::date IS NOT NULL',
                    'Ficheprescription93.referent_id IN ('.$this->idReferent.')',
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
					'Referent',
				),
				'conditions' => array(
					'DATE( Rendezvous.daterdv ) BETWEEN \''.date ('Y-m-d H:i:s').'\' AND \''.$limite->format ('Y-m-d').'\'',
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
		 * Récupération des derniers CER périmés des allocataires
		 *
		 * @param $departement
		 * @param $parametres
		 * @return array
		 */
		protected function _getDernierscersperimes ($departement, $parametres = array ()) {
			$cers = array ();

			$this->loadModel('Typeorient');
			$typeOrientEmploi = implode(',', $this->Typeorient->listIdTypeOrient('EMPLOI'));

			if(empty($typeOrientEmploi)) {
				$conditionsCte = ' and "CteOrientation"."Typeorient__id" not in ('. $typeOrientEmploi . ') ';
			} else {
				$conditionsCte = '';
			}

			$this->loadModel('Contratinsertion');
			$query = '
				with "CteCer" as (
					select DISTINCT ON ("Contratinsertion"."personne_id") "Contratinsertion".*
					from "public"."contratsinsertion" AS "Contratinsertion"
					order by "Contratinsertion"."personne_id", "Contratinsertion"."df_ci" desc
				),
				"CteOrientation" as (
					select DISTINCT ON ("Orientstruct"."personne_id") "Orientstruct".*, "Typeorient"."lib_type_orient", "Typeorient"."id" AS "Typeorient__id"
					from "public"."orientsstructs" AS "Orientstruct"
						inner join "public"."typesorients" AS "Typeorient" on ("Orientstruct"."typeorient_id" = "Typeorient"."id")
					order by "Orientstruct"."personne_id", "Orientstruct"."date_valid" desc
				)
				select
					DISTINCT ON ("Personne"."id") "Personne"."id" as "Personne__id",
					"Referent"."id" as "Referent__id",
					"Personne"."nom" AS "Personne__nom",
					"Personne"."prenom" AS "Personne__prenom",
					"CteCer"."id" AS "Contratinsertion__id",
					"CteCer"."df_ci" AS "Contratinsertion__df_ci",
					"CteCer"."positioncer" AS "Contratinsertion__positioncer",
					"CteOrientation"."referent_id" AS "Orientstruct__referent_id",
					"CteOrientation"."lib_type_orient" AS "Typeorient__lib_type_orient",
					"Referent"."qual" AS "Referent__qual",
					"Referent"."nom" AS "Referent__nom",
					"Referent"."prenom" AS "Referent__prenom"
				from "public"."personnes" AS "Personne"
					join "CteCer" using ("id")
					join "CteOrientation" using ("id")
					INNER JOIN "public"."foyers" AS "Foyer" ON ("Foyer"."id" = "Personne"."foyer_id")
					INNER JOIN "public"."dossiers" AS "Dossier" ON ("Foyer"."dossier_id" = "Dossier"."id")
					INNER JOIN "public"."situationsdossiersrsa" AS "Situationdossierrsa" ON (
						"Situationdossierrsa"."dossier_id" = "Dossier"."id"
						AND "Situationdossierrsa"."etatdosrsa" = \'2\'
					)
					INNER JOIN "public"."calculsdroitsrsa" AS "Calculdroitrsa" ON (
						"Calculdroitrsa"."personne_id" = "Personne"."id"
						AND "Calculdroitrsa"."toppersdrodevorsa" = \'1\'
					)
					INNER JOIN "public"."structuresreferentes" AS "Structurereferente" ON ("Structurereferente"."id" = "CteCer"."structurereferente_id")
					INNER JOIN "public"."personnes_referents" AS "PersonneReferent" ON "PersonneReferent"."personne_id" = "Personne"."id"
					INNER JOIN "public"."referents" AS "Referent" ON ("PersonneReferent"."referent_id" = "Referent"."id")
					INNER JOIN "public"."prestations" AS "Prestation" ON ("Personne"."id" = "Prestation"."personne_id")
				where "CteCer"."positioncer" IN (\'perime\') ' . $conditionsCte . '
					and "Referent"."id" IN ('.$this->idReferent.')
					and "Prestation"."rolepers" IN (\'DEM\', \'CJT\');
			';

			$cers = $this->Contratinsertion->query ($query);

			return $cers;
		}

		/**
		 * Récupération de nombre de cantons n'ayant pas été nommés
		 * @param string
		 * @param mixed
		 *
		 * @return int
		 */
		public function _getCantons($departement, $value){
			$this->loadModel('Canton');
			$query = array(
				'conditions' => array(
					'OR' => array(
						'canton' => '',
						'canton IS NULL'
					)
				)
			);
			return $this->Canton->find('count', $query);
		}

		/**
		 * Récupération des dossier de recours gracieux
		 * si le User connecter est résponsable d'un dossier
		 * et que ce dossier est a X jours de la date butoir
		 * @param string
		 * @param mixed
		 *
		 * @return array
		 */
		public function _getRecoursgracieux($departement, $value){
			$recoursgracieux = array ();
			$limit = null;

			$limite = new DateTime ();
			$limite->add (new DateInterval('P'. $value['limite'] .'D'));
			$this->loadModel('Recourgracieux');
			$query = array(
				'conditions' => array(
					'OR' => array(
						'DATE (Recourgracieux.dtbutoir) BETWEEN \''
							.date ('Y-m-d H:i:s').'\' AND \''
							.$limite->format ('Y-m-d').'\'',
					)
				)
			);
			$recoursgracieux = $this->Recourgracieux->find ('all', $query);
			foreach ($recoursgracieux as $key => $recourgracieux) {
				$recoursgracieux[$key]['Recourgracieux']['etatDepuis'] =
					__d('recourgracieux', 'ENUM::ETAT::'
						.$recoursgracieux[$key]['Recourgracieux']['etat'])
						.__m('since')
						.date('d/m/Y', strtotime( $recoursgracieux[$key]['Recourgracieux']['modified'] )
					);
			}
			$recoursgracieux['limite'] = $value['limite'];

			return $recoursgracieux;
		}

		public function _getOrientationssansstructoutype(){

			$this->loadModel('Orientstruct');
			$query = array(
				'conditions' => array(
					'OR' => array(
						'Orientstruct.structurereferente_id IS NULL',
						'Orientstruct.typeorient_id IS NULL'
					)
				)
			);

			return $this->Orientstruct->find('all', $query);
		}

		/**
		 * Récupère les personnes sans référents
		 * @return array
		 */
		public function _getBrsasansreferent(){
			$accueil = Configure::read('page.accueil.profil');
			$profil = $this->Session->read( 'Auth.User.Group.code' );

			if (!isset ($accueil[$profil])) {
				$profil = 'by-default';
			}

			$conditionsSDD = $accueil[$profil]['brsasansreferent']['toppersdrodevorsa'];
			if(count($accueil[$profil]['brsasansreferent']['etatdosrsa']) == 1) {
				$conditionEtatdossierRSA = "'{$accueil[$profil]['brsasansreferent']['etatdosrsa'][0]}'";
				$etatdossier = 'Search__Situationdossierrsa__etatdosrsa__0:'.$accueil[$profil]['brsasansreferent']['etatdosrsa'][0];
			} else {
				$conditionEtatdossierRSA = "'" . implode("','", $accueil[$profil]['brsasansreferent']['etatdosrsa']) . "'";
				$index = 0;
				$etatdossier = [];
				foreach ($accueil[$profil]['brsasansreferent']['etatdosrsa'] as $condition){
					$etatdossier[] = 'Search__Situationdossierrsa__etatdosrsa__'.$index.':'.$condition;
					$index++;
				}
				$etatdossier = implode('/', $etatdossier);
			}

			$this->loadModel('Personne');

			$query = array(
				'fields' => array(
					'concat_ws(\' \', "Personne"."qual", "Personne"."nom", "Personne"."prenom") AS "Demandeur"',
					"Dossier.dtdemrsa",
					"Dossier.id",
					"Situationdossierrsa.etatdosrsa"
				),
				'recursive' => -1,
				'joins' => array(
					$this->Personne->join("Dernierdossierallocataire", ['type' => 'inner']),
					$this->Personne->Dernierdossierallocataire->join("Dossier", ['type' => 'inner']),
					$this->Personne->join("Calculdroitrsa", ['type' => 'inner']),
					[
						'table'      => 'situationsdossiersrsa',
						'alias'      => 'Situationdossierrsa',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => ["Situationdossierrsa.dossier_id = Dernierdossierallocataire.dossier_id"]
					],
					$this->Personne->join('PersonneReferent')
				),
				'conditions' => array(
					"PersonneReferent.id IS NULL",
					"Situationdossierrsa.etatdosrsa IN (" . $conditionEtatdossierRSA . ")",
					"Calculdroitrsa.toppersdrodevorsa" => $conditionsSDD
				)
			);
			if(isset($accueil[$profil]['brsasansreferent']['limite']) && !empty($accueil[$profil]['brsasansreferent']['limite'])) {
				$limit = $accueil[$profil]['brsasansreferent']['limite'];
			}

			$results = $this->Personne->find('all', $query);

			$total = count($results);
			if(isset($limit)){
				array_splice($results, $limit);
			}
			$results['arguments'] = 'Search__Dossier__dernier:1/Search__Dossier__dtdemrsa:0/Search__Situationdossierrsa__etatdosrsa_choice:1/'.$etatdossier.'/Search__Detailcalculdroitrsa__natpf_choice:0/Search__Detaildroitrsa__oridemrsa_choice:0/Search__Calculdroitrsa__toppersdrodevorsa:'.$conditionsSDD;
			$results['nombre_total'] = $total;

			return $results;
		}

		/**
		 * Récupère les BRSA avec que des référents clôturés
		 * @return array
		 */
		public function _getBrsaReferentsClotures(){
			$accueil = Configure::read('page.accueil.profil');
			$profil = $this->Session->read( 'Auth.User.Group.code' );

			if (!isset ($accueil[$profil])) {
				$profil = 'by-default';
			}

			$conditionsSDD = $accueil[$profil]['brsareferentsclotures']['toppersdrodevorsa'];
			if(count($accueil[$profil]['brsareferentsclotures']['etatdosrsa']) == 1) {
				$conditionEtatdossierRSA = "'{$accueil[$profil]['brsareferentsclotures']['etatdosrsa'][0]}'";
				$etatdossier = 'Search__Situationdossierrsa__etatdosrsa__0:'.$accueil[$profil]['brsareferentsclotures']['etatdosrsa'][0];
			} else {
				$conditionEtatdossierRSA = "'" . implode("','", $accueil[$profil]['brsareferentsclotures']['etatdosrsa']) . "'";
				$index = 0;
				$etatdossier = [];
				foreach ($accueil[$profil]['brsareferentsclotures']['etatdosrsa'] as $condition){
					$etatdossier[] = 'Search__Situationdossierrsa__etatdosrsa__'.$index.':'.$condition;
					$index++;
				}

				$etatdossier = implode('/', $etatdossier);
			}

			if(isset($accueil[$profil]['brsareferentsclotures']['limite']) && !empty($accueil[$profil]['brsareferentsclotures']['limite'])) {
				$limit =  $accueil[$profil]['brsareferentsclotures']['limite'];
			}

			$sql = "
			select
				d.id,
				d.dtdemrsa,
				concat_ws(' ', p.qual, p.nom, p.prenom) AS Demandeur,
				s.etatdosrsa
			from public.personnes p
				join calculsdroitsrsa c on c.personne_id = p.id
				join derniersdossiersallocataires dd on dd.personne_id = p.id
				join dossiers d on d.id = dd.dossier_id
				join situationsdossiersrsa s on s.dossier_id = d.id
				join public.personnes_referents pr on pr.personne_id = p.id
			where
				c.toppersdrodevorsa = '$conditionsSDD'
				and s.etatdosrsa IN ($conditionEtatdossierRSA)
			group by p.id, d.id, s.etatdosrsa
			having array_position(array_agg(coalesce (pr.dfdesignation, '99991231')), '99991231') is null";

			$results = $this->Personne->query($sql);

			$total = count($results);
			if(isset($limit)){
				array_splice($results, $limit);
			}
			$results['arguments'] = 'Search__Dossier__dernier:1/Search__Dossier__dtdemrsa:0/Search__Situationdossierrsa__etatdosrsa_choice:1/'.$etatdossier.'/Search__Detailcalculdroitrsa__natpf_choice:0/Search__Detaildroitrsa__oridemrsa_choice:0/Search__Calculdroitrsa__toppersdrodevorsa:'.$conditionsSDD;
			$results['nombre_total'] = $total;
			return $results;
		}


		/**
		 * Récupère les BRSA avec que des référents non actifs pour la sectorisation
		 * @return array
		 */
		public function _getBrsaReferentsNonSectorisation(){
			$accueil = Configure::read('page.accueil.profil');
			$profil = $this->Session->read( 'Auth.User.Group.code' );

			if (!isset ($accueil[$profil])) {
				$profil = 'by-default';
			}

			$conditionsSDD = $accueil[$profil]['brsareferentsnonsectorisation']['toppersdrodevorsa'];
			if(count($accueil[$profil]['brsareferentsnonsectorisation']['etatdosrsa']) == 1) {
				$conditionEtatdossierRSA = "'{$accueil[$profil]['brsareferentsnonsectorisation']['etatdosrsa'][0]}'";
				$etatdossier = 'Search__Situationdossierrsa__etatdosrsa__0:'.$accueil[$profil]['brsareferentsnonsectorisation']['etatdosrsa'][0];
			} else {
				$conditionEtatdossierRSA = "'" . implode("','", $accueil[$profil]['brsareferentsnonsectorisation']['etatdosrsa']) . "'";
				$index = 0;
				$etatdossier = [];
				foreach ($accueil[$profil]['brsareferentsnonsectorisation']['etatdosrsa'] as $condition){
					$etatdossier[] = 'Search__Situationdossierrsa__etatdosrsa__'.$index.':'.$condition;
					$index++;
				}

				$etatdossier = implode('/', $etatdossier);
			}

			if(isset($accueil[$profil]['brsareferentsnonsectorisation']['limite']) && !empty($accueil[$profil]['brsareferentsnonsectorisation']['limite'])) {
				$limit =  $accueil[$profil]['brsareferentsnonsectorisation']['limite'];
			}

			$sql = "
			select
			distinct on (p.id)
			d.id,
			d.dtdemrsa,
			concat_ws(' ', p.qual, p.nom, p.prenom) AS Demandeur,
			s.etatdosrsa
			from public.personnes p
			join calculsdroitsrsa c on c.personne_id = p.id
			join derniersdossiersallocataires dd on dd.personne_id = p.id
			join dossiers d on d.id = dd.dossier_id
			join situationsdossiersrsa s on s.dossier_id = d.id
			join public.personnes_referents pr on pr.personne_id = p.id
			where
			c.toppersdrodevorsa = '$conditionsSDD'
			and s.etatdosrsa IN ($conditionEtatdossierRSA)
			and false = all (
				select s2.actif_sectorisation
				from personnes_referents pr2 join structuresreferentes s2 on pr2.structurereferente_id = s2.id
				where pr2.personne_id = p.id
			)";

			$results = $this->Personne->query($sql);

			$total = count($results);
			if(isset($limit)){
				array_splice($results, $limit);
			}

			$results['arguments'] = 'Search__Dossier__dernier:1/Search__Dossier__dtdemrsa:0/Search__Situationdossierrsa__etatdosrsa_choice:1/'.$etatdossier.'/Search__Detailcalculdroitrsa__natpf_choice:0/Search__Detaildroitrsa__oridemrsa_choice:0/Search__Calculdroitrsa__toppersdrodevorsa:'.$conditionsSDD;
			$results['nombre_total'] = $total;

			return $results;
		}

	}
?>