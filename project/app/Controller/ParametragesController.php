<?php
	/**
	 * Code source de la classe ParametragesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe ParametragesController ...
	 *
	 * @package app.Controller
	 */
	class ParametragesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Parametrages';

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
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cataloguepdifp93',
			'Catalogueromev3',
			'Contactpartenaire',
			'Courrierpdo',
			'Partenaire',
			'Pieceaide66',
			'Themeapre66'
		);

		/**
		 * Paramétrages des APRE/ADRE
		 *
		 * @return array
		 */
		protected function _apres() {
			$departement = Configure::read( 'Cg.departement' );

			if( 66 == $departement ) {
				$items = array(
					__m('typesaidesapres66/index') => array(
						'url' => array( 'controller' => 'typesaidesapres66', 'action' => 'index' )
					),
					__m('piecesaides66/index') => array(
						'url' => array( 'controller' => 'piecesaides66', 'action' => 'index' )
					),
					__m('piecescomptables66/index') => array(
						'url' => array( 'controller' => 'piecescomptables66', 'action' => 'index' )
					),
					__m('themesapres66/index') => array(
						'url' => array( 'controller' => 'themesapres66', 'action' => 'index' )
					)
				);
			}
			else if( 93 == $departement ) {
				$items = array(
					__m('parametresfinanciers/index') => array(
						'url' => array( 'controller' => 'parametresfinanciers', 'action' => 'index' )
					),
					__m('participantscomites/index') => array(
						'url' => array( 'controller' => 'participantscomites', 'action' => 'index' )
					),
					__m('suivisaidesapres/index') => array(
						'url' => array( 'controller' => 'suivisaidesapres', 'action' => 'index' )
					),
					__m('tiersprestatairesapres/index') => array(
						'url' => array( 'controller' => 'tiersprestatairesapres', 'action' => 'index' )
					),
					__m('suivisaidesaprestypesaides/index') => array(
						'url' => array( 'controller' => 'suivisaidesaprestypesaides', 'action' => 'index' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des CER.
		 *
		 * @return array
		 */
		protected function _contratsinsertion() {
			$departement = Configure::read( 'Cg.departement' );

			if( 66 == $departement ) {
				$items = array(
					__m('motifscersnonvalids66/index') => array(
						'url' => array( 'controller' => 'motifscersnonvalids66', 'action' => 'index' )
					)
				);
			}
			else if( 93 == $departement ) {
				$items = array(
					__m('commentairesnormescers93/index') => array(
						'url' => array( 'controller' => 'commentairesnormescers93', 'action' => 'index' )
					),
					__m('metiersexerces/index') => array(
						'url' => array( 'controller' => 'metiersexerces', 'action' => 'index' )
					),
					__m('naturescontrats/index') => array(
						'url' => array( 'controller' => 'naturescontrats', 'action' => 'index' )
					),
					__m('secteursactis/index') => array(
						'url' => array( 'controller' => 'secteursactis', 'action' => 'index' )
					),
					__m('sujetscers93/index') => array(
						'url' => array( 'controller' => 'sujetscers93', 'action' => 'index' )
					),
					__m('soussujetscers93/index') => array(
						'url' => array( 'controller' => 'soussujetscers93', 'action' => 'index' )
					),
					__m('valeursparsoussujetscers93/index') => array(
						'url' => array( 'controller' => 'valeursparsoussujetscers93', 'action' => 'index' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des code ROME v.3.
		 *
		 * @return array
		 */
		protected function _cataloguesromesv3() {
			if( true == Configure::read( 'Romev3.enabled' ) ) {
				$items = array();
				foreach( $this->Catalogueromev3->modelesParametrages as $modelName ) {
					$tableName = Inflector::tableize( $modelName );
					$items[__d( 'cataloguesromesv3', "/Cataloguesromesv3/{$tableName}/:heading" )] = array(
						'url' => array( 'controller' => 'cataloguesromesv3', 'action' => $tableName )
					);
				}
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des CREANCES.
		 *
		 * @return array
		 */
		protected function _creances() {
			$activateTitreCreancier = Configure::read( 'Creances.Titrescreanciers.enabled' );
			$activateRecoursGracieux = Configure::read( 'Module.Recoursgracieux.enabled' );

			$items = array ();
			if( $activateTitreCreancier || $activateRecoursGracieux ) {
				if( $activateTitreCreancier) {
					$items[__m('titrescreanciers')] = array(
							__m('typestitrescreanciers/index') => array(
								'url' => array( 'controller' => 'typestitrescreanciers', 'action' => 'index' )
							),
							__m('motifsemissionstitrescreanciers/index') => array(
								'url' => array( 'controller' => 'motifsemissionstitrescreanciers', 'action' => 'index' )
							)
						);
					$items[__m('titresuivis')] = array(
							__m('typestitrescreanciersannulationsreductions/index') => array(
								'url' => array( 'controller' => 'typestitrescreanciersannulationsreductions', 'action' => 'index' )
							),
							__m('typestitrescreanciersinfospayeurs/index') => array(
								'url' => array( 'controller' => 'typestitrescreanciersinfospayeurs', 'action' => 'index' )
							),
							__m('typestitrescreanciersautresinfos/index') => array(
								'url' => array( 'controller' => 'typestitrescreanciersautresinfos', 'action' => 'index' )
							)
						);
				}
				if($activateRecoursGracieux ) {
					$items[__m('recoursgracieux')] = array(
							__m('originesrecoursgracieux/index') => array(
								'url' => array( 'controller' => 'originesrecoursgracieux', 'action' => 'index' )
							),
							__m('typesrecoursgracieux/index') => array(
								'url' => array( 'controller' => 'typesrecoursgracieux', 'action' => 'index' )
							),
							__m('motifsproposrecoursgracieux/index') => array(
								'url' => array( 'controller' => 'motifsproposrecoursgracieux', 'action' => 'index' )
							)
						);
				}
			}else{
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des EMAILS.
		 *
		 * @return array
		 */
		protected function _emails() {
			$activateEmails = Configure::read( 'Emails.Activer' );

			$items = array ();
			if( $activateEmails ) {
				$items = array(
					__m('emailsdestinataires/index') => array(
							'url' => array( 'controller' => 'emailsdestinataires', 'action' => 'index' )
					),
					__m('textsemails/index') => array(
						'url' => array( 'controller' => 'textsemails', 'action' => 'index' )
					),
					__m('piecesemails/index') => array(
						'url' => array( 'controller' => 'piecesemails', 'action' => 'index' )
					),
				);
			}else{
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des CUI.
		 *
		 * @return array
		 */
		protected function _cuis() {
			$departement = Configure::read( 'Cg.departement' );

			if( 66 == $departement && true == Configure::read( 'Module.Cui.enabled' ) ) {
				$items = array(
					__m('motifsrupturescuis66/index') => array(
						'url' => array( 'controller' => 'motifsrupturescuis66', 'action' => 'index' )
					),
					__m('motifssuspensioncuis66/index') => array(
						'url' => array( 'controller' => 'motifssuspensioncuis66', 'action' => 'index' )
					),
					__m('motifsrefuscuis66/index') => array(
						'url' => array( 'controller' => 'motifsrefuscuis66', 'action' => 'index' )
					),
					__m('piecesmailscuis66/index') => array(
						'url' => array( 'controller' => 'piecesmailscuis66', 'action' => 'index' )
					),
					__d( 'piecemanquantecui66', 'Pièces manquantes mails employeur' ) => array(
						'url' => array( 'controller' => 'piecesmanquantescuis66', 'action' => 'index' )
					),
					__m('tauxcgscuis66/index') => array(
						'url' => array( 'controller' => 'tauxcgscuis66', 'action' => 'index' )
					),
					__m('textsmailscuis66/index') => array(
						'url' => array( 'controller' => 'textsmailscuis66', 'action' => 'index' )
					),
					__m('typescontratscuis66/index') => array(
						'url' => array( 'controller' => 'typescontratscuis66', 'action' => 'index' )
					),
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des DSP.
		 *
		 * @return array
		 */
		protected function _dsps() {
			$items = array(
				__m('codesromesecteursdsps66/index') => array(
					'url' => array( 'controller' => 'codesromesecteursdsps66', 'action' => 'index' )
				),
				__m('codesromemetiersdsps66/index') => array(
					'url' => array( 'controller' => 'codesromemetiersdsps66', 'action' => 'index' )
				)
			);

			return $items;
		}

		/**
		 * Paramétrages de l'éditeur de requêtes.
		 *
		 * @return array
		 */
		protected function _requestsmanager() {
			if( true == Configure::read( 'Requestmanager.enabled' ) ) {
				$items = array(
					__m('codesromemetiersdsps66/index') => array(
						'url' => array( 'controller' => 'requestgroups', 'action' => 'index' )
					),
					__m('requestsmanager/savedindex') => array(
						'url' => array( 'controller' => 'requestsmanager', 'action' => 'savedindex' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des équipes pluridisciplinaires.
		 *
		 * @return array
		 */
		protected function _eps() {
			$departement = Configure::read( 'Cg.departement' );

			if( true == in_array( $departement, array( 58, 66, 93 )) ) {
				$items = array(
					__m('fonctionsmembreseps/index') => array(
						'url' => array( 'controller' => 'fonctionsmembreseps', 'action' => 'index' )
					),
					__m('regroupementseps/index') => array(
						'url' => array( 'controller' => 'regroupementseps', 'action' => 'index' )
					),
					__m('motifsreorientseps93/index') => array(
						'disabled' => 93 != $departement,
						'url' => array( 'controller' => 'motifsreorientseps93', 'action' => 'index' )
					),
					__m('compositionsregroupementseps/index') => array(
						'disabled' => 66 != $departement,
						'url' => array( 'controller' => 'compositionsregroupementseps', 'action' => 'index' )
					),
					__m('listesanctionseps58/index') => array(
						'disabled' => 58 != $departement,
						'url' => array( 'controller' => 'listesanctionseps58', 'action' => 'index' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des entretiens.
		 *
		 * @return array
		 */
		protected function _entretiens() {
			$items = array(
				__m('objetsentretien/index') => array(
					'url' => array( 'controller' => 'objetsentretien', 'action' => 'index' )
				),
				__m('permanences/index') => array(
					'url' => array( 'controller' => 'permanences', 'action' => 'index' )
				)
			);

			return $items;
		}

		/**
		 * Paramétrages des fiches de candidature (CD 66).
		 *
		 * @return array
		 */
		protected function _actionscandidats_personnes() {
			$departement = Configure::read( 'Cg.departement' );

			if( 66 == $departement ) {
				$items = array(
					__m('partenaires/index') => array(
						'url' => array( 'controller' => 'partenaires', 'action' => 'index' )
					),
					__m('contactspartenaires/index')  => array(
						'url' => array( 'controller' => 'contactspartenaires', 'action' => 'index' )
					),
					__m('actionscandidats/index')  => array(
						'url' => array( 'controller' => 'actionscandidats', 'action' => 'index' )
					),
					__m('progsfichescandidatures66/index')  => array(
						'url' => array( 'controller' => 'progsfichescandidatures66', 'action' => 'index' )
					),
					__m('valsprogsfichescandidatures66/index')  => array(
						'url' => array( 'controller' => 'valsprogsfichescandidatures66', 'action' => 'index' )
					),
					__m('motifssortie/index')  => array(
						'url' => array( 'controller' => 'motifssortie', 'action' => 'index' )
					),
					__m('raisonssocialespartenairescuis66/index')  => array(
						'url' => array( 'controller' => 'raisonssocialespartenairescuis66', 'action' => 'index' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des fiches de liaisons (CD 66).
		 *
		 * @return array
		 */
		protected function _fichedeliaisons() {
			$departement = Configure::read( 'Cg.departement' );

			if( 66 == $departement ) {
				$items = array(
					__m('motiffichedeliaisons/index') => array(
						'url' => array( 'controller' => 'motiffichedeliaisons', 'action' => 'index' )
					),
					__m('logicielprimos/index') => array(
						'url' => array( 'controller' => 'logicielprimos', 'action' => 'index' )
					),
					__m('propositionprimos/index') => array(
						'url' => array( 'controller' => 'propositionprimos', 'action' => 'index' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des fiches de prescription (CD 93).
		 *
		 * @return array
		 */
		protected function _fichesprescriptions93() {
			$departement = Configure::read( 'Cg.departement' );

			if( 93 == $departement ) {
				$items = array(
					__d( 'cataloguespdisfps93', '/Cataloguespdisfps93/search/:heading' ) => array(
						'url' => array( 'controller' => 'cataloguespdisfps93', 'action' => 'search' )
					)
				);
				foreach( $this->Cataloguepdifp93->modelesParametrages as $modelName ) {
					$items[__d( 'cataloguespdisfps93', "/Cataloguespdisfps93/index/{$modelName}/:heading" )] = array(
						'url' => array( 'controller' => 'cataloguespdisfps93', 'action' => 'index', $modelName )
					);
				}
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages du module FSE (CD 93).
		 *
		 * @return array
		 */
		protected function _modulefse93() {
			$departement = Configure::read( 'Cg.departement' );

			if( 93 == $departement ) {
				$items = array(
					__d( 'sortiesaccompagnementsd2pdvs93', '/Sortiesaccompagnementsd2pdvs93/index/:heading' ) => array(
						'url' => array( 'controller' => 'sortiesaccompagnementsd2pdvs93', 'action' => 'index' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des PDO/PCG.
		 *
		 * @return array
		 */
		protected function _pdos() {
			$departement = Configure::read( 'Cg.departement' );

			$items = array(
				__m('decisionspdos/index')=> array(
					'url' => array( 'controller' => 'decisionspdos', 'action' => 'index' )
				),
				__m('descriptionspdos/index') => array(
					'url' => array( 'controller' => 'descriptionspdos', 'action' => 'index' )
				),
				__m('originespdos/index') => array(
					'url' => array( 'controller' => 'originespdos', 'action' => 'index' )
				),
				__m('courrierPCG') => array(
					'disabled' => ( 66 != $departement ),
					__m('typescourrierspcgs66/index') => array(
						'url' => array( 'controller' => 'typescourrierspcgs66', 'action' => 'index' )
					),
					__m('modelestypescourrierspcgs66/index') => array(
						'url' => array( 'controller' => 'modelestypescourrierspcgs66', 'action' => 'index' )
					),
					__m('piecesmodelestypescourrierspcgs66/index') => array(
						'url' => array( 'controller' => 'piecesmodelestypescourrierspcgs66', 'action' => 'index' )
					)
				),
				__m('orgstransmisdossierspcgs66/index') => array(
					'disabled' => ( 66 != $departement ),
					'url' => array( 'controller' => 'orgstransmisdossierspcgs66', 'action' => 'index' )
				),
				__m('decisionPCG') => array(
					'disabled' => ( 66 != $departement ),
					__m('composfoyerspcgs66/index') => array(
						'url' => array( 'controller' => 'composfoyerspcgs66', 'action' => 'index' )
					),
					__m('decisionspcgs66/index') => array(
						'url' => array( 'controller' => 'decisionspcgs66', 'action' => 'index' )
					),
					__m('questionspcgs66/index') => array(
						'url' => array( 'controller' => 'questionspcgs66', 'action' => 'index' )
					)
				),
				__m('polesdossierspcgs66/index') => array(
					'disabled' => ( 66 != $departement ),
					'url' => array( 'controller' => 'polesdossierspcgs66', 'action' => 'index' )
				),
				__m('situationspdos/index') => array(
 					'url' => array( 'controller' => 'situationspdos', 'action' => 'index' )
				),
				 __m('statutspdos/index')=> array(
					'url' => array( 'controller' => 'statutspdos', 'action' => 'index' )
				),
				__m('typesnotifspdos/index') => array(
					'url' => array( 'controller' => 'typesnotifspdos', 'action' => 'index' )
				),
				__m('traitementstypespdos/index') => array(
					'disabled' => ( 66 == $departement ),
					'url' => array( 'controller' => 'traitementstypespdos', 'action' => 'index' )
				),
				__m('typespdos/index') => array(
					'url' => array( 'controller' => 'typespdos', 'action' => 'index' )
				),
				__m('typesrsapcgs66/index') => array(
					'disabled' => ( 66 != $departement ),
					'url' => array( 'controller' => 'typesrsapcgs66', 'action' => 'index' )
				),
				__m('courrierspdos/index') => array(
					'url' => array( 'controller' => 'courrierspdos', 'action' => 'index' )
				),
				__m('textareascourrierspdos/index') => array(
					'url' => array( 'controller' => 'textareascourrierspdos', 'action' => 'index' )
				)
			);

			return $items;
		}

		/**
		 * Paramétrages des rendez-vous.
		 *
		 * @return array
		 */
		protected function _rendezvous() {
			$departement = Configure::read( 'Cg.departement' );

			$items = array(
				__m('typesrdv/index') => array(
					'url' => array( 'controller' => 'typesrdv', 'action' => 'index' )
				),
				__m('statutsrdvs/index') => array(
					'url' => array( 'controller' => 'statutsrdvs', 'action' => 'index' )
				),
				__m('thematiquesrdvs/index') => array(
					'disabled' => false == Configure::read( 'Rendezvous.useThematique' ),
					'url' => array( 'controller' => 'thematiquesrdvs', 'action' => 'index' )
				),
				__m('statutsrdvs_typesrdv/index') => array(
					'disabled' => ( 58 != $departement ),
					'url' => array( 'controller' => 'statutsrdvs_typesrdv', 'action' => 'index' )
				)
			);

			return $items;
		}

		/**
		 * Paramétrages du tableau de bord.
		 *
		 * @return array
		 */
		protected function _dashboards() {
			if( true == Configure::read( 'Module.Dashboards.enabled' ) ) {
				$items = array(
					__m('roles/index') => array(
						'url' => array('controller' => 'roles', 'action' => 'index')
					),
					__m('categoriesactionroles/index') => array(
						'url' => array('controller' => 'categoriesactionroles', 'action' => 'index')
					),
					__m('actionroles/index') => array(
						'url' => array('controller' => 'actionroles', 'action' => 'index')
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des tags.
		 *
		 * @return array
		 */
		protected function _tags() {
			$items = array(
				__m('categorietags/index') => array(
					'url' => array( 'controller' => 'categorietags', 'action' => 'index' )
				),
				__m('valeurstags/index') => array(
					'url' => array( 'controller' => 'valeurstags', 'action' => 'index' )
				)
			);

			return $items;
		}

		/**
		 * Paramétrages de l'application, en fonction du département et des
		 * habilitations.
		 */
		public function index() {
			$departement = Configure::read( 'Cg.departement' );

			$items = array(
				__m('acceuil') => array(
					__m('accueilsarticles/index') => array(
						'url' => array( 'controller' => 'accueilsarticles', 'action' => 'index' )
					),
					__m('accueilscorrespondances/index') => array(
						'url' => array( 'controller' => 'accueilscorrespondances', 'action' => 'index' )
					),
				),
				__m('actions/index') => array(
					'disabled' => 66 == $departement,
					'url' => array( 'controller' => 'actions', 'action' => 'index' )
				),
				66 == $departement ? __m('APRE/ADRE') : __m('APRE') => $this->_apres(),
				__m('cantons/index') => array(
					'disabled' => false == Configure::read( 'CG.cantons' ),
					'url' => array( 'controller' => 'cantons', 'action' => 'index' )
				),
				__m('categoriespiecesjointes/index') => array(
					'disabled' => !Configure::read( 'Module.Piecejointe' ),
					'url' => array( 'controller' => 'categoriespiecesjointes', 'action' => 'index' )
				),
				__m('CER') => $this->_contratsinsertion(),
				__d( 'parametrages', '/Parametrages/cataloguesromesv3/:heading' ) => $this->_cataloguesromesv3(),
				__d( 'communautessrs', '/Communautessrs/index/:heading' ) => array(
					'disabled' => ( 93 != $departement ),
					'url' => array( 'controller' => 'communautessrs', 'action' => 'index' )
				),
				__m('Créances') => $this->_creances(),
				__m('CUI') => $this->_cuis(),
				__m('DSP') => $this->_dsps(),
				__m('requestsmanager') => $this->_requestsmanager(),
				__m('eps') => $this->_eps(),
				__m('emails') => $this->_emails(),
				__m('entretiens') => $this->_entretiens(),
				__m('actionscandidats_personnes') => $this->_actionscandidats_personnes(),
				__m('fichedeliaisons') => $this->_fichedeliaisons(),
				__m('fichesprescriptions93') => $this->_fichesprescriptions93(),
				__m('modulefse93') => $this->_modulefse93(),
				__m('pdos') => $this->_pdos(),
				__m('referents/index') => array(
					'url' => array( 'controller' => 'referents', 'action' => 'index' )
				),
				__m('relances') => array(
					'disabled' => 66 != $departement,
					__m('relances/index') => array(
						'url' => array( 'controller' => 'relances', 'action' => 'index' )
					),
					__m('relanceslogs/index') => array(
						'url' => array( 'controller' => 'relanceslogs', 'action' => 'index' )
					),
				),
				__m('rendezvous') => $this->_rendezvous(),
				__m('services66/index') => array(
					'disabled' => 66 != $departement,
					'url' => array( 'controller' => 'services66', 'action' => 'index' )
				),
				__m('servicesinstructeurs/index') => array(
					'url' => array( 'controller' => 'servicesinstructeurs', 'action' => 'index' )
				),
				__m('sitescovs58/index') => array(
					'disabled' => ( 58 != $departement ),
					'url' => array( 'controller' => 'sitescovs58', 'action' => 'index' )
				),
				__m('drees') => array(
					__m('dreesorganismes/index') => array(
						'url' => array('controller' => 'dreesorganismes', 'action' => 'index' )
					),
					__m('dreesactionscers/index') => array(
						'url' => array('controller' => 'dreesactionscers', 'action' => 'index' )
					),
				),
				__m('structuresreferentes/index') => array(
					'url' => array( 'controller' => 'structuresreferentes', 'action' => 'index' )
				),
				__m('dashboards') => $this->_dashboards(),
				__m('tags') => $this->_tags(),
				__m('typesactions/index') => array(
					'disabled' => 66 == $departement,
					'url' => array( 'controller' => 'typesactions', 'action' => 'index' )
				),
				__m('typesorients/index') => array(
					'url' => array( 'controller' => 'typesorients', 'action' => 'index' )
				),
				__m('motifsetatsdossiers/index') => array(
					'url' => array( 'controller' => 'motifsetatsdossiers', 'action' => 'index' )
				),
				__m('vaguesdorientations/index') => array(
					'Paramétrage des dates des vagues' => array(
						'url' => array( 'controller' => 'vaguesdorientations', 'action' => 'index' )
					),
				),
				__m('zonesgeographiques/index') => array(
					'url' => array( 'controller' => 'zonesgeographiques', 'action' => 'index' )
				)
			);

			$this->set( compact( 'items' ) );
		}
	}
