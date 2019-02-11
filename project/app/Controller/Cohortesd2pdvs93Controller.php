<?php
	/**
	 * Code source de la classe Cohortesd2pdvs93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Cohortesd2pdvs93Controller ...
	 *
	 * @package app.Controller
	 */
	class Cohortesd2pdvs93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cohortesd2pdvs93';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'Cohortes' => array(
				'index'
			),
			'Gestionzonesgeos',
			'InsertionsBeneficiaires',
			'Search.SearchFiltresdefaut' => array(
				'index',
			),
			'Search.SearchPrg' => array(
				'actions' => array(
					'index' => array('filter' => 'Search'),
				),
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Allocataires',
			'Cake1xLegacy.Ajax',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cohorted2pdv93',
			'Dossier',
			'Option',
			'Personne',
			'Questionnaired2pdv93',
			'Tableausuivipdv93',
			'WebrsaTableausuivipdv93',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'ajaxadd' => 'Questionnairesd2pdvs93:index'
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
			'index' => 'read',
		);

		/**
		 * Moteur de recherche des questionnaires D2
		 */
		public function index() {
			if( !empty( $this->request->data ) ) {
				// Traitement du formulaire de recherche
				$querydata = $this->Cohorted2pdv93->search( $this->request->data['Search'] );

				$querydata = $this->Cohortes->qdConditions( $querydata );

				// INFO: on se base sur la SR du RDV plutôt que sur la zone géographique pour limiter les résultats
				$querydata['conditions'][] = array(
					'Rendezvous.structurereferente_id' => $this->InsertionsBeneficiaires->structuresreferentes(
						array(
							'type' => 'ids',
							'prefix' => false,
							'conditions' => array(
								'Structurereferente.id' => array_keys( $this->WebrsaTableausuivipdv93->listePdvs() )
							)
						)
					)
				);

				$this->paginate = array( 'Personne' => $querydata );
				$results = $this->paginate(
					$this->Personne,
					array(),
					array(),
					!Hash::get( $this->request->data, 'Search.Pagination.nombre_total' )
				);

				$this->Cohortes->get( Hash::extract( $results, '{n}.Dossier.id' ) );

				// On désactive le lien vers le formulaire Ajax lorsque l'on est en ajout,
				// que l'utilisateur est limité au niveau des codes INSEE et que le foyer
				// n'est plus sur un code INSEE auquel l'utilisateur a droit
				$restrict = (
					'0' == Hash::get( $this->request->data, 'Search.Questionnaired2pdv93.exists' )
					&& '1' == $this->Session->read( 'Auth.User.filtre_zone_geo' )
				);
				$zonesgeographiques = (array)$this->Session->read( 'Auth.Zonegeographique' );

				foreach( $results as $i => $result ) {
					$numcom = Hash::get( $result, 'Adresse.numcom' );
					$results[$i]['/Questionnaired2pdv93/ajaxadd'] = (
						false === $restrict
						|| true === in_array( $numcom, $zonesgeographiques )
					);
				}

				$this->set( compact( 'results' ) );
			}

			// Options à envoyer à la vue
			$years = array_reverse( array_range( 2009, date( 'Y' ) ) );
			$options = Hash::merge(
				array(
					'Adresse' => array(
						'numcom' => $this->Gestionzonesgeos->listeCodesInsee(),
						'canton' => $this->Gestionzonesgeos->listeCantons()
					),
					'Calculdroitrsa' => array(
						'toppersdrodevorsa' => $this->Option->toppersdrodevorsa()
					),
					'Questionnaired1' => array(
						'annee' => array_combine( $years, $years )
					),
					'Rendezvous' => array(
						'structurereferente_id' => $this->InsertionsBeneficiaires->structuresreferentes(
							array(
								'type' => InsertionsBeneficiairesComponent::TYPE_LIST,
								'prefix' => false,
								'conditions' => array(
									'Structurereferente.id' => array_keys( $this->WebrsaTableausuivipdv93->listePdvs() )
								)
							)
						)

					),
					'cantons' => $this->Gestionzonesgeos->listeCantons(),
					'mesCodesInsee' => $this->Gestionzonesgeos->listeCodesInsee(),
					'etatdosrsa' => ClassRegistry::init('Dossier')->enum('etatdosrsa'),
				),
				$this->Questionnaired2pdv93->options( array( 'find' => true ) ),
				$this->Allocataires->optionsSessionCommunautesr( 'Rendezvous' ),
				array(
					'PersonneReferent' => array(
						'structurereferente_id' => $this->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup', 'prefix' => false ) ),
						'referent_id' => $this->InsertionsBeneficiaires->referents( array( 'prefix' => true ) )
					)
				),
				$this->Allocataires->optionsSessionCommunautesr( 'PersonneReferent' )
			);
			$this->set( compact( 'options' ) );

			$this->set( 'isAjax', $this->request->is( 'ajax' ) );

			if( $this->request->is( 'ajax' ) ) {
				$this->layout = null;
			}
		}
	}
?>
