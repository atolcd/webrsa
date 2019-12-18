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
	App::uses('CakeEmail', 'Network/Email');
	App::uses( 'WebrsaEmailConfig', 'Utility' );

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
			'etat' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'CREE', 'SENT'
						)
					)
				)
			),
		);

		/**
		 *
		 * @param array $data
		 * @return boolean
		 */
		public function saveAddEdit( array $data ) {
			$Emaildestinataire = ClassRegistry::init( 'Emaildestinataire' );
			$emaildestinatairesInfos = $Emaildestinataire->find( 'first',
			array(
				'fields'=> 'Emaildestinataire.email',
				'conditions' => array (
					'Emaildestinataire.id' => $data['Email']['emaildestinataire_id']
				)
			));
			$data['Email']['emaildestinataire'] = $emaildestinatairesInfos['Emaildestinataire']['email'];

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

			// Modeles d'email parametrable
			$Textemail = ClassRegistry::init( 'Textemail' );
			$options['Email']['textemail_id'] = $Textemail->find( 'list', array( 'order' => 'name' ) );
			$options['Email']['textemail_id_actif'] = $Textemail->find( 'list', array( 'conditions' => array( 'actif' => true ), 'order' => 'name' ) );

			// Destinataires d'email parametrable
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

		/**
		 * Envoi d'un E-mail
		 *
		 * @param type $id
		 * @param type $modele
		 * @param type $action
		 * @param type $configEmail
		 * @throws Exception
		 */
		public function send( $id, $modele, $action = null, $configEmail ){
			$success = false;
			$result = $this->_getDataEmail($id, $modele, $action );
			if ( isset ($result['Email']['id']) ) {
				$email_id = $result['Email']['id'];

				$data = $result['Email'];

				$Piecemail = ClassRegistry::init( 'Piecemail' );
				$filesIds = $data['pj'] ;

				$filesNames = array();
				if( !empty ($filesIds) ) {
					foreach( $filesIds as $fileId ){
						$filesNames = array_merge( $filesNames, $Piecemail->getFichiersLiesById( $fileId ) );
					}
					$filesNames = $this->_transformOdtIntoPdf($filesNames, $result['Email']['modele_id'], $modele);
				}

				$Email = new CakeEmail( $configEmail );
				if ( !empty($data['emailredacteur']) ){
					$Email->replyTo( $data['emailredacteur'] );
					$Email->cc( $data['emailredacteur'] );
				}

				$Email	->subject( $data['titre'] )
						->attachments( $filesNames );

				// Si le mode debug est activé, on envoi l'email à l'éméteur ( @see app/Config/email.php )
				if ( WebrsaEmailConfig::isTestEnvironment() ){
					$Email->to ( WebrsaEmailConfig::getValue( $configEmail, 'to', $Email->to() ) );
				}
				else{
					$Email->to( $data['emaildestinataire'] );
				}

				$this->id = $email_id;
				$this->begin();
				if ( $Email->send( $data['message'] ) ){
					$data['Email'] = array (
						'id' => $email_id,
						'dateenvoi' => date('Y-m-d G:i:s'),
						'etat' => 'SENT'
					);
					$success = $this->save( $data, array( 'atomic' => false ) );
					if ( !$success ) {
						$this->rollback();
					}else{
						$this->commit();
						return $email_id;
					}
				}
				else{
					$this->rollback();
				}
			}
			return $success;
		}

		/**
		 * Transforme les fichiers ODT en document PDF (avec remplissage des champs)
		 *
		 * @param array $paths Liste des chemins vers les fichiers
		 * @param integer $modele_id
		 * @return array retourne la liste de fichiers avec retrait des ODT et ajout des PDF
		 */
		protected function _transformOdtIntoPdf( $paths, $modele_id, $modele_name ) {
			$newPaths = array();
			foreach ($paths as $path) {
				// Si l'extension du fichier n'est pas odt on l'ajoute à la nouvelle liste et on passe au prochain
				if ( strpos($path, '.odt') !== strlen($path)-4 ) {
					$newPaths[] = $path;
					continue;
				}

				// On sépare le chemin du nom de fichier
				$dirPath = explode(DS, $path);
				$odtName = $dirPath[count($dirPath)-1];
				unset($dirPath[count($dirPath)-1]); // Retrait du nom de fichier
				$dirPath = implode(DS, $dirPath);
				$fileName = substr($odtName, 0, strlen($odtName) -4); // Nom du fichier sans extension
				$pdfPath = $dirPath.DS.$fileName.'.pdf';

				// On récupère les données pour le remplissage du ODT
				$modele = ClassRegistry::init($modele_name);

				$query = $modele->queryImpression( $modele_id );
				$modele->forceVirtualFields = true;
				$data = $modele->find( 'first', $query );
				$options = $modele->options();
				$data = $modele->completeDataImpression( $data );
				$pdf = $this->Email->ged(
					$data,
					$path,
					true,
					$options
				);

				// On créer un nouveau fichier à coté du fichier ODT
				if (is_file($pdfPath) ) {
					unlink($pdfPath); // Supprime le fichier pdf du même nom si il existe
				}
				$File = fopen($pdfPath, 'w');
				fwrite($File, $pdf);
				fclose($File);

				// On ajoute finalement le fichier à la liste
				$newPaths[] = $pdfPath;
			}

			return $newPaths;
		}

	}
?>