<?php
	/**
	 * Code source de la classe Cer93Helper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * La classe Cer93Helper ...
	 *
	 * @package app.View.Helper
	 */
	class Cer93Helper extends AppHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			// TODO: utiliser ceux de Default3 ?
			'Html',
			'Form'
		);

		/**
		 * Ici, c'est pour l'action edit, lorsque des enregistrements on été désactivés
		 * @deprecated
		 */
		public function sujets( array $data, array $params = array() ) {
			// TODO: Params avec les paths de chaque champ, pour pouvoir l'utiliser dans la vue
			$params += array(
				'hidden' => false,
				'domain' => $this->request->params['controller'],
				'visibleFields' => array(
					'Sujetcer93.name' => 'Cer93Sujetcer93.commentaireautre',
					'Soussujetcer93.name' => 'Cer93Sujetcer93.autresoussujet',
					'Valeurparsoussujetcer93.name' => 'Cer93Sujetcer93.autrevaleur'
				),
				'hiddenFields' => array(
					'Cer93Sujetcer93.sujetcer93_id',
					'Cer93Sujetcer93.commentaireautre',
					'Cer93Sujetcer93.soussujetcer93_id',
					'Cer93Sujetcer93.autresoussujet',
					'Cer93Sujetcer93.valeurparsoussujetcer93_id',
					'Cer93Sujetcer93.autrevaleur'
				),
				'hiddenPrefix' => 'Sujetcer93.Sujetcer93',
				'id' => null
			);

			$return = '';

			if( !empty( $data ) ) {
				$trs = '';
				foreach( $data as $index => $record ) {
					// Champs visibles
					$visible = array();
					foreach( $params['visibleFields'] as $path => $commentairePath ) {
						$name = Hash::get( $record, $path );
						$commentaire = Hash::get( $record, $commentairePath );
						if( !empty( $commentaire ) ){
							$name = "{$name} : {$commentaire}";
						}
						$visible[] = h( $name );
					}

					// Champs cachés si besoin
					$hidden = '';
					if( $params['hidden'] === true ) {
						foreach( $params['hiddenFields'] as $path ) {
							list( $modelName, $fieldName ) = model_field( $path );
							$value = Hash::get( $record, $path );
							$hidden .= $this->Form->input( "{$params['hiddenPrefix']}.{$index}.{$fieldName}", array( 'type' => 'hidden', 'value' => $value, 'id' => null ) );
						}

						$visible[0] = $hidden.$visible[0];
					}

					// Ligne du tableau
					$trs .= $this->Html->tableCells(
						$visible,
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				}
				// Corps du tableau
				$tbody = $this->Html->tag( 'tbody', $trs );

				// En-tête du tableau
				$headers = array();
				foreach( array_keys( $params['visibleFields'] ) as $path ) {
					$headers[] = __d( $params['domain'], $path );
				}
				$thead = $this->Html->tag( 'thead', $this->Html->tableHeaders( $headers ) );

				$return = $this->Html->tag( 'table', $thead.$tbody, array( 'id' => $params['id'] ) );
			}

			return $return;
		}

		/**
		 * TODO: vérifier l'affichage avec "Autre, ..."
		 * @deprecated
		 */
		public function sujetspcds( array $data, array $params = array() ) {
			$test = array();
			foreach( $data['Sujetcer93'] as $index => $sujetcer93 ) {
				// 1. Déplacement de la valeur par sous-sujet / Valeurparsoussujetcer93
				$valeurparsoussujetcer93 = Hash::get( $sujetcer93, 'Cer93Sujetcer93.Valeurparsoussujetcer93' );
				$sujetcer93 = Hash::remove( $sujetcer93, 'Cer93Sujetcer93.Valeurparsoussujetcer93' );

				// 2. Déplacement du sujet
				$soussujetcer93 = Hash::get( $sujetcer93, 'Cer93Sujetcer93.Soussujetcer93' );
				$sujetcer93 = Hash::remove( $sujetcer93, 'Cer93Sujetcer93.Soussujetcer93' );

				// 3. Déplacement des données de la table de liaison
				$cer93Sujetcer93 = Hash::get( $sujetcer93, 'Cer93Sujetcer93' );
				$sujetcer93 = Hash::remove( $sujetcer93, 'Cer93Sujetcer93' );

				$test[] = array(
					'Sujetcer93' => $sujetcer93,
					'Soussujetcer93' => $soussujetcer93,
					'Cer93Sujetcer93' => $cer93Sujetcer93,
					'Valeurparsoussujetcer93' => $valeurparsoussujetcer93
				);
			}

			return $this->sujets( $test );
		}

		// Test, tableau plus complêt, comme dans edit>Bilan du contrat précédent>
		public function sujets2( array $data, array $params = array() ) {
			// TODO: Params avec les paths de chaque champ, pour pouvoir l'utiliser dans la vue
			$params += array(
				'hidden' => false,
				'domain' => $this->request->params['controller'],
				'visibleFields' => array(
					'Sujetcer93.name',
					'Cer93Sujetcer93.commentaireautre',
					'Soussujetcer93.name',
					'Cer93Sujetcer93.autresoussujet',
					'Valeurparsoussujetcer93.name',
					'Cer93Sujetcer93.autrevaleur'
				),
				'hiddenFields' => array(
					'Cer93Sujetcer93.sujetcer93_id',
					'Cer93Sujetcer93.commentaireautre',
					'Cer93Sujetcer93.soussujetcer93_id',
					'Cer93Sujetcer93.autresoussujet',
					'Cer93Sujetcer93.valeurparsoussujetcer93_id',
					'Cer93Sujetcer93.autrevaleur'
				),
				'hiddenPrefix' => 'Sujetcer93.Sujetcer93',
				'id' => null
			);

			$return = '';

			if( !empty( $data ) ) {
				$trs = '';
				foreach( $data as $index => $record ) {
					// Champs visibles
					$visible = array();
					foreach( $params['visibleFields'] as $path ) {
						$name = Hash::get( $record, $path );
						$visible[] = h( $name );
					}

					// Champs cachés si besoin
					$hidden = '';
					if( $params['hidden'] === true ) {
						foreach( $params['hiddenFields'] as $path ) {
							list( $modelName, $fieldName ) = model_field( $path );
							$value = Hash::get( $record, $path );
							$hidden .= $this->Form->input( "{$params['hiddenPrefix']}.{$index}.{$fieldName}", array( 'type' => 'hidden', 'value' => $value, 'id' => null ) );
						}

						$visible[0] = $hidden.$visible[0];
					}

					// Ligne du tableau
					$trs .= $this->Html->tableCells(
						$visible,
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
				}
				// Corps du tableau
				$tbody = $this->Html->tag( 'tbody', $trs );

				// En-tête du tableau
				$headers = array();
				foreach( $params['visibleFields'] as $path ) {
					$headers[] = __d( $params['domain'], $path );
				}
				$thead = $this->Html->tag( 'thead', $this->Html->tableHeaders( $headers ) );

				$return = $this->Html->tag( 'table', $thead.$tbody, array( 'id' => $params['id'] ) );
			}

			return $return;
		}


		// TODO: vérifier l'affichage avec "Autre, ..."
		public function sujetspcds2( array $data, array $params = array() ) {
			$test = array();
			foreach( $data['Sujetcer93'] as $index => $sujetcer93 ) {
				// 1. Déplacement de la valeur par sous-sujet / Valeurparsoussujetcer93
				$valeurparsoussujetcer93 = Hash::get( $sujetcer93, 'Cer93Sujetcer93.Valeurparsoussujetcer93' );
				$sujetcer93 = Hash::remove( $sujetcer93, 'Cer93Sujetcer93.Valeurparsoussujetcer93' );

				// 2. Déplacement du sujet
				$soussujetcer93 = Hash::get( $sujetcer93, 'Cer93Sujetcer93.Soussujetcer93' );
				$sujetcer93 = Hash::remove( $sujetcer93, 'Cer93Sujetcer93.Soussujetcer93' );

				// 3. Déplacement des données de la table de liaison
				$cer93Sujetcer93 = Hash::get( $sujetcer93, 'Cer93Sujetcer93' );
				$sujetcer93 = Hash::remove( $sujetcer93, 'Cer93Sujetcer93' );

				$test[] = array(
					'Sujetcer93' => $sujetcer93,
					'Soussujetcer93' => $soussujetcer93,
					'Cer93Sujetcer93' => $cer93Sujetcer93,
					'Valeurparsoussujetcer93' => $valeurparsoussujetcer93
				);
			}

			return $this->sujets2( $test, $params );
		}
	}
?>