<?php
	/**
	 * Code source de la classe Pdf.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once  APPLIBS.'cmis.php' ;
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Pdf ...
	 *
	 * @package app.Model
	 */
	class Pdf extends AppModel
	{
		public $name = 'Pdf';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate' => array(
				'rules' => array(
					'datetime' => false
				)
			)
		);

		/**
		* Surcharge de la fonction de sauvegarde pour essayer d'enregistrer le PDF sur Alfresco.
		*/

		public function save( $data = null, $validate = true, $fieldList = array() ) {
			$cmsPath = "/{$this->data[$this->alias]['modele']}/{$this->data[$this->alias]['fk_value']}.pdf";
			$cmsSuccess = Cmis::write( $cmsPath, $this->data[$this->alias]['document'], 'application/pdf', true );

			if( $cmsSuccess ) {
				$this->data[$this->alias]['cmspath'] = $cmsPath;
				$this->data[$this->alias]['document'] = null;
			}

			$success = parent::save( $data, $validate, $fieldList );
			if( !$success && $cmsSuccess ) {
				$success = Cmis::delete( $cmsPath, true ) && $success;
			}

			return $success;
		}

		/**
		*
		*/

		public function delete( $id = NULL, $cascade = true ) {
			$conditions = array();
			if( empty( $id ) && !empty( $this->id ) ) {
				$conditions["{$this->alias}.{$this->primaryKey}"] = $this->id;
			}
			else {
				$conditions["{$this->alias}.{$this->primaryKey}"] = $id;
			}

			$records = $this->find(
				'all',
				array(
					'fields' => array( 'id', 'modele', 'fk_value', 'cmspath' ),
					'conditions' => $conditions
				)
			);

			$success = parent::delete( $id, $cascade );
			$cmspaths = Hash::filter( (array)Set::extract( $records, "/{$this->alias}/cmspath" ) );

			if( $success && !empty( $cmspaths ) ) {
				foreach( $cmspaths as $cmspath ) {
					$success = Cmis::delete( $cmspath, true ) && $success;
				}
			}

			return $success;
		}

		/**
		*
		*/

		public function deleteAll( $conditions, $cascade = true, $callbacks = false ) {
			$records = $this->find(
				'all',
				array(
					'fields' => array( 'id', 'modele', 'fk_value', 'cmspath' ),
					'conditions' => $conditions
				)
			);

			$success = parent::deleteAll( $conditions, $cascade, $callbacks );
			$cmspaths = Hash::filter( (array)Set::extract( $records, "/{$this->alias}/cmspath" ) );

			if( $success && !empty( $cmspaths ) ) {
				foreach( $cmspaths as $cmspath ) {
					$success = Cmis::delete( $cmspath, true ) && $success;
				}
			}

			return $success;
		}

		/**
		 * Sous-requête permettant de savoir si une entrée existe dans la table pdfs pour une entrée d'une
		 * table d'un autre modèle.
		 *
		 * @param Model $Model
		 * @param string $fieldName Si null, renvoit uniquement la sous-reqête,
		 * 	sinon renvoit la sous-requête aliasée pour un champ (avec l'alias du
		 * 	modèle).
		 * @return string
		 */
		public function sqImprime( Model $Model, $fieldName = null ) {
			$alias = Inflector::underscore( $this->alias );

			$sq = $this->sq(
				array(
					'fields' => array(
						"{$alias}.id"
					),
					'alias' => $alias,
					'conditions' => array(
						"{$alias}.modele" => $Model->alias,
						"{$alias}.fk_value = {$Model->alias}.{$Model->primaryKey}"
					)
				)
			);
			$sq = "EXISTS( {$sq} )";

			if( !is_null( $fieldName ) ) {
				$sq = "( {$sq} ) AS \"{$Model->alias}__{$fieldName}\"";
			}

			return $sq;
		}
	}
?>