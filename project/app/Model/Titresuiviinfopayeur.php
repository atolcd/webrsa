<?php
	/**
	 * Code source de la classe Titresuiviinfopayeur.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Titresuiviinfopayeur ...
	 *
	 * @package app.Model
	 */
	class Titresuiviinfopayeur extends AppModel
	{
		public $name = 'Titresuiviinfopayeur';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'titressuivisinfospayeurs';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Belongs To".
		 * @var array
		 */
		public $belongsTo = array(
			'Typetitrecreancierinfopayeur' => array(
				'className' => 'Typetitrecreancierinfopayeur',
				'foreignKey' => 'typesinfopayeur_id',
				'conditions' => array('Typetitrecreancierinfopayeur.actif' => 1),
				'fields' => '',
				'order' => ''
			),
			'Titrecreancier' => array(
				'className' => 'Titrecreancier',
				'foreignKey' => 'titrecreancier_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			)
		);

		/** Récupère les infos payeurs par rapport à un titre créancier défini
		 * puis lui ajoutes les droits d'ajout / suppression / vue / réponse du payeur
		 *
		 * @param int id
		 *
		 * @return array
		 */
		public function getList($titrecreancier_id) {
			$titresInfosPayeurs = $this->find('all', array(
				'conditions' => array(
					'Titresuiviinfopayeur.titrecreancier_id' => $titrecreancier_id
				)
			));

			foreach($titresInfosPayeurs as $nbInfo => $info) {
				/*Toujours actifs*/
				$info['/Titressuivisinfospayeurs/add'] = true;
				$info['/Titressuivisinfospayeurs/view'] = true;
				//Récupération de l'e-mail
				$Email = ClassRegistry::init('Email');
				$emailInfos = $Email->find( 'first',
					array(
						'fields'=> 'Email.etat',
						'conditions' => array (
							'Email.modele' => 'Titresuiviinfopayeur',
							'Email.modele_id' => $info['Titresuiviinfopayeur']['id'],
						)
					)
				);
				// Si l'email existe
				if (! empty ($emailInfos['Email']['etat'])) {
					//On ne modifie plus et on n'envoit plus
					$info['/Titressuivisinfospayeurs/edit'] = false;
					$info['/Titressuivisinfospayeurs/emailsend'] = false;
					//Mais on peut renseigner une réponse
					$info['/Titressuivisinfospayeurs/answer'] = true;
				}else{
					//Sans e-mail
					$info['/Titressuivisinfospayeurs/edit'] = true;
					$info['/Titressuivisinfospayeurs/emailsend'] = true;
					$info['/Titressuivisinfospayeurs/answer'] = true;
					//Si on as un retour payeur
					if (!empty ($info['Titresuiviinfopayeur']['retourpayeur'])) {
						$info['/Titressuivisinfospayeurs/delete'] = false;
					}else{
						$info['/Titressuivisinfospayeurs/delete'] = true;
					}
				}
				$titresInfosPayeurs[$nbInfo] = $info;
			}

			return $titresInfosPayeurs;
		}

	}
