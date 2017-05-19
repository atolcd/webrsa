<?php
	/**
	 * Code source de la classe WebrsaCohorteDossierpcg66Imprimer.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorteDossierpcg66', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteDossierpcg66Imprimer ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteDossierpcg66Imprimer extends AbstractWebrsaCohorteDossierpcg66
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteDossierpcg66Imprimer';
		
		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Typepdo' => 'INNER',
				'Originepdo' => 'INNER',
				'Serviceinstructeur' => 'LEFT OUTER',
			);
			
			$query = $this->WebrsaRechercheDossierpcg66->searchQuery( $types );
			
			$query['conditions'] = array(
				'OR' => array(
					array(
						// Décision à imprimer
						'Decisiondossierpcg66.etatdossierpcg IS NULL',
						'Dossierpcg66.etatdossierpcg' => 'decisionvalid',
						'Dossierpcg66.dateimpression' => null
					),
					array(
						// Traitement de type courrier à imprimer sans décision
						'Dossierpcg66.id IN ('
						. 'SELECT a.id FROM dossierspcgs66 AS a '
						. 'INNER JOIN personnespcgs66 AS b ON a.id = b.dossierpcg66_id '
						. 'INNER JOIN traitementspcgs66 AS c ON c.personnepcg66_id = b.id '
						. 'WHERE a.id = "Dossierpcg66"."id" '
						. 'AND a.poledossierpcg66_id = "Dossierpcg66"."poledossierpcg66_id" '
						. 'AND a.etatdossierpcg != \'annule\''
						. 'AND c.imprimer = 1 '
						. 'AND c.annule = \'N\''
						. 'AND c.etattraitementpcg = \'imprimer\')',
						
						// Négation de la 1ère condition (Décision à imprimer) pour éviter les doublons
						'NOT EXISTS ('
						. 'SELECT a.id FROM dossierspcgs66 AS a '
						. 'INNER JOIN decisionsdossierspcgs66 AS b ON a.id = b.dossierpcg66_id '
						. 'WHERE a.foyer_id = "Dossierpcg66"."foyer_id" '
						. 'AND a.id != "Dossierpcg66"."id" '
						. 'AND b.etatdossierpcg IS NULL '
						. 'AND a.etatdossierpcg = \'decisionvalid\' '
						. 'AND a.dateimpression IS NULL)'
					)
				),
				array(
					'Personne.id = Personnepcg66.personne_id'
				)
			);
			
			return $query;
		}
	}
?>