<?php
	/**
	 * Code source de la classe ApreEtatliquidatif.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ApreEtatliquidatif ...
	 *
	 * @package app.Model
	 */
	class ApreEtatliquidatif extends AppModel
	{

		public $name = 'ApreEtatliquidatif';
		public $actsAs = array(
			'Frenchfloat' => array(
				'fields' => array(
					'montantattribue',
				)
			),
			'Gedooo.Gedooo',
			'ModelesodtConditionnables' => array(
				93 => array(
					'APRE/apreforfaitaire.odt',
					'APRE/Paiement/paiement_tiersprestataire.odt',
					'APRE/Paiement/paiement_formation_beneficiaire.odt',
					'APRE/Paiement/paiement_horsformation_beneficiaire.odt',
				)
			)
		);
		public $validate = array(
			'montantattribue' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
				array(
					'rule' => 'numeric',
					'message' => 'Valeur numérique seulement'
				)
			)
		);
		public $belongsTo = array(
			'Apre' => array(
				'className' => 'Apre',
				'foreignKey' => 'apre_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Etatliquidatif' => array(
				'className' => 'Etatliquidatif',
				'foreignKey' => 'etatliquidatif_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Before Validate
		 */
		public function beforeValidate( $options = array( ) ) {
			if( $return = parent::beforeValidate( $options ) ) {
				$apre_id = Set::classicExtract( $this->data, "{$this->alias}.apre_id" );
				$qd_apre = array(
					'conditions' => array(
						'Apre.id' => $apre_id
					),
					'fields' => null,
					'order' => null,
					'recursive' => -1
				);
				$apre = $this->Apre->find( 'first', $qd_apre );

				// Déjà versé dans les autres états liquidatifs
				$montantdejaverse = Set::classicExtract( $apre, 'Apre.montantdejaverse' );
				$nbpaiementsouhait = Set::classicExtract( $apre, 'Apre.nbpaiementsouhait' );//FIXME: parfois il est dans le précédent ?
				// Montant attribué dans ce comité
				$montantattribue = Set::classicExtract( $this->data, "{$this->alias}.montantattribue" );
				// Montant attribué par le comité ou pour l'APRE forfaitaire
				$montantaverser = Set::classicExtract( $this->data, "{$this->alias}.montantaverser" );
				// Nombre d'étatsliquidatifs dans lequel cette apre est déjà passé
				$etatliquidatif_id = Set::classicExtract( $this->data, "{$this->alias}.etatliquidatif_id" );
				$nbrPassagesEffectues = $this->find(
						'count', array(
					'conditions' => array(
						"{$this->alias}.apre_id" => $apre_id,
						"{$this->alias}.etatliquidatif_id <>" => $etatliquidatif_id,
					),
					'contain' => false
						)
				);

				$nbrPassagesSubsequents = max( 0, ( $nbrPassagesEffectues - 1 - $nbrPassagesEffectues ) );
				$montantversable = max( 0, ( $montantaverser - $montantdejaverse - $nbrPassagesSubsequents ) );

				// Montant positif
				if( $montantattribue < 0 ) {
					$this->invalidate( 'montantattribue', "Le montant doit être positif" );
				}

				// Montant maximum: montant à verser
				if( $montantattribue > $montantversable ) {
					$this->invalidate( 'montantattribue', "Montant trop élevé (max: {$montantversable})" );
				}
			}

			return $return;
		}

		/**
		 * Retourne le PDF concernant le passage d'une APRE dans un état liquidatif.
		 *
		 * @param string $typeapre Le type d'APRE (forfaitaire ou complementaire)
		 * @param integer $apre_id L'id de l'APRE
		 * @param integer $etatliquidatif_id L'id de l'état liquidatif
		 * @param string $dest Le destinataire du courrier (beneficiaire ou tiersprestataire)
		 * @param integer $user_id L'id de l'utilisateur ayant demandé l'impression
		 * @return string
		 */
		public function getDefaultPdf( $typeapre, $apre_id, $etatliquidatif_id, $dest, $user_id ) {
			$method = 'qdDonneesApre'.Inflector::camelize( $typeapre ).'Gedooo';

			$querydata = $this->Etatliquidatif->{$method}();
			$querydata = Set::merge(
							$querydata, array(
						'conditions' => array(
							'Etatliquidatif.id' => $etatliquidatif_id,
							'Apre.id' => $apre_id
						)
							)
			);

			$deepAfterFind = $this->Etatliquidatif->Apre->deepAfterFind;
			$this->Etatliquidatif->Apre->deepAfterFind = false;
			$apre = $this->Etatliquidatif->find( 'first', $querydata );
			$this->Etatliquidatif->Apre->deepAfterFind = $deepAfterFind;

			/// Récupération de l'utilisateur
			$user = ClassRegistry::init( 'User' )->find(
					'first', array(
				'conditions' => array(
					'User.id' => $user_id
				),
				'contain' => false
					)
			);
			$apre['User'] = $user['User'];

			// Traductions
			$Option = ClassRegistry::init( 'Option' );
			$options = array(
				'Apre' => array(
					'natureaide' => $Option->natureAidesApres()
				),
				'Personne' => array(
					'qual' => $Option->qual()
				),
				'Tiersprestataireapre' => array(
					'typevoie' => $Option->typevoie()
				)
			);

			// Choix du modèle de document à utiliser
			if( $typeapre == 'forfaitaire' ) {
				$modeleodt = 'APRE/apreforfaitaire.odt';
			}
			else if( $typeapre == 'complementaire' && $dest == 'tiersprestataire' ) {
				$modeleodt = 'APRE/Paiement/paiement_tiersprestataire.odt';
			}
			else if( $typeapre == 'complementaire' && $dest == 'beneficiaire' ) {
				if( !empty( $apre['Apre']['nomaide'] ) && in_array( $apre['Apre']['nomaide'], $this->Etatliquidatif->Apre->WebrsaApre->modelsFormation ) ) {
					$typeformation = 'formation';
				}
				else {
					$typeformation = 'horsformation';
				}

				$modeleodt = 'APRE/Paiement/paiement_'.$typeformation.'_beneficiaire.odt';
			}

			return $this->ged(
							$apre, $modeleodt, false, $options
			);
		}

		/**
		 * Retourne le PDF concernant le passage d'une APRE dans un état liquidatif.
		 *
		 * @param string $typeapre
		 * @param integer $etatliquidatif_id
		 * @param string $dest
		 * @param integer $user_id
		 * @param integer $page
		 * @return string
		 */
		public function getDefaultCohortePdf( $typeapre, $etatliquidatif_id, $dest, $user_id, $page, $limit, $sort, $direction ) {
			$method = 'qdDonneesApre'.Inflector::camelize( $typeapre ).'Gedooo';
			$querydata = $this->Etatliquidatif->{$method}();
			$querydata = Set::merge(
							$querydata, array(
						'conditions' => array(
							'Etatliquidatif.id' => $etatliquidatif_id,
						),
						'limit' => $limit
							)
			);

			if( !empty( $sort ) && !empty( $direction ) ) {
				$querydata['order'] = "{$sort} {$direction}";
			}

			$User = ClassRegistry::init( 'User' );
			$dbo = $User->getDataSource( $User->useDbConfig );

			$querydata['fields'] = Set::merge( $querydata['fields'], $User->fields() );
			$querydata['joins'][] = array(
				'table' => $dbo->fullTableName( $User, true, false ),
				'alias' => $User->alias,
				'type' => 'LEFT OUTER',
				'conditions' => array(
					'User.id' => $user_id
				)
			);

			$deepAfterFind = $this->Etatliquidatif->Apre->deepAfterFind;
			$this->Etatliquidatif->Apre->deepAfterFind = false;

			$querydata['offset'] = ( ( $page ) <= 1 ? 0 : ( $querydata['limit'] * ( $page - 1 ) ) );
			$apres = $this->Etatliquidatif->find( 'all', $querydata );

			$this->Etatliquidatif->Apre->deepAfterFind = $deepAfterFind;

//			if( $typeapre == 'forfaitaire' ) {
			$key = 'forfaitaire';
			$modeleodt = 'APRE/apreforfaitaire.odt';
			/* }
			  else if( $typeapre == 'complementaire' && $dest == 'tiersprestataire' ) {
			  $key = 'etatliquidatif_tiers';
			  $modeleodt = 'APRE/Paiement/paiement_tiersprestataire.odt';
			  $nomfichierpdf = sprintf( 'paiement_tiersprestataire-%s.pdf', date( 'Y-m-d' ) );
			  }
			  else if( $typeapre == 'complementaire' && $dest == 'beneficiaire' ) {
			  $key = 'apreforfaitaire';
			  $modeleodt = 'APRE/Paiement/paiement_'.$typeformation.'_beneficiaire.odt';
			  $nomfichierpdf = sprintf( 'paiement_'.$typeformation.'_beneficiaire-%s.pdf', date( 'Y-m-d' ) );
			  } */

			// Traductions
			$Option = ClassRegistry::init( 'Option' );
			$options = array(
				'Apre' => array(
					'natureaide' => $Option->natureAidesApres()
				),
				'Personne' => array(
					'qual' => $Option->qual()
				),
				'Tiersprestataireapre' => array(
					'typevoie' => $Option->typevoie()
				)
			);

			return $this->ged(
							array( $key => $apres ), $modeleodt, true, $options
			);
		}

	}
?>
