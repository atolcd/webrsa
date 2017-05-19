<?php
	/**
	 * Code source de la classe Passagecommissionep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Passagecommissionep ...
	 *
	 * @package app.Model
	 */
	class Passagecommissionep extends AppModel
	{
		/**
		*
		*/

		public $recursive = -1;

		public $virtualFields = array(
			'chosen' => array(
				'type'      => 'boolean',
				'postgres'  => '(CASE WHEN "%s"."id" IS NOT NULL THEN true ELSE false END )'
			),
		);

		/**
		*
		*/

		public $actsAs = array(
			'Autovalidate2',
			'ValidateTranslate',
			'Formattable',
			'Enumerable' => array(
				'fields' => array(
					'etatdossierep'
				)
			)
		);

		/**
		*
		*/

		public $belongsTo = array(
			'Commissionep' => array(
				'className' => 'Commissionep',
				'foreignKey' => 'commissionep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Dossierep' => array(
				'className' => 'Dossierep',
				'foreignKey' => 'dossierep_id',
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
			)
		);

		public $hasMany = array(
			'Decisionreorientationep93' => array(
				'className' => 'Decisionreorientationep93',
				'foreignKey' => 'passagecommissionep_id',
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
			'Decisionnonorientationproep58' => array(
				'className' => 'Decisionnonorientationproep58',
				'foreignKey' => 'passagecommissionep_id',
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
			'Decisionregressionorientationep58' => array(
				'className' => 'Decisionregressionorientationep58',
				'foreignKey' => 'passagecommissionep_id',
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
			'Decisionsanctionep58' => array(
				'className' => 'Decisionsanctionep58',
				'foreignKey' => 'passagecommissionep_id',
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
			'Decisionsanctionrendezvousep58' => array(
				'className' => 'Decisionsanctionrendezvousep58',
				'foreignKey' => 'passagecommissionep_id',
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
			'Decisionnonrespectsanctionep93' => array(
				'className' => 'Decisionnonrespectsanctionep93',
				'foreignKey' => 'passagecommissionep_id',
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
			'Decisionnonorientationproep93' => array(
				'className' => 'Decisionnonorientationproep93',
				'foreignKey' => 'passagecommissionep_id',
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
			'Decisionsignalementep93' => array(
				'className' => 'Decisionsignalementep93',
				'foreignKey' => 'passagecommissionep_id',
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
			'Decisioncontratcomplexeep93' => array(
				'className' => 'Decisioncontratcomplexeep93',
				'foreignKey' => 'passagecommissionep_id',
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
			'Decisiondefautinsertionep66' => array(
				'className' => 'Decisiondefautinsertionep66',
				'foreignKey' => 'passagecommissionep_id',
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
			'Decisionsaisinebilanparcoursep66' => array(
				'className' => 'Decisionsaisinebilanparcoursep66',
				'foreignKey' => 'passagecommissionep_id',
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
			'Decisionsaisinepdoep66' => array(
				'className' => 'Decisionsaisinepdoep66',
				'foreignKey' => 'passagecommissionep_id',
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
			'Decisionnonorientationproep66' => array(
				'className' => 'Decisionnonorientationproep66',
				'foreignKey' => 'passagecommissionep_id',
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
		 * Sous-requête permettant d'obtenir l'id du dernier passage en commission d'EP (par-rapport à la
		 * date de la commission) d'un dossier d'EP.
		 *
		 * @param string $dossierepId Le champ de la requête principale correspondant
		 *	à la clé primaire de la table dossierseps
		 * @return string
		 */
		public function sqDernier( $dossierepId = 'Dossierep.id' ) {
			$passageAlias = Inflector::tableize( $this->alias );
			$commissionepAlias = Inflector::tableize( $this->Commissionep->alias );

			return $this->sq(
				array(
					'fields' => array(
						"{$passageAlias}.id"
					),
					'alias' => $passageAlias,
					'conditions' => array(
						"{$passageAlias}.dossierep_id = {$dossierepId}"
					),
					'joins' => array(
						array_words_replace(
							$this->join( 'Commissionep', array( 'type' => 'INNER' ) ),
							array(
								$this->alias => $passageAlias,
								$this->Commissionep->alias => $commissionepAlias
							)
						)
					),
					'order' => array( "{$commissionepAlias}.dateseance DESC", "{$commissionepAlias}.id DESC" ),
					'limit' => 1
				)
			);
		}

		/**
		 * Retourne l'id de la personne à laquelle est lié un enregistrement.
		 *
		 * @param integer $id L'id de l'enregistrement
		 * @return integer
		 */
		public function personneId( $id ) {
			$querydata = array(
				'fields' => array( "Dossierep.personne_id" ),
				'joins' => array(
					$this->join( 'Dossierep', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					"{$this->alias}.id" => $id
				),
				'recursive' => -1
			);

			$result = $this->find( 'first', $querydata );

			if( !empty( $result ) ) {
				return $result['Dossierep']['personne_id'];
			}
			else {
				return null;
			}
		}

		/**
		 * Permet de rechercher et de supprimer les courriers de décisions stockés
		 * dans la table pdfs.
		 */
		public function afterDelete() {
			$Pdf = ClassRegistry::init( 'Pdf' );
			$query = array(
				'fields' => array( 'Pdf.id' ),
				'contain' => false,
				'conditions' => array(
					'Pdf.modele' => $this->alias,
					'Pdf.fk_value' => $this->id
				)
			);
			$stored = $Pdf->find( 'all', $query );
			if( !empty( $stored ) ) {
				$ids = Hash::extract( $stored, '{n}.Pdf.id' );
				$success = $Pdf->deleteAll( array( 'Pdf.id' => $ids ) );

				if( $success !== true ) {
					$message = sprintf( 'Impossible de supprimer les entrées %s de la table pdfs.', implode( ',', $ids ) );
					throw new InternalErrorException( $message );
				}
			}

			parent::afterDelete();
		}
	}
?>