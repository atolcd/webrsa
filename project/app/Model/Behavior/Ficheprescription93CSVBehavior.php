<?php
	/**
	 * Code source de la classe Ficheprescription93CSVBehavior..
	 *
	 * PHP 7.2
	 *
	 * @package app.Model.Behavior
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses(
		'ModelBehavior', 'Model',
		'Instantanedonneesfp93'
	);

	/**
	 * La classe Ficheprescription93CSVBehavior. contient du code commun des classes
	 * de modèles Ficheprescription93.
	 *
	 * @package app.Model.Behavior
	 */
	class Ficheprescription93CSVBehavior extends ModelBehavior
	{

		/**
		 * Recherche et Update de l'enregistrement sinon Insertion, avec complément de
		 * données si besoin et retourne la valeur de la clé primaire ou null.
		 *
		 * @param Model $Model
		 * @param array $conditions
		 * @param array $complement
		 * @return integer
		 */
		public function csvUpdate( Model $Model, array $conditions, array $complement = array() ) {
			$primaryKey = null;
			$go = true;
			$conditions = Hash::flatten( $Model->doFormatting( Hash::expand( $conditions ) ) );
			$modelPrimaryKey = "{$Model->alias}.{$Model->primaryKey}";

			/*Gestions spécifiques des valeurs */
			//Add datetransmi à today if not exists
			if (empty($conditions['Ficheprescription93.frsa_datetransmi'] ) ) {
				$conditions['Ficheprescription93.frsa_datetransmi'] = date("Y-m-d");
			}

			//Vidage des valeurs
			if ($conditions['Ficheprescription93.personne_retenue'] == TRUE) {
				$conditions['Ficheprescription93.motifnonretenuefp93_id'] = NULL;
				$conditions['Ficheprescription93.personne_nonretenue_autre'] = NULL;
			}

			if ($conditions['Ficheprescription93.personne_a_integre'] == TRUE) {
				$conditions['Ficheprescription93.motifnonintegrationfp93_id'] = NULL;
				$conditions['Ficheprescription93.personne_nonintegre_autre'] = NULL;
			}

			if ($conditions['Ficheprescription93.personne_acheve'] == TRUE ){
				$conditions['Ficheprescription93.motifnonactionachevefp93_id'] = NULL;
			}else{
				$conditions['Ficheprescription93.motifactionachevefp93_id'] = NULL;
				$conditions['Ficheprescription93.personne_acheve_autre'] =
					$conditions['Ficheprescription93.personne_nonacheve_autre'];
			}
			unset ($conditions['Ficheprescription93.personne_nonacheve_autre']) ;

			/* Verification de la date */
			$conditions['Ficheprescription93.rdvprestataire_date'] =
				date("Y-m-d H:i:s", strtotime($conditions['Ficheprescription93.rdvprestataire_date']) );

			//Merge des données
			$data = Hash::merge(
				Hash::expand( $conditions ),
				Hash::expand( $complement )
			);

			//Necessite les date_signature, date_transmission et date_retour qui ne sont actuelement pas renseigner par FRSA
			//Recuperer valeur précédentes
			$query = array(
				'fields' => array (
					'Ficheprescription93.id',
					'Ficheprescription93.posorigine',
					'Ficheprescription93.date_retour',
					'Ficheprescription93.date_signature',
					'Ficheprescription93.date_transmission'
				),
				'conditions' => array (
						"{$Model->alias}.{$Model->primaryKey}" => $data['Ficheprescription93']['id'],
				)
			);
			$record = $Model->find( 'first', $query );
			// IF beneficiaire_present 'oui' and date_retour empty Then
			if ( $data['Ficheprescription93']['benef_retour_presente'] == 'oui' && empty($record['Ficheprescription93']['date_retour'])  ) {
				//Date_retour set Today
				$data['Ficheprescription93']['date_retour']	= date("Y-m-d");
			} elseif ( $data['Ficheprescription93']['benef_retour_presente'] == 'non' || $data['Ficheprescription93']['benef_retour_presente'] == 'excuse'  ) {
				$data['Ficheprescription93']['date_retour']	= null;
			} else {
				$data['Ficheprescription93']['date_retour']	= $record['Ficheprescription93']['date_retour'];
			}
			//Get date_signature et date_transmission
			$data['Ficheprescription93']['date_signature'] = $record['Ficheprescription93']['date_signature'];	
			$data['Ficheprescription93']['date_transmission'] =	$record['Ficheprescription93']['date_transmission'];
			$data['Ficheprescription93']['posorigine'] = $record['Ficheprescription93']['posorigine'];

			$data = $Model->calculStatusFP($data);

			//Verification de l'existance des id des table liées
			foreach( $data['Ficheprescription93'] as $fieldName => $idvalue) {
				if( preg_match( '/_id$/', $fieldName ) && !is_null($idvalue) && $fieldName != 'frsa_id' ) {
					$linkedModelName = Inflector::classify( preg_replace( '/_id$/', '', $fieldName ) );
					$Linked = $Model->{$linkedModelName};
					//Query LinkedModel ID exists
					$query = array(
						'fields' => array (
							"{$linkedModelName}.id"
						),
						'conditions' => array (
							"{$linkedModelName}.id" => $idvalue,
						)
					);
					$record = $Linked->find( 'first', $query );
					//If linked model ID not exist then fail
					if (empty ($record) ){
						$go = false;
						//TODO : Error LOG Id inexistant dans Model
					}
				}
			}
			if ( $go ) {
				//Test de conflit de modification au même jour
				$query = array(
					'fields' => $Model->fields(),
					'conditions' => array (
						'Ficheprescription93.id' => $conditions['Ficheprescription93.id'],
						'Ficheprescription93.modified' =>  date("Y-m-d")
					)
				);
				$record = $Model->find( 'first', $query );
				//If modified today then
				if( !empty( $record ) ) {
					//debug('Modified Today');
					//TODO : Insert old fields to Admin
					// Select fields for admin table
						//$record
					//Prepare Admin Model
					//Test Save
					//ELSE
						//$go = false;
				}
			}
			if ( $go ){
				// Insert fields to Ficheprescritpion
				$Model->create($data);
				if ( $Model->save( null, array( 'atomic' => false ) ) ) {
					$primaryKey = $Model->{$Model->primaryKey};
				}else{
					$primaryKey = null;
				}
			}

			return $primaryKey;
		}

		/**
		 * Recherche et Update de l'enregistrement sinon Insertion, avec complément de
		 * données si besoin et retourne la valeur de la clé primaire ou null.
		 *
		 * @param Model $Model
		 * @param array $conditions
		 * @param array $complement
		 * @return integer
		 */
		public function getInsertedUpdatedPrimaryKey( Model $Model, array $conditions, array $complement = array() ) {
			/*
			 * Check Fiches prescription
			 */
			$primaryKey = null;
			$go = true;

			$conditions = Hash::flatten( $Model->doFormatting( Hash::expand( $conditions ) ) );
			$modelPrimaryKey = "{$Model->alias}.{$Model->primaryKey}";

			/*Gestions spécifiques des valeurs */
			//Add datetransmi à today if not exists
			if (empty($conditions['Ficheprescription93.frsa_datetransmi'] ) ) {
				$conditions['Ficheprescription93.frsa_datetransmi'] = date("Y-m-d");
			}

			//Merge des données
			$data = Hash::merge(
				Hash::expand( $conditions ),
				Hash::expand( $complement )
			);

			// Defintion de l'objet
			$data['Ficheprescription93']['objet'] = $data['Ficheprescription93']['frsa_motivation'];

			// IF beneficiaire_present 'oui' and date_retour empty Then
			$data['Ficheprescription93']['date_retour']	= NULL;

			//Get date_signature et date_transmission
			$data['Ficheprescription93']['date_transmission'] =	$data['Ficheprescription93']['date_signature'];

			// Ajout du statut
			$data = $Model->calculStatusFP($data);

			//Get Action name from action ID
			$result = $Model->Actionfp93->find(
				'first',
				array(
					'fields' => array(
						'Actionfp93.name',
						'Actionfp93.duree',
					),
					'conditions' => array( 'Actionfp93.id'=> $data['Ficheprescription93']['actionfp93_id'])
				)
			);
			$data['Ficheprescription93']['actionfp93'] = $result['Actionfp93']['name'];
			$data['Ficheprescription93']['duree_action'] = $result['Actionfp93']['duree'];

			//Get and set ID-referent
			//Selon les config otpions :
			switch ( Configure::read('CSVImport.FRSA.AutoPositionnement.ReferentOption') ) {
				case 1 :
					//Option 1:  Si on décide d'utiliser l'identifiant du réferent de la personne.
					$structReferent = $Model->Personne->PersonneReferent->find(
						'first',
						array(
							'fields' => array(
								'Referent.id',
								'Structurereferente.id'
							),
							'contain' => array(
								'Referent',
								'Structurereferente'
							),
							'conditions' => array( 'PersonneReferent.personne_id' => $data['Ficheprescription93']['personne_id'], 'PersonneReferent.dfdesignation IS NULL' ),
							'order' => array( 'PersonneReferent.dddesignation DESC' )
						)
					);
					$data['Ficheprescription93']['referent_id'] = $structReferent['Referent']['id'];
				case 2 :
					//Option 2 : Identifiant Fixe
					$data['Ficheprescription93']['referent_id'] = Configure::read('CSVImport.FRSA.AutoPositionnement.ReferentID');
			}

			$Model->create($data);
			if ( $Model->save( null, array( 'atomic' => false ) ) ) {
				$primaryKey = $Model->{$Model->primaryKey};
				$instantaneeKey = $this->createInstantaneeDonnee($Model, $primaryKey, $data);
				if ($instantaneeKey != $primaryKey) {
					$primaryKey = null;
				}
			}else{
				$primaryKey = null;
			}
			return $primaryKey;
		}

		/**
		 *Instantanée données
		 * Creation de l'instantanée Donnée qui va avec la fiche prescription
		 *
		 * @param Model $Model
		 * @param integer
		 * @param array $data
		 * @return integer
		 *
		 **/
		public function createInstantaneeDonnee( Model $Model, $primaryKey, array $data) {
			$instantaneeKey = null;

			$instantanedonneesfp93 = $Model->Instantanedonneesfp93->getInstantane( $data['Ficheprescription93']['personne_id'] );//Get Information Beneficiaire
			$instantanedonneesfp93['Instantanedonneesfp93']['ficheprescription93_id'] = $primaryKey;

			//Get Referent et Structure
			$fields = array_merge(
				$Model->Personne->PersonneReferent->Referent->fields(),
				$Model->Personne->PersonneReferent->Referent->Structurereferente->fields()
			);
			$structReferent = $Model->Personne->PersonneReferent->Referent->find(
				'first',
				array(
					'fields' => $fields,
					'contain' => array(
						'Structurereferente'
					),
					'conditions' => array( 'Referent.id' => $data['Ficheprescription93']['referent_id'])
				)
			);
			if (!empty( $structReferent )) {
				$instantanedonneesfp93['Instantanedonneesfp93']['referent_fonction'] = $structReferent['Referent']['fonction'] ;
				$instantanedonneesfp93['Instantanedonneesfp93']['referent_email'] = $structReferent['Referent']['email'];
				$instantanedonneesfp93['Instantanedonneesfp93']['structure_name'] = $structReferent['Structurereferente']['lib_struc'];
				$instantanedonneesfp93['Instantanedonneesfp93']['structure_num_voie'] = $structReferent['Structurereferente']['num_voie'];
				$instantanedonneesfp93['Instantanedonneesfp93']['structure_type_voie'] = $structReferent['Structurereferente']['type_voie'];
				$instantanedonneesfp93['Instantanedonneesfp93']['structure_nom_voie'] = $structReferent['Structurereferente']['nom_voie'];
				$instantanedonneesfp93['Instantanedonneesfp93']['structure_code_postal'] = $structReferent['Structurereferente']['code_postal'];
				$instantanedonneesfp93['Instantanedonneesfp93']['structure_ville'] = $structReferent['Structurereferente']['ville'];
				$instantanedonneesfp93['Instantanedonneesfp93']['structure_tel'] = $structReferent['Structurereferente']['numtel'];
				$instantanedonneesfp93['Instantanedonneesfp93']['structure_fax'] = $structReferent['Structurereferente']['numfax'];

				$Model->Instantanedonneesfp93->create($instantanedonneesfp93['Instantanedonneesfp93']);
				if ( $Model->Instantanedonneesfp93->save( null, array( 'atomic' => false ) ) ) {
					$instantaneeKey = $primaryKey;
				}else{
					$instantaneeKey = null;
				}

			}else {
				$instantaneeKey = null;
			}

			return $instantaneeKey ;
		}

	}
?>