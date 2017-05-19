<?php
	/**
	 * Fichier source de la classe Orientstruct.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Orientstruct ...
	 *
	 * @package app.Model
	 */

	class Orientstruct extends AppModel
	{
		public $name = 'Orientstruct';

		/**
		 * Les modèles utilisés par ce modèle, en plus des modèles présents dans
		 * les relations.
		 *
		 * @var array
		 */
		public $uses = array( 'Option', 'Transfertpdv93', 'WebrsaOrientstruct' );

		/**
		 * Les behaviors utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Allocatairelie',
			'Dependencies',
            'Postgres.PostgresAutovalidate',
			'Formattable' => array(
				'suffix' => array(
					'structurereferente_id',
					'referent_id',
					'structureorientante_id',
					'referentorientant_id'
				),
			),
			'Gedooo.Gedooo',
			// INFO: chargé à la volée avec la bonne configuration
			//'StorablePdf' => array( 'active' => ORIENTSTRUCT_STORABLE_PDF_ACTIVE ),
			'ModelesodtConditionnables' => array(
				66 => array(
					'Orientation/changement_referent_cgcg.odt',
					'Orientation/changement_referent_cgoa.odt',
					'Orientation/changement_referent_oacg.odt',
					'Orientation/orientationpe.odt',
					'Orientation/orientationpedefait.odt',
					'Orientation/orientationsociale.odt',
					'Orientation/orientationsocialeauto.odt',
                    'Orientation/orientationsystematiquepe.odt'
				)
			)
		);

		/**
		 * Règles de validation ne pouvant être déduites de la base de données,
		 * pour l'ensemble des départements (surcharge possible dans le constructeur).
		 *
		 * @var array
		 */
		public $validate = array(
			'structurereferente_id' => array(
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Structurereferente', 'Typeorient' ),
					'message' => 'La structure référente ne correspond pas au type d\'orientation',
				),
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'statut_orient', true, array( 'Orienté' ) ),
					'message' => 'Champ obligatoire',
				)
			),
			'referent_id' => array(
				'dependentForeignKeys' => array(
					'rule' => array( 'dependentForeignKeys', 'Referent', 'Structurereferente' ),
					'message' => 'Le référent n\'appartient pas à la structure référente',
				),
			),
			'date_valid' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'statut_orient', true, array( 'Orienté' ) ),
					'message' => 'Veuillez entrer une date valide'
				)
			),
			'statut_orient' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' )
				)
			),
			'typeorient_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'statut_orient', true, array( 'Orienté' ) ),
					'message' => 'Champ obligatoire',
				)
			),
		);

		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typeorient' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'typeorient_id',
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
			'Structureorientante' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structureorientante_id',
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
			),
			'Referentorientant' => array(
				'className' => 'Referent',
				'foreignKey' => 'referentorientant_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		public $hasMany = array(
			'Nonrespectsanctionep93' => array(
				'className' => 'Nonrespectsanctionep93',
				'foreignKey' => 'orientstruct_id',
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
			'Reorientationep93' => array(
				'className' => 'Reorientationep93',
				'foreignKey' => 'orientstruct_id',
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
			'Nonorientationproep58' => array(
				'className' => 'Nonorientationproep58',
				'foreignKey' => 'orientstruct_id',
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
			'Nonorientationproep58nv' => array(
				'className' => 'Nonorientationproep58',
				'foreignKey' => 'nvorientstruct_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Nonorientationproep66' => array(
				'className' => 'Nonorientationproep66',
				'foreignKey' => 'orientstruct_id',
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
			'Nonorientationproep93' => array(
				'className' => 'Nonorientationproep93',
				'foreignKey' => 'orientstruct_id',
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
			'Nonorientationproep93nv' => array(
				'className' => 'Nonorientationproep93',
				'foreignKey' => 'nvorientstruct_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Propoorientationcov58nv' => array(
				'className' => 'Propoorientationcov58',
				'foreignKey' => 'nvorientstruct_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Propoorientsocialecov58' => array(
				'className' => 'Propoorientsocialecov58',
				'foreignKey' => 'nvorientstruct_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Propononorientationprocov58' => array(
				'className' => 'Propononorientationprocov58',
				'foreignKey' => 'orientstruct_id',
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
			'Propononorientationprocov58nv' => array(
				'className' => 'Propononorientationprocov58',
				'foreignKey' => 'nvorientstruct_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Regressionorientationep58nv' => array(
				'className' => 'Regressionorientationep58',
				'foreignKey' => 'nvorientstruct_id',
				'dependent' => false,
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
					'Fichiermodule.modele = \'Orientstruct\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Bilanparcours66' => array(
				'className' => 'Bilanparcours66',
				'foreignKey' => 'orientstruct_id',
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
			'Saisinebilanparcoursep66nv' => array(
				'className' => 'Saisinebilanparcoursep66',
				'foreignKey' => 'nvorientstruct_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Defautinsertionep66' => array(
				'className' => 'Defautinsertionep66',
				'foreignKey' => 'orientstruct_id',
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
			'Sanctionep58' => array(
				'className' => 'Sanctionep58',
				'foreignKey' => 'orientstruct_id',
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
			'Reorientationep93nv' => array(
				'className' => 'Reorientationep93',
				'foreignKey' => 'nvorientstruct_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Nonorientationprocov58' => array(
				'className' => 'Nonorientationprocov58',
				'foreignKey' => 'orientstruct_id',
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
			'Nonorientationprocov58nv' => array(
				'className' => 'Nonorientationprocov58',
				'foreignKey' => 'nvorientstruct_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Regressionorientationcov58' => array(
				'className' => 'Regressionorientationcov58',
				'foreignKey' => 'orientstruct_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Regressionorientationcov58nv' => array(
				'className' => 'Regressionorientationcov58',
				'foreignKey' => 'nvorientstruct_id',
				'dependent' => false,
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

		public $hasAndBelongsToMany = array(
			'Serviceinstructeur' => array(
				'className' => 'Serviceinstructeur',
				'joinTable' => 'orientsstructs_servicesinstructeurs',
				'foreignKey' => 'orientstruct_id',
				'associationForeignKey' => 'serviceinstructeur_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'OrientstructServiceinstructeur'
			)
		);

		public $hasOne = array(
			'Nonoriente66' => array(
				'className' => 'Nonoriente66',
				'foreignKey' => 'orientstruct_id',
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
			'VxTransfertpdv93' => array(
				'className' => 'Transfertpdv93',
				'foreignKey' => 'vx_orientstruct_id',
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
			'NvTransfertpdv93' => array(
				'className' => 'Transfertpdv93',
				'foreignKey' => 'nv_orientstruct_id',
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

		public $virtualFields = array(
			'nbjours' => array(
				'type'      => 'integer',
				'postgres'  => 'DATE_PART( \'day\', NOW() - "%s"."date_impression" )'
			),
			// ---------------------
			'dernier' => array(
				'type'      => 'boolean',
				'postgres'  => '"%s"."id" IN (
					SELECT a.id FROM orientsstructs AS a
					WHERE a.personne_id = "%s"."personne_id"
					ORDER BY COALESCE( a.rgorient, \'0\') DESC,
						a.date_valid DESC,
						a.id DESC
					LIMIT 1)'
			),
			'dernier_oriente' => array(
				'type'      => 'boolean',
				'postgres'  => 'NOT EXISTS(
					SELECT * FROM orientsstructs AS a
					WHERE a.personne_id = "%s"."personne_id"
					AND a.statut_orient = \'Orienté\' AND "%s"."statut_orient" = \'Orienté\'
					AND "%s"."id" != a.id
					AND (
						a.date_valid > "%s"."date_valid"
						OR (a.date_valid = "%s"."date_valid" AND a.id > "%s"."id")
					) LIMIT 1)'
			),
			'premier_oriente' => array(
				'type'      => 'boolean',
				'postgres'  => 'NOT EXISTS(
					SELECT a.id FROM orientsstructs AS a
					WHERE a.personne_id = "%s"."personne_id"
					AND a.statut_orient = \'Orienté\' AND "%s"."statut_orient" = \'Orienté\'
					AND "%s"."id" != a.id
					AND (
						a.date_valid < "%s"."date_valid"
						OR (a.date_valid = "%s"."date_valid" AND a.id < "%s"."id")
					) LIMIT 1)'
			),
		);

		/**
		 * Surcharge du constructeur pour ajouter des règles de validation suivant
		 * le département connecté (66, 976) et la configuration de StorablePdf.
		 *
		 * @param mixed $id Set this ID for this model on startup, can also be an array of options, see above.
		 * @param string $table Name of database table to use.
		 * @param string $ds DataSource connection name.
		 */
		public function __construct( $id = false, $table = null, $ds = null ) {
			$active = !in_array( Configure::read( 'Cg.departement' ), array( 66, 976 ) );
			$this->actsAs = Hash::insert( $this->actsAs, 'StorablePdf.active', $active );

			parent::__construct( $id, $table, $ds );

			$departement = (int)Configure::read( 'Cg.departement' );

			if( $departement === 66 ) {
				$this->validate['structureorientante_id']['notEmptyIf'] = array(
					'rule' => array( 'notEmptyIf', 'statut_orient', true, array( 'Orienté' ) ),
					'message' => 'Veuillez choisir une structure orientante'
				);

				$this->validate['referentorientant_id']['notEmptyIf'] = array(
					'rule' => array( 'notEmptyIf', 'statut_orient', true, array( 'Orienté' ) ),
					'message' => 'Veuillez choisir un référent orientant'
				);

				$this->validate['referentorientant_id']['dependentForeignKeys'] = array(
					'rule' => array( 'dependentForeignKeys', 'Referentorientant', 'Structureorientante', 'Structurereferente' ),
					'message' => 'Le référent n\'appartient pas à la structure référente'
				);
			}
			else if( $departement === 976 ) {
				$this->validate['typeorient_id']['notEmptyIf'] = array(
					'rule' => array( 'notEmptyIf', 'statut_orient', true, array( 'Orienté', 'En attente', '' ) ),
					'message' => 'Champ obligatoire',
				);

				$this->validate['structurereferente_id']['notEmptyIf'] = array(
					'rule' => array( 'notEmptyIf', 'statut_orient', true, array( 'Orienté', 'En attente', '' ) ),
					'message' => 'Champ obligatoire',
				);
			}
		}

		/**
		 * Ajout du rang d'orientation à la sauvegarde, lorsqu'on passe en 'Orienté'.
		 * Mise à jour de l'origine suivant le statut et le rang de l'orientation.
		 *
		 * @param array $options
		 * @return boolean
		 */
		public function beforeSave( $options = array( ) ) {
			$success = parent::beforeSave( $options );

			$id = Hash::get( $this->data, "{$this->alias}.{$this->primaryKey}" );
			$statut_orient = Hash::get( $this->data, "{$this->alias}.statut_orient" );

			// Si on change le statut_orient de <> 'Orienté' en 'Orienté', alors, il faut changer le rang
			if( $statut_orient === 'Orienté' ) {
				$personne_id = Hash::get( $this->data, "{$this->alias}.personne_id" );

				// Change-t'on le statut ?
				if( !empty( $id ) ) {
					$query = array(
						'conditions' => array(
							"{$this->alias}.{$this->primaryKey}" => $id
						),
						'contain' => false
					);
					$tuple_pcd = $this->find( 'first', $query );
					if( $tuple_pcd[$this->alias]['statut_orient'] !== 'Orienté' ) {
						$this->data[$this->alias]['rgorient'] = ( $this->WebrsaOrientstruct->rgorientMax( $personne_id ) + 1 );
					}
					else {
						$this->data[$this->alias]['rgorient'] = $tuple_pcd[$this->alias]['rgorient'];
					}
				}
				// Nouvelle entrée
				else if( !empty( $personne_id ) ) {
					$this->data[$this->alias]['rgorient'] = ( $this->WebrsaOrientstruct->rgorientMax( $personne_id ) + 1 );
				}

				$origine = Hash::get( $this->data, "{$this->alias}.origine" );
				if( ( $this->data[$this->alias]['rgorient'] > 1 ) && !in_array( $origine, array( null, 'demenagement' ), true ) ) {
					$this->data[$this->alias]['origine'] = 'reorientation';
				}
			}
			// Il ne s'agit pas d'une orientation effective
			else {
				if( empty( $id ) ) {
					$this->data[$this->alias]['origine'] = null;
				}
				$this->data[$this->alias]['rgorient'] = null;
				$this->data[$this->alias]['date_valid'] = null;
			}

			if( isset( $this->data[$this->alias]['statut_orient'] ) && empty( $this->data[$this->alias]['statut_orient'] ) ) {
				$this->data[$this->alias]['origine'] = null;
			}

			return $success;
		}

		/**
		 * AfterSave.
		 *
		 * @param boolean $created
		 * @return boolean
		 */
		public function afterSave( $created ) {
			$return = parent::afterSave( $created );

			$return = $this->WebrsaOrientstruct->updateNonoriente66( $this->id ) && $return;

			return $return;
		}

		/**
		 * Récupère les données pour le PDF.
		 *
		 * Pont vers la méthode WebrsaOrientstruct::getDataForPdf permettant de
		 * fonctionner avec StorablePdfBehavior.
		 *
		 * @param integer $id L'id technique de l'orientation
		 * @param integer $user_id L'id technique de l'utilisateur effectuant l'impression
		 * @return array
		 */
		public function getDataForPdf( $id, $user_id = null ) {
			return $this->WebrsaOrientstruct->getDataForPdf( $id, $user_id );
		}

		/**
		 * Retourne le chemin relatif du modèle de document à utiliser pour
		 * l'enregistrement du PDF.
		 *
		 * Pont vers la méthode WebrsaOrientstruct::modeleOdt permettant de
		 * fonctionner avec StorablePdfBehavior.
		 *
		 * @param array $data Les données envoyées au modèle pour construire le PDF
		 * @return string
		 */
		public function modeleOdt( $data ) {
			return $this->WebrsaOrientstruct->modeleOdt( $data );
		}
	}
?>