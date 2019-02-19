<?php
	/**
	 * Code source de la classe Apre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Apre ...
	 *
	 * @package app.Model
	 */
	class Apre extends AppModel
	{
		public $name = 'Apre';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'numeroapre';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Option', 'WebrsaApre' );

		public $deepAfterFind = true;

		public $actsAs = array(
			'Allocatairelie',
			'Fichiermodulelie',
			'Gedooo.Gedooo',
			'StorablePdf' => array(
				'afterSave' => 'deleteAll'
			),
			'ModelesodtConditionnables' => array(
				93 => 'APRE/apre.odt'
			),
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'secteurprofessionnel' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'typedemandeapre' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'activitebeneficiaire' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'montantattribue' => array(
				'numeric' => array(
					'rule' => array( 'numeric' ),
					'message' => 'Veuillez entrer une valeur numérique.'
				),
				'comparison' => array(
					'rule' => array( 'comparison', '>=', 0 ),
					'message' => 'Veuillez entrer un nombre positif.'
				)
			),
			'structurereferente_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'nbheurestravaillees' => array(
				'comparison' => array(
					'rule' => array( 'comparison', '>=', 0 ),
					'message' => 'Veuillez saisir une valeur positive.',
					'allowEmpty' => true
				)
			)
		);

		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Referent' => array(
				'className' => 'Referent',
				'foreignKey' => 'referent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasOne = array(
			'Acccreaentr' => array(
				'className' => 'Acccreaentr',
				'foreignKey' => 'apre_id',
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
			'Acqmatprof' => array(
				'className' => 'Acqmatprof',
				'foreignKey' => 'apre_id',
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
			'Actprof' => array(
				'className' => 'Actprof',
				'foreignKey' => 'apre_id',
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
			'Amenaglogt' => array(
				'className' => 'Amenaglogt',
				'foreignKey' => 'apre_id',
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
			'Permisb' => array(
				'className' => 'Permisb',
				'foreignKey' => 'apre_id',
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
			'Formqualif' => array(
				'className' => 'Formqualif',
				'foreignKey' => 'apre_id',
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
			'Formpermfimo' => array(
				'className' => 'Formpermfimo',
				'foreignKey' => 'apre_id',
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
			'Locvehicinsert' => array(
				'className' => 'Locvehicinsert',
				'foreignKey' => 'apre_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);


		public $hasMany = array(
			'Montantconsomme' => array(
				'className' => 'Montantconsomme',
				'foreignKey' => 'apre_id',
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
			'Relanceapre' => array(
				'className' => 'Relanceapre',
				'foreignKey' => 'apre_id',
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
			'Fichiermodule' => array(
                'className' => 'Fichiermodule',
                'foreignKey' => false,
                'dependent' => false,
                'conditions' => array(
                    'Fichiermodule.modele = \'Apre\'',
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


		public $hasAndBelongsToMany = array(
			'Comiteapre' => array(
				'className' => 'Comiteapre',
				'joinTable' => 'apres_comitesapres',
				'foreignKey' => 'apre_id',
				'associationForeignKey' => 'comiteapre_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'ApreComiteapre'
			),
			'Etatliquidatif' => array(
				'className' => 'Etatliquidatif',
				'joinTable' => 'apres_etatsliquidatifs',
				'foreignKey' => 'apre_id',
				'associationForeignKey' => 'etatliquidatif_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'ApreEtatliquidatif'
			),
			'Pieceapre' => array(
				'className' => 'Pieceapre',
				'joinTable' => 'apres_piecesapre',
				'foreignKey' => 'apre_id',
				'associationForeignKey' => 'pieceapre_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'AprePieceapre'
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

			$departement = Configure::read( 'Cg.departement' );
			// Seulement pour le CG 93, lorsque l'on n'est pas en train d'importer des fixtures
			// TODO: mise en cache ?
			if( !( unittesting() && $this->useDbConfig === 'default' ) && $departement === 93 ) {
				$this->virtualFields['natureaide'] = $this->WebrsaApre->vfListeAidesLiees93( null );
			}
		}

		/**
		*
		*/

		public function afterFind( $results, $primary = false ) {
			parent::afterFind( $results, $primary );

			if( $this->deepAfterFind && !empty( $results ) && Set::check( $results, '0.Apre' ) ) {
				foreach( $results as $key => $result ) {
					if( isset( $result['Apre']['id'] ) ) {
						$results[$key]['Apre'] = Set::merge(
							$results[$key]['Apre'],
							$this->WebrsaApre->details( $result['Apre']['id'] )
						);
					}
					else if( isset( $result['Apre'][0]['id'] ) ) {
						foreach( $result['Apre'] as $key2 => $result2 ) {
							$results[$key]['Apre'][$key2] = Set::merge(
								$results[$key]['Apre'][$key2],
								$this->WebrsaApre->details( $result2['id'] )
							);
						}
					}
				}
			}

			return $results;
		}

		/**
		*
		*/

		public function beforeSave( $options = array() ) {
			$return = parent::beforeSave( $options );
			$statutapre = Set::classicExtract( $this->data, "{$this->alias}.statutapre" );

			if( $statutapre == 'C' ) {
				$valide = true;
				$nbNormalPieces = $this->WebrsaApre->nbrNormalPieces();
				foreach( $nbNormalPieces as $aide => $nbPieces ) {
					$key = 'Piece'.strtolower( $aide );
					if( isset( $this->data[$aide] ) && isset( $this->data[$key] ) && isset( $this->data[$key][$key] ) ) {
						$valide = ( count( (array)$this->data[$key][$key] ) == $nbPieces ) && $valide;
					}
				}
				$this->data['Apre']['etatdossierapre'] = ( $valide ? 'COM' : 'INC' );
			}
			else if( $statutapre == 'F' ){
				$this->data['Apre']['etatdossierapre'] = 'COM';
			}

			if( array_key_exists( $this->name, $this->data ) && array_key_exists( 'referent_id', $this->data[$this->name] ) ) {
				$this->data = Hash::insert( $this->data, "{$this->alias}.referent_id", suffix( Set::extract( $this->data, "{$this->alias}.referent_id" ) ) );
			}
			return $return;
		}

		/**
		*
		*/
		public function afterSave( $created, $options = array() ) {
			parent::afterSave( $created, $options );

			$details = $this->WebrsaApre->details( $this->id );

			$personne_id = Set::classicExtract( $this->data, "{$this->alias}.personne_id" );
			$statutapre = Set::classicExtract( $this->data, "{$this->alias}.statutapre" );

			if( !empty( $personne_id ) && ( $statutapre == 'C' ) && Configure::read( 'Cg.departement' ) == 66 ){
				$this->query( "UPDATE apres SET eligibiliteapre = 'O' WHERE apres.personne_id = {$personne_id} AND apres.etatdossierapre = 'COM' AND ( SELECT COUNT(contratsinsertion.id) FROM contratsinsertion WHERE contratsinsertion.personne_id = {$personne_id} ) > 0;" );

				$this->query( "UPDATE apres SET eligibiliteapre = 'N' WHERE apres.personne_id = {$personne_id} AND NOT ( apres.etatdossierapre = 'COM' AND ( SELECT COUNT(contratsinsertion.id) FROM contratsinsertion WHERE contratsinsertion.personne_id = {$personne_id} ) > 0 );" );
			}
			else if( Configure::read( 'Cg.departement' ) == 93 ){
				$this->query( "UPDATE apres SET eligibiliteapre = 'O' WHERE apres.personne_id = {$personne_id} AND apres.etatdossierapre = 'COM';" );
				$this->query( "UPDATE apres SET eligibiliteapre = 'N' WHERE apres.personne_id = {$personne_id} AND NOT ( apres.etatdossierapre = 'COM' );" );
			}
		}

		public function enums() {
			$options = parent::enums();
			$departement = (int)Configure::read( 'Cg.departement' );

			if( $departement === 93 ) {
				$options[$this->alias]['natureaide'] = $this->Option->natureAidesApres();
			}

			return $options;
		}

		/**
		 *	@deprecated since version 3.1	N'est plus utilisé
		 */
		public function sousRequeteMontanttotal() {
			$fieldTotal = array();
			foreach( $this->WebrsaApre->aidesApre as $modelAide ) {
				$fieldTotal[] = "\"{$modelAide}\".\"montantaide\"";
			}
			return '( COALESCE( '.implode( ', 0 ) + COALESCE( ', $fieldTotal ).', 0 ) )';
		}

		/**
		 * Retourne les données nécessaires à l'impression d'une APRE complémentaire
		 * pour le CG 93.
		 *
		 * Pont vers la méthode WebrsaApre::getDataForPdf permettant de
		 * fonctionner avec StorablePdfBehavior.
		 *
		 * @param integer $id L'id technique de l'orientation
		 * @param integer $user_id L'id technique de l'utilisateur effectuant l'impression
		 * @return array
		 */
		public function getDataForPdf( $id, $user_id = null ) {
			return $this->WebrsaApre->getDataForPdf( $id, $user_id );
		}

		/**
		 * Retourne le chemin vers le modèle odt utilisé pour l'APRE.
		 *
		 * Pont vers la méthode WebrsaApre::modeleOdt permettant de
		 * fonctionner avec StorablePdfBehavior.
		 *
		 * @param array $data Les données envoyées au modèle pour construire le PDF
		 * @return string
		 */
		public function modeleOdt( $data ) {
			return $this->WebrsaApre->modeleOdt( $data );
		}
	}
?>