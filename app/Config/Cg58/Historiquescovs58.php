<?php
	Configure::write(
		'ConfigurableQuery.Historiquescovs58.view',
			array(
			'common' => array(
				'Cov58.datecommission',
				'Sitecov58.name',
				'Dossiercov58.themecov58',
				'Passagecov58.etatdossiercov',
				'Dossiercov58.created'
			),
			// ---------------------------------------------------------------------
			'proposorientationscovs58' => array(
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Referent.nom_complet',
				'Propoorientationcov58.commentaire'
			),
			'decisionsproposorientationscovs58' => array(
				'Decisionpropoorientationcov58.decisioncov',
				'NvTypeorient.lib_type_orient',
				'NvStructurereferente.lib_struc',
				'NvReferent.nom_complet',
				'Decisionpropoorientationcov58.commentaire'
			),
			// ---------------------------------------------------------------------
			'proposcontratsinsertioncovs58' => array(
				'Propocontratinsertioncov58.num_contrat',
				'Propocontratinsertioncov58.dd_ci',
				'Propocontratinsertioncov58.duree',
				'Propocontratinsertioncov58.df_ci',
				'VxStructurereferente.lib_struc',
				'VxReferent.nom_complet',
				'Propocontratinsertioncov58.commentaire'
			),
			'decisionsproposcontratsinsertioncovs58' => array(
				'Decisionpropocontratinsertioncov58.decisioncov',
				'Decisionpropocontratinsertioncov58.datevalidation',
				'Decisionpropocontratinsertioncov58.dd_ci',
				'Decisionpropocontratinsertioncov58.duree_engag',
				'Decisionpropocontratinsertioncov58.df_ci',
				'Decisionpropocontratinsertioncov58.commentaire'
			),
			// ---------------------------------------------------------------------
			'proposnonorientationsproscovs58' => array(
				'VxOrientstruct.date_valid' => array( 'type' => 'date' ),
				'VxTypeorient.lib_type_orient',
				'VxStructurereferente.lib_struc',
				'VxReferent.nom_complet',
				'Propononorientationprocov58.commentaire'
			),
			'decisionsproposnonorientationsproscovs58' => array(
				'Decisionpropononorientationprocov58.decisioncov',
				'NvTypeorient.lib_type_orient',
				'NvStructurereferente.lib_struc',
				'NvReferent.nom_complet',
				'Decisionpropononorientationprocov58.commentaire'
			),
			// ---------------------------------------------------------------------
			'proposorientssocialescovs58' => array(
				'Propoorientsocialecov58.commentaire',
				'Rendezvous.daterdv',
				'Rendezvous.heurerdv',
				'Structurereferenterdv.lib_struc',
				'Referentrdv.nom_complet',
				'Typerdv.libelle',
				'Statutrdv.libelle'
			),
			'decisionsproposorientssocialescovs58' => array(
				'Decisionpropoorientsocialecov58.decisioncov',
				'NvTypeorient.lib_type_orient',
				'NvStructurereferente.lib_struc',
				'NvReferent.nom_complet',
				'Decisionpropoorientsocialecov58.commentaire'
			),
			// ---------------------------------------------------------------------
			'nonorientationsproscovs58' => array(
				'VxOrientstruct.date_valid' => array( 'type' => 'date' ),
				'VxTypeorient.lib_type_orient',
				'VxStructurereferente.lib_struc',
				'VxReferent.nom_complet',
				'Nonorientationprocov58.commentaire'
			),
			'decisionsnonorientationsproscovs58' => array(
				'Decisionnonorientationprocov58.decisioncov',
				'NvTypeorient.lib_type_orient',
				'NvStructurereferente.lib_struc',
				'NvReferent.nom_complet',
				'Decisionnonorientationprocov58.commentaire'
			),
			// ---------------------------------------------------------------------
			'regressionsorientationscovs58' => array(
				'VxOrientstruct.date_valid' => array( 'type' => 'date' ),
				'VxTypeorient.lib_type_orient',
				'VxStructurereferente.lib_struc',
				'VxReferent.nom_complet',
				'Regressionorientationcov58.commentaire'
			),
			'decisionsregressionsorientationscovs58' => array(
				'Decisionregressionorientationcov58.decisioncov',
				'NvTypeorient.lib_type_orient',
				'NvStructurereferente.lib_struc',
				'NvReferent.nom_complet',
				'Decisionregressionorientationcov58.commentaire'
			)
		)
	);
?>