<?php
	/**
	 * Code source de la classe Cohortesvalidationapres66Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Cohortesvalidationapres66Controller ... (CG 66)
	 * (CG 66 et 93).
	 *
	 * @package app.Controller
	 * @deprecated since version 3.0
	 */
	class Cohortesvalidationapres66Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cohortesvalidationapres66';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Cohortes' => array(
				'apresavalider',
				'transfert',
				'traitement',
			),
			'Gedooo.Gedooo',
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',			
			'Search.SearchPrg' => array(
				'actions' => array(
					'apresavalider' => array('filter' => 'Search'),
					'validees' => array('filter' => 'Search'),
					'notifiees' => array('filter' => 'Search'),
					'transfert' => array('filter' => 'Search'),
					'traitement' => array('filter' => 'Search')
				),
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'Default2',
			'Locale',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cohortevalidationapre66',
			'Aideapre66',
			'Apre',
			'Apre66',
			'Canton',
			'Dossier',
			'Option',
			'Zonegeographique',
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
			'apresavalider' => 'update',
			'exportcsv' => 'read',
			'notificationsCohorte' => 'read',
			'notifiees' => 'read',
			'traitement' => 'read',
			'transfert' => 'read',
			'validees' => 'update',
		);

		/**
		*
		*/
		public function _setOptions() {
			$this->set( 'options',  (array)Hash::get( $this->Apre66->enums(), 'Apre66' ) );

			$this->set( 'qual',  $this->Option->qual() );
			$this->set( 'optionsaideapre66',  (array)Hash::get( $this->Aideapre66->enums(), 'Aideapre66' ) );
			$this->set( 'referents',  $this->Apre->Referent->find( 'list' ) );
			$this->set( 'themes', $this->Apre66->Aideapre66->Themeapre66->find( 'list' ) );
			$this->set( 'typesaides', $this->Apre66->Aideapre66->Typeaideapre66->listOptions() );
		}



		/**
		*
		*/

		public function apresavalider() {
			$this->_index( 'Validationapre::apresavalider' );
		}

		/**
		*
		*/

		public function validees() {
			$this->_index( 'Validationapre::validees' );
		}

		/**
		*
		*/

		public function notifiees() {
			$this->_index( 'Validationapre::notifiees' );
		}

		/**
		*
		*/

		public function transfert() {
			$this->_index( 'Validationapre::transfert' );
		}

		/**
		*
		*/

		public function traitement() {
			$this->_index( 'Validationapre::traitementcellule' );
		}

		/**
		*
		*/

		protected function _index( $statutValidation = null ) {
			$this->assert( !empty( $statutValidation ), 'invalidParameter' );

			$this->set( 'cantons', $this->Gestionzonesgeos->listeCantons() );
			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			if( !empty( $this->request->data ) ) {
				///Sauvegarde
				// On a renvoyé  le formulaire de la cohorte
				if( !empty( $this->request->data['Aideapre66'] ) ) {

                    $this->Cohortes->get( array_unique( Set::extract( $this->request->data, 'Aideapre66.{n}.dossier_id' ) ) );
					// Ajout des règles de validation
					$this->Apre66->Aideapre66->validationDecisionAllowEmpty( false );

                    unset( $this->Apre66->Aideapre66->validate['montantpropose'] );
					$valid = $this->Apre66->Aideapre66->saveAll( $this->request->data['Aideapre66'], array( 'validate' => 'only', 'atomic' => false ) );

					if( $valid ) {
						$this->Aideapre66->begin();
						$saved = $this->Apre66->Aideapre66->saveAll( $this->request->data['Aideapre66'], array( 'validate' => 'first', 'atomic' => false ) );

						if( $saved ) {
							$this->Aideapre66->commit();
                            $this->Session->setFlash( 'Enregistrement effectué.', 'flash/success' );
                            $this->Cohortes->release( array_unique( Set::extract( $this->request->data, 'Aideapre66.{n}.dossier_id' ) ) );
							unset( $this->request->data['Aideapre66'], $this->request->data['Apre66'] );
						}
						else {
							$this->Aideapre66->rollback();
                            $this->Session->setFlash( 'Erreur lors de l\'enregistrement.', 'flash/error' );
						}
					}
				}
				else if( in_array( $this->action, array( 'traitement', 'transfert' ) ) && !empty( $this->request->data['Apre66'] ) ){
                    $this->Cohortes->get( array_unique( Set::extract( $this->request->data, 'Apre66.{n}.dossier_id' ) ) );

					$valid = $this->Apre66->saveAll( $this->request->data['Apre66'], array( 'validate' => 'only', 'atomic' => false ) );

					if( $valid ) {
						$this->Apre66->begin();
						$saved = $this->Apre66->saveAll( $this->request->data['Apre66'], array( 'validate' => 'first', 'atomic' => false ) );

						if( $saved ) {
							$this->Apre66->commit();
                            $this->Session->setFlash( 'Enregistrement effectué.', 'flash/success' );
                            $this->Cohortes->release( array_unique( Set::extract( $this->request->data, 'Apre66.{n}.dossier_id' ) ) );
							unset( $this->request->data['Apre66'] );
						}
						else {
							$this->Apre66->rollback();
                            $this->Session->setFlash( 'Erreur lors de l\'enregistrement.', 'flash/error' );
						}
					}
				}

				///Filtrage

				if( ( $statutValidation == 'Validationapre::apresavalider' ) || ( $statutValidation == 'Validationapre::traitementcellule' ) || ( $statutValidation == 'Validationapre::notifiees' ) || ( $statutValidation == 'Validationapre::transfert' ) || ( $statutValidation == 'Validationapre::validees' ) && !empty( $this->request->data ) ) {

                    $paginate = $this->Cohortevalidationapre66->search(
                        $statutValidation,
                        (array)$this->Session->read( 'Auth.Zonegeographique' ),
                        $this->Session->read( 'Auth.User.filtre_zone_geo' ),
                        $this->request->data,
                        $this->Cohortes->sqLocked( 'Dossier' )
                    );
					$paginate['limit'] = 10;
					$paginate['conditions'][] = WebrsaPermissions::conditionsDossier();


					$this->paginate = $paginate;
					$progressivePaginate = !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' );
					$cohortevalidationapre66 = $this->paginate( 'Apre66', array(), array(), $progressivePaginate );

					//Pour le lien filelink, sauvegarde de l'URL de la recherche lorsqu'on cliquera sur le bouton "Retour" dans la liste des fichiers liés
					$this->Session->write( "Savedfilters.Apres66.filelink",
						Set::merge(
							array(
								'controller' => Inflector::underscore( $this->name ),
								'action' => $this->action
							),
							$this->request->params['named']
						)
					);


					foreach( $cohortevalidationapre66 as $key => $value ) {
						if( empty( $value['Aideapre66']['datemontantaccorde'] ) ) {
							$cohortevalidationapre66[$key]['Aideapre66']['proposition_datemontantaccorde'] = date( 'Y-m-d' );
						}
						else{
							$cohortevalidationapre66[$key]['Aideapre66']['proposition_datemontantaccorde'] = $value['Aideapre66']['datemontantaccorde'];
						}

						$cohortevalidationapre66[$key]['Aideapre66']['datemontantpropose'] = $value['Aideapre66']['datemontantpropose'];

					}

					$this->Cohortes->get( Set::extract( $cohortevalidationapre66, '{n}.Dossier.id' ) );
					$this->set( 'cohortevalidationapre66', $cohortevalidationapre66 );

				}

			}

			$this->_setOptions();

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			switch( $statutValidation ) {
				case 'Validationapre::apresavalider':
					$this->render( 'formulaire' );
					break;
				case 'Validationapre::validees':
					$this->render( 'visualisation' );
					break;
				case 'Validationapre::notifiees':
					$this->render( 'notifiees' );
					break;
				case 'Validationapre::transfert':
					$this->render( 'transfert' );
					break;
				case 'Validationapre::traitementcellule':
					$this->render( 'traitement' );
					break;
			}
		}

		/**
		 * Export des résultats sous forme de tableau CSV.
		 *
		 * @return void
		 */
		public function exportcsv() {
			$querydata = $this->Cohortevalidationapre66->search(
				'Validationapre::notifiees',
				(array)$this->Session->read( 'Auth.Zonegeographique' ),
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				Hash::expand( $this->request->params['named'], '__' )
			);
			unset( $querydata['limit'] );
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();
			$apres = $this->Apre66->find( 'all', $querydata );

			$this->_setOptions();
			$this->layout = '';
			$this->set( compact( 'apres' ) );
		}

		/**
		 * Génération de la cohorte des notification d'APRE pour le CG 66.
		 *
		 * @return void
		 */
		public function notificationsCohorte( $statutValidation ) {
			$this->Apre66->begin();

			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			$querydata = $this->Cohortevalidationapre66->search(
				"Validationapre::{$statutValidation}",
				$mesCodesInsee,
				$this->Session->read( 'Auth.User.filtre_zone_geo' ),
				Hash::expand( $this->request->params['named'], '__' ),
				$this->Cohortes->sqLocked( 'Dossier' )
			);
			unset( $querydata['limit'] );
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();
			$apres = $this->Apre66->find( 'all', $querydata );

			$pdfs = array();
			foreach( Set::extract( '/Apre66/id', $apres ) as $apre_id ) {
				$pdfs[] = $this->Apre66->WebrsaApre66->getNotificationAprePdf( $apre_id );
			}
			$pdfs = $this->Gedooo->concatPdfs( $pdfs, 'NotificationsApre' );

			if( $pdfs ) {
				$this->Apre66->commit();
				$this->Gedooo->sendPdfContentToClient( $pdfs, sprintf( 'NotificationsApre-%s.pdf', date( 'Y-m-d' ) ) );
			}
			else {
				$this->Apre66->rollback();
				$this->Session->setFlash( 'Impossible de générer les notifications d\'APRE.', 'default', array( 'class' => 'error' ) );
				$this->redirect( $this->referer() );
			}
		}
	}
?>