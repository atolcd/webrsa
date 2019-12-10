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
			'ajax_generate_email' => 'read',
		);

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
					$formatedDate = utf8_encode ( $formatedDate );
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
			$modleinfos = $model->find('first', array ('recursive' => 0, 'conditions' => array ( $parentmodele.".id" => $parentmodele_id ) ));

			$options = $model->options();

			foreach( $matches[1] as $key => $value ){
				$modelName = $value;
				$fieldName = $matches[2][$key];

				if ( $modelName !== 'Email' && isset($modleinfos[$modelName]) ){
					$fieldValue = $modleinfos[$modelName][$fieldName];

					$isDate = preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/", $fieldValue);

					if ( $isDate ){
						$tradFieldValue = strftime("%A %d %B %Y", strtotime($fieldValue));
						$tradFieldValue = utf8_encode ( $tradFieldValue );
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
