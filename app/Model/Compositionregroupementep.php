<?php
	/**
	 * Code source de la classe Compositionregroupementep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Compositionregroupementep ...
	 *
	 * @package app.Model
	 */
	class Compositionregroupementep extends AppModel
	{
		public $name = 'Compositionregroupementep';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		public $belongsTo = array(
			'Fonctionmembreep' => array(
				'className' => 'Fonctionmembreep',
				'foreignKey' => 'fonctionmembreep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Regroupementep' => array(
				'className' => 'Regroupementep',
				'foreignKey' => 'regroupementep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		public function compositionValide( $regroupementep_id, $membreseps ) {
			$return = true;
			$error = null;

			$compositionregroupementep = $this->Regroupementep->find(
				'first',
				array(
					'conditions' => array(
						'Regroupementep.id' => $regroupementep_id
					),
					'contain' => array(
						'Compositionregroupementep'
					)
				)
			);

			$membreChoisi = false;
			$compoCree = false;
			$uneCompoObligatoire = false;
			foreach( $compositionregroupementep['Compositionregroupementep'] as $compo ) {
				$compoCree = true;

				if( $compo['obligatoire'] == 1 ) {
					$uneCompoObligatoire = true;
					$membresFonction = $this->Fonctionmembreep->find(
						'first',
						array(
							'conditions' => array(
								'Fonctionmembreep.id' => $compo['fonctionmembreep_id']
							),
							'contain' => array(
								'Membreep'
							)
						)
					);

					foreach ( $membresFonction['Membreep'] as $membre ) {
						if ( !empty( $membreseps ) && in_array( $membre['id'], $membreseps ) ) {
							if( $compo['obligatoire'] == 1 ) {
								$membreChoisi = true;
							}
						}
					}
				}
			}

			if ( !$membreChoisi && $compoCree && $uneCompoObligatoire ) {
				$return = false;
				$error = "obligatoire";
			}
			elseif ( $compositionregroupementep['Regroupementep']['nbminmembre'] > 0 && count( $membreseps ) < $compositionregroupementep['Regroupementep']['nbminmembre'] ) {
				$return = false;
				$error = 'nbminmembre';
			}
			elseif ( $compositionregroupementep['Regroupementep']['nbmaxmembre'] > 0 && count( $membreseps ) > $compositionregroupementep['Regroupementep']['nbmaxmembre'] ) {
				$return = false;
				$error = 'nbmaxmembre';
			}

			return array( 'check' => $return, 'error' => $error );
		}

		public function listeFonctionsObligatoires( $regroupementep_id ) {
			$compositionregroupementep = $this->find(
				'all',
				array(
					'conditions' => array(
						'Compositionregroupementep.regroupementep_id' => $regroupementep_id
					),
					'contain' => array(
						'Fonctionmembreep'
					)
				)
			);

			$fonctionsObligatoires = array();
			foreach( $compositionregroupementep as $compo ) {
				if ( $compo['Compositionregroupementep']['obligatoire'] == 1 ) {
					$fonctionsObligatoires[] = $compo['Fonctionmembreep']['name'];
				}
			}

			return $fonctionsObligatoires;
		}

	}
?>