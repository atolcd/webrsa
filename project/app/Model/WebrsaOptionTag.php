<?php
	/**
	 * Code source de la classe WebrsaOptionTag.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe WebrsaOptionTag ...
	 *
	 * @package app.Model
	 */
	class WebrsaOptionTag extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaOptionTag';

		/**
		 * On n'utilise pas de table.
		 *
		 * @var mixed
		 */
		public $useTable = false;

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Tag',
			'Requestmanager',
			'Zonegeographique'
		);

		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 * @return array
		 */
		public function optionsEnums( array $options = array() ) {
			$options = Hash::merge(
				$options,
				array(
					'Foyer' => array(
						'composition' => array(
							'cpl_sans_enf' => 'Couple sans enfant',
							'cpl_avec_enf' => 'Couple avec enfant(s)',
							'iso_sans_enf' => 'Isolé sans enfant',
							'iso_avec_enf' => 'Isolé avec enfant(s)'
						),
					),
					'Adresse' => array(
						'heberge' => array(
							0 => 'Non',
							1 => 'Oui',
						),
					),
				),
				(array)Configure::read('Tag.Options.enums'),
				$this->Tag->EntiteTag->Personne->Prestation->enums(),
				$this->Tag->EntiteTag->Personne->Dsp->enums()
			);

			$options['DspRev'] = $options['Dsp'];

			$accepted = array( 'DEM', 'CJT' );
			foreach( array_keys($options['Prestation']['rolepers']) as $value ) {
				if( !in_array( $value, $accepted ) ) {
					unset( $options['Prestation']['rolepers'][$value] );
				}
			}

			return $options;
		}

		/**
		 * Retourne les options stockées liées à des enregistrements en base de
		 * données, ne dépendant pas de l'utilisateur connecté.
		 *
		 * @return array
		 */
		public function optionsRecords( array $options = array() ) {
			$modeles = $this->Tag->EntiteTag->find('list', array('fields' => 'modele', 'order' => 'modele'));
			foreach ( $modeles as $value ) {
				$options['EntiteTag']['modele'][$value] = $value;
			}

			// Valeur tag / catégorie
			$query = array(
				'fields' => array(
					'Categorietag.name',
					'Valeurtag.id',
					'Valeurtag.name'
				),
				'joins' => array(
					$this->Tag->Valeurtag->join('Categorietag')
				),
			);

			if (!is_null(Configure::read( 'tag.affichage.actif.recherche' ))) {
				$query['conditions']['Valeurtag.actif'] = Configure::read( 'tag.affichage.actif.recherche' );
			}

			$results = $this->Tag->Valeurtag->find('all', $query);

			foreach ($results as $value) {
				$categorie = Hash::get($value, 'Categorietag.name') ?: 'Sans catégorie';
				$valeur = Hash::get($value, 'Valeurtag.name');
				$valeurtag_id = Hash::get($value, 'Valeurtag.id');
				$options['Tag']['valeurtag_id'][$categorie][$valeurtag_id] = $valeur;
			}

			$options['Zonegeographique']['id'] = $this->Zonegeographique->find( 'list' );

			$options['Requestgroup']['name'] = $this->Requestmanager->Requestgroup->find('list', array('order' => 'name'));
			$requestManager = $this->Requestmanager->find('all', array('conditions' => array( 'actif' => '1' )));

			foreach ($requestManager as $value) {
				$group_id = $value['Requestmanager']['requestgroup_id'];
				$group = $options['Requestgroup']['name'][$group_id];
				$have['Foyer'] = $this->Requestmanager->modelPresence($value, 'Foyer');
				$have['Personne'] = $this->Requestmanager->modelPresence($value, 'Personne');
				$have['Dossier'] = $this->Requestmanager->modelPresence($value, 'Dossier');

				if (!in_array(false, $have, true)) {
					$options['Requestmanager']['name'][$group][$value['Requestmanager']['id']] = $value['Requestmanager']['name'];
				}
			}

			return $options;
		}

		/**
		 * Retourne les noms des modèles dont des enregistrements seront mis en
		 * cache après l'appel à la méthode _optionsRecords() afin que la clé de
		 * cache générée par la méthode _options() se trouve associée dans
		 * ModelCache.
		 *
		 * @return array
		 */
		public function optionsRecordsModels( array $result = array() ) {
			return array_merge($result,
				array(
					'Valeurtag',
					'Categorietag',
					'Zonegeographique',
					'Requestmanager',
				)
			);
		}
	}
?>