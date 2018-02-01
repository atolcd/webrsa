<?php
	/**
	 * Fichier source de la classe AutomatisationsepsShell.
	 *
	 * PHP 5.3
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'XShell', 'Console/Command' );

	/**
	 * La classe AutomatisationsepsShell ...
	 *
	 * @package app.Console.Command
	 */
	class AutomatisationsepsShell extends XShell
	{
		/**
		 * Modèles utilisés par le shell.
		 *
		 * @var array
		 */
		public $uses = array(
			'Nonrespectsanctionep93',
			'Propopdo'
		);

		/**
		 *
		 * @return type
		 */
		public function getOptionParser() {
			$parser = parent::getOptionParser();
			$parser->description( 'Le script a pour but de détecter les personnes possédant une DO19 mais n\'ayant pas signer de CER 1 mois suivant la décision du PCG sur cette DO19.' );
			return $parser;
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
		 * Retourne la liste des DO 19 à traiter.
		 *
		 * @return array
		 */
		protected function _propospdos() {
			$query = array(
				'joins' => array(
					$this->Propopdo->join( 'Decisionpropopdo', array( 'type' => 'INNER' ) ),
					$this->Propopdo->Decisionpropopdo->join( 'Decisionpdo', array( 'type' => 'INNER' ) )
				),
				'contain' => false,
				'conditions' => array(
					'Decisionpdo.libelle LIKE' => 'DO 19%',
					'Decisionpropopdo.datedecisionpdo IS NOT NULL',
					// La date de décision de la PDO doit être supérieure à celle de la validation du CER
					// Une fois la décision émise, le CER doit être validé par la suite et non pas avant
					// + intervalle d'1 mois entre la date de décision et la validation du CER
					'Propopdo.personne_id NOT IN (
						SELECT contratsinsertion.personne_id
							FROM contratsinsertion
							WHERE
								contratsinsertion.personne_id = Propopdo.personne_id
								AND date_trunc( \'day\', contratsinsertion.datevalidation_ci ) >= Decisionpropopdo.datedecisionpdo
								AND date_trunc( \'day\', contratsinsertion.datevalidation_ci ) <= ( Decisionpropopdo.datedecisionpdo + INTERVAL \''.Configure::read( 'Nonrespectsanctionep93.intervalleCerDo19' ).'\' )
					)',
					// Qui n'ont pas de dossier d'EP pas encore associé à une commission
					'Propopdo.personne_id NOT IN (
						SELECT dossierseps.personne_id
							FROM dossierseps
							WHERE
								dossierseps.personne_id = Propopdo.personne_id
								AND dossierseps.actif = \'1\'
								AND dossierseps.themeep = \'nonrespectssanctionseps93\'
								AND dossierseps.id NOT IN (
									SELECT passagescommissionseps.dossierep_id
										FROM passagescommissionseps
										WHERE passagescommissionseps.dossierep_id = dossierseps.id
								)
					)',
					// Et qui n'ont pas de dossier d'EP en train de passer en commission
					'Propopdo.personne_id NOT IN (
						SELECT dossierseps.personne_id
							FROM dossierseps
							INNER JOIN passagescommissionseps ON (
								passagescommissionseps.dossierep_id = dossierseps.id
							)
							WHERE
								dossierseps.personne_id = Propopdo.personne_id
								AND dossierseps.actif = \'1\'
								AND dossierseps.themeep = \'nonrespectssanctionseps93\'
								AND passagescommissionseps.etatdossierep NOT IN ( \'traite\', \'annule\' )
					)',
					// Et qui n'ont pas de dossier d'EP passé en commission depuis moins que le délai entre deux passages en commission
					'Propopdo.personne_id NOT IN (
						SELECT dossierseps.personne_id
							FROM dossierseps
							INNER JOIN passagescommissionseps ON (
								passagescommissionseps.dossierep_id = dossierseps.id
							)
							INNER JOIN commissionseps ON (
								passagescommissionseps.commissionep_id = commissionseps.id
							)
							WHERE
								dossierseps.personne_id = Propopdo.personne_id
								AND dossierseps.themeep = \'nonrespectssanctionseps93\'
								AND passagescommissionseps.etatdossierep IN ( \'traite\', \'annule\' )
								AND ( commissionseps.dateseance + INTERVAL \''.Configure::read( 'Nonrespectsanctionep93.intervalleCerDo19' ).' days\' ) <= NOW()
					)',
					// Et qui ne sont pas sortis de la procédure pour la même DO
					'Propopdo.id NOT IN (
						SELECT nonrespectssanctionseps93.propopdo_id
							FROM nonrespectssanctionseps93
								INNER JOIN dossierseps ON (
									nonrespectssanctionseps93.dossierep_id = dossierseps.id
								)
							WHERE
								nonrespectssanctionseps93.propopdo_id = Propopdo.id
								AND nonrespectssanctionseps93.sortieprocedure IS NOT NULL
					)'
				)
			);

			return $this->Propopdo->find( 'all', $query );
		}

		/**
		 * Retourne les données à enregistrer dans le nouveau dossier d'EP.
		 *
		 * @param array $propopdo
		 * @return array
		 */
		protected function _dossierep( array $propopdo ) {
			$query = array(
				'conditions' => array(
					'Dossierep.themeep' => 'nonrespectssanctionseps93',
					'Nonrespectsanctionep93.origine' => 'pdo',
					'Nonrespectsanctionep93.propopdo_id' => $propopdo['Propopdo']['id'],
					'Nonrespectsanctionep93.sortieprocedure IS NULL',
					'Nonrespectsanctionep93.active' => 0
				),
				'joins' => array(
					array(
						'alias' => 'Nonrespectsanctionep93',
						'table' => 'nonrespectssanctionseps93',
						'type' => 'INNER',
						'conditions' => array(
							'Nonrespectsanctionep93.dossierep_id = Dossierep.id'
						)
					)
				),
				'contain' => false
			);

			$nbpassagespcd = $this->Nonrespectsanctionep93->Dossierep->find( 'count', $query );

			$dossierep = array(
				'Dossierep' => array(
					'personne_id' => $propopdo['Propopdo']['personne_id'],
					'themeep' => 'nonrespectssanctionseps93',
				),
				'Nonrespectsanctionep93' => array(
					'propopdo_id' => $propopdo['Propopdo']['id'],
					'origine' => 'pdo',
					'rgpassage' => ( $nbpassagespcd + 1 )
				)
			);

			return $dossierep;
		}

		/**
		 *
		 */
		public function main() {
			$success = true;
			$out = array( );
			$thrown = null;

			$propospdos = $this->_propospdos();

			if( count( $propospdos ) > 0 ) {
				$this->Propopdo->begin();

				try {
					$this->XProgressBar->start( count( $propospdos ) );
					foreach( $propospdos as $propopdo ) {
						$dossierep = $this->_dossierep( $propopdo );
						$tmpSuccess = $this->Nonrespectsanctionep93->saveAll( $dossierep, array( 'atomic' => false ) );
						$success = !empty( $tmpSuccess ) && $success;
						$this->XProgressBar->next();
					}
				} catch( PDOException $e ) {
					$thrown = $e;
					$success = false;
					$this->log( $e->getMessage(), LOG_ERR );
				}

				if( $success ) {
					$this->Propopdo->commit();
					$out[] = '<success>'.sprintf( 'Succès pour l\'enregistrement des %s dossiers EP pour la thématique "non respect / sanctions (CG 93)"', count( $propospdos ) ).'</success>';
				}
				else {
					$this->Propopdo->rollback();
					$out[] = '<error>'.sprintf( 'Erreur(s) lors de l\'enregistrement des %s dossiers EP pour la thématique "non respect / sanctions (CG 93)"', count( $propospdos ) ).'</error>';
					if( null !== $thrown ) {
						$message = sprintf( "%s\nSQL: %s", $thrown->getTraceAsString(), $thrown->queryString );
						$this->log( $message, LOG_ERR );
					}
				}
			}
			else {
				$out[] = '<success>Aucun dossier EP pour la thématique "non respect / sanctions (CG 93)" à traiter</success>';
			}

			$this->out();
			$this->out( $out );

			$this->_stop( $success ? self::SUCCESS : self::ERROR );
		}
	}
?>