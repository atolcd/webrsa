<?php

/**
 * Code source de la classe ContratsinsertionController.
 *
 * PHP 5.3
 *
 * @package app.Controller
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * La classe ContratsinsertionController permet la gestion des contrats d'insertion au niveau du dossier
 * de l'allocataire.
 *
 * @package app.Controller
 * @deprecated since version 3.1
 */
class ContratsinsertionOldController extends AppController
{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'ContratsinsertionOld';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes',
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'InsertionsBeneficiaires',
			'Jetons2',
			'RequestHandler',
			'Search.SearchPrg' => array(
				'actions' => array(
					'search',
					'cohorte_nouveaux' => array('filter' => 'Search'),
					'cohorte_valides' => array('filter' => 'Search'),
					'cohorte_cersimpleavalider' => array('filter' => 'Search'),
					'cohorte_cerparticulieravalider' => array('filter' => 'Search'),
					'search_valides' => array('filter' => 'Search'),
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
			'Cake1xLegacy.Ajax',
			'Default2',
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Fileuploader',
			'Widget',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Contratinsertion',
			'Option',
			'WebrsaContratinsertion',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Contratsinsertion:edit',
			'cohorte_cerparticulieravalider' => 'Cohortesci:nouveauxparticulier',
			'cohorte_cersimpleavalider' => 'Cohortesci:nouveauxsimple',
			'cohorte_nouveaux' => 'Cohortesci:nouveaux',
			'cohorte_valides' => 'Cohortesci:valides',
			'exportcsv' => 'Criteresci:exportcsv',
			'exportcsv_valides' => 'Cohortesci:valides',
			'search' => 'Criteresci:index',
			'search_valides' => 'Cohortesci:valides',
			'view' => 'Contratsinsertion:index',
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajax',
			'ajaxaction',
			'ajaxfiledelete',
			'ajaxfileupload',
			'ajaxraisonci',
			'ajaxref',
			'ajaxstruct',
			'download',
			'fileview',
			'notificationsop',
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajaxaction' => 'read',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'ajaxref' => 'update',
			'ajaxstruct' => 'update',
			'cancel' => 'update',
			'cohorte_cerparticulieravalider' => 'update',
			'cohorte_cersimpleavalider' => 'update',
			'cohorte_nouveaux' => 'update',
			'cohorte_valides' => 'read',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'exportcsv' => 'read',
			'exportcsv_cerparticulieravalider' => 'update',
			'exportcsv_cersimpleavalider' => 'update',
			'exportcsv_search_valides' => 'update',
			'exportcsv_valides' => 'read',
			'ficheliaisoncer' => 'read',
			'filelink' => 'read',
			'fileview' => 'read',
			'impression' => 'read',
			'index' => 'read',
			'notifbenef' => 'read',
			'notification' => 'update',
			'notificationsop' => 'read',
			'reconduction_cer_plus_55_ans' => 'read',
			'search' => 'read',
			'search_valides' => 'read',
			'valider' => 'update',
			'validerparticulier' => 'update',
			'validersimple' => 'update',
			'view' => 'read',
		);

    /**
     * Envoi des options communes à la vue (CG 58, 66, 93).
     *
     * @return void
     */
    protected function _setOptions() {
        $options = $this->Contratinsertion->enums();

		$this->set('duree_engag', $this->Option->duree_engag());

        if (in_array($this->action, array('index', 'add', 'edit', 'view', 'valider', 'validersimple', 'validerparticulier'))) {
            $this->set( 'duree_engag', $this->Option->duree_engag() );
            $options = array_merge($options, $this->Contratinsertion->Propodecisioncer66->enums());
            $this->set('decision_ci', ClassRegistry::init('Contratinsertion')->enum('decision_ci'));
            $forme_ci = array();
            if (Configure::read('nom_form_ci_cg') == 'cg93') {
                $forme_ci = array('S' => 'Simple', 'C' => 'Complexe');
            } else if (Configure::read('nom_form_ci_cg') == 'cg66') {
                $forme_ci = array('S' => 'Simple', 'C' => 'Particulier');
            }
            $this->set('forme_ci', $forme_ci);
        }

        if (in_array($this->action, array('add', 'edit', 'view', 'valider'))) {
            $this->set('formeci', ClassRegistry::init('Cer93')->enum('formeci'));
        }

        if (in_array($this->action, array('add', 'edit', 'view', 'valider', 'validersimple', 'validerparticulier'))) {
            $this->set('qual', $this->Option->qual());
            $this->set('raison_ci', ClassRegistry::init('Contratinsertion')->enum('raison_ci'));
            if (Configure::read('Cg.departement') == 66) {
                $this->set('avisraison_ci', ClassRegistry::init('Contratinsertion')->enum('avisraison_ci'));
            } else if (Configure::read('Cg.departement') == 93) {
                $this->set('avisraison_ci', array('D' => 'Defaut de conclusion', 'N' => 'Non respect du contrat'));
            }
            $this->set('aviseqpluri', ClassRegistry::init('Contratinsertion')->enum('aviseqpluri'));
            $this->set('sect_acti_emp', ClassRegistry::init('Contratinsertion')->enum('sect_acti_emp'));
            $this->set('emp_occupe', ClassRegistry::init('Contratinsertion')->enum('emp_occupe'));
            $this->set('duree_hebdo_emp', ClassRegistry::init('Contratinsertion')->enum('duree_hebdo_emp'));
            $this->set('nat_cont_trav', ClassRegistry::init('Contratinsertion')->enum('nat_cont_trav'));
            $this->set('duree_cdd', ClassRegistry::init('Contratinsertion')->enum('duree_cdd'));
            $this->set( 'duree_engag', $this->Option->duree_engag() );

            $this->set('nivetus', $this->Contratinsertion->Personne->Dsp->enum('nivetu'));
            $this->set('nivdipmaxobt', $this->Contratinsertion->Personne->Dsp->enum('nivdipmaxobt'));
            $this->set('typeserins', $this->Option->typeserins());

            $this->set('lib_action', ClassRegistry::init('Actioninsertion')->enum('lib_action'));
            $this->set('typo_aide', ClassRegistry::init('Aidedirecte')->enum('typo_aide'));
            $this->set('soclmaj', ClassRegistry::init('Infofinanciere')->enum('natpfcre', array('type' => 'soclmaj')));
            $this->set('rolepers', ClassRegistry::init('Prestation')->enum('rolepers'));
            $this->set('sitfam', $this->Option->sitfam());
            $this->set('typeocclog', ClassRegistry::init('Foyer')->enum('typeocclog'));
            $this->set('emp_trouv', array('N' => 'Non', 'O' => 'Oui'));
            $this->set('zoneprivilegie', ClassRegistry::init('Zonegeographique')->find('list'));
            $this->set('actions', $this->Contratinsertion->Action->grouplist('prest'));
            $this->set('fiches', (array) Hash::get($this->Contratinsertion->Personne->ActioncandidatPersonne->Actioncandidat->enums(), 'Actioncandidat'));

            $options = array_merge(
                    $options, (array) Hash::get($this->Contratinsertion->Autreavissuspension->enums(), 'Autreavissuspension'), (array) Hash::get($this->Contratinsertion->Autreavisradiation->enums(), 'Autreavisradiation'), $options['Contratinsertion']
            );
        }

		if ( Configure::read( 'Cg.departement' ) == 66 ) {
			$Entretien = ClassRegistry::init( 'Entretien' );

			$options = array_merge($options, $Entretien->options());
			$options['Entretien']['structurereferente_id'] = $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) );
		}

        $this->set('options', $options);
    }

    /**
     * Ajax pour les partenaires fournissant l'action liée au CER (CG 66).
     *
     * @param type $actioncandidat_id
     */
    public function ajaxaction($actioncandidat_id = null) {
        Configure::write('debug', 2);

        $dataActioncandidat_id = Set::extract($this->request->data, 'Contratinsertion.actioncandidat_id');
        $actioncandidat_id = ( empty($actioncandidat_id) && !empty($dataActioncandidat_id) ? $dataActioncandidat_id : $actioncandidat_id );

        if (!empty($actioncandidat_id)) {
            $actioncandidat = $this->Contratinsertion->Actioncandidat->find(
                    'first', array(
                'conditions' => array(
                    'Actioncandidat.id' => $actioncandidat_id
                ),
                'contain' => array(
                    'Contactpartenaire' => array(
                        'Partenaire'
                    ),
                    'Fichiermodule',
                    'Referent'
                )
                    )
            );
            $this->set(compact('actioncandidat'));
        }
        $this->render('ajaxaction', 'ajax');
    }

    /**
     *
     * @param type $typeorient_id
     * @return type
     */
//		protected function _libelleTypeorientNiv0( $typeorient_id ) {
//			$typeorient_niv1_id = $this->Contratinsertion->Personne->Orientstruct->Typeorient->getIdLevel0( $typeorient_id );
//
//			$typeOrientation = $this->Contratinsertion->Personne->Orientstruct->Typeorient->find(
//				'first',
//				array(
//					'fields' => array( 'Typeorient.lib_type_orient' ),
//					'recursive' => -1,
//					'conditions' => array(
//						'Typeorient.id' => $typeorient_niv1_id
//					)
//				)
//			);
//
//			return Set::classicExtract( $typeOrientation, 'Typeorient.lib_type_orient' );
//		}

    /**
     *
     * @param type $structurereferente_id
     * @return type
     */
