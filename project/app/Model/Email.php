<?php
	/**
	 * Fichier source de la classe Email.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Email est la classe contenant les e-mails.
	 *
	 * @package app.Model
	 */
	class Email extends AppModel
	{
		public $name = 'Email';

		public $belongsTo = array(
			'Textemail' => array(
				'className' => 'Textemail',
				'foreignKey' => 'textemail_id',
				'dependent' => false
			)
		);

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		public $validate = array(
			'emailredacteur' => array(
				'email' => array(
					'rule' => array( 'email' ),
					'allowEmpty' => false,
				)
			),
			'titre' => array(
				array(
					'rule' => array(NOT_BLANK_RULE_NAME),
				),
			),
			'message' => array(
				array(
					'rule' => array(NOT_BLANK_RULE_NAME),
				),
			),
		);

		/**
		 *
		 * @param array $data
		 * @return boolean
		 */
		public function saveAddEdit( array $data ) {
			if ( is_numeric($data['Email']['emaildestinataire']) ) {
				$Emaildestinataire = ClassRegistry::init( 'Emaildestinataire' );
				$emaildestinatairesInfos = $Emaildestinataire->find( 'first', 
				array(
					'fields'=> 'Emaildestinataire.email', 
					'conditions' => array (
						'Emaildestinataire.id' => $data['Email']['emaildestinataire']
					)
				));
				$data['Email']['emaildestinataire'] = $emaildestinatairesInfos['Emaildestinataire']['email'];
			}
			$data['Email']['pj'] = is_array($data['Email']['pj']) ? implode( '_', $data['Email']['pj'] ) : '';

			$this->create($data);
			$success = $this->save( $data, array( 'atomic' => false ) );
			return $success;
		}

		/**
		 * Retourne les options nécessaires au formulaire de recherche, au formulaire,
		 * aux impressions, ...
		 *
		 * @param array $params <=> array( 'allocataire' => true, 'find' => false, 'autre' => false, 'pdf' => false )
		 * @return array
		 */
		public function options( array $params = array() ) {

			$options = array();

			// Pièce jointes parametrables
			$Piecemail = ClassRegistry::init( 'Piecemail' );
			$options['Email']['pj'] = $Piecemail->find( 'list' );
			$options['Email']['pj_actif'] = $Piecemail->find( 'list', array( 'conditions' => array( 'actif' => true ), 'order' => 'name' ) );

			// Modeles d'e-mail parametrable
			$Textemail = ClassRegistry::init( 'Textemail' );
			$options['Email']['textemail_id'] = $Textemail->find( 'list', array( 'order' => 'name' ) );
			$options['Email']['textemail_id_actif'] = $Textemail->find( 'list', array( 'conditions' => array( 'actif' => true ), 'order' => 'name' ) );

			// Destinataires d'e-mail parametrable
			$Emaildestinataire = ClassRegistry::init( 'Emaildestinataire' );
			$emaildestinatairesInfos = $Emaildestinataire->find( 'all', array( 'order' => 'id' ) );
			foreach ( $emaildestinatairesInfos AS $emaildestinatairesInfo) {
				$options['Email']['emaildestinataire_id'][$emaildestinatairesInfo['Emaildestinataire']['id']] = $emaildestinatairesInfo['Emaildestinataire']['info_complet'];
				if ($emaildestinatairesInfo['Emaildestinataire']['actif'] ){
					$options['Email']['emaildestinataire_id_actif'][$emaildestinatairesInfo['Emaildestinataire']['id']] = $emaildestinatairesInfo['Emaildestinataire']['info_complet'];
				}	
			}
			
			$options = Hash::merge(
				$options,
				$this->enums()
			);

			return $options;
		}

		/**
		 * data d'un E-mail
		 *
		 * @param int $id
		 * @param string $modele
		 * @param string $action default null
		 */
		protected function _getDataEmail( $id, $modele, $action = null ) {
			$query = array(
				'conditions' => array(
					'Email.modele_id' => $id,
					'Email.modele' => $modele,
				)
			);
			if ( $action != null ){
				$query['conditions']['Email.modele_action'] = $action; 
			}
			$result = $this->find( 'first', $query );
			if ( isset ($result['Email']['pj']) ) {
				$result['Email']['pj'] = explode( '_', $result['Email']['pj'] );
			}
			return $result;
		}

		/**
		 * Vue d'un E-mail
		 *
		 * @param int $id
		 * @param string $modele
		 * @param string $action dafault null
		 */
		public function view( $id, $modele, $action = null  ) {
			$result = $this->_getDataEmail($id, $modele, $action );

			// Pièce jointes parametrables
			$Piecemail = ClassRegistry::init( 'Piecemail' );
			$options = $Piecemail->find( 'list' );
			if ( isset ($result['Email']['pj']) ) {
			$arrayPJ = array();
				foreach ( $result['Email']['pj'] AS $key => $pj_id) {
					$arrayPJ[$pj_id] = $options[$pj_id];
				}
				$result['Email']['pj'] = implode( ', ', $arrayPJ);
			}
			return $result; 
		}

		/**
		 * Donnée pour modification d'un E-mail
		 *
		 * @param int $id
		 * @param string $modele
		 * @param string $action dafault null
		 */
		public function edit( $id, $modele, $action = null ) {
			$result = $this->_getDataEmail($id, $modele, $action );
			return $result; 
		}

		/**
		 * Vue des E-mails
		 *
		 * @param int $id
		 * @param string $modele
		 */
		public function viewAll( $id, $modele ) {

			$query = array(
				'conditions' => array(
					'Email.modele_id' => $id,
					'Email.modele' => $modele,
				)
			);

			// Pièce jointes parametrables
			$Piecemail = ClassRegistry::init( 'Piecemail' );
			$options = $Piecemail->find( 'list' );

			$results = $this->find( 'all', $query );

			foreach($results AS $keyResult => $result){
				if ( isset ($result['Email']['pj']) ) {
					$result['Email']['pj'] = explode( '_', $result['Email']['pj'] );
					$arrayPJ = array();
					foreach ( $result['Email']['pj'] AS $pj_id) {
						$arrayPJ[$pj_id] = $options[$pj_id];
					}
					$results[$keyResult]['Email']['pj'] = implode( ', ', $arrayPJ);
				}
			}

			return $results;
		}
	}
?>