<?php
	/**
	 * Code source de la classe Emails.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );
	App::uses( 'CakeEmail', 'Network/Email' );
	App::uses( 'WebrsaEmailConfig', 'Utility' );

	/**
	 * La classe Emails ...
	 *
	 * @package app.Controller
	 */
	class EmailsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Emails';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'DossiersMenus',
			'Fileuploader',
			'Gedooo.Gedooo',
			'Gestionzonesgeos',
			'Jetons2',
			'WebrsaAccesses',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Ajax2' => array(
				'className' => 'Prototype.PrototypeAjax',
				'useBuffer' => false
			),
			'Cake1xLegacy.Ajax',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Fileuploader',
			'Observer' => array(
				'className' => 'Prototype.PrototypeObserver',
				'useBuffer' => true
			),
			'Romev3',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Email',
			'Option',
			'Personne',
			'WebrsaEmail',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'email',
			'view',
			'send',
			'ajax_generate_email',
			'delete'
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'email' => 'read',
			'view' => 'read',
			'send' => 'create',
			'ajax_generate_email' => 'read',
		);

		/**
		 * Liste des Emails de l'élément.
		 *
		 * @param integer $personne_id
		 */
		/*public function email(Model $model, $user_id = null, $modele_id = null, $personne_id, $urlmenu ) {
			$this->WebrsaAccesses->setMainModel($model)->check($modele_id, $user_id);
			if ( empty($user_id) || empty($modele_id) || !is_numeric($user_id) || !is_numeric($modele_id) ){
				throw new NotFoundException();
			}
			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );

			$params = WebrsaAccessEmail::getParamsList(array('isModuleEmail' => true));
			$paramsAccess = $this->WebrsaEmail->getParamsForAccess($modele_id, $params);
			$query = $this->WebrsaEmail->completeVirtualFieldsForAccess(
				array(
					'fields' => array(
						'Email.id',
						'Email.modele_id',
						'Email.user_id',
						'Email.titre',
						'Email.created',
						'Email.dateenvoi',
					),
					'conditions' => array(
						'Email.modele_id' => $modele_id
					),
					'order' => array( 'Email.created DESC' )
				)
			);
			$results = WebrsaAccessEmail::accesses($this->Email->find('all', $query), $paramsAccess);
			$ajoutPossible = Hash::get($paramsAccess, 'ajoutPossible') !== false;
			$messages = $this->Email->messages( $personne_id );

			// Options
			$options = $this->Email->WebrsaEmail->options();

			$this->set( compact( 'results', 'dossierMenu', 'messages', 'ajoutPossible', 'personne_id', 'options', 'cui_id', 'urlmenu' ) );
		}*/

		/**
		 * Envoi d'un E-mail
		 *
		 * @param type $personne_id
		 * @param type $cui_id
		 * @param type $email_id
		 * @throws Exception
		 */
		public function send( $email_id ){
			$this->WebrsaAccesses->setWebrsaModel('WebrsaEmail')->setMainModel('Email')
					->check($email_id);
			$datas = $this->Email->find('first', array( 'conditions' => array( 'Email.id' => $email_id ) ) );
			$data = $datas['Email'];

			$Piecemail = ClassRegistry::init( 'Piecemail' );

			$filesIds = explode( '_', $data['pj'] );

			$filesNames = array();
			foreach( $filesIds as $fileId ){
				$filesNames = array_merge( $filesNames, $Piecemail->getFichiersLiesById( $fileId ) );
			}

			$filesNames = $this->_transformOdtIntoPdf($filesNames, $cui_id);

			$Email = new CakeEmail( $this->configEmail );
			if ( !empty($data['emailredacteur']) ){
				$Email->replyTo( $data['emailredacteur'] );
				$Email->cc( $data['emailredacteur'] );
			}

			$Email	->subject( $data['titre'] )
					->attachments( $filesNames );

			// Si le mode debug est activé, on envoi l'e-mail à l'éméteur ( @see app/Config/email.php )
			if ( WebrsaEmailConfig::isTestEnvironment() ){
				$Email->to ( WebrsaEmailConfig::getValue( $this->configEmail, 'to', $Email->to() ) );
			}
			else{
				$Email->to( $data['emailemployeur'] );
			}

			$this->Email->id = $email_id;
			$this->Email->begin();
			try {
				if ( $Email->send( $data['message'] ) ){
					$this->Flash->success( 'E-mail envoyé avec succès' );
					if ( !$this->Email->saveField( 'dateenvoi', date('Y-m-d G:i:s') ) ){
						$this->Email->rollback();
						$this->Flash->error( 'Sauvegarde en base impossible!' );
					}
					$this->Email->commit();
				}
				else{
					$this->Email->rollback();
					throw new Exception( 'Envoi E-mail échoué!' );
				}
			}
			catch (Exception $e) {
				$this->Email->rollback();
				$this->Flash->error( 'Erreur lors de l\'envoi de l\'E-mail.' );
			}

			$this->Cui->Cui66->WebrsaCui66->updatePositionsCuisById( $cui_id );

			$this->redirect( array( 'action' => 'email', $personne_id, $cui_id ) );
		}

		/**
		 * Permet de récupérer en base les informations nécéssaire afin de générer le texte d'un e-mail
		 */
		public function ajax_generate_email(){

			$query = array(
				'conditions' => array(
					'id' => $this->request->data['Email_textemail_id']
				)
			);
			$modelEmail = ClassRegistry::init( 'Textemail' )->find( 'first', $query );

			$text = $modelEmail['Textemail']['contenu'];
			preg_match_all('/#([A-Z][a-z_0-9]+)\.([a-z_0-9]+)#/', $text, $matches);

			$erreurs = array();
			foreach( $this->request->data as $input => $data ){
				if ( $input === 'Email_insertiondate' ){
					$formatedDate = strftime("%A %d %B %Y", strtotime($data));
					$text = str_replace( '#Email.insersiondate#', $formatedDate, $text );
				}
				elseif ( preg_match( '/^Email_[\w]+$/', $input ) ){
					$input = str_replace( '.id', '_id', str_replace( '_', '.', $input ) );
					$text = str_replace( '#' . $input . '#', $data, $text );
				}
			}

			$parentmodele = $this->request->data['Email_modeleparent'];
			$parentmodele_id = $this->request->data['Email_modeleparent_id'];

			App::import('Model', $parentmodele);
			$model = new $parentmodele;
			$modleinfos = $model->find('first', array ( 'conditions' => array ( $parentmodele.".id" => $parentmodele_id ) ));

			$options = $model->options();

			foreach( $matches[1] as $key => $value ){
				$modelName = $value;
				$fieldName = $matches[2][$key];

				if ( $modelName !== 'Email' && isset($modleinfos[$modelName]) ){
					$fieldValue = $modleinfos[$modelName][$fieldName];

					$isDate = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/", $fieldValue);

					if ( $isDate ){
						$tradFieldValue = strftime("%A %d %B %Y", strtotime($fieldValue));
					}
					else{
						$tradFieldValue = isset( $options[$modelName][$fieldName][$fieldValue] ) ?
							$options[$modelName][$fieldName][$fieldValue] :
							$fieldValue
						;
					}

					if ( empty($tradFieldValue) ){
						$erreurs[] = __d( 'email', $modelName . '.' . $fieldName );
					}
					else{
						$text = str_replace( '#' . $modelName . '.' . $fieldName . '#', $tradFieldValue, $text );
					}
				}
			}

			// On retire les retours à la ligne en trop
			$text = preg_replace('/[\n\r]{3,}/', "\n\n", $text);

			if ( !empty($erreurs) ){
				$text = "[[[----------ERREURS----------]]]\n" . implode("\n", $erreurs) . "\n\nMerci de compléter les champs requis pour envoyer cet e-mail.";
			}

			$json = array(
				'EmailTitre' => $modelEmail['Textemail']['sujet'],
				'EmailMessage' => $text
			);
			$this->set( compact( 'json' ) );
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}
	}
?>
