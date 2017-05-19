<?php
	/**
	 * Code source de la classe AjoutdossierscompletsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe AjoutdossierscompletsController ...
	 *
	 * @package app.Controller
	 */
	class AjoutdossierscompletsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Ajoutdossierscomplets';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default2',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Dossier',
			'Adresse',
			'Adressefoyer',
			'Ajoutdossiercomplet',
			'Dernierdossierallocataire',
			'Detaildroitrsa',
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
			'add' => 'create',
		);

		protected function  _setOptions() {
			$options = array();
            $services = ClassRegistry::init( 'Serviceinstructeur' )->find( 'list' );
			$options = array(
				'qual' => ClassRegistry::init( 'Option' )->qual()
			);
			$options = Hash::merge(
				$options,
				$this->Adresse->enums()
			);
			$this->set( compact( 'options', 'services' ) );
		}


       /**
        *
        */

        /**
        *
        */

        public function add(){

            // Si le formulaire est renvoyé
            if( !empty( $this->request->data ) ) {
				$data = $this->request->data;

				// Validation
				$this->Personne->set( $this->request->data['Personne'] );
				unset( $this->Personne->validate['dtnai'] );
				$valid = $this->Personne->validates();

				$this->Adresse->set( $this->request->data['Adresse'] );
				$this->Adressefoyer->set( $this->request->data['Adressefoyer'] );
				unset( $this->Adresse->validate['compladr'] );
				unset( $this->Adresse->validate['complideadr'] );
				$valid = $this->Adresse->validates() && $valid;
				$valid = $this->Adressefoyer->validates() && $valid;

				$this->Dossier->set( $this->request->data['Dossier'] );
				$valid = $this->Dossier->validates() && $valid;

				if( $valid ){
					// Début de la transaction
					$this->Dossier->begin();

					if( !empty( $data['Dossier']['numdemrsatemp'] ) ) {
						$data['Dossier']['numdemrsa'] = $this->Dossier->generationNumdemrsaTemporaire();
					}

					// Tentatives de sauvegarde
					$saved = $this->Dossier->save( $data['Dossier'] , array( 'atomic' => false ) );

					if( $saved ){

						// Détails du droit
						$data['Detaildroitrsa']['dossier_id'] = $this->Dossier->id;
						$saved = $this->Detaildroitrsa->save( $data['Detaildroitrsa'] , array( 'atomic' => false ) ) && $saved;

						// Situation dossier RSA
						$situationdossierrsa = array( 'Situationdossierrsa' => array( 'dossier_id' => $this->Dossier->id, 'etatdosrsa' => 'Z' ) );
						$this->Dossier->Situationdossierrsa->validate = array();
						$saved = $this->Dossier->Situationdossierrsa->save( $situationdossierrsa , array( 'atomic' => false ) ) && $saved;

						// Foyer
						$saved = $this->Foyer->save( array( 'dossier_id' => $this->Dossier->id ), array( 'atomic' => false ) ) && $saved;

						if( $data['Adresse']['presence'] == 1 ) {
							// Adresse
							$saved = $this->Adresse->save( $data['Adresse'] , array( 'atomic' => false ) ) && $saved;
						}
						else {
							//FIXME : création d'une adresse spécifique pour éviter les problèmes de code insee manquants
							$dataAdresse = array(
								'Adresse' => array(
									'numvoie'	=> '25',
									'libtypevoie'	=> 'RUE',
									'nomvoie'	=> 'petite la monnaie',
									'codepos'	=> '66000',
									'locaadr'	=> 'PERPIGNAN CEDEX',
									'numcomptt'	=> '66136',
									'foyerid'	=> $this->Foyer->id
								)
							);
							$saved = $this->Adresse->save( $dataAdresse , array( 'atomic' => false ) ) && $saved;
						}

						// Adresse foyer
						$data['Adressefoyer']['foyer_id'] = $this->Foyer->id;
						$data['Adressefoyer']['adresse_id'] = $this->Adresse->id;
						$saved = $this->Adressefoyer->save( $data['Adressefoyer'] , array( 'atomic' => false ) ) && $saved;

						// Personne
						$dataPersonne = array(
							'Personne' => $data['Personne']
						);
						$dataPersonne['Personne']['foyer_id'] = $this->Foyer->id;
						$this->Personne->create( $dataPersonne );
						$saved = $this->Personne->save( null, array( 'atomic' => false ) ) && $saved;

						// Prestation
						$dataPrestation = array(
							'Prestation' => $data['Prestation']
						);
						$dataPrestation['Prestation']['personne_id'] = $this->Personne->id;
						$this->Personne->Prestation->create( $dataPrestation );
						$saved = $this->Personne->Prestation->save( null, array( 'atomic' => false ) ) && $saved;

					}

					// Utilisateur
					$user = $this->User->find(
						'first',
						array(
							'conditions' => array(
								'User.id' => $this->Session->read( 'Auth.User.id' )
							),
							'recursive' => -1
						)
					);
					$this->assert( !empty( $user ), 'error500' );

					if( !empty( $data['Serviceinstructeur']['id'] ) ) {
						// Service instructeur
						$service = ClassRegistry::init( 'Serviceinstructeur' )->find(
							'first',
							array(
								'conditions' => array(
									'Serviceinstructeur.id' => $data['Serviceinstructeur']['id']
								),
								'recursive' => -1
							)
						);
						$this->assert( !empty( $service ), 'error500' );


						$suiviinstruction = array(
							'Suiviinstruction' => array(
								'dossier_id'           => $this->Dossier->id,
								'suiirsa'                  => '01',
								'date_etat_instruction'    => strftime( '%Y-%m-%d' ),
								'nomins'                   => $user['User']['nom'],
								'prenomins'                => $user['User']['prenom'],
								'numdepins'                => $service['Serviceinstructeur']['numdepins'],
								'typeserins'               => $service['Serviceinstructeur']['typeserins'],
								'numcomins'                => $service['Serviceinstructeur']['numcomins'],
								'numagrins'                => $service['Serviceinstructeur']['numagrins']
							)
						);
						$this->Dossier->Suiviinstruction->set( $suiviinstruction );

						$validate = $this->Dossier->Suiviinstruction->validates();

						if( $validate ) {
							$saved = $this->Dossier->Suiviinstruction->save( $suiviinstruction , array( 'atomic' => false ) ) && $saved;
						}
					}

					// Fin de la transaction
					if( $saved ) {
						$this->Dossier->commit();
						$this->redirect( array('controller'=>'dossiers', 'action'=>'view', $this->Dossier->id ) );
					}
					// Annulation de la transaction
					else {
						$this->Flash->error( __( 'Save->error' ) );
					}
				}
			}
			$this->_setOptions();
        }
	}
?>