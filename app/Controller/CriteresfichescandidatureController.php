<?php
	/**
	 * Fichier source de la classe CriteresfichescandidatureController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Moteur de recherche de fiches de candidatures.
	 *
	 * @deprecated since version 3.0.0
	 * @see ActionscandidatsPersonnes::search() et ActionscandidatsPersonnes::exportcsv()
	 * @package app.Controller
	 */
	class CriteresfichescandidatureController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Criteresfichescandidature';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
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
			'Default',
			'Default2',
			'Locale',
			'Search',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Criterefichecandidature',
			'ActioncandidatPersonne',
			'Partenaire',
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
			'exportcsv',
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'exportcsv' => 'read',
			'index' => 'read',
		);

		/**
		*
		*/
		public function _setOptions() {
			$options = array();
			$optionsactions = (array)Hash::get( $this->ActioncandidatPersonne->Actioncandidat->enums(), 'Actioncandidat' );
			$actions = $this->ActioncandidatPersonne->Actioncandidat->find( 'list', array( 'fields' => array( 'name' ), 'order' => array( 'Actioncandidat.name ASC' ) ) );
			$partenaires = $this->Partenaire->find( 'list', array( 'fields' => array( 'libstruc' ), 'order' => array( 'Partenaire.libstruc ASC' ) ) );
			$motifssortie = $this->ActioncandidatPersonne->Motifsortie->find( 'list', array( 'fields' => array( 'name' ) ) );
			$options = (array)Hash::get( $this->ActioncandidatPersonne->enums(), 'ActioncandidatPersonne' );
			$options = Set::merge( $options, $optionsactions );


			$listeactions = $this->ActioncandidatPersonne->Actioncandidat->listActionParPartenaire();
            $this->set( 'progsfichescandidatures66', $this->ActioncandidatPersonne->Progfichecandidature66->find( 'list', array( 'conditions' => array( 'Progfichecandidature66.isactif' => '1' ) ) ) );

			$this->set( compact( 'actions', 'options', 'partenaires', 'motifssortie', 'listeactions' ) );
			$this->set( 'referents', $this->ActioncandidatPersonne->Referent->find( 'list', array( 'recursive' => -1 ) ) );
		}

		/**
		*
		*/

		public function index() {
			$this->Gestionzonesgeos->setCantonsIfConfigured();
			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			if( !empty( $this->request->data ) ) {

                $data = $this->request->data;
				if( !empty( $data['ActioncandidatPersonne']['actioncandidat_id'] )) {
					$actioncandidatId = suffix( $data['ActioncandidatPersonne']['actioncandidat_id'] );
					$data['ActioncandidatPersonne']['actioncandidat_id'] = $actioncandidatId;
				}


				$queryData = $this->Criterefichecandidature->search( $mesCodesInsee, $this->Session->read( 'Auth.User.filtre_zone_geo' ), $data );
				$queryData['limit'] = 10;
				$queryData['conditions'][] = WebrsaPermissions::conditionsDossier();
				$this->paginate = $queryData;
				$progressivePaginate = !Hash::get( $this->request->data, 'Pagination.nombre_total' );
				$actionscandidats_personnes = $this->paginate( $this->ActioncandidatPersonne, array(), array(), $progressivePaginate );




				foreach( $actionscandidats_personnes as $key => $actioncandidat_personne ) {
					$actionscandidats_personnes[$key]['Personne']['nom_complet'] = implode(
						' ',
						array(
							@$actionscandidats_personnes[$key]['Personne']['qual'],
							@$actionscandidats_personnes[$key]['Personne']['nom'],
							@$actionscandidats_personnes[$key]['Personne']['prenom']
						)
					);
					$actionscandidats_personnes[$key]['Referent']['nom_complet'] = implode(
						' ',
						array(
							@$actionscandidats_personnes[$key]['Referent']['qual'],
							@$actionscandidats_personnes[$key]['Referent']['nom'],
							@$actionscandidats_personnes[$key]['Referent']['prenom']
						)
					);

				}

				$this->set( 'actionscandidats_personnes', $actionscandidats_personnes );
			}

			$this->set( 'mesCodesInsee', $this->Gestionzonesgeos->listeCodesInsee() );

			$this->_setOptions();

			$this->set( 'structuresreferentesparcours', $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'conditions' => array( 'Structurereferente.orientation' => 'O' ) + $this->InsertionsBeneficiaires->conditions['structuresreferentes'], 'prefix' => false ) ) );
			$this->set( 'referentsparcours', $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) ) );

			$this->render( 'index' );
		}


		/**
		* Export du tableau en CSV
		*/

		public function exportcsv() {
			$mesZonesGeographiques = $this->Session->read( 'Auth.Zonegeographique' );
			$mesCodesInsee = ( !empty( $mesZonesGeographiques ) ? $mesZonesGeographiques : array() );

			$querydata = $this->Criterefichecandidature->search( $mesCodesInsee, $this->Session->read( 'Auth.User.filtre_zone_geo' ), Hash::expand( $this->request->params['named'], '__' ) );
			unset( $querydata['limit'] );
			$querydata['conditions'][] = WebrsaPermissions::conditionsDossier();
			$actionscandidats_personnes = $this->ActioncandidatPersonne->find( 'all', $querydata );

			foreach( $actionscandidats_personnes as $key => $actioncandidat_personne ) {
				$actionscandidats_personnes[$key]['Personne']['nom_complet'] = implode(
					' ',
					array(
						@$actionscandidats_personnes[$key]['Personne']['qual'],
						@$actionscandidats_personnes[$key]['Personne']['nom'],
						@$actionscandidats_personnes[$key]['Personne']['prenom']
					)
				);
				$actionscandidats_personnes[$key]['Referent']['nom_complet'] = implode(
					' ',
					array(
						@$actionscandidats_personnes[$key]['Referent']['qual'],
						@$actionscandidats_personnes[$key]['Referent']['nom'],
						@$actionscandidats_personnes[$key]['Referent']['prenom']
					)
				);

			}

			$this->_setOptions();
			$this->layout = ''; // FIXME ?
			$this->set( compact( 'actionscandidats_personnes' ) );
		}

	}
?>