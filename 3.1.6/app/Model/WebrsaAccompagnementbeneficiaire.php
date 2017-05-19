<?php
	/**
	 * Code source de la classe WebrsaAccompagnementbeneficiaire.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'DefaultUrl', 'Default.Utility' );
	App::uses( 'WebrsaLogicAccessInterface', 'Model/Interface' );
	App::uses( 'WebrsaPermissions', 'Utility' );

	/**
	 * La classe WebrsaAccompagnementbeneficiaire ...
	 *
	 * @package app.Model
	 */
	class WebrsaAccompagnementbeneficiaire extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaAccompagnementbeneficiaire';

		/**
		 * Ce modèle ne possède pas de table liée.
		 *
		 * @var bool
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array();

		/**
		 * Modèles utilisés par ce modéle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Informationpe',
			'Option', // TODO: à mettre dans les modèles concernés, à déprécier dans Option
			'Pdf',
			'Personne'
		);

		/**
		 * Le nom des méthodes à précharger.
		 *
		 * @var array
		 */
		public $prechargementMethods  =array(
			'qdDetails',
			'_configActions',
			'_configFichiersmodules',
			'_configImpressions',
			'options'
		);

		/**
		 * Retourne le querydata contenant les détails du bénéficiaire.
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function qdDetails( array $conditions = array() ) {
			$cacheKey = $this->useDbConfig.'_'.$this->alias.'_'.__FUNCTION__;
			$query = Cache::read( $cacheKey );

			if( false === $query ) {
				$query = array(
					'fields' => array(
						// -----------------------------------------------------
						// 0. Titre de la page
						'Personne.qual',
						'Personne.nom',
						'INITCAP( "Personne"."prenom" ) AS "Personne__prenom"',
						// I. Bloc "Droits"
						// Date de demande RMI
						'Dossier.dtdemrmi',
						// Date de demande RSA
						'Dossier.dtdemrsa',
						// Nature de prestations
						$this->Personne->Foyer->Dossier->Detaildroitrsa->vfNatpf().' AS "Detaildroitrsa__natpf"',
						//Etat du droit
						'Situationdossierrsa.etatdosrsa',
						// CMU: Oui/Non/Inconnu
						'Cer93.cmu',
						// CMUC : Oui/Non/Inconnu
						'Cer93.cmuc',
						// Pôle Emploi
						'Historiqueetatpe.identifiantpe',
						// Soumis à Droits et Devoirs
						'Calculdroitrsa.toppersdrodevorsa',
						// Orientation
						'Typeorient.lib_type_orient',
						'Structurereferente.lib_struc',
						// -----------------------------------------------------
						// II. Bloc "Compétences"
						// Niveau de formation -> CER date de validation du CER; DSP date de création; D1 date de création, DSP
						// INFO: suivant le champ, on n'a pas les memes valeurs d'ENUM
						'CASE
							WHEN
								"Cer93"."nivetu" IS NOT NULL
								AND ( "DspRev"."nivetu" IS NULL OR "Contratinsertion"."datedecision" >= "DspRev"."modified"::DATE )
								AND ( "Questionnaired1pdv93"."nivetu" IS NULL OR "Contratinsertion"."datedecision" >= "Questionnaired1pdv93"."created"::DATE )
								THEN "Cer93"."nivetu"
							ELSE NULL
						END AS "Cer93__nivetu"',
						'CASE
							WHEN
								"DspRev"."nivetu" IS NOT NULL
								AND ( "Cer93"."nivetu" IS NULL OR "Contratinsertion"."datedecision" < "DspRev"."modified"::DATE )
								AND ( "Questionnaired1pdv93"."nivetu" IS NULL OR "DspRev"."modified"::DATE >= "Questionnaired1pdv93"."created"::DATE )
								THEN "DspRev"."nivetu"
							ELSE NULL
						END AS "DspRev__nivetu"',
						'CASE
							WHEN
								"Questionnaired1pdv93"."nivetu" IS NOT NULL
								AND ( "Cer93"."nivetu" IS NULL OR "Contratinsertion"."datedecision" < "Questionnaired1pdv93"."created"::DATE )
								AND ( "DspRev"."nivetu" IS NULL OR "DspRev"."modified"::DATE < "Questionnaired1pdv93"."created"::DATE )
								THEN "Questionnaired1pdv93"."nivetu"
							ELSE NULL
						END AS "Questionnaired1pdv93__nivetu"',
						'CASE
							WHEN
								"Cer93"."nivetu" IS NULL
								AND "DspRev"."nivetu" IS NULL
								AND "Questionnaired1pdv93"."nivetu" IS NULL
								THEN "Dsp"."nivetu"
							ELSE NULL
						END AS "Dsp__nivetu"',
						// -----------------------------------------------------
						// Diplôme  (Dernier diplôme à partir du dernier CER validé)
						'Diplomecer93.name',
						// Emploi recherché: Appelation métier code rome V3 du champs "Votre contrat porte sur l'emploi" du dernier CER validé
						'Appellationromev3.name',
						// Mobilité (correspondant à Disposez-vous d’un moyen de transport collectif ou individuel à partir de la dernière MAJ DSP)
						'( CASE WHEN "DspRev"."id" IS NOT NULL THEN "DspRev"."topmoyloco" = \'1\' WHEN "Dsp"."id" IS NOT NULL THEN "Dsp"."topmoyloco" = \'1\' ELSE NULL END ) AS "Accompagnement__topmoyloco"',
						// Permis (Permis B ou autre à partir de la DSP)
						'( CASE
							WHEN "DspRev"."id" IS NOT NULL THEN (
								ARRAY_TO_STRING( ARRAY[
									( CASE WHEN "DspRev"."toppermicondub" = \'1\' THEN \'Permis B\' ELSE NULL END ),
									( CASE WHEN "DspRev"."topautrpermicondu" = \'1\' AND "DspRev"."libautrpermicondu" IS NULL THEN \'Permis autre catégorie\' ELSE NULL END ),
									( CASE WHEN "DspRev"."topautrpermicondu" = \'1\' AND "DspRev"."libautrpermicondu" IS NOT NULL THEN \'Permis \' || "DspRev"."libautrpermicondu" ELSE NULL END )
								], \', \' )
							)
							WHEN "Dsp"."id" IS NOT NULL THEN (
								ARRAY_TO_STRING( ARRAY[
									( CASE WHEN "Dsp"."toppermicondub" = \'1\' THEN \'Permis B\' ELSE NULL END ),
									( CASE WHEN "Dsp"."topautrpermicondu" = \'1\' AND "Dsp"."libautrpermicondu" IS NULL THEN \'Permis autre catégorie\' ELSE NULL END ),
									( CASE WHEN "Dsp"."topautrpermicondu" = \'1\' AND "Dsp"."libautrpermicondu" IS NOT NULL THEN \'Permis \' || "Dsp"."libautrpermicondu" ELSE NULL END )
								], \', \' )
							)
							ELSE NULL
						END ) AS "Accompagnement__toppermicondu"',
						// -----------------------------------------------------
						// III. Bloc "Suivi"
						// Nom du référent de parcours (actuel)
						'Referent.nom_complet',
					),
					'joins' => array(
						$this->Personne->join( 'Calculdroitrsa', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->join(
							'Contratinsertion',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Contratinsertion.decision_ci' => 'V',
									'Contratinsertion.id IN ( '.$this->Personne->Contratinsertion->WebrsaContratinsertion->sqDernierContrat( 'Contratinsertion.personne_id', true ).' )',
								)
							)
						),
						$this->Personne->join(
							'Dsp',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Dsp.id IN ( '.$this->Personne->Dsp->WebrsaDsp->sqDerniereDsp( 'Dsp.personne_id' ).' )'
								)
							)
						),
						$this->Personne->join(
							'DspRev',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'DspRev.id IN ( '.$this->Personne->DspRev->sqDerniere( 'DspRev.personne_id' ).' )'
								)
							)
						),
						$this->Personne->join(
							'Orientstruct',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Orientstruct.id IN ( '.$this->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().' )'
								)
							)
						),
						$this->Personne->join(
							'Questionnaired1pdv93',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									$this->Personne->sqLatest( 'Questionnaired1pdv93', 'modified' ),
								)
							)
						),
						$this->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Personne->join(
							'PersonneReferent',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'PersonneReferent.id IN ( '.$this->Personne->PersonneReferent->sqDerniere( 'Personne.id', false ).' )'
								)
							)
						),
						$this->Personne->Contratinsertion->join( 'Cer93', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Contratinsertion->Cer93->join(
							'Diplomecer93',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Diplomecer93.id IN ( '.$this->Personne->Contratinsertion->Cer93->Diplomecer93->sqDernier().' )'
								)
							)
						),
						$this->Personne->Contratinsertion->Cer93->join( 'Sujetromev3', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Contratinsertion->Cer93->Sujetromev3->join( 'Appellationromev3', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Personne->Foyer->Dossier->join( 'Detaildroitrsa', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Foyer->Dossier->Detaildroitrsa->join( 'Detailcalculdroitrsa', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->PersonneReferent->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					),
					'conditions' => array(),
					'contain' => false
				);

				// Jointures spéciales concernant les derniers éléments transmis par Pôle Emploi
				// 1. Dernière information Pole Emploi
				$joinPersonneInformationpe = $this->Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', 'LEFT OUTER' );
				$joinPersonneInformationpe['conditions'][] = 'Informationpe.id IN ( '.$this->Informationpe->sqDerniere().' )';
				$query['joins'][] = $joinPersonneInformationpe;
				// 2. Dernier historique Pole Emploi
				$query['joins'][] = $this->Informationpe->join(
					'Historiqueetatpe',
					array(
						'type' => 'LEFT OUTER',
						'conditions' => array(
							'Historiqueetatpe.id IN ( '.$this->Informationpe->Historiqueetatpe->sqDernier().' )'
						)
					)
				);

				Cache::write( $cacheKey, $query );
			}

			$query['conditions'] = array_merge( $query['conditions'], $conditions );

			return $query;
		}

		/**
		 * Complète les résultats au moyen des classes de logique d'accès métier
		 * (voir les clés webrsaModelName et webrsaAccessName).
		 *
		 * @param array $query
		 * @return array
		 */
		protected function _completeConfigAccess( array $query ) {
			$query += array(
				'webrsaModelName' => null,
				'webrsaAccessName' => null
			);

			$webrsaModelName = ( null === $query['webrsaModelName'] )
				? 'Webrsa'.$query['modelName']
				: $query['webrsaModelName'];

			// INFO: c'est compliqué, mais sinon on n'a pas la bonne mise au pluriel de PersonneReferent
			$webrsaAccessName = ( null === $query['webrsaAccessName'] )
				? 'WebrsaAccess'.Inflector::camelize( Inflector::pluralize( Inflector::underscore( $query['modelName'] ) ) )
				: $query['webrsaAccessName'];

			App::uses( $webrsaModelName, 'Model' );
			$WebrsaModel = ClassRegistry::init( $webrsaModelName );

			if( $WebrsaModel instanceof WebrsaLogicAccessInterface ) {
				$query['webrsaModelName'] = $webrsaModelName;
				$query['webrsaAccessName'] = $webrsaAccessName;

				$query = $WebrsaModel->completeVirtualFieldsForAccess( $query );
			}
			else {
				$query['webrsaModelName'] = $query['webrsaAccessName'] = false;
			}

			return $query;
		}

		/**
		 * Lecture de la "configuration" pour le tableau d'actions.
		 *
		 * @return array
		 */
		protected function _readConfigActions() {
			$config = array(
				'Rendezvouscollectif' => array(
					'modelName' => 'Rendezvous',
					'fields' => array(
						'Rendezvous.id',
						'Rendezvous.daterdv',
						'Structurereferente.lib_struc',
						'Referent.nom_complet',
						'Statutrdv.libelle',
						'Rendezvous.thematiques_virgules',
						'Rendezvous.objetrdv',
						'Rendezvous.commentairerdv'
					),
					'joins' => array(
						'Structurereferente' => array( 'type' => 'INNER' ),
						'Referent' => array( 'type' => 'LEFT OUTER' ),
						'Statutrdv' => array( 'type' => 'INNER' )
					),
					'conditions' => array(
						'Rendezvous.typerdv_id' => Configure::read( 'Rendezvous.Typerdv.collectif_id' )
					)
				),
				'Rendezvousindividuel' => array(
					'modelName' => 'Rendezvous',
					'fields' => array(
						'Rendezvous.id',
						'Rendezvous.daterdv',
						'Structurereferente.lib_struc',
						'Referent.nom_complet',
						'Statutrdv.libelle',
						'Rendezvous.thematiques_virgules',
						'Rendezvous.objetrdv',
						'Rendezvous.commentairerdv'
					),
					'joins' => array(
						'Structurereferente' => array( 'type' => 'INNER' ),
						'Referent' => array( 'type' => 'LEFT OUTER' ),
						'Statutrdv' => array( 'type' => 'INNER' )
					),
					'conditions' => array(
						'Rendezvous.typerdv_id' => Configure::read( 'Rendezvous.Typerdv.individuel_id' )
					)
				),
				'Contratinsertion' => array(
					'webrsaModelName' => 'WebrsaCer93',
					'webrsaAccessName' => 'WebrsaAccessCers93',
					'fields' => array(
						'Contratinsertion.id',
						'Contratinsertion.created',
						'Structurereferente.lib_struc',
						'Referent.nom_complet',
						'Cer93.positioncer',
						'Contratinsertion.dd_ci',
						'Contratinsertion.df_ci',
						'Cer93.duree',
						'Cer93.sujets_virgules',
						'Cer93.prevu'
					),
					'joins' => array(
						'Structurereferente' => array( 'type' => 'INNER' ),
						'Referent' => array( 'type' => 'LEFT OUTER' ),
						'Cer93' => array( 'type' => 'INNER' )
					),
					'url' => '/Cers93/index'
				),
				'Ficheprescription93' => array(
					'fields' => array(
						'Ficheprescription93.id',
						'Ficheprescription93.created',
						'Structurereferente.lib_struc',
						'Referent.nom_complet',
						'Ficheprescription93.statut',
						'Categoriefp93.name',
						'Thematiquefp93.name',
						'Prestatairehorspdifp93.name',
						'Prestatairefp93.name',
					),
					'joins' => array(
						'Adresseprestatairefp93' => array(
							'type' => 'LEFT OUTER',
							'joins' => array(
								'Prestatairefp93' => array(
									'type' => 'LEFT OUTER',
								)
							)
						),
						'Filierefp93' => array(
							'type' => 'LEFT OUTER',
							'joins' => array(
								'Categoriefp93' => array(
									'type' => 'LEFT OUTER',
									'joins' => array(
										'Thematiquefp93' => array( 'type' => 'LEFT OUTER' )
									)
								),
							)
						),
						'Prestatairehorspdifp93' => array(
							'type' => 'LEFT OUTER'
						),
						'Referent' => array(
							'type' => 'INNER',
							'joins' => array(
								'Structurereferente' => array(
									'type' => 'INNER'
								)
							)
						),
					),
				),
				'Questionnaired1pdv93' => array(
					'fields' => array(
						'Questionnaired1pdv93.id',
						'Questionnaired1pdv93.created',
						'Structurereferente.lib_struc',
						'Referent.nom_complet'
					),
					'joins' => array(
						'Rendezvous' => array(
							'type' => 'INNER',
							'joins' => array(
								'Structurereferente' => array( 'type' => 'INNER' ),
								'Referent' => array( 'type' => 'LEFT OUTER' )
							)
						)
					),
				),
				'Questionnaired2pdv93' => array(
					'fields' => array(
						'Questionnaired2pdv93.id',
						'Questionnaired2pdv93.created',
						'Structurereferente.lib_struc',
						'Referent.nom_complet',
						'Questionnaired2pdv93.situationaccompagnement',
					),
					'joins' => array(
						'Questionnaired1pdv93' => array(
							'type' => 'INNER',
							'joins' => array(
								'Rendezvous' => array(
									'type' => 'INNER',
									'joins' => array(
										'Structurereferente' => array( 'type' => 'INNER' ),
										'Referent' => array( 'type' => 'LEFT OUTER' )
									)
								)
							)
						)
					),
				),
				'DspRev' => array(
					'fields' => array(
						'DspRev.id',
						'DspRev.personne_id',
						'DspRev.created'
					),
					'url' => '/Dsps/histo'
				),
				'Entretien' => array(
					'fields' => array(
						'Entretien.id',
						'Entretien.dateentretien',
						'Structurereferente.lib_struc',
						'Referent.nom_complet',
						'Objetentretien.name',
					),
					'joins' => array(
						'Objetentretien' => array( 'type' => 'INNER' ),
						'Structurereferente' => array( 'type' => 'INNER' ),
						'Referent' => array( 'type' => 'LEFT OUTER' )
					)
				)
			);

			return $config;
		}

		/**
		 * Retourne les queries normalisées utilisées pour le tableau "Actions".
		 *
		 * @return array
		 */
		protected function _configActions() {
			$cacheKey = $this->useDbConfig.'_'.$this->alias.'_'.__FUNCTION__;
			$config = Cache::read( $cacheKey );

			if( false === $config ) {
				$config = $this->_readConfigActions();

				foreach( $config as $alias => $query ) {
					$query = (array)$query;

					$query['modelName'] = isset( $query['modelName'] ) ? $query['modelName'] : $alias;

					// Jointures
					$joins = (array)Hash::get( $query, 'joins' );
					if( !empty( $joins ) ) {
						$joins = $this->Personne->{$query['modelName']}->joins( $joins );
					}
					$query['joins'] = $joins;

					$query['contain'] = false;

					$query['url'] = isset( $query['url'] )
						? $query['url']
						: '/'.Inflector::camelize( Inflector::tableize( $query['modelName'] ) ).'/index';

					$query = $this->_completeConfigAccess( $query );

					$config[$alias] = $query;
				}

				Cache::write( $cacheKey, $config );
			}

			return $config;
		}

		/**
		 * Retourne les résultats complétés avec les accès des différentes actions
		 * en fonction du module concerné.
		 *
		 * @param array $results Les résultats à compléter
		 * @param integer $personne_id L'id du bénéficiaire auquel les résultats
		 *	appartiennent
		 * @param array $params
		 *	- webrsaModelName Le nom du modèle contenant la
		 *  logique métier permettant de calculer les accès (implémentant
		 *	WebrsaLogicAccessInterface).
		 *	- webrsaAccessName Le nom de la classe utilitaire
		 *  permettant de calculer les accès (implémentant WebrsaAccessInterface).
		 * @return array
		 */
		protected function _computeAccesses( array $results, $personne_id, array $params = array() ) {
			$webrsaModelName = Hash::get( $params, 'webrsaModelName' );
			$webrsaAccessName = Hash::get( $params, 'webrsaAccessName' );

			if( !empty( $webrsaModelName ) && !empty( $webrsaAccessName ) ) {
				$WebrsaModel = ClassRegistry::init( $webrsaModelName );
				App::uses( $webrsaAccessName, 'Utility' );

				$paramsActions = call_user_func( array( $webrsaAccessName, 'getParamsList' ) );
				$paramsAccess = $WebrsaModel->getParamsForAccess( $personne_id, $paramsActions + $params );

				$results = call_user_func(
					array( $webrsaAccessName, 'accesses' ),
					$results,
					$paramsAccess
				);
			}

			return $results;
		}

		/**
		 * Conditions supplémentaires pour les enregistrements qui ne doivent
		 * pas être partagés entre structures référentes.
		 *	- RDV et entretiens
		 *	- D1 et D2 (liés à un RDV)
		 *
		 * @param string $modelName
		 * @param array $query
		 * @param array $structuresreferentes_ids
		 * @return array
		 */
		public function completeQueryProtectedRecords( $modelName, array $query, array $structuresreferentes_ids ) {
			if( !empty( $structuresreferentes_ids ) ) {
				if( in_array( $modelName, array( 'Rendezvous', 'Entretien' ) ) ) {
					$query['conditions'][] = array( "{$modelName}.structurereferente_id" => $structuresreferentes_ids );
				}
				elseif( in_array( $modelName, array( 'Questionnaired1pdv93', 'Questionnaired2pdv93' ) ) ) {
					$query['conditions'][] = array( 'Rendezvous.structurereferente_id' => $structuresreferentes_ids );
				}
			}

			return $query;
		}

		/**
		 * Récupère la liste des actions liées au bénéficiaire.
		 *
		 * @param integer $personne_id L'id du bénéficiaire
		 * @return array
		 */
		public function actions( $personne_id, array $params = array() ) {
			$structuresreferentes_ids = (array)Hash::get( $params, 'structurereferente_id' );
			unset( $params['structurereferente_id'] );

			$config = Hash::normalize( $this->_configActions() );
			$results = array();

			foreach( $config as $alias => $query ) {
				$modelName = $query['modelName'];
				$webrsaModelName = $query['webrsaModelName'];
				$webrsaAccessName = $query['webrsaAccessName'];
				$url = $query['url'];
				unset( $query['modelName'], $query['webrsaModelName'], $query['webrsaAccessName'], $query['url'] );

				$url = DefaultUrl::toArray($url);
				if( true === WebrsaPermissions::check( $url['controller'], $url['action'] ) ) {
					// Conditions
					$query['conditions'][] = array( "{$modelName}.personne_id" => $personne_id );

					$query = $this->completeQueryProtectedRecords( $modelName, $query, $structuresreferentes_ids );

					// Conditions supplémentaires pour savoir si l'utilisateur est hors zone ou pas lorsque c'est possible
					if( false === $this->Personne->{$modelName}->Behaviors->attached( 'WebrsaStructurereferenteliee' ) ) {
						$this->Personne->{$modelName}->Behaviors->attach( 'WebrsaStructurereferenteliee' );
					}

					$query = $this->Personne->{$modelName}->completeQueryHorsZone(
						$query,
						$structuresreferentes_ids,
						$this->Personne->{$modelName}->links()
					);

					$this->Personne->{$modelName}->forceVirtualFields = true;
					$records = $this->Personne->{$modelName}->find( 'all', $query );

					$records = $this->_computeAccesses( $records, $personne_id, $params + compact( 'webrsaModelName', 'webrsaAccessName' ) );

					$results = array_merge(
						$results,
						Hash::insert( $records, '{n}.Action.name', $alias )
					);
				}
			}

			return $results;
		}

		/**
		 * Lecture de la "configuration" pour le tableau de fichiers liés.
		 *
		 * @return array
		 */
		protected function _readConfigFichiersmodules() {
			$config = array(
				'Apre',
				'Contratinsertion' => array(
					'modelName' => 'Contratinsertion',
					'webrsaModelName' => 'WebrsaCer93',
					'webrsaAccessName' => 'WebrsaAccessCers93',
					'joins' => array(
						'Cer93'
					),
					'controller' => 'cers93',
					'url' => '/Cers93/index'
				),
				'DspRev' => array(
					'controller' => 'dsps',
					'view_action' => 'view_revs',
					'url' => '/Dsps/histo'
				),
				'Entretien',
				'Memo',
				'Orientstruct',
				'Personne',
				'PersonneReferent',
				'Rendezvous',
			);

			return $config;
		}

		/**
		 * Retourne les queries normalisées utilisées pour le tableau "Fichiers
		 * liés".
		 *
		 * @return array
		 */
		protected function _configFichiersmodules() {
			$cacheKey = $this->useDbConfig.'_'.$this->alias.'_'.__FUNCTION__;
			$config = Cache::read( $cacheKey );

			if( false === $config ) {
				$fichierModuleFields = $this->Personne->Fichiermodule->fields();
				array_remove( $fichierModuleFields, 'Fichiermodule.document' );
				array_remove( $fichierModuleFields, 'Fichiermodule.cmspath' );

				$config = Hash::normalize( $this->_readConfigFichiersmodules() );
				foreach( $config as $alias => $query ) {
					$query = (array)$query;

					$query['modelName'] = isset( $query['modelName'] ) ? $query['modelName'] : $alias;

					// Contrôleur
					$query['controller'] = isset( $query['controller'] ) ? $query['controller'] : Inflector::tableize( $query['modelName'] );
					// Action
					$query['view_action'] = isset( $query['view_action'] ) ? $query['view_action'] : 'view';

					// Champs
					$fields = (array)Hash::get( $query, 'fields' );
					$fields = array_merge( $fichierModuleFields, $fields );
					$query['fields'] = $fields;

					// Jointures
					$joins = (array)Hash::get( $query, 'joins' );

					if( 'Personne' !== $query['modelName'] ) {
						$joinFichierModule = $this->Personne->{$query['modelName']}->join( 'Fichiermodule', array( 'type' => 'INNER' ) );
						$primaryKey = $this->Personne->{$query['modelName']}->primaryKey;
						if( !empty( $joins ) ) {
							$joins = $this->Personne->{$query['modelName']}->joins( $joins );
						}
					}
					else {
						$joinFichierModule = $this->Personne->join( 'Fichiermodule', array( 'type' => 'INNER' ) );
						$primaryKey = $this->Personne->primaryKey;
						if( !empty( $joins ) ) {
							$joins = $this->Personne->joins( $joins );
						}
					}

					$joinFichierModule['conditions'] = str_replace(
						'{$__cakeID__$}',
						"\"{$alias}\".\"{$primaryKey}\"",
						$joinFichierModule['conditions']
					);

					$query['joins'] = array_merge( array( $joinFichierModule ), $joins );

					$query['contain'] = false;

					$query['url'] = isset( $query['url'] )
						? $query['url']
						: '/'.Inflector::camelize( Inflector::tableize( $query['modelName'] ) ).'/index';

					$query = $this->_completeConfigAccess( $query );

					$config[$alias] = $query;
				}

				Configure::write( $cacheKey, $config );
			}

			return $config;
		}

		/**
		 * Récupère la liste des fichiers liés aux enregistrements d'un
		 * bénéficiaire.
		 *
		 * @fixme
		 *	- parfois, le fichier est lié à un enregistrement lié (ex. Passagecommissionep, <Thematiqueep>, ...)
		 *	- droits d'accès (contrôleur / component)
		 *	- mise en cache
		 *
		 * @param integer $personne_id L'id du bénéficiaire
		 * @return array
		 */
		public function fichiersmodules( $personne_id, array $params = array() ) {
			$structuresreferentes_ids = (array)Hash::get( $params, 'structurereferente_id' );
			unset( $params['structurereferente_id'] );

			$results = array();
			$config = $this->_configFichiersmodules();

			foreach( $config as $alias => $query ) {
				$modelName = $query['modelName'];
				$webrsaModelName = $query['webrsaModelName'];
				$webrsaAccessName = $query['webrsaAccessName'];
				$controller = $query['controller'];
				$view_action = $query['view_action'];
				$url = $query['url'];
				unset( $query['modelName'], $query['webrsaModelName'], $query['webrsaAccessName'], $query['controller'], $query['url'] );

				$url = DefaultUrl::toArray($url);
				$permissionListeEnregistrements = true === WebrsaPermissions::check( $url['controller'], $url['action'] );
				$permissionListeFichierslies = true === WebrsaPermissions::check( $url['controller'], 'filelink' );

				if( $permissionListeEnregistrements && $permissionListeFichierslies ) {
					if( 'Personne' !== $modelName ) {
						// Conditions
						$query['conditions'][] = array( "{$modelName}.personne_id" => $personne_id );

						// Conditions supplémentaires pour les RDV et les entretiens
						$query = $this->completeQueryProtectedRecords( $modelName, $query, $structuresreferentes_ids );

						// Conditions supplémentaires pour savoir si l'utilisateur est hors zone ou pas lorsque c'est possible
						if( false === $this->Personne->{$modelName}->Behaviors->attached( 'WebrsaStructurereferenteliee' ) ) {
							$this->Personne->{$modelName}->Behaviors->attach( 'WebrsaStructurereferenteliee' );
						}

						$query = $this->Personne->{$modelName}->completeQueryHorsZone(
							$query,
							$structuresreferentes_ids,
							$this->Personne->{$modelName}->links()
						);

						$this->Personne->{$modelName}->forceVirtualFields = true;
						$records = $this->Personne->{$modelName}->find( 'all', $query );
					}
					else {
						// Conditions
						$query['conditions'][] = array( "{$modelName}.id" => $personne_id );

						$this->Personne->forceVirtualFields = true;
						$records = $this->Personne->find( 'all', $query );
					}

					$records = Hash::insert( $records, '{n}.Fichiermodule.controller', $controller );
					$records = Hash::insert( $records, '{n}.Fichiermodule.view_action', $view_action );
					$records = $this->_computeAccesses( $records, $personne_id, compact( 'webrsaModelName', 'webrsaAccessName' ) );

					$results = array_merge(
						$results,
						Hash::insert( $records, '{n}.Action.name', $alias )
					);
				}
			}

			return $results;
		}

		/**
		 * Lecture de la "configuration" pour le tableau d'impressions.
		 *
		 * @return array
		 */
		protected function _readConfigImpressions() {
			$config = array(
				'/Cers93/impression/#Contratinsertion.id#' => array(
					'modelName' => 'Contratinsertion',
					'webrsaModelName' => 'WebrsaCer93',
					'webrsaAccessName' => 'WebrsaAccessCers93',
					'fields' => array(
						'Contratinsertion.id',
						'Contratinsertion.personne_id',
						'Contratinsertion.created',
						'\'impression\' AS "Impression__impression"',
						'\'Contrat\' AS "Impression__type"',
					),
					'joins' => array(
						'Cer93' => array( 'type' => 'INNER' )
					)
				),
				'/Cers93/impressionDecision/#Contratinsertion.id#' => array(
					'modelName' => 'Contratinsertion',
					'webrsaModelName' => 'WebrsaCer93',
					'webrsaAccessName' => 'WebrsaAccessCers93',
					'fields' => array(
						'Contratinsertion.id',
						'Contratinsertion.personne_id',
						'Contratinsertion.created',
						'\'impressionDecision\' AS "Impression__impression"',
						'\'Décision\' AS "Impression__type"',
					),
					'joins' => array(
						'Cer93' => array( 'type' => 'INNER' )
					),
					'conditions' => array(
						'Cer93.positioncer' => array( '99rejete', '99valide' )
					)
				),
				'/Cohortestransfertspdvs93/impression/#Orientstruct.id#' => array(
					'pdf' => true, // Stocké dans la table PDF pour le 93
					'name' => 'Transfertpdv93',
					'modelName' => 'Orientstruct',
					'webrsaAccessName' => 'WebrsaAccessTransfertspdvs93',
					'links' => array(
						'structurereferente_id' => 'structurereferente_id',
						'referent_id' => 'referent_id'
					),
					'fields' => array(
						'Orientstruct.id',
						'Orientstruct.personne_id',
						'"NvTransfertpdv93"."created" AS "Transfertpdv93__created"',
						'\'impression\' AS "Impression__impression"',
						'\'Courrier transfert\' AS "Impression__type"',
					),
					'joins' => array(
						'NvTransfertpdv93' => array( 'type' => 'INNER' ),
						'Personne' => array( 'type' => 'INNER' )
					),
					'conditions' => array(
						'Orientstruct.origine' => 'demenagement'
					)
				),
				'/Fichesprescriptions93/impression/#Ficheprescription93.id#' => array(
					'modelName' => 'Ficheprescription93',
					'fields' => array(
						'Ficheprescription93.id',
						'Ficheprescription93.personne_id',
						'Ficheprescription93.created',
						'\'Fiche\' AS "Impression__type"',
					)
				),
				'/Orientsstructs/impression/#Orientstruct.id#' => array(
					'pdf' => true, // Stocké dans la table PDF pour le 93
					'modelName' => 'Orientstruct',
					'links' => array(
						'structurereferente_id' => 'structurereferente_id',
						'referent_id' => 'referent_id'
					),
					'fields' => array(
						'Orientstruct.id',
						'Orientstruct.personne_id',
						'Orientstruct.date_valid',
						'( CASE WHEN "Orientstruct"."origine" = \'reorientation\' THEN \'Réorientation\' ELSE \'Orientation\' END ) AS "Impression__type"',
					),
					'conditions' => array(
						'Orientstruct.origine <>' => 'demenagement'
					)
				)
			);

			return $config;
		}

		/**
		 * Retourne les queries normalisées utilisées pour le tableau "Impressions".
		 *
		 * @todo
		 *	- Transfert PDV
		 *
		 * @return array
		 */
		protected function _configImpressions() {
			$cacheKey = $this->useDbConfig.'_'.$this->alias.'_'.__FUNCTION__;
			$config = Cache::read( $cacheKey );

			if( false === $config ) {
				$config = $this->_readConfigImpressions();

				// Normalisation
				$config = Hash::normalize( $config );
				foreach( $config as $path => $query ) {
					$query = (array)$query;
					$query += array(
						'pdf' => false,
						'contain' => false,
						'links' => null
					);

					if( !isset( $query['name'] ) ) {
						$query['name'] = $query['modelName'];
					}

					// Jointures
					$joins = (array)Hash::get( $query, 'joins' );
					if( !empty( $joins ) ) {
						if( 'Personne' !== $query['modelName'] ) {
							if( isset( $this->Personne->{$query['modelName']} ) ) {
								$joins = $this->Personne->{$query['modelName']}->joins( $joins );
							}
							else {
								$this->loadModel( $query['modelName'] );
								$joins = $this->{$query['modelName']}->joins( $joins );
							}
						}
						else {
							$joins = $this->Personne->joins( $joins );
						}
					}
					$query['joins'] = $joins;

					$query = $this->_completeConfigAccess( $query );

					$config[$path] = $query;
				}

				Cache::write( $cacheKey, $config );
			}

			return $config;
		}

		/**
		 * Retourne la liste des impression possibles
		 *
		 * @param integer $personne_id L'id du bénéficiaire
		 * @return array
		 */
		public function impressions( $personne_id, array $params = array() ) {
			$structuresreferentes_ids = (array)Hash::get( $params, 'structurereferente_id' );
			unset( $params['structurereferente_id'] );

			$results = array();
			$config = $this->_configImpressions();

			foreach( $config as $path => $query ) {
				$modelName = $query['modelName'];
				$webrsaModelName = $query['webrsaModelName'];
				$webrsaAccessName = $query['webrsaAccessName'];
				$name = $query['name'];
				$pdf = $query['pdf'];
				$links = $query['links'];

				unset( $query['modelName'], $query['webrsaModelName'], $query['webrsaAccessName'], $query['pdf'], $query['name'], $query['links'] );

				if( 'Personne' !== $modelName ) {
					if( isset( $this->Personne->{$modelName} ) ) {
						$Model = $this->Personne->{$modelName};
						$query['conditions'][] = array( "{$modelName}.personne_id" => $personne_id );
						if( true === $pdf ) {
							$query['conditions'][] = $this->Pdf->sqImprime( $this->Personne->{$modelName} );
						}
					}
					else {
						$this->loadModel( $modelName );
						$Model = $this->{$modelName};
						$query['conditions'][] = array( 'Personne.id' => $personne_id );
						if( true === $pdf ) {
							$query['conditions'][] = $this->Pdf->sqImprime( $this->{$modelName} );
						}
					}

					$query = $this->completeQueryProtectedRecords( $modelName, $query, $structuresreferentes_ids );

					// Conditions supplémentaires pour savoir si l'utilisateur est hors zone ou pas lorsque c'est possible
					if( false === $Model->Behaviors->attached( 'WebrsaStructurereferenteliee' ) ) {
						$Model->Behaviors->attach( 'WebrsaStructurereferenteliee' );
					}

					$links = false === empty( $links ) ? $links : $Model->links();

					$query = $Model->completeQueryHorsZone(
						$query,
						$structuresreferentes_ids,
						$links
					);

					$Model->forceVirtualFields = true;
					$records = $Model->find( 'all', $query );
				}
				else {
					$query['conditions'][] = array( 'Personne.id' => $personne_id );

					$this->Personne->forceVirtualFields = true;
					$records = $this->Personne->find( 'all', $query );
					debug( $this->Personne->sq( $query ) );
				}

				$records = $this->_computeAccesses( $records, $personne_id, compact( 'webrsaModelName', 'webrsaAccessName' ) );

				$results = array_merge(
					$results,
					 Hash::insert( $records, '{n}.Impression.name', $name )
				);
			}

			return $results;
		}


		/**
		 * Retourne les options à utiliser dans la vue.
		 *
		 * @todo Nom de la méthode en paramètre
		 *
		 * @return array
		 */
		public function options() {
			$cacheKey = $this->useDbConfig.'_'.$this->alias.'_'.__FUNCTION__;
			$options = Cache::read( $cacheKey );

			if( false === $options ) {
				$options = array(
					'Accompagnement' => array(
						'topmoyloco' => $this->Personne->Dsp->enum( 'topmoyloco' )
					),
					// Tableau "Actions"
					'Action' => array(
						'name' => array(
							'Contratinsertion' => 'CER',
							'Questionnaired1pdv93' => 'D1',
							'Questionnaired2pdv93' => 'D2',
							'Entretien' => 'Entretien',
							'DspRev' => 'MAJ DSP',
							'Ficheprescription93' => 'Prescription',
							'Rendezvouscollectif' => 'RDV collectif',
							'Rendezvousindividuel' => 'RDV individuel',
						)
					),
					'Calculdroitrsa' => array(
						'toppersdrodevorsa' => array( '' => __d( 'calculdroitrsa', 'ENUM::TOPPERSDRODEVORSA::NULL' ) )
							+ $this->Personne->Calculdroitrsa->enum( 'toppersdrodevorsa' )
					),
					'Cer93' => array(
						'nivetu' => $this->Personne->Contratinsertion->Cer93->enum( 'nivetu' ),
						'positioncer' => $this->Personne->Contratinsertion->Cer93->enum( 'positioncer' ),
						'cmu' => $this->Personne->Contratinsertion->Cer93->enum( 'cmu' ),
						'cmuc' => $this->Personne->Contratinsertion->Cer93->enum( 'cmuc' )
					),
					'Dsp' => array(
						'nivetu' => $this->Personne->Dsp->enum( 'nivetu' )
					),
					'DspRev' => array(
						'nivetu' => $this->Personne->Dsp->enum( 'nivetu' )
					),
					'Ficheprescription93' => array(
						'statut' => $this->Personne->Ficheprescription93->enum( 'statut' )
					),
					// Tableau "Fichiers liés"
					'Fichiermodule' => array(
						'modele' => array(
							'Apre' => 'APRE',
							'Personne' => 'Bénéficiaire',
							'Contratinsertion' => 'CER',
							'Entretien' => 'Entretien',
							'DspRev' => 'MAJ DSP',
							'Memo' => 'Mémo',
							'Orientstruct' => 'Orientation',
							'PersonneReferent' => 'Référent du parcours',
							'Rendezvous' => 'Rendez-vous'
						)
					),
					// Tableau "Actions"
					'Impression' => array(
						// Tableau "Impressions"
						'name' => array(
							'Contratinsertion' => 'CER',
							'Ficheprescription93' => 'Prescription',
							'Orientstruct' => 'Orientation',
							'Transfertpdv93' => 'Transfert PDV',
						)
					),
					'Questionnaired1pdv93' => array(
						'nivetu' => $this->Personne->Questionnaired1pdv93->enum( 'nivetu' )
					),
					'Questionnaired2pdv93' => array(
						'situationaccompagnement' => $this->Personne->Questionnaired2pdv93->enum( 'situationaccompagnement' )
					),
					'Situationdossierrsa' => array(
						'etatdosrsa' => ClassRegistry::init('Dossier')->enum('etatdosrsa')
					)
				);

				Cache::write( $cacheKey, $options );
			}

			return $options;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les fonctions vides.
		 */
		public function prechargement() {
			$departement = (int)Configure::read( 'Cg.departement' );

			if( 93 === $departement ) {
				$success = true;

				foreach( $this->prechargementMethods as $method ) {
					$data = $this->{$method}();
					$success = !empty( $data ) && $success;
				}
			}
			else {
				$success = null;
			}

			return $success;
		}
	}
?>