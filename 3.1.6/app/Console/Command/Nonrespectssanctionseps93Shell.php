<?php
	/**
	 * Fichier source de la classe Nonrespectssanctionseps93Shell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe Nonrespectssanctionseps93Shell permet de créer un dossier d'EP
	 * pour la thématique "Non respects et sanctions" (CG 93) pour tous les
	 * allocataires ayant besoin d'un second passage automatique (personne orientée
	 * sans contrat ou personne orientée avec contrat) pour cette thématique.
	 *
	 * @package app.Console.Command
	 */
	class Nonrespectssanctionseps93Shell extends XShell
	{
		/**
		 * Modèles utilisés par ce shell
		 *
		 * @var array
		 */
		public $uses = array( 'Nonrespectsanctionep93' );

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
		 * Affiche l'en-tête du shell
		 */
		public function _welcome() {
			$this->out();
			$this->out( 'Shell de création de dossiers d\'EP pour second passage.' );
			$this->out();
			$this->hr();
			$this->out();
		}

		/**
		 * Surcharge de la méthode startup pour vérifier que le département soit
		 * uniquement le 93.
		 */
		public function startup() {
			parent::startup();

			$this->checkDepartement( 93 );
		}

		/**
		 * Méthode principale.
		 */
		public function main() {
			$querydata = $this->Nonrespectsanctionep93->qdSecondsPassagesCerOrientstruct();
			$results = $this->Nonrespectsanctionep93->find( 'all', $querydata );

			if( !empty( $results ) ) {
				$success = true;
				$this->Nonrespectsanctionep93->begin();

				foreach( $results as $result ) {
					$dossierep = array(
						'Dossierep' => array(
							'personne_id' => $result['Dossierep']['personne_id'],
							'themeep' => $result['Dossierep']['themeep'],
						)
					);
					$this->Nonrespectsanctionep93->Dossierep->create( $dossierep );
					$success = $this->Nonrespectsanctionep93->Dossierep->save() && $success;

					if( $success ) {
						$nonrespectsanctionep93 = array(
							'Nonrespectsanctionep93' => array(
								'dossierep_id' => $this->Nonrespectsanctionep93->Dossierep->id,
								'propopdo_id' => null,
								'orientstruct_id' => $result['Nonrespectsanctionep93']['orientstruct_id'],
								'contratinsertion_id' => $result['Nonrespectsanctionep93']['contratinsertion_id'],
								'origine' => $result['Nonrespectsanctionep93']['origine'],
								'rgpassage' => $result['Nonrespectsanctionep93']['rgpassage'] + 1,
								'sortieprocedure' => null,
								'active' => '1',
								'historiqueetatpe_id' => null,
							)
						);
						$this->Nonrespectsanctionep93->create( $nonrespectsanctionep93 );
						$success = $this->Nonrespectsanctionep93->save() && $success;
					}
				}

				if( $success ) {
					$this->Nonrespectsanctionep93->commit();
					$this->out( '<success>'.sprintf( 'Succès pour l\'enregistrement des %s dossiers EP pour la thématique "non respect / sanctions (CG 93)"', count( $results ) ).'</success>' );
					$this->_stop( 0 );
				}
				else {
					$this->Nonrespectsanctionep93->rollback();
					$this->out( $out = '<error>'.sprintf( 'Erreur(s) lors de l\'enregistrement des %s dossiers EP pour la thématique "non respect / sanctions (CG 93)"', count( $results ) ).'</error>' );
					$this->_stop( 2 );
				}
			}
			else {
				$this->out( '<info>Aucun dossier d\'EP à créer pour la thématique "non respect / sanctions (CG 93)"</info>' );
				$this->_stop( 0 );
			}
		}
	}
?>