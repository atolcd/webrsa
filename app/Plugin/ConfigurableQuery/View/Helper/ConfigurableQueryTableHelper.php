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
	App::uses( 'DefaultTableHelper', 'Default.View/Helper' );

	/**
	 * La classe DefaultTableHelper ...
	 *
	 * @package Default
	 * @subpackage View.Helper
	 */
	class ConfigurableQueryTableHelper extends DefaultTableHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'DefaultTableCell' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryTableCell'
			),
			'DefaultHtml' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryHtml'
			),
			'DefaultPaginator' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryPaginator'
			)
		);

		/**
		 * Permet l'affichage d'érreur dans le cas où un Préfix est appliqué à un input
		 * Si self::$entityErrorPrefix = 'Cohorte' alors :
		 *	 Cohorte.0.Monmodel.field = Monmodel.0.field
		 *
		 * @var string
		 */
		public $entityErrorPrefix = null;

		/**
		 * TODO
		 *
		 * @param array $fields
		 * @param array $params
		 * @return type
		 */
		public function thead( array $fields, array $params ) {
			if( $this->entityErrorPrefix !== null ) {
				$cohorte = Hash::get( $this->request->data, $this->entityErrorPrefix );
				unset($this->request->data[$this->entityErrorPrefix]);
			}

			$return = parent::thead( $fields, $params );

			if( $this->entityErrorPrefix !== null ) {
				$this->request->data[$this->entityErrorPrefix] = $cohorte;
			}

			// Début FIXME------------------------------------------------------
			$innerTable = Hash::get( $params, 'innerTable' );
			if( !empty( $innerTable ) ) {
				$return = str_replace( '</tr>', '<th class="innerTableHeader noprint">Informations complémentaires</th></tr>', $return );
			}
			// Fin FIXME------------------------------------------------------

			return $return;
		}

		public function getFields( $key ) {
			// FIXME: utiliser la fonction adéquate
			$fields = Hash::normalize( (array)Configure::read( $key ) );

			foreach( $fields as $fieldName => $p ) {
				$p = (array) $p;
				if( !isset( $p['type'] ) ) {
					$fields[$fieldName]['type'] = $this->DefaultTableCell->DefaultData->type( $fieldName );
				}
				if( !isset( $p['label'] ) ) {
					$fields[$fieldName]['label'] = __m( $fieldName );
				}
			}

			return $fields;
		}

		public function tr( $index, array $data, array $fields, array $params = array() ) {
			$return = parent::tr( $index, $data, $fields, $params );
			$tableId = Hash::get( $params, 'id' );

			$innerTable = Hash::get( $params, 'innerTable' );
			if( !empty( $innerTable ) ) {
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
		 *
		 * @param array $data
		 * @param array $fields
		 * @param array $params
		 * @return string
		 */
		public function index( array $data, array $fields, array $params = array() ) {
			$innerTable = Hash::get( $params, 'innerTable' );
			if( !empty( $innerTable ) ) {
				$params = $this->addClass( $params, 'tooltips' );
			}

			return parent::index( $data, $fields, $params );
		}
	}
?>