//		protected function _referentStruct( $structurereferente_id ) {
//			$referents = $this->Contratinsertion->Structurereferente->Referent->find(
//				'all',
//				array(
//					'recursive' => -1,
//					'fields' => array( 'Referent.id', 'Referent.qual', 'Referent.nom', 'Referent.prenom', 'Referent.fonction' ),
//					'conditions' => array( 'structurereferente_id' => $structurereferente_id )
//				)
//			);
//
//			if( !empty( $referents ) ) {
//				$ids = Set::extract( $referents, '/Referent/id' );
//				$values = Set::format( $referents, '{0} {1}', array( '{n}.Referent.nom', '{n}.Referent.prenom' ) );
//				$referents = array_combine( $ids, $values );
//			}
//
//			return $referents;
//		}

    /**
     * Ajax pour les coordonnées du référent (CG 58, 66, 93).
     *
     * @param integer $referent_id
     */
    public function ajaxref($referent_id = null) {
        Configure::write('debug', 0);

        if (!empty($referent_id)) {
            $referent_id = suffix($referent_id);
        } else {
            $referent_id = suffix(Set::extract($this->request->data, 'Contratinsertion.referent_id'));
        }

        $referent = array();
        if (!empty($referent_id)) {
            $qd_referent = array(
                'conditions' => array(
                    'Referent.id' => $referent_id
                ),
                'fields' => null,
                'order' => null,
                'recursive' => -1
            );
            $referent = $this->Contratinsertion->Structurereferente->Referent->find('first', $qd_referent);
        }

        $this->set('referent', $referent);
        $this->render('ajaxref', 'ajax');
    }

    /**
     * Ajax pour les coordonnées de la structure référente liée (CG 58, 66, 93).
     *
     * @param type $structurereferente_id
     */
    public function ajaxstruct($structurereferente_id = null) {
        Configure::write('debug', 0);
        $this->set('typesorients', $this->Contratinsertion->Personne->Orientstruct->Typeorient->find('list', array('fields' => array('lib_type_orient'))));

        $dataStructurereferente_id = Set::extract($this->request->data, 'Contratinsertion.structurereferente_id');
        $structurereferente_id = ( empty($structurereferente_id) && !empty($dataStructurereferente_id) ? $dataStructurereferente_id : $structurereferente_id );

        $qd_struct = array(
            'conditions' => array(
                'Structurereferente.id' => $structurereferente_id
            ),
            'fields' => null,
            'order' => null,
            'recursive' => -1
        );
        $struct = $this->Contratinsertion->Structurereferente->find('first', $qd_struct);


        $this->set('struct', $struct);
        $this->render('ajaxstruct', 'ajax');
    }

    /**
     * http://valums.com/ajax-upload/
     * http://doc.ubuntu-fr.org/modules_php
     * increase post_max_size and upload_max_filesize to 10M
     * debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
     *
     * (CG 58, 66, 93)
     */
    public function ajaxfileupload() {
        $this->Fileuploader->ajaxfileupload();
    }

    /**
     * http://valums.com/ajax-upload/
     * http://doc.ubuntu-fr.org/modules_php
     * increase post_max_size and upload_max_filesize to 10M
     * debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
     * FIXME: traiter les valeurs de retour
     *
     * (CG 58, 66, 93)
     */
    public function ajaxfiledelete() {
        $this->Fileuploader->ajaxfiledelete();
    }

    /**
     * Fonction permettant de visualiser les fichiers chargés dans la vue
     * avant leur envoi sur le serveur (CG 58, 66, 93).
     */
    public function fileview($id) {
        $this->Fileuploader->fileview($id);
    }

    /**
     * Téléchargement des fichiers préalablement associés à un traitement
     * donné (CG 58, 66, 93).
     */
    public function download($fichiermodule_id) {
        $this->assert(!empty($fichiermodule_id), 'error404');
        $this->Fileuploader->download($fichiermodule_id);
    }

    /**
     * Fonction permettant d'accéder à la page pour lier les fichiers au CER
     * (CG 58, 66, 93).
     *
     * @param type $id
     */
    public function filelink($id) {
        $this->assert(valid_int($id), 'invalidParameter');

        $fichiers = array();
        $contratinsertion = $this->Contratinsertion->find(
                'first', array(
            'conditions' => array(
                'Contratinsertion.id' => $id
            ),
            'contain' => array(
                'Fichiermodule' => array(
                    'fields' => array('name', 'id', 'created', 'modified')
                )
            )
                )
        );

        $personne_id = $contratinsertion['Contratinsertion']['personne_id'];
        $this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));

        $dossier_id = $this->Contratinsertion->Personne->dossierId($personne_id);
        $this->assert(!empty($dossier_id), 'invalidParameter');

        $this->Jetons2->get($dossier_id);

        // Retour à l'index en cas d'annulation
        if (isset($this->request->data['Cancel'])) {
            $this->Jetons2->release($dossier_id);
            if (Configure::read('Cg.departement') == 93) {
                $this->redirect(array('controller' => 'cers93', 'action' => 'index', $personne_id));
            } else {
                $this->redirect(array('action' => 'index', $personne_id));
            }
        }

        if (!empty($this->request->data)) {
            $this->Contratinsertion->begin();

            $saved = $this->Contratinsertion->updateAllUnBound(
                    array('Contratinsertion.haspiecejointe' => '\'' . $this->request->data['Contratinsertion']['haspiecejointe'] . '\''), array(
                '"Contratinsertion"."personne_id"' => $personne_id,
                '"Contratinsertion"."id"' => $id
                    )
            );

            if ($saved) {
                // Sauvegarde des fichiers liés à une PDO
                $dir = $this->Fileuploader->dirFichiersModule($this->action, $this->request->params['pass'][0]);
                $saved = $this->Fileuploader->saveFichiers($dir, !Set::classicExtract($this->request->data, "Contratinsertion.haspiecejointe"), $id) && $saved;
            }

            if ($saved) {
                $this->Contratinsertion->commit();
                $this->Jetons2->release($dossier_id);
                $this->Session->setFlash('Enregistrement effectué', 'flash/success');
                $this->redirect($this->referer());
            } else {
                $fichiers = $this->Fileuploader->fichiers($id);
                $this->Contratinsertion->rollback();
                $this->Session->setFlash('Erreur lors de l\'enregistrement', 'flash/error');
            }
        }

        $this->_setOptions();
        $this->set(compact('dossier_id', 'personne_id', 'fichiers', 'contratinsertion'));
        $this->set('urlmenu', '/contratsinsertion/index/' . $personne_id);
    }

    /**
     * (CG 58, 93)
     *
     * @param type $modele
     * @param type $personne_id
     * @return type
     */
    protected function _qdThematiqueEp($modele, $personne_id) {
        return array(
            'fields' => array(
                'Dossierep.id',
                'Dossierep.personne_id',
                'Dossierep.themeep',
                'Dossierep.created',
                'Dossierep.modified',
                'Passagecommissionep.etatdossierep',
                'Contratinsertion.dd_ci',
                'Contratinsertion.df_ci',
            ),
            'conditions' => array(
                'Dossierep.actif' => '1',
                'Dossierep.personne_id' => $personne_id,
                'Dossierep.themeep' => Inflector::tableize($modele),
                'Dossierep.id NOT IN ( ' . $this->Contratinsertion->{$modele}->Dossierep->Passagecommissionep->sq(
                        array(
                            'alias' => 'passagescommissionseps',
                            'fields' => array(
                                'passagescommissionseps.dossierep_id'
                            ),
                            'conditions' => array(
                                'passagescommissionseps.etatdossierep' => array('traite', 'annule')
                            )
                        )
                ) . ' )'
            ),
            'joins' => array(
                array(
                    'table' => Inflector::tableize($modele),
                    'alias' => $modele,
                    'type' => 'INNER',
                    'foreignKey' => false,
                    'conditions' => array("Dossierep.id = {$modele}.dossierep_id")
                ),
                array(
                    'table' => 'contratsinsertion',
                    'alias' => 'Contratinsertion',
                    'type' => 'INNER',
                    'foreignKey' => false,
                    'conditions' => array("Contratinsertion.id = {$modele}.contratinsertion_id")
                ),
                array(
                    'table' => 'passagescommissionseps',
                    'alias' => 'Passagecommissionep',
                    'type' => 'LEFT OUTER',
                    'foreignKey' => false,
                    'conditions' => array('Dossierep.id = Passagecommissionep.dossierep_id')
                ),
            ),
        );
    }

    /**
     * Liste des CER pour un allocataire donné (CG 58, 66, 93).
     *
     * @param integer $personne_id L'id technique de la personne.
     */
    public function index($personne_id = null) {
        // On s'assure que la personne existe
        $nbrPersonnes = $this->Contratinsertion->Personne->find(
                'count', array(
            'conditions' => array(
                'Personne.id' => $personne_id
            ),
            'recursive' => -1
                )
        );
        $this->assert(( $nbrPersonnes == 1), 'invalidParameter');

        $this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));

		$this->_setEntriesAncienDossier( $personne_id, 'Contratinsertion' );

        // Pour les CGs 58  et 66, on veut avoir assez bien d'informations en plus
        if (Configure::read('Cg.departement') != 93) {
            // Recherche de la dernière orientation en cours pour l'allocataire
            // Si aucun alors message d'erreur signalant la présence d'une orientation en emploi (CG 58 et 66)
            $orientstruct = $this->Contratinsertion->Personne->Orientstruct->find(
                    'count', array(
                'conditions' => array(
                    'Orientstruct.personne_id' => $personne_id,
                    'Orientstruct.statut_orient' => 'Orienté',
                ),
                'recursive' => -1
                    )
            );
            $this->set(compact('orientstruct'));

            $conditionsTypeorient = array();
            $blockCumulCER66 = false;
            if (Configure::read('Cg.departement') == 66) {
                $this->Contratinsertion->Personne->id = $personne_id;
                $agePersonne = $this->Contratinsertion->Personne->field('age');
                // Blocage du bouton ajouter et affichage d'un message si le cumul des CERs
                // dépasse 24 mois et que l'allocataire a moins de 55ans
                if ($agePersonne < Configure::read('Tacitereconduction.limiteAge')) {
                    if ($this->Contratinsertion->WebrsaContratinsertion->limiteCumulDureeCER($personne_id) > 24) {
                        $blockCumulCER66 = true;
                    }
                }
                $this->set('blockCumulCER66', $blockCumulCER66);


                $typeOrientPrincipaleEmploiId = Configure::read('Orientstruct.typeorientprincipale.Emploi');
                if (is_array($typeOrientPrincipaleEmploiId) && isset($typeOrientPrincipaleEmploiId[0])) {
                    $typeOrientPrincipaleEmploiId = $typeOrientPrincipaleEmploiId[0];
                } else {
                    trigger_error(__('Le type orientation principale Emploi n\'est pas bien défini.'), E_USER_WARNING);
                }

                $conditionsTypeorient = array('Typeorient.parentid' => $typeOrientPrincipaleEmploiId);

                $cuiEncours = $this->Contratinsertion->Personne->Cui->find(
					'first',
					array(
						'conditions' => array(
							'Cui.personne_id' => $personne_id,
							'NOT' => array(
								'Cui66.etatdossiercui66' => array( 'perime', 'rupturecontrat', 'decisionsanssuite', 'nonvalide', 'annule' )
							)
						),
						'contain' => false,
						'joins' => array(
							$this->Contratinsertion->Personne->Cui->join( 'Cui66' )
						),
						'recursive' => -1
					)
                );
                $this->set(compact('cuiEncours'));
            } else {
                $typeOrientPrincipaleEmploiId = Configure::read('Typeorient.emploi_id');
                if (empty($typeOrientPrincipaleEmploiId)) {
                    trigger_error(__('Le type orientation principale Emploi n\'est pas bien défini.'), E_USER_WARNING);
                }

                $conditionsTypeorient = array('Typeorient.id' => $typeOrientPrincipaleEmploiId);
            }

            $orientstructEmploi = $this->Contratinsertion->Personne->Orientstruct->find(
                    'first', array(
                'conditions' => array(
                    'Orientstruct.personne_id' => $personne_id,
                    'Orientstruct.statut_orient' => 'Orienté',
                    $conditionsTypeorient,
                    'Orientstruct.id IN ( ' . $this->Contratinsertion->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere('Orientstruct.personne_id') . ' )'
                ),
                'order' => 'Orientstruct.date_valid DESC',
                'contain' => array(
                    'Typeorient',
                    'Structurereferente'
                )
                    )
            );
            $this->set(compact('orientstructEmploi'));
            if (Configure::read('Cg.departement') == 58) {
                $isOrientBloquante = Set::classicExtract($orientstructEmploi, 'Structurereferente.typestructure');
                $bloquageAjoutCER = 0;
                if ($isOrientBloquante == 'oa') {
                    $bloquageAjoutCER = 1;
                }
                $this->set('bloquageAjoutCER', $bloquageAjoutCER);
            }
        }

        if (Configure::read('Cg.departement') == 58) {

            $nbDemandedemaintienNonfinalisesCovs = 0;
            $cumulCer = $this->Contratinsertion->WebrsaContratinsertion->limiteCumulDureeCER($personne_id);
            if ($cumulCer >= 12) {
                // Nombre de dossiers COV de cette thématique qui ne sont pas finalisés (Demande de maintien en social)
                $demandedemaintien = $this->Contratinsertion->Personne->Dossiercov58->qdDossiersNonFinalises($personne_id, 'proposnonorientationsproscovs58');
                $nbDemandedemaintienNonfinalisesCovs = $this->Contratinsertion->Personne->Dossiercov58->find('count', $demandedemaintien);
            }
            $this->set('nbDemandedemaintienNonfinalisesCovs', $nbDemandedemaintienNonfinalisesCovs);

            $qdEnCours = $this->Contratinsertion->Personne->Dossiercov58->Propocontratinsertioncov58->qdEnCours($personne_id);
            $propocontratinsertioncov58 = $this->Contratinsertion->Personne->Dossiercov58->Propocontratinsertioncov58->find('first', $qdEnCours);

            // Nombre de dossiers COV de cette thématique qui ne sont pas finalisés.
            $qdDossiersCov58NonFinalises = $this->Contratinsertion->Personne->Dossiercov58->qdDossiersNonFinalises($personne_id, 'proposcontratsinsertioncovs58');
            $nbdossiersnonfinalisescovs = $this->Contratinsertion->Personne->Dossiercov58->find('count', $qdDossiersCov58NonFinalises);
            $this->set('nbdossiersnonfinalisescovs', $nbdossiersnonfinalisescovs);

			// 
            $querydata = $this->_qdThematiqueEp('Sanctionep58', $personne_id);
            $querydata['fields'] = Set::merge(
                            $querydata['fields'], array(
                        'Sanctionep58.id',
                        'Sanctionep58.contratinsertion_id',
                        'Sanctionep58.created',
                        'Sanctionep58.modified',
                            )
            );

            $sanctionseps58 = $this->Contratinsertion->Signalementep93->Dossierep->find('all', $querydata);

            $contratsenep = Set::extract($sanctionseps58, '/Sanctionep58/contratinsertion_id');

            $soumisADroitEtDevoir = $this->Contratinsertion->Personne->Calculdroitrsa->isSoumisAdroitEtDevoir($personne_id);

            $this->set(compact('sanctionseps58', 'contratsenep', 'soumisADroitEtDevoir', 'propocontratinsertioncov58'));

            $this->set('erreursCandidatePassage', $this->Contratinsertion->Sanctionep58->Dossierep->getErreursCandidatePassage($personne_id));
            $this->set('optionsdossierscovs58', array_merge($this->Contratinsertion->Personne->Orientstruct->Personne->Dossiercov58->Passagecov58->enums(), $this->Contratinsertion->Personne->Orientstruct->Personne->Dossiercov58->Propocontratinsertioncov58->enums()));
            $this->set('optionsdossierseps', $this->Contratinsertion->Sanctionep58->Dossierep->Passagecommissionep->enums());
        } else if (Configure::read('Cg.departement') == 66) {
            $persreferent = $this->Contratinsertion->Personne->PersonneReferent->find(
                    'count', array(
                'conditions' => array(
                    'PersonneReferent.personne_id' => $personne_id,
                    'PersonneReferent.dfdesignation IS NULL'
                ),
                'recursive' => -1
                    )
            );
            $this->set(compact('persreferent'));


            $listesActions = $this->Contratinsertion->Personne->ActioncandidatPersonne->find(
                    'all', array(
                'fields' => array(
                    'Actioncandidat.name'
                ),
                'conditions' => array(
                    'ActioncandidatPersonne.personne_id' => $personne_id,
                    'ActioncandidatPersonne.positionfiche = \'encours\''
                ),
                'contain' => array(
                    'Actioncandidat'
                ),
                'order' => 'ActioncandidatPersonne.id DESC'
                    )
            );
            $this->set('listesActions', $listesActions);
        } else if (Configure::read('Cg.departement') == 93) {
            // Des dossiers pour la thématique des signalements ?
            $querydata = $this->_qdThematiqueEp('Signalementep93', $personne_id);
            $querydata['fields'] = Set::merge(
                            $querydata['fields'], array(
                        'Signalementep93.contratinsertion_id',
                        'Signalementep93.id',
                        'Signalementep93.motif',
                        'Signalementep93.date',
                        'Signalementep93.rang',
                        'Signalementep93.created',
                        'Signalementep93.modified',
                            )
            );

            $signalementseps93 = $this->Contratinsertion->Signalementep93->Dossierep->find('all', $querydata);

            // Des dossiers pour la thématique des signalements ?
            $querydata = $this->_qdThematiqueEp('Contratcomplexeep93', $personne_id);
            $querydata['fields'] = Set::merge(
                            $querydata['fields'], array(
                        'Contratcomplexeep93.contratinsertion_id',
                        'Contratcomplexeep93.id',
                        'Contratcomplexeep93.created',
                        'Contratcomplexeep93.modified',
                            )
            );
            $contratscomplexeseps93 = $this->Contratinsertion->Contratcomplexeep93->Dossierep->find('all', $querydata);

            $contratsenep = Set::merge(
                            Set::extract($signalementseps93, '/Signalementep93/contratinsertion_id'), Set::extract($contratscomplexeseps93, '/Contratcomplexeep93/contratinsertion_id')
            );

            $this->set(compact('signalementseps93', 'contratscomplexeseps93', 'contratsenep'));

            $this->set('erreursCandidatePassage', $this->Contratinsertion->Signalementep93->Dossierep->getErreursCandidatePassage($personne_id));
            $this->set('optionsdossierseps', $this->Contratinsertion->Signalementep93->Dossierep->Passagecommissionep->enums());
        }

        $this->_setOptions();
        $this->set('personne_id', $personne_id);

        // Recherche des CER, suivanrt le CG
        $querydata = $this->Contratinsertion->qdIndex($personne_id);
        $this->set('contratsinsertion', $this->Contratinsertion->find('all', $querydata));

        $this->render('index_' . Configure::read('nom_form_ci_cg'));
    }

    /**
     * Visualisation d'un CER en particulier (CG 58, 66, 93).
     *
     * @param integer $contratinsertion_id
     */
    public function view($contratinsertion_id = null) {
		$query = array(
			'fields' => array_merge(
					$this->Contratinsertion->fields(),
					$this->Contratinsertion->Action->fields(),
					$this->Contratinsertion->Actioninsertion->fields(),
					$this->Contratinsertion->Propodecisioncer66->fields(),
					array(
						$this->Contratinsertion->Referent->sqVirtualField( 'nom_complet' ),
						'Structurereferente.lib_struc',
						'Typeorient.lib_type_orient',
						$this->Contratinsertion->Personne->sqVirtualField( 'nom_complet' ),
						$this->Contratinsertion->Propodecisioncer66->Motifcernonvalid66Propodecisioncer66->Motifcernonvalid66->vfListeMotifs( 'Propodecisioncer66.id', '', ', ' ).' AS "Propodecisioncer66__listeMotifs66"'
				)
			),
			'joins' => array(
				$this->Contratinsertion->join( 'Action', array( 'type' => 'LEFT OUTER' ) ),
				$this->Contratinsertion->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
				$this->Contratinsertion->join( 'Personne', array( 'type' => 'INNER' ) ),
				$this->Contratinsertion->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
				$this->Contratinsertion->Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) ),
				$this->Contratinsertion->join( 'Actioninsertion', array( 'type' => 'LEFT OUTER' ) ),
				$this->Contratinsertion->join( 'Propodecisioncer66', array( 'type' => 'LEFT OUTER' ) ),
				// $this->Contratinsertion->Propodecisioncer66->join( 'Motifcernonvalid66Propodecisioncer66', array( 'type' => 'LEFT OUTER' ) ),
				// $this->Contratinsertion->Propodecisioncer66->Motifcernonvalid66Propodecisioncer66->join( 'Motifcernonvalid66', array( 'type' => 'LEFT OUTER' ) )
			),
			'conditions' => array(
				'Contratinsertion.id' => $contratinsertion_id
			),
			'recursive' => -1,
			'contain' => false
		);

		if( Configure::read( 'Cg.departement' ) == 66 ) {
			$query['fields'][] = 'Contratinsertion.num_contrat_66';
		}

		$contratinsertion = $this->Contratinsertion->find( 'first', $query );

        $this->assert(!empty($contratinsertion), 'invalidParameter');

        $this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $contratinsertion['Contratinsertion']['personne_id'])));

        // Utilisé pour les détections de fiche de candidature pour savoir si des actions sont en cours ou non
        $fichescandidature = $this->Contratinsertion->Personne->ActioncandidatPersonne->find(
                'all', array(
            'conditions' => array(
                'ActioncandidatPersonne.personne_id' => $contratinsertion['Contratinsertion']['personne_id'],
                'ActioncandidatPersonne.positionfiche = \'encours\'',
            ),
            'contain' => array(
                'Actioncandidat' => array(
                    'Contactpartenaire' => array(
                        'Partenaire'
                    )
                ),
                'Referent'
            )
                )
        );

        $this->_setOptions();
        $this->set(compact('contratinsertion', 'fichescandidature'));
        $this->set('personne_id', $contratinsertion['Contratinsertion']['personne_id']);
        $this->set('urlmenu', '/contratsinsertion/index/' . $contratinsertion['Contratinsertion']['personne_id']);

        // Retour à la liste en cas d'annulation
        if (isset($this->request->data['Cancel'])) {
            $this->redirect(array('action' => 'index', $contratinsertion['Contratinsertion']['personne_id']));
        }
    }

    /**
     * Formulaire d'ajout d'un CER (CG 58, 66, 93).
     *
     * @param integer $id
     */
    public function add($id = null) {
        $args = func_get_args();
        call_user_func_array(array($this, '_add_edit'), $args);
    }

    /**
     * Formulaire modification d'un CER (CG 58, 66, 93).
     *
     * @param integer $id
     */
    public function edit($id = null) {
        $args = func_get_args();
        call_user_func_array(array($this, '_add_edit'), $args);
    }

    /**
     * Récupération des données socio pro (notamment Niveau etude) lié au
     * contrat (CG 93).
     *
     * @param type $personne_id
     * @return type
     */
    protected function _getDsp($personne_id) {
        $this->Contratinsertion->Personne->Dsp->unbindModelAll();
        $dsp = $this->Contratinsertion->Personne->Dsp->find(
                'first', array(
            'fields' => array(
                'Dsp.id',
                'Dsp.personne_id',
                'Dsp.nivetu',
                'Dsp.nivdipmaxobt',
                'Dsp.annobtnivdipmax',
            ),
            'conditions' => array(
                'Dsp.personne_id' => $personne_id
            ),
            'recursive' => -1
                )
        );

        if (empty($dsp)) {
            $dsp = array('Dsp' => array('personne_id' => $personne_id));

            $this->Contratinsertion->Personne->Dsp->set($dsp);
            if ($this->Contratinsertion->Personne->Dsp->save($dsp)) {
                $qd_dsp = array(
                    'conditions' => array(
                        'Dsp.personne_id' => $personne_id
                    ),
                    'fields' => null,
                    'order' => null,
                    'recursive' => -1
                );
                $dsp = $this->Contratinsertion->Personne->Dsp->find('first', $qd_dsp);
            } else {
                $this->cakeError('error500');
            }
            $this->assert(!empty($dsp), 'error500');
        }

        $return = array();
        $return['Dsp'] = array(
            'id' => $dsp['Dsp']['id'],
            'personne_id' => $dsp['Dsp']['personne_id']
        );
        $return['Dsp']['nivetu'] = ( ( isset($dsp['Dsp']['nivetu']) ) ? $dsp['Dsp']['nivetu'] : null );
        $return['Dsp']['nivdipmaxobt'] = ( ( isset($dsp['Dsp']['nivdipmaxobt']) ) ? $dsp['Dsp']['nivdipmaxobt'] : null );
        $return['Dsp']['annobtnivdipmax'] = ( ( isset($dsp['Dsp']['annobtnivdipmax']) ) ? $dsp['Dsp']['annobtnivdipmax'] : null );

        return $return;
    }

    /**
     * Formulaire d'ajout ou de modification d'un CER (CG 58, 66, 93).
     *
     * INFO: 521 lignes @20120928.15:52
     *
     * @param integer $id
     */
    protected function _add_edit($id = null) {
        $this->assert(!empty($id), 'invalidParameter');

        $valueFormeci = null;
        if ($this->action == 'add') {
            $personne_id = $id;

            // TODO: $this->request->data Contratinsertion.forme_ci
            $valueFormeci = 'S';

            // TODO: $this->request->data Contratinsertion.num_contrat
            $nbContratsPrecedents = $this->Contratinsertion->find('count', array('recursive' => -1, 'conditions' => array('Contratinsertion.personne_id' => $personne_id)));
            if ($nbContratsPrecedents >= 1) {
                $tc = 'REN';
            } else {
                $tc = 'PRE';
            }
        } else if ($this->action == 'edit') {
            $contratinsertion = $this->Contratinsertion->find(
                    'first', array(
                'conditions' => array(
                    'Contratinsertion.id' => $id
                ),
                'contain' => array(
                    'Autreavissuspension',
                    'Autreavisradiation',
                )
                    )
            );
            $this->assert(!empty($contratinsertion), 'invalidParameter');

            $personne_id = $contratinsertion['Contratinsertion']['personne_id'];

            // TODO: $this->request->data Contratinsertion.forme_ci
            $valueFormeci = Set::classicExtract($contratinsertion, 'Contratinsertion.forme_ci');
            //$nbContratsPrecedents = $this->Contratinsertion->find( 'count', array( 'recursive' => -1, 'conditions' => array( 'Contratinsertion.personne_id' => $personne_id ) ) );
            // TODO: $this->request->data Contratinsertion.num_contrat
            $tc = Set::classicExtract($contratinsertion, 'Contratinsertion.num_contrat');
        }

        $this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));

        // Récupération de l'id du dossier
        $dossier_id = $this->Contratinsertion->Personne->dossierId($personne_id);
        $this->assert(!empty($dossier_id), 'invalidParameter');
