<?php
	/**
	 * Fichier source de la classe InitTableauxsuivispdvs93Shell.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe InitTableauxsuivispdvs93Shell ...
	 *
	 * Se lance dans public_html : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake InitTableauxsuivispdvs93 -app app
	 *
	 * @package app.Console.Command
	 */
	class InitTableauxsuivispdvs93Shell extends XShell
	{
		/**
		 * Modèles utilisés par ce shell
		 *
		 * @var array
		 */
		public $uses = array( 'Categoriefp93' );

		/**
		 * Paramètres par défaut pour ce shell
		 *
		 * @var array
		 */
		public $defaultParams = array(
			'log' => false,
			'logpath' => LOGS,
			'verbose' => false,
		);

		/**
		 * Liste des messages gérer par le shell
		 */
		public $msg = array();

		/**
		 * Affiche l'en-tête du shell
		 */
		public function _welcome() {
			$this->out();
			$this->out( __d('shells', 'Shell::InitTableauxsuivispdvs93::welcome') );
			$this->out();
			$this->hr();
		}

		/**
		 *
		 */
		public function help() {
			$this->out();
			$this->out( sprintf( __d('shells', 'Shell::help::usage::centos'), 'InitTableauxsuivispdvs93' ) );
			$this->out();
			$this->_stop( 0 );
		}

		/**
		 * Envoie les messages du traitement
		 */
		public function sendMessages() {
			foreach( $this->msg as $message ) {
				$this->out($message);
				$this->out();
			}
		}

		/**
		 * Méthode gérant la mise à jour du tableau mis en paramètre
		 * @param array
		 * @param string
		 *
		 * @return boolean
		 */
		protected function _gestionUpdate($tableau, $nomTableau) {
			$conditionsOR = array('OR' => array());

			// Modification des paramètres de récupération des catégories
			// Ici les clés correspondent à ce que l'on peut trouver dans la variable de configuration
			// Les valeurs correspondent à ce que l'on souhaite comme condition au final
			$changeCondition = array(
				'Thematiquefp93.name' => 'Thematiquefp93.name ILIKE',
				'Categoriefp93.name LIKE' => 'Categoriefp93.name',
				'Categoriefp93.name' => 'Categoriefp93.name ILIKE',
			);

			// La transformation de la variable de configuration ($tableau) dans ce foreach
			// se fait via une transformation en JSON pour modifier toutes les valeurs que l'on trouve
			// quelque soit la profondeur de la clé, puis une retransformation du tout en tableau associatif
			foreach($changeCondition as $origin => $dest) {
				$tableau = json_decode(str_replace($origin, $dest, json_encode($tableau)), true);
			}

			// Création de la condition
			foreach( array_keys( $tableau ) as $thematiqueName ) {
				foreach( array_keys( $tableau[$thematiqueName] ) as $categorieName ) {
					$conditionsOR['OR'][] = $tableau[$thematiqueName][$categorieName];
				}
			}

			// Récupération des id liés
			$categories_id = $this->Categoriefp93->find('list', array(
				'fields' => 'Categoriefp93.id',
				'recursive' => -1,
				'joins' => array(
					$this->Categoriefp93->join('Thematiquefp93', array( 'type' => 'INNER'))
				),
				'conditions' => $conditionsOR
			));

			// Update des tableaux
			$success = $this->Categoriefp93->updateAllUnBound(
				array("{$nomTableau}_actif" => true),
				array('id' => $categories_id)
			);

			if($success) {
				return count($categories_id);
			}
			return $success;
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			// Gestion du tableau 4
			$nbCategorieUpdated = $this->_gestionUpdate(Configure::read('Tableausuivi93.tableau1b4.categories'), 'tableau4' );
			if( $nbCategorieUpdated ) {
				$this->msg[] =  sprintf(__d('shells', 'Shell::InitTableauxsuivispdvs93::nbCategorieUpdated'), $nbCategorieUpdated, 4 );
			} else {
				$this->msg[] =  sprintf(__d('shells', 'Shell::InitTableauxsuivispdvs93::error'), 4 );
			}

			$nbCategorieUpdated = $this->_gestionUpdate(Configure::read('Tableausuivi93.tableau1b5.categories'), 'tableau5' );
			// Gestion du tableau 5
			if( $nbCategorieUpdated ) {
				$this->msg[] = sprintf(__d('shells', 'Shell::InitTableauxsuivispdvs93::nbCategorieUpdated'), $nbCategorieUpdated, 5 );
			} else {
				$this->msg[] = sprintf(__d('shells', 'Shell::InitTableauxsuivispdvs93::error'), 5 );
			}
			$this->sendMessages();
		}
	}
?>