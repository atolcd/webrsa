<?php
	/**
	 * Code source de la classe DefaultDefaultHelper.
	 *
	 * PHP 5.4
	 *
	 * @package Default
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'DefaultDefaultHelper', 'Default.View/Helper' );
	App::uses( 'SearchProgressivePagination', 'Search.Utility' );

	/**
	 * La classe DefaultDefaultHelper ...
	 *
	 * @package Default
	 * @subpackage View.Helper
	 */
	class ConfigurableQueryDefaultHelper extends DefaultDefaultHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'DefaultAction' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryAction'
			),
			'DefaultCsv' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryCsv'
			),
			'DefaultForm' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryForm'
			),
			'DefaultHtml' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryHtml'
			),
			'DefaultPaginator' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryPaginator'
			),
			'DefaultTable' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryTable'
			),
			'Form'
		);

		/**
		 * On y stock les champs hidden avant leur retrait du tableau de résultat
		 * @var array
		 */
		public $hiddenFields = array();

		/**
		 * Permet l'affichage d'érreur dans le cas où un Préfix est appliqué à un input
		 * Si self::$entityErrorPrefix = 'Cohorte' alors :
		 *	 Cohorte.0.Monmodel.field = Monmodel.0.field
		 *
		 * @var string
		 */
		public $entityErrorPrefix = null;

		public function configuredParams( array $params = array() ) {
			return $params + array(
				'keyPrefix' => 'ConfigurableQuery',
				'key' => Inflector::camelize( $this->request->params['controller'] ).".{$this->request->params['action']}"
			);
		}

		public function normalizeConfiguredFields( array $fields ) {
			$fields = Hash::normalize( $fields );

			foreach( $fields as $fieldName => $params ) {
				$params = (array)$params;

				// Si c'est un champ caché, on ne l'utilisera pas dans la vue
				if( isset( $params['hidden'] ) && $params['hidden'] ) {
					$this->hiddenFields[$fieldName] = $fields[$fieldName];
					unset( $fields[$fieldName] );
				}
				else {
					if( !isset( $params['type'] ) && strstr( $fieldName, '/' ) === false ) {
						$fields[$fieldName]['type'] = $this->DefaultTable->DefaultTableCell->DefaultData->type( $fieldName );
					}
					else if( isset( $params['type'] ) && strpos( $fieldName, 'data[' ) === 0 ) {
						$fields[$fieldName] = $this->addClass( $fields[$fieldName], 'input' );
					}
					if( !isset( $params['label'] ) ) {
						$fields[$fieldName]['label'] = __m( $fieldName );
					}
				}
			}

			return $fields;
		}

		/**
		 * Récupère les fields dans Config/CgXX/NomDuController.php
		 *
		 * @todo Modifier fonctionnement avant/après, voir utilisation
		 *
		 * @param array $params
		 * @param array $insert Ajout possible de champs avant les actions
		 * @return type
		 */
		public function configuredFields( array $params = array(), array $insert = array() ) {
			$params = $this->configuredParams( $params );
			$configuredFields = (array)Configure::read( ltrim( "{$params['keyPrefix']}.{$params['key']}", '.' ) );

			$fields = $this->normalizeConfiguredFields( $configuredFields );
			$insertedFields = array();
			$inserted = false;

			if ( !empty($insert) ) {
				$insert = $this->normalizeConfiguredFields($insert);

				// On cherche la première action pour l'insert
				foreach ( $fields as $key => $value ) {
					if( strstr( $key, '/' ) !== false && $inserted === false ) {
						$inserted = true;
						$insertedFields = array_merge($insertedFields, $insert);
					}

					$insertedFields[$key] = $value;
				}

				// S'il n'y a pas d'action, on insert à la fin
				if ( $inserted === false ) {
					$insertedFields = array_merge($insertedFields, $insert);
				}
			}
			else{
				$insertedFields = $fields;
			}

			return $insertedFields;
		}

		/**
		 *
		 * @param array $data
		 * @param array $params
		 * @return string
		 */
		public function configuredCsv( array $results, array $params = array() ) {
			$params = $this->configuredParams( $params );
			$fields = $this->configuredFields( array( 'key' => $params['key'].'.results.fields', 'keyPrefix' => $params['keyPrefix'] ) );

			return $this->DefaultCsv->render( $results, $fields, $params );
		}

		/**
		 * TODO
		 *
		 * @param array $results
		 * @param array $params
		 * @param array $insert
		 * @return type
		 */
		public function configuredIndex( array $results, array $params = array(), array $insert = array() ) {
			$params = $this->configuredParams( $params );
			$params += array(
				'format' => SearchProgressivePagination::format( !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' ) ),
				'view' => false,
				'th' => true
			);
			$fields = $this->configuredFields(
				array( 'key' => $params['key'].'.results.fields', 'keyPrefix' => $params['keyPrefix'] ),
				(!$params['view'] ? $insert : array())
			);

			$header = (array)Configure::read( ltrim( "{$params['keyPrefix']}.{$params['key']}".'.results.header', '.' ) );
			if( !empty( $header ) ) {
				$params['header'] = $header;
			}

			$innerTable = (array)Configure::read( ltrim( "{$params['keyPrefix']}.{$params['key']}".'.results.innerTable', '.' ) );
			if( !empty( $innerTable ) ) {
				$params['innerTable'] = $this->normalizeConfiguredFields( $innerTable );
			}

			// Pour les liens de pagination, on enlève ce qui n'est pas de la recherche
			$data = $this->request->data;
			if( $this->entityErrorPrefix !== null ) {
				unset($data[$this->entityErrorPrefix]);
			}

			$this->DefaultPaginator->options(
				array( 'url' => Hash::flatten( (array)$data, '__' ) )
			);

			unset( $params['keyPrefix'] );

			// On génère le tableau de résultat
			if ( !$params['view'] ) {
				return $this->index(
					$results,
					$fields,
					$params
				);
			}

			// Affichage vertical des résultats
			if( empty( $results ) ) {
				return $this->DefaultHtml->tag( 'p', 'Aucun enregistrement', array( 'class' => 'notice' ) );
			}

			$return = $this->pagination();
			foreach($results as $i => $result) {
				$return .= $this->view(
					$result,
					$fields,
					$params
				).'<br/>';

				if ( !empty($insert) ) {
					$return .= '<fieldset>';
					$subformfields = array();
					foreach ( $insert as $inputName => $value ) {
						$preformatedPath = preg_replace( '/^data\[(.*)\]$/', '\1', str_replace( '[Cohorte][]', "[Cohorte][{$i}]", $inputName ) );
						$fullPath = str_replace( '][', '.', $preformatedPath );
						if ( strpos($fullPath, '.') === false ) {
							continue;
						}

						//$return .= count($input) ? $input[0] : '';
						$subformfields[$fullPath] = $value;
					}
					$return .= $this->subform($subformfields, $params);
					$return .= '</fieldset>';
				}
			}
			$return .= $this->pagination();

			return $return;
		}

		/**
		 * Génère un tableau de résultat de recherche avec formulaire de cohorte
		 *
		 * $params Aura besoin de certaines clefs :
		 * - extraHiddenFields : Permet de conserver les filtres de recherches en champs caché additionnel.
		 * - cohorteFields : Ajoute au Fields configurés les valeurs renseigné ici.
		 *	   ex: array('data[Cohorte][][Mymodel][myfield]' => array('type'=>'checkbox', 'label'=>''))
		 * - entityErrorPrefix : Permet un affichage des érreurs sur les élements portant un préfix ex: 'Cohorte'.
		 *
		 * Pour les autres params, voir $this->_params
		 *
		 * @param array $results
		 * @param array $params
		 * @return string
		 */
		public function configuredCohorte( array $results, array $params = array() ) {
			$extraHiddenFields = (array)Hash::get( $params, 'extraHiddenFields' );
			$insert = (array)Hash::get( $params, 'cohorteFields' );
			unset($params['extraHiddenFields'], $params['cohorteFields']);
			$params['id'] = false;

			$this->entityErrorPrefix = $this->DefaultTable->entityErrorPrefix = $this->DefaultForm->entityErrorPrefix = Hash::get( $params, 'entityErrorPrefix' );
			unset($params['entityErrorPrefix']);

			$table = $this->configuredIndex($results, $params, $insert);

			// On ajoute les champs cachés à la fin
			foreach ($this->hiddenFields as $key => $hiddenFields) {
				for ($i=0; $i<count($results); $i++) {
					$preformatedPath = preg_replace( '/^data\[(.*)\]$/', '\1', str_replace( '[]', "[{$i}]", $key ) );
					$fullPath = str_replace( '][', '.', $preformatedPath );
					if ( strpos($fullPath, '.') === false ) {
						break;
					}

					$path = str_replace( '][', '.', str_replace( "[{$i}]", '', $preformatedPath ) );
					$model_field = model_field( $path );

					$field = $hiddenFields;
					$field += array('value' => Hash::get($results, $i.'.'.$model_field[0].'.'.$model_field[1]));

					$input = $this->DefaultTable->DefaultTableCell->input($fullPath, $field);
					$table .= count($input) ? $input[0] : '';
				}
			}

			foreach ( Hash::flatten( (array)$extraHiddenFields, '.' ) as $path => $value ) {
				$input = strpos($path, '.')
					? $this->DefaultTable->DefaultTableCell->input( $path, array( 'type' => 'hidden', 'value' => $value ) )
					: $this->Form->input( $path, array( 'type' => 'hidden', 'value' => $value ) )
				;
				$table .= is_array($input) ? $input[0] : $input;
			}

			return $table;
		}

		/**
		 * Surcharge de pagination() pour retirer $this->request->data[$this->entityErrorPrefix] de la pagination
		 * Evite les érreurs dù au trop grand nombre d'informations dans la barre d'adresse
		 *
		 * @param array $params
		 * @return type
		 */
		public function pagination(array $params = array()) {
			if( $this->entityErrorPrefix !== null ) {
				$cohorte = Hash::get( $this->request->data, $this->entityErrorPrefix );
				unset($this->request->data[$this->entityErrorPrefix]);
			}

			$result = parent::pagination($params);

			if( $this->entityErrorPrefix !== null ) {
				$this->request->data[$this->entityErrorPrefix] = $cohorte;
			}

			return $result;
		}
	}
?>