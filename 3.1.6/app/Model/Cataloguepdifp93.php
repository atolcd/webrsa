<?php
	/**
	 * Code source de la classe Cataloguepdifp93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractSearch', 'Model/Abstractclass' );

	/**
	 * La classe Cataloguepdifp93 ...
	 *
	 * @package app.Model
	 */
	class Cataloguepdifp93 extends AbstractSearch
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Cataloguepdifp93';

		/**
		 * Ce modèle n'est pas lié à une table.
		 *
		 * @var boolean
		 */
		public $useTable = false;

		/**
		 * Liste des modèles disponibles dans le paramétrage.
		 *
		 * @var array
		 */
		public $modelesParametrages = array(
			'Thematiquefp93',
			'Categoriefp93',
			'Filierefp93',
			'Actionfp93',
			'Prestatairefp93',
			'Modtransmfp93',
			'Adresseprestatairefp93',
			'Motifnonreceptionfp93',
			'Motifnonretenuefp93',
			'Motifnonsouhaitfp93',
			'Motifnonintegrationfp93',
			'Documentbeneffp93'
		);

		/**
		 * Liste des modèles disponibles dans le paramétrage directement liés au
		 * catalogue.
		 *
		 * @var array
		 */
		public $modelesCatalogue = array(
			'Thematiquefp93',
			'Categoriefp93',
			'Filierefp93',
			'Actionfp93',
			'Prestatairefp93'
		);

		public $virtualFieldsActionfp93Tree = array(
			'Ficheprescription93.typethematiquefp93_id' => 'Thematiquefp93.type',
			'Ficheprescription93.thematiquefp93_id' => 'Thematiquefp93.id',
			'Ficheprescription93.categoriefp93_id' => 'Categoriefp93.id',
			'Ficheprescription93.filierefp93_id' => 'Filierefp93.id',
			'Ficheprescription93.prestatairefp93_id' => 'Prestatairefp93.id',
			'Ficheprescription93.actionfp93_id' => 'Actionfp93.id'
		);

		/**
		 *
		 * @param array $types Le nom du modèle => le type de jointure
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$Thematiquefp93 = ClassRegistry::init( 'Thematiquefp93' );

			$types += array(
				'Categoriefp93' => 'LEFT OUTER',
				'Filierefp93' => 'LEFT OUTER',
				'Actionfp93' => 'LEFT OUTER',
				'Adresseprestatairefp93' => 'LEFT OUTER',
				'Prestatairefp93' => 'LEFT OUTER'
			);

			$cacheKey = Inflector::underscore( $Thematiquefp93->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = array(
					'fields' => array(
						'Thematiquefp93.type',
						'Thematiquefp93.name',
						'Categoriefp93.name',
						'Filierefp93.name',
						'Adresseprestatairefp93.name',
						'Prestatairefp93.name',
						'Actionfp93.id',
						'Actionfp93.annee',
						'Actionfp93.name',
						'Actionfp93.actif'
					),
					'joins' => array(
						$Thematiquefp93->join( 'Categoriefp93', array( 'type' => $types['Categoriefp93'] ) ),
						$Thematiquefp93->Categoriefp93->join( 'Filierefp93', array( 'type' => $types['Filierefp93'] ) ),
						$Thematiquefp93->Categoriefp93->Filierefp93->join( 'Actionfp93', array( 'type' => $types['Actionfp93'] ) ),
						$Thematiquefp93->Categoriefp93->Filierefp93->Actionfp93->join( 'Adresseprestatairefp93', array( 'type' => $types['Adresseprestatairefp93'] ) ),
						$Thematiquefp93->Categoriefp93->Filierefp93->Actionfp93->Adresseprestatairefp93->join( 'Prestatairefp93', array( 'type' => $types['Prestatairefp93'] ) )
					),
					'conditions' => array(),
					'order' => array(
						'Thematiquefp93.type',
						'Thematiquefp93.name',
						'Filierefp93.name',
						'Adresseprestatairefp93.name',
						'Prestatairefp93.name',
						'Actionfp93.annee',
						'Actionfp93.name',
						'Actionfp93.actif'
					)
				);

				// Enregistrement dans le cache
				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			// 1. Valeurs exactes
			$paths = array(
				'Thematiquefp93.type',
				'Actionfp93.annee',
				'Actionfp93.actif'
			);
			foreach( $paths as $path ) {
				$value = trim( Hash::get( $search, $path ) );
				if( $value != '' ) {
					$query['conditions'][$path] = $value;
				}
			}

			// 2. Valeurs approchantes
			$paths = array(
				'Thematiquefp93.name',
				'Filierefp93.name',
				'Categoriefp93.name',
				'Prestatairefp93.name',
				'Actionfp93.name',
				'Actionfp93.numconvention'
			);
			foreach( $paths as $path ) {
				$value = trim( Hash::get( $search, $path ) );
				if( $value != '' ) {
					$query['conditions']["{$path} ILIKE"] = "%{$value}%";
				}
			}

			return $query;
		}

		/**
		 * Retourne les options nécessaires au formulaire de recherche, aux
		 * impressions, ...
		 *
		 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
		 *
		 * @param array $params
		 * @return array
		 */
		public function options( array $params = array() ) {
			$Thematiquefp93 = ClassRegistry::init( 'Thematiquefp93' );

			$options = Hash::merge(
				$Thematiquefp93->enums(),
				$Thematiquefp93->Categoriefp93->enums(),
				$Thematiquefp93->Categoriefp93->Filierefp93->enums(),
				$Thematiquefp93->Categoriefp93->Filierefp93->Actionfp93->enums(),
				$Thematiquefp93->Categoriefp93->Filierefp93->Actionfp93->Adresseprestatairefp93->enums(),
				$Thematiquefp93->Categoriefp93->Filierefp93->Actionfp93->Adresseprestatairefp93->Prestatairefp93->enums()
			);

			return $options;
		}
	}
?>