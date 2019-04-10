<?php
	/**
	 * Code source de la classe DonneescafController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe DonneescafController ...
	 *
	 * @package app.Controller
	 */
	class DonneescafController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Donneescaf';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'DossiersMenus',
			'Jetons2',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Foyer',
			'Option',
			'Personne',
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
			'foyer' => 'read',
			'personne' => 'read',
		);

		/**
		 * Liste des donnees d'une personne
		 *
		 * @param integer $personne_id
		 */
		public function personne($personne_id) {
			$this->assert(valid_int($personne_id), 'invalidParameter');
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));

			$this->set('personnes', $personnes = $this->Personne->find(
				'all', array(
					'fields' => Hash::merge($this->Personne->fields(),
						array(
							'("Personne"."nati" IS NOT NULL OR '
							.'"Personne"."dtnati" IS NOT NULL OR '
							.'"Personne"."pieecpres" IS NOT NULL) AS "Personne__have_nati"'
						)
					),
					'contain' => array(
						'Dossiercaf',
						'Prestation',
						'Rattachement',
						'Ressource' => array(
							'Ressourcemensuelle' => 'Detailressourcemensuelle'
						),
						'Calculdroitrsa' => array(
							'conditions' => array(
								'OR' => array(
									'Calculdroitrsa.toppersdrodevorsa IS NOT NULL',
									'Calculdroitrsa.toppersentdrodevorsa IS NOT NULL',
									'Calculdroitrsa.mtpersressmenrsa IS NOT NULL',
									'Calculdroitrsa.mtpersabaneursa IS NOT NULL',
								)
							)
						),
						'Activite',
						'Allocationsoutienfamilial' => array(
							'conditions' => array(
								'OR' => array(
									'Allocationsoutienfamilial.sitasf IS NOT NULL',
									'Allocationsoutienfamilial.parassoasf IS NOT NULL',
									'Allocationsoutienfamilial.ddasf IS NOT NULL',
									'Allocationsoutienfamilial.dfasf IS NOT NULL',
								)
							)
						),
						'Creancealimentaire',
						'Grossesse',
						'Infoagricole' => array(
							'conditions' => array(
								'OR' => array(
									'Infoagricole.mtbenagri IS NOT NULL',
									'Infoagricole.regfisagri IS NOT NULL',
									'Infoagricole.dtbenagri IS NOT NULL',
								)
							),
							'Aideagricole'
						),
						'Avispcgpersonne' => array('Derogation', 'Liberalite'),
						// 'Aviscgssdompersonne', // Lien non fait dans Personne - reservé aux DOM
						'Suiviappuiorientation',
						'Dsp' => array(
							'fields' => Hash::merge($this->Personne->Dsp->fields(),
								array(
									'("Dsp"."sitpersdemrsa" IS NOT NULL OR '
									.'"Dsp"."topisogroouenf" IS NOT NULL OR '
									.'"Dsp"."topdrorsarmiant" IS NOT NULL OR '
									.'"Dsp"."drorsarmianta2" IS NOT NULL OR '
									.'"Dsp"."topcouvsoc" IS NOT NULL) AS "Dsp__have_generalite"',
									'("Dsp"."accosocfam" IS NOT NULL OR '
									.'"Dsp"."libcooraccosocfam" IS NOT NULL OR '
									.'"Dsp"."accosocindi" IS NOT NULL OR '
									.'"Dsp"."libcooraccosocindi" IS NOT NULL OR '
									.'"Dsp"."soutdemarsoc" IS NOT NULL) AS "Dsp__have_comsitsoc"',
									'("Dsp"."nivetu" IS NOT NULL OR '
									.'"Dsp"."nivdipmaxobt" IS NOT NULL OR '
									.'"Dsp"."annobtnivdipmax" IS NOT NULL OR '
									.'"Dsp"."topqualipro" IS NOT NULL OR '
									.'"Dsp"."libautrqualipro" IS NOT NULL OR '
									.'"Dsp"."topcompeextrapro" IS NOT NULL OR '
									.'"Dsp"."libcompeextrapro" IS NOT NULL) AS "Dsp__have_nivetu"'
								)
							),
							'Detaildifsoc',
							'Detailaccosocfam',
							'Detailaccosocindi',
							'Detaildifdisp',
							'Detailnatmob',
							'Detaildiflog',
						),
						'Parcours' => array(
							'fields' => Hash::merge($this->Personne->Parcours->fields(),
								array(
									'("Parcours"."natparcocal" IS NOT NULL OR '
									.'"Parcours"."natparcomod" IS NOT NULL OR '
									.'"Parcours"."toprefuparco" IS NOT NULL OR '
									.'"Parcours"."motimodparco" IS NOT NULL) AS "Parcours__have_parcours"',
								)
							)
						),
						'Orientation',
						'Titresejour',
						'Informationeti',
						'Conditionactiviteprealable',
						'Personnelangue'
					),
					'conditions' => array('Personne.id' => $personne_id)
				)
			));

			$this->set('options', $this->_options());

			// Pour liste personnes dans les tabs
			$this->set('personnes_list', $this->_getPersonnes_list($personnes[0]['Personne']['foyer_id']));

			/**
			 * Extraction du contain au 1er niveau
			 */
			$this->_extractAndSet(
				array(
					'Rattachement', 'Ressource', 'Activite', 'Allocationsoutienfamilial',
					'Creancealimentaire', 'Grossesse', 'Infoagricole', 'Avispcgpersonne',
					'Derogation', 'Liberalite', 'Suiviappuiorientation', 'Parcours',
					'Orientation', 'Titresejour', 'Informationeti', 'Conditionactiviteprealable','Personnelangue'
				), $personnes
			);

			/**
			 * Extraction du contain aux autres niveaux
			 */
			$this->_extractAndSet(
				array(
					'Ressourcemensuelle', 'Detailressourcemensuelle', 'Derogation',
					'Liberalite', 'Detaildifsoc', 'Detailaccosocfam', 'Detailaccosocindi',
					'Detaildifdisp', 'Detailnatmob', 'Detaildiflog', 'Aideagricole'
				),
				array(0 => array(
					'Ressourcemensuelle' => Hash::extract($personnes, '0.Ressource.{n}.Ressourcemensuelle.{n}'),
					'Detailressourcemensuelle'
						=> Hash::extract($personnes, '0.Ressource.{n}.Ressourcemensuelle.{n}.Detailressourcemensuelle.{n}'),
					'Derogation' => Hash::extract($personnes, '0.Avispcgpersonne.{n}.Derogation.{n}'),
					'Liberalite' => Hash::extract($personnes, '0.Avispcgpersonne.{n}.Liberalite.{n}'),
					'Detaildifsoc' => Hash::extract($personnes, '0.Dsp.Detaildifsoc.{n}'),
					'Detailaccosocfam' => Hash::extract($personnes, '0.Dsp.Detailaccosocfam.{n}'),
					'Detailaccosocindi' => Hash::extract($personnes, '0.Dsp.Detailaccosocindi.{n}'),
					'Detaildifdisp' => Hash::extract($personnes, '0.Dsp.Detaildifdisp.{n}'),
					'Detailnatmob' => Hash::extract($personnes, '0.Dsp.Detailnatmob.{n}'),
					'Detaildiflog' => Hash::extract($personnes, '0.Dsp.Detaildiflog.{n}'),
					'Aideagricole' => Hash::extract($personnes, '0.Infoagricole.{n}.Aideagricole.{n}'),
				))
			);
		}

		/**
		 * Liste des donnees d'un foyer
		 *
		 * @param integer $foyer_id
		 */
		public function foyer($foyer_id) {
			$this->assert(valid_int($foyer_id), 'invalidParameter');
			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('foyer_id' => $foyer_id)));

			$this->set('foyers', $foyers = $this->Foyer->find(
				'all', array(
					'contain' => array(
						'Dossier' => array(
							'Situationdossierrsa' => array(
								'Suspensiondroit',
								'Suspensionversement',
							),
							'Detaildroitrsa' => array(
								'Detailcalculdroitrsa',
								'fields' => array_merge(
									$this->Foyer->Dossier->Detaildroitrsa->fields(),
									array(
										'("Detaildroitrsa".topsansdomfixe IS NOT NULL OR '
										.'"Detaildroitrsa".nbenfautcha IS NOT NULL OR '
										.'"Detaildroitrsa".oridemrsa IS NOT NULL OR '
										.'"Detaildroitrsa".dtoridemrsa IS NOT NULL OR '
										.'"Detaildroitrsa".topfoydrodevorsa IS NOT NULL) AS "Detaildroitrsa__have_tronccommun"',
										'("Detaildroitrsa"."ddelecal" IS NOT NULL OR '
										.'"Detaildroitrsa"."dfelecal" IS NOT NULL OR '
										.'"Detaildroitrsa"."mtrevminigararsa" IS NOT NULL OR '
										.'"Detaildroitrsa"."mtpentrsa" IS NOT NULL OR '
										.'"Detaildroitrsa"."mtlocalrsa" IS NOT NULL OR '
										.'"Detaildroitrsa"."mtrevgararsa" IS NOT NULL OR '
										.'"Detaildroitrsa"."mtpfrsa" IS NOT NULL OR '
										.'"Detaildroitrsa"."mtalrsa" IS NOT NULL OR '
										.'"Detaildroitrsa"."mtressmenrsa" IS NOT NULL OR '
										.'"Detaildroitrsa"."mtsanoblalimrsa" IS NOT NULL OR '
										.'"Detaildroitrsa"."mtredhosrsa" IS NOT NULL OR '
										.'"Detaildroitrsa"."mtredcgrsa" IS NOT NULL OR '
										.'"Detaildroitrsa"."mtcumintegrsa" IS NOT NULL OR '
										.'"Detaildroitrsa"."mtabaneursa" IS NOT NULL OR '
										.'"Detaildroitrsa"."mttotdrorsa" IS NOT NULL) AS "Detaildroitrsa__have_mntcalculdroit"',
									)
								)
							),
							'Avispcgdroitrsa' => array(
								'Condadmin',
								'Reducrsa',
							),
							'Infofinanciere',
							'Suiviinstruction',
						),
						'Adressefoyer' => array(
							'Adresse' => array(
								'fields' => Hash::merge(
									$this->Foyer->Adressefoyer->Adresse->fields(),
									array(
										'("Adresse"."typeres" IS NOT NULL OR '
										.'"Adresse"."topresetr" IS NOT NULL) AS "Adresse__have_comp"'
									)
								)
							)
						),
						'Evenement',
						'Controleadministratif',
						'Creance',
						'Modecontact',
						'Paiementfoyer',
					),
					'conditions' => array('Foyer.id' => $foyer_id)
				)
			));

			$this->set('options', $this->_options());

			// Pour liste personnes dans les tabs
			$this->set('personnes_list', $this->_getPersonnes_list($foyer_id));

			/**
			 * Extraction du contain au 1er niveau
			 */
			$this->_extractAndSet(
				array(
					'Adressefoyer', 'Evenement', 'Controleadministratif', 'Creance',
					'Modecontact', 'Paiementfoyer'
				), $foyers
			);

			/**
			 * Extraction du contain aux autres niveaux
			 */
			$this->_extractAndSet(
				array(
					'Suspensionversement', 'Suspensiondroit', 'Detailcalculdroitrsa',
					'Condadmin', 'Reducrsa', 'Infofinanciere', 'Suiviinstruction'
				),
				array(0 => array(
					'Suspensiondroit' => Hash::extract($foyers, '0.Dossier.Situationdossierrsa.Suspensiondroit.{n}'),
					'Suspensionversement' => Hash::extract($foyers, '0.Dossier.Situationdossierrsa.Suspensionversement.{n}'),
					'Detailcalculdroitrsa' => Hash::extract($foyers, '0.Dossier.Detaildroitrsa.Detailcalculdroitrsa.{n}'),
					'Condadmin' => Hash::extract($foyers, '0.Dossier.Avispcgdroitrsa.Condadmin.{n}'),
					'Reducrsa' => Hash::extract($foyers, '0.Dossier.Avispcgdroitrsa.Reducrsa.{n}'),
					'Infofinanciere' => Hash::extract($foyers, '0.Dossier.Infofinanciere.{n}'),
					'Suiviinstruction' => Hash::extract($foyers, '0.Dossier.Suiviinstruction.{n}'),
				))
			);
		}

		/**
		 * Envoi une variable à la vue contenant le contain d'un enregistrement
		 *
		 * Exemple:
		 *  $toExtract = array('Prestation')
		 *	$data = array(0 => array('Prestation' => array(0 => array('id' => 1))))
		 *	une variable nommé <strong>$prestations</strong> contiendra : array(0 => array('Prestation' => array('id' => 1)))
		 *
		 * @param array $toExtract - Liste des contain à extraire
		 * @param array $data - Données du find all
		 */
		protected function _extractAndSet($toExtract, $data) {
			foreach ($toExtract as $extractName) {
				$varName = Inflector::pluralize(Inflector::underscore($extractName));
				$$varName = array();
				foreach (Hash::extract($data, '0.'.$extractName) as $extractedData) {
					${$varName}[][$extractName] = $extractedData;
				}
				$this->set($varName, $$varName);
			}
		}

		/**
		 * Permet d'obtenir la liste des Personnes d'un foyer pour affichage des onglets
		 *
		 * @param integer $foyer_id
		 * @return array
		 */
		protected function _getPersonnes_list($foyer_id) {
			$this->Foyer->forceVirtualFields = true;
			return $this->Foyer->find(
				'all', array(
					'fields' => array(
						'Personne.id',
						'Personne.nom_complet',
						'Prestation.rolepers',
					),
					'contain' => false,
					'joins' => array(
						$this->Foyer->join('Personne'),
						$this->Foyer->Personne->join('Prestation'),
					),
					'conditions' => array(
						'Personne.foyer_id' => $foyer_id
					),
					'order' => array(
						'("Prestation"."rolepers" = \'DEM\')' => 'DESC NULLS LAST',
						'("Prestation"."rolepers" = \'CJT\')' => 'DESC NULLS LAST',
						'("Prestation"."rolepers" = \'ENF\')' => 'DESC NULLS LAST',
						'Personne.nom' => 'ASC',
						'Personne.prenom' => 'ASC',
						'Personne.id' => 'ASC',
					)
				)
			);
		}

		/**
		 * Ajoute les options extraites des données CAF
		 *
		 * @return array
		 */
		protected function _options() {
			return Hash::merge(
				$this->Personne->enums(),
				$this->Allocataires->options(),
				$this->Foyer->Dossier->Situationdossierrsa->Suspensionversement->enums(),
				$this->Foyer->Creance->enums(),
				$this->Personne->Suiviappuiorientation->enums(),
				$this->Foyer->Dossier->Suiviinstruction->enums(),
				$this->Personne->Activite->enums(),
				$this->Personne->Dsp->enums(),
				$this->Personne->Dsp->Detaildifsoc->enums(),
				$this->Personne->Dsp->Detailaccosocfam->enums(),
				$this->Personne->Dsp->Detailaccosocindi->enums(),
				$this->Personne->Dsp->Detaildifdisp->enums(),
				$this->Personne->Dsp->Detailnatmob->enums(),
				$this->Personne->Dsp->Detaildiflog->enums(),
				$this->Personne->Rattachement->enums(),
				$this->Personne->Ressource->Ressourcemensuelle->Detailressourcemensuelle->enums(),
				$this->Personne->Activite->enums(),
				$this->Personne->Allocationsoutienfamilial->enums(),
				$this->Personne->Creancealimentaire->enums(),
				$this->Personne->Grossesse->enums(),
				$this->Personne->Infoagricole->enums(),
				$this->Personne->Avispcgpersonne->enums(),
				$this->Personne->Avispcgpersonne->Derogation->enums(),
				$this->Foyer->Dossier->enums(),
				$this->Foyer->enums(),
				$this->Foyer->Adressefoyer->enums(),
				$this->Foyer->Dossier->Situationdossierrsa->enums(),
				$this->Foyer->Dossier->Situationdossierrsa->Suspensiondroit->enums(),
				$this->Foyer->Dossier->Detaildroitrsa->Detailcalculdroitrsa->enums(),
				$this->Foyer->Dossier->Avispcgdroitrsa->enums(),
				$this->Foyer->Dossier->Avispcgdroitrsa->Condadmin->enums(),
				$this->Foyer->Evenement->enums(),
				$this->Foyer->Controleadministratif->enums(),
				$this->Foyer->Dossier->Infofinanciere->enums(),
				$this->Personne->Parcours->enums(),
				$this->Personne->Titresejour->enums(),
				$this->Personne->Informationeti->enums(),
				$this->Foyer->Modecontact->enums(),
				$this->Foyer->Paiementfoyer->enums()
			);
		}
	}
?>
