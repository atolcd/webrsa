<?php
	/**
	 * Code source de la classe WebrsaActioncandidatPersonne.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractLogic', 'Model');
	App::uses('WebrsaLogicAccessInterface', 'Model/Interface');

	/**
	 * La classe WebrsaActioncandidatPersonne possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaActioncandidatPersonne extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaActioncandidatPersonne';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('ActioncandidatPersonne');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 * 
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$query['fields'][] = 'ActioncandidatPersonne.positionfiche';
			
			return $query;
		}
		
		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 * 
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$query = array(
				'fields' => array(
					'ActioncandidatPersonne.id',
					'ActioncandidatPersonne.personne_id',
				),
				'conditions' => $conditions,
				'contain' => false
			);
			$results = $this->ActioncandidatPersonne->find('all', $this->completeVirtualFieldsForAccess($query));
			
			return $results;
		}
		
		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 * 
		 * @see WebrsaAccess::getParamsList
		 * @param integer $personne_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($personne_id, array $params = array()) {
			$results = array();
			
			if (in_array('ajoutPossible', $params)) {
				$results['ajoutPossible'] = $this->ajoutPossible($personne_id);
			}
			
			return $results;
		}
		
		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 * 
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function ajoutPossible($personne_id) {
			$query = array(
				'fields' => 'Personne.id',
				'joins' => array(
					// Doit avoir une orientation
					$this->ActioncandidatPersonne->Personne->join('Orientstruct', array('type' => 'INNER')),
					// Doit avoir un référent actif
					$this->ActioncandidatPersonne->Personne->join('PersonneReferent', array('type' => 'INNER')),
				),
				'contain' => false,
				'conditions' => array(
					'Personne.id' => $personne_id,
					'PersonneReferent.dfdesignation IS NULL',
				)
			);
			$result = $this->ActioncandidatPersonne->Personne->find('first', $query);
			
			return !empty($result);
		}
		
		/**
		 * Venu
		 * 	Retenu
		 * 		Pas de sortie ?
		 * 			-> Position: en cours
		 * 			-> nullify: date, motifsortie
		 * 		Sinon ?
		 * 			-> sauve tout
		 * 			-> position: sortie
		 * 	Non retenu
		 * 		-> Position: Non retenu
		 * 		-> nullify: sortie, date, motifdemande
		 * Non venu
		 * 	bilanretenu: non retenu
		 * 	position: nonretenue
		 * 	nullify: sortie, date, motifdemande
		 */
		public function bilanAccueil( $data ) {
			$bilanvenu = Set::classicExtract( $data, "{$this->ActioncandidatPersonne->alias}.bilanvenu" );
			$bilanretenu = Set::classicExtract( $data, "{$this->ActioncandidatPersonne->alias}.bilanretenu" );
			$issortie = Set::classicExtract( $data, "{$this->ActioncandidatPersonne->alias}.issortie" );

			if( empty( $bilanvenu ) ) {
				$data[$this->ActioncandidatPersonne->alias]['positionfiche'] = 'enattente';
				$data[$this->ActioncandidatPersonne->alias]['bilanretenu'] = null;
				$data[$this->ActioncandidatPersonne->alias]['issortie'] = null;
				$data[$this->ActioncandidatPersonne->alias]['sortiele'] = null;
				$data[$this->ActioncandidatPersonne->alias]['motifsortie_id'] = null;
			}
			else {
				if( $bilanvenu == 'VEN' ) {
					if( $bilanretenu == 'RET' ) {
						if( !$issortie ) {
							$data[$this->ActioncandidatPersonne->alias]['positionfiche'] = 'encours';
							$data[$this->ActioncandidatPersonne->alias]['sortiele'] = null;
							$data[$this->ActioncandidatPersonne->alias]['motifsortie_id'] = null;
						}
						else {
							$data[$this->ActioncandidatPersonne->alias]['positionfiche'] = 'sortie';
						}
					}
					else if( $bilanretenu == 'NRE' ) {
						$data[$this->ActioncandidatPersonne->alias]['positionfiche'] = 'nonretenue';
						$data[$this->ActioncandidatPersonne->alias]['issortie'] = null;
						$data[$this->ActioncandidatPersonne->alias]['sortiele'] = null;
						$data[$this->ActioncandidatPersonne->alias]['motifsortie_id'] = null;
					}
				}
				else if( $bilanvenu == 'NVE' ) {
					$data[$this->ActioncandidatPersonne->alias]['bilanretenu'] = 'NRE';
					$data[$this->ActioncandidatPersonne->alias]['positionfiche'] = 'nonretenue';
					$data[$this->ActioncandidatPersonne->alias]['issortie'] = null;
					$data[$this->ActioncandidatPersonne->alias]['sortiele'] = null;
					$data[$this->ActioncandidatPersonne->alias]['motifsortie_id'] = null;
				}
			}

			return $data;
		}

		/**
		 * Retourne les options à utiliser pour une fiche de candidature, que ce
		 * soit pour l'affichage, le formulaire ou l'impression.
		 *
		 * @return array
		 */
		public function getFichecandidatureOptions() {
			$Option = ClassRegistry::init( 'Option' );

			$options = array(
				'Chargeinsertion' => array(
					'qual' => $Option->qual()
				),
				'Contactpartenaire' => array(
					'qual' => $Option->qual()
				),
				'Partenaire' => array(
					'typevoie' => $Option->typevoie()
				),
				'Personne' => array(
					'qual' => $Option->qual()
				),
				'Prestation' => array(
					'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers')
				),
				'Referent' => array(
					'qual' => $Option->qual()
				),
				'Structurereferente' => array(
					'type_voie' => $Option->typevoie()
				),
				'StructureChargeinsertion' => array(
					'type_voie' => $Option->typevoie()
				),
				'type' => array(
					'voie' => $Option->typevoie()
				),
			);

			$options = Hash::insert( $options, 'ActioncandidatPersonne.naturemobile', $this->ActioncandidatPersonne->Personne->Dsp->Detailnatmob->enum( 'natmob' ) );

			$options = Hash::merge( $options, $this->ActioncandidatPersonne->Personne->Contratinsertion->enums() );
			$options = Hash::merge( $options, $this->ActioncandidatPersonne->Personne->Contratinsertion->Cer93->enums() );
			$options = Hash::merge( $options, $this->ActioncandidatPersonne->enums() );

			return $options;
		}

		/**
		 * Retourne les données d'une fiche de candidataure, que ce soit pour
		 * l'affichage, le formulaire ou l'impression.
		 *
		 * @param integer $actioncandidat_personne_id
		 * @return array
		 */
		public function getFichecandidatureData( $actioncandidat_personne_id ) {
			$sqDernierChargeinsertion = $this->ActioncandidatPersonne->Personne->PersonneReferent->sqDerniere( 'Personne.id', false );
			$sqDernierContratinsertion = $this->ActioncandidatPersonne->Personne->sqLatest( 'Contratinsertion', 'dd_ci', array(), true );
			$sqDernierAdressefoyer = $this->ActioncandidatPersonne->Personne->Foyer->sqLatest( 'Adressefoyer', 'dtemm', array( 'Adressefoyer.rgadr' => '01' ), true );

			$replacements = array(
				'PersonneReferent' => 'PersonneChargeinsertion',
				'Referent' => 'Chargeinsertion',
				'Structurereferente' => 'StructureChargeinsertion',
			);

			$querydata = array(
				'fields' => array_merge(
					$this->ActioncandidatPersonne->fields(),
					$this->ActioncandidatPersonne->Actioncandidat->fields(),
					$this->ActioncandidatPersonne->Actioncandidat->Contactpartenaire->fields(),
					$this->ActioncandidatPersonne->Actioncandidat->Contactpartenaire->Partenaire->fields(),
					$this->ActioncandidatPersonne->Actioncandidat->Contratinsertion->fields(),
					$this->ActioncandidatPersonne->Actioncandidat->Contratinsertion->Cer93->fields(),
					$this->ActioncandidatPersonne->Motifsortie->fields(),
					$this->ActioncandidatPersonne->Personne->fields(),
					$this->ActioncandidatPersonne->Personne->Activite->fields(),
					$this->ActioncandidatPersonne->Personne->Foyer->fields(),
					$this->ActioncandidatPersonne->Personne->Foyer->Adressefoyer->fields(),
					$this->ActioncandidatPersonne->Personne->Foyer->Adressefoyer->Adresse->fields(),
					$this->ActioncandidatPersonne->Personne->Foyer->Dossier->fields(),
					$this->ActioncandidatPersonne->Personne->Foyer->Dossier->Detaildroitrsa->fields(),
					$this->ActioncandidatPersonne->Personne->Foyer->Dossier->Suiviinstruction->fields(),
					$this->ActioncandidatPersonne->Personne->Prestation->fields(),
					$this->ActioncandidatPersonne->Referent->fields(),
					array(
						$this->ActioncandidatPersonne->Referent->sqVirtualField( 'nom_complet' ),
					),
					$this->ActioncandidatPersonne->Referent->Structurereferente->fields(),
					$this->ActioncandidatPersonne->Personne->Prestation->fields(),
					array_words_replace(
						$this->ActioncandidatPersonne->Personne->PersonneReferent->fields(),
						$replacements
					),
					array_words_replace(
						array_merge(
							$this->ActioncandidatPersonne->Personne->PersonneReferent->Referent->fields(),
							array(
								str_replace( 'Referent__', 'Chargeinsertion__', $this->ActioncandidatPersonne->Referent->sqVirtualField( 'nom_complet' ) ),
							)
						),
						$replacements
					),
					array_words_replace(
						$this->ActioncandidatPersonne->Personne->PersonneReferent->Referent->Structurereferente->fields(),
						$replacements
					),
					array(
						$this->ActioncandidatPersonne->Fichiermodule->sqNbFichiersLies( $this->ActioncandidatPersonne, 'nb_fichiers_lies' ),
					),
					$this->ActioncandidatPersonne->Personne->Foyer->Dossier->Detaildroitrsa->Detailcalculdroitrsa->vfsSummary()
				),
				'joins' => array(
					$this->ActioncandidatPersonne->join( 'Actioncandidat', array( 'type' => 'INNER' ) ),
					$this->ActioncandidatPersonne->Actioncandidat->join( 'Contactpartenaire', array( 'type' => 'LEFT OUTER' ) ),
					$this->ActioncandidatPersonne->Actioncandidat->Contactpartenaire->join( 'Partenaire', array( 'type' => 'LEFT OUTER' ) ),
					$this->ActioncandidatPersonne->join( 'Motifsortie', array( 'type' => 'LEFT OUTER' ) ),
					$this->ActioncandidatPersonne->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->ActioncandidatPersonne->join( 'Referent', array( 'type' => 'INNER' ) ),
					$this->ActioncandidatPersonne->Personne->join( 'Activite', array( 'type' => 'LEFT OUTER' ) ),
					$this->ActioncandidatPersonne->Personne->join( 'Contratinsertion', array( 'type' => 'LEFT OUTER' ) ),
					$this->ActioncandidatPersonne->Personne->Contratinsertion->join( 'Cer93', array( 'type' => 'LEFT OUTER' ) ),
					$this->ActioncandidatPersonne->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->ActioncandidatPersonne->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$this->ActioncandidatPersonne->Personne->Foyer->Dossier->join( 'Suiviinstruction', array( 'type' => 'LEFT OUTER' ) ),
					$this->ActioncandidatPersonne->Personne->Foyer->Dossier->join( 'Detaildroitrsa', array( 'type' => 'LEFT OUTER' ) ),
					$this->ActioncandidatPersonne->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->ActioncandidatPersonne->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$this->ActioncandidatPersonne->Personne->join( 'Prestation', array( 'type' => 'LEFT OUTER' ) ),
					$this->ActioncandidatPersonne->Referent->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					array_words_replace(
						$this->ActioncandidatPersonne->Personne->join(
							'PersonneReferent',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									"PersonneReferent.id IN ( {$sqDernierChargeinsertion} )"
								)
							)
						),
						$replacements
					),
					array_words_replace(
						$this->ActioncandidatPersonne->Personne->PersonneReferent->join( 'Referent', array( 'type' => 'INNER' ) ),
						$replacements
					),
					array_words_replace(
						$this->ActioncandidatPersonne->Personne->PersonneReferent->Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$replacements
					),
				),
				'conditions' => array(
					'ActioncandidatPersonne.id' => $actioncandidat_personne_id,
					$sqDernierContratinsertion,
					$sqDernierAdressefoyer
				),
				'recursive' => -1
			);
			$actioncandidat_personne = $this->ActioncandidatPersonne->find( 'first', $querydata );

			if( !empty( $actioncandidat_personne ) ) {
				$fichiersmodules = (array)$this->ActioncandidatPersonne->Fichiermodule->find(
					'all',
					array(
						'fields' => array(
							'Fichiermodule.id',
							'Fichiermodule.name',
							'Fichiermodule.created',
						),
						'conditions' => array(
							'Fichiermodule.modele' => $this->ActioncandidatPersonne->alias,
							'Fichiermodule.fk_value' => $actioncandidat_personne_id,
						),
						'contain' => false
					)
				);
				$fichiersmodules = array( 'Fichiermodule' => Hash::extract( $fichiersmodules, '{n}.Fichiermodule' ) );
				$actioncandidat_personne = Hash::merge( $actioncandidat_personne, $fichiersmodules );
				unset( $actioncandidat_personne['Fichiermodule']['nb_fichiers_lies'] );

				// TODO: virtual field
				if( $actioncandidat_personne['Activite']['act'] === 'ANP' ) {
					$actioncandidat_personne['Activite']['inscritpe'] = true;
				}
				else {
					$actioncandidat_personne['Activite']['inscritpe'] = false;
				}
			}

			return $actioncandidat_personne;
		}

		/**
		*
		*/

		public function getPdfFiche( $actioncandidat_personne_id ) {
			// TODO: scinder dans les sous-méthodes + nettoyer pour le 66
			if( Configure::read( 'ActioncandidatPersonne.suffixe' ) == 'cg93' ) {
				$actioncandidat = $this->getFichecandidatureData( $actioncandidat_personne_id );
				$options = $this->getFichecandidatureOptions();
			}
			else {
				$queryData = array(
					'fields' => array_merge(
						$this->ActioncandidatPersonne->fields(),
						$this->ActioncandidatPersonne->Actioncandidat->fields(),
						$this->ActioncandidatPersonne->Actioncandidat->Contactpartenaire->fields(),
						$this->ActioncandidatPersonne->Actioncandidat->Contactpartenaire->Partenaire->fields(),
						$this->ActioncandidatPersonne->Personne->fields(),
						$this->ActioncandidatPersonne->Referent->fields(),
						$this->ActioncandidatPersonne->Referent->Structurereferente->fields(),
						$this->ActioncandidatPersonne->Personne->Foyer->fields(),
						$this->ActioncandidatPersonne->Personne->Foyer->Dossier->fields(),
						$this->ActioncandidatPersonne->Personne->Foyer->Adressefoyer->fields(),
						$this->ActioncandidatPersonne->Personne->Foyer->Adressefoyer->Adresse->fields(),
                        $this->ActioncandidatPersonne->Progfichecandidature66->fields()
					),
					'joins' => array(
						array(
							'table'      => 'actionscandidats',
							'alias'      => 'Actioncandidat',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'Actioncandidat.id = ActioncandidatPersonne.actioncandidat_id'
							),
						),
						array(
							'table'      => 'contactspartenaires',
							'alias'      => 'Contactpartenaire',
							'type'       => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array( 'Contactpartenaire.id = Actioncandidat.contactpartenaire_id' ),
						),
						array(
							'table'      => 'partenaires',
							'alias'      => 'Partenaire',
							'type'       => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array( 'Partenaire.id = Contactpartenaire.partenaire_id' ),
						),
						array(
							'table'      => 'personnes',
							'alias'      => 'Personne',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( "ActioncandidatPersonne.personne_id = Personne.id" ),
						),
						array(
							'table'      => 'referents',
							'alias'      => 'Referent',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Referent.id = ActioncandidatPersonne.referent_id' ),
						),
						$this->ActioncandidatPersonne->Referent->join('Structurereferente'),
						array(
							'table'      => 'foyers',
							'alias'      => 'Foyer',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Personne.foyer_id = Foyer.id' )
						),
						array(
							'table'      => 'dossiers',
							'alias'      => 'Dossier',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Dossier.id = Foyer.dossier_id' )
						),
						array(
							'table'      => 'adressesfoyers',
							'alias'      => 'Adressefoyer',
							'type'       => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array(
								'Foyer.id = Adressefoyer.foyer_id',
								'Adressefoyer.id IN (
									'.ClassRegistry::init( 'Adressefoyer' )->sqDerniereRgadr01('Adressefoyer.foyer_id').'
								)'
							)
						),
						array(
							'table'      => 'adresses',
							'alias'      => 'Adresse',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array( 'Adresse.id = Adressefoyer.adresse_id' )
						),
                        array(
							'table'      => 'progsfichescandidatures66',
							'alias'      => 'Progfichecandidature66',
							'type'       => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array( "Progfichecandidature66.id = ActioncandidatPersonne.progfichecandidature66_id" ),
						),
					),
					'conditions' => array(
						'ActioncandidatPersonne.id' => $actioncandidat_personne_id
					),
					'recursive' => -1
				);

				$options = array( 'Personne' => array( 'qual' => ClassRegistry::init( 'Option' )->qual() ) );
				$options = Hash::insert( $options, 'ActioncandidatPersonne.naturemobile', $this->ActioncandidatPersonne->Personne->Dsp->Detailnatmob->enum( 'natmob' ) );


				$options = Set::merge( $options, $this->ActioncandidatPersonne->enums() );


				$actioncandidat = $this->ActioncandidatPersonne->find( 'first', $queryData );
				$referents = $this->ActioncandidatPersonne->Referent->find( 'list' );
				$motifssortie = ClassRegistry::init( 'Motifsortie' )->find( 'list' );

				$correspondantaction = Set::classicExtract( $actioncandidat, 'Actioncandidat.correspondantaction' );

				if( !empty( $correspondantaction ) ){
					$actioncandidat['Actioncandidat']['correspondantaction_nom_complet'] = Set::enum( $actioncandidat['Actioncandidat']['referent_id'],  $referents );
				}
				$actioncandidat['Actioncandidat']['codeaction'] = Set::classicExtract( $actioncandidat, 'Actioncandidat.themecode' ).' '. Set::classicExtract( $actioncandidat, 'Actioncandidat.codefamille' ).' '.Set::classicExtract( $actioncandidat, 'Actioncandidat.numcodefamille' );



				$actioncandidat['ActioncandidatPersonne']['motifsortie_id'] = Set::enum( Set::classicExtract( $actioncandidat, 'ActioncandidatPersonne.motifsortie_id' ), $motifssortie );
			}

			// Récupération des dernières informations Pôle Emploi
			$Informationpe = ClassRegistry::init( 'Informationpe' );
			$derniereInformationPe = $Informationpe->derniereInformation( $actioncandidat );
			$derniereInformationPe = (array)Hash::get( $derniereInformationPe, 'Historiqueetatpe.0' );
			if( empty( $derniereInformationPe ) ) {
				$derniereInformationPe = Hash::normalize( array_keys( $Informationpe->Historiqueetatpe->schema() ) );
			}
			$actioncandidat = Hash::merge( $actioncandidat, array( 'Historiqueetatpe' => $derniereInformationPe ) );

			$options = Hash::merge(
				$options,
				$Informationpe->Historiqueetatpe->enums(), // Informationpe.etat
				array(
					'Historiqueetatpe' => array(
						'code' => ClassRegistry::init('Historiqueetatpe')->enum('code')
					)
				)
			);

			$modeleodt = Hash::get( $actioncandidat, 'Actioncandidat.modele_document' );
			return $this->ActioncandidatPersonne->ged( array( $actioncandidat ), "Candidature/{$modeleodt}.odt", true, $options );
		}

		/**
		 * Retourne la liste des modèles odt paramétrés pour le impressions de
		 * cette classe.
		 *
		 * @return array
		 */
		public function modelesOdt() {
			$prefix = 'Candidature'.DS;

			$items = $this->ActioncandidatPersonne->Actioncandidat->find(
				'all',
				array(
					'fields' => array(
						'( \''.$prefix.'\' || "'.$this->ActioncandidatPersonne->Actioncandidat->alias.'"."modele_document" || \'.odt\' ) AS "'.$this->ActioncandidatPersonne->Actioncandidat->alias.'__modele"',
					),
					'recursive' => -1
				)
			);
			return Set::extract( $items, '/'.$this->ActioncandidatPersonne->Actioncandidat->alias.'/modele' );
		}
	}