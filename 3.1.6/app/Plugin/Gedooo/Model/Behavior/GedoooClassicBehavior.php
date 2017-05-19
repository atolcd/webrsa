<?php
	/**
	 * Fichier source de la classe GedoooClassicBehavior.
	 *
	 * PHP 5.3
	 *
	 * @package Gedooo
	 * @subpackage Model.Behavior
	 */
	// Inclusion des fichiers nécessaires à GEDOOo
	require_once( PHPGEDOOO_DIR.'GDO_Utility.class' );
	require_once( PHPGEDOOO_DIR.'GDO_FieldType.class' );
	require_once( PHPGEDOOO_DIR.'GDO_ContentType.class' );
	require_once( PHPGEDOOO_DIR.'GDO_IterationType.class' );
	require_once( PHPGEDOOO_DIR.'GDO_PartType.class' );
	require_once( PHPGEDOOO_DIR.'GDO_FusionType.class' );
	require_once( PHPGEDOOO_DIR.'GDO_MatrixType.class' );
	require_once( PHPGEDOOO_DIR.'GDO_MatrixRowType.class' );
	require_once( PHPGEDOOO_DIR.'GDO_AxisTitleType.class' );

	/**
	 * La classe GedoooClassicBehavior permet de générer un fichier PDF avec
	 * l'ancienne version de Gedooo et un ODT avec la nouvelle version.
	 *
	 * Cette classe est utilisée comme classe parente d'autres behaviors.
	 *
	 * @package Gedooo
	 * @subpackage Model.Behavior
	 */
	class GedoooClassicBehavior extends ModelBehavior
	{
		/**
		 * INFO: GDO_FieldType: text, string, number, date
		 */
		protected function _addPartValue( $oPart, $key, $value, $options ) {
			$type = 'text';
			if( preg_match( '/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/', $value ) ) {
				$type = 'date';
			}
			else if( preg_match( '/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $value, $matches ) ) {
				$type = 'date';
				$value = "{$matches[3]}/{$matches[2]}/{$matches[1]}";
			}
			else if( preg_match( '/^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}:[0-9]{2}:[0-9]{2})$/', $value, $matches ) ) {
				$type = 'date';
				$value = "{$matches[3]}/{$matches[2]}/{$matches[1]}";
				$oPart->addElement( new GDO_FieldType( strtolower( $key ).'_time', preg_replace( '/([0-9]{2}:[0-9]{2}):[0-9]{2}/', '\1', $matches[4] ), 'text' ) );
			}

			// Traduction des enums
			if( preg_match( '/([^_]+)_([0-9]+_){0,1}([^_]+)$/', $key, $matches ) ) {
				if( isset( $options[$matches[1]][$matches[3]][$value] ) ) {
					$value = $options[$matches[1]][$matches[3]][$value];
				}
			}

			$oPart->addElement( new GDO_FieldType( strtolower( $key ), $value, $type ) );

			return $oPart;
		}

		/**
		 * Fonction de génération de documents générique
		 * @param $datas peut prendre la forme suivante:
		 *     - array( ... ) si $section == false
		 *     - array( 0 => array( ... ), 'section1' => array( ... ), 'section2' => array( ... ) ) si $section == true
		 */
		public function ged( Model $model, $datas, $document, $section = false, $options = array( ) ) {
			// Définition des variables & macros
			$sMimeType = "application/pdf"; // FIXME
			$path_model = ( ( isset( $document[0] ) && $document[0] == '/' ) ? '' : MODELESODT_DIR ).$document;

			// Quel type de données a-t-on reçu ?
			if( !$section ) {
				$mainData = $datas;
				$cohorteData = array( );
			}
			else {
				$mainData = ( isset( $datas[0] ) ? $datas[0] : array( ) );
				$cohorteData = $datas;
				unset( $cohorteData[0] );
			}

			//
			// Organisation des données
			//
			$u = new GDO_Utility();
			$oMainPart = new GDO_PartType();

			$bTemplate = $u->ReadFile( $path_model );
			if( empty( $bTemplate ) ) {
				$this->log( sprintf( "Le modèle de document %s n'existe pas ou n'est pas lisible.", $path_model ), LOG_ERROR );
				return false;
			}

			if( !empty( $mainData ) ) {
				foreach( Hash::flatten( $mainData, '_' ) as $key => $value ) {
					if( !( is_array( $value ) && empty( $value ) ) ) {
						$oMainPart = $this->_addPartValue( $oMainPart, $key, $value, $options );
					}
				}
			}

			// Ajout d'une variable contenant le chemin vers le fichier et vérification
			// que le modèle connaisse bien le fichier odt lorsqu'on est en debug
			if( Configure::read( 'debug' ) > 0 ) {
				// Attention, c'est le modèle qui doit avoir le comportement Gedooo -> FIXME
				/* $modelesOdt = str_replace( '%s', $model->alias, $model->modelesOdt );

				  // Récupération des valeurs de la méthode modelesOdt lorsqu'elle est présente
				  if( in_array( 'modelesOdt', get_class_methods( $model->name ) ) ) {
				  $modelesOdt = Set::merge( $modelesOdt, $model->modelesOdt() );
				  }

				  if( !in_array( $document, $modelesOdt ) ) {
				  $this->log( sprintf( "Le modèle de document %s n'est pas connu du modèle %s.", $document, $model->alias ), LOG_DEBUG );
				  } */

				$oMainPart->addElement( new GDO_FieldType( 'modeleodt_path', str_replace( MODELESODT_DIR, '', $path_model ), 'text' ) );
			}

			if( !empty( $cohorteData ) ) {
				foreach( $cohorteData as $cohorteName => $sectionDatas ) {
					// Traitement d'une section
					$sectionFields = array( );

					$oIteration = new GDO_IterationType( $cohorteName );
					foreach( $sectionDatas as $sectionData ) {
						$oDevPart = new GDO_PartType();

						$sectionData = Hash::flatten( $sectionData, '_' );
						foreach( $sectionData as $key => $value ) {
							if( !( is_array( $value ) && empty( $value ) ) ) {
								$oDevPart = $this->_addPartValue( $oDevPart, $key, $value, $options );
							}
						}
						$oIteration->addPart( $oDevPart );
					}
					$oMainPart->addElement( $oIteration );
				}
			}

			if( Configure::read( 'debug' ) > 0 ) {
				App::uses( 'GedoooUtility', 'Gedooo.Utility' );

				$lines = GedoooUtility::toCsv(
					$oMainPart,
					$mainData,
					$cohorteData,
					Configure::read( 'Gedooo.debug_export_data' )
				);

				GedoooUtility::exportCsv(
					TMP.DS.'logs'.DS.__CLASS__.'__'.str_replace( '/', '__', str_replace( '.', '_', $document ) ).'.csv',
					$lines
				);
			}

			$oTemplate = new GDO_ContentType(
							"",
							"modele.ott",
							$u->getMimeType( $path_model ),
							"binary",
							$bTemplate
			);

			$oFusion = new GDO_FusionType( $oTemplate, $sMimeType, $oMainPart );
			$oFusion->process();
			$success = ( $oFusion->getCode() == 'OK' );

			if( $success ) {
				$content = $oFusion->getContent();
				return $content->binary;
			}
			else {
				$this->log( sprintf( "Erreur lors de la génération du document (%s num. %s: %s).", $oFusion->getCode(), $oFusion->errNum, $oFusion->getMessage() ), LOG_ERROR );
			}

			return $success;
		}

		/**
		 * Retourne la liste des clés de configuration.
		 *
		 * @return array
		 */
		public function gedConfigureKeys( Model $model ) {
			return array(
				'Gedooo.wsdl' => 'string'
			);
		}

		public function gedTestPrint( Model $model, array $access ) {
            if( $access['success'] ) {
                if( get_class( $this ) == 'GedoooClassicBehavior' ) {
                    $test_print = $this->ged( $model, array( ), GEDOOO_TEST_FILE );
                }
                else {
                    $test_print = $this->gedFusion( $model, array( 'foo' => 'bar' ), GEDOOO_TEST_FILE );
                }

                $test_print = !empty( $test_print ) && preg_match( '/^(%PDF\-[0-9]|PK)/m', $test_print );
            }
            else {
                $test_print = false;
            }

			return array(
				'success' => $test_print,
				'message' => ( $test_print ? null : 'Impossible d\'imprimer avec le serveur Gedooo.' )
			);
		}

		/**
		 * @return array
		 */
		public function gedTests( Model $model ) {
			App::import( 'Model', 'Appchecks.Check' );
			$Check = ClassRegistry::init( 'Appchecks.Check' );

            $access = $Check->webservice( GEDOOO_WSDL );

			return array(
				'Accès au WebService' => $access,
				'Présence du modèle de test' => $Check->filePermission( GEDOOO_TEST_FILE ),
				'Test d\'impression' => $this->gedTestPrint( $model, $access )
			);
		}

	}
?>