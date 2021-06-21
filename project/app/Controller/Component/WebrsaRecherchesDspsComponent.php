<?php
	/**
	 * Code source de la classe WebrsaRecherchesDspsComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesDspsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesDspsComponent extends WebrsaAbstractRecherchesComponent
	{
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			$Controller = $this->_Collection->getController();

			$options = Hash::merge(
				parent::_optionsEnums( $params ),
				$Controller->Dsp->WebrsaDsp->options( array( 'find' => false, 'allocataire' => false, 'alias' => 'Donnees', 'enums' => true ) )
			);

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

			$options = Hash::merge(
				parent::_optionsRecords( $params ),
				$Controller->Dsp->WebrsaDsp->options( array( 'find' => true, 'allocataire' => false, 'alias' => 'Donnees', 'enums' => false ) )
			);

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
			$result = array_merge(
				parent::_optionsRecordsModels( $params ),
				 array( 'Familleromev3', 'Domaineromev3', 'Metierromev3', 'Appellationromev3' )
			);

			$departement = Configure::read( 'Cg.departement' );
			if( $departement == 66 ) {
				$result = array_merge(
					$result,
					array( 'Libderact66Metier', 'Libsecactderact66Secteur' )
				);
			}

			return $result;
		}

		/**
		 * Fait la traduction des données socio-professionnelles
		 * @param string $code : code DSP
		 * @param array $data : résultat de la query
		 * @param array $option : liste des traductions
		 */
		private function _tradDsps($code, $data, $option) {
			$return = '';

			if(isset($data['Donnees'][$code]) && !empty($data['Donnees'][$code])) {
				// Condition utilisée dans le cas où l'option de compatibilité "standard_conforming_strings"
				// au niveau postgres soit à off (cf CD93)
				if(strpos( $data['Donnees'][$code], '\n\r' ) !== false ){
					$glue = '\n\r-';
					$dsps = $data['Donnees'][$code];
				} else {
					$glue = '-';
					$dsps = preg_replace('/\s+/', ' ', $data['Donnees'][$code]);
				}
				// Récupère les différents codes
				$difs = explode($glue, $dsps);
				$prefix = '';
				$suffix = '</li>';

				// Création des libellés sous forme de liste
				foreach($difs as $dif) {
					if($dif != '') {
						$return .= $prefix . $option[trim($dif)] . $suffix;
						$prefix = '<li>';
					}
				}
			}
			return $return;
		}

		public function afterSearch( array $params, array $results ) {
			$Controller = $this->_Collection->getController();
			$options = $Controller->Dsp->WebrsaDsp->options( array( 'find' => false, 'allocataire' => false, 'alias' => 'Donnees', 'enums' => true ) );

			// Suppression de \n\r- dans les résultats
			foreach ($results as $i => $result) {
				// Difficultés sociales
				$results[$i]['Donnees']['difsoc'] = $this->_tradDsps('difsoc', $result, $options['Donnees']['difsoc']);

				// Domaine d'accompagnement individuel
				$results[$i]['Donnees']['nataccosocindi'] = $this->_tradDsps('nataccosocindi', $result, $options['Donnees']['nataccosocindi']);

				// Obstacles à la recherche d'emploi
				$results[$i]['Donnees']['difdisp'] = $this->_tradDsps('difdisp', $result, $options['Donnees']['difdisp']);
			}
			return $results;
		}
	}
