<?php
	/**
	 * Fichier source de la classe TransfertDonneesHorsPDIEShell.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe TransfertDonneesHorsPDIEShell ...
	 *
	 * Se lance dans public_html : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake TransfertDonneesHorsPDIE -app app [anneesource] [anneedest]
	 *
	 * @package app.Console.Command
	 */
	class TransfertDonneesHorsPDIEShell extends XShell
	{
		/**
		 * Modèles utilisés par ce shell
		 *
		 * @var array
		 */
		public $uses = array( 'Thematiquefp93', 'Categoriefp93', 'Filierefp93' );

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
			$this->out( __d('shells', 'Shell::TransfertDonneesHorsPDIE::welcome') );
			$this->out();
			$this->hr();
		}

		/**
		 *
		 */
		public function help() {
			$this->out();
			$this->out( sprintf(__d('shells', 'Shell::TransfertDonneesHorsPDIE::help::usage::centos'), 'TransfertDonneesHorsPDIE' ) );
			$this->out();
			$this->out( __d('shells', 'Shell::TransfertDonneesHorsPDIE::help::usage::exemple' ) );
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
		 * Vérifie les arguments
		 * @return boolean
		 */
		public function isArgumentsOK($arg) {
			$check = $this->Thematiquefp93->find('count', array(
				'conditions' => array(
					'yearthema' => $arg,
					'type' => 'horspdi'
				)
			));
			return $check > 0;
		}

		/**
		 * Créé le tableau de données à insérer en base de données à partir des années mis en argument
		 * @param string
		 * @param string
		 * @return array
		 */
		public function setNewData($anneeSource, $anneeDest) {
			// Récupération des données à transférer
			$dataToTransfert = $this->Thematiquefp93->find('all', array(
				'recursive' => 2,
				'conditions' => array(
					'Thematiquefp93.yearthema' => $anneeSource,
					'Thematiquefp93.type' => 'horspdi'
				)
			));

			$newdata = array();
			foreach ($dataToTransfert as $keyT => $data) {
				// Suppression des données inutiles de thématique
				unset($dataToTransfert[$keyT]['Thematiquefp93']['id']);
				unset($dataToTransfert[$keyT]['Thematiquefp93']['created']);
				unset($dataToTransfert[$keyT]['Thematiquefp93']['modified']);
				$dataToTransfert[$keyT]['Thematiquefp93']['yearthema'] = $anneeDest;
				// Suppression des données inutiles de catégories
				foreach($dataToTransfert[$keyT]['Categoriefp93'] as $keyC => $cat) {
					unset($dataToTransfert[$keyT]['Categoriefp93'][$keyC]['id']);
					unset($dataToTransfert[$keyT]['Categoriefp93'][$keyC]['thematiquefp93_id']);
					unset($dataToTransfert[$keyT]['Categoriefp93'][$keyC]['created']);
					unset($dataToTransfert[$keyT]['Categoriefp93'][$keyC]['modified']);
					unset($dataToTransfert[$keyT]['Categoriefp93'][$keyC]['Thematiquefp93']);
					// Suppression des données inutiles de filière
					foreach($dataToTransfert[$keyT]['Categoriefp93'][$keyC]['Filierefp93'] as $keyF => $fil) {
						unset($dataToTransfert[$keyT]['Categoriefp93'][$keyC]['Filierefp93'][$keyF]['id']);
						unset($dataToTransfert[$keyT]['Categoriefp93'][$keyC]['Filierefp93'][$keyF]['categoriefp93_id']);
						unset($dataToTransfert[$keyT]['Categoriefp93'][$keyC]['Filierefp93'][$keyF]['created']);
						unset($dataToTransfert[$keyT]['Categoriefp93'][$keyC]['Filierefp93'][$keyF]['modified']);
					}
				}
				$newdata[] = $dataToTransfert[$keyT];
			}
			return $newdata;
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			// Vérification de la présence des arguments
			if (!isset($this->args[0])) {
				$this->msg[] = __d('shells', 'Shells:TransfertDonneesHorsPDIE:error::noarg');
			} else if (!isset($this->args[1])) {
				$this->msg[] = __d('shells', 'Shells:TransfertDonneesHorsPDIE:error::nosecarg');
			}

			// Vérification de l'intégrité des arguments
			$anneeSource = 0;
			if($this->isArgumentsOK($this->args[0])) {
				$anneeSource = $this->args[0];
			} else {
				$this->msg[] = __d('shells', 'Shells:TransfertDonneesHorsPDIE:error::nodatasource');
			}

			$anneeDest = 0;
			if(!$this->isArgumentsOK($this->args[1])) {
				$anneeDest = $this->args[1];
			} else {
				$this->msg[] = __d('shells', 'Shells:TransfertDonneesHorsPDIE:error::datadest');
			}

			if( $anneeSource > 0 && $anneeDest > 0) {
				$datas = $this->setNewData($anneeSource, $anneeDest);
				$this->Thematiquefp93->begin();

				if( $this->Thematiquefp93->saveMany( $datas, array('validate' => false, 'deep' => true) ) ) {
					$this->Thematiquefp93->commit();
					$this->msg[] = __d('shells', "Shells:TransfertDonneesHorsPDIE:success");
				} else {
					$this->Thematiquefp93->rollback();
					$this->msg[] = __d('shells', "Shells:TransfertDonneesHorsPDIE:fail");
				}
			}
			$this->sendMessages();
		}
	}
?>