<?php
	/**
	 * Code source de la classe Indu.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Indu ...
	 *
	 * @package app.Model
	 */
	class Indu extends AppModel
	{
		public $name = 'Indu';

		public $useTable = 'infosfinancieres';

		/**
		*
		*/

		public function search( $mesCodesInsee, $filtre_zone_geo, $criteres ) {
			/// Conditions de base
			$conditions = array();

			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );

			/// Critères
			$mois = Set::extract( $criteres, 'Filtre.moismoucompta' );

			/// Mois du mouvement comptable
			if( !empty( $mois ) && dateComplete( $criteres, 'Filtre.moismoucompta' ) ) {
				$mois = $mois['month'];
				$conditions[] = 'EXTRACT(MONTH FROM Infofinanciere.moismoucompta) = '.$mois;
			}

			/// Id du Dossier
			if( !empty( $criteres ) && isset( $criteres['Dossier.id'] ) ) {
				$conditions['Dossier.id'] = $criteres['Dossier.id'];
			}

			/// Requête
			$this->Dossier = ClassRegistry::init( 'Dossier' );

			$query = array(
				'fields' => array(
					'"Infofinanciere"."id"',
					'"Infofinanciere"."dossier_id"',
					'"Infofinanciere"."moismoucompta"',
					'"Infofinanciere"."type_allocation"',
					'"Infofinanciere"."natpfcre"',
					'"Infofinanciere"."rgcre"',
					'"Infofinanciere"."numintmoucompta"',
					'"Infofinanciere"."typeopecompta"',
					'"Infofinanciere"."sensopecompta"',
					'"Infofinanciere"."mtmoucompta"',
					'"Infofinanciere"."ddregu"',
					'"Infofinanciere"."dttraimoucompta"',
					'"Infofinanciere"."heutraimoucompta"',
					'"Dossier"."id"',
					'"Dossier"."numdemrsa"',
					'"Dossier"."matricule"',
					'"Dossier"."typeparte"',
					'"Personne"."id"',
					'"Personne"."nom"',
					'"Personne"."prenom"',
					'"Personne"."nir"',
					'"Personne"."dtnai"',
					'"Personne"."qual"',
					'"Personne"."nomcomnai"',
					'"Situationdossierrsa"."etatdosrsa"',
					'"Adresse"."nomcom"',
					'"Adresse"."codepos"',
				),
				'recursive' => -1,
				'joins' => array(
					array(
						'table'      => 'dossiers',
						'alias'      => 'Dossier',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Infofinanciere.dossier_id = Dossier.id' )
					),
					array(
						'table'      => 'situationsdossiersrsa',
						'alias'      => 'Situationdossierrsa',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Situationdossierrsa.dossier_id = Dossier.id' )
					),
					array(
						'table'      => 'foyers',
						'alias'      => 'Foyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Foyer.dossier_id = Dossier.id' )
					),
					array(
						'table'      => 'personnes',
						'alias'      => 'Personne',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Personne.foyer_id = Foyer.id' )
					),
					array(
						'table'      => 'prestations',
						'alias'      => 'Prestation',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'Personne.id = Prestation.personne_id',
							'Prestation.natprest = \'RSA\'',
							'( Prestation.rolepers = \'DEM\' )',
						)
					),
					array(
						'table'      => 'adressesfoyers',
						'alias'      => 'Adressefoyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Foyer.id = Adressefoyer.foyer_id', 'Adressefoyer.rgadr = \'01\'' )
					),
					array(
						'table'      => 'adresses',
						'alias'      => 'Adresse',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Adresse.id = Adressefoyer.adresse_id' )
					),
				),
				'limit' => 10,
				'order' => array( '"Personne"."nom"' ),
				'conditions' => $conditions
			);

			$typesAllocation = array( 'AllocationsComptabilisees', 'IndusConstates', 'IndusTransferesCG', 'RemisesIndus', 'AnnulationsFaibleMontant', 'AutresAnnulations' );

			foreach( $typesAllocation as $type ) {
				$meu  = Inflector::singularize( Inflector::tableize( $type ) );
				$query['fields'][] = '"'.$type.'"."mtmoucompta" AS mt_'.$meu;

				$join = array(
					'table'      => 'infosfinancieres',
					'alias'      => $type,
					'type'       => 'LEFT OUTER',
					'foreignKey' => false,
					'conditions' => array(
						$type.'.dossier_id = Dossier.id',
						$type.'.type_allocation' => $type,
					)
				);
				$query['joins'][] = $join;
			}
			$query['conditions'] = Set::merge( $query['conditions'], $conditions );
			return $query;
		}
	}
?>
