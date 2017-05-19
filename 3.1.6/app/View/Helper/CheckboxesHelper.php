<?php
	/**
	 * Code source de la classe CheckboxesHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe CheckboxesHelper permet d'obtenir facilement une liste non
	 * ordonnée de checkboxes (ou de valeurs issues de ces checkboxes), dont
	 * certaines font apparaître un champ texte lorsqu'elles sont cochées.
	 *
	 * @package       app.View.Helper
	 */
	class CheckboxesHelper extends AppHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array( 'Xform', 'Xhtml' );

		/**
		 * Retourne le chemin à utiliser par la méthode Set::extract() à partir
		 * des morceaux de chemin.
		 *
		 * Exemple:
		 * <pre>$tokens = array( 'Model1', 'Model2', 'Model3' );</pre>
		 * retournera
		 * <pre>/Model1/Model2/Model3</pre>
		 *
		 * @param array $tokens Les morceaux de chemin
		 * @return string
		 */
		protected function _setExtractPath( $tokens ) {
			$setPath = '';

			foreach( $tokens as $token ) {
				if( $token != '%d' ) {
					$setPath .= "/{$token}";
				}
			}

			return $setPath;
		}

		/**
		 * Retourne le chemin à utiliser avec FormHelper::input().
		 *
		 * Exemple:
		 * <pre>$tokens = array( 'Model1', 'Model2', '%d', 'Model3' );</pre>
		 * <pre>$i = 5;</pre>
		 * retournera
		 * <pre>Model1.Model2.5.Model3</pre>
		 *
		 * @param array $tokens Les morceaux de chemin
		 * @param integer $i La valeur entière qui remplacera %d
		 * @return string
		 */
		protected function _inputPath( $tokens, $i ) {
			return sprintf( implode( '.', $tokens ), $i );
		}

		/**
		 * Retourne la valeur de l'attribut name d'un champ avec FormHelper::input().
		 *
		 * Exemple:
		 * <pre>$tokens = array( 'Model1', 'Model2', '%d', 'Model3' );</pre>
		 * <pre>$i = 5;</pre>
		 * retournera
		 * <pre>data[Model1][Model2][5][Model3]</pre>
		 *
		 * @param array $tokens Les morceaux de chemin
		 * @param integer $i La valeur entière qui remplacera %d
		 * @return string
		 */
		protected function _dataPath( $tokens, $i ) {
			return 'data['.sprintf( implode( '][', $tokens ), $i ).']';
		}

		/**
		 * Retourne une liste de cases à cocher (à un seul niveau), dans un liste
		 * non ordonnée, avec la possibilité que certaines valeurs donnent accès
		 * à un champ de type texte lorsqu'elles sont cochées.
		 * Le code javascript permettant de faire apparaître et disparaître le
		 * champ de type texte est inclus.
		 *
		 * <pre>
		 * echo $this->Checkboxes->inputs(
		 * 	'Commentairenormecer93.Commentairenormecer93.%d',
		 * 	array(
		 * 		'fk_field' => 'commentairenormecer93_id',
		 * 		'autre_field' => 'commentaireautre',
		 * 		'autres_type' => 'textarea',
		 * 		'offset' => 0,
		 * 		'options' => $commentairesnormescers93_list,
		 * 		'autres_ids' => $commentairesnormescers93_autres_ids
		 * 	)
		 * );
		 * </pre>
		 *
		 * @param string $base_path Le chamin de base, qui sera complété par les
		 *	valeurs de $params['fk_field'] et $params['autre_field'].
		 * @param array $params
		 * @return string
		 */
		public function inputs( $base_path, $params ) {
			$return = '';
			$script = '';

			$default = array( 'autres_type' => 'textarea', 'offset' => 0, 'cohorte' => false );
			$params = Set::merge( $default, $params );

			$keys = array( 'fk_field', 'autre_field', 'autres_type', 'offset', 'options', 'autres_ids', 'cohorte' );
			foreach( $keys as $key ) {
				$$key = Hash::get( $params, $key );
			}

			//TODO: trigger_error( "Fuu", E_USER_WARNING );

			$explodedFkPath = explode( '.', "{$base_path}.{$fk_field}" );
			$explodedAutresPath = explode( '.', "{$base_path}.{$autre_field}" );

			$checkedIds = Hash::filter( (array)
				Set::extract(
					$this->request->data,
					$this->_setExtractPath( $explodedFkPath )
				)
			);

			if( !empty( $options ) ) {
				$i = $offset;
				$return .= '<ul class="checkboxes simplelist inputs">';

				foreach( $options as $id => $name ) {
					$fkInputPath = $this->_inputPath( $explodedFkPath, $i );

					if( $cohorte === false ) {
						$array_key = array_search( $id, $checkedIds );
						$isChecked = ( ( $array_key !== false ) ? 'checked' : '' );
					}
					else {
						$fkInputValue = Hash::get( $this->request->data, $fkInputPath );
						$isChecked = ( !empty( $fkInputValue ) ? 'checked' : '' );
						$array_key = $i;
					}

					$return .= '<li>';

					$return .= $this->Xform->input(
						$fkInputPath,
						array(
							'name' => $this->_dataPath( $explodedFkPath, $i ),
							'label' => $name,
							'type' => 'checkbox',
							'value' => $id,
							'checked' => $isChecked,
							'hiddenField' => false
						)
					);

					if( in_array( $id, $autres_ids ) ) {
						$autreInputPath = $this->_inputPath( $explodedAutresPath, $i );
						$return .= $this->Xform->input(
							$autreInputPath,
							array(
								'name' => $this->_dataPath( $explodedAutresPath, $i ),
								'label' => false,
								'type' => $autres_type,
								'value' => Hash::get(
									$this->request->data,
									$this->_inputPath( $explodedAutresPath, $array_key )
								)
							)
						);

						$script .= "observeDisableFieldsOnCheckbox( '".$this->domId( $fkInputPath )."', ['".$this->domId( $autreInputPath )."'], false, true );";
					}
					$return .= '</li>';

					$i++;
				}

				$return .= '</ul>';
				$return .= $this->Xhtml->scriptBlock( $script );
			}

			return $return;
		}

		/**
		 * Retourne une liste d'éléments cochés (à un seul niveau), dans un liste
		 * non ordonnée, avec la possibilité que certaines valeurs donnent accès
		 * à un champ de type texte.
		 *
		 * Exemple:
		 * <pre>
		 * echo $this->Checkboxes->view(
		 * 	array(
		 *		'Commentairenormecer93' => array(
		 *			0 => array( 'name' => 'Foo' ),
		 *			1 => array( 'name' => 'Bar' ),
		 *		),
		 *		'Commentairenormecer93Histochoixcer93' => array(
		 *			0 => array( 'commentaireautre' => null ),
		 *			1 => array( 'commentaireautre' => 'Baz' ),
		 *		)
		 *  ),
		 * 	'Commentairenormecer93.name',
		 * 	'Commentairenormecer93Histochoixcer93.commentaireautre'
		 * );
		 * </pre>
		 * retournera
		 * <pre>
		 * &lt;ul class="checkboxes simplelist view"&gt;
		 *	&lt;li&gt;Foo&lt;/li&gt;
		 *	&lt;li&gt;Bar: Baz&lt;/li&gt;
		 * &lt;/ul&gt;
		 * </pre>
		 *
		 * @param array $data Les données d'où extraire les informations
		 * @param string $fk_model_field Le chemin vers l'intitulé
		 * @param string $autres_model_field Le chemin vers le texte libre des champs de type "autre"
		 * @return string
		 */
		public function view( $data, $fk_model_field, $autres_model_field ) {
			$return = '';

			$names = Set::extract( $data, $this->_setExtractPath( explode( '.', $fk_model_field ) ) );
			$autres = Set::extract( $data, $this->_setExtractPath( explode( '.', $autres_model_field ) ) );

			if( !empty( $names ) ) {
				$return .= '<ul class="checkboxes simplelist view">';
				foreach( $names as $i => $name ) {
					if( isset( $autres[$i] ) && !empty( $autres[$i] ) ) {
						$name = "{$name}: {$autres[$i]}";
					}
					$return .= '<li>'.h( $name ).'</li>';
				}
				$return .= '</ul>';
			}

			return $return;
		}
	}
?>