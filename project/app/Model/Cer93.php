<?php
	/**
	 * Code source de la classe Cer93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Cer93 gère les CER du CG 93.
	 *
	 * @package app.Model
	 */
	class Cer93 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Cer93';

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Allocatairelie' => array(
				'joins' => array( 'Contratinsertion' )
			),
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate',
			'Gedooo.Gedooo',
			'ModelesodtConditionnables' => array(
				93 => array(
					'Contratinsertion/contratinsertion.odt',
					'Contratinsertion/cer_valide.odt',
					'Contratinsertion/cer_rejete.odt',
					'Contratinsertion/decision_valide.odt',
					'Contratinsertion/decision_rejete.odt'
				)
			),
			'StorablePdf'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'WebrsaCer93'
		);

		public $validate = array(
			'matricule' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'qual' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'codepos' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'nomcom' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'isemploitrouv' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'dureehebdo' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( 'notEmptyIf', 'isemploitrouv', true, array( 'O' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'naturecontrat_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( 'notEmptyIf', 'isemploitrouv', true, array( 'O' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'prevu' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'duree' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'pointparcours' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
			'datepointparcours' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( 'notEmptyIf', 'pointparcours', true, array( 'aladate' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'datesignature' => array(
				'datePassee' => array(
					'rule' => array( 'datePassee' ),
					'message' => 'Merci de renseigner une date antérieure ou égale à la date du jour'
				)
			)
		);

		/**
		 * Liaisons "belongsTo" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Annulateur' => array(
				'className' => 'User',
				'foreignKey' => 'annulateur_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'contratinsertion_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Emptrouvromev3' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'emptrouvromev3_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Metierexerce' => array(
				'className' => 'Metierexerce',
				'foreignKey' => 'metierexerce_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Secteuracti' => array(
				'className' => 'Secteuracti',
				'foreignKey' => 'secteuracti_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Naturecontrat' => array(
				'className' => 'Naturecontrat',
				'foreignKey' => 'naturecontrat_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Sujetromev3' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'sujetromev3_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Liaisons "hasMany" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Compofoyercer93' => array(
				'className' => 'Compofoyercer93',
				'foreignKey' => 'cer93_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Diplomecer93' => array(
				'className' => 'Diplomecer93',
				'foreignKey' => 'cer93_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Expprocer93' => array(
				'className' => 'Expprocer93',
				'foreignKey' => 'cer93_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Histochoixcer93' => array(
				'className' => 'Histochoixcer93',
				'foreignKey' => 'cer93_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		/**
		 * Liaisons "hasAndBelongsToMany" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
            'Sujetcer93' => array(
				'className' => 'Sujetcer93',
				'joinTable' => 'cers93_sujetscers93',
				'foreignKey' => 'cer93_id',
				'associationForeignKey' => 'sujetcer93_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Cer93Sujetcer93'
			)
		);

		/**
		 * Surcharge du constructeur pour ajouter des champs virtuels.
		 *
		 * @param mixed $id Set this ID for this model on startup, can also be an array of options, see above.
		 * @param string $table Name of database table to use.
		 * @param string $ds DataSource connection name.
		 */
		public function __construct( $id = false, $table = null, $ds = null ) {
			parent::__construct( $id, $table, $ds );

			// Seulement lorsque l'on n'est pas en train d'importer des fixtures
			if( !( unittesting() && $this->useDbConfig === 'default' ) ) {
				$this->virtualFields['sujets'] = $this->vfListeSujetscers93( null );
				$this->virtualFields['sujets_virgules'] = $this->vfListeSujetscers93( null, ', ' );
			}
		}

		/**
		 * Retourne un champ virtuel contenant la liste des sujets liées à
		 * une CER, séparées par la chaîne de caractères $glue.
		 *
		 * Si le nom du champ virtuel est vide, alors le champ non aliasé sera
		 * retourné.
		 *
		 * @param string $fieldName Le nom du champ virtuel; le modèle sera l'alias
		 *	du modèle (Cer93) utilisé.
		 * @param string $glue La chaîne de caratcères utilisée pour séparer les
		 *	noms des aides.
		 * @return string
		 */
		public function vfListeSujetscers93( $fieldName = 'sujets', $glue = '\\n\r-' ) {
			$query = array(
				'fields' => array( 'Sujetcer93.name' ),
				'alias' => 'cer93_sujetscers93',
				'joins' => array(
					$this->Cer93Sujetcer93->join( 'Sujetcer93', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					"Cer93Sujetcer93.cer93_id = {$this->alias}.id"
				),
				'contain' => false
			);
			$replacements = array( 'Cer93Sujetcer93' => 'cer93_sujetscers93', 'Sujetcer93' => 'sujetscers93' );
			$query = array_words_replace( $query, $replacements );

			$sql = "TRIM( BOTH ' ' FROM TRIM( TRAILING '{$glue}' FROM ARRAY_TO_STRING( ARRAY( ".$this->Cer93Sujetcer93->sq( $query )." ), '{$glue}' ) ) )";

			if( !empty( $fieldName ) ) {
				$sql = "{$sql} AS \"{$this->alias}__{$fieldName}\"";
			}

			return $sql;
		}

		/**
		 * Retourne le chemin relatif du modèle de document à utiliser pour
		 * l'enregistrement du PDF.
		 *
		 * @param array $data
		 * @return string
		 */
		public function modeleOdt( $data ) {
			return "Contratinsertion/contratinsertion.odt";
		}

		/**
		 * Retourne le querydata complété par les champs et les jointures concernant
		 * l'emploi trouvé (ROME v.3).
		 *
		 * @param array $query Le querydata à compléter
		 * @param string Le préfixe de l'alias avec Entreeromev3 qui sera utilisé
		 *	comme suffixe pour les enregistrements qui en dépendent (ex.: "emptrouv"
		 *	concerne l'alias "Emptrouvromev3" et créera des alias "Familleemptrouv",
		 *	...)
		 * @param string $type Le type de jointure (par défaut: LEFT OUTER)
		 * @return array
		 */
		public function getCompletedRomev3Joins( array $query, $word, $type = 'LEFT OUTER' ) {
			// $word = 'emptrouv'; // TODO: paramètre + rendre générique dans le modèle Entreeromev3
			// @see Entreeromev3::getCompletedRomev3Joins()
			$suffix = Inflector::underscore( $word );
			$alias = Inflector::classify( "{$word}romev3" );

			$replacements = array(
				"Familleromev3" => "Famille{$suffix}",
				"Domaineromev3" => "Domaine{$suffix}",
				"Metierromev3" => "Metier{$suffix}",
				"Appellationromev3" => "Appellation{$suffix}"
			);

			$query['fields'] = array_merge(
				$query['fields'],
				array(
					"Famille{$suffix}.code" => "Famille{$suffix}.code",
					"Famille{$suffix}.name" => "Famille{$suffix}.name",
					"Domaine{$suffix}.code" => "( \"Famille{$suffix}\".\"code\" || \"Domaine{$suffix}\".\"code\" ) AS \"Domaine{$suffix}__code\"",
					"Domaine{$suffix}.name" => "Domaine{$suffix}.name",
					"Metier{$suffix}.code" => "( \"Famille{$suffix}\".\"code\" || \"Domaine{$suffix}\".\"code\" || \"Metier{$suffix}\".\"code\" ) AS \"Metier{$suffix}__code\"",
					"Metier{$suffix}.name" => "Metier{$suffix}.name",
					"Appellation{$suffix}.name" => "Appellation{$suffix}.name"
				)
			);

			$query['joins'][] = $this->join( $alias, array( 'type' => $type ) );

			$query['joins'][] = array_words_replace(
				$this->{$alias}->join( 'Familleromev3', array( 'type' => $type ) ),
				$replacements
			);

			$query['joins'][] = array_words_replace(
				$this->{$alias}->join( 'Domaineromev3', array( 'type' => $type ) ),
				$replacements
			);

			$query['joins'][] = array_words_replace(
				$this->{$alias}->join( 'Metierromev3', array( 'type' => $type ) ),
				$replacements
			);

			$query['joins'][] = array_words_replace(
				$this->{$alias}->join( 'Appellationromev3', array( 'type' => $type ) ),
				$replacements
			);

			return $query;
		}

		/**
		 * Récupère les données pour le PDF.
		 *
		 * @param integer $contratinsertion_id
		 * @param integer $user_id
		 * @return array
		 */
		public function getDataForPdf( $contratinsertion_id, $user_id ) {
			$this->Contratinsertion->Personne->forceVirtualFields = true;
			$Informationpe = ClassRegistry::init( 'Informationpe' );

			$joins = array(
				$this->join( 'Contratinsertion', array( 'type' => 'INNER' ) ),
				$this->join( 'User', array( 'type' => 'INNER' ) ),
				$this->Contratinsertion->join( 'Personne', array( 'type' => 'INNER' )),
				$Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', 'LEFT OUTER' ),
				$Informationpe->join( 'Historiqueetatpe', array( 'type' => 'LEFT OUTER' ) ),
				$this->Contratinsertion->join( 'Structurereferente', array( 'type' => 'INNER' )),
				$this->Contratinsertion->join( 'Referent', array( 'type' => 'LEFT OUTER' )),
				$this->Contratinsertion->Personne->join( 'Dsp', array( 'type' => 'LEFT OUTER' )),
				$this->Contratinsertion->Personne->join( 'DspRev', array( 'type' => 'LEFT OUTER' )),
				$this->Contratinsertion->Personne->join( 'Foyer', array( 'type' => 'INNER' )),
				$this->Contratinsertion->Personne->join( 'Prestation', array( 'type' => 'LEFT OUTER'  )),
				$this->Contratinsertion->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
				$this->Contratinsertion->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
				$this->Contratinsertion->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
				$this->join( 'Naturecontrat', array( 'type' => 'LEFT OUTER' ) ),
				array_words_replace(
					$this->join( 'Metierexerce', array( 'type' => 'LEFT OUTER' ) ),
					array(
						'Metierexerce' => 'Metierexercecer93'
					)
				),
				array_words_replace(
					$this->join( 'Secteuracti', array( 'type' => 'LEFT OUTER' ) ),
					array(
						'Secteuracti' => 'Secteuracticer93'
					)
				)
			);

			$queryData = array(
				'fields' => array_merge(
					$this->fields(),
					$this->User->fields(),
					$this->Contratinsertion->fields(),
					array_words_replace(
						$this->Metierexerce->fields(),
						array(
							'Metierexerce' => 'Metierexercecer93'
						)
					),
					array_words_replace(
						$this->Secteuracti->fields(),
						array(
							'Secteuracti' => 'Secteuracticer93'
						)
					),
					$this->Naturecontrat->fields(),
					$this->Contratinsertion->Structurereferente->fields(),
					$this->Contratinsertion->Structurereferente->Referent->fields(),
					$this->Contratinsertion->Personne->fields(),
					$this->Contratinsertion->Personne->Prestation->fields(),
					$this->Contratinsertion->Personne->Dsp->fields(),
					$this->Contratinsertion->Personne->DspRev->fields(),
					$this->Contratinsertion->Personne->Foyer->fields(),
					$this->Contratinsertion->Personne->Foyer->Adressefoyer->Adresse->fields(),
					$this->Contratinsertion->Personne->Foyer->Dossier->fields(),
					array(
						'Historiqueetatpe.identifiantpe',
						'Historiqueetatpe.etat'
					)
				),
				'joins' => $joins,
				'conditions' => array(
					'Cer93.contratinsertion_id' => $contratinsertion_id,
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ( '.$this->Contratinsertion->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
						)
					),
					array(
						'OR' => array(
							'Dsp.id IS NULL',
							'Dsp.id IN ( '.$this->Contratinsertion->Personne->Dsp->WebrsaDsp->sqDerniereDsp( 'Personne.id' ).' )'
						)
					),
					array(
						'OR' => array(
							'DspRev.id IS NULL',
							'DspRev.id IN ( '.$this->Contratinsertion->Personne->DspRev->sqDerniere( 'Personne.id' ).' )'
						)
					),
					array(
						'OR' => array(
							'Informationpe.id IS NULL',
							'Informationpe.id IN( '.$Informationpe->sqDerniere( 'Personne' ).' )'
						)
					),
					array(
						'OR' => array(
							'Historiqueetatpe.id IS NULL',
							'Historiqueetatpe.id IN( '.$Informationpe->Historiqueetatpe->sqDernier( 'Informationpe' ).' )'
						)
					)
				),
				'contain' => false
			);

			// Codes ROME V.3, emploi trouvé
			$queryData = $this->getCompletedRomev3Joins( $queryData, 'emptrouv' );

			// Codes ROME V.3, votre contrat porte sur l'emploi
			$queryData = $this->getCompletedRomev3Joins( $queryData, 'sujet' );

			// On copie les DspsRevs si elles existent à la place des DSPs (on garde l'information la plus récente)
			if( !empty( $data['DspRev']['id'] ) ) {
				$data['Dsp'] = $data['DspRev'];
				unset( $data['DspRev'], $data['Dsp']['id'], $data['Dsp']['dsp_id'] );
			}

			$data = $this->find( 'first', $queryData );

			// Si on ne trouve pas de référent lié au CER, on va chercher le référent de parcours qui était désigné au moment de la date de validation du CER
			if( empty( $data['Referent']['id'] ) ) {
				$referent = $this->Contratinsertion->Personne->PersonneReferent->find(
					'first',
					array(
						'conditions' => array(
							'PersonneReferent.personne_id' => $data['Personne']['id'],
							'PersonneReferent.dddesignation <=' => $data['Contratinsertion']['date_saisi_ci'],
							'OR' => array(
								'PersonneReferent.dfdesignation IS NULL',
								'PersonneReferent.dfdesignation >=' => $data['Contratinsertion']['date_saisi_ci'],
							)
						),
						'contain' => array(
							'Referent'
						),
						'order' => array( 'PersonneReferent.dfdesignation ASC' )
					)
				);

				if( !empty( $referent ) ) {
					$data['Referent'] = $referent['Referent'];
				}
			}

			// Liste des informations concernant la composition du foyer
			$composfoyerscers93 = $this->Contratinsertion->Personne->find(
				'all',
				array(
					'fields' => array(
						'"Personne"."qual" AS "Compofoyercer93__qual"',
						'"Personne"."nom" AS "Compofoyercer93__nom"',
						'"Personne"."prenom" AS "Compofoyercer93__prenom"',
						'"Personne"."dtnai" AS "Compofoyercer93__dtnai"',
						'"Prestation"."rolepers" AS "Compofoyercer93__rolepers"'
					),
					'conditions' => array( 'Personne.foyer_id' => $data['Foyer']['id'] ),
					'contain' => array(
						'Prestation'
					)
				)
			);

			// Liste des diplômes enregistrés pour ce CER
			$diplomescers93 = $this->Diplomecer93->find(
				'all',
				array(
					'fields' => array(
						'Diplomecer93.id',
						'Diplomecer93.cer93_id',
						'Diplomecer93.name',
						'Diplomecer93.annee',
						'Diplomecer93.isetranger'
					),
					'conditions' => array( 'Diplomecer93.cer93_id' => $data['Cer93']['id'] ),
					'order' => array( 'Diplomecer93.annee DESC' ),
					'contain' => false
				)
			);

			// Bloc 4 : Formation et expériece
			// Liste des expériences pro enregistrés pour ce CER
			$expsproscers93 = $this->Expprocer93->find(
				'all',
				array(
					'fields' => array(
						'Expprocer93.id',
						'Expprocer93.cer93_id',
						'Expprocer93.metierexerce_id',
						'Expprocer93.secteuracti_id',
						'Expprocer93.anneedeb',
						'Expprocer93.duree',
						'Expprocer93.nbduree',
						'Expprocer93.typeduree',
						'Metierexerce.name',
						'Secteuracti.name',
						'Naturecontrat.name',
						'Familleromev3.code',
						'Familleromev3.name',
						'( "Familleromev3"."code" || "Domaineromev3"."code" ) AS "Domaineromev3__code"',
						'Domaineromev3.name',
						'( "Familleromev3"."code" || "Domaineromev3"."code" || "Metierromev3"."code" ) AS "Metierromev3__code"',
						'Metierromev3.name',
						'Appellationromev3.name'
					),
					'conditions' => array( 'Expprocer93.cer93_id' => $data['Cer93']['id'] ),
					'order' => array( 'Expprocer93.anneedeb DESC' ),
					'contain' => false,
					'joins' => array(
						$this->Expprocer93->join( 'Metierexerce', array( 'type' => 'LEFT OUTER' ) ),
						$this->Expprocer93->join( 'Secteuracti', array( 'type' => 'LEFT OUTER' ) ),
						$this->Expprocer93->join( 'Entreeromev3', array( 'type' => 'LEFT OUTER' ) ),
						$this->Expprocer93->join( 'Naturecontrat', array( 'type' => 'LEFT OUTER' ) ),
						$this->Expprocer93->Entreeromev3->join( 'Familleromev3', array( 'type' => 'LEFT OUTER' ) ),
						$this->Expprocer93->Entreeromev3->join( 'Domaineromev3', array( 'type' => 'LEFT OUTER' ) ),
						$this->Expprocer93->Entreeromev3->join( 'Metierromev3', array( 'type' => 'LEFT OUTER' ) ),
						$this->Expprocer93->Entreeromev3->join( 'Appellationromev3', array( 'type' => 'LEFT OUTER' ) ),
					)
				)
			);

			// Liste des sujets sur lequel porte ce CER
			$sujetscers93 = $this->Cer93Sujetcer93->find(
				'all',
				array(
					'conditions' => array( 'Cer93Sujetcer93.cer93_id' => $data['Cer93']['id'] ),
					'contain' => array(
						'Sujetcer93',
						'Soussujetcer93',
						'Valeurparsoussujetcer93'
					)
				)
			);

			// Transformation des sujets, ... précédents
			$sujetscerspcds93 = array();
			$sujetspcds = Hash::get( $data, 'Cer93.sujetpcd' );
			if( !empty($sujetspcds) ) {
				$sujetspcds = unserialize( $sujetspcds );
				foreach( $sujetspcds['Sujetcer93'] as $i => $sujetcer93pcd ) {
					$sujetscerspcds93[$i]['Soussujetcerpcd93'] = (array)Hash::get( $sujetcer93pcd, 'Cer93Sujetcer93.Soussujetcer93' );
					unset( $sujetcer93pcd['Cer93Sujetcer93']['Soussujetcer93'] );

					$sujetscerspcds93[$i]['Valeurparsoussujetcerpcd93'] = (array)Hash::get( $sujetcer93pcd, 'Cer93Sujetcer93.Valeurparsoussujetcer93' );
					unset( $sujetcer93pcd['Cer93Sujetcer93']['Valeurparsoussujetcer93'] );

					$sujetscerspcds93[$i]['Cerpcd93Sujetcerpcd93'] = (array)Hash::get( $sujetcer93pcd, 'Cer93Sujetcer93' );
					unset( $sujetcer93pcd['Cer93Sujetcer93'] );

					$sujetscerspcds93[$i]['Sujetcerpcd93'] = $sujetcer93pcd;
				}

				// Sujet précédent, ROME v.3
				$sujetromev3 = (array)Hash::get( $sujetspcds, 'Sujetromev3' );
				if( !empty( $sujetromev3 ) ) {
					$aliases = array(
						'Familleromev3' => 'famille',
						'Domaineromev3' => 'domaine',
						'Metierromev3' => 'metier'
					);
					$data['Sujetromev3pcd'] = array();
					$code = '';
					foreach( $aliases as $alias => $fieldName ) {
						$code = $code.Hash::get( $sujetromev3, "{$alias}.code" );
						$data['Sujetromev3pcd'][$fieldName] = implode( ' - ', Hash::filter( array( $code, Hash::get( $sujetromev3, "{$alias}.name" ) ) ) );
					}
					$data['Sujetromev3pcd']['appellation'] = Hash::get( $sujetromev3, 'Appellationromev3.name' );
				}
			}

            // Récupération du nom de l'utilsiateur ayant émis la première lecture
			$histopremierelecture = $this->Histochoixcer93->find(
				'first',
				array(
					'fields' => array_merge(
						$this->Histochoixcer93->fields(),
                        array(
                            $this->Histochoixcer93->User->sqVirtualField( 'nom_complet' ),
                            'User.numtel'
                        )
					),
					'conditions' => array(
                        'Histochoixcer93.cer93_id' => $data['Cer93']['id'],
                        'Histochoixcer93.etape' => '04premierelecture'
                    ),
					'contain' => array(
                        'User'
                    )
				)
			);

            if( !empty( $histopremierelecture ) ) {
                $userPremierelecture = Hash::get( $histopremierelecture, 'User.nom_complet' );
                $data['Cer93']['userpremierelecture'] = $userPremierelecture;
                $data['Cer93']['userpremierelecture_numtel'] = Hash::get( $histopremierelecture, 'User.numtel' );
            }

			return array(
				$data,
				'compofoyer' => $composfoyerscers93,
				'exppro' => $expsproscers93,
				'diplome' => $diplomescers93,
				'sujetcer' => $sujetscers93,
				'sujetcerpcd' => $sujetscerspcds93
			);
		}


		/**
		 * Retourne le PDF par défaut, stocké, ou généré par les appels aux méthodes getDataForPdf, modeleOdt et
		 * à la méthode ged du behavior Gedooo et le stocke,
		 *
		 * @param integer $id Id du CER
		 * @param integer $user_id Id de l'utilisateur connecté
		 * @return string
		 */
		public function getDefaultPdf( $contratinsertion_id, $user_id ) {
			$pdf = false;
			//Recherche du PDF en Base de donnée ou CMIS
			if ( Configure::read( 'cer.pdf.save' ) ) {
				$pdf = $this->getStoredPdf($contratinsertion_id);
				if( !empty( $pdf ) ) {
					$pdf = $pdf['Pdf']['document'];
				}
			}
			//Si le PDF n'existe pas on le crée
			if( empty( $pdf ) ) {
				$data = $this->getDataForPdf( $contratinsertion_id, $user_id );
				$modeleodt = $this->modeleOdt( $data );

				$Option = ClassRegistry::init( 'Option' );
				$options =  Set::merge(
					array(
						'Personne' => array(
							'qual' => $Option->qual()
						),
						'Cer93' => array(
							'dureecdd' => ClassRegistry::init('Contratinsertion')->enum('duree_cdd')
						)
					),
					$this->enums()
				);
				$pdf = $this->ged( $data, $modeleodt, true, $options );
				//On stocke le PDF crée
				if ( Configure::read( 'cer.pdf.save' ) ) {
					if( !empty( $pdf ) ) {
						$this->storePdf( $contratinsertion_id, $modeleodt, $pdf );
					}
				}
			}
			return $pdf;
		}

		/**
		 * Retourne le PDF de décision pour un CER donné
		 *
		 * @param integer $id Id du CER
		 * @param integer $user_id Id de l'utilisateur connecté
		 * @return string
		 */

		public function getDecisionPdf( $contratinsertion_id, $user_id ) {
			$pdf = false;
			//Recherche du PDF en Base de donnée ou CMIS
			if ( Configure::read( 'cer.pdf.save' ) ) {
				$pdf = $this->getStoredPdf($contratinsertion_id);
				if( !empty( $pdf ) ) {
					$pdf = $pdf['Pdf']['document'];
				}
			}
			//Si le PDF n'existe pas on le crée
			if( empty( $pdf ) ) {
				$options = $this->WebrsaCer93->optionsView();
				$data = $this->getDataForPdf( $contratinsertion_id, $user_id );
				$data = $data[0];
				$dateimpressiondecision = date( 'Y-m-d' );
				if( !empty( $dateimpressiondecision ) ) {
					$this->updateAllUnBound(
						array( 'Cer93.dateimpressiondecision' => '\''.$dateimpressiondecision.'\'' ),
						array(
							'"Cer93"."id"' => $data['Cer93']['id']
						)
					);
				}
				// Choix du modèle de document
				$decision = $data['Contratinsertion']['decision_ci'];
				// Forme du CER
				$formeci = $data['Contratinsertion']['forme_ci'];
				if( $formeci == 'S' ) {
					if( $decision == 'V' ) {
						$modeleodt  = "Contratinsertion/cer_valide.odt";
					}
					else if( in_array( $decision, array( 'R', 'N' ) ) ){
						$modeleodt  = "Contratinsertion/cer_rejete.odt";
					}
				}
				else {
					if( $decision == 'V' ) {
						$modeleodt  = "Contratinsertion/decision_valide.odt";
					}
					else if( in_array( $decision, array( 'R', 'N' ) ) ){
						$modeleodt  = "Contratinsertion/decision_rejete.odt";
					}
				}
				$pdf = $this->ged( $data, $modeleodt, false, $options );

				//On stocke le PDF crée
				if ( Configure::read( 'cer.pdf.save' ) ) {
					if( !empty( $pdf ) ) {
						$this->storePdf( $contratinsertion_id, $modeleodt, $pdf );
					}
				}
			}
			return $pdf;
		}

		/**
		 * Retourne les options nécessaires au formulaire de recherche
		 *
		 * @param array $params <=> array( 'find' => false, 'autre' => false )
		 * @return array
		 */
		public function options( array $params = array() ) {
			$options = array();
			$params = $params + array( 'find' => false, 'autre' => false );

			$foreignKeyPrev = null;
			foreach( array( 'Sujetcer93', 'Soussujetcer93', 'Valeurparsoussujetcer93' ) as $modelName ) {
				$Model = ClassRegistry::init( $modelName );

				// Find list normal
				if( Hash::get( $params, 'find' ) ) {
					$query = array(
						'fields' => array(
							"{$Model->alias}.{$Model->primaryKey}",
							"{$Model->alias}.{$Model->displayField}"
						),
						'order' => array(
							"{$Model->alias}.name ASC"
						)
					);

					if( !empty( $foreignKeyPrev ) ) {
						array_unshift( $query['order'], "{$Model->alias}.{$foreignKeyPrev} ASC" );
						$query['fields'] = array(
							"{$Model->alias}.{$Model->primaryKey}",
							"{$Model->alias}.{$Model->displayField}",
							"{$Model->alias}.{$foreignKeyPrev}",
						);
					}

					$results = (array)$Model->find( 'all', $query );

					if( !empty( $foreignKeyPrev ) ) {
						$results = Hash::combine(
							$results,
							array( '%s_%s', "{n}.{$Model->alias}.{$foreignKeyPrev}", "{n}.{$Model->alias}.{$Model->primaryKey}" ),
							"{n}.{$Model->alias}.{$Model->displayField}"
						);
					}
					else {
						$results = Hash::combine(
							$results,
							"{n}.{$Model->alias}.{$Model->primaryKey}",
							"{n}.{$Model->alias}.{$Model->displayField}"
						);
					}

					$options['Cer93Sujetcer93'][Inflector::underscore( $Model->alias ).'_id'] = $results;
				}

				// Valeurs "Autre"
				if( Hash::get( $params, 'autre' ) ) {
					$query = array(
						'conditions' => array(
							"{$Model->alias}.isautre" => 1
						)
					);
					$options['Autre']['Cer93Sujetcer93'][Inflector::underscore( $Model->alias ).'_id'] = array_keys( (array)$Model->find( 'list', $query ) );
				}

				//
				$foreignKeyPrev = Inflector::underscore( $Model->alias ).'_id';
			}

			return $options;
		}
	}
?>