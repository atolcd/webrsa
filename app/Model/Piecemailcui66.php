<?php
	/**
	 * Code source de la classe Piecemailcui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'File', 'Utility' );
	App::uses( 'Folder', 'Utility' );

	/**
	 * La classe Piecemailcui66 ...
	 *
	 * @package app.Model
	 */
	class Piecemailcui66 extends AppModel
	{
		public $name = 'Piecemailcui66';

		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Has Many".
		 * @var array
		 */
		public $hasMany = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Piecemailcui66\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Emailcui' => array(
				'className' => 'Emailcui',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					"Piecemailcui66.id = ANY ( string_to_array( Emailcui.pj, '_' )::int[] )"
				),
			)
		);

		public function getFichiersLiesById( $id ){
			$files = ClassRegistry::init( 'Fichiermodule' )->find( 'all',
				array(
					'conditions' => array(
						'Fichiermodule.modele' => $this->name,
						'fk_value' => $id
					)
				)
			);

			$path = TMP . 'Cui' . DS . $id;
			if ( !is_dir( $path ) ){
				mkdir( $path, 0777, true );
			}

			$filesNames = array();
			foreach( $files as $file ){
				$this->_generateTmpFile( $file['Fichiermodule'], $path );
				$filesNames[] = $path . DS . $file['Fichiermodule']['name'];
			}

			return $filesNames;
		}

		protected function _generateTmpFile( $data, $path ){
			if( !empty( $data['cmspath'] )  ) {
				$document = Cmis::read( $data['cmspath'], true );
			}
			elseif( !empty( $data['document'] ) ) {
				$document['content'] = $data['document'];
			}
			else {
				$this->cakeError( 'error500' );
			}

			$file = new File( $path . DS . $data['name'], true, 0777 );
			$file->write( $document['content'] );

			return $this;
		}
	}
?>