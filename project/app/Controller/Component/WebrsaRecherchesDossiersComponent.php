<?php
	/**
	 * Code source de la classe WebrsaRecherchesDossiersComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesDossiersComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesDossiersComponent extends WebrsaAbstractRecherchesComponent
	{
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$departement = Configure::read( 'Cg.departement' );

			$exists = array( '1' => 'Oui', '0' => 'Non' );

			$options = Hash::merge(
				parent::_optionsEnums( $params ),
				array(
					'Dsp' => array(
						'natlog' => $Controller->Dossier->Foyer->Personne->Dsp->enum( 'natlog', array( 'sort' => true ) ),
						'nivetu' => $Controller->Dossier->Foyer->Personne->Dsp->enum( 'nivetu', array( 'sort' => true ) ),
						'inscdememploi' => $Controller->Dossier->Foyer->Personne->Dsp->enum( 'inscdememploi', array( 'sort' => true ) )
					),
					'Personne' => array(
						'has_contratinsertion' => $exists,
						'has_cui' => $exists,
						'has_dsp' => $exists,
						'has_orientstruct' => $exists,
						'trancheage' => Configure::read( 'Search.Options.enums.personne.Personne.trancheage'),
					),
				)
			);

			if( $departement == 58 ) {
				$options['Activite']['act'] = $Controller->Dossier->Foyer->Personne->Activite->enum( 'act' );
				$options['Personne']['etat_dossier_orientation'] = $Controller->Dossier->Foyer->Personne->enum( 'etat_dossier_orientation' );
			}

			$Controller->loadModel( 'Tag' );
			$options['Tag']['etat'] = $Controller->Tag->enum( 'etat' );

			return $options;
		}

		/**
		 * Retourne les options stockées liées à des enregistrements en base de
		 * données, ne dépendant pas de l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsRecords( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$departement = Configure::read( 'Cg.departement' );

			$options = parent::_optionsRecords( $params );

			if( $departement == 58 ) {
				$options['Propoorientationcov58']['referentorientant_id'] = $Controller->Dossier->Foyer->Personne->PersonneReferent->Referent->find( 'list', array( 'order' => array( 'Referent.nom' ) ) );
			}

			$options = ClassRegistry::init('WebrsaOptionTag')->optionsRecords($options);

			return $options;
		}

		/**
		 * Retourne les noms des modèles dont des enregistrements seront mis en
		 * cache après l'appel à la méthode _optionsRecords() afin que la clé de
		 * cache générée par la méthode _options() se trouve associée dans
		 * ModelCache.
		 *
		 * @see _optionsRecords(), _options()
		 *
		 * @return array
		 */
		protected function _optionsRecordsModels( array $params ) {
			$departement = Configure::read( 'Cg.departement' );

			$result = parent::_optionsRecordsModels( $params );

			if( $departement == 58 ) {
				$result = array_merge( $result, array( 'Typeorient', 'Structurereferente', 'Referent' ) );
			}

			$result[] = 'Valeurtag';
			$result[] = 'Categorietag';

			return $result;
		}
	}
?>