//			$this->set( 'dossier_id', $dossier_id );
        // Tentative d'acquisition du jeton sur le dossier
        $this->Jetons2->get($dossier_id);

        // Retour à la liste en cas d'annulation (on relache le jeton sur le dossier)
        if (isset($this->request->data['Cancel'])) {
            $this->Jetons2->release($dossier_id);
            $this->redirect(array('action' => 'index', $personne_id));
        }

        /**
         *   Utilisé pour les dates de suspension et de radiation
         *   Si les dates ne sont pas présentes en base, elles ne seront pas affichées
         *   Situation dossier rsa : dtclorsa -> date de radiation
         *   Suspension droit : ddsusdrorsa -> date de suspension
         *
         * CG 66 et 93
         */
        // TODO: $this->request->data Contratinsertion.dateradiationparticulier et Contratinsertion.datesuspensionparticulier
        $situationdossierrsa = $this->Contratinsertion->Personne->Foyer->Dossier->Situationdossierrsa->find(
                'first', array(
            'conditions' => array(
                'Situationdossierrsa.dossier_id' => $dossier_id
            ),
            'contain' => array(
                'Suspensiondroit' => array(
                    'fields' => array(
                        'Suspensiondroit.ddsusdrorsa'
                    ),
                    'order' => 'Suspensiondroit.ddsusdrorsa DESC',
                    'limit' => 1
                )
            )
                )
        );

        // On ajout l'ID de l'utilisateur connecté afin de récupérer son service instructeur
        $personne = $this->Contratinsertion->Personne->WebrsaPersonne->newDetailsCi($personne_id, $this->Session->read('Auth.User.id'));

        /// Calcul du numéro du contrat d'insertion
        $nbrCi = $this->Contratinsertion->find('count', array('conditions' => array('Personne.id' => $personne_id)));

        $numouverturedroit = $this->Contratinsertion->checkNumDemRsa($personne_id);

        //$this->set( 'nbContratsPrecedents', $nbContratsPrecedents );
        $this->set('tc', $tc);
        $this->set(compact('situationdossierrsa'));
        $this->set('personne', $personne);
        $this->set('numouverturedroit', $numouverturedroit);
        $this->set('valueFormeci', $valueFormeci);

        // Utilisé pour les détections de fiche de candidature pour savoir
        // si des actions sont en cours ou non, (CG 66, affichage)
        if (Configure::read('Cg.departement') == 66) {
            $fichescandidature = $this->Contratinsertion->Personne->ActioncandidatPersonne->find(
                    'all', array(
                'conditions' => array(
                    'ActioncandidatPersonne.personne_id' => $personne_id,
                    'ActioncandidatPersonne.positionfiche = \'encours\''
                ),
                'contain' => array(
                    'Actioncandidat' => array(
                        'Contactpartenaire' => array(
                            'Partenaire'
                        )
                    ),
                    'Referent'
                )
                    )
            );
            $this->set(compact('fichescandidature'));

            $cersPrecedents = $this->Contratinsertion->find(
                    'all', array(
                'fields' => array_merge(
                        $this->Contratinsertion->Actioncandidat->fields(), $this->Contratinsertion->Actioncandidat->Contactpartenaire->fields(), $this->Contratinsertion->Actioncandidat->Contactpartenaire->Partenaire->fields(), $this->Contratinsertion->Referent->fields(), array(
                    'Contratinsertion.id',
                    'Contratinsertion.actioncandidat_id'
                        )
                ),
                'conditions' => array(
                    'Contratinsertion.personne_id' => $personne_id
                ),
                'joins' => array(
                    $this->Contratinsertion->join('Actioncandidat', array('type' => 'INNER')),
                    $this->Contratinsertion->Actioncandidat->join('Contactpartenaire', array('type' => 'LEFT OUTER')),
                    $this->Contratinsertion->Actioncandidat->Contactpartenaire->join('Partenaire', array('type' => 'LEFT OUTER')),
                    $this->Contratinsertion->join('Referent', array('type' => 'LEFT OUTER')),
                ),
                'contain' => false
                    )
            );
            $action = null;
            foreach ($cersPrecedents as $i => $cerPrecedent) {
                $action = $cerPrecedent;
            }
            $this->set('action', $action);
        }

        /// Essai de sauvegarde
        if (!empty($this->request->data)) { // INFO: 168 lignes @20120928.16:09
            $this->Contratinsertion->begin();

            if ($this->action == 'add') {
                $this->request->data['Contratinsertion']['rg_ci'] = $nbrCi + 1;
            }

            if (Configure::read('nom_form_ci_cg') == 'cg58') {
                $this->request->data['Contratinsertion']['forme_ci'] = 'S';
                $this->request->data['Contratinsertion']['datevalidation_ci'] = Set::classicExtract($this->request->data, 'Contratinsertion.dd_ci');
            }

            $contratinsertionRaisonCi = Set::classicExtract($this->request->data, 'Contratinsertion.raison_ci');
            if ($contratinsertionRaisonCi == 'S') {
                $this->request->data['Contratinsertion']['avisraison_ci'] = Set::classicExtract($this->request->data, 'Contratinsertion.avisraison_suspension_ci');
            } else if ($contratinsertionRaisonCi == 'R') {
                $this->request->data['Contratinsertion']['avisraison_ci'] = Set::classicExtract($this->request->data, 'Contratinsertion.avisraison_radiation_ci');
            }

            //FIXME: bloc à commenter une fois confirmé le fait de ne plus valider automatiquemlent les CERs à l'enregistrement
// 				if( Configure::read( 'nom_form_ci_cg' ) == 'cg66' ) {
// 					$contratinsertionDecisionCi = Set::classicExtract( $this->request->data, 'Contratinsertion.forme_ci' );
// 					if( $contratinsertionDecisionCi == 'S' ) {
// 						///Validation si le contrat est simple (CG66)
// 						$this->request->data['Contratinsertion']['decision_ci'] = 'V';
// 						$this->request->data['Contratinsertion']['datevalidation_ci'] = $this->request->data['Contratinsertion']['date_saisi_ci'];
// 					}
// 				}

            /**
             *   Utilisé pour les dates de suspension et de radiation
             *   Si les dates ne sont pas présentes en base, elles ne seront pas affichées
             *   Situation dossier rsa : dtclorsa -> date de radiation
             *   Suspension droit : ddsusdrorsa -> date de suspension
             */
            if (isset($situationdossierrsa)) {
                if (!empty($situationdossierrsa['Situationdossierrsa']['dtclorsa'])) {
                    $this->request->data['Contratinsertion']['dateradiationparticulier'] = $situationdossierrsa['Situationdossierrsa']['dtclorsa'];
                }
                if (!empty($situationdossierrsa['Suspensiondroit'][0]['ddsusdrorsa'])) {
                    $this->request->data['Contratinsertion']['datesuspensionparticulier'] = $situationdossierrsa['Suspensiondroit'][0]['ddsusdrorsa'];
                }
            }

            // Si Contratinsertion.objetcerprecautre est disabled, on enregistre null
            $this->request->data = Set::merge(array('Contratinsertion' => array('objetcerprecautre' => null)), $this->request->data);

            $this->Contratinsertion->create($this->request->data);
            $success = $this->Contratinsertion->save();

            // Enregistrement des DSP (CG 93)
            if (Configure::read('nom_form_ci_cg') == 'cg93') {
                $dspStockees = $this->_getDsp($personne_id);
                $this->request->data['Dsp'] = Set::merge(
                                isset($dspStockees['Dsp']) ? Hash::filter((array) $dspStockees['Dsp']) : array(), isset($this->request->data['Dsp']) ? Hash::filter((array) $this->request->data['Dsp']) : array()
                );

                $isDsp = Hash::filter((array) $this->request->data['Dsp']);
                if (!empty($isDsp)) {
                    $success = $this->Contratinsertion->Personne->Dsp->save(array('Dsp' => $this->request->data['Dsp'])) && $success;
                }
            }

            // Sauvegarde des numéros de téléphone si ceux-ci ne sont pas présents en amont (CG 66)
            if (isset($this->request->data['Personne'])) {
                $isDataPersonne = Hash::filter((array) $this->request->data['Personne']);
                if (!empty($isDataPersonne)) {
                    $success = $this->Contratinsertion->Personne->save(array('Personne' => $this->request->data['Personne'])) && $success;
                }
            }

            // CGs 66, 93
            $models = array('Autreavissuspension', 'Autreavisradiation');
            foreach ($models as $model) {
                if ($this->action == 'add') {
                    $this->{$this->modelClass}->{$model}->set('contratinsertion_id', $this->{$this->modelClass}->id);
                } else if ($this->action == 'edit') {
                    $this->Contratinsertion->{$model}->deleteAll(array("{$model}.contratinsertion_id" => $this->Contratinsertion->id));
                }

                if (isset($this->request->data[$model])) {
                    $is{$model} = Hash::filter((array) $this->request->data[$model]);
                    if (!empty($is{$model})) {
                        $Autresavis = Set::extract($is{$model}, "/{$model}");
                        $data = array($model => array());

                        foreach ($Autresavis as $i => $Autreavis) {
                            $data[$model][] = array(
                                'contratinsertion_id' => $this->Contratinsertion->id,
                                strtolower($model) => $Autreavis
                            );
                        }
                        $success = $this->Contratinsertion->{$model}->saveAll($data[$model], array('atomic' => false)) && $success;
                    }
                }
            }

            // CG 93
            if (isset($this->request->data['Actioninsertion'])) {
                $isActioninsertion = Hash::filter((array) $this->request->data['Actioninsertion']);
                $this->{$this->modelClass}->Actioninsertion->set('contratinsertion_id', $this->{$this->modelClass}->id);

                if (!empty($isActioninsertion)) {
                    $success = $this->Contratinsertion->Actioninsertion->save(array('Actioninsertion' => $this->request->data['Actioninsertion'])) && $success;
                }
            }

            // Un contrat complexe est directement envoyé en EP (CG 93)
            if (Configure::read('Cg.departement') == 93 && $this->request->data['Contratinsertion']['forme_ci'] == 'C') {
                $dossierep = array(
                    'Dossierep' => array(
                        'themeep' => 'contratscomplexeseps93',
                        'personne_id' => $personne_id
                    )
                );

                $this->Contratinsertion->Personne->Dossierep->create($dossierep);
                $tmpSuccess = $this->Contratinsertion->Personne->Dossierep->save();

                // Sauvegarde des données de la thématique
                if ($tmpSuccess) {
                    $contratcomplexeep93 = array(
                        'Contratcomplexeep93' => array(
                            'dossierep_id' => $this->Contratinsertion->Personne->Dossierep->id,
                            'contratinsertion_id' => $this->Contratinsertion->id
                        )
                    );

                    $this->Contratinsertion->Personne->Dossierep->Contratcomplexeep93->create($contratcomplexeep93);
                    $success = $this->Contratinsertion->Personne->Dossierep->Contratcomplexeep93->save() && $success;
                }
                $success = $success && $tmpSuccess;
            }

            if ($success) {
                $saved = true;

                // Au 66, si on enregistre un CER pour l'allocataire, on passe le statut de son RDV
                // "01 - Convocation à un Entretien - Contrat" de "Prévu" à "Venu(e)"
                if (Configure::read('Cg.departement') == 66) {
                    $cg66Rendezvous = Configure::read('Contratinsertion.Cg66.Rendezvous');
                    $lastrdvorient = $this->Contratinsertion->Referent->Rendezvous->find(
                            'first', array(
                        'fields' => array(
                            'Rendezvous.id'
                        ),
                        'conditions' => array(
                            'Rendezvous.typerdv_id' => $cg66Rendezvous['conditions']['typerdv_id'],
                            'Rendezvous.personne_id' => $this->request->data['Contratinsertion']['personne_id'],
                            'Rendezvous.statutrdv_id' => $cg66Rendezvous['conditions']['statutrdv_id']
                        ),
                        'contain' => false,
                        'order' => array('Rendezvous.daterdv DESC')
                            )
                    );

                    if (!empty($lastrdvorient)) {
                        $lastrdvorient['Rendezvous']['statutrdv_id'] = $cg66Rendezvous['statutrdv_id'];
                        $saved = $this->Contratinsertion->Referent->Rendezvous->save($lastrdvorient) && $saved;
                    }
                }

                if ($saved) {
                    $this->Contratinsertion->commit();
                    $this->Jetons2->release($dossier_id);
                    $this->Session->setFlash('Enregistrement effectué', 'flash/success');
                    $this->redirect(array('controller' => 'contratsinsertion', 'action' => 'index', $personne_id));
                } else {
                    $this->Contratinsertion->rollback();
                    $this->Session->setFlash('Erreur lors de l\'enregistrement', 'flash/error');
                }
            } else {
                $this->Contratinsertion->rollback();
                $this->Session->setFlash('Erreur lors de l\'enregistrement', 'flash/error');
            }
        } else { // Préparation des données du formulaire ...: prepareFormData ?
            if ($this->action == 'edit') {
                $this->request->data = $contratinsertion;

                // CG 93
                $actioninsertion = $this->Contratinsertion->Actioninsertion->find(
                        'first', array(
                    'conditions' => array(
                        'Actioninsertion.contratinsertion_id' => $contratinsertion['Contratinsertion']['id'],
                        'Actioninsertion.dd_action IS NOT NULL'
                    ),
                    'recursive' => -1,
                    'order' => array('Actioninsertion.dd_action DESC')
                        )
                );
                $this->request->data['Actioninsertion'] = Hash::get($actioninsertion, 'Actioninsertion');

                // Suspension / Radiation (CG 66, 93)
                if ($this->request->data['Contratinsertion']['raison_ci'] == 'S') {
                    $this->request->data['Contratinsertion']['avisraison_suspension_ci'] = $this->request->data['Contratinsertion']['avisraison_ci'];
                } else if ($this->request->data['Contratinsertion']['raison_ci'] == 'R') {
                    $this->request->data['Contratinsertion']['avisraison_radiation_ci'] = $this->request->data['Contratinsertion']['avisraison_ci'];
                }

                // Si on est en présence d'un deuxième contrat -> Alors renouvellement
                $nbrCi = $contratinsertion['Contratinsertion']['rg_ci'];
            }

            // CG 93
            $this->request->data = Set::merge($this->request->data, $this->_getDsp($personne_id));
        }

        $this->set('nbrCi', $nbrCi);

        // Doit-on setter les valeurs par défault ?
        $dataStructurereferente_id = Set::classicExtract($this->request->data, "{$this->Contratinsertion->alias}.structurereferente_id");
        $dataReferent_id = Set::classicExtract($this->request->data, "{$this->Contratinsertion->alias}.referent_id");

        // Si le formulaire ne possède pas de valeur pour ces champs, on met celles par défaut
        if (empty($dataStructurereferente_id) && empty($dataReferent_id)) {
            // Recherche du type d'orientation
            $orientstruct = $this->Contratinsertion->Structurereferente->Orientstruct->find(
                    'first', array(
                'conditions' => array(
                    'Orientstruct.personne_id' => $personne_id,
                    'Orientstruct.typeorient_id IS NOT NULL',
                    'Orientstruct.statut_orient' => 'Orienté'
                ),
                'order' => 'Orientstruct.date_valid DESC',
                'recursive' => -1
                    )
            );

            // Référent du parcours
            $personne_referent = $this->Contratinsertion->Personne->PersonneReferent->find(
                    'first', array(
                'conditions' => array(
                    'PersonneReferent.personne_id' => $personne_id,
                    'PersonneReferent.dfdesignation IS NULL'
                ),
                'recursive' => -1
                    )
            );

            $structurereferente_id = $referent_id = null;
            // Valeur par défaut préférée: à partir de personnes_referents
            if (!empty($personne_referent)) {
                $structurereferente_id = Set::classicExtract($personne_referent, "{$this->Contratinsertion->Personne->PersonneReferent->alias}.structurereferente_id");
                $referent_id = Set::classicExtract($personne_referent, "{$this->Contratinsertion->Personne->PersonneReferent->alias}.referent_id");
            }
            // Valeur par défaut de substitution: à partir de orientsstructs
            else if (!empty($orientstruct)) {
                $structurereferente_id = Set::classicExtract($orientstruct, "{$this->Contratinsertion->Personne->Orientstruct->alias}.structurereferente_id");
                $referent_id = Set::classicExtract($orientstruct, "{$this->Contratinsertion->Personne->Orientstruct->alias}.referent_id");
            }

            if (!empty($structurereferente_id)) {
                $this->request->data = Hash::insert($this->request->data, "{$this->Contratinsertion->alias}.structurereferente_id", $structurereferente_id);
            }

            if (!empty($structurereferente_id) && !empty($referent_id)) {
                $this->request->data = Hash::insert($this->request->data, "{$this->Contratinsertion->alias}.referent_id", preg_replace('/^_$/', '', "{$structurereferente_id}_{$referent_id}"));
            }
        }

        // Ajout des listes de strctures référentes et de référents
        if (Configure::read('Cg.departement') == 66) {
            // TODO: grep -nr "Configure::read.*Orientstruct\.typeorientprincipale" app | grep -v "\.svn"
            $typeOrientPrincipaleEmploiId = Configure::read('Orientstruct.typeorientprincipale.Emploi');
            if (is_array($typeOrientPrincipaleEmploiId) && isset($typeOrientPrincipaleEmploiId[0])) {
                $typeOrientPrincipaleEmploiId = $typeOrientPrincipaleEmploiId[0];
            } else {
                trigger_error(__('Le type orientation principale Emploi n\'est pas bien défini.'), E_USER_WARNING);
            }

            /* $structures = $this->Contratinsertion->Structurereferente->find(
              'list',
              array(
              'fields' => array(
              'Structurereferente.id',
              'Structurereferente.lib_struc',
              'Typeorient.lib_type_orient'
              ),
              'joins' => array(
              $this->Contratinsertion->Structurereferente->join( 'Typeorient', array( 'type' => 'INNER' ) )
              ),
              'recursive' => -1,
              'order' => array(
              'Typeorient.lib_type_orient ASC',
              'Structurereferente.lib_struc'
              ),
              'conditions' => array(
              'Structurereferente.actif' => 'O',
              'Typeorient.parentid <>' => $typeOrientPrincipaleEmploiId
              )
              )
              ); */

            $structures = $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'conditions' => array( 'Typeorient.parentid <>' => $typeOrientPrincipaleEmploiId ) + $this->InsertionsBeneficiaires->conditions['structuresreferentes'], 'prefix' => false ) );

            //On affiche les actions inactives en édition mais pas en ajout,
            // afin de pouvoir gérer les actions n'étant plus prises en compte mais toujours en cours
            $isactive = 'O';
            if ($this->action == 'edit') {
                $isactive = array('O', 'N');
            }
            $actionsSansFiche = $this->{$this->modelClass}->Actioncandidat->listePourFicheCandidature(null, $isactive, array('0', '1'));
            $this->set('actionsSansFiche', $actionsSansFiche);
        } else {
            $structures = $this->Contratinsertion->Structurereferente->listOptions();
        }

        $referents = $this->Contratinsertion->Referent->WebrsaReferent->listOptions();

        $struct_id = Set::classicExtract($this->request->data, 'Contratinsertion.structurereferente_id');
        // FIXME: $this->request->data Contratinsertion.structurereferente_id
        $this->set('struct_id', $struct_id);

        /* if( !empty( $struct_id ) ) {
          $struct = $this->Contratinsertion->Structurereferente->find(
          'first',
          array(
          'fields' => array(
          'Structurereferente.num_voie',
          'Structurereferente.type_voie',
          'Structurereferente.nom_voie',
          'Structurereferente.code_postal',
          'Structurereferente.ville',
          ),
          'conditions' => array(
          'Structurereferente.id' => Set::extract( $this->request->data, 'Contratinsertion.structurereferente_id' )
          ),
          'recursive' => -1
          )
          );
          $this->set( 'StructureAdresse', $struct['Structurereferente']['num_voie'].' '.$struct['Structurereferente']['type_voie'].' '.$struct['Structurereferente']['nom_voie'].'<br/>'.$struct['Structurereferente']['code_postal'].' '.$struct['Structurereferente']['ville'] );
          } */

        $referent_id = Set::classicExtract($this->request->data, 'Contratinsertion.referent_id');
        $referent_id = preg_replace('/^[0-9]+_([0-9]+)$/', '\1', $referent_id);
        // TODO: $this->request->data Contratinsertion.referent_id
        $this->set('referent_id', $referent_id);

        // CG 66
        if (!empty($referent_id) && !empty($this->request->data['Contratinsertion']['referent_id'])) {
            $contratinsertionReferentId = preg_replace('/^[0-9]+_([0-9]+)$/', '\1', $this->request->data['Contratinsertion']['referent_id']);
            $referent = $this->Contratinsertion->Structurereferente->Referent->find(
                    'first', array(
                'fields' => array(
                    'Referent.email',
                    'Referent.fonction',
                    'Referent.nom',
                    'Referent.prenom',
                    'Referent.numero_poste',
                ),
                'conditions' => array(
                    'Referent.id' => $contratinsertionReferentId
                ),
                'recursive' => -1
                    )
            );

//				$this->set( 'ReferentEmail', $referent['Referent']['email'].'<br/>'.$referent['Referent']['numero_poste'] );
//				$this->set( 'ReferentFonction', $referent['Referent']['fonction'] );
            $this->set('ReferentNom', $referent['Referent']['nom'] . ' ' . $referent['Referent']['prenom']);
        }

		if ( Configure::read( 'Cg.departement' ) == 66 && isset($personne_id) ) {
			$entretiens = $this->Contratinsertion->Personne->Entretien->find( 'all', $this->Contratinsertion->Personne->Entretien->queryEntretiens( $personne_id )	);
			$this->set( compact( 'entretiens' ) );
		}

        $this->_setOptions();
        $this->set(compact('structures', 'referents'));
        $this->set('urlmenu', '/contratsinsertion/index/' . $personne_id);

        $this->render('add_edit_specif_cg' . Configure::read('Cg.departement'));
    }

    /**
     * Formulaire de validation d'un CER (CG 66, 93).
     *
     * @param integer $contratinsertion_id
     */
    public function valider($contratinsertion_id = null) {
        if (Configure::read('Cg.departement') == 66) {
            $fields = array(
                'Contratinsertion.id',
                'Contratinsertion.personne_id',
                'Contratinsertion.structurereferente_id',
                'Contratinsertion.forme_ci',
                'Contratinsertion.observ_ci',
                'Contratinsertion.datevalidation_ci',
                'Contratinsertion.datedecision',
                'Contratinsertion.decision_ci',
                'Contratinsertion.positioncer',
                'Contratinsertion.dd_ci',
                'Contratinsertion.df_ci',
                'Contratinsertion.duree_engag',
                'Propodecisioncer66.isvalidcer',
                'Propodecisioncer66.datevalidcer',
                $this->Contratinsertion->Referent->sqVirtualField('nom_complet'),
            );
            $contain = array(
                'Propodecisioncer66',
                'Referent'
            );
        } else {
            $fields = array(
                'Contratinsertion.id',
                'Contratinsertion.personne_id',
                'Contratinsertion.structurereferente_id',
                'Contratinsertion.forme_ci',
                'Contratinsertion.observ_ci',
                'Contratinsertion.datevalidation_ci',
                'Contratinsertion.decision_ci',
                'Contratinsertion.positioncer',
                'Contratinsertion.dd_ci',
                'Contratinsertion.df_ci'
            );
            $recursive = -1;
            $contain = false;
        }

        $contratinsertion = $this->Contratinsertion->find(
                'first', array(
            'fields' => $fields,
            'conditions' => array(
                'Contratinsertion.id' => $contratinsertion_id
            ),
            'contain' => $contain
                )
        );

        $this->assert(!empty($contratinsertion), 'invalidParameter');
        $this->set('contratinsertion', $contratinsertion);

        $this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $contratinsertion['Contratinsertion']['personne_id'])));

        $dossier_id = $this->Contratinsertion->dossierId($contratinsertion_id);
        $this->Jetons2->get($dossier_id);

        $this->set('personne_id', $contratinsertion['Contratinsertion']['personne_id']);

        // Retour à la liste en cas d'annulation
        if (!empty($this->request->data) && isset($this->request->data['Cancel'])) {
            $this->Jetons2->release($dossier_id);
            $this->redirect(array('action' => 'index', $contratinsertion['Contratinsertion']['personne_id']));
        }

        if (!empty($this->request->data)) {
            if ($this->Contratinsertion->valider($this->request->data)) {
                $this->Jetons2->release($dossier_id);
                $this->Session->setFlash('Enregistrement effectué', 'flash/success');
                $this->redirect(array('controller' => 'contratsinsertion', 'action' => 'index', $contratinsertion['Contratinsertion']['personne_id']));
            }
        } else {
            $this->request->data = $contratinsertion;
        }

        $this->_setOptions();
        $this->set('urlmenu', '/contratsinsertion/index/' . $contratinsertion['Contratinsertion']['personne_id']);
        $this->render('valider');
    }

    /**
     * *Fonction de validation pour les CERs Simples (CG 66).
     *
     * @param type $contratinsertion_id
     *
     */
    public function validersimple($contratinsertion_id = null) {
        $this->Contratinsertion->id = $contratinsertion_id;
        $forme_ci = $this->Contratinsertion->field('forme_ci');
        $this->assert(( $forme_ci == 'S'), 'error500');

        $this->valider($contratinsertion_id);
    }

    /**
     * Fonction de validation pour les CERs Particuliers (CG 66).
     *
     * @param type $contratinsertion_id
     *
     */
    public function validerparticulier($contratinsertion_id = null) {
        $this->Contratinsertion->id = $contratinsertion_id;
        $forme_ci = $this->Contratinsertion->field('forme_ci');
        $this->assert(( $forme_ci == 'C'), 'error500');

        $this->valider($contratinsertion_id);
    }

    /**
     * Suppression d'un CER (CG 58, 93).
     *
     * @param integer $id
     */
    public function delete($id) {
        $dossier_id = $this->Contratinsertion->dossierId($id);
        $this->DossiersMenus->checkDossierMenu(array('id' => $dossier_id));

        $this->Jetons2->get($dossier_id);

        $this->{$this->modelClass}->begin();
        $success = $this->{$this->modelClass}->Actioninsertion->deleteAll(array('Actioninsertion.contratinsertion_id' => $id));
        $success = $this->{$this->modelClass}->delete($id) && $success;
        $this->_setFlashResult('Delete', $success);

        if ($success) {
            $this->{$this->modelClass}->commit();
            $this->Jetons2->release($dossier_id);
        } else {
            $this->{$this->modelClass}->rollback();
        }

        $this->redirect($this->referer());
    }

    /**
     * Fonction pour annuler le CER (CG 66).
     *
     * @param type $id
     */
    public function cancel($id) {
        $qd_contrat = array(
            'conditions' => array(
                $this->modelClass . '.id' => $id
            ),
            'fields' => null,
            'order' => null,
            'recursive' => -1
        );
        $contrat = $this->{$this->modelClass}->find('first', $qd_contrat);

        $personne_id = Set::classicExtract($contrat, 'Contratinsertion.personne_id');
        $this->set('personne_id', $personne_id);

        $this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));

        $dossier_id = $this->Contratinsertion->dossierId($id);
        $this->Jetons2->get($dossier_id);

        // Retour à la liste en cas d'annulation
        if (!empty($this->request->data) && isset($this->request->data['Cancel'])) {
            $this->Jetons2->release($dossier_id);
            $this->redirect(array('action' => 'index', $personne_id));
        }

		// Dans ce contexte-ci, la raison de l'annulation est obligatoire
		$this->Contratinsertion->validate['motifannulation']['notEmpty'] = array(
			'rule' => array( 'notEmpty' ),
			'message' => 'Champ obligatoire'
		);

        if (!empty($this->request->data)) {
            $this->Contratinsertion->begin();

			$this->request->data['Contratinsertion']['positioncer'] = 'annule';
			$this->request->data['Contratinsertion']['decision_ci'] = 'A';

            $saved = $this->Contratinsertion->save($this->request->data);

			$saved = $saved && $this->Contratinsertion->WebrsaContratinsertion->updatePositionsCersByConditions(
				array( 'Contratinsertion.personne_id' => $contrat['Contratinsertion']['personne_id'] )
			);

            /*$saved = $this->{$this->modelClass}->updateAllUnBound(
                            array('Contratinsertion.positioncer' => '\'annule\''), array(
                        '"Contratinsertion"."personne_id"' => $contrat['Contratinsertion']['personne_id'],
                        '"Contratinsertion"."id"' => $contrat['Contratinsertion']['id']
                            )
                    ) && $saved;*/

            if ($saved) {
                $this->Contratinsertion->commit();
                $this->Jetons2->release($dossier_id);
                $this->Session->setFlash('Enregistrement effectué', 'flash/success');
                $this->redirect(array('action' => 'index', $personne_id));
            } else {
                $this->Contratinsertion->rollback();
                $this->Session->setFlash('Erreur lors de l\'enregistrement.', 'flash/error');
            }
        } else {
            $this->request->data = $contrat;
        }
        $this->set('urlmenu', '/contratsinsertion/index/' . $personne_id);
    }

    /**
     * Retourn le PDF de notification d'un CER pour l'OP (CG 66).
     *
     * @param integer $id L'id du CER pour lequel générer la notification.
     * @return void
     */
    public function notificationsop($contratinsertion_id = null) {
        $personne_id = $this->Contratinsertion->personneId($contratinsertion_id);
        $this->DossiersMenus->checkDossierMenu(array('personne_id' => $personne_id));

        $pdf = $this->Contratinsertion->getNotificationopPdf($contratinsertion_id, $this->Session->read('Auth.User.id'));

        if (!empty($pdf)) {
            $this->Gedooo->sendPdfContentToClient($pdf, sprintf("contratinsertion_%d_notificationop_%s.pdf", $contratinsertion_id, date('Y-m-d')));
        } else {
            $this->Session->setFlash('Impossible de générer la notification du CER pour l\'OP.', 'default', array('class' => 'error'));
            $this->redirect($this->referer());
        }
    }

    /**
     * Impression de la fiche de liaison d'un CER (CG 66).
     *
     * @param integer $contratinsertion_id
     * @return void
     */
    public function ficheliaisoncer($contratinsertion_id) {
        $personne_id = $this->Contratinsertion->personneId($contratinsertion_id);
        $this->DossiersMenus->checkDossierMenu(array('personne_id' => $personne_id));

        $pdf = $this->Contratinsertion->getPdfFicheliaisoncer($contratinsertion_id, $this->Session->read('Auth.User.id'));

        if (!empty($pdf)) {
            $this->Gedooo->sendPdfContentToClient($pdf, "contratinsertion_{$contratinsertion_id}_FicheLiaison.pdf");
        } else {
            $this->Session->setFlash('Impossible de générer la fiche de liaison', 'default', array('class' => 'error'));
            $this->redirect($this->referer());
        }
    }

    /**
     * Impression d'une notification pour le bénéficiaire concernant une
     * proposition de décision d'un CER  (CG 66).
     *
     * @param integer $id
     * @return void
     */
    public function notifbenef($contratinsertion_id) {
        $personne_id = $this->Contratinsertion->personneId($contratinsertion_id);
        $this->DossiersMenus->checkDossierMenu(array('personne_id' => $personne_id));

        $pdf = $this->Contratinsertion->getPdfNotifbenef($contratinsertion_id, $this->Session->read('Auth.User.id'));

        if (!empty($pdf)) {
            $this->Gedooo->sendPdfContentToClient($pdf, "contratinsertion_{$contratinsertion_id}_NotificationBeneficiaire_.pdf");
        } else {
            $this->Session->setFlash('Impossible de générer la notification du bénéficiaire', 'default', array('class' => 'error'));
            $this->redirect($this->referer());
        }
    }

    /**
     * Imprime un CER (CG 58, 66, 93).
     * INFO: http://localhost/webrsa/trunk/contratsinsertion/impression/44327
     * FIXME: ajouter une colonne de date de première impression ?
     *
     * @param integer $contratinsertion_id
     * @return void
     */
    public function impression($contratinsertion_id = null) {
        $personne_id = $this->Contratinsertion->personneId($contratinsertion_id);
        $this->DossiersMenus->checkDossierMenu(array('personne_id' => $personne_id));

        $pdf = $this->Contratinsertion->getDefaultPdf($contratinsertion_id, $this->Session->read('Auth.User.id'));

        if (!empty($pdf)) {
            $this->Gedooo->sendPdfContentToClient($pdf, "contratinsertion_{$contratinsertion_id}_nouveau.pdf");
        } else {
            $this->Session->setFlash('Impossible de générer le courrier de contrat d\'insertion.', 'default', array('class' => 'error'));
            $this->redirect($this->referer());
        }
    }

    /**
     * Fonction permettant d'enregistrer la date de la notification au
     * bénéficiaire (CG 66).
     *
     * @param type $id
     */
    public function notification($id) {
        $this->assert(!empty($id), 'error404');

        $contratinsertion = $this->Contratinsertion->find(
                'first', array(
            'conditions' => array(
                'Contratinsertion.id' => $id
            ),
            'contain' => false
                )
        );

        $this->assert(!empty($contratinsertion), 'invalidParameter');
        $this->set('contratinsertion', $contratinsertion);

        $personne_id = $contratinsertion['Contratinsertion']['personne_id'];
        $this->set('personne_id', $personne_id);

        $this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));

        $dossier_id = $this->Contratinsertion->Personne->dossierId($personne_id);
        $this->assert(!empty($dossier_id), 'invalidParameter');

        $this->Jetons2->get($dossier_id);

        // Retour à l'index en cas d'annulation
        if (isset($this->request->data['Cancel'])) {
            $this->Jetons2->release($dossier_id);
            $this->redirect(array('action' => 'index', $personne_id));
        }

        if (!empty($this->request->data)) {
            $this->Contratinsertion->begin();

            $datenotification = $this->request->data['Contratinsertion']['datenotification'];
            $saved = $this->Contratinsertion->updateAllUnBound(
                    array('Contratinsertion.datenotification' => "'{$datenotification['year']}-{$datenotification['month']}-{$datenotification['day']}'"), array(
                '"Contratinsertion"."personne_id"' => $personne_id,
                '"Contratinsertion"."id"' => $id
                    )
            );

			$saved = $saved && $this->Contratinsertion->WebrsaContratinsertion->updatePositionsCersByConditions(
				array( 'Contratinsertion.personne_id' => $personne_id )
			);

            /*if ($saved) {
                $this->request->data['Contratinsertion']['decision_ci'] = $contratinsertion['Contratinsertion']['decision_ci'];
                $this->request->data['Contratinsertion']['positioncer'] = $this->Contratinsertion->calculPosition($this->request->data);

                $saved = $this->Contratinsertion->updateAllUnBound(
                        array('Contratinsertion.positioncer' => "'" . $this->request->data['Contratinsertion']['positioncer'] . "'"), array(
                    '"Contratinsertion"."personne_id"' => $personne_id,
                    '"Contratinsertion"."id"' => $id
                        )
                );
            }*/

            if ($saved) {
                $this->Contratinsertion->commit();
                $this->Jetons2->release($dossier_id);
                $this->Session->setFlash('Enregistrement effectué', 'flash/success');
                $this->redirect(array('controller' => 'contratsinsertion', 'action' => 'index', $personne_id));
            } else {
                $this->Contratinsertion->rollback();
                $this->Session->setFlash('Erreur lors de l\'enregistrement', 'flash/error');
            }
        } else {
            $this->request->data = $contratinsertion;
        }

        $this->set('urlmenu', '/contratsinsertion/index/' . $contratinsertion['Contratinsertion']['personne_id']);
        $this->render('notification');
    }

    /**
     * Impression d'une notification pour les bénéficiaires de + 55ans (CG 66).
     *
     * @param integer $id
     * @return void
     */
    public function reconduction_cer_plus_55_ans($contratinsertion_id) {
        $personne_id = $this->Contratinsertion->personneId($contratinsertion_id);
        $this->DossiersMenus->checkDossierMenu(array('personne_id' => $personne_id));

        $pdf = $this->Contratinsertion->getPdfReconductionCERPlus55Ans($contratinsertion_id, $this->Session->read('Auth.User.id'));

        $success = true;
        if (!empty($pdf)) {
            $success = $this->Contratinsertion->updateAllUnBound(
                            array('Contratinsertion.datetacitereconduction' => date("'Y-m-d'")), array(
                        '"Contratinsertion"."id"' => $contratinsertion_id,
                        '"Contratinsertion"."datetacitereconduction" IS NULL'
                            )
                    ) && $success;
            $this->Gedooo->sendPdfContentToClient($pdf, "taciteReconductionPlus55ans.pdf");
        } else {
            $this->Session->setFlash('Impossible de générer la notification du bénéficiaire', 'default', array('class' => 'error'));
        }
        $this->redirect($this->referer());
    }

	/**
	 * Moteur de recherche
	 */
	public function search() {
		$this->helpers[] = 'Search.SearchForm';

		$Recherches = $this->Components->load( 'WebrsaRecherchesContratsinsertion' );
		$Recherches->search();
		$this->Contratinsertion->validate = array();
		$this->Contratinsertion->Structurereferente->Orientstruct->validate = array();
	}

	/**
	 * Export du tableau de résultats de la recherche
	 */
	public function exportcsv() {
		$Recherches = $this->Components->load( 'WebrsaRecherchesContratsinsertion' );
		$Recherches->exportcsv();
	}

	/**
	 * Cohorte de validation de CER
	 */
	public function cohorte_nouveaux() {
		$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertionNouveaux' );

		$Cohortes->cohorte(
			array(
				'modelName' => 'Contratinsertion',
				'modelRechercheName' => 'WebrsaCohorteContratinsertionNouveau'
			)
		);
	}

	/**
	 * Cohorte de CER validés
	 */
	public function cohorte_valides() {
		$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertionValides' );
		$Cohortes->search(
			array(
				'modelName' => 'Contratinsertion',
				'modelRechercheName' => 'WebrsaCohorteContratinsertionValide'
			)
		);
	}

	/**
	 * Cohorte de CER validés
	 */
	public function exportcsv_valides() {
		$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertionValides' );
		$Cohortes->exportcsv(
			array(
				'modelName' => 'Contratinsertion',
				'modelRechercheName' => 'WebrsaCohorteContratinsertionValide'
			)
		);
	}

	/**
	 * Cohorte
	 */
	public function cohorte_cersimpleavalider() {
		$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertion' );
		$Cohortes->cohorte( array( 'modelRechercheName' => 'WebrsaCohorteContratinsertionCersimpleavalider' ) );
	}

	/**
	 * Export CSV
	 */
	public function exportcsv_cersimpleavalider() {
		$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertion' );
		$Cohortes->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteContratinsertionCersimpleavalider' ) );
	}

	/**
	 * Cohorte
	 */
	public function cohorte_cerparticulieravalider() {
		$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertion' );
		$Cohortes->cohorte( array( 'modelRechercheName' => 'WebrsaCohorteContratinsertionCersimpleavalider' ) );
		$this->view = 'cohorte_cersimpleavalider';
	}

	/**
	 * Export CSV
	 */
	public function exportcsv_cerparticulieravalider() {
		$Cohortes = $this->Components->load( 'WebrsaCohortesContratsinsertion' );
		$Cohortes->exportcsv( array( 'modelRechercheName' => 'WebrsaCohorteContratinsertionCersimpleavalider' ) );
	}

	/**
	 * Cohorte
	 */
	public function search_valides() {
		$Recherche = $this->Components->load( 'WebrsaRecherchesContratsinsertion' );
		$Recherche->search( array( 'modelRechercheName' => 'WebrsaRechercheContratinsertionValides' ) );
		$this->Contratinsertion->validate = array();
	}

	/**
	 * Export CSV
	 */
	public function exportcsv_search_valides() {
		$Recherche = $this->Components->load( 'WebrsaRecherchesContratsinsertion' );
		$Recherche->exportcsv( array( 'modelRechercheName' => 'WebrsaRechercheContratinsertionValides' ) );
	}
}
?>