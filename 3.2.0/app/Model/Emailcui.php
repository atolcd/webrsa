<?php
	/**
	 * Fichier source de la classe Emailcui.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Emailcui est la classe contenant les e-mails du CUI.
	 *
	 * @package app.Model
	 */
	class Emailcui extends AppModel
	{
		public $name = 'Emailcui';

		public $belongsTo = array(
			'Cui' => array(
				'className' => 'Cui',
				'foreignKey' => 'cui_id',
				'dependent' => true,
			),
			'Textmailcui66' => array(
				'className' => 'Textmailcui66',
				'foreignKey' => 'textmailcui66_id',
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
			'emailemployeur' => array(
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
		 * @param integer $cui_id
		 * @param integer $id
		 * @return array
		 */
		public function prepareFormDataAddEdit( $personne_id, $cui_id, $email_id = null ) {
			// Ajout
			if( empty( $email_id ) ) {
				$query = array(
					'fields' => array(
						'Adressecui.email',
						'Cui66.id',
						'Partenairecui.id',
						'Partenairecui66.id',
						'Adressecui.id',
					),
					'recursive' => -1,
					'conditions' => array(
						'Cui.id' => $cui_id,
						'Cui.personne_id' => $personne_id
					),
					'joins' => array(
						$this->Cui->join( 'Partenairecui', array( 'type' => 'INNER' ) ),
						$this->Cui->join( 'Cui66', array( 'type' => 'INNER' ) ),
						$this->Cui->Partenairecui->join( 'Adressecui', array( 'type' => 'INNER' ) ),
						$this->Cui->Partenairecui->join( 'Partenairecui66', array( 'type' => 'INNER' ) ),
					)
				);
				$record = $this->Cui->find( 'first', $query );

				if ( empty($record) ){
					throw new NotFoundException();
				}

				$result = array(
					'Emailcui' => array(
						'cui_id' => $cui_id,
						'cui66_id' => $record['Cui66']['id'],
						'partenairecui_id' => $record['Partenairecui']['id'],
						'partenairecui66_id' => $record['Partenairecui66']['id'],
						'Adressecui_id' => $record['Adressecui']['id'],
						'personne_id' => $personne_id,
						'emailemployeur' => $record['Adressecui']['email'],
					),
				);

				$query = array(
					'fields' => array( 'Decisioncui66.id' ),
					'conditions' => array( 'cui66_id' => $record['Cui66']['id'] ),
					'order' => array( 'Decisioncui66.datedecision DESC')
				);
				$record = $this->Cui->Cui66->Decisioncui66->find( 'first', $query );

				if ( !empty($record) ){
					$result['Emailcui']['decisioncui66_id'] = $record['Decisioncui66']['id'];
				}
			}
			// Mise à jour
			else {
				$query = $this->queryView($email_id);
				$result = $this->find( 'first', $query );
				$result['Emailcui']['pj'] = explode( '_', $result['Emailcui']['pj'] );
				$result['Emailcui']['piecesmanquantes'] = explode( '_', $result['Emailcui']['piecesmanquantes'] );
			}

			return $result;
		}

		public function queryView( $email_id ){
			$query = array(
				'conditions' => array(
					'Emailcui.id' => $email_id,
				)
			);

			return $query;
		}

		/**
		 *
		 * @param array $data
		 * @return boolean
		 */
		public function saveAddEdit( array $data, $user_id = null ) {
			$data['Emailcui']['user_id'] = $user_id;
			$data['Emailcui']['pj'] = is_array($data['Emailcui']['pj']) ? implode( '_', $data['Emailcui']['pj'] ) : '';
			$data['Emailcui']['piecesmanquantes'] = is_array($data['Emailcui']['piecesmanquantes']) ? implode( '_', $data['Emailcui']['piecesmanquantes'] ) : '';

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

			if ( Configure::read( 'Cg.departement' ) == 66 ){
				$Piecemailcui66 = ClassRegistry::init( 'Piecemailcui66' );
				$options['Emailcui']['pj'] = $Piecemailcui66->find( 'list' );
				$options['Emailcui']['pj_actif'] = $Piecemailcui66->find( 'list', array( 'conditions' => array( 'actif' => true ), 'order' => 'name' ) );

				// Pièces manquante
				$Piecemanquantecui66 = ClassRegistry::init( 'Piecemanquantecui66' );
				$options['Emailcui']['piecesmanquantes'] = $Piecemanquantecui66->find( 'list', array( 'order' => 'name' ) );
				$options['Emailcui']['piecesmanquantes_actif'] = $Piecemanquantecui66->find( 'list', array( 'conditions' => array( 'actif' => true ), 'order' => 'name' ) );

				// Modeles d'e-mail parametrable
				$Textmailcui66 = ClassRegistry::init( 'Textmailcui66' );
				$options['Emailcui']['textmailcui66_id'] = $Textmailcui66->find( 'list', array( 'order' => 'name' ) );
				$options['Emailcui']['textmailcui66_id_actif'] = $Textmailcui66->find( 'list', array( 'conditions' => array( 'actif' => true ), 'order' => 'name' ) );
			}

			$options = Hash::merge(
				$options,
				$this->enums()
			);

			return $options;
		}
	}
?>