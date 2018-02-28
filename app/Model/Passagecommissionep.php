<?php
	/**
	 * Code source de la classe Passagecommissionep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

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
		public $virtualFields = array(
			'chosen' => array(
				'type'      => 'boolean',
				'postgres'  => '(CASE WHEN "%s"."id" IS NOT NULL THEN true ELSE false END )'
			)
		);

		/**
		*
		*/

		public $actsAs = array(
			'Allocatairelie' => array(
				'joins' => array( 'Dossierep' )
			),
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
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

		/**
		 * Gestion de l'heure de rendez-vous pour les participants à l'EP
		 *
		 * Données configurable dans les .inc :
		 *  - commissionep.heure.debut.standard
		 *  - commissionep.heure.ecart.minute
		 *
		 * @param int $commissionep_id L'identifiant de la commission d'EP
		 * @return array Les allocataires participant à l'EP
		 */
		public function gereHeureCommissionEp ( $commissionep_id ) {
			// Récupération de la date de la séance
			$commissionep = $this->Commissionep->find(
				'first',
				array(
					'conditions' => array(
						'Commissionep.id' => $commissionep_id
					)
				)
			);

			try {
				$dateseance = new DateTime ($commissionep['Commissionep']['dateseance']);
			} catch (Exception $e) {
				$dateseance = new DateTime ('2000-01-01 '.Configure::read( 'commissionep.heure.debut.standard' ).':00');
			}

			if ($dateseance->format ('H:i:00') == '00:00:00') {
				$dateseance = new DateTime ('2000-01-01 '.Configure::read( 'commissionep.heure.debut.standard' ).':00');
			}

			// Récupération des allocataires participant
			$passagecommissioneps = $this->find(
				'all',
				array(
					'conditions' => array(
					'Passagecommissionep.commissionep_id' => $commissionep_id
				),
				'joins' => array(
					array(
						'table' => 'dossierseps',
						'alias' => 'Dossierep',
						'type' => 'RIGHT',
						'conditions' => array(
							'Passagecommissionep.dossierep_id = Dossierep.id',
						)
					),
					array(
					'table' => 'personnes',
					'alias' => 'Personne',
					'type' => 'RIGHT',
					'conditions' => array(
						'Dossierep.personne_id = Personne.id',
						)
					),
				),
				'order' => array (
					'Personne.nom' => 'ASC',
					'Personne.prenom' => 'ASC',
					),
				)
			);
			$nbAllocataireParLot = Configure::read( 'commissionep.heure.personnes.convoquees' );
			$numeroAllocataireLot = 1;

			// Enregistrement des résultats en bdd avec l'écart en config.
			$success = true;
			$nbPassagecommissioneps = count ($passagecommissioneps);

			// Dans le cas où une pause méridienne a été définie en config.
			if (is_array (Configure::read( 'commissionep.heure.debut.pause.meridienne' ))) {
				$heureDebPause = Configure::read( 'commissionep.heure.debut.pause.meridienne' );
				$heureFinPause = Configure::read( 'commissionep.heure.fin.pause.meridienne' );

				$pauseMeridienneDeb = new DateTime ();
				$pauseMeridienneDeb->setTimestamp($dateseance->getTimestamp ());
				$pauseMeridienneDeb->setTime ($heureDebPause['heure'], $heureDebPause['minute'], 0);
				$pauseMeridienneFin = new DateTime ();
				$pauseMeridienneFin->setTimestamp($dateseance->getTimestamp ());
				$pauseMeridienneFin->setTime ($heureFinPause['heure'], $heureFinPause['minute'], 0);
			}

			for ($i = 0; $i < $nbPassagecommissioneps; $i++) {
				$passagecommissioneps[$i]['Passagecommissionep']['heureseance'] = $dateseance->format ('H:i:00');
				$passagecommissioneps[$i] = $passagecommissioneps[$i]['Passagecommissionep'];

				if ($nbAllocataireParLot == $numeroAllocataireLot++) {
					$dateseance->add (new DateInterval('PT'. Configure::read( 'commissionep.heure.ecart.minute' ) .'M'));
					$numeroAllocataireLot = 1;
				}

				// On teste si la date de séance tombera pendant la pause méridienne.
				if (is_array (Configure::read( 'commissionep.heure.debut.pause.meridienne' ))) {
					$diffDeb = $dateseance->diff ($pauseMeridienneDeb);
					$diffFin = $dateseance->diff ($pauseMeridienneFin);

					if (((($diffDeb->h > 0 || $diffDeb->i > 0) && $diffDeb->invert == 1) // Après l'heure de début
						|| ($diffDeb->h == 0 && $diffDeb->i == 0 && $diffDeb->invert == 0)) // Égal à l'heure de début
						&& (($diffFin->h > 0 || $diffFin->i > 0) && $diffFin->invert == 0)) { // Avant l'heure de fin
						$dateseance = $pauseMeridienneFin;
					}
				}
			}

			return $passagecommissioneps;
		}
	}
?>