<?php
	/**
	 * Code source de la classe Nonoriente66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Nonoriente66 ...
	 *
	 * @package app.Model
	 */
	class Nonoriente66 extends AppModel
	{
		public $name = 'Nonoriente66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Conditionnable',
			'Fichiermodulelie',
			'Gedooo.Gedooo',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Historiqueetatpe' => array(
				'className' => 'Historiqueetatpe',
				'foreignKey' => 'historiqueetatpe_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Orientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'orientstruct_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);


		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Nonoriente66\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		/**
		 * Liste des modeles odt utilisé par ce Modele
		 *
		 * @var array
		 */
		public $modelesOdt = array(
			'default' => 'Orientation/questionnaireorientation66.odt',
			'courrier1' => 'Orientation/orientationpedefait.odt',
			'courrier2' => 'Orientation/orientationpe.odt',
			'courrier3' => 'Orientation/orientationsociale.odt',
			'courrier4' => 'Orientation/orientationsocialeauto.odt',
		);

		/**
		 * Retourne les données nécessaires à l'impression
		 *
		 * @param integer $user_id
		 * @return array
		 */
		public function getDataForPdf($user_id = null) {
			$User = ClassRegistry::init('User');
			$querydata = array(
				'fields' => array_merge(
					$this->Personne->fields(),
					$this->Personne->Foyer->Adressefoyer->Adresse->fields(),
					$this->Personne->Foyer->fields(),
					$this->Personne->Foyer->Dossier->fields(),
					$this->Personne->Orientstruct->Nonoriente66->fields(),
					$this->Personne->Orientstruct->Typeorient->fields(),
					$this->Personne->Orientstruct->Structurereferente->fields(),
					$this->Personne->Orientstruct->Referent->fields(),
					$User->fields(),
					$User->Serviceinstructeur->fields()
				),
				'joins' => array(
					$this->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$this->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$this->Personne->join( 'Orientstruct', array( 'type' => 'LEFT OUTER' ) ),
					$this->Personne->Orientstruct->join( 'Nonoriente66', array( 'type' => 'LEFT OUTER' ) ),
					$this->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
					$this->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					$this->Personne->Orientstruct->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
					array(
						'alias' => 'User',
						'table' => 'users',
						'conditions' => array('User.id' => $user_id),
						'type' => 'LEFT OUTER'
					),
					$User->join( 'Serviceinstructeur', array( 'type' => 'LEFT OUTER' ) ),
				),
				'conditions' => array(
					'Adressefoyer.id IN ( '.$this->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
				),
				'contain' => false
			);
			return $querydata;
		}

		/**
		 * Retourne le PDF par défaut généré par les appels aux méthodes getDataForPdf, modeleOdt et
		 * à la méthode ged du behavior Gedooo
		 * Le courrier généré est le questionnaire à destination des allocataires non orientés et non inscrits au PE
		 *
		 * @param type $id Id de la personne
		 * @param type $user_id Id de l'utilisateur connecté
		 * @return string PDF
		 */
		public function getDefaultPdf( $id, $user_id ) {
			$options = array(
				'Personne' => array(
					'qual' => ClassRegistry::init( 'Option' )->qual()
				)
			);

			$querydata = $this->getDataForPdf();

			$querydata = Set::merge(
				$querydata,
				array(
					'conditions' => array(
						'Personne.id' => $id
					)
				)
			);
			$personne = $this->Personne->find( 'first', $querydata );

			/// Récupération de l'utilisateur
			$user = ClassRegistry::init( 'User' )->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => false
				)
			);
			$personne['User'] = $user['User'];

			if( empty( $personne ) ) {
				$this->cakeError( 'error404' );
			}

			return $this->ged(
				$personne,
				$this->modelesOdt['default'],
				false,
				$options
			);
		}

		/**
		 * Fonction permettant d'enregistrer la date du jour de l'impression du courrier envoyé
		 * aux allocataires ne possédant pas encore d'orientation
		 *
		 * @param integer $personne_id
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveImpression( $personne_id, $user_id ) {
			$nonoriente66 = array(
				'Nonoriente66' => array(
					'personne_id' => $personne_id,
					'dateimpression' => date( 'Y-m-d' ),
					'orientstruct_id' => null,
					'historiqueetatpe_id' => null,
					'origine' => 'notisemploi',
					'user_id' => $user_id
				)
			);

			$this->create( $nonoriente66 );
			return $this->save( null, array( 'atomic' => false ) );
		}

		/**
		 * Renvoi le chemin vers le document odt en fonction de data
		 *
		 * @param array $data
		 * @return string
		 */
		public function modeleOdt( $data = array(), $name = null ) {
			if ($name === 'cohorte_imprimernotifications_impressions') {
				$typeOrientParentIdPdf = Hash::get( $data, 'Typeorient.parentid' );

				if( Hash::get($data, 'Nonoriente66.origine') === 'isemploi' ) {
					return $this->modelesOdt['courrier1'];
				} elseif (in_array( $typeOrientParentIdPdf, Configure::read( 'Orientstruct.typeorientprincipale.Emploi' ))) {
					return $this->modelesOdt['courrier2'];
				} elseif (in_array( $typeOrientParentIdPdf, Configure::read( 'Orientstruct.typeorientprincipale.SOCIAL' ))) {
					return Hash::get($data, 'Nonoriente66.reponseallocataire') === 'N' ? $this->modelesOdt['courrier4'] : $this->modelesOdt['courrier3'];
				}
			}

			return $this->modelesOdt['default'];
		}

		/**
		 * Permet un choix de structures en fonction du canton
		 *
		 * @return array
		 */
		public function structuresAutomatiques() {
			$this->Structurereferente = ClassRegistry::init( 'Structurereferente' );

			$results = $this->Structurereferente->find(
				'all',
				array(
					'fields' => array(
						'Structurereferente.typeorient_id',
						'( "Structurereferente"."typeorient_id" || \'_\' || "Structurereferente"."id" ) AS "Structurereferente__id"',
						'Canton.canton'
					),
					'conditions' => array(
						'Structurereferente.typeorient_id' => Configure::read( 'Nonoriente66.notisemploi.typeorientId' )
					),
					'joins' => array(
						$this->Structurereferente->join( 'StructurereferenteZonegeographique' ),
						$this->Structurereferente->StructurereferenteZonegeographique->join( 'Zonegeographique' ),
						$this->Structurereferente->StructurereferenteZonegeographique->Zonegeographique->join( 'Canton' )
					),
					'contain' => false
				)
			);

			return Set::combine( $results, '{n}.Structurereferente.typeorient_id', '{n}.Structurereferente.id', '{n}.Canton.canton' );
		}
	}
?>