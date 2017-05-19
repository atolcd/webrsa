<?php
	/**
	 * Code source de la classe ApreComiteapre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ApreComiteapre ...
	 *
	 * @package app.Model
	 */
	class ApreComiteapre extends AppModel
	{
		public $name = 'ApreComiteapre';

		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'decisioncomite' => array( 'type' => 'decisioncomite', 'domain' => 'apre' ),
					'recoursapre' => array( 'type' => 'recoursapre', 'domain' => 'apre' ),
				)
			),
			'Frenchfloat' => array( 'fields' => array( 'montantattribue' ) ),
			'ModelesodtConditionnables' => array(
				93 => array(
					'APRE/DecisionComite/Accord/AccordFormationbeneficiaire.odt',
					'APRE/DecisionComite/Accord/AccordHorsFormationbeneficiaire.odt',
					'APRE/DecisionComite/Accord/Accordreferent.odt',
					'APRE/DecisionComite/Accord/AccordVersementtiers.odt',
					'APRE/DecisionComite/Ajournement/Ajournementbeneficiaire.odt',
					'APRE/DecisionComite/Ajournement/Ajournementreferent.odt',
					'APRE/DecisionComite/Refus/Refusbeneficiaire.odt',
					'APRE/DecisionComite/Refus/Refusreferent.odt',
				)
			)
		);

		public $validate = array(
			'decisioncomite' => array(
				array(
					'rule'      => array( 'inList', array( 'AJ', 'ACC', 'REF' ) ),
					'message'   => 'Veuillez choisir une valeur.',
					'allowEmpty' => false
				)
			),
			'montantattribue' => array(
				array(
					'rule' => 'numeric',
					'message' => 'Veuillez entrer une valeur numérique.',
					'allowEmpty' => false
				)
			),
		);

		public $belongsTo = array(
			'Apre' => array(
				'className' => 'Apre',
				'foreignKey' => 'apre_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Comiteapre' => array(
				'className' => 'Comiteapre',
				'foreignKey' => 'comiteapre_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		*   Before Save pour remettre à zéro les montants attribués par le comité si la décision est passée en Refus
		**/

		public function beforeSave( $options = array() ) {
			$return = parent::beforeSave( $options );
			//FIXME: a mettre dans le beforeValidate
			if( isset( $this->data[$this->name]['decisioncomite'] ) ) {
				if( $this->data[$this->name]['decisioncomite'] != 'ACC' ) {
					$this->data[$this->name]['montantattribue'] = null;
				}
				else {
					$apre = $this->Apre->read( array( 'id', $this->Apre->sousRequeteMontantTotal().' AS "Apre__montantaverser"' ), $this->data[$this->name]['apre_id'] );


					$montantattribue = Set::classicExtract( $this->data, "{$this->alias}.montantattribue" );
					if( ( Set::check( $montantattribue ) == false ) && !is_numeric( $montantattribue ) ) {
						$this->invalidate( 'montantattribue', 'Veuillez saisir un montant' );
					}

					/// INFO: devrait fonctionner avec comparison, mais ce n'est pas le cas
					$montantpositif = ( $montantattribue >= 0 );
					if( !$montantpositif ) {
						$this->invalidate( 'montantattribue', 'Veuillez entrer un nombre positif' );
					}

					$return = ( $return && $montantpositif );
				}
			}

			return $return;
		}

		/**
		* Sous-requête permettant d'obtenir l'id du dernier passage en comité
		* (par-rapport à la date et à l'heure du comité) pour une APRE donnée.
		*
		* @param string $field Le nom du champ contanant l'id de l'APRE
		* @param mixed $conditions Conditions supplémentaire à insérer dans la sous-requête
		* @return string Une sous-requête SQL, suivant le driver utilisé
		*/

		public function sqDernierComiteApre( $field = 'Apre.id', $conditions = array() ) {
			$dbo = $this->getDataSource( $this->useDbConfig );

			$conditions = Set::merge(
				array( "apres_comitesapres.apre_id = {$field}" ),
				(array) $conditions
			);

			return $this->sq(
				array(
					'alias' => 'apres_comitesapres',
					'fields' => array( 'apres_comitesapres.id' ),
					'joins' => array(
						array(
							'table' => $dbo->fullTableName( $this->Comiteapre, true, false ),
							'alias' => 'comitesapres',
							'type' => 'INNER',
							'conditions' => array(
								'apres_comitesapres.comiteapre_id = comitesapres.id'
							)
						)
					),
					'conditions' => $conditions,
					'order' => array(
						'comitesapres.datecomite DESC',
						'comitesapres.heurecomite DESC'
					),
					'limit' => 1
				)
			);
		}

		/**
		 * Retourne le PDF de décision du passage d'une APRE en comité APRE, pour un destinataire donné,
		 * et contenant les données de l'utilisateur connecté.
		 *
		 * @param integer $id L'id de l'entrée de décision d'une APRE en comité APRE
		 * @param string $dest Le destinataire de l'impression (beneficiaire, referent, tiers)
		 * @param integer $user_id L'id de l'utilisateur qui demande l'impression
		 * @return string
		 */
		public function getNotificationPdf( $id, $dest, $user_id ) {
			$querydata = array(
				'fields' => array_merge(
					$this->fields(),
					$this->Apre->fields(),
					$this->Apre->Personne->fields(),
					$this->Apre->Structurereferente->fields(),
					$this->Apre->Referent->fields(),
					array(
						'( '.$this->Apre->WebrsaApre->sqApreNomaide().' ) AS "Apre__Natureaide"'
					)
				),
				'conditions' => array(
					'ApreComiteapre.id' => $id,
					'OR' => array(
						'Adressefoyer.id IS NULL',
						'Adressefoyer.id IN ( '.$this->Apre->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )',
					)
				),
				'contain' => false,
				'joins' => array(
					$this->join( 'Apre', array( 'type' => 'INNER' ) ),
					$this->join( 'Comiteapre', array( 'type' => 'INNER' ) ),
					$this->Apre->join( 'Personne', array( 'type' => 'INNER' ) ),
					$this->Apre->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
					$this->Apre->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
				)
			);

			$aidesApre = $this->Apre->WebrsaApre->aidesApre;
			sort( $aidesApre );
			foreach( $aidesApre as $aide ) {
				$querydata['fields'] = array_merge( $querydata['fields'], $this->Apre->{$aide}->fields() );
				$querydata['joins'][] = $this->Apre->join( $aide, array( 'type' => 'LEFT OUTER' ) );
			}

			$querydata['fields'] = array_merge(
				$querydata['fields'],
				$this->Comiteapre->fields(),
				$this->Apre->Personne->Foyer->Adressefoyer->Adresse->fields()
			);

			$querydata['joins'][] = $this->Apre->Personne->join( 'Foyer', array( 'type' => 'INNER' ) );
			$querydata['joins'][] = $this->Apre->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) );
			$querydata['joins'][] = $this->Apre->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) );

			// Tiers prestataire
			$conditionsJoinTiersprestataireApre = array();
			foreach( $this->Apre->WebrsaApre->modelsFormation as $modelFormation ) {
				$conditionsJoinTiersprestataireApre[] = array(
					'Tiersprestataireapre.aidesliees' => $modelFormation,
					"{$modelFormation}.tiersprestataireapre_id IS NOT NULL",
					"Tiersprestataireapre.id = {$modelFormation}.tiersprestataireapre_id"
				);
			}
			$querydata['joins'][] = array(
				'table'      => 'tiersprestatairesapres',
				'alias'      => 'Tiersprestataireapre',
				'type'       => 'LEFT OUTER',
				'foreignKey' => false,
				'conditions' => array( 'OR' => $conditionsJoinTiersprestataireApre )
			);
			$querydata['fields'] = array_merge(
				$querydata['fields'],
				ClassRegistry::init( 'Tiersprestataireapre' )->fields()
			);

			$deepAfterFind = $this->Apre->deepAfterFind;
			$this->Apre->deepAfterFind = false;
			$apre = $this->find( 'first', $querydata );
			$this->Apre->deepAfterFind = $deepAfterFind;

			if( empty( $apre ) ) {
				$this->cakeError( 'error404' );
			}

			// Récupération de la personne chargée du suivi
			$Suiviaideapre = ClassRegistry::init( 'Suiviaideapre' );
			$dataperssuivi = $Suiviaideapre->find(
				'first',
				array(
					'contain' => false,
					'joins' => array(
						$Suiviaideapre->join( 'Suiviaideapretypeaide', array( 'type' => 'INNER' ) )
					),
					'conditions' => array(
						'Suiviaideapretypeaide.typeaide' => $apre['Apre']['Natureaide'] // FIXME: traduction
					)
				)
			);
			$apre['Dataperssuivi'] = array();
			if( !empty( $dataperssuivi ) ) {
				foreach( $dataperssuivi['Suiviaideapre'] as $key => $value ) {
					$apre['Dataperssuivi']["{$key}suivi"] = $value;
				}
			}

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
			$apre['User'] = $user['User'];

			// Choix du modèle de document
			if( in_array( $apre['Apre']['Natureaide'], $this->Apre->WebrsaApre->modelsFormation ) ) {
				$typeformation = 'Formation';
			}
			else {
				$typeformation = 'HorsFormation';
			}

			$options = $this->enums();
			$typedecision = Set::enum( Set::classicExtract( $apre, 'ApreComiteapre.decisioncomite' ), $options['ApreComiteapre']['decisioncomite'] );

			///Paramètre nécessaire pour connaitre le type de paiement au tiers (total/ plusieurs versements )
			$typepaiement = 'Versement';

			$modeleodt = null;
			if( ( $dest == 'beneficiaire' || $dest == 'referent' || $dest == 'tiers' ) && ( $typedecision == 'Refus' || $typedecision == 'Ajournement' ) ) {
				$modeleodt = 'APRE/DecisionComite/'.$typedecision.'/'.$typedecision.$dest.'.odt';
			}
			else if( $dest == 'beneficiaire' && $typedecision == 'Accord' ) {
				$modeleodt = 'APRE/DecisionComite/'.$typedecision.'/'.$typedecision.$typeformation.$dest.'.odt';
			}
			else if( $dest == 'referent' && $typedecision == 'Accord' ) {
				$modeleodt = 'APRE/DecisionComite/'.$typedecision.'/'.$typedecision.$dest.'.odt';
			}
			else if( $dest == 'tiers' && !empty( $typedecision ) ) {
				$modeleodt = 'APRE/DecisionComite/'.$typedecision.'/'.$typedecision.$typepaiement.$dest.'.odt';
			}

			// Traductions
			$Option = ClassRegistry::init( 'Option' );
			// Traduction des noms de table en libellés de l'aide
			$apre['Apre']['Natureaide'] = Set::enum( $apre['Apre']['Natureaide'], $Option->natureAidesApres() );
			$apre['Apre']['Natureaide'] = "  - {$apre['Apre']['Natureaide']}\n";

			$options['Personne'] = array( 'qual' => $Option->qual() );
			$options['Referent']['qual'] = $options['Personne']['qual'];
			$options['Dataperssuivi']['qualsuivi'] = $options['Personne']['qual'];

			$typevoie = $Option->typevoie();
			$options['type']['voie'] = $typevoie;
			$options['Structurereferente']['type_voie'] = $typevoie;
			$options['Tiersprestataireapre']['typevoie'] = $typevoie;

			return $this->Apre->ged( $apre, $modeleodt, false, $options );
		}
	}
?>
