<?php
	/**
	 * Code source de la classe DefaultTableHelper.
	 *
	 * PHP 5.4
	 *
	 * @package Default
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );
	App::uses( 'DefaultUtility', 'Default.Utility' );

	/**
	 * La classe DefaultTableHelper ...
	 *
	 * @package Default
	 * @subpackage View.Helper
	 */
	class DefaultTableHelper extends AppHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default.DefaultTableCell',
			'Default.DefaultHtml',
			'Default.DefaultPaginator',
			'Paginator'
		);

		/**
		 * Permet de savoir si un champ est sous la forme /Controller/action,
		 * ce qui signifie un lien.
		 *
		 * @param string $field
		 * @return boolean
		 */
		protected function _isUrlField( $field ) {
			return ( strpos( $field, '/' ) === 0 );
		}

		/**
		 * Permet de savoir si un champ est sous la forme data[Model][field],
		 * ce qui signifie un champ de formulaire.
		 *
		 * @param string $field
		 * @return boolean
		 */
		protected function _isInputField( $field ) {
			return ( strpos( $field, 'data[' ) === 0 );
		}

		/**
		 * Permet de savoir si un champ est sous la forme Model.field, ce qui
		 * signifie de l'affichage formaté.
		 *
		 * @param string $field
		 * @return boolean
		 */
		protected function _isDataField( $field ) {
			return ( strstr( $field, '.' ) !== false )
				&& !$this->_isUrlField( $field )
				&& !$this->_isInputField( $field );
		}

		/**
		 * Retourne l'élément thead d'une table pour un ensemble d'enregistrements.
		 *
		 * @param array $fields
		 * @param array $params
		 * @return string
		 */
		public function thead( array $fields, array $params ) {
			if( empty( $fields ) ) {
				return null;
			}

			$fields = Hash::normalize( $fields );
			$theadTr = array();
			$domain = Hash::get( $params, 'domain' );
			$tableId = Hash::get( $params, 'id' );
			$sort = ( isset( $params['sort'] ) ? $params['sort'] : true );

			$columns = array();
			$colspans = array();

			foreach( $fields as $field => $attributes ) {
				$attributes = (array)$attributes;
				$attributes['sort'] = isset( $attributes['sort'] ) ? $attributes['sort'] : $sort;

				$hasUrl = $this->_isUrlField( $field );
				$hasCondition = Hash::check( $attributes, 'condition' );
				$conditionGroup = (string)Hash::get( $attributes, 'condition_group' );

				// On détermine le groupe
				if( $hasUrl ) {
					$group = '__actions__';
				}
				else if( $hasCondition ) {
					$group = $conditionGroup;
				}
				else {
					$group = $field;
				}

				// Stockage des colspan, en fonction des conditions et de condition_group
				if( !isset( $colspans[$group] ) ) {
					$colspans[$group] = array();
				}
				if( !isset( $colspans[$group][$conditionGroup] ) ) {
					$colspans[$group][$conditionGroup] = array( 1 => 0, 0 => 0 );
				}
				$colspans[$group][$conditionGroup][(int)$hasCondition]++;

				// On détermine les attributs pour chaque groupe
				if( !isset( $columns[$group] ) ) {
					$columns[$group] = $attributes;

					if( $hasUrl ) {
						$columns[$group]['id'] = "{$tableId}ColumnActions";
						$columns[$group] = $this->addClass( $columns[$group], 'actions' );
					}
					else if( $this->_isInputField( $field ) ) {
						$columns[$group]['id'] = "{$tableId}ColumnInput".Inflector::camelize( preg_replace( '/(\[|\])+/', '_', $field ) );
						$columns[$group]['label'] = __m($field);
					}
					else if( $this->_isDataField( $field ) ) {
						// INFO: la mise en cache n'a pas de sens ici
						list( $modelName, $fieldName ) = model_field( $field );

						$columns[$group]['id'] = "{$tableId}Column{$modelName}".Inflector::camelize( $fieldName );

						// Intitulé depuis la traductions ou surchargé
						$label = Hash::get( $attributes, 'label' );
						unset( $attributes['label'] );
						if( empty( $label ) ) {
							$label = __d( $domain, "{$modelName}.{$fieldName}" );
						}

						$cellSort = Hash::get( $attributes, 'sort' );
						if(isset($params['sortPaginator'])){
							if (strpos($field, 'Erreur.') === 0){
								//Si field est Erreur.qqchose
								//On renomme en Personne.qqchose
								//Pour Rapportsechangesali details
								$field = 'PersonneEchangeALI.'.substr($field, 7);
							}
							$label = $this->Paginator->sort($field, $label);
						}
						else if( ( $cellSort === null && $sort ) || ( $cellSort !== null && $cellSort ) ) {
							$label = $this->DefaultPaginator->sort( $field, $label );
						}

						$columns[$group]['label'] = $label;
					}
				}
			}

			// Normalisation des colonnes pur une utilisation avec (Default)Html::tableHeaders
			foreach( $columns as $group => $colParams ) {
				$label = $group === '__actions__'
					? __d( $domain, 'Actions' )
					: $colParams['label'];

				// Calcul du colspan réel
				$colspan = 0;
				foreach( $colspans[$group] as $conditionGroup => $bools ) {
					$colspan += $bools[0];
					if( $bools[1] > 0 ) {
						$colspan += 1;
					}
				}
				if( $colspan === 1 ) {
					$colspan = null;
				}

				$class = preg_replace( '/#[^#]+#/', '', Hash::get( $colParams, 'class' ) );
				$id = Hash::get( $colParams, 'id' );

				$theadTr[] = array(
					$label => array(
						'colspan' => $colspan,
						'class' => ( '' === $class ? null : $class ),
						'id' => ( '' === $id ? null : $id )
					)
				);
			}

			// Ligne d'en-têtes supplémentaires ?
			$header = (array)Hash::get( $params, 'header' );
			if( !empty( $header ) ) {
				$header = $this->DefaultHtml->tableHeaders( $header );
			}
			else {
				$header = '';
			}

			return $this->DefaultHtml->tag( 'thead', $header.$this->DefaultHtml->tableHeaders( $theadTr ) );
		}

		/**
		 * Retourne l'élément tr du body d'une table pour un enregistrement donné.
		 *
		 * @param integer $index
		 * @param array $data
		 * @param array $fields
		 * @param array $params
		 * @return array
		 */
		public function tr( $index, array $data, array $fields, array $params = array() ) {
			$this->DefaultTableCell->set( $data );
			$tr = array();

			foreach( $fields as $path => $attributes ) {
				$attributes = (array)$attributes;

				$condition = $this->_condition( $data, $attributes );
				unset( $attributes['condition'], $attributes['condition_group'] );

				if( $condition ) {
					$path = str_replace( '[]', "[{$index}]", $path );
					if( !isset( $attributes['domain'] ) && isset( $params['domain'] ) ) {
						$attributes['domain'] = $params['domain'];
					}

					if( $this->_isDataField( $path ) ) {
						list( $modelName, $fieldName ) = model_field( $path );
						if( !isset( $attributes['options'] ) && isset( $params['options'][$modelName][$fieldName] ) ) {
							$attributes['options'] = $params['options'][$modelName][$fieldName];
						}
					}

					$tr[] = $this->DefaultTableCell->auto( $path, $attributes );
				}
			}

			$class = ( $index % 2 == 0 ) ? 'odd' : 'even';
			$return = $this->DefaultHtml->tableCells( array( $tr ), array( 'class' => $class ), array( 'class' => $class ), false, true );

			$tableId = Hash::get( $params, 'id' );
			$innerTable = Hash::get( $params, 'innerTable' );
			$edit = strpos(Hash::get( $params, 'class' ), 'edit');
			if( !empty( $innerTable ) && $edit) {
				$innerTable = $this->details(
					$data,
					$innerTable,
					array(
						'options' => (array)Hash::get( $params, 'options' ),
						'class' => 'innerTable',
						'id' => "innerTable{$tableId}{$index}",
						'th' => true
					)
				);


				$return = str_replace( '</tr>', "<td class=\"innerTableCell noprint\">{$innerTable}</td></tr>", $return );
			}

			return $return;
		}

		/**
		 * Retourne l'élément tbody d'une table pour un ensemble d'enregistrements.
		 *
		 * @param array $datas
		 * @param array $fields
		 * @param array $params
		 * @return string
		 */
		public function tbody( array $datas, array $fields, array $params = array() ) {
			if( empty( $datas ) || empty( $fields ) ) {
				return null;
			}

			$fields = Hash::normalize( $fields );
			$trs = array();

			foreach( $datas as $i => $data ) {
				$trs[] = $this->tr( $i, $data, $fields, $params );
			}

			return $this->DefaultHtml->tag( 'tbody', implode( '', $trs ) );
		}

		/**
		 * Retourne les paramètres à utiliser pour une table.
		 *
		 * @param array $params
		 * @return array
		 */
		public function tableParams( array $params = array() ) {
			$result = array(
				'id' => ( isset( $params['id'] ) ? $params['id'] : $this->domId( "Table.{$this->request->params['controller']}.{$this->request->params['action']}" ) ),
				'class' => "{$this->request->params['controller']} {$this->request->params['action']}",
				'domain' => ( isset( $params['domain'] ) ? $params['domain'] : Inflector::underscore( $this->request->params['controller'] ) ),
				'sort' => ( isset( $params['sort'] ) ? $params['sort'] : true )
			);

			$class = Hash::get( $params, 'class' );
			if( !empty( $class ) ) {
				$result = $this->addClass( $result, $class );
			}

			return $result;
		}

		/**
		 * Retourne une table complète (thead/tbody) pour un ensemble d'enregistrements.
		 *
		 * @param array $data
		 * @param array $fields
		 * @param array $params
		 * @return null
		 */
		public function index( array $data, array $fields, array $params = array() ) {
			if( empty( $data ) || empty( $fields ) ) {
				return null;
			}

			$tableParams = $this->tableParams( $params );
			unset( $params['id'] );

			$thead = $this->thead( $fields, $tableParams + $params );
			$tbody = $this->tbody( $data, $fields, $tableParams + $params );

			return $this->DefaultHtml->tag( 'table', $thead.$tbody, array( 'id' => $tableParams['id'], 'class' => $tableParams['class'] ) );
		}

		/**
		 * Retourne un booléen permettant de savoir s'il faut effectuer l'affichage,
		 * en fonction des données et d'une éventuelle clé condition dans les paramètres.
		 *
		 * @param array $data Les données à utiliser pour l'évaluation
		 * @param array $params Les paramètres contenant une éventuelle clé condition
		 * @return boolean
		 */
		protected function _condition( array &$data, array &$params ) {
			$condition = true;

			if( isset( $params['condition'] ) ) {
				$condition = $params['condition'];

				if( is_string( $condition ) ) {
					$condition = eval( 'return '.DefaultUtility::evaluate( $data, $condition ).';' );
				}
			}

			return $condition;
		}

		/**
		 * Retourne le tbody une table de détails (verticale) pour un
		 * enregistrement particulier.
		 *
		 * @param array $data
		 * @param array $fields
		 * @param array $params
		 * @return string
		 */
		public function detailsTbody( array $data, array $fields, array $params = array() ) {
			if( empty( $data ) || empty( $fields ) ) {
				return null;
			}

			$this->DefaultTableCell->set( $data );
			$fields = Hash::normalize( $fields );
			$trs = array();
			$domain = Hash::get( $params, 'domain' );

			foreach( $fields as $path => $attributes ) {
				$attributes = (array)$attributes;

				$condition = $this->_condition( $data, $attributes );
				unset( $attributes['condition'] );

				if( $condition ) {
					if (strpos($path, '/') === false) {
						// INFO: la mise en cache n'a pas de sens ici
						list( $modelName, $fieldName ) = model_field( $path );


						if( !isset( $attributes['options'] ) && isset( $params['options'][$modelName][$fieldName] ) ) {
							$attributes['options'] = $params['options'][$modelName][$fieldName];
						}
					}

					if( isset( $attributes['domain'] ) && !empty( $attributes['domain'] ) ) {
						$specificDomain = $attributes['domain'];
					}
					else {
						$specificDomain = $domain;
					}

					$label = Hash::get( $attributes, 'label' );
					unset( $attributes['label'] );
					if( empty( $label ) ) {
						$label = __d( $specificDomain, $path );
					}

					$trs[] = array(
						$label, // INFO: pas possible me mettre un th de cette manière, avec tableCells
						$this->DefaultTableCell->auto( $path, $attributes ),
					);
				}
			}

			if( empty( $trs ) ) {
				return null;
			}

			// INFO: en fait, on peut remplacer par des th -> options
			$tableCells = $this->DefaultHtml->tableCells( $trs, array( 'class' => 'odd' ), array( 'class' => 'even' ), false, false );
			if( Hash::get( $params, 'th' ) ) {
				$tableCells = preg_replace( '/<tr([^>]*)><td([^>]*)>([^><]*)<\/td([^>]*)>/', '<tr\1><th\2>\3</th\4>', $tableCells );
			}
			return $this->DefaultHtml->tag( 'tbody', $tableCells );
		}

		/**
		 * Retourne une table de détails (verticale) pour un enregistrement
		 * particulier.
		 *
		 * @param array $data
		 * @param array $fields
		 * @param array $params
		 * @return null
		 */
		public function details( array $data, array $fields, array $params = array() ) {
			if( empty( $data ) || empty( $fields ) ) {
				return null;
			}

			$tableParams = $this->tableParams( $params );

			// Ajoute un thead avec un th colspan 2 contenant le titre du tableau
			$caption = '';
			if (Hash::get($params, 'caption')) {
				$caption = $this->DefaultHtml->tag('caption', Hash::get($params, 'caption'));
				unset($params['caption']);
			}

			$tbody = $this->detailsTbody( $data, $fields, $tableParams + $params );

			if( !empty( $tbody ) ) {
				return $this->DefaultHtml->tag( 'table', $caption.$tbody, array( 'id' => $tableParams['id'], 'class' => $tableParams['class'] ) );
			}
			else {
				return null;
			}
		}
	}
?>