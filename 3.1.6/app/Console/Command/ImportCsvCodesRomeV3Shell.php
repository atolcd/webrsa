<?php
	/**
	 * Code source de la classe ImportCsvCodesRomeV3Shell.
	 *
	 * PHP 5.3
	 *
	 * sudo -u www-data lib/Cake/Console/cake import_csv_codes_rome_v3 "/home/cbuffin/Bureau/WebRSA/2014/2.8.0/En cours/CG 66 - Codes ROME v3/ROMEv3/codes_rome_v3_emboites.csv"
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'CsvAbstractImporterShell', 'Csv.Console/Command/Abstract' );

	/**
	 * La classe ImportCsvCodesRomeV3Shell ...
	 *
	 * @package app.Console.Command
	 */
	class ImportCsvCodesRomeV3Shell extends CsvAbstractImporterShell
	{
		/**
		 * Les modèles utilisés par ce shell.
		 *
		 * Il faut que ces modèles soient uniquement les modèles qui servent à
		 * l'enregistrement d'une ligne et qu'ils soient dans le bon ordre.
		 *
		 * @var array
		 */
		public $uses = array( 'Familleromev3', 'Domaineromev3', 'Metierromev3', 'Appellationromev3', 'Correspondanceromev2v3' );

		/**
		 * Les tâches utilisées par ce shell.
		 *
		 * @var array
		 */
		public $tasks = array( 'XProgressBar' );

		/**
		 * Les en-têtes par défaut tels qu'ils sont attendus.
		 *
		 * @var array
		 */
		protected $_defaultHeaders = array(
			'ROME V3',
			'Intitulé ROME V3',
			'Appellation V3',
			'Code Domaine',
			'Intitulé Domaine',
			'Code Famille',
			'Intitulé famille',
			'Correspondance ROME V2',
			'Intitulé ROME V2',
			'Appellation V2'
		);

		/**
		 * Tableau de correspondances entre les en-têtes et des chemins de
		 * modèles CakePHP.
		 *
		 * @var array
		 */
		protected $_correspondances = array(
			'Metierromev3.code',
			'Metierromev3.name',
			'Appellationromev3.name',
			'Domaineromev3.code',
			'Domaineromev3.name',
			'Familleromev3.code',
			'Familleromev3.name',
			'Coderomemetierdsp66.code',
			'Coderomemetierdsp66.name',
			'Correspondanceromev2v3.appellationromev2'
		);

		/**
		 * Cache mémoire pour les entrées du modèle Coderomemetierdsp66; la clé
		 * est le code métier.
		 *
		 * @var array
		 */
		protected $_cache_coderomemetierdsp66 = array();

		/**
		 * Nettoyage des valeurs des champs (suppression des espaces excédentaires)
		 * et transformation des clés via les correspondances.
		 *
		 * @todo Voir avec le behavior Validation2Formattable
		 *
		 * @param array $row
		 * @return array
		 */
		public function normalizeRow( array $row ) {
			$new = array();

			foreach( $row as $key => $value ) {
				if( isset( $this->_correspondances[$key] ) ) {
					$new = Hash::insert(
						$new,
						$this->_correspondances[$key],
						trim( preg_replace( '/[ ]+/', ' ', $value ) )
					);
				}
			}

			return $new;
		}

		/**
		 * Nettoyage et normalisation de la ligne d'en-tête.
		 *
		 * @param array $headers
		 * @return array
		 */
		public function processHeaders( array $headers ) {
			foreach( $headers as $key => $value ) {
				$headers[$key] = preg_replace( '/[\W_ ]+/', ' ', noaccents_upper( trim( $value ) ) );
			}

			return $headers;
		}

		/**
		 * Recherche ou insertion du code famille ROME V3.
		 *
		 * @param array $row
		 * @param array $data
		 * @return array|false
		 */
		protected function _processFamilleromev3( array $row, array $data ) {
			$familleromev3 = array(
				'Familleromev3' => array(
					'code' => Hash::get( $data, 'Familleromev3.code' ),
					'name' => Hash::get( $data, 'Familleromev3.name' )
				)
			);

			$query = array(
				'conditions' => array(
					'Familleromev3.code' => $familleromev3['Familleromev3']['code']
				)
			);

			$record = $this->Familleromev3->find( 'first', $query );

			if( empty( $record ) ) {
				$this->Familleromev3->create( $familleromev3 );
				$record = $this->Familleromev3->save();

				if( empty( $record ) ) {
					$this->rejectRow( $row, $this->Familleromev3 );
				}
			}

			return $record;
		}

		/**
		 * Recherche ou insertion du code domaine ROME V3.
		 *
		 * @param array $row
		 * @param array $data
		 * @param array|boolean $familleromev3
		 * @return array|false
		 */
		protected function _processDomaineromev3( array $row, array $data, $familleromev3 ) {
			$record = false;

			if( !empty( $familleromev3 ) ) {
				$domaineromev3 = array(
					'Domaineromev3' => array(
						'familleromev3_id' => Hash::get( $familleromev3, 'Familleromev3.id' ),
						'code' => Hash::get( $data, 'Domaineromev3.code' ),
						'name' => Hash::get( $data, 'Domaineromev3.name' )
					)
				);

				// INFO: il faut enlever le code famille...
				$domaineromev3['Domaineromev3']['code'] = preg_replace( "/^{$familleromev3['Familleromev3']['code']}/", '', $domaineromev3['Domaineromev3']['code'] );

				$query = array(
					'conditions' => array(
						'Domaineromev3.familleromev3_id' => Hash::get( $familleromev3, 'Familleromev3.id' ),
						'Domaineromev3.code' => $domaineromev3['Domaineromev3']['code']
					)
				);

				$record = $this->Domaineromev3->find( 'first', $query );

				if( empty( $record ) ) {
					$this->Domaineromev3->create( $domaineromev3 );
					$record = $this->Domaineromev3->save();

					if( empty( $record ) ) {
						$this->rejectRow( $row, $this->Domaineromev3 );
					}
				}
			}

			return $record;
		}

		/**
		 * Recherche ou insertion du code métier ROME V3.
		 *
		 * @param array $row
		 * @param array $data
		 * @param array|boolean $familleromev3
		 * @param array|boolean $domaineromev3
		 * @return array|false
		 */
		protected function _processMetierromev3( array $row, array $data, $familleromev3, $domaineromev3 ) {
			$record = false;

			if( !empty( $familleromev3 ) && !empty( $domaineromev3 ) ) {
				$metierromev3 = array(
					'Metierromev3' => array(
						'domaineromev3_id' => Hash::get( $domaineromev3, 'Domaineromev3.id' ),
						'code' => Hash::get( $data, 'Metierromev3.code' ),
						'name' => Hash::get( $data, 'Metierromev3.name' )
					)
				);

				// INFO: il faut enlever le code domainepro et le code famille...
				$metierromev3['Metierromev3']['code'] = preg_replace( "/^{$familleromev3['Familleromev3']['code']}{$domaineromev3['Domaineromev3']['code']}/", '', $metierromev3['Metierromev3']['code'] );

				$query = array(
					'conditions' => array(
						'Metierromev3.domaineromev3_id' => Hash::get( $domaineromev3, 'Domaineromev3.id' ),
						'Metierromev3.code' => $metierromev3['Metierromev3']['code']
					)
				);

				$record = $this->Metierromev3->find( 'first', $query );

				if( empty( $record ) ) {
					$this->Metierromev3->create( $metierromev3 );
					$record = $this->Metierromev3->save();

					if( empty( $record ) ) {
						$this->rejectRow( $row, $this->Metierromev3 );
					}
				}
			}

			return $record;
		}

		/**
		 * Recherche ou insertion de l'appellation ROME V3.
		 *
		 * @param array $row
		 * @param array $data
		 * @param array|false $metierromev3
		 */
		protected function _processAppellationromev3( array $row, array $data, $metierromev3 ) {
			$record = false;

			if( !empty( $metierromev3 ) ) {
				$appellationromev3 = array(
					'Appellationromev3' => array(
						'metierromev3_id' => Hash::get( $metierromev3, 'Metierromev3.id' ),
						'name' => Hash::get( $data, 'Appellationromev3.name' )
					)
				);

				$query = array(
					'conditions' => array(
						'Appellationromev3.metierromev3_id' => Hash::get( $metierromev3, 'Metierromev3.id' ),
						'Appellationromev3.name' => $appellationromev3['Appellationromev3']['name']
					)
				);

				$record = $this->Appellationromev3->find( 'first', $query );

				if( empty( $record ) ) {
					$this->Appellationromev3->create( $appellationromev3 );
					$record = $this->Appellationromev3->save();

					if( empty( $record ) ) {
						$this->rejectRow( $row, $this->Appellationromev3 );
					}
				}
			}

			return $record;
		}

		/**
		 * Recherche ou insertion de la correspondance entre les codes ROME V2 et V3.
		 *
		 * @param array $row
		 * @param array $data
		 * @param array|false $metierromev3
		 * @param array|false $appellationromev3
		 * @return array|false
		 */
		protected function _processCorrespondanceromev2v3( array $row, array $data, $metierromev3, $appellationromev3 ) {
			$record = true;

			if( !empty( $metierromev3 ) && !empty( $appellationromev3 ) ) {
				$code = Hash::get( $data, 'Coderomemetierdsp66.code' );

				$coderomemetierdsp66 = Hash::get( $this->_cache_coderomemetierdsp66, $code );

				if( $coderomemetierdsp66 === null ) {
					$query = array(
						'conditions' => array(
							'Coderomemetierdsp66.code' => $code
						),
						'contain' => false
					);

					$coderomemetierdsp66 = $this->Correspondanceromev2v3->Coderomemetierdsp66->find( 'first', $query );
					$this->_cache_coderomemetierdsp66[$code] = $coderomemetierdsp66;
				}

				if( !empty( $coderomemetierdsp66 ) ) {
					$correspondanceromev2v3 = array(
						'Correspondanceromev2v3' => array(
							'coderomemetierdsp66_id' => Hash::get( $coderomemetierdsp66, 'Coderomemetierdsp66.id' ),
							'appellationromev2' => Hash::get( $data, 'Correspondanceromev2v3.appellationromev2' ),
							'metierromev3_id' => Hash::get( $metierromev3, 'Metierromev3.id' ),
							'appellationromev3_id' => Hash::get( $appellationromev3, 'Appellationromev3.id' )
						)
					);

					$record = $this->Correspondanceromev2v3->find( 'first', array( 'conditions' => Hash::flatten( $correspondanceromev2v3) ) );

					if( empty( $record ) ) {
						$this->Correspondanceromev2v3->create( $correspondanceromev2v3 );
						$record = $this->Correspondanceromev2v3->save();

						if( empty( $record ) ) {
							$this->rejectRow( $row, $this->Correspondanceromev2v3 );
						}
					}
				}
			}

			return $record;
		}

		/**
		 * Traitement d'une ligne de données du fichier CSV.
		 *
		 * @param array $row
		 * @return boolean
		 */
		public function processRow( array $row ) {
			$success = true;

			if( empty( $row ) ) {
				$this->empty[] = $row;
			}
			else {
				$data = $this->normalizeRow( $row );

				$familleromev3 = $domaineromev3 = $metierromev3 = $appellationromev3 = null;

				$this->Familleromev3->begin();

				$familleromev3 = $this->_processFamilleromev3( $row, $data );
				$success = $success && !empty( $familleromev3 );

				$domaineromev3 = $this->_processDomaineromev3( $row, $data, $familleromev3 );
				$success = $success && !empty( $domaineromev3 );

				$metierromev3 = $this->_processMetierromev3( $row, $data, $familleromev3, $domaineromev3 );
				$success = $success && !empty( $metierromev3 );

				$appellationromev3 = $this->_processAppellationromev3( $row, $data, $metierromev3 );
				$success = $success && !empty( $appellationromev3 );

				// TODO: iif on a les colonnes de correspondances ?
				$correspondanceromev2v3 = $this->_processCorrespondanceromev2v3( $row, $data, $metierromev3, $appellationromev3 );
				$success = $success && !empty( $correspondanceromev2v3 );

				if( $success ) {
					$this->Familleromev3->commit();
				}
				else {
					$this->Familleromev3->rollback();
				}
			}

			$this->XProgressBar->next();
			return $success;
		}

		/**
		 * Surcharge de la méthode startup pour vérifier que le département ait
		 * configuré l'utilisation des codes ROME V3 dans le webrsa.inc et démarrage
		 * de la barre de progression.
		 */
		public function startup() {
			parent::startup();

			if( !Configure::read( 'Romev3.enabled' ) ) {
				$msgstr = 'Ce shell est utilisé pour les codes ROME V3, pour l\'utiliser, merci de passer la valeur de "Romev3.enabled" à true dans le fichier app/Config/webrsa.inc';
				$this->error( $msgstr );
			}

			$this->XProgressBar->start( $this->_Csv->count() );
		}
	}
